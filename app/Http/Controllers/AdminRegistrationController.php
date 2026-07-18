<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Auth;
use DB;
use Excel;

use App\Student;
use App\StudentAccDetail;
use App\StudentContact;
use App\StudentAL;
use App\SpecializationRequest;
use App\Specialization;
use App\SemesterRegistrationSubject;

use App\CourseSubject;

// use App\Batch;
use App\Regulation;


use App\Imports\SpecializationImport;
use App\Imports\YearRegistrationImport;

use App\Exports\SpecializationExport;
use App\Exports\VLEAccountExport;



class AdminRegistrationController extends Controller
{
    public function __construct(){
        $this->middleware('auth:users');
        $this->registration_year = settings('reg_year');
    }

    public function view_year_registration(Request $request){
        if (!(Auth::user()->hasPermissionTo('registration:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        if(!isset($request->type) || $request->type == 'view'){
            return view('admin.registration.view');
        }
        else{
            $data = [];
            $col = [0=>'RegistrationNo',1=>'Name',2=>'Batch',3=>'IDNo',4=>'StudyYear',5=>'AcademicYear'];
            //DB::enableQueryLog();
            $a = Student::join('student_yearly_registration','student_personal_details.id','=','student_yearly_registration.student_id')
                    ->join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id') 
                    ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                    ->where('student_yearly_registration.registered_year','=',$request->studyyear)
                    ->where('student_yearly_registration.academic_year','=',$request->accyear)                    
                    ->select(
                        'student_personal_details.id AS ID',
                        'student_personal_details.registration_no AS RegistrationNo',
                        'student_personal_details.id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        'master_batch.code AS Batch',
                        'student_yearly_registration.registered_year as StudyYear',
                        'student_yearly_registration.academic_year as AcademicYear',
                    );

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

    public function view_upload_year_registration(Request $request){
        if (!(Auth::user()->hasPermissionTo('registration:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        DB::table('temp_year_registration_upload')->truncate();
        return view('admin.registration.import');
    }

    public function upload_year_registration(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('registration:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['list' => 'required|file']);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            DB::table('temp_year_registration_upload')->truncate();
            $path = storage_path() . '/data/YearRegistrations/Upload/';
            $file = $request->file('list');        
            $file_name = str_replace(' ', '-', strtolower($file->getClientOriginalName()));
            
            //uplaod the record
            if($file->move($path, $file_name)){
                Excel::import(new YearRegistrationImport(), $path.$file_name);
            }
            $today = Carbon::now()->format('Y-m-d');
            
            $sql = 'UPDATE temp_year_registration_upload X INNER JOIN student_personal_details Y ON X.registration_no = Y.registration_no SET X.student_id = Y.id';
            DB::update($sql);

            $sql = 'delete y FROM temp_year_registration_upload x inner join temp_year_registration_upload y on x.student_id=y.student_id and x.year =y.year and x.study_year=y.study_year WHERE x.id < y.id ';
            DB::delete($sql);

            if($request->update==1){
                $sql = 'UPDATE temp_year_registration_upload x INNER JOIN student_yearly_registration y ON x.student_id = y.student_id AND x.year = y.academic_year SET y.registered_year = x.study_year, y.total_paid_amount = x.paid_amount, y.need_hostel = x.hostel';
                DB::update($sql);
            }

            $sql = 'DELETE x FROM temp_year_registration_upload x INNER JOIN student_yearly_registration y ON x.student_id = y.student_id AND x.year = y.academic_year AND x.study_year = y.registered_year';
            DB::delete($sql);      

            
            $sql = 'INSERT INTO student_yearly_registration(student_id, academic_year, registered_year, registration_date, total_paid_amount, need_hostel, status) SELECT x.student_id, x.year, x.study_year, "'.$today.'" as registration_date, paid_amount, hostel, 1 as "status" FROM temp_year_registration_upload x where x.student_id > 0';
            DB::insert($sql);

            $sql = 'UPDATE student_academic_details x INNER JOIN ( SELECT a.student_id, a.academic_year, a.registered_year FROM student_yearly_registration a INNER JOIN (SELECT student_id, MAX(academic_year) as academic_year FROM student_yearly_registration WHERE student_id > 0  GROUP BY student_id ) b ON a.student_id = b.student_id AND a.academic_year= b.academic_year) y ON x.student_id = y.student_id SET x.current_reg_year=y.academic_year, x.current_study_year=y.registered_year WHERE x.current_reg_year <= y.academic_year';
            DB::update($sql);

            return 1;
        }    
    }

    public function view_process_specialization(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('specialization:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){
            $groupSize = round(settings('batch_size')*.25);
            return view('admin.registration.specialization',['groupSize'=>$groupSize]); 

        }elseif($request->type == 'json'){

            $year = isset($request->year)?$request->year:settings('year');
            $col = [2=>'RegistrationNo',3=>'GPA', 4=>'Name'];
            
            // $gpaSemester = \intval(settings('sp_select_semster'))-1;

            $SpRequests = SpecializationRequest::where('year','=',$year)->groupBy('student_id')->pluck('student_id')->toArray();


            $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    // ->join('student_al_results','student_personal_details.id','=','student_al_results.student_id')
                    ->whereIn('student_personal_details.id',$SpRequests)
                    ->select(
                        'student_personal_details.id AS ID',
                        'student_personal_details.registration_no AS RegistrationNo',
                            DB::raw('CONCAT(student_personal_details.initials," ",student_personal_details.name_marking) AS Name'),
                        'student_academic_details.y2_gpa AS GPA',
                        'student_academic_details.specialization_id AS Specialization'
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
            $a->offset($request->start)->limit($request->length);            
            
            $studentRows = $a->get();   
            $records = [];
            $finalStdList =[];
            foreach($studentRows as $row){
                $records[$row->ID] = ['ID'=>$row->ID,'RegistrationNo'=>$row->RegistrationNo,'Name'=>$row->Name,'GPA'=>$row->GPA,'Option1'=>'-','Option2'=>'-','Option3'=>'-','Option4'=>'-','Option5'=>'-','Option6'=>'-','Option7'=>'-','Option8'=>'-'] ;
                array_push($finalStdList,$row->ID);
            }

            $SpRequests = SpecializationRequest::join('master_course_specialization_categories','student_spcialization_requests.specialization_id','=','master_course_specialization_categories.id')
                                ->where('year','=',$year)
                                ->whereIn('student_id',$finalStdList)
                                ->orderBy('student_id')
                                ->orderBy('preference_order')
                                ->select('student_spcialization_requests.student_id as ID','student_spcialization_requests.preference_order as PO','master_course_specialization_categories.name as Specilization')
                                ->get();

            foreach($SpRequests as $row){
                $records[$row->ID]['Option'.$row->PO] = $row->Specilization;
            }           
            
            $data['data']=array_values($records);
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function download_specialization(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('specialization:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $year = isset($request->year)?$request->year:settings('year');
        // $gpaSemester = \intval(settings('sp_select_semster'))-1;

        $SpRSid = SpecializationRequest::where('year','=',$year)->groupBy('student_id')->pluck('student_id')->toArray();

        $spName = Arr::pluck(Specialization::select('id','name')->get()->toArray(), 'name', 'id');
        $spName[0]="-";

        $studentRows = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                ->whereIn('student_personal_details.id',$SpRSid)
                ->select(
                    'student_personal_details.id AS ID',
                    'student_personal_details.registration_no AS RegistrationNo',
                    DB::raw('CONCAT(student_personal_details.initials," ",student_personal_details.name_marking) AS Name'),
                    'student_academic_details.y2_gpa AS GPA',
                    'student_academic_details.specialization_id AS Specialization'
                )->get();
        
        $records =[];
        foreach($studentRows as $row){
            $records[$row->ID] = ['ID'=>$row->ID,'RegistrationNo'=>$row->RegistrationNo,'Name'=>$row->Name,'GPA'=>$row->GPA,'Option1'=>'-','Option2'=>'-','Option3'=>'-','Option4'=>'-','Option5'=>'-','Option6'=>'-','Option7'=>'-','Option8'=>'-','ProposedOption'=>'-','CurrentOption'=>$spName[$row->Specialization]] ;
        }

        $SpRequests = SpecializationRequest::join('master_course_specialization_categories','student_spcialization_requests.specialization_id','=','master_course_specialization_categories.id')
                                ->where('year','=',$year)
                                ->whereIn('student_id',$SpRSid)
                                ->orderBy('student_id')
                                ->orderBy('preference_order')
                                ->select('student_spcialization_requests.student_id as ID',
                                        'student_spcialization_requests.preference_order as PO',
                                        'student_spcialization_requests.specialization_id as SID',
                                        'master_course_specialization_categories.name as Specilization')
                                ->get();

        $spcializations = [];
        foreach($SpRequests as $row){
            $records[$row->ID]['Option'.$row->PO] = $row->Specilization;
            $spcializations[$row->ID][$row->PO] = $row->SID;
        }  

        $students = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                                ->orderBy('student_academic_details.y2_gpa','desc')
                                ->whereIn('student_id',$SpRSid)
                                ->pluck('student_personal_details.id')
                                ->toArray();

        $spCounter = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0];
       

        $groupSize = (int)$request->GroupSize;
        if($groupSize < 1) $groupSize = round(settings('batch_size')*.25);

        foreach($students as $std){
            for($i=1; $i<9; $i++){
                if(isset($spcializations[$std][$i]) && $spCounter[$spcializations[$std][$i]] < $groupSize) {
                    $records[$std]['ProposedOption'] = $spName[$spcializations[$std][$i]];
                    $spCounter[$spcializations[$std][$i]]++;
                    break;
                }
            }
        }

        return Excel::download(new SpecializationExport($records), 'specialization_requests_'.$year.'.xlsx');

    }

    public function upload_specialization_selection(Request $request){
        if (!(Auth::user()->hasPermissionTo('specialization:process') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['list' => 'required|file']);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            // $spName = Arr::pluck(Specialization::select('id','name')->get()->toArray(), 'id', 'name');

            DB::table('temp_specialization_upload')->truncate();
            $path = storage_path() . '/data/Specilization/Upload/';
            $file = $request->file('list');        
            $file_name = str_replace(' ', '-', strtolower($file->getClientOriginalName()));
            
            //uplaod the record
            if($file->move($path, $file_name)){
                Excel::import(new SpecializationImport(), $path.$file_name);
            };

            $sql = 'UPDATE temp_specialization_upload x INNER JOIN student_personal_details y ON x.registration_no= y.registration_no SET x.student_id = y.id';
            DB::update($sql);

            $sql = 'UPDATE temp_specialization_upload x INNER JOIN master_course_specialization_categories y ON x.specialization= y.name SET x.specialization_id = y.id';
            DB::update($sql);

            $sql = 'UPDATE temp_specialization_upload x INNER JOIN student_academic_details y ON x.student_id= y.student_id SET y.specialization_id = x.specialization_id';
            DB::update($sql);

            DB::table('temp_specialization_upload')->truncate();

            return 1;
        }
    }



    public function view_lms_export(Request $request){
        if (!(Auth::user()->hasPermissionTo('registration:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view'){

            $regulations = Arr::pluck(Regulation::orderBy('id','desc')->select('name', 'id')->get()->toArray(), 'name', 'id');

            return view('admin.registration.export-lms',['regulations'=>$regulations]);
        }else{

            $academic_year = settings('year');

            $col = [2=>'RegistrationNo',3=>'Name',5=>'IDNo'];
            $data = ['recordsTotal'=>0,'recordsFiltered'=>0,'draw'=>$request->draw,'data'=>[]];

            $regulation = Regulation::where('id','=',$request->regulation)->select('id')->first();
            if(empty($regulation)) return response()->json($data);
            
            $year = filter_var($request->year,FILTER_SANITIZE_NUMBER_INT);

            //DB::enableQueryLog();
            $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    ->join('student_yearly_registration','student_personal_details.id','=','student_yearly_registration.student_id')
                    ->where('student_academic_details.current_study_year','=',$year)
                    ->where('student_academic_details.regulation_id','=',$regulation->id)
                    ->where('student_yearly_registration.registered_year','=',$year)
                    ->where('student_yearly_registration.academic_year','=',$academic_year)
                    ->select(
                        'student_personal_details.id AS ID',
                        'registration_no AS RegistrationNo',
                        'id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name')
                    );


            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;

            $applications  = $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir'])->get();
            
            $data['data']=$applications;
            return response()->json($data);
        }
    }

    public function download_vle_export_file(Request $request){
        if (!(Auth::user()->hasPermissionTo('registration:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $year = filter_var($request->year,FILTER_SANITIZE_NUMBER_INT);
        $regulation = filter_var($request->regulation,FILTER_SANITIZE_NUMBER_INT);

        if(empty($year) || empty($regulation)) return;

        $semesters = [0=>$year*2-1,1=>$year*2];


        $semesterReqRegistration = settings('sem_reg_min_semester');

        $reqSemReg = ($semesters[1]>=$semesterReqRegistration)?true:false; //check if we need to check the semester registered subject table 
        $sids = explode(',',$request->studentIds);

        //get the students array
        $students = Student::join('student_contact_details','student_personal_details.id','=','student_contact_details.student_id')
                ->whereIn('student_personal_details.id',$sids)
                ->select('student_personal_details.id','student_personal_details.initials','student_personal_details.name_marking','student_personal_details.index_no','student_personal_details.registration_no','student_personal_details.id_no','student_contact_details.email')
                ->get();


        $data = [];
        $subjects = [];
        $maxSubCount = 0;

        if($students){
            if(!$reqSemReg){

                $subTemp = CourseSubject::where('regulation_id','=',$regulation)->whereIn('semester',$semesters)->where('status','=',1)->select('id','code')->get();
                
                foreach($subTemp as $s){
                    $subjects[] = $s->code;
                    $maxSubCount++;
                }

                foreach($students as $std){
                    $data[$std->id] = ['firstname'=>$std->initials,'lastname'=>$std->name_marking,'email'=>$std->email,'idnumber'=>$std->index_no,'username'=>$std->registration_no,'password'=>$std->id_no,'subjects'=>$subjects];
                }

            }else{
             
                
                $maxSubCount = CourseSubject::where('regulation_id','=',$regulation)->whereIn('semester',$semesters)->where('status','=',1)->count('id');

                if($semesters[0]<$semesterReqRegistration){
                    $subTemp = CourseSubject::where('regulation_id','=',$regulation)
                                ->where('semester','=',$semesters[0])
                                ->where('status','=',1)
                                ->select('id','code')
                                ->get();
                    
                    foreach($subTemp as $s){
                        $subjects[] = $s->code;
                    }
                }

                $studentIds  = [];
                foreach($students as $std){
                    $data[$std->id] = ['firstname'=>$std->initials,'lastname'=>$std->name_marking,'email'=>$std->email,'idnumber'=>$std->index_no,'username'=>$std->registration_no,'password'=>$std->id_no,'subjects'=>$subjects];
                    $studentIds[] = $std->id;
                }
                

                $stdSubSelection = CourseSubject::
                                    join('student_semester_registration_subjects','course_subjects.id','=','student_semester_registration_subjects.subject_id')
                                    ->where('course_subjects.regulation_id','=',$regulation)
                                    ->whereIn('course_subjects.semester',$semesters)
                                    ->where('course_subjects.status','=',1)
                                    ->whereIn('student_semester_registration_subjects.student_id',$studentIds)
                                    ->select('student_id','course_subjects.code')
                                    ->get();

                foreach($stdSubSelection as $sub){
                    $data[$sub->student_id]['subjects'][] = $sub->code;
                }
            }
        }
        
        return Excel::download(new VLEAccountExport($data,$maxSubCount), 'vle_accounts_'.$year.'.xlsx');
        
    }



}
