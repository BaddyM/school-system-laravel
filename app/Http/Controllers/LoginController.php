<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthMail;

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

    //Signup
    public function signup_index(){
        return view('login.signup');
    }

    public function register_user(Request $req){
        $email = $req->email;
        $fname = strtolower($req->fname);
        $lname = strtolower($req->lname);
        $username = $lname." ".$fname;

        //Check if the email exists
        $exists = User::where('email',$email)->exists();
        
        if($exists == 1){
            $response = "Email Already Exists";
        }else{
            try{
                //Register new User
                $password = Hash::make($req->password);

                User::insert([
                    'dept' => '-',
                    'gender' => '-',
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'created_at' => now()
                ]);

                $response = "Registration Successfull";
            }catch(Exception $e){
                $response = "Failed to Register Email!";
                info($e);
            }
        }
        return response($response);
    }

    //Forgot-password
    public function forgot_pass_index(){
        return view('login.forgot_password');
    }

    public function send_email(Request $req){
        $email = $req->email;

        //Make user inactive
        try{
            $response = "Contact Admin!";
            User::where('email',$email)->update([
                'is_active' => 0,
                'password' => null
            ]);
        }catch(Exception $e){
            $response = "Password reset failed!";
            info($e);
        }

        /*
        $content = [
            'subject' => 'This is the mail subject',
            'body' => 'This is the email body of how to send email from laravel 10 with mailtrap.'
        ];

        Mail::to('arnoldhenry958@gmail.com')->send(new AuthMail($content));
        */
        return response($response);
    }
}
