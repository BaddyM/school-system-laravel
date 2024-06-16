<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    function index(){
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active',1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active',1)->first())->year; 

        $staff = User::all();
        $students = DB::table('student_'.$year.'')->where('status','continuing')->get();
        $planner = DB::table('planner')->where(['term' => $term, 'year' => $year])->get();
        
        $subjects = DB::select("
            SELECT 
                DISTINCT name
            FROM
                subjects
        ");

        Artisan::call('optimize:clear');
        Artisan::call('view:clear');

        return view('common.landing',compact('staff','students','subjects', 'planner'));
    }

    function fetch_term(){
        $term = DB::table('term')->select('term','year')->where('active',1)->first();
        $year =  (DB::table('term')->select('year')->where('active',1)->first())->year;

        //Students Summary
        $girls = DB::table('student_'.$year.'')->where('gender','Female')->count();
        $boys = DB::table('student_'.$year.'')->where('gender','Male')->count();

        //Staff Summary
        $females = User::where('gender','female')->count();
        $males = User::where('gender','male')->count();

        //School Details
        $school = DB::table('school_details')->where('id',1)->first();

        return response()->json([
            'term' => $term,
            'girls' => $girls,
            'boys' => $boys,
            'males' => $males,
            'females' => $females,
            'school' => $school
        ]);
    }

}
