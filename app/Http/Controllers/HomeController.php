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
        $staff = User::all();
        $students = Student::where('status','continuing')->get();
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active',1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active',1)->first())->year; 
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

        //Students Summary
        $girls = Student::where('gender','Female')->count();
        $boys = Student::where('gender','Male')->count();

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
