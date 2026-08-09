<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Validator;
use Carbon\Carbon;
use Auth;
use DB;
use Excel;
use PDF;

use App\Student;
use App\StudentAccDetail;
use App\SpecializationRequest;
use App\StudentExam;
use App\StudentExamSubject;
use App\CourseSubject;
use App\CourseSchedule;
use App\StudentExamResult;
use App\CourseGrade;
use App\Regulation;
use App\PerformanceClass;
use \App\SystemLog;



use App\Imports\ResultsImport;
use App\Imports\ResultsImportBulk;

use App\Exports\GPAExport;
use App\Imports\GPAImport;

use App\TempResultsImport;

class AdminResultController extends Controller
{
    private $year;

    public function __construct(){
        $this->middleware('auth:users');
        $this->year = settings('year');
    }

    public function view_uploaded_resutls(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){
            return view('admin.results.view-results');
        }else{
            $subjectIds=[];
            if (Auth::user()->hasRole('Admin')){ 
                $subjectIds = CourseSubject::where('semester','>',0)->where('code','=',$request->subject)->orderBy('id')->pluck('id')->toArray();
            }elseif(Auth::user()->hasPermissionTo('course:teacher')){
                $allowsSubjects = Auth::user()->subjects()->pluck('course_subjects.id')->toArray();
                $subjectIds = CourseSubject::whereIn('id',$allowsSubjects)->where('code','=',$request->subject)->orderBy('id')->pluck('id')->toArray();
            }

            $data = [];
            $col = [
                0 => 'student_personal_details.id',
                1 => 'RegistrationNo',
                2 => 'Name',
                3 => 'Marks',
                4 => 'Result'
            ];
            //DB::enableQueryLog();
            $a = Student::join('student_exam_results','student_personal_details.id','=','student_exam_results.student_id')
                    ->join('course_subjects','student_exam_results.course_subject_id','=','course_subjects.id')
                    ->where('student_exam_results.year','=',$request->accyear)
                    ->whereIn('student_exam_results.course_subject_id',$subjectIds)                    
                    ->select(
                        'student_personal_details.id AS ID',
                        'student_personal_details.registration_no AS RegistrationNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        // DB::raw('CONCAT(course_subjects.code," ",course_subjects.name) AS Subject'),
                        'student_exam_results.marks AS Marks',
                        'student_exam_results.result as Result',
                    );

            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;

            $orderIndex = isset($request->order[0]['column']) ? $request->order[0]['column'] : 1;
            $orderCol = isset($col[$orderIndex]) ? $col[$orderIndex] : 'RegistrationNo';
            $orderDir = isset($request->order[0]['dir']) ? $request->order[0]['dir'] : 'asc';

            $a->orderBy($orderCol, $orderDir);
            $a->offset($request->start)->limit($request->length);
            $applications = $a->get();                 
            
            
            $data['data']=$applications;
            $data['draw']=$request->draw;
            return response()->json($data);
        }


    }

    public function view_results_upload(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $subjects = [];
        if (Auth::user()->hasRole('Admin')){ 
            $subjects = CourseSubject::where('semester','>',0)->where('status','=','1')->select('code as Code',DB::raw('CONCAT(code," ",name)  AS Name'))->orderBy('code')->get();
        }elseif(Auth::user()->hasPermissionTo('course:teacher')){
            $allowsSubjects = Auth::user()->subjects()->pluck('course_subjects.id')->toArray();
            $subjects = CourseSubject::whereIn('id',$allowsSubjects)->where('status','=','1')->select('code as Code',DB::raw('CONCAT(code," ",name) AS Name'))->orderBy('code')->get();
        }
        return view('admin.results.import-results', ['subjects'=>$subjects]);
    }

    public function upload_results(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['list' => 'required|file','subject'=>'required']);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            try {
                $userId = Auth::user()->id;

                TempResultsImport::where('uploaded_by','=',$userId)->delete();
                $path = storage_path() . '/data/Results/Upload/';
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $file = $request->file('list');        
                $file_name = time().'_'.str_replace(' ', '-', strtolower($file->getClientOriginalName()));
                
                // Import from the temporary upload file first so validation can run even if storage is not writable.
                Excel::import(new ResultsImport($userId), $file);

                @copy($file->getPathname(), $path.$file_name);
                SystemLog::create(['ip'=>$request->ip(),'user_id'=>$userId,'module'=>'Results','description'=>'Results file named <a href="'.url("/admin/settings/get-file").'?file=Results/Upload/'.$file_name.'">'.$file_name.'</a> for subject '.$request->subject.' was uploaded.']);
                
                $processingSubs = TempResultsImport::where('uploaded_by','=',$userId)->select('subject_code')->groupBy('subject_code')->get();
                if(count($processingSubs) != 1){
                    TempResultsImport::where('uploaded_by','=',$userId)->delete();
                    return response()->json(['status'=>-1,'msg'=>'Invalid students/subject code were detected.']);
                }

                $uploadedSubject = $processingSubs[0]->subject_code;
                $subjectExists = CourseSubject::where('code','=',$uploadedSubject)->exists();

                if(!$subjectExists){
                    TempResultsImport::where('uploaded_by','=',$userId)->delete();
                    return response()->json(['status'=>-1,'msg'=>'Invalid students/subject code were detected.']);
                }

                if($uploadedSubject != $request->subject){
                    TempResultsImport::where('uploaded_by','=',$userId)->delete();
                    return response()->json(['status'=>-2,'msg'=>'Subject code didn\'t match']);
                }
                return response()->json(['status'=>1,'msg'=>'Successfuly Uploaded']);
            } catch (\Exception $e) {
                \Log::error('Upload error: ' . $e->getMessage());
                TempResultsImport::where('uploaded_by','=',Auth::user()->id)->delete();
                return response()->json(['status'=>-1,'msg'=>'Upload failed: ' . $e->getMessage()]);
            }
            
        }
    }

    public function view_bulk_results_upload(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        return view('admin.results.import-results-bulk');        
    }

    public function upload_bulk_results(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['list' => 'required|file','year'=>'required']);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            $userId = Auth::user()->id;

            TempResultsImport::where('uploaded_by','=',$userId)->delete();
            $path = storage_path() . '/data/Results/Upload/';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $file = $request->file('list');        
            $file_name = time().'_'.str_replace(' ', '-', strtolower($file->getClientOriginalName()));
            
            // Import from the temporary upload file first so validation can run even if storage is not writable.
            Excel::import(new ResultsImportBulk($userId, $request->year), $file);

            $importedRows = TempResultsImport::where('uploaded_by','=',$userId)->count();
            if($importedRows <= 0){
                TempResultsImport::where('uploaded_by','=',$userId)->delete();
                return response()->json(['status'=>-1,'msg'=>'No valid records were found in the uploaded file.']);
            }

            @copy($file->getPathname(), $path.$file_name);
            SystemLog::create(['ip'=>$request->ip(),'user_id'=>$userId,'module'=>'Results','description'=>'Bulk Results file named <a href="'.url("/admin/settings/get-file").'?file=Results/Upload/'.$file_name.'">'.$file_name.'</a> for subject '.$request->subject.' was uploaded.']);
            
            return response()->json(['status'=>1,'msg'=>'Successfuly Uploaded']);
            
        }
    }

    public function get_uploaded_results(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $userId = Auth::user()->id;
        $data = TempResultsImport::where('uploaded_by','=',$userId)->select('registration_no','year','subject_code','marks','result')->get();

        return response()->json($data);
    }

    public function process_uploaded_results(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $userId = Auth::user()->id;

        $processingSubs = TempResultsImport::where('uploaded_by','=',$userId)->select('subject_code')->groupBy('subject_code')->get();

        if( !(Auth::user()->hasPermissionTo('results:upload-bulk') || Auth::user()->hasRole('Admin')) && (count($processingSubs)!= 1 || $processingSubs[0]->subject_code != $request->processingSubject)) {
            TempResultsImport::where('uploaded_by','=',$userId)->delete();
            return -1;
        }

        $sql = 'UPDATE temp_exam_results x INNER JOIN student_personal_details y ON x.registration_no= y.registration_no SET x.student_id = y.id WHERE x.uploaded_by = "'.$userId.'"';
        DB::update($sql);

        $sql = 'UPDATE temp_exam_results x INNER JOIN student_academic_details z ON x.student_id = z.student_id INNER JOIN course_subjects y ON x.subject_code = y.code AND z.regulation_id = y.regulation_id SET x.course_subject_id = y.id WHERE x.uploaded_by = "'.$userId.'"';
        DB::update($sql);

        // Fallback for subjects without regulation mismatch
        $sql = 'UPDATE temp_exam_results x INNER JOIN course_subjects y ON x.subject_code = y.code SET x.course_subject_id = y.id WHERE x.uploaded_by = "'.$userId.'" AND x.course_subject_id = 0';
        DB::update($sql);


        $invalidResutls = TempResultsImport::where('uploaded_by','=',$userId)->where(function ($query) {
            $query->where('student_id', '=', 0)->orWhere('course_subject_id','=',0);
        })->first();
        
        if(empty($invalidResutls)){
            if($request->update == 1){
                $sql = 'UPDATE student_exam_results y INNER JOIN (SELECT student_id, year, course_subject_id, MAX(marks) AS marks, MIN(result) AS result FROM temp_exam_results WHERE uploaded_by = "'.$userId.'" GROUP BY student_id, year, course_subject_id) x ON x.student_id = y.student_id AND x.year = y.year AND x.course_subject_id = y.course_subject_id SET y.marks = x.marks, y.result = x.result, y.status = 0';
                DB::update($sql);
    
                SystemLog::create(['ip'=>$request->ip(),'user_id'=>$userId,'module'=>'Results','description'=>'Existing exam results for subject '.$request->processingSubject.' was updated']);
    
            }
    
            $sql = 'DELETE x FROM temp_exam_results x INNER JOIN student_exam_results y ON x.student_id = y.student_id AND x.year = y.year  AND x.course_subject_id = y.course_subject_id WHERE x.uploaded_by = "'.$userId.'"';
            DB::delete($sql);
    
            $sql = 'INSERT INTO student_exam_results(student_id, year, course_subject_id, marks, result) SELECT student_id, year, course_subject_id, MAX(marks) AS marks, MIN(result) AS result FROM  temp_exam_results WHERE uploaded_by = "'.$userId.'" GROUP BY student_id, year, course_subject_id';
            DB::insert($sql);
    
            SystemLog::create(['ip'=>$request->ip(),'user_id'=>$userId,'module'=>'Results','description'=>'New results for subject '.$request->processingSubject.' was uploaded']);
    
            $sql = 'UPDATE student_exam_results SET status = 1 WHERE status = 0 AND result in ("A","A+","A-","B","B+","B-","C","C+") ';
            DB::update($sql);
    
            $sql = 'UPDATE student_exam_results SET status = -1 WHERE status = 0';
            DB::update($sql);
            
            TempResultsImport::where('uploaded_by','=',$userId)->delete();
            return response()->json(['status'=>1,'msg'=>'Records were Successfuly Uploaded']);
        }
        else{
            TempResultsImport::where('uploaded_by','=',$userId)->delete();
            return response()->json(['status'=>-1,'msg'=>'Invalid students/subject code were detected.']);
        }
    }

    public function process_bulk_uploaded_results(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $userId = Auth::user()->id;

        $processingRows = TempResultsImport::where('uploaded_by','=',$userId)->count();
        if($processingRows <= 0){
            return response()->json(['status'=>-1,'msg'=>'No uploaded records were found to process.']);
        }

        $sql = 'UPDATE temp_exam_results x INNER JOIN student_personal_details y ON x.registration_no= y.registration_no SET x.student_id = y.id WHERE x.uploaded_by = "'.$userId.'"';
        DB::update($sql);

        $sql = 'UPDATE temp_exam_results x INNER JOIN student_academic_details z ON x.student_id = z.student_id INNER JOIN course_subjects y ON x.subject_code = y.code AND z.regulation_id = y.regulation_id SET x.course_subject_id = y.id WHERE x.uploaded_by = "'.$userId.'"';
        DB::update($sql);

        // Fallback for subjects without regulation mismatch
        $sql = 'UPDATE temp_exam_results x INNER JOIN course_subjects y ON x.subject_code = y.code SET x.course_subject_id = y.id WHERE x.uploaded_by = "'.$userId.'" AND x.course_subject_id = 0';
        DB::update($sql);

        $invalidResults = TempResultsImport::where('uploaded_by','=',$userId)->where(function ($query) {
            $query->where('student_id', '=', 0)->orWhere('course_subject_id','=',0);
        })->first();

        if(!empty($invalidResults)){
            TempResultsImport::where('uploaded_by','=',$userId)->delete();
            return response()->json(['status'=>-1,'msg'=>'Invalid students/subject code were detected.']);
        }

        if($request->update == 1){
            $sql = 'UPDATE student_exam_results y INNER JOIN (SELECT student_id, year, course_subject_id, MAX(marks) AS marks, MIN(result) AS result FROM temp_exam_results WHERE uploaded_by = "'.$userId.'" GROUP BY student_id, year, course_subject_id) x ON x.student_id = y.student_id AND x.year = y.year AND x.course_subject_id = y.course_subject_id SET y.marks = x.marks, y.result = x.result, y.status = 0';
            DB::update($sql);

            SystemLog::create(['ip'=>$request->ip(),'user_id'=>$userId,'module'=>'Results','description'=>'Existing exam results were updated during bulk import']);
        }

        $sql = 'DELETE x FROM temp_exam_results x INNER JOIN student_exam_results y ON x.student_id = y.student_id AND x.year = y.year AND x.course_subject_id = y.course_subject_id WHERE x.uploaded_by = "'.$userId.'"';
        DB::delete($sql);

        $sql = 'INSERT INTO student_exam_results(student_id, year, course_subject_id, marks, result) SELECT student_id, year, course_subject_id, MAX(marks) AS marks, MIN(result) AS result FROM temp_exam_results WHERE uploaded_by = "'.$userId.'" GROUP BY student_id, year, course_subject_id';
        DB::insert($sql);

        SystemLog::create(['ip'=>$request->ip(),'user_id'=>$userId,'module'=>'Results','description'=>'New results for subject '.$request->processingSubject.' was uploaded']);

        $sql = 'UPDATE student_exam_results SET status = 1 WHERE status = 0 AND result in ("A","A+","A-","B","B+","B-","C","C+") ';
        DB::update($sql);

        $sql = 'UPDATE student_exam_results SET status = -1 WHERE status = 0';
        DB::update($sql);

        TempResultsImport::where('uploaded_by','=',$userId)->delete();

        return response()->json(['status'=>1,'msg'=>'Successfully Uploaded']);
    }

    /* ************************************************************************************************************************ */
    /* ************************************************* GPA ****************************************************************** */
    /* ************************************************************************************************************************ */
    

    public function gpa_process_view(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('results:dogpa') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){
            $regulations = Regulation::get();
            return view('admin.results.process-gpa',['regulations'=>$regulations]);
        }else{
            $col = [2=>'RegistrationNo',3=>'Name',4=>'Batch',5=>'IDNo'];

            $data['data']=['recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[],'draw'=>$request->draw];

            $search = trim($request->search);
            $regulation = trim($request->regulation);
            if(!empty($search) || !empty($request->year) || !empty($regulation)){
                $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                ->where('regulation_id','=',$regulation)
                ->select(
                    'student_personal_details.id AS ID',
                    'registration_no AS RegistrationNo',
                    'id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                    'master_batch.code AS Batch');


                if(!empty($request->search)){
                    $search = $request->search.'%';
                    $a->where(function($query) use($search){
                        return $query->where('registration_no','like',$search);
                    });
                }

                if(!empty($request->year)){
                    $a->where('student_academic_details.current_study_year','=',$request->year);
                }

                // if(!empty($request->batches) && (count($request->batches)==1 && !empty($request->batches[0]))){
                //     $a->whereIn('student_academic_details.batch',$request->batches);
                // }

                $ac = clone $a;
                $Count = $ac->count();

                $data['recordsTotal']=    $Count;
                $data['recordsFiltered']= $Count;

                $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
                // $a->offset($request->start)->limit($request->length);
                $applications = $a->get(); 

                $data['data']=$applications;
     
            }
            return response()->json($data);
        }
    }

    public function download_raw_gpa_file(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('results:dogpa') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        try {
            $data =[];
            $semester = $request->semester;
            $regulation = $request->regulation;
            $studentIds = explode(',',$request->students);
            
            \Log::info('download_raw_gpa_file - Parameters', ['semester' => $semester, 'regulation' => $regulation, 'studentIds' => $studentIds]);
            
            if (!isset($semester) || !is_numeric($semester)) {
                return back()->with('error', 'Invalid semester parameter');
            }
            
            if (empty($regulation) || !is_numeric($regulation)) {
                return back()->with('error', 'Invalid regulation parameter');
            }
            
            if (empty($studentIds)) {
                return back()->with('error', 'No students selected');
            }
            
            $students = Student::whereIn('id',$studentIds)->select('id','full_name','registration_no','index_no')->get();
            if($students){
                $studentIds = [];
                foreach($students as $std){
                   $data[$std->id] = ['id'=>$std->id,'full_name'=>$std->full_name,'registration_no'=>$std->registration_no,'index_no'=>$std->index_no,'results'=>[]];
                   array_push($studentIds, $std->id);
                } 

                $subjectIds = [];
                $subjects = [];
                if($semester > 0 ) {
                    $subjectArr = CourseSubject::where('regulation_id','=',$regulation)->where('semester','=',$semester)->select('id','code','credits')->orderBy('id')->get();
                    \Log::info('download_raw_gpa_file - Subject Query', ['regulation_id' => $regulation, 'semester' => $semester, 'count' => $subjectArr->count()]);
                }
                else $subjectArr = CourseSubject::where('regulation_id','=',$regulation)->where('year','>',0)->select('id','code','credits')->orderBy('id')->get();

                if(!empty($subjectArr) && $subjectArr->count() > 0){
                    foreach($subjectArr as $sub){
                        $subjects[$sub->id] = ['id'=>$sub->id,'code'=>$sub->code,'credits'=>$sub->credits];
                        $subjectIds[] = $sub->id;
                    }

                    $maxResult = StudentExamResult::whereIn('student_id',$studentIds)->whereIn('course_subject_id',$subjectIds)->groupBy('student_id')->groupBy('course_subject_id')->select('student_id','course_subject_id',DB::raw('MAX(marks) as marks'));

                    $resultsTemp = StudentExamResult::joinSub($maxResult, 'max_result', function ($join) {
                                            $join->on('student_exam_results.student_id', '=', 'max_result.student_id')
                                                ->on('student_exam_results.course_subject_id', '=', 'max_result.course_subject_id')
                                                ->on('student_exam_results.marks', '=', 'max_result.marks');
                                        })
                                        ->leftJoin('student_semester_registration_subjects',function ($join) {
                                            $join->on('student_exam_results.student_id', '=', 'student_semester_registration_subjects.student_id')
                                                ->on('student_exam_results.course_subject_id', '=', 'student_semester_registration_subjects.subject_id');
                                        })
                                        ->select(
                                            'student_exam_results.student_id',
                                            'student_exam_results.course_subject_id as subject_id',
                                            'student_exam_results.marks',
                                            'student_exam_results.result as grade',
                                            DB::raw('IFNULL(student_semester_registration_subjects.type,1) as type')
                                        )
                                        ->get();
                    if($resultsTemp){
                        foreach($resultsTemp as $row){
                            $data[$row->student_id]['results'][$row->subject_id] = ['marks'=>$row->marks, 'grade'=>$row->grade, 'type'=>$row->type];
                        }
                    }
                    $grades =  Arr::pluck(CourseGrade::all()->toArray(),'grade_point','grade');

                    return Excel::download(new GPAExport($data,$subjects,$grades), 'raw_gpa_file_'.$semester.'.xlsx');
                } else {
                    \Log::warning('download_raw_gpa_file - No subjects found', ['regulation_id' => $regulation, 'semester' => $semester]);
                    return back()->with('error', 'No subjects found for the selected regulation and semester');
                }
            } else {
                return back()->with('error', 'No students found');
            }
        } catch (\Exception $e) {
            \Log::error('download_raw_gpa_file - Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'An error occurred while processing the export: ' . $e->getMessage());
        }
    }

    public function view_upload_gpa(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:dogpa') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        return view('admin.results.import-gpa');
    }

    public function upload_gpa(Request $request){
        if (!(Auth::user()->hasPermissionTo('results:dogpa') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $validator = Validator::make($request->all(), ['list' => 'required|file|mimes:csv,txt,xlsx,xls','ProcessingSemester'=>'required']);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{

            $columns = ['0'=>'final_gpa','1'=>'s1_gpa','2'=>'s2_gpa','3'=>'s3_gpa','4'=>'s4_gpa','5'=>'s5_gpa','6'=>'s6_gpa','7'=>'s7_gpa','8'=>'s8_gpa','9'=>'y2_gpa'];
            
            $semester = intval($request->ProcessingSemester);
            if(isset($columns[$semester])){

                DB::table('temp_gpa_upload')->truncate();
                
                $path = storage_path() . '/data/GPA/Upload/';
                $file = $request->file('list');        
                $file_name = str_replace(' ', '-', strtolower($file->getClientOriginalName()));
                
                //uplaod the record
                if($file->move($path, $file_name)){
                    Excel::import(new GPAImport(), $path.$file_name);
                };

                $sql = 'UPDATE temp_gpa_upload x INNER JOIN student_personal_details y ON x.registration_no= y.registration_no SET x.student_id = y.id';
                DB::update($sql);

                $sql = 'UPDATE temp_gpa_upload x INNER JOIN student_academic_details y on x.student_id = y.student_id SET y.'.$columns[$semester].'= x.gpa';
                DB::update($sql);

                if($semester==0){ //calculate performance class
                    $performanceClasses = PerformanceClass::select('class','lower_limit')->orderBy('lower_limit')->get();
                    foreach($performanceClasses as $class){
                        $sql = 'UPDATE temp_gpa_upload x INNER JOIN student_academic_details y on x.student_id = y.student_id SET y.class= "'.$class->class.'" where final_gpa >= "'.$class->lower_limit.'"';
                        DB::update($sql);
                    }
                }
                return 1;
            }
        }
        return -1;
    }
    
}
