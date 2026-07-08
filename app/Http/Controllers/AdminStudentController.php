<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;

use Illuminate\Http\Request;
use Session;
use Auth;
use DB;
use File;
use Excel;
use Hash;
use Storage;

use Validator;
use Carbon\Carbon;

use App\Student;
use App\StudentAccDetail;
use App\StudentContact;
use App\StudentAL;
use App\StudentExamResult;
use App\StudentBatchMis;
use App\StudentAchievement;
use App\YearRegistration;
use App\StudentGaurdian;
use App\SpecializationRequest;


use App\Scholarship;
use App\Specialization;
use App\Batch;
use App\Regulation;
use App\ALSubject;

use App\TempStudentImport;
use App\Imports\StudentImport;

use App\TempTranferredList;
use App\Imports\TranferImport;

use App\TempGraduateList;
use App\Imports\GraduateImport;

use App\Imports\ScholarshipImport;


use App\Exports\StudentExport;


class AdminStudentController extends Controller{
    
    public function __construct(){
        $this->middleware('auth:users');
    }
        
    public function index(){
        if (!(Auth::user()->hasPermissionTo('student:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $batches =  Arr::pluck(Batch::orderBy('code','desc')->select('code', 'id')->get()->toArray(), 'code', 'id');
        $curBatch = Batch::where('is_current','=','1')->select('id')->first();

        return view('admin.students.index',['batches'=>$batches,'curBatch'=>$curBatch->id]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request){
        if(!isset($request->type) || $request->type == 'json'){
            $data = ['recordsTotal'=>0,'recordsFiltered'=>0,'draw'=>$request->draw,'data'=>[]];

            if(empty($request->batch) && empty($request->search)) return response()->json($data);
            
            // $batch = Batch::where('id','=',$request->batch)->select('code')->first();

            $col = [2=>'RegistrationNo',3=>'Name',4=>'Batch',5=>'IDNo'];
            //DB::enableQueryLog();
            $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                    ->join('master_batch','student_academic_details.batch','=','master_batch.id')
                    ->select(
                        'student_personal_details.id AS ID',
                        'registration_no AS RegistrationNo',
                        'id_no AS IDNo',
                        DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                        DB::raw("master_batch.code AS Batch")
                    );
                    
            if(!empty(trim($request->batch))){
                $a->where('student_academic_details.batch','=',$request->batch);
            }

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
            $applications = $a->get();                 
            
            
            $data['data']=$applications;
            $data['draw']=$request->draw;
            return response()->json($data);
        }elseif($request->type=='excel'){

            $batch = Batch::where('id','=',$request->batch)->select('code')->first();
            $data = [];

            $specializations = Arr::pluck(Specialization::select('id','name')->get()->toArray(), 'name', 'id');
            $specializations[0] ='';

            if(!empty($batch)){

                $a = Student::join('student_academic_details','student_personal_details.id','=','student_academic_details.student_id')
                            ->where('student_academic_details.batch','=',$request->batch)
                            ->select(
                                'registration_no AS RegistrationNo',
                                'index_no AS IndexNo',
                                'id_no AS IDNo',
                                DB::raw('CONCAT(initials," ",name_marking) AS Name'),
                                DB::raw("'$batch->code' AS Batch"),
                                'specialization_id as SpecializationId',
                                's1_gpa AS S1GPA', 
                                's2_gpa AS S2GPA',
                                's3_gpa AS S3GPA',
                                's4_gpa AS S4GPA', 
                                's5_gpa AS S5GPA',
                                's6_gpa AS S6GPA',
                                's7_gpa AS S7GPA', 
                                's8_gpa AS S8GPA',
                                'final_gpa AS FinalGPA',
                                'is_batch_miss AS BatchMiss',
                                'current_study_year AS StudyYear',
                            );


                if(!empty($request->search)){
                    $search = $request->search.'%';
                    $a->where(function($query) use($search){
                        return $query->where('registration_no','like',$search)->orWhere('id_no','like',$search);
                    });
                }
                $data = $a->get();
            }
            return Excel::download(new StudentExport($data,$specializations), 'student_list.xlsx');

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $Student = Student::create($request->all());
        Session::flash('success','Student    successfully created');
        return redirect()->route('students.show',$Student->id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $id)
    {
        if (!(Auth::user()->hasPermissionTo('student:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $student = Student::with('Contact','AcademicDetail')->find($id);
        
        $scholaships = Arr::pluck(Scholarship::all()->toArray(), 'name', 'id');
        $scholaships[0] ='';

        $specializations = Arr::pluck(Specialization::select('id','name')->get()->toArray(), 'name', 'id');
        $specializations[0] ='';

        $results = [1=>[],2=>[],3=>[],4=>[],5=>[],6=>[],7=>[],8=>[]];

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

        $payments = YearRegistration::where('student_id','=',$student->id)->orderBy('academic_year')
                        ->select('academic_year', 'registered_year', 'registration_date', 'need_hostel', 'total_paid_amount',)
                        ->get()
                        ->toArray();

        $hasDoc =  file_exists(storage_path().'/Student/Documents/'.$student->id.'.pdf');

        $stdBatch = Batch::where('id','=',$student->AcademicDetail->batch)->first();

        $stdRegulation = Regulation::where('id','=',$student->AcademicDetail->regulation_id)->first();
        
        $batches = Arr::pluck(Batch::where('academic_year','>',$stdBatch->academic_year)->select('id','code')->orderBy('academic_year')->limit(10)->get(),'code','id');

        $alSubjects = Arr::pluck(ALSubject::orderBy('subject')->select('id','subject')->get()->toArray(), 'subject', 'id');
        $alSubjects['-1'] ='';

        $achievments = StudentAchievement::where('student_id','=',$student->id)->get();
        return view('admin.students.view',['student'=>$student,'scholarships'=>$scholaships,'specializations'=>$specializations, 'results'=>$results,'payments'=>$payments,'hasDoc'=>$hasDoc,'batches'=> $batches, 'achievments'=>$achievments, 'regulation'=>$stdRegulation, 'alSubjects'=>$alSubjects]);
    }

    public function view_profile_images(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $student = Student::where('id','=',$request->id)->first();
        $type = (($request->type==1)?1:2);

        if($student){
            $file = storage_path() . '/Student/Image/Profile/'.$type.'/'.$student->id.'.jpg';
            if(!File::exists($file)) $file = public_path().'/images/'.(($request->type==1)?'user':'signature').'.jpg';
        }else{
            $file = public_path().'/images/'.(($request->type==1)?'user':'signature').'.jpg';
        }

        $headers = [
            'Content-Type' => 'image/jpg',
            'Content-Disposition' => sprintf('attachment; filename="%s"', time().'_image.jpg'),
        ];

        return response()->file($file, $headers);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $student = Student::find($request->id);
        if(empty($student)) abort(404, 'Student not found.');
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update_personal_details(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $student = Student::find($request->id);
        if($student){
            $student->full_name = $request->full_name;
            $student->full_name_sinhala = $request->full_name_sinhala;
            $student->full_name_tamil = $request->full_name_tamil;
            $student->name_marking = $request->name_marking;
            $student->initials = $request->initials;
            $student->id_no = $request->id_no;
            $student->id_no_2 = $request->id_no_2;
            $student->dob = $request->dob;
            $student->gender = $request->gender;
            $student->race = $request->race;
            $student->religion = $request->religion;
            $student->civil_status = $request->civil_status;
            $student->writing_hand = $request->writing_hand;
            $student->save();
            
            $conctact = $student->contact()->first();
            $conctact->address1 = $request->address1;
            $conctact->address2 = $request->address2;
            $conctact->address3 = $request->address3;
            $conctact->contact_address1 = $request->contact_address1;
            $conctact->contact_address2 = $request->contact_address2;
            $conctact->contact_address3 = $request->contact_address3;
            $conctact->district = $request->district;
            $conctact->gn_division = $request->gn_division;
            $conctact->electorate = $request->electorate;
            $conctact->moh_area = $request->moh_area;
            $conctact->mobile = $request->mobile;
            $conctact->phone = $request->phone;
            $conctact->email = $request->email;
            $conctact->save();

            $gaurdian = $student->gaurdian()->first();
            if(empty($gaurdian)) {
                $gaurdian = new StudentGaurdian();
                $gaurdian->student_id = $student->id;
            }
            $gaurdian->type = $request->gaurdian_type ;
            $gaurdian->full_name = $request->gaurdian_name ;
            $gaurdian->occupation = $request->gaurdian_occupation ;
            $gaurdian->address = $request->gaurdian_address ;
            $gaurdian->phone = $request->gaurdian_phone ;
            $gaurdian->emergency_c_name = $request->gaurdian_emergency_c_name ;
            $gaurdian->emergency_c_phone = $request->gaurdian_emergency_c_phone ;
            $gaurdian->save();
            
            Session::flash('success','Student\'s Personal Details Were Updated');         
        }else{
            Session::flash('success','Error occured while trying to update Student Personal Details');         
        }
        //return 1;
        return redirect()->route('admin.student.view',$student->id);

        //$Student->update($request->all());
    }


    public function update_al_details(Request $request){
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $student = Student::find($request->id);
        if(!empty($student)){
            $alDetails = $student->AL()->first();
            if(empty($alDetails)){
                $alDetails = new StudentAL();
                $alDetails->student_id = $request->id;
            }
            $alDetails->attempt = $request->attempt;
            $alDetails->subject1 = $request->subject1;
            $alDetails->subject2 = $request->subject2;
            $alDetails->subject3 = $request->subject3;
            $alDetails->subject4 = $request->subject4;
            $alDetails->subject5 = $request->subject5;
            $alDetails->subject6 = $request->subject6;
            $alDetails->result1 = $request->result1;
            $alDetails->result2 = $request->result2;
            $alDetails->result3 = $request->result3;
            $alDetails->result4 = $request->result4;
            $alDetails->result5 = $request->result5;
            $alDetails->result6 = $request->result6;
            $alDetails->save();
            Session::flash('success','A/L Details Were Updated');         
        }else{
            Session::flash('success','Error occured while trying to update A/L Details');         
        }
        return redirect()->route('admin.student.view',$student->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $student = Student::find($request->id);
        if(empty($student)) abort(404, 'Student not found.');
        $student->delete();
        Session::flash('success','Student successfully deleted');
        return redirect()->route('students.index');
    }


    public function import(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $batches =  Arr::pluck(Batch::where('is_current','=','1')->get()->toArray(), 'code', 'id');
        // $curBatch = Batch::where('is_current','=','1')->orderBy('id','desc')->select('id')->first();

        $regulations=  Arr::pluck(Regulation::all()->toArray(), 'name', 'id');
        $curRegulation = Regulation::where('is_current','=','1')->select('id')->first();

        DB::table('temp_student_upload_rusl_file')->truncate();
        return view('admin.students.import',['batches'=>$batches,'regulations'=>$regulations,'curRegulation'=>$curRegulation->id]);
    }

    
    public function upload(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['type' => 'required','student_list' => 'required|file', 'batch'=>'required']);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            DB::table('temp_student_upload_rusl_file')->truncate();

            if($request->type == 2){
                $path = storage_path() . '/data/registrations/Upload/RUSL/';
                $file = $request->file('student_list');        
                $file_name = str_replace(' ', '-', strtolower($file->getClientOriginalName()));
    
                if($file->move($path, $file_name)){
                    Excel::import(new StudentImport($request->batch,$request->regulation), $path.$file_name);
                    return 1;
                }
            }
        }            
        return response()->json(['errors'=>'Oops! something when wrong. Refresh the page and try to upload again.']);     
    }

    public function uploaded_list(Request $request){
        $records =[];
        $recordCount= 0;

        if($request->type == 2){
            $records = DB::table('temp_student_upload_rusl_file')->take(10)->get();
            $recordCount = DB::table('temp_student_upload_rusl_file')->count();
        }

        return response()->json(['records'=>$records, 'recordCount'=>$recordCount]);
    }

    public function process_import(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if($request->processType == 2){
            $records = TempStudentImport::get();
            if($records){
                $gender = ['Male'=>1,'M'=>1,'Female'=>2,'F'=>2];
                $titles = Arr::pluck(DB::table('master_applicant_title')->get()->toArray(),'id', 'name');

                // $uplaodedBatches = DB::table('temp_student_upload_rusl_file')->groupBy('batch')->select('batch')->get();
                // if(count($uplaodedBatches)!=1)return;
                // else $batch = Batch::where('id','=',$uplaodedBatches[0]->batch)->first();

                foreach($records as $row) {
                    DB::transaction( function() use( &$request, &$row, &$gender,&$titles, &$batch){
                        $batch = Batch::where('id','=',$row->batch)->first();

                        if(empty($batch)) return;

                        if(trim(strtolower($row->status)) !== 'enrolled') return;
                        // $record = [];
                        $regNo = $row->registration_no;
                        $student = Student::where('registration_no','=',$regNo)->first();
                        if(!empty($student)){
                            // $student->restore();
                            if($request->insert_type == 1)return;
                        }
            
                        if(empty($student)){
                            $student = new Student();
                            $student->registration_no = $regNo;
                            $student->password = Hash::make($row->nic);
                        }

                        $index = str_replace('/','',$student->registration_no);
                        $student->index_no = substr($index,0,2).'/'.substr($index,-5,5);
                        $student->year = $batch->academic_year;
                        $student->full_name = $row->full_name;
                        $student->name_marking = $row->name_marking;
                        $student->initials = $row->initials;
                        $student->gender = ($gender[$row->gender])?$gender[$row->gender]:1;
                        $student->title = ($titles[$row->title])?$titles[$row->title]:6;
                        $student->id_no = $row->nic;
                        $student->medium = $row->medium;
                        $student->save();
            
                        if($student){
            
                            if($student->AcademicDetail()->exists()){
                                $academicDetail = $student->AcademicDetail()->first();
                            }else{
                                $academicDetail = new StudentAccDetail();
                            }
                            
                            $academicDetail->student_id = $student->id;
                            $academicDetail->batch =  $batch->id;
                            $academicDetail->current_reg_year = $batch->academic_year;
                            $academicDetail->registration_date = Carbon::parse($row->registration_date)->format('Y-m-d');
                            $academicDetail->registration_status = $row->status;
                            $academicDetail->regulation_id = $row->regulation_id;
                            $academicDetail->status = 1;
                            $academicDetail->save();
            
                            if($student->Contact()->exists()){
                                $studentContact = $student->Contact()->first();
                            }else{
                                $studentContact = new StudentContact();
                            }
            
                            $studentContact->student_id = $student->id;
                            $studentContact->email =$row->email;
                            $studentContact->mobile = $row->mobile;
                            $studentContact->phone = (!empty($row->phone1))?$row->phone1:$row->phone2;
                            $studentContact->address1 = $row->address1;
                            $studentContact->address2 = $row->address2;
                            $studentContact->address3 = $row->address3;
                            $studentContact->district = $row->district;
                            $studentContact->save();
            
                            if($student->AL()->exists()){
                                $studentAL = $student->AL()->first();
                            }else{
                                $studentAL = new StudentAL();
                            }
            
                            $studentAL->student_id = $student->id;
                            $studentAL->year = 0;
                            $studentAL->index_no = $row->al_index_no;
                            $studentAL->zscore = $row->zscore;
                            $studentAL->save();        
                        }
                    });
                }
                DB::table('temp_student_upload_rusl_file')->truncate();
                // Session::flash('success','Records were successfully uploaded');
                return 1;
            }
        }
        return -1;
    }

    /* **********************************************************************************************/
    /* ************************************* Profile Pic ********************************************/
    /* **********************************************************************************************/

    public function view_upload_profile_pictures(Request $request){
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        return view('admin.students.import-profile-pic');
    }

    public function upload_profile_pictures(Request $request){
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|integer|in:1,2',
            'images' => 'required|file|mimes:jpg,jpeg,zip|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if($request->type==1){
            $path = storage_path() . '/Student/Image/Profile/1/';
        }else{
            $path = storage_path() . '/Student/Image/Profile/2/';
        }

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $file = $request->file('images');
        if(!empty($file)) {
            $extension = $file->extension();
            if($extension == 'jpg' || $extension == 'jpeg'){
                $image = $file->getClientOriginalName();
                $nic = \explode('.',$image);
                $nic = trim($nic[0]);

                $student = Student::where('id_no','=',$nic)->first();
                if(!empty($student)){
                    $file->move($path,$student->id.'.jpg');
                    return response()->json(['success' => true], 200);
                }
                return response()->json(['error' => 'No student found with NIC matching filename.'], 404);
            }elseif($extension == 'zip'){
                $zip = new \ZipArchive;
                $file_path = $file->getPathName();
                $res = $zip->open($file_path);
                if ($res === TRUE) {
                    $temppath = storage_path() . '/data/Student/Image/Temp/';
                    $zip->extractTo($temppath);
                    $zip->close();
                    chdir($temppath);
                    $images = glob("*.jpg");

                    foreach($images as $image){
                        $nic = trim(substr($image,0, -4));

                        $student = Student::where('id_no','=',$nic)->first();
                        if(!empty($student)){
                            rename( $temppath.$image, $path.$student->id.'.jpg');
                        }
                    }
                    
                    $images = glob("*.jpeg");

                    foreach($images as $image){
                        $nic = trim(substr($image,0, -5));

                        $student = Student::where('id_no','=',$nic)->first();
                        if(!empty($student)){
                            rename( $temppath.$image, $path.$student->id.'.jpg');
                        }
                    }

                    chdir(storage_path() . '/Student/Image/');
                    $files = glob( $temppath . '*', GLOB_MARK);
                    foreach( $files as $file ){
                        unlink($file);      
                    }
                    rmdir( $temppath );
                }
                return response()->json(['success' => true], 200);
            }
        }
        return response()->json(['error' => 'Invalid file type. Only JPG, JPEG, and ZIP are allowed.'], 422);       
    }


    public function update_student_scholarship(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $student = Student::find($request->id);
        $accDetails = $student->AcademicDetail()->first();
        $accDetails->main_scholarship = $request->main_scholarship;
        $accDetails->other_scholarship = $request->other_scholarship;
        $accDetails->scholarship_start_date = $request->scholarship_start_date;
        $accDetails->save();
        return redirect()->route('admin.student.view',$student->id);
    }

    /* **********************************************************************************************/
    /* ************************************* Transfer ***********************************************/
    /* **********************************************************************************************/

    public function transfer(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:delete') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        DB::table('temp_transferred_list')->truncate();
        return view('admin.students.transfer');
    }


    public function upload_transfer(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:delete') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['student_list' => 'required|file']);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }else{
            DB::table('temp_transferred_list')->truncate();

            $path = storage_path() . '/data/registrations/transfer/';
            $file = $request->file('student_list');        
            $file_name = time().'-'.str_replace(' ', '-', strtolower($file->getClientOriginalName()));

            if($file->move($path, $file_name)){
                Excel::import(new TranferImport(), $path.$file_name);
                return 1;
            }
        }            
        return response()->json(['errors'=>'Oops! something when wrong. Refresh the page and try to upload again.']); 
    }

    public function transfer_list(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:delete') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $records = TempTranferredList::get();
        $recordCount = TempTranferredList::count();
        return response()->json(['records'=>$records, 'recordCount'=>$recordCount]);
    }

    public function process_transfer(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:delete') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $records = TempTranferredList::select('registration_no')->orderBy('registration_no')->pluck('registration_no')->toArray();

        if (empty($records)) {
            return response()->json(['error' => 'No records found in the uploaded list. Please upload a valid file first.'], 422);
        }

        $existingStudents = Student::whereIn('registration_no', $records)->select('id', 'registration_no')->get();
        $studentIds = $existingStudents->pluck('id')->toArray();
        $existingRegs = $existingStudents->pluck('registration_no')->toArray();

        if (empty($studentIds)) {
            return response()->json(['error' => 'None of the registration numbers in the uploaded file matched any student in the system.'], 422);
        }

        // Calculate not found before deletion
        $notFound = array_diff($records, $existingRegs);

        Student::whereIn('id', $studentIds)->delete();
        StudentAccDetail::whereIn('student_id', $studentIds)->delete();
        StudentContact::whereIn('student_id', $studentIds)->delete();
        StudentAL::whereIn('student_id', $studentIds)->delete();
        StudentExamResult::whereIn('student_id', $studentIds)->delete();
        StudentGaurdian::whereIn('student_id', $studentIds)->delete();
        SpecializationRequest::whereIn('student_id', $studentIds)->delete();
        YearRegistration::whereIn('student_id', $studentIds)->delete();
        
        return response()->json([
            'success' => true,
            'removed' => count($studentIds),
            'notFound' => array_values($notFound),
        ]);
    }

    /* **********************************************************************************************/
    /* ******************************* Uni Application Upload ***************************************/
    /* **********************************************************************************************/


    public function view_upload_documents(Request $request){
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        return view('admin.students.import-documents');
    }

    public function upload_documents(Request $request){
        if (!(Auth::user()->hasPermissionTo('student:upload') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,zip|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first('file')], 422);
        }

        try {
            $path = storage_path() . '/Student/Documents/';

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file = $request->file('file');
            if (!empty($file)) {
                $extension = $file->extension();

                if ($extension == 'pdf') {
                    $name = $file->getClientOriginalName();
                    $regNo = substr($name, 0, 2) . '/' . substr($name, 2, 4) . '/' . substr($name, 6, 3);
                    $student = Student::where('registration_no', '=', $regNo)->first();

                    if (!empty($student)) {
                        $file->move($path, $student->id . '.pdf');
                        return response()->json(['success' => true], 200);
                    }
                    return response()->json(['error' => 'No student found with registration number extracted from filename.'], 404);

                } elseif ($extension == 'zip') {
                    $zip = new \ZipArchive;
                    $file_path = $file->getPathName();
                    $res = $zip->open($file_path);

                    if ($res === TRUE) {
                        $temppath = storage_path() . '/data/Student/Document/Temp/';

                        if (!file_exists($temppath)) {
                            mkdir($temppath, 0777, true);
                        }

                        $zip->extractTo($temppath);
                        $zip->close();

                        // Glob only the PDF files at the top level of the temp directory
                        $documents = glob($temppath . '*.pdf');
                        foreach ($documents as $docFile) {
                            $baseName = basename($docFile);
                            $baseName = trim($baseName);
                            $regNo = substr($baseName, 0, 2) . '/' . substr($baseName, 2, 4) . '/' . substr($baseName, 6, 3);
                            $student = Student::where('registration_no', '=', $regNo)->first();
                            if (!empty($student)) {
                                rename($docFile, $path . $student->id . '.pdf');
                            }
                        }

                        // Recursively delete everything in the temp directory
                        $this->deleteDirectory($temppath);
                    } else {
                        return response()->json(['error' => 'Could not open the ZIP file. It may be corrupted.'], 422);
                    }

                    return response()->json(['success' => true], 200);
                }
            }

            return response()->json(['error' => 'Invalid file type. Only PDF and ZIP are allowed.'], 422);

        } catch (\Exception $e) {
            \Log::error('upload_documents error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the file. Please try again.'], 500);
        }
    }

    /**
     * Recursively delete a directory and all its contents.
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $target = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($target)) {
                $this->deleteDirectory($target);
            } else {
                @unlink($target);
            }
        }
        @rmdir($dir);
    }


    public function download_document(Request $request, int $id){
        if (!(Auth::user()->hasPermissionTo('student:view') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $student = Student::where('id','=',$id)->first();
        if($student){
            $file = storage_path().'/Student/Documents/'.$student->id.'.pdf';
            if(file_exists($file)){
                $headers = [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', 'application_'.$student->id.'.pdf')
                ];
    
                return response()->file($file,$headers);
            }else abort(404, 'File not found.');
        }else abort(404, 'File not found.');

    }


    /* **********************************************************************************************/
    /* ************************************* Graduate ***********************************************/
    /* **********************************************************************************************/

    public function graduate(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        DB::table('temp_graduate_list')->truncate();
        return view('admin.students.graduate');
    }


    public function upload_graduate(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), ['student_list' => 'required|file|mimes:xlsx,xls,csv|max:51200']);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()->first('student_list')], 422);
        }

        DB::table('temp_graduate_list')->truncate();

        $path = storage_path() . '/data/registrations/graduate/';
        $file = $request->file('student_list');        
        $file_name = time().'-'.str_replace(' ', '-', strtolower($file->getClientOriginalName()));

        if($file->move($path, $file_name)){
            Excel::import(new GraduateImport(), $path.$file_name);
            return response()->json(['success' => true], 200);
        }

        return response()->json(['errors'=>'Oops! Something went wrong. Refresh the page and try again.'], 500); 
    }

    public function graduate_list(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $records = TempGraduateList::get();
        $recordCount = TempGraduateList::count();
        return response()->json(['records'=>$records, 'recordCount'=>$recordCount]);
    }

    public function process_graduate(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $records = TempGraduateList::select('registration_no','degree_effective_date')->orderBy('registration_no')->get();

        if ($records->isEmpty()) {
            return response()->json(['error' => 'No records found in the uploaded list. Please upload a valid file first.'], 422);
        }

        $graduated = 0;
        $notFound = [];

        foreach($records as $r){
            $student = Student::where('registration_no','=',$r->registration_no)->first();
            if($student){
                $student->status = 2;
                $student->save();
                $acc = $student->AcademicDetail()->first();
                if($acc){
                    $acc->status = 2;
                    $acc->is_complete = 1;
                    $acc->degree_effective_date = $r->degree_effective_date;
                    $acc->save();
                }
                $graduated++;
            } else {
                $notFound[] = $r->registration_no;
            }
        }

        if ($graduated === 0) {
            return response()->json(['error' => 'None of the registration numbers in the uploaded file matched any student in the system.'], 422);
        }

        $response = [
            'success' => true,
            'graduated' => $graduated,
        ];

        if (!empty($notFound)) {
            $response['skipped'] = $notFound;
        }

        return response()->json($response);
    }

    /* **********************************************************************************************/
    /* ************************************* Batch miss *********************************************/
    /* **********************************************************************************************/

    public function add_batch_mis(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $student = Student::where('id','=',$request->student_id)->first();
        $stdAcademicDetails = $student->AcademicDetail()->first();

        if(!empty($student) && $stdAcademicDetails->is_batch_miss==0){

            $stdCurBatch = Batch::where('id','=',$stdAcademicDetails->batch)->first();

            $bms = new StudentBatchMis();
            $bms->student_id = $student->id;
            $bms->reason = $request->newBatchReason;
            $bms->old_regulation = $student->AcademicDetail->regulation_id;
            $bms->old_batch = $stdCurBatch->id;
            $bms->save();

            $stdAcademicDetails->batch = $request->newBatch;
            $stdAcademicDetails->is_batch_miss = 1;
            $stdAcademicDetails->save();

            return 1;

        }
        return -1;        
    }


    /* **********************************************************************************************/
    /* ************************************* Achievments *********************************************/
    /* **********************************************************************************************/

    public function add_student_achievemnt(Request $request){
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $student = Student::where('id','=',$request->student_id)->first();
        if($student){
            $sa = new StudentAchievement();
            $sa->type = $request->Type;
            $sa->student_id = $student->id;
            $sa->comment = $request->Comment;
            $sa->save();
            return 1;
        }

        return -1;
    }
        



    /* **********************************************************************************************/
    /* ************************************* Scholarships *********************************************/
    /* **********************************************************************************************/

    public function view_upload_scholarships(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $scholarships =  Arr::pluck(Scholarship::get()->toArray(), 'name', 'id');

        DB::table('temp_student_upload_rusl_file')->truncate();
        return view('admin.students.scholarships',['scholarships'=>$scholarships]);


    }


    public function upload_scholarships(Request $request)
    {
        if (!(Auth::user()->hasPermissionTo('student:edit') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'student_list' => 'required|file|mimes:xlsx,xls,csv|max:51200',
            'type' => 'required|integer',
        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        DB::table('temp_scholarship_upload')->truncate();

        $path = storage_path() . '/data/registrations/scholarship/';
        $file = $request->file('student_list');        
        $file_name = time().'-'.str_replace(' ', '-', strtolower($file->getClientOriginalName()));

        if($file->move($path, $file_name)){
            try {
                Excel::import(new ScholarshipImport($request->type), $path.$file_name);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            $sql = 'UPDATE temp_scholarship_upload x INNER JOIN student_personal_details y ON x.registration_no= y.registration_no SET x.student_id = y.id';
            DB::update($sql);

            $matchedCount = DB::table('temp_scholarship_upload')->where('student_id', '>', 0)->count();

            if ($matchedCount === 0) {
                return response()->json(['error' => 'No matching students found for the registration numbers in the file.'], 422);
            }

            DB::update('UPDATE temp_scholarship_upload x INNER JOIN student_academic_details y on x.student_id = y.student_id SET y.scholarship_start_date = x.awarded_date, y.main_scholarship = x.scholarship_type');

            return response()->json(['success' => true, 'updated' => $matchedCount], 200);
        }

        return response()->json(['errors' => 'Oops! Something went wrong. Refresh the page and try again.'], 500); 
    }
}
