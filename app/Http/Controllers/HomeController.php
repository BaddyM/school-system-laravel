<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    function index(){
        return view('common.landing');
    }

    function fetch_term(){
        $term = DB::table('term')->select('term','year')->where('active',1)->first();
        return response()->json(['term'=>$term]);
    }
}
