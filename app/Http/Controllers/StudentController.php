<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Auth;
// use DB;
use File;
// use Excel;
use Hash;

use Validator;
use Carbon\Carbon;

use App\Student;
use App\StudentAccDetail;
use App\StudentContact;
use App\StudentAL;
use App\StudentGaurdian;
use App\ALSubject;

use Illuminate\Support\Arr;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:student');
    }

    public function index()
    {
        $user = Auth::user();
        if($user->is_profile_confirmed==0) return redirect(url('/student/update-profile'));
        else return view('student.index',['user'=>$user]);
    }

    public function update_profile()
    {
        $user = Auth::user();
        $alSubjects = Arr::pluck(ALSubject::orderBy('subject')->select('id','subject')->get()->toArray(), 'subject', 'id');
        $alSubjects[-1] ='';
        return view('student.update-profile',['student'=>$user,'alSubjects'=>$alSubjects]);
    }

    public function profile_images(Request $request)
    {
        $user = Auth::user();

        $file = storage_path() . '/Student/Image/Profile/1/'.$user->id.'.jpg';
        if(!File::exists($file)) $file = public_path().'/images/user.jpg';

        $headers = [
            'Content-Type' => 'image/jpg',
            'Content-Disposition' => sprintf('attachment; filename="%s"', 'image.jpg'),
        ];

        return response()->file($file);

    }

    public function save_profile_updates(Request $request)
    {
        $user = Auth::user();


        // print_r($request->all());
        $student = Student::where('id','=',$user->id)->first();

        // Capture the original confirmation status BEFORE any mutation
        $wasConfirmed = ($user->is_profile_confirmed == 1);

        if(!$wasConfirmed){
            $student->dob = $request->dob;
            $student->race = $request->race;
            $student->religion = $request->religion;
            $student->writing_hand = $request->writing_hand;        
            $student->citizenship = $request->citizenship;
            $student->is_profile_confirmed=1;
        }
        $student->full_name_sinhala = $request->full_name_sinhala;
        $student->full_name_tamil = $request->full_name_tamil;
        $student->save();

        $contact = $student->contact()->first();
        $contact->mobile = $request->mobile ;
        if(!$wasConfirmed){
            $contact->contact_address1 = $request->contact_address1 ;
            $contact->contact_address2 = $request->contact_address2 ;
            $contact->contact_address3 = $request->contact_address3 ;
            $contact->gn_division = $request->gn_division ;
            $contact->moh_area = $request->moh_area ;
            $contact->electorate = $request->electorate ;
        }
        $contact->save();

        $gaurdian = $student->gaurdian()->first();
        if(empty($gaurdian)) {
            $gaurdian = new StudentGaurdian();
            $gaurdian->student_id = $user->id;
        }

        if(!$wasConfirmed){
            $gaurdian->type = $request->gaurdian_type ;
            $gaurdian->full_name = $request->gaurdian_name ;
            $gaurdian->occupation = $request->gaurdian_occupation ;
            $gaurdian->address = $request->gaurdian_address ;
            $gaurdian->phone = $request->gaurdian_phone ;
            $gaurdian->emergency_c_name = $request->gaurdian_emergency_c_name ;
            $gaurdian->emergency_c_phone = $request->gaurdian_emergency_c_phone ;
        }
        $gaurdian->save();
        

        $alDetails = $student->AL()->first();
        if(empty($alDetails)){
            $alDetails = new StudentAL();
            $alDetails->student_id = $request->id;
        }
        if(!$wasConfirmed){
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
        }
        $alDetails->save();

        return redirect(url('/'));

    }

    
    public function update_password(){
        $user = Auth::user();
        return view('student.change-password');
    }

    public function save_updated_password(Request $request){
        $user = Auth::user();
        $student = Student::where('id','=',$user->id)->first();



        if($request->new_password_1===$request->new_password_2 &&  Hash::check($request->current_password, $student->password)){
            $student->password = Hash::make($request->new_password_1);
            $student->save();
            Session::flash('success','Password was updated successfully');
            return redirect(url('/'));
        }

        return redirect()->back()->withErrors(['password'=>'Password update failed. Please check your current password.']);
    }


    
}
