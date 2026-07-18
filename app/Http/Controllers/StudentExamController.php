<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Form;
use Validator;
use Redirect;
use Session;
use DB;
use Auth;
use PDF;
// use Config;

use \Carbon\Carbon;

use App\Student;
use App\StudentAccDetail;
use App\StudentExam;
use App\StudentExamSubject;
use App\StudentExamResult;

use App\CourseSubject;
use App\CourseSchedule;

class StudentExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
        $this->year = settings('year');

    }
    

    public function exam_registration(Request $request){
        $student = Auth::user();
        $accDetails = $student->AcademicDetail()->first(); 

        $studyYear = $accDetails->current_study_year;

        $codes = [];
        $date = Carbon::now()->format('Y-m-d');

        if(settings('enable_exam_reg')==1){
            for($i=1; $i <= ($studyYear*2); $i++){
                Array_push($codes,'SM'.$i.'ExReg');
            }
    
            $semesters = CourseSchedule::whereIn('code',$codes)->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_enabled','=','1')->pluck('raw_code')->toArray();    
        }else $semesters = [];

        if(settings('exam_app_download')==1){
            for($i=1; $i <= ($studyYear*2); $i++){
                Array_push($codes,'SM'.$i.'ExAppDL');
            }
            $appSemesters = CourseSchedule::whereIn('code',$codes)->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_enabled','=','1')->pluck('raw_code')->toArray();

            $applications = $appliedStudents = StudentExam::join('student_exam_subjects','student_exam.id','=','student_exam_subjects.student_exam_id')
                                    ->where('student_exam.year','=',$this->year)
                                    ->whereIn('student_exam.semester',$appSemesters)
                                    ->where('student_exam.student_id','=',$student->id)
                                    ->where('student_exam_subjects.registered','=','1')
                                    ->select('student_exam.id','student_exam.semester')
                                    ->distinct()
                                    ->get()->toArray();
        }else $applications =[];
        
        return view('student.exam-registration',['student'=>$student,'semesters'=>$semesters,'applications'=>$applications]);

    }


    public function exam_registration_view(Request $request)
    {
        if(settings('enable_exam_reg')!=1) abort(403, 'Unauthorized action.');

        $semester  = $request->semester;
        $student = Auth::user();
        $accDetails = $student->AcademicDetail()->first(); 
        
        $studentID = $student->id;
        $studyYear = $accDetails->current_study_year;
        $accYear =  $this->year;

        $today = Carbon::now()->format('Y-m-d');

        $isValid = CourseSchedule::where('code','like','SM'.$semester.'ExReg')
                    ->where('start_date','<=',$today)
                    ->where('end_date','>=',$today)
                    ->where('is_enabled','=','1')
                    ->pluck('raw_code')->toArray();

        if($semester > $studyYear *2 || empty($isValid)) return redirect(url('/student/exam-registration/'));

        $studentExam = StudentExam::where('year', '=', $accYear)->where('semester', '=', $semester)->where('student_id', '=', $student->id)->select('id')->first();
        
        
        if(empty($studentExam)){//check whether the exam is already registered
            
            $reqSemReg = ($semester>=settings('sem_reg_min_semester'))?true:false; //check if we need to check the semester registered subject table (for optional/audit

            $completedSubjects = StudentExamResult::where('student_id','=',$studentID)->where('status','=','1')->orderBy('course_subject_id')->pluck('course_subject_id')->toArray();
            
            $compulsory = $optional = $audit = [];

            if(!$reqSemReg){
                $compulsory = CourseSubject::whereNotIn('id',$completedSubjects)
                                            ->where('regulation_id','=',$accDetails->regulation_id)
                                            ->where('semester','=',$semester)
                                            ->where('status','=',1)
                                            ->where('type','=','C')
                                            ->orderBy('course_subjects.code')
                                            ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester',DB::raw('1 as registered'))
                                            ->get()->toArray();
                DB::transaction(function () use(&$accYear, &$semester, &$student, &$compulsory, &$studentExamId){
                    $studentExamId =StudentExam::create(['year' => $accYear,'semester' => $semester,'student_id' => $student->id,'status' => '0'])->id;
                    foreach($compulsory as $subject){
                        StudentExamSubject::create(array('student_exam_id' =>$studentExamId ,'subject_id'=>$subject['id'],'exam_type'=>'1','is_repeat'=>'0','status'=>'0'));
                    }
                });

            }else{

                $compulsory = CourseSubject::join('student_semester_registration_subjects','course_subjects.id','=','student_semester_registration_subjects.subject_id')
                                    ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                    ->whereNotIn('course_subjects.id',$completedSubjects)
                                    ->where('course_subjects.semester','=',$semester)
                                    ->where('course_subjects.status','!=',0)
                                    ->where('student_semester_registration_subjects.student_id','=',$student->id)
                                    ->where('student_semester_registration_subjects.type','=','1')  
                                    ->orderBy('course_subjects.code')                            
                                    ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester',DB::raw('1 as registered'))
                                    ->get()->toArray();

                $optional = CourseSubject::join('student_semester_registration_subjects','course_subjects.id','=','student_semester_registration_subjects.subject_id')
                                    ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                    ->whereNotIn('course_subjects.id',$completedSubjects)
                                    ->where('course_subjects.semester','=',$semester)
                                    ->where('course_subjects.status','!=',0)
                                    ->where('student_semester_registration_subjects.student_id','=',$student->id)
                                    ->where('student_semester_registration_subjects.type','=','2')    
                                    ->orderBy('course_subjects.code')                          
                                    ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester',DB::raw('0 as registered'))
                                    ->get()->toArray();


                $audit = CourseSubject::join('student_semester_registration_subjects','course_subjects.id','=','student_semester_registration_subjects.subject_id')
                                    ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                    ->whereNotIn('course_subjects.id',$completedSubjects)
                                    ->where('course_subjects.semester','=',$semester)
                                    ->where('course_subjects.status','!=',0)
                                    ->where('student_semester_registration_subjects.student_id','=',$student->id)
                                    ->where('student_semester_registration_subjects.type','=','0')    
                                    ->orderBy('course_subjects.code')
                                    ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester',DB::raw('0 as registered'))
                                    ->get()->toArray();
                $studentExamId = 0;
                DB::transaction(function () use(&$accYear, &$semester, &$student, &$compulsory, &$optional, &$audit,&$studentExamId) { 
                    $studentExamId =StudentExam::create(['year' => $accYear,'semester' => $semester,'student_id' => $student->id,'status' => '0'])->id;
                    foreach($compulsory as $subject){
                        StudentExamSubject::create(array('student_exam_id' =>$studentExamId ,'subject_id'=>$subject['id'],'exam_type'=>'1','is_repeat'=>'0','status'=>'0'));
                    }

                    foreach($optional as $subject){
                        StudentExamSubject::create(array('student_exam_id' =>$studentExamId ,'subject_id'=>$subject['id'],'exam_type'=>'2','is_repeat'=>'0','status'=>'0'));
                    }

                    foreach($audit as $subject){
                        StudentExamSubject::create(array('student_exam_id' =>$studentExamId ,'subject_id'=>$subject['id'],'exam_type'=>'0','is_repeat'=>'0','status'=>'0'));
                    }
                });                
            }
        
            $takenSubjects = StudentExamResult::where('student_id','=',$studentID)->where('result','<>','MCA')->orderBy('course_subject_id')->pluck('course_subject_id')->toArray();

            StudentExamSubject::where('student_exam_id','=',$studentExamId)->whereIn('subject_id',$takenSubjects)->update(['is_repeat'=>'1','status'=>'1']); //update and approve the repeat subjects

            return view('student.exam-registration-view',['semester'=>$semester,'compulsory'=>$compulsory,'optional'=>$optional,'audit'=>$audit,'studentExamID'=>$studentExamId]);
        
        }else{
            
            $studentExamId = $studentExam->id;

            $compulsory = CourseSubject::join('student_exam_subjects','course_subjects.id','=','student_exam_subjects.subject_id')
                                ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                ->where('student_exam_subjects.student_exam_id','=',$studentExamId)
                                ->where('student_exam_subjects.exam_type','=','1')
                                ->where('course_subjects.semester','=',$semester)
                                ->where('course_subjects.status','!=',0)
                                ->orderBy('course_subjects.code')
                                ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester','student_exam_subjects.registered')
                                ->get()->toArray();

            $optional = CourseSubject::join('student_exam_subjects','course_subjects.id','=','student_exam_subjects.subject_id')
                                ->where('student_exam_subjects.student_exam_id','=',$studentExamId)
                                ->where('student_exam_subjects.exam_type','=','2')
                                ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                ->where('course_subjects.semester','=',$semester)
                                ->where('course_subjects.status','!=',0)
                                ->orderBy('course_subjects.code')
                                ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester','student_exam_subjects.registered')
                                ->get()->toArray();
            
            $audit = CourseSubject::join('student_exam_subjects','course_subjects.id','=','student_exam_subjects.subject_id')
                                ->where('student_exam_subjects.student_exam_id','=',$studentExamId)
                                ->where('student_exam_subjects.exam_type','=','0')
                                ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                ->where('course_subjects.semester','=',$semester)
                                ->where('course_subjects.status','!=',0)
                                ->orderBy('course_subjects.code')
                                ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester','student_exam_subjects.registered')
                                ->get()->toArray();

            return view('student.exam-registration-view',['semester'=>$semester,'compulsory'=>$compulsory,'optional'=>$optional,'audit'=>$audit,'studentExamID'=>$studentExamId]);
        }
    }

    public function save_register_exam(Request $request){

        if(settings('enable_exam_reg')!=1) abort(403, 'Unauthorized action.');

        $student = Auth::user();
        $accYear =  $this->year;

        $accDetails = $student->AcademicDetail()->first(); 
        $studyYear = $accDetails->current_study_year;

        $semester = $request->semester;
        $studentExamId = $request->studentExamID;

        $today = Carbon::now()->format('Y-m-d');

        $isValid = CourseSchedule::where('code','like','SM'.$semester.'ExReg')
                    ->where('start_date','<=',$today)
                    ->where('end_date','>=',$today)
                    ->where('is_enabled','=','1')
                    ->pluck('raw_code')->toArray();

        if($semester > $studyYear *2 || empty($isValid)) return redirect(url('/student/exam-registration/'));

        $studentExam = StudentExam::where('year', '=', $accYear)->where('semester', '=', $semester)->where('student_id', '=', $student->id)->select('id')->first();

        if($studentExam->id != $studentExamId)  return redirect(url('/student/exam-registration/'));

        StudentExamSubject::where('student_exam_id','=',$studentExamId)->update(['registered'=>'0']);

        $compulsory = $request->compulsory;
        if(!empty($compulsory)){
            StudentExamSubject::where('student_exam_id','=',$studentExam->id )->whereIn('subject_id',$compulsory)->update(['registered' => 1]); 
        }

        $elective = $request->elective;
        if(!empty($elective)){
            StudentExamSubject::where('student_exam_id','=',$studentExam->id )->whereIn('subject_id',$elective)->update(['registered' => 1]); 
        }

        $audit = $request->audit;
        if(!empty($audit)){
            StudentExamSubject::where('student_exam_id','=',$studentExam->id )->whereIn('subject_id',$audit)->update(['registered' => 1]); 
        }

        return redirect(url('/student/exam-registration/'));
    }


    public function view_approved_exam_subjects(Request $request){
        if(settings('exam_app_download')!=1) abort(403, 'Unauthorized action.');
        $student = Auth::user();
        $application = StudentExam::where('year', '=', $this->year)->where('semester', '=', $request->semester)->where('student_id', '=', $student->id)->first();

        if(empty($application))abort(404, 'Application Not Found.');

        $specialization = $student->AcademicDetail->specialization_id;
        $tempSub = StudentExamSubject::join('course_subjects','student_exam_subjects.subject_id','=','course_subjects.id')
                            ->leftJoin('course_subject_specialization','course_subjects.id','=','course_subject_specialization.subject_id')
                            ->where('student_exam_id','=',$application->id)
                            ->where(function($query) use($specialization) {
                                $query->whereNull('course_subject_specialization.specialization_id')->orWhere('course_subject_specialization.specialization_id','=',$specialization);
                            })
                            ->where('student_exam_subjects.status','!=','0')
                            ->where('student_exam_subjects.registered','=','1')
                            ->select('course_subjects.name','course_subjects.code','student_exam_subjects.*')
                            ->orderBy('student_exam_subjects.exam_type')->orderBy('course_subjects.code')
                            ->get();
        $subjects = [0=>[],1=>[],2=>[]];
        if($tempSub){
            foreach($tempSub as $subject){
                $subjects[$subject->exam_type][$subject->id] = $subject;
            }
        }

        
        return view('student.exam-view-approved-subjects',['student'=>$student,'application'=>$application,'subjects'=>$subjects] );
    }



    public function view_results(){
        if(settings('std_show_results')!=1) abort(403, 'Unauthorized action.');
        
        $results = [1=>[],2=>[],3=>[],4=>[],5=>[],6=>[],7=>[],8=>[]];
        $student = Auth::user();
        $resultsTemp = StudentExamResult::join('course_subjects','student_exam_results.course_subject_id','course_subjects.id')
                        ->where('student_id','=',$student->id)
                        ->select('course_subjects.semester','student_exam_results.year','student_exam_results.marks','student_exam_results.result','course_subjects.code','course_subjects.name')
                        ->orderBy('course_subjects.semester')
                        ->orderBy('course_subjects.code')
                        ->orderBy('student_exam_results.year')
                        ->get()
                        ->toArray();
        if($resultsTemp){
            foreach($resultsTemp as $record){
                array_push($results[$record['semester']],$record); 
            }
        }
        for($i=1;$i<9;$i++){
            if(empty($results[$i]))unset($results[$i]);
        }

        return view('student.view-results',['student'=>$student,'results'=>$results]);

    }

    // public function download_application(Request $request){
    //     $student = Auth::user();
    //     $application = StudentExam::where('year', '=', $this->year)->where('semester', '=', $request->semester)->where('student_id', '=', $student->id)->first();
        
    //     if(empty($application))abort(404, 'Application Not Found.');
    //     $application->acc_year = round($application->semester/2);

    //     $subjects = StudentExamSubject::join('course_subjects','student_exam_subjects.subject_id','=','course_subjects.id')
    //                 ->where('student_exam_subjects.student_exam_id','=',$application->id)
    //                 ->where('student_exam_subjects.status','=','1')
    //                 ->select('course_subjects.code','course_subjects.name')
    //                 ->get();
        

    //     $pdf = PDF::loadView('student.pdf.application',['student'=>$student,'application'=>$application,'subjects'=>$subjects] );
        
    //     // return view('student.pdf.application',['student'=>$student,'application'=>$application,'subjects'=>$subjects] );
                
    //     return $pdf->download('application.pdf');

    // }
}
