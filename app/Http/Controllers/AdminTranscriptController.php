<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Carbon\Carbon;
use Auth;
use DB;
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
use App\Batch;

use App\Settings;


class AdminTranscriptController extends Controller
{
    private $year;

    public function __construct(){
        $this->middleware('auth:users');
        $this->year = settings('year');
    }

    public function print_semester_transcripts(Request $request){
        if (!(Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        if(!isset($request->type) || $request->type == 'view'){
            $regulations = Regulation::get();
            return view('admin.transcripts.print-semester-results');
        }else{
            $col = [2=>'RegistrationNo',3=>'RegistrationNo',4=>'Name',5=>'Batch',6=>'IDNo'];
            $data['data']=['recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[],'draw'=>$request->draw];
            $search = trim($request->search);

            if(!empty($request->semester) && !empty($request->year) ){


                $studentList = CourseSubject::join('student_exam_results','course_subjects.id','=','student_exam_results.course_subject_id')
                                    ->where('student_exam_results.year','=',intval($request->year))
                                    ->where('course_subjects.semester','=',intval($request->semester))
                                    ->groupBy('student_exam_results.student_id')
                                    ->pluck('student_exam_results.student_id')
                                    ->toArray();


                $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                        ->join('master_batch','student_academic_details.batch','=','master_batch.id')       
                        ->whereIn('student_personal_details.id',$studentList)
                        ->select(
                            'student_personal_details.id AS ID',
                            'registration_no AS RegistrationNo',
                            'id_no AS IDNo',
                                DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                            'master_batch.code AS Batch'
                        );
                if(!empty($request->search)){
                    $search = $request->search.'%';
                    $a->where(function($query) use($search){
                        return $query->where('registration_no','like',$search);
                    });
                }

                $ac = clone $a;
                $Count = $ac->count();

                $data['recordsTotal']=    $Count;
                $data['recordsFiltered']= $Count;

                $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
                $applications = $a->get(); 

                $data['data']=$applications;

            }
            return response()->json($data);
        }
    }

    public function print_semester_transcripts_download(Request $request){
        if (!(Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');


        $stdList = trim($request->students,',');
        if(!empty($stdList)){
            $semester = intval($request->semester);
            $studentIds = explode(',',$stdList);

            $subQuery = "( SELECT student_id, course_subject_id, MAX(id) as id FROM student_exam_results WHERE year < $request->year AND student_id IN ($stdList) GROUP BY student_id, course_subject_id ) AS pastResults";
    
            // DB::table('student_exam_results')->where('student_exam_results.year','<',$pYear)->whereIn('student_id',$pIds)->groupBy('student_id')->groupBy('course_subject_id')->select(DB::raw(' MAX(id) as id'),'student_id','course_subject_id')->toSql();
    
            $ex = StudentExamResult::join('course_subjects','student_exam_results.course_subject_id','=','course_subjects.id')
                    ->leftJoin(DB::raw( $subQuery ),function($query){
                        $query->on('student_exam_results.student_id','=','pastResults.student_id')
                            ->on('student_exam_results.course_subject_id','=','pastResults.course_subject_id');
                    })
                    ->where('student_exam_results.year','=',$request->year)
                    ->whereIn('student_exam_results.student_id',$studentIds)
                    ->where('course_subjects.semester','=',$semester)
                    ->select(
                        'student_exam_results.student_id',
                        'student_exam_results.course_subject_id',
                        'student_exam_results.marks',
                        'student_exam_results.result as grade',
                        'course_subjects.code',
                        'course_subjects.name'
                    );
    
            if($request->type == 1){ //proper
                $ex->whereNull('pastResults.id');
            }else{
                $ex->whereNotNull('pastResults.id');
            }
            $resutls = $ex->get();

            $data = [];
            $processingStd = [];
            foreach($resutls as $row){
                $processingStd[] = $row->student_id;
                $data[$row->student_id]['results'][$row->course_subject_id] =  ['subject_name'=>$row->name,'subject_code'=>$row->code, 'marks'=>$row->marks, 'grade'=>$row->grade, 'type'=>$row->type];
            }

            $students = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                            ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                            ->join('master_degree_programs','master_batch.program_id','=','master_degree_programs.id')
                            ->whereIn('student_personal_details.id',$processingStd)
                            ->select(
                                'student_personal_details.id',
                                'student_personal_details.full_name',
                                'student_personal_details.registration_no',
                                'student_personal_details.index_no',
                                'student_academic_details.s'.$semester.'_gpa as gpa',
                                'student_academic_details.degree_effective_date',
                                'master_degree_programs.name as program',
                        )->get();
            foreach($students as $std){   
                $data[$std->id]['id']=$std->id;                
                $data[$std->id]['full_name']=$std->full_name;
                $data[$std->id]['registration_no']=$std->registration_no;
                $data[$std->id]['index_no']=$std->index_no;
                $data[$std->id]['gpa']=$std->gpa;      
                $data[$std->id]['program']=\strtoupper($std->program);                
            }

            $date = Carbon::now()->format('d.m.Y');

            $grades =  CourseGrade::where('upper_mark_limit','>',0)->orderBy('upper_mark_limit','desc')->get();

            $year = [1=>'I',2=>'I',3=>'II',4=>'II',5=>'III',6=>'III',7=>'IV',8=>'IV',9=>'V',10=>'V'];
            $semester = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'V1',7=>'VII',8=>'VIII',9=>'IX',10=>'X'];

            $examString = 'YEAR '.$year[$request->semester].' SEMESTER '.$semester[$request->semester].' EXAMINATION ';
            
            $examStDate = Carbon::parse($request->startdate);
            $examEdDate = Carbon::parse($request->enddate);
            $examDateString = ($examStDate->format('F')!=$examEdDate->format('F'))?$examStDate->format('F').'/'.$examEdDate->format('F Y'):$examStDate->format('F Y');

            // return  view('admin.transcripts.pdf.semester-result',['data'=>$data,'grades'=>$grades,'examString'=>$examString,'examDateString'=>$examDateString  ]);
            $pdf = PDF::loadView('admin.transcripts.pdf.semester-result',['data'=>$data,'grades'=>$grades,'examString'=>$examString,'examDateString'=>$examDateString,'date'=>$date,'type'=>$request->type  ])->setPaper('a4', 'portrait');                      
            return $pdf->download('semester-results.pdf');
        }
    }

    public function print_final_transcripts_view(Request $request){
        if (!(Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){
            $regulations = Regulation::get();
            $batches =  Arr::pluck(Batch::all()->toArray(), 'code', 'id');
            return view('admin.transcripts.print-final-transcripts',['regulations'=>$regulations,'batches'=>$batches]);
        }else{
            // dd($request);
            $col = [2=>'RegistrationNo',3=>'Name',4=>'Batch',5=>'IDNo'];

            $data['data']=['recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[],'draw'=>$request->draw];
            if(!empty($request->batch)){
                $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    ->join('master_batch','student_academic_details.batch','=','master_batch.id')                        
                    ->where('regulation_id','=',$request->regulation)
                    ->where('student_academic_details.batch','=',$request->batch)
                    ->select(
                        'student_personal_details.id AS ID',
                        'registration_no AS RegistrationNo',
                        'id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        'master_batch.code AS Batch');

                $search = trim($request->search);
                if(!empty($search)){
                    $a->where('registration_no','like',$search.'%');
                }


                $ac = clone $a;
                $Count = $ac->count();

                $data['recordsTotal']=    $Count;
                $data['recordsFiltered']= $Count;

                $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
                $applications = $a->get(); 

                $data['data']=$applications;
     
            }
            return response()->json($data);
        }        
    }


    public function print_final_transcripts_download(Request $request){
        if (!(Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');

        $data =[];
        // $year = $request->year;
        $regulation = $request->regulation;
        $studentIds = explode(',',$request->students);
        $students = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                    ->join('master_degree_programs','master_batch.program_id','=','master_degree_programs.id')
                    ->leftJoin('master_course_specialization_categories','student_academic_details.specialization_id','=','master_course_specialization_categories.id')
                    ->whereIn('student_personal_details.id',$studentIds)
                    ->select(
                        'student_personal_details.id',
                        'student_personal_details.full_name',
                        'student_personal_details.registration_no',
                        'student_personal_details.index_no',
                        'student_academic_details.final_gpa',
                        'student_academic_details.class',
                        'student_academic_details.degree_effective_date',
                        DB::raw('IFNULL(master_course_specialization_categories.name,"") as specialization'),
                        'master_degree_programs.name as program',
                    )->get();

        if($students){
            $studentIds = [];
            foreach($students as $std){
               $data[$std->id] = ['id'=>$std->id,'full_name'=>$std->full_name,'registration_no'=>$std->registration_no,'index_no'=>$std->index_no,'gpa'=>$std->final_gpa,'effective_date'=>$std->degree_effective_date,'class'=>$std->class,'program'=>$std->program,'specialization'=>$std->specialization,'results'=>[]];
               $studentIds[] =$std->id;
            } 

            $subjectIds = [];
            $subjects = []; 
            $subjectArr = CourseSubject::where('regulation_id','=',$regulation)->select('id','year','semester','code','name','credits')->orderBy('display_order')->get();

            if(!empty($subjectArr)){
                foreach($subjectArr as $sub){
                    $semester = (int)$sub->semester;
                    $subjects[$sub->year][$semester][$sub->id] = ['id'=>$sub->id,'code'=>$sub->code,'name'=>$sub->name,'credits'=>$sub->credits];
                    $subjectIds[] =$sub->id;
                }

                $maxResult = DB::table('student_exam_results')->whereIn('student_id',$studentIds)->whereIn('course_subject_id',$subjectIds)->groupBy('student_id')->groupBy('course_subject_id')->select('student_id','course_subject_id',DB::raw('MAX(marks) as marks'));

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
                $grades =  CourseGrade::where('upper_mark_limit','>',0)->orderBy('upper_mark_limit','desc')->get();
                $optSubSemStart = Settings::where('name','=','sp_select_semster')->first();

                $submitto = $request->submitto;

                $pdf = PDF::loadView('admin.transcripts.pdf.full-transcript-with-grades',['data'=>$data,'subjects'=>$subjects,'grades'=>$grades,'optSubSemStart'=>$optSubSemStart->value,'submitto'=>$submitto])->setPaper('a4', 'portrait');                      
                return $pdf->download('transcripts.pdf');

            }
        }
    }


    public function print_final_detail_certificate_view(Request $request){
        if (!(Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){
            $regulations = Regulation::get();
            $batches =  Arr::pluck(Batch::all()->toArray(), 'code', 'id');
            return view('admin.transcripts.print-final-detail-certificate',['regulations'=>$regulations,'batches'=>$batches]);
        }else{
            $col = [2=>'RegistrationNo',3=>'Name',4=>'Batch',5=>'IDNo'];

            $data['data']=['recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[],'draw'=>$request->draw];

            if(!empty($request->batch)){
                $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    ->join('master_batch','student_academic_details.batch','=','master_batch.id')                        
                    ->where('regulation_id','=',$request->regulation)
                    ->where('student_academic_details.batch','=',$request->batch)

                    ->select(
                        'student_personal_details.id AS ID',
                        'registration_no AS RegistrationNo',
                        'id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        'master_batch.code AS Batch');


                $search = trim($request->search);
                if(!empty($search)){
                    $a->where('registration_no','like',$search.'%');
                }

                $ac = clone $a;
                $Count = $ac->count();

                $data['recordsTotal']=    $Count;
                $data['recordsFiltered']= $Count;

                $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
                $applications = $a->get(); 

                $data['data']=$applications;
     
            }
            return response()->json($data);
        }        
    }


    public function print_final_detail_certificate_download(Request $request){
        if (!(Auth::user()->hasPermissionTo('transcript:print') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');
        
        $data =[];
        // $year = $request->year;
        $regulation = $request->regulation;
        $studentIds = explode(',',$request->students);
        $students = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
            ->join('master_batch','student_academic_details.batch','=','master_batch.id')
            ->join('master_degree_programs','master_batch.program_id','=','master_degree_programs.id')
            ->leftJoin('master_course_specialization_categories','student_academic_details.specialization_id','=','master_course_specialization_categories.id')
            ->whereIn('student_personal_details.id',$studentIds)
            ->select(
                'student_personal_details.id',
                'student_personal_details.full_name',
                'student_personal_details.registration_no',
                'student_personal_details.index_no',
                'student_academic_details.final_gpa',
                'student_academic_details.class',
                'student_academic_details.degree_effective_date',
                DB::raw('IFNULL(master_course_specialization_categories.name,"") as specialization'),
                'master_degree_programs.special_name as program',
            )->get();

        if($students){
            $studentIds = [];
            foreach($students as $std){
               $data[$std->id] = ['id'=>$std->id,'full_name'=>$std->full_name,'registration_no'=>$std->registration_no,'index_no'=>$std->index_no,'gpa'=>$std->final_gpa,'effective_date'=>$std->degree_effective_date,'class'=>$std->class,'program'=>$std->program,'specialization'=>$std->specialization,'results'=>[]];
               array_push($studentIds, $std->id);
            } 

            $subjectIds = [];
            $subjects = [1=>[1=>[],2=>[]],2=>[3=>[],4=>[]],3=>[5=>[],6=>[]],4=>[7=>[],8=>[]]]; 
            $subjectArr = CourseSubject::where('regulation_id','=',$regulation)->select('id','year','semester','code','name')->orderBy('display_order')->get();

            if(!empty($subjectArr)){
                foreach($subjectArr as $sub){
                    $semester = (int)$sub->semester;
                    $subjects[$sub->year][$semester][$sub->id] = ['id'=>$sub->id,'code'=>$sub->code,'name'=>$sub->name];
                    $subjectIds[] =$sub->id;
                }

                $maxResult = DB::table('student_exam_results')->whereIn('student_id',$studentIds)->whereIn('course_subject_id',$subjectIds)->groupBy('student_id')->groupBy('course_subject_id')->select('student_id','course_subject_id',DB::raw('MAX(marks) as marks'));

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
                $grades =  CourseGrade::where('upper_mark_limit','>',0)->orderBy('upper_mark_limit','desc')->get();
                $optSubSemStart = Settings::where('name','=','sp_select_semster')->first();


                $pdf = PDF::loadView('admin.transcripts.pdf.full-detail-certificate',['data'=>$data,'subjects'=>$subjects,'grades'=>$grades,'optSubSemStart'=>$optSubSemStart->value] )->setPaper('a4', 'portrait');                      
                return $pdf->download('detail-certificate.pdf');

            }
        }
    }
}
