<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use \App\Student;

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest:student')->except('logout');
    }

    public function showLoginForm(){
        return view('student.login');
    }

    public function login(Request $request){
        $messages = [
            'password.required' => 'Please enter your password',
            'registration.required' => 'Please enter a valid Registration number',
        ];

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'password' => 'required',
            'registration' => 'required',

        ], $messages);

        if($validator->fails()){
            return redirect('/login')
                ->withErrors($validator)
                ->withInput();
        }else{
            if(Auth::guard('student')->attempt(['registration_no'=>$request->registration,'password'=>$request->password,'status'=>1],false)){
                return redirect('/');
            }else{
                return redirect()->back()->withInput($request->only('registration_no'))->withErrors(['account'=>'Registration number and password did not match.']);
            }
        }
    }

    public function logout(){
        Auth::guard('student')->logout();
        return redirect('/login');
    }
}
