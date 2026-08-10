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
use App\Regulation;
use App\StudentAccDetail;
use App\SpecializationRequest;
use App\StudentExam;
use App\StudentExamSubject;
use App\CourseSubject;
use App\CourseSchedule;
use App\StudentExamResult;
use App\CourseGrade;
use \App\SystemLog;

use App\Exports\ApplicationExport;

class AdminExamController extends Controller
{
    private $year;

    public function __construct(){
        $this->middleware('auth:users');
        $this->year = settings('year');
    }

    public function index(Request $request){
        if (!(Auth::user()->hasPermissionTo('examapp:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        return view('admin.exam.index');
    }

      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request){
        if (!(Auth::user()->hasPermissionTo('examapp:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $data = [];
        if(empty($request->semester)){
            return response()->json([
                'draw' => (int) ($request->draw ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }
        if(!isset($request->type) || $request->type == 'json'){

            $col = [2=>'RegistrationNo',3=>'Name',4=>'Batch',5=>'IDNo'];

            $subjectAppliedApps = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                    ->where('student_exam.year','=',$this->year)
                                    ->where('student_exam.semester','=',$request->semester)
                                    ->groupBy('student_exam_subjects.student_exam_id')
                                    ->orderBy('student_exam_subjects.student_exam_id')
                                    ->pluck('student_exam_subjects.student_exam_id')
                                    ->toArray();

            $a = Student::join('student_exam','student_personal_details.id','=','student_exam.student_id')
                    ->join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                    ->whereIn('student_exam.id',$subjectAppliedApps)
                    ->select(
                        'student_exam.id AS ID',
                        'registration_no AS RegistrationNo',
                        'id_no AS IDNo',
                            DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        'master_batch.code AS Batch');

            $searchValue = trim($request->input('search') ?? $request->input('search.value') ?? '');
            if(!empty($searchValue)){
                $search = $searchValue.'%';
                $a->where(function($query) use($search){
                    return $query->where('registration_no','like',$search)->orWhere('id_no','like',$search);
                });
            }

            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $applications = $a->get();                 
            
            
            $data['data']=$applications;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function excel_export_applications(Request $request){
        if (!(Auth::user()->hasPermissionTo('examapp:export') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $data = [];
        if(empty($request->semester)) return;
        else{

            $semester = filter_var($request->semester,FILTER_SANITIZE_NUMBER_INT);
            $search = preg_replace('/[^a-zA-Z0-9\/]/i', '',trim($request->search));
            $appliedStudents = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                    ->where('student_exam.year','=',$this->year)
                                    ->where('student_exam.semester','=',$semester)
                                    ->where('student_exam_subjects.registered','=','1')
                                    ->groupBy('student_exam_subjects.student_exam_id')
                                    ->groupBy('student_exam.student_id')
                                    ->orderBy('student_exam.student_id')
                                    ->pluck('student_exam.student_id')
                                    ->toArray();

            $q = Student::whereIn('student_personal_details.id',$appliedStudents)
                            ->select(
                                'student_personal_details.id',
                                'student_personal_details.registration_no',
                                'student_personal_details.id_no',
                                'student_personal_details.index_no',
                                'student_personal_details.full_name',
                                DB::raw('CONCAT(initials," ",name_marking) AS name')
                            );
            if(!empty($search))$q->where('student_personal_details.registration_no','like',$search.'%')->orWhere('student_personal_details.id_no','like',$search.'%');
            $students = $q->get();
            if(empty($students) || $students->isEmpty()) return ;

            foreach($students as $std){
                $data[$std->id] = ['name'=>$std->name,'full_name'=>$std->full_name,'regno'=>$std->registration_no,'indexno'=>$std->index_no,'idno'=>$std->id_no,'subjects'=>[]];
            }

            $subjects = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                ->where('student_exam.year','=',$this->year)
                                ->where('student_exam.semester','=',$request->semester)
                                ->where('student_exam_subjects.registered','=','1')
                                ->select('student_exam.student_id','student_exam_subjects.subject_id as id','student_exam_subjects.status')
                                ->get();

            foreach($subjects as $subject){
                $data[$subject->student_id]['subjects'][$subject->id] = $subject->status;
            };

            unset($subjects);
            $subjects = [];
            $temp = CourseSubject::where('semester','=',$semester)->where('status','=','1')->select('id','code','name')->get();

            foreach($temp as $subject){
                $subjects[$subject->id] = $subject->code.' '.$subject->name;
            }
            return Excel::download(new ApplicationExport($data,$subjects), 'exam_applications_'.$semester.'.xlsx');

            
        }
    }

    public function download_applications(Request $request){
        if (!(Auth::user()->hasPermissionTo('examapp:export') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        return view('admin.exam.download');
    }

    public function print_applications(Request $request){
        if (!(Auth::user()->hasPermissionTo('examapp:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $data = [];
       
        $search = preg_replace('/[^a-zA-Z0-9\/]/i', '',trim($request->search));
        $year = [1=>1,2=>1,3=>2,4=>2,5=>3,6=>3,7=>4,8=>4];
        $semester = filter_var($request->semester,FILTER_SANITIZE_NUMBER_INT);
        if(empty($semester) || !isset($year[$semester])) return;
        // else{
            
            
        if($request->type=='R'){ //repeat
            $accYear = $year[$semester];

            $SemCodes = [];
            for($i=1; $i <= ($accYear*2); $i++){
                Array_push($SemCodes,'SM'.$i.'ExAppDL');
            }

            $activeSemesters =   CourseSchedule::whereIn('code',$SemCodes)->where('is_enabled','=','1')->pluck('raw_code')->toArray();   
             
            $appliedStudents = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                    ->join('student_academic_details','student_exam.student_id','=','student_academic_details.student_id')
                                    ->where('student_exam.year','=',$this->year)
                                    ->where('student_academic_details.current_study_year','=',$accYear)
                                    ->whereIn('student_exam.semester',$activeSemesters)
                                    ->where('student_exam_subjects.registered','=','1')
                                    ->where('student_exam_subjects.is_repeat','=','1')
                                    ->where('student_exam_subjects.status','=','1')
                                    ->groupBy('student_exam.student_id')
                                    ->orderBy('student_exam.student_id')
                                    ->pluck('student_exam.student_id')
                                    ->toArray();

            $q = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                            ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                            ->join('master_degree_programs','master_batch.program_id','=','master_degree_programs.id')
                            ->whereIn('student_personal_details.id',$appliedStudents)
                            ->select(
                                'student_personal_details.id',
                                'student_personal_details.registration_no',
                                'student_personal_details.id_no',
                                'student_personal_details.index_no',
                                'student_personal_details.full_name',
                                'master_degree_programs.short_name as program',
                                DB::raw('CONCAT(initials," ",name_marking) AS name')
                            );
            if(!empty($search))$q->where('student_personal_details.registration_no','like',$search.'%')->orWhere('student_personal_details.id_no','like',$search.'%');
            $students = $q->get();
            if(empty($students) || $students->isEmpty()) {
                return redirect()->back()->with('error', 'No admission records found for the selected semester/search criteria.');
            }

            foreach($students as $std){
                $data[$std->id] = ['name'=>$std->name,'full_name'=>$std->full_name,'regno'=>$std->registration_no,'indexno'=>$std->index_no,'idno'=>$std->id_no,'program'=>$std->program,'subjects'=>[]];
            }

            $subjects = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                ->where('student_exam.year','=',$this->year)
                                ->whereIn('student_exam.semester',$activeSemesters)
                                ->whereIn('student_exam.student_id',$appliedStudents)
                                ->where('student_exam_subjects.registered','=','1')
                                ->where('student_exam_subjects.is_repeat','=','1')
                                ->where('student_exam_subjects.status','=','1')
                                ->select('student_exam.student_id','student_exam_subjects.subject_id as id','student_exam_subjects.status')
                                ->get();

            foreach($subjects as $subject){
                $data[$subject->student_id]['subjects'][$subject->id] = $subject->status;
            };

            unset($subjects);
            $subjects = [];
            $temp = CourseSubject::where('status','=','1')->select('id','code','name')->get();


            foreach($temp as $subject){
                $subjects[$subject->id] = ['code'=>$subject->code,'name'=>$subject->name];
            }

            $code = 'SM'.$semester.'Exam';
            $examSchedule = CourseSchedule::where('code',$code)->select(
                DB::raw('DATE_FORMAT(start_date, "%b") as start_month'), 
                DB::raw('DATE_FORMAT(end_date, "%b") as end_month'),
                DB::raw('YEAR(end_date) as end_year'))->first();

            $exam = ['semester'=>$semester,'acc_year'=>$this->year,'year'=>$year[$semester],'period' => $examSchedule->start_month.(($examSchedule->start_month != $examSchedule->end_month)?'. / '.$examSchedule->end_month.'.':'').' '.$examSchedule->end_year];

            SystemLog::create(['ip'=>$request->ip(),'user_id'=>Auth::user()->id,'module'=>'Exam','description'=>'Repeat Exam Admission were generated for semester '.$semester]);
            $pdf = PDF::loadView('admin.exam.pdf.application',['data'=>$data,'exam'=>$exam,'subjects'=>$subjects,'type'=>'R'] );                      
            return $pdf->download('application-repeat.pdf');
            
        }else{
            
            $appliedStudents = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                    ->where('student_exam.year','=',$this->year)
                                    ->where('student_exam.semester','=',$semester)
                                    ->where('student_exam_subjects.registered','=','1')
                                    ->where('student_exam_subjects.is_repeat','=','0')
                                    ->where('student_exam_subjects.status','=','1')
                                    ->groupBy('student_exam_subjects.student_exam_id')
                                    ->groupBy('student_exam.student_id')
                                    ->orderBy('student_exam.student_id')
                                    ->pluck('student_exam.student_id')
                                    ->toArray();

            $q = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                            ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                            ->join('master_degree_programs','master_batch.program_id','=','master_degree_programs.id')
                            ->whereIn('student_personal_details.id',$appliedStudents)
                            ->select(
                                'student_personal_details.id',
                                'student_personal_details.registration_no',
                                'student_personal_details.id_no',
                                'student_personal_details.index_no',
                                'student_personal_details.full_name',
                                DB::raw('CONCAT(initials," ",name_marking) AS name'),
                                'master_degree_programs.short_name as program'
                            );
            if(!empty($search))$q->where('student_personal_details.registration_no','like',$search.'%')->orWhere('student_personal_details.id_no','like',$search.'%');
            $students = $q->get();
            if(empty($students) || $students->isEmpty()) {
                return redirect()->back()->with('error', 'No admission records found for the selected semester/search criteria.');
            }

            foreach($students as $std){
                $data[$std->id] = ['name'=>$std->name,'full_name'=>$std->full_name,'regno'=>$std->registration_no,'indexno'=>$std->index_no,'idno'=>$std->id_no,'program'=>$std->program,'subjects'=>[]];
            }

            $subjects = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                ->where('student_exam.year','=',$this->year)
                                ->where('student_exam.semester','=',$request->semester)
                                ->where('student_exam_subjects.registered','=','1')
                                ->where('student_exam_subjects.is_repeat','=','0')
                                ->where('student_exam_subjects.status','=','1')
                                ->select('student_exam.student_id','student_exam_subjects.subject_id as id','student_exam_subjects.status')
                                ->get();

            foreach($subjects as $subject){
                $data[$subject->student_id]['subjects'][$subject->id] = $subject->status;
            };

            unset($subjects);
            $subjects = [];
            $temp = CourseSubject::where('semester','=',$semester)->where('status','=','1')->select('id','code','name')->get();


            foreach($temp as $subject){
                $subjects[$subject->id] = ['code'=>$subject->code,'name'=>$subject->name];
            }
            $code = 'SM'.$semester.'Exam';
            $examSchedule = CourseSchedule::where('code',$code)->select(
                    DB::raw('DATE_FORMAT(start_date, "%b") as start_month'), 
                    DB::raw('DATE_FORMAT(end_date, "%b") as end_month'),
                    DB::raw('YEAR(end_date) as end_year'))->first();

            $exam = ['semester'=>$semester,'acc_year'=>$this->year,'year'=>$year[$semester],'period' => $examSchedule->start_month.(($examSchedule->start_month != $examSchedule->end_month)?'. / '.$examSchedule->end_month.'.':'').' '.$examSchedule->end_year];

            SystemLog::create(['ip'=>$request->ip(),'user_id'=>Auth::user()->id,'module'=>'Exam','description'=>'Proper Exam Admission were generated for semester '.$semester]);
            $pdf = PDF::loadView('admin.exam.pdf.application',['data'=>$data,'exam'=>$exam,'subjects'=>$subjects,'type'=>'P'] );                      
            return $pdf->download('application.pdf');


        }
        // }

    }

    public function approve_by_subject(Request $request) {
        if (!(Auth::user()->hasPermissionTo('examapp:approve') || Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){
            $regulations = Regulation::select('name', 'id')->get();
            return view('admin.exam.approve-requests',['regulations'=>$regulations]);
        }else{

            $subject  = CourseSubject::where('id','=',$request->subjectId)->first();
            if(empty($subject)) return response()->json([$subject]);

            $col = [2=>'RegistrationNo',3=>'Name',4=>'IDNo',5=>'Status'];

            // DB::enableQueryLog();
            $a = Student::join('student_exam','student_personal_details.id','=','student_exam.student_id')
                    ->join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')

                    ->where('student_exam.year','=',$this->year)
                    ->where('student_exam_subjects.subject_id','=',$subject->id)
                    ->where('student_exam_subjects.registered','=','1')
                    ->select(
                        'student_exam_subjects.id AS ID',
                        'student_personal_details.id AS StudentID',
                        'student_exam_subjects.status AS StatusID',
                        'registration_no AS RegistrationNo',
                        'id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        DB::raw('CASE WHEN student_exam_subjects.status = 1 THEN "Approved" ELSE "Pending" END as Status'),
                        'student_exam_subjects.is_repeat AS Repeat',
                        DB::raw("'$subject->name' AS Subject")
                        );

            if(!empty($request->search)){
                $search = $request->search.'%';
                $a->where(function($query) use($search){
                    return $query->where('registration_no','like',$search)->orWhere('id_no','like',$search);
                });
            }

            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;

            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $applications = $a->get();                 
            
            $data['data']=$applications;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function view_application(Request $request, $id){
        if (!(Auth::user()->hasPermissionTo('examapp:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $application = StudentExam::where('id','=',$id)->first();
        if(!empty($application)){
            $student = Student::where('id','=',$application->student_id)->first();
            $specialization = $student->AcademicDetail->specialization_id;
            $stdRegulation = Regulation::where('id','=',$student->AcademicDetail->regulation_id)->first();



            $subjects = StudentExamSubject::join('course_subjects','student_exam_subjects.subject_id','=','course_subjects.id')
                            ->where('student_exam_id','=',$application->id)
                            ->where('student_exam_subjects.registered','=','1')
                            ->select('course_subjects.name','course_subjects.code','student_exam_subjects.*')
                            ->orderBy('type')->orderBy('display_order')
                            ->get();
            return view('admin.exam.application',['student'=>$student,'application'=>$application,'subjects'=>$subjects,'regulation'=>$stdRegulation]);
        }else return redirect()->route('admin.exam');
        

        
    }

    public function get_subjects(Request $request){
        $subjects = [];
        if (Auth::user()->hasRole('Admin')){ 
            $subjects = CourseSubject::where('regulation_id','=',$request->regulation)->where('semester','=',$request->semester)->where('status','=','1')->select('id as ID',DB::raw('CONCAT(name," ( ",code," )") AS Name'))->orderBy('name')->get();
        }else{
            $allowsSubjects = Auth::user()->subjects()->pluck('course_subject_id')->toArray();
            $subjects = CourseSubject::where('regulation_id','=',$request->regulation)->where('semester','=',$request->semester)->whereIn('id',$allowsSubjects)->where('status','=','1')->select('id as ID',DB::raw('CONCAT(name," ( ",code," )") AS Name'))->orderBy('name')->get();

        }
        return response()->json($subjects);

    }

    public function approve_application_subject(Request $request){
        if (!(Auth::user()->hasPermissionTo('examapp:approve') || Auth::user()->hasPermissionTo('results:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $table = (new StudentExamSubject())->getTable();

        if(!empty($request->Approved)){
            DB::table($table)->where('subject_id','=',$request->Subject)->whereIn('id',$request->Approved)->update(['status'=>1]);
            foreach($request->Approved as $eid){
                SystemLog::create(['ip'=>$request->ip(),'user_id'=>Auth::user()->id,'module'=>'Exam','description'=>'Exam subject request for subject id '.$request->Subject.' for application id '.$eid.' was approved']);
            }

        }
        if(!empty($request->Pending)){
            DB::table($table)->where('subject_id','=',$request->Subject)->whereIn('id',$request->Pending)->update(['status'=>0]);
            foreach($request->Pending as $eid){
            SystemLog::create(['ip'=>$request->ip(),'user_id'=>Auth::user()->id,'module'=>'Exam','description'=>'Exam subject request for subject id '.$request->Subject.' for application id '.$eid.' was rejected']);
            }

        }
        return 1;
    }

}
