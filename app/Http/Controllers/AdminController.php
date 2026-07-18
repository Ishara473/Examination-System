<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Role;
use App\Permission;
use App\User;


use DB;
use Validator;
use Redirect;

use Illuminate\Support\Facades\Hash;

class AdminController extends Controller{

    public function __construct(){
        $this->middleware('auth:users');
    }

    public function index(){
        return view('admin.dashboard.index');
    }

    public function view_account(){
        $user = Auth::user();
        return view('admin.account',['user'=>$user]);
    }


    public function update_account(Request $request){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email,'.$user->id.',id',
            'first_name' => 'required',
            'last_name' => 'required',
            'designation' => 'required'
            ]);
        if($validator->fails()){
            return Redirect::back()->withErrors($validator->errors());
        }else{           
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->designation = $request->designation;
            $user->email = $request->email;
            if(trim($request->password))$user->password = Hash::make($request->password);
            $user->save();
        }

        return redirect(url('/admin/account'));
    }

}
