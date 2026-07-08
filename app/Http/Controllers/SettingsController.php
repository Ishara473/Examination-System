<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;


use App\Settings;
use App\CourseSchedule;
use App\CourseSubject;
use App\Specialization;
use App\CourseSpecialization;

use App\Fee;
use App\Role;
use App\Permission;
use App\User;
use App\Batch;
use App\Program;
use App\Regulation;
use App\SystemLog;




use Auth;
use DB;
use Artisan;
use Validator;
use Redirect;

use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    protected $AuthUser;

    public function __construct(){
        $this->middleware('auth:users');    
    }

    public function index(){
        return view('admin.settings.index');
    }

    /****************************************************************************************************** */    
    /**********************************  System Settings ************************************************** */  
    /****************************************************************************************************** */    

    public function list_settings(Request $request){ 
        if (!(Auth::user()->hasPermissionTo('manage:settings') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        return view('admin.settings.system-settings');
        
    }

    public function add_setting(Request $request){
        
    }

    public function update_setting(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:settings') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        foreach($request->Settings as $key=>$value){
            $setting = Settings::where('name','=',$key)->first();
            $setting->value = $value;
            $setting->save();
        }
        Artisan::call('cache:clear');
        return 1;
    }

    public function delete_setting(Request $request){
        
    }

    /****************************************************************************************************** */    
    /**********************************   Fees   ********************************************************** */  
    /****************************************************************************************************** */    

    public function list_fees(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:fee') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view' ) return view('admin.settings.fees');
        elseif($request->type == 'json'){
            $data = [];
            $col = ['2'=>'Code',3=>'Name',4=>'Amount',5=>'Surchage'];

            $a = Fee::select('id as ID', 'code AS Code', 'name as Name', 'amount as Amount', 'surcharge_amount as Surchage');

            if(!empty($request->search)){
                $search = '%'.$request->search.'%';
                $a->Where('code','like',$search);
            }
            
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function add_fees(Request $request){

    }

    public function update_fees(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:fee') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $fee = Fee::where('id','=',$request->id)->first();
        if($fee){
            $fee->amount = $request->amount ;
            $fee->surcharge_amount = $request->surcharge_amount ;
            $fee->save();
            return 1;
        }
        return -1;
    }

    public function delete_fees(Request $request){
 
    }

    /****************************************************************************************************** */    
    /**********************************   Batch   ********************************************************** */  
    /****************************************************************************************************** */    

    public function list_batch(Request $request){
        // if (!(Auth::user()->hasPermissionTo('manage:batch') || Auth::user()->hasRole('Admin') )){ 
        //     abort(403, 'Unauthorized action.');
        // }

        if(!isset($request->type) || $request->type == 'view' ){
            $programs = Arr::pluck(Program::all()->toArray(),'name','id');
            return view('admin.settings.batch',['programs'=>$programs]);

        }elseif($request->type == 'json'){
            $data = [];
            $col = [2=>'Code',3=>'ALYear',4=>'AccYear',5=>'IsCurrent'];

            $a = Batch::select('id as ID', 'code as Code', 'al_year as ALYear', 'academic_year as AccYear','is_current as IsCurrent', 'program_id as ProgramId',  );
           
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function add_batch(Request $request){
        // if (!(Auth::user()->hasPermissionTo('manage:batch') || Auth::user()->hasRole('Admin') )){ 
        //     abort(403, 'Unauthorized action.');
        // }
        $validator = Validator::make($request->all(), ['code' => 'required','accyear' => 'required','alyear' => 'required','iscurrent' => 'required' ]);
        if($validator->fails()){
            return -1;
        }else{
            if($request->iscurrent == 1){
                Batch::where('is_current','=','1')->update(['is_current'=>0]);
            }
            $batch = new Batch();
            $batch->code = $request->code;
            $batch->academic_year = $request->accyear;
            $batch->al_year = $request->alyear ;
            $batch->is_current = $request->iscurrent ;
            $batch->program_id = $request->programid ;
            $batch->save();
            return 1;
        }

    }

    public function update_batch(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:fee') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $batch = Batch::where('id','=',$request->id)->first();
        if($batch){
            $batch->code = $request->code;
            $batch->academic_year = $request->accyear;
            $batch->al_year = $request->alyear ;
            $batch->is_current = $request->iscurrent ;
            $batch->program_id = $request->programid ;
            $batch->save();
            return 1;
        }
        return -1;
    }

    // public function delete_bacth(Request $request){
 
    // }


    /****************************************************************************************************** */    
    /**********************************   Regulations   ********************************************************** */  
    /****************************************************************************************************** */    

    public function list_regulations(Request $request){
        // if (!(Auth::user()->hasPermissionTo('manage:regulation') || Auth::user()->hasRole('Admin') )){ 
        //     abort(403, 'Unauthorized action.');
        // }

        if(!isset($request->type) || $request->type == 'view' ) return view('admin.settings.regulation');
        elseif($request->type == 'json'){
            $data = [];
            $col = [2=>'Name',3=>'BylawVer',4=>'Version',5=>'IsCurrent'];

            $a = Regulation::select('id as ID', 'name as Name', 'by_law_version as BylawVer', 'version as Version','is_current as IsCurrent');
           
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;

            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function add_regulation(Request $request){
        // if (!(Auth::user()->hasPermissionTo('manage:batch') || Auth::user()->hasRole('Admin') )){ 
        //     abort(403, 'Unauthorized action.');
        // }
        $validator = Validator::make($request->all(), ['name' => 'required','iscurrent' => 'required' ]);
        if($validator->fails()){
            return -1;
        }else{
            if($request->iscurrent == 1){
                Regulation::where('is_current','=','1')->update(['is_current'=>0]);
            }
            $batch = new Regulation();
            $batch->name = $request->name;
            $batch->by_law_version = $request->bylaw;
            $batch->version = $request->version ;
            $batch->is_current = $request->iscurrent ;
            $batch->save();
            return 1;
        }

    }

    public function update_regulation(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:fee') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $batch = Regulation::where('id','=',$request->id)->first();
        if($batch){
            if($request->iscurrent == 1){
                Regulation::where('is_current','=','1')->update(['is_current'=>0]);
                $batch->is_current = $request->iscurrent ;
            }
            $batch->name = $request->name;
            $batch->by_law_version = $request->bylaw;
            $batch->version = $request->version ;
            $batch->save();
            return 1;
        }
        return -1;
    }

    // public function delete_bacth(Request $request){
 
    // }



    /****************************************************************************************************** */    
    /********************************** Courses *********************************************************** */  
    /****************************************************************************************************** */    

    public function list_courses(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        if(!isset($request->type) || $request->type == 'view' ) {
            $regulations = Arr::pluck(Regulation::all()->toArray(),'name','id');
            $currnt_regulation = Regulation::where('is_current','=','1')->pluck('id');
            $regulations['']='';
            $specializations = Specialization::orderBy('department')->orderBy('name')->get();


            return view('admin.settings.subjects',['specializations'=> $specializations,'regulations'=>$regulations,'currnt_regulation'=>$currnt_regulation]);

        }elseif($request->type == 'json'){
            $data = [];
            $col = [2=>'Code',3=>'Name',4=>'Year',5=>'Semester', 6=>'Credits', 7=>'Type',8=>'Status'];

            $a = CourseSubject::select('id as ID', 'code AS Code', 'name as Name', 'year as Year', 'semester as Semester', 'status as Status', 'type as Type', 'credits as Credits', 'amount as Amount', 'surcharge as Surcharge','regulation_id as Regulation', 'display_order as Order');

            if(!empty($request->search)){
                $search = '%'.$request->search.'%';
                $a->Where('name','like',$search);
            }
            if(!empty($request->search_regulation))$a->Where('regulation_id','=',$request->search_regulation);
            if(!empty($request->search_type))$a->Where('Type','=',$request->search_type);
            if(!empty($request->search_semester))$a->Where('Semester','like',$request->search_semester);
            

            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function get_course_specialization(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $data =[];
        $specialization = CourseSubject::join('course_subject_specialization','course_subjects.id','course_subject_specialization.subject_id')
                                ->where('id','=',$request->subjectId)
                                ->select('course_subject_specialization.specialization_id','course_subject_specialization.type')->get();

        if($specialization){
            foreach($specialization as $row)$data[$row->specialization_id]=$row->type;
        }
        return response()->json($data);
    }

    public function update_course_specialization(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $subject = CourseSubject::where('id','=',$request->spcid)->first();
        if(!empty($subject)){
            CourseSpecialization::where('subject_id','=',$subject->id)->delete();
            foreach($request->sp as $key=>$value){
                CourseSpecialization::create(['subject_id'=>$subject->id,'specialization_id'=>$key,'type'=>strtoupper($value)]);
            }
            return 1;
        }
        return -1;

    }


    public function add_course(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $year = [1=>1,2=>1,3=>2,4=>2,5=>3,6=>3,7=>4,8=>4];
        $validator = Validator::make($request->all(), ['code' => 'required','name' => 'required','semester' => 'required','credits' => 'required','type' => 'required','status' => 'required' ]);
        if($validator->fails()){
            return -1;
        }else{
            $course = new CourseSubject();
            $course->code = $request->code;
            $course->name = $request->name;
            $course->year = isset($year[$request->semester])?$year[$request->semester]:1;
            $course->semester = $request->semester ;
            $course->credits = $request->credits ;
            $course->type = $request->type ;
            $course->status = $request->status ;    
            $course->regulation_id = $request->regulation ;
            $course->display_order = $request->display_order;
            $course->save();
            return 1;
        }
    }

    public function update_course(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $course = CourseSubject::where('id','=',$request->id)->first();
        if($course){
            $year = [1=>1,2=>1,3=>2,4=>2,5=>3,6=>3,7=>4,8=>4]; //semester to year mapping

            $course->code = $request->code;
            $course->name = $request->name;
            $course->year = isset($year[$request->semester])?$year[$request->semester]:1;
            $course->semester = $request->semester ;
            $course->credits = $request->credits ;
            $course->type = $request->type ;
            $course->status = $request->status ;   
            $course->regulation_id = $request->regulation ;
            $course->display_order = $request->display_order;
            $course->save();
            return 1;
        }
        return -1;
    }

    public function get_course_lecturer_list(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $permission = Permission::where('name','=','course:teacher')->first();
        $roles = $permission->getRoleNames();

        // dd($roles);
        // $lec = User::hasPermissionTo('course:teacher')->get();


        $lec = User::role($roles)->get();
        if($lec){
            $lectueres = [];
            foreach($lec as $l){
                $lectueres[$l->id] = ['Id'=>$l->id, 'Name' => $l->first_name.' '.$l->last_name,'IsAssigned' => 0];
            }
        }


        $subject = CourseSubject::where('id','=',$request->id)->first();
        $assigned = $subject->lectueres()->get();
        if($assigned){
            foreach($assigned as $u){
                $lectueres[$u->id]['IsAssigned'] = 1;
            }
        }
        return response()->json($lectueres);   
    }

    public function update_course_lecturer(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:course') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $subject = CourseSubject::where('id','=',$request->subjectId)->first();
        if($subject){
            $new_lecturers = $request->lecturer;
            $assigned = $subject->lectueres()->get();
            if($assigned){          
                foreach($assigned as $l){
                    if(!isset($new_lecturers[$l->id])){
                        $subject->lectueres()->detach($l->id);
                    }else{
                        unset($new_lecturers[$l->id]);
                    }
                }
            }
            if($new_lecturers){
                foreach($new_lecturers as $key=>$value){
                    $subject->lectueres()->attach($key);
                }
            }
            return 1;
        }
        return -1;
    }

    public function delete_course(Request $request){

    }


    /****************************************************************************************************** */    
    /**********************************  Schedules ******************************************************** */  
    /****************************************************************************************************** */

    public function list_schedules(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:schedule') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view' ) return view('admin.settings.schedule');
        elseif($request->type == 'json'){
            $data = [];

            $col = ['2'=>'Code', 3=>'Name', 4=>'StartDate', 5=>'OverdueDate', 6=>'EndDate', 7=>'IsEnabled'];

            $a = CourseSchedule::select('id as ID', 'code AS Code', 'name as Name', 'start_date as StartDate', 'overdue_date as OverdueDate', 'end_date as EndDate', 'is_enabled as IsEnabled' );

            if(!empty($request->search)){
                $search = '%'.$request->search.'%';
                $a->where('code','like',$search)->orWhere('name','like',$search);
            }
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function add_schedule(){

    }

    public function update_schedule(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:schedule') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $schudule = CourseSchedule::where('id','=',$request->id)->first();
        if($schudule){
            $schudule->name = $request->name;
            $schudule->start_date = $request->start_date ;
            $schudule->overdue_date = $request->overdue_date ;
            $schudule->end_date = $request->end_date ;
            $schudule->is_enabled = $request->is_enabled ;
            $schudule->save();
            return 1;
        }
        return -1;
    }

    public function delete_schedule(Request $request){
        
    }

    /****************************************************************************************************** */    
    /**********************************  Roles ************************************************************ */  
    /****************************************************************************************************** */    

    public function list_roles(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:roles') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view' ) return view('admin.settings.roles.index');
        elseif($request->type == 'json'){
            $data = [];
            $col = ['2'=>'Name','3'=>'Description'];
            $a = Role::where('id','>',1)->select('id as ID',  'name as Name', 'description as Description' );

            if(!empty($request->search)){
                $search = '%'.$request->search.'%';
                $a->where('name','like',$search);
            }
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function create_role_view(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:roles') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $permissions =[];
        $ps = Permission::orderBy('name')->get();
        foreach($ps as $p){
            $permission_type = explode(':',$p->name);
            $permissions[$p->id] = ['id'=>$p->id,'name'=>$p->name,'module'=>$permission_type[0],'event'=>$permission_type[1],'description'=>$p->description];
        }            
        return view('admin.settings.roles.add',['permissions'=>$permissions]);

    }

    public function create_role(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:roles') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }       
        $validator = Validator::make($request->all(), ['name' => 'required|unique:roles,name','description' => 'required' ]);
        if($validator->fails()){
            return Redirect::back()->withErrors($validator->errors());
        }else{
            $role = new Role();
            $role->name = $request->name;
            $role->description = $request->description;
            $role->save();

            $new_permissions = $request->permissions;
            if($new_permissions){
                foreach($new_permissions as $key=>$value){
                    $permission = Permission::findById($value);
                    $role->givePermissionTo($permission->name);
                }
            }
            return redirect(url('/admin/settings/roles'));
        }
    }

    public function update_role_view(Request $request,int $id){
        if (!(Auth::user()->hasPermissionTo('manage:roles') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $role = Role::where('id','=',$id)->first();
        if($role){
            $permissions =[];
            $ps = Permission::orderBy('name')->get();
            foreach($ps as $p){
                $permission_type = explode(':',$p->name);
                $permissions[$p->id] = ['id'=>$p->id,'name'=>$p->name,'module'=>$permission_type[0],'event'=>$permission_type[1],'description'=>$p->description];
            }

            $role_permissions = [];
            $rps = $role->permissions()->pluck('id');
            if($rps){
                foreach($rps as $rp){
                    $role_permissions[$rp] = 1;
                }
            }
            return view('admin.settings.roles.edit',['role'=>$role,'role_permissions'=>$role_permissions,'permissions'=>$permissions]);
        }
    }

    public function update_role(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:roles') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }       
        $validator = Validator::make($request->all(), ['name' => 'required|unique:roles,name,'.$request->id.',id','description' => 'required' ]);
        if($validator->fails()){
            return Redirect::back()->withErrors($validator->errors());
        }else{
            $role = Role::where('id','=',$request->id)->first();
            $role->name = $request->name;
            $role->description = $request->description;
            $role->save();

            $new_permissions = $request->permissions;
            
            $role_permissions = [];
            $rps = $role->permissions()->pluck('id');
            if($rps){
                foreach($rps as $rp){
                    if(!isset($new_permissions[$rp])){
                        $permission = Permission::findById($rp);
                        $role->revokePermissionTo($permission->name);
                    }else{
                        unset($new_permissions[$rp]);
                    }
                }
            }
            if($new_permissions){
                foreach($new_permissions as $key=>$value){
                    $permission = Permission::findById($value);
                    $role->givePermissionTo($permission->name);
                }
            }
            return redirect(url('/admin/settings/roles'));
        }
    }


    /****************************************************************************************************** */    
    /**********************************  Users ************************************************************ */  
    /****************************************************************************************************** */    

    public function list_users(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:users') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        if(!isset($request->type) || $request->type == 'view' ) return view('admin.settings.users.index');
        elseif($request->type == 'json'){
            $data = [];
            $col = ['2'=>'FirstName','3'=>'LastName','4'=>'Email','5'=>'Designation'];

            $a = User::searchUsers($request->search);
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function create_user_view(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:users') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }
        $roles = Role::orderBy('name')->get();          
        return view('admin.settings.users.add',['roles'=>$roles]);
    }

    public function create_user(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:users') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email',
            'first_name' => 'required',
            'last_name' => 'required',
            'designation' => 'required',
            'password' => 'required'
            ]);
        if($validator->fails()){
            return Redirect::back()->withErrors($validator->errors());
        }else{
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->designation = $request->designation;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $roles = $request->roles;
            if($roles){
                foreach($roles as $key=>$value){
                    $role = Role::findById($value);
                    $user->assignRole($role);
                }
            }
            return redirect(url('/admin/settings/users'));
        }
    }

    public function update_user_view(Request $request,int $id){
        if (!(Auth::user()->hasPermissionTo('manage:users') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $user = User::where('id','=',$id)->first();
        if($user){
            $roles = Role::orderBy('name')->get();          
            $user_roles = [];
            $rs = $user->roles()->pluck('id');
            if($rs){
                foreach($rs as $r){
                    $user_roles[$r] = 1;
                }
            }
            return view('admin.settings.users.edit',['user'=>$user,'roles'=>$roles,'user_roles'=>$user_roles]);
        }
    }

    public function update_user(Request $request){
        if (!(Auth::user()->hasPermissionTo('manage:users') || Auth::user()->hasRole('Admin') )){ 
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,'.$request->id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'designation' => 'required'
            ]);
        if($validator->fails()){
            return Redirect::back()->withErrors($validator->errors());
        }else{
            $user = User::where('id','=',$request->id)->first();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->designation = $request->designation;
            $user->email = $request->email;
            $user->is_active = isset($request->is_active)?1:0;
            if(trim($request->password))$user->password = Hash::make($request->password);
            $user->save();

            $new_role = $request->roles;
            
            $role_permissions = [];
            $rs = $user->roles()->pluck('id');
            if($rs){
                foreach($rs as $r){
                    if(!isset($new_role[$r])){
                        $role = Role::findById($r);
                        $user->removeRole($role);
                    }else{
                        unset($new_role[$r]);
                    }
                }
            }
            if($new_role){
                foreach($new_role as $key=>$value){
                    $role = Role::findById($value);
                    $user->assignRole($role);
                }
            }
            return redirect(url('/admin/settings/users'));
        }
    }


    /****************************************************************************************************** */    
    /**********************************  System Logs ****************************************************** */  
    /****************************************************************************************************** */   

    public function list_system_logs(Request $request){
        if (!Auth::user()->hasRole('Admin')){ 
            abort(403, 'Unauthorized action.');
        }
        if(!isset($request->type) || $request->type == 'view' ) return view('admin.settings.system-logs');
        elseif($request->type == 'json'){
            $data = [];
            $col = ['1'=>'Date','2'=>'User','3'=>'IP','4'=>'Module','5'=>'Description'];

            $a = SystemLog::leftJoin('users','system_logs.user_id','=','users.id')
                    ->select(
                            'system_logs.id as ID',
                            'system_logs.time as Date',
                             DB::raw('CONCAT(users.first_name," ",users.last_name) AS User'),
                             'system_logs.ip as IP',
                             'system_logs.module as Module',
                             'system_logs.description as Description');
            

            if(!empty($request->module)){
                $a->where('module','=',$request->module);
            }
            $ac = clone $a;
            $Count = $ac->count();

            $data['recordsTotal']=    $Count;
            $data['recordsFiltered']= $Count;


            $a->orderBy($col[$request->order[0]['column']],$request->order[0]['dir']);
            $a->offset($request->start)->limit($request->length);
            $records = $a->get();                 
            
            
            $data['data']=$records;
            $data['draw']=$request->draw;
            return response()->json($data);
        }
    }

    public function get_uploaded_files(Request $request){
        if (!Auth::user()->hasRole('Admin')){ 
            abort(403, 'Unauthorized action.');
        }
        if(!empty($request->file)){
            $file = storage_path() .'/data/'. $request->file;
            return response()->file($file);
        }

    }
}
