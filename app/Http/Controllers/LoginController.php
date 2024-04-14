<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller {
    public function login(){
        return view('login.login');
    }

    public function validate_login(Request $req){              
        $credentials = $req->validate([
            'email' => ['required','email'],
            'password' => ['required']
        ]);

        //Check if the credentials match those in the database
        if(Auth::attempt($credentials)){
            $response = redirect()->intended(route('home'));
        }else{            
            $response =  redirect()->route('login')->with('error','Incorrect Credentials');
        }

        return $response;
    }

    public function logout(Request $request){
        Auth::logout(); 
        $request->session()->invalidate();    
        $request->session()->regenerateToken();    
        return redirect()->route('login');
    }
}
