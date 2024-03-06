<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class HomeController extends Controller
{
    function index(){
        $staff = User::all();
        $students = Student::all();

        $subjects = DB::select("
            SELECT 
                DISTINCT name
            FROM
                subjects
        ");

        return view('common.landing',compact('staff','students','subjects'));
    }

    function fetch_term(){
        $term = DB::table('term')->select('term','year')->where('active',1)->first();
        return response()->json(['term'=>$term]);
    }
}
