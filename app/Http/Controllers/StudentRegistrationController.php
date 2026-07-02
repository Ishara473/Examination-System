<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// use Mail;
use Form;
use Validator;
use Redirect;
use Session;
use DB;
use Auth;
// use PDF;
// use DNS1D; 
// use URL;
// use Config;

use \Carbon\Carbon;


use App\Student;
use App\StudentAccDetail;
use App\StudentContact;
use App\StudentAL;
use App\SemesterRegistrationSubject;

use App\SpecializationRequest;

use App\CourseSchedule;
use App\CourseSubject;



class StudentRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        $user = Auth::user();
    }

    public function annual_registration(Request $request)
    {
        $student = Auth::user();
        return view('student.annual-registration',['student'=>$student]);
    }

    public function semester_registration(Request $request)
    {
        $student = Auth::user();
        $accDetails = $student->AcademicDetail()->first(); 

        $studyYear =$accDetails->current_study_year;
        $stdMaxSem = $studyYear*2;

        if(settings('enable_semester_reg')==1 && settings('sp_select_semster') <= $stdMaxSem){

            $codes = [0=>'SM'.$stdMaxSem.'Reg'];
            if($stdMaxSem-1 >= settings('sp_select_semster') ) $codes[1] = 'SM'.($stdMaxSem-1).'Reg';
            $date = Carbon::now()->format('Y-m-d');
            $availSemesters = CourseSchedule::whereIn('code',$codes)->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_enabled','=','1')->select('raw_code')->first();    
            
            $compulsory = $optional =[];
            if($availSemesters){
                $semester = $availSemesters->raw_code;

                $subQuery = "(SELECT student_id,subject_id,type FROM student_semester_registration_subjects WHERE student_id = '$student->id') AS y ";

                $cSubIDs = CourseSubject::where('regulation_id','=',$accDetails->regulation_id)
                                ->where('semester','=',$semester)
                                ->where('status','=',1)
                                ->where('type','=','C')
                                ->pluck('course_subjects.id')->toArray();
    
                $cSpSubIDs =  CourseSubject::join('course_subject_specialization','course_subjects.id','=','course_subject_specialization.subject_id')
                                            ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                            ->where('course_subjects.semester','=',$semester)
                                            ->where('course_subjects.status','=',1)
                                            ->where('course_subjects.type','=','S')
                                            ->where('course_subject_specialization.type','=','C')
                                            ->where('course_subject_specialization.specialization_id','=',$accDetails->specialization_id)
                                            ->pluck('course_subjects.id')->toArray();                
                $cSubIDs = array_merge($cSubIDs,$cSpSubIDs);

                $compulsory = CourseSubject::leftJoin(DB::raw($subQuery),function($join){$join->on('course_subjects.id', '=','y.subject_id'); })
                                ->whereIn('course_subjects.id',$cSubIDs)
                                ->where(function($query) use($student){
                                    $query->where('y.student_id','=',$student->id)
                                    ->orWhereNull('y.student_id');
                                })                                
                                ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester',DB::raw('IFNULL(y.type,-1) as type'))
                                ->get();


                $eSubIDs = CourseSubject::where('regulation_id','=',$accDetails->regulation_id)
                                ->where('semester','=',$semester)
                                ->where('status','=',1)
                                ->where('type','=','E')
                                ->pluck('course_subjects.id')->toArray();
                
                $cSpSubIDs =  CourseSubject::join('course_subject_specialization','course_subjects.id','=','course_subject_specialization.subject_id')
                                ->where('course_subjects.regulation_id','=',$accDetails->regulation_id)
                                ->where('course_subjects.semester','=',$semester)
                                ->where('course_subjects.status','=',1)
                                ->where('course_subjects.type','=','S')
                                ->where('course_subject_specialization.type','=','E')
                                ->where('course_subject_specialization.specialization_id','=',$accDetails->specialization_id)
                                ->pluck('course_subjects.id')->toArray();

                $eSubIDs = array_merge($eSubIDs,$cSpSubIDs);
    
                $optional = CourseSubject::leftJoin(DB::raw($subQuery),function($join){$join->on('course_subjects.id', '=','y.subject_id'); })
                                            ->whereIn('course_subjects.id',$eSubIDs)
                                            ->where(function($query) use($student){
                                                $query->where('y.student_id','=',$student->id)
                                                ->orWhereNull('y.student_id');
                                            })                                
                                            ->select('course_subjects.id','course_subjects.code','course_subjects.name','course_subjects.semester',DB::raw('IFNULL(y.type,-1) as type'))
                                            ->get();

            }     
            return view('student.semester-registration',['student'=>$student,'complusorySub'=>$compulsory,'optionalSub'=>$optional]);
        }else return redirect(url('/home'));
    }


    public function save_semester_registration(Request $request){
        $student = Auth::user();
        $accDetails = $student->AcademicDetail()->first(); 

        $studyYear =$accDetails->current_study_year;
        $stdMaxSem = $studyYear*2;

        if(settings('enable_semester_reg')==1 && settings('sp_select_semster') <= $stdMaxSem){
            $codes = [0=>'SM'.$stdMaxSem.'Reg'];
            if($stdMaxSem-1 >= settings('sp_select_semster') ) $codes[1] = 'SM'.($stdMaxSem-1).'Reg';
            $date = Carbon::now()->format('Y-m-d');
            $availSemesters = CourseSchedule::whereIn('code',$codes)->where('start_date','<=',$date)->where('end_date','>=',$date)->where('is_enabled','=','1')->select('raw_code')->first();


            if($availSemesters){
                $semester = $availSemesters->raw_code;
                $availableSub = CourseSubject::where('semester','=',$semester)->where('status','=',1)->pluck('course_subjects.id')->toArray();
                $availableSubStr = implode(',',$availableSub);

                $cSubject = SemesterRegistrationSubject::where('student_id','=',$student->id)->whereIn('subject_id',$availableSub)->pluck('subject_id')->toArray();
                // $cSubjectStr = implode(',',$cSubject);

                $subjects = $request->autid;
                if(!empty($subjects)){
                    $sub  = array_intersect($subjects,$availableSub);
                    $subStr = implode(',',$sub);

                    if(!empty($sub)){
                        $sql = 'DELETE FROM student_semester_registration_subjects  WHERE student_id = "'.$student->id.'" and subject_id IN ('.$availableSubStr.') AND subject_id NOT IN ('.$subStr.') AND type = 0';
                        DB::delete($sql);

                        $sql = 'UPDATE student_semester_registration_subjects SET type = 0 WHERE student_id = "'.$student->id.'" and subject_id IN ('.$subStr.')';
                        DB::update($sql);

                        $sub = array_diff($sub,$cSubject);
                        $subStr = implode(',',$sub);

                        if(!empty($sub)){
                            $sql = 'INSERT INTO student_semester_registration_subjects(student_id, subject_id, type, status) SELECT "'.$student->id.'" as student_id, id, 0 as type, 1 as status  FROM  course_subjects WHERE id in ('.$subStr.')';
                            DB::insert($sql);
                        }
                    }
                }else{
                    $sql = 'DELETE FROM student_semester_registration_subjects  WHERE student_id = "'.$student->id.'" and subject_id > 0 AND subject_id IN ('.$availableSubStr.') AND  type = 0';
                    DB::delete($sql);
                }

                $subjects = $request->compulsory;
                if(!empty($subjects)){
                    $sub  = array_intersect($subjects,$availableSub);
                    $subStr = implode(',',$sub);

                    if(!empty($sub)){
                        $sql = 'DELETE FROM student_semester_registration_subjects  WHERE student_id = "'.$student->id.'" and subject_id IN ('.$availableSubStr.') AND subject_id NOT IN ('.$subStr.') AND type = 1';
                        DB::delete($sql);

                        $sql = 'UPDATE student_semester_registration_subjects SET  type = 1 WHERE student_id = "'.$student->id.'" and subject_id IN ('.$subStr.')';
                        DB::update($sql);

                        $sub = array_diff($sub,$cSubject);
                        $subStr = implode(',',$sub);
                        if(!empty($sub)){
                            $sql = 'INSERT INTO student_semester_registration_subjects(student_id, subject_id, type, status) SELECT "'.$student->id.'" as student_id, id, 1 as type, 1 as status  FROM  course_subjects WHERE id in ('.$subStr.')';
                            DB::insert($sql);
                        }
                    }
                }else{
                    $sql = 'DELETE FROM student_semester_registration_subjects  WHERE student_id = "'.$student->id.'" and subject_id > 0 AND  subject_id IN ('.$availableSubStr.') AND type = 1';
                    DB::delete($sql);
                }

                $cSubject = SemesterRegistrationSubject::where('student_id','=',$student->id)->whereIn('subject_id',$availableSub)->pluck('subject_id')->toArray();
                $subjects = $request->elective;
                if(!empty($subjects)){
                    $sub  = array_intersect($subjects,$availableSub);
                    $subStr = implode(',',$sub);

                    if(!empty($sub)){
                        $sql = 'DELETE FROM student_semester_registration_subjects  WHERE student_id = "'.$student->id.'" and subject_id IN ('.$availableSubStr.') AND subject_id NOT IN ('.$subStr.') AND type = 2';
                        DB::delete($sql);

                        $sql = 'UPDATE student_semester_registration_subjects SET  type = 2 WHERE student_id = "'.$student->id.'" and subject_id IN ('.$subStr.')';
                        DB::update($sql);

                        $sub = array_diff($sub,$cSubject);
                        $subStr = implode(',',$sub);
                        if(!empty($sub)){
                            $sql = 'INSERT INTO student_semester_registration_subjects(student_id, subject_id, type, status) SELECT "'.$student->id.'" as student_id, id, 2 as type, 1 as status  FROM  course_subjects WHERE id in ('.$subStr.')';
                            DB::insert($sql);
                        }
                    }
                }else{
                    $sql = 'DELETE FROM student_semester_registration_subjects  WHERE student_id = "'.$student->id.'" and subject_id > 0 AND subject_id IN ('.$availableSubStr.') AND  type = 2';
                    DB::delete($sql);
                }
            }
        }
        return redirect(url('/home'));
    }

    public function specialization_selection(Request $request)
    {
        $year = [1=>1,2=>1,3=>2,4=>2,5=>3,6=>3,7=>4,8=>4]; //semester to year mapping
        $sp_selection_year = $year[settings('sp_select_semster')];

        $student = Auth::user();
        $accDetails = $student->AcademicDetail()->first(); 

        if(settings('sp_selection_enable')==1 && $accDetails->current_study_year >= $sp_selection_year && $accDetails->specialization_id == 0 ){
            $tempSpecialization = DB::table('master_course_specialization_categories')->select('id','department','name')->get();
            $specializations = [];
            foreach($tempSpecialization as $row){
                $specializations[$row->id]=$row;
            }

            $std_specialization_requests = SpecializationRequest::where('student_id','=',$student->id)->orderBy('preference_order')->get();

            return view('student.request-specialization',['student'=>$student,'specializations'=>$specializations,'std_sp_requests'=> $std_specialization_requests]);
        }else return redirect(url('/home'));

    }

    public function save_specialization_selection(Request $request){
        $student = Auth::user();
        
        $appoved_specialization = SpecializationRequest::where('student_id','=',$student->id)->where('status','=','1')->get();
        $newSpecialization = $request->specialization;

        if(empty($appoved_specialization) && !empty($newSpecialization)){
            $specializations = $student->specializationRequests()->get();
           
            $newSpTempFlipped = array_flip($newSpecialization);
            $newSpTemp = [];
            $count = 1;
            for($i=1; $i<=8;$i++){
                if(isset($newSpTempFlipped[$i])){
                    $newSpTemp[$count] = $newSpTempFlipped[$i];
                    $count++;
                }
            }
            $newSpecialization = array_flip($newSpTemp);

            if(!empty($specializations)){
                foreach($specializations as $specialization){
                    if(isset($newSpecialization[$specialization->specialization_id])){
                        SpecializationRequest::where('id','=',$specialization->id)->update(['preference_order' => $newSpecialization[$specialization->specialization_id]]);
                        unset($newSpecialization[$specialization->specialization_id]);
                    }else{
                        $SpRequest = SpecializationRequest::find($specialization->id);
                        $SpRequest->delete();
                    }
                }
            }

            foreach($newSpecialization as $key=>$value){
                $SpRequest = new SpecializationRequest;
                $SpRequest->year = settings('year');
                $SpRequest->student_id = $student->id;
                $SpRequest->specialization_id = $key;
                $SpRequest->preference_order = $value;
                $SpRequest->status = 0;
                $SpRequest->save();
            }
            return 1;
        }
        return -1;

    }



}
