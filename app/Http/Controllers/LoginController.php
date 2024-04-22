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
            'password' => ['required'],
        ]);

        //Check if the credentials match those in the database
        if(Auth::attempt($credentials)){
            $active = Auth::user()->is_active;
            $email_verified = Auth::user()->email_verified;
            
            if($active == 0){
                $response =  redirect()->route('login')->withErrors("Sorry, User Inactive!");
            }elseif($email_verified == 0){
                $response =  redirect()->route('login')->withErrors("Sorry, Email Not Verified!");
            }else{
                $response = redirect()->intended(route('home'));
            }
            
        }else{            
            $response =  redirect()->route('login')->withErrors("Incorrect credentials!");
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
