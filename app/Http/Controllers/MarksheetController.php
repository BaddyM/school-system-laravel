<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Log;
use DB;
use PDF;

class MarksheetController extends Controller{
    //declare global variable in the class
    var $points = [];

    public function alevel(){
        $result_set = DB::select("SELECT * FROM old_curr");
        $term = DB::select("SELECT * FROM term");

        return view('marksheet.alevel',compact('result_set','term'));
    }

    public function marksheet(Request $request){
        $class = $request->classname;
        $result = $request->result;

        return response()->json([
            'class' => $class,
            'result' => $result
        ]);
    }

    public function fetchdata(Request $request){
        $class = $request->classname;
        $result = $request->result;
        
        $data = DB::select('
        select * from
        students
        inner join
        '.$result.'
        where
        students.stdID = '.$result.'.stdID
        and class="'.$class.'"
        ');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('result_set',function(){
                
            })
            ->addColumn('points', function($fetch){
                $this->points = [];

                //grading subjects
                $two_subs = [
                    "Mathematics",
                    "History",
                    "Economics",
                ];

                $three_subs = [
                    "Geography",
                    "Divinity",
                    "Literature",
                    "Entrepreneurship",
                    "Luganda"
                ];

                $one_subs = [
                    "Submath",
                    "general_paper"
                ];
                
                //Two paper grade here
               foreach($two_subs as $two){
                    $paper_one = $two;
                    $paper_two = $two.'2';

                    if (($fetch->$paper_one >= 75 and $fetch->$paper_two >= 75) and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100)) {
                        $grade = 6;
                    } elseif (($fetch->$paper_one >= 65 and $fetch->$paper_two >= 75) || ($fetch->$paper_one >= 75 and $fetch->$paper_two >= 65)
                    || ($fetch->$paper_one >= 65 and $fetch->$paper_two >= 65)  and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100)
                    ) {
                        $grade = 5;
                    } elseif (($fetch->$paper_one >= 60 and $fetch->$paper_two >= 65) || ($fetch->$paper_one >= 65 and $fetch->$paper_two >= 60)
                    || ($fetch->$paper_one >= 60 and $fetch->$paper_two >= 60)  and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100)
                    ) {
                        $grade = 4;
                    } elseif (($fetch->$paper_one >= 55 and $fetch->$paper_two >= 60) || ($fetch->$paper_one >= 60 and $fetch->$paper_two >= 55)
                    || ($fetch->$paper_one >= 55 and $fetch->$paper_two >= 55)  and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100)
                    ) {
                        $grade = 3;
                    } elseif (($fetch->$paper_one >= 50 and $fetch->$paper_two >= 55) || ($fetch->$paper_one >= 55 and $fetch->$paper_two >= 50)
                    || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 50) || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 55) || ($fetch->$paper_one >= 55 and $fetch->$paper_two >= 50)
                    || ($fetch->$paper_one >= 45 and $fetch->$paper_two >= 55) || ($fetch->$paper_one >= 55 and $fetch->$paper_two >= 45)  and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100)
                    ) {
                        $grade = 2;
                    } elseif (($fetch->$paper_one >= 50 and $fetch->$paper_two >= 40) || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 50)
                    || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 60) || ($fetch->$paper_one >= 60 and $fetch->$paper_two >= 50) || ($fetch->$paper_one >= 65 and $fetch->$paper_two >= 0)
                    || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 65) || ($fetch->$paper_one >= 60 and $fetch->$paper_two >= 0) || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 60)
                    || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 50) || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 0) || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 50)
                    || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 40)  and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100)
                    ) {
                        $grade = 1;
                    } elseif (($fetch->$paper_one >= 40 and $fetch->$paper_two >= 0) || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 40) || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 0)
                        and ($fetch->$paper_one <= 49 and $fetch->$paper_two <= 49)
                    ) {
                        $grade = 0;
                    } else {
                        $grade = 0;
                    }

                    array_push($this->points, $grade);
                }

                //Three paper grade
                foreach($three_subs as $three){
                    $paper_one = $three;
                    $paper_two = $three.'2';
                    $paper_three = $three.'3';

                        if ((($fetch->$paper_one >= 75 and $fetch->$paper_two >= 75 and $fetch->$paper_three >= 64) || ($fetch->$paper_one >= 64 and $fetch->$paper_two >= 75 and $fetch->$paper_three >= 75)
                        || ($fetch->$paper_one >= 75 and $fetch->$paper_two >= 64 and $fetch->$paper_three >= 75) and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100))) {
                        $grade = 6;
                    } elseif ((($fetch->$paper_one >= 65 and $fetch->$paper_two >= 65 and $fetch->$paper_three >= 60) || ($fetch->$paper_one >= 60 and $fetch->$paper_two >= 65 and $fetch->$paper_three >= 65)
                        || ($fetch->$paper_one >= 65 and $fetch->$paper_two >= 60 and $fetch->$paper_three >= 65) and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100))) {
                        $grade = 5;
                    } elseif ((($fetch->$paper_one >= 60 and $fetch->$paper_two >= 60 and $fetch->$paper_three >= 55) || ($fetch->$paper_one >= 55 and $fetch->$paper_two >= 60 and $fetch->$paper_three >= 60)
                        || ($fetch->$paper_one >= 60 and $fetch->$paper_two >= 55 and $fetch->$paper_three >= 60) and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100))) {
                        $grade = 4;
                    } elseif ((($fetch->$paper_one >= 55 and $fetch->$paper_two >= 55 and $fetch->$paper_three >= 50) || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 55 and $fetch->$paper_three >= 55)
                        || ($fetch->$paper_one >= 55 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 55) and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100))) {
                        $grade = 3;
                    } elseif ((($fetch->$paper_one >= 50 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 45)
                        || ($fetch->$paper_one >= 45 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 50) || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 45 and $fetch->$paper_three >= 50)
                        || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 65) || ($fetch->$paper_one >= 65 and $fetch->$paper_two >= 40 and $fetch->$paper_three >= 50)
                        || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 65 and $fetch->$paper_three >= 40) and ($fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100))) {
                        $grade = 2;
                    } elseif (($fetch->$paper_one >= 50 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 50) || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 40 and $fetch->$paper_three >= 40)
                        || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 40 and $fetch->$paper_three >= 40) || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 0 and $fetch->$paper_three >= 40)
                        || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 40 and $fetch->$paper_three >= 0) || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 0 and $fetch->$paper_three >= 50)
                        || ($fetch->$paper_one >= 50 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 0) || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 50 and $fetch->$paper_three >= 50 and $fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100)
                    ) {
                        $grade = 1;
                    } elseif (($fetch->$paper_one >= 0 and $fetch->$paper_two >= 40 and $fetch->$paper_three >= 40) || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 0 and $fetch->$paper_three >= 40)
                        || ($fetch->$paper_one >= 40 and $fetch->$paper_two >= 40 and $fetch->$paper_three >= 0) || ($fetch->$paper_one >= 0 and $fetch->$paper_two >= 0 and $fetch->$paper_three >= 0 and $fetch->$paper_one <= 100 and $fetch->$paper_two <= 100 and $fetch->$paper_three <= 100)
                    ) {
                        $grade = 0;
                    } else {
                        $grade = 0;
                    }
                    array_push($this->points, $grade);
                }    

                //One paper grading
                foreach($one_subs as $one){
                    $paper_one = $one;
                    if (($fetch->$paper_one >= 50 and $fetch->$paper_one <= 100)) {
                        $grade = 1;
                    } else {
                        $grade = 0;
                    }
                    array_push($this->points, $grade);
                }

                //SubICT paper grade
                $subict = round((($fetch->Subict + $fetch->Subict2)/2),0);
                if (($subict >= 50 and $subict <= 100)) {
                    $grade = 1;
                    array_push($this->points, $grade);
                } else {
                    $grade = 0;
                    array_push($this->points, $grade);
                }

                //Four paper grade here
                $art = $fetch->Art;
                $art2 = $fetch->Art2;
                $art3 = $fetch->Art3;
                $art4 = $fetch->Art4;

                if($art >= 75 and $art2 >= 80 and $art3 >= 80 and $art4 >= 80 ||
                $art >= 80 and $art2 >= 75 and $art3 >= 80 and $art4 >= 80 ||
                $art >= 80 and $art2 >= 80 and $art3 >= 75 and $art4 >= 80 ||
                $art >= 80 and $art2 >= 80 and $art3 >= 80 and $art4 >= 75

                ) {
                $grade = 6;
                }elseif(
                $art >= 70 and $art2 >= 75 and $art3 >= 75 and $art4 >= 75 ||
                $art >= 75 and $art2 >= 70 and $art3 >= 75 and $art4 >= 75 ||
                $art >= 75 and $art2 >= 75 and $art3 >= 70 and $art4 >= 75 ||
                $art >= 75 and $art2 >= 75 and $art3 >= 75 and $art4 >= 70

                ){
                $grade = 5;
                }elseif(
                $art >= 65 and $art2 >= 70 and $art3 >= 70 and $art4 >= 70 ||
                $art >= 70 and $art2 >= 65 and $art3 >= 70 and $art4 >= 70 ||
                $art >= 70 and $art2 >= 70 and $art3 >= 65 and $art4 >= 70 ||
                $art >= 70 and $art2 >= 70 and $art3 >= 70 and $art4 >= 65

                ){
                $grade = 4;
                }elseif(
                $art >= 60 and $art2 >= 65 and $art3 >= 65 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 60 and $art3 >= 65 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 65 and $art3 >= 60 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 65 and $art3 >= 65 and $art4 >= 60

                ){
                $grade = 3;
                }elseif(
                $art >= 50 and $art2 >= 60 and $art3 >= 60 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 50 and $art3 >= 60 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 60 and $art3 >= 50 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 60 and $art3 >= 60 and $art4 >= 50 ||
                $art >= 40 and $art2 >= 60 and $art3 >= 60 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 60 and $art3 >= 60 and $art4 >= 40 ||
                $art >= 60 and $art2 >= 65 and $art3 >= 40 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 40 and $art3 >= 65 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 60 and $art3 >= 40 and $art4 >= 65 

                ){
                $grade = 2;
                }elseif(
                $art >= 50 and $art2 >= 50 and $art3 >= 50 and $art4 >= 50 ||
                $art >= 40 and $art2 >= 40 and $art3 >= 40 and $art4 >= 40 ||
                $art >= 0 and $art2 >= 40 and $art3 >= 40 and $art4 >= 40 ||
                $art >= 40 and $art2 >= 0 and $art3 >= 40 and $art4 >= 40 ||
                $art >= 40 and $art2 >= 40 and $art3 >= 0 and $art4 >= 40 ||
                $art >= 40 and $art2 >= 40 and $art3 >= 40 and $art4 >= 0 ||
                $art >= 0 and $art2 >= 0 and $art3 >= 50 and $art4 >= 50 ||
                $art >= 50 and $art2 >= 50 and $art3 >= 0 and $art4 >= 0 ||
                $art >= 50 and $art2 >= 0 and $art3 >= 0 and $art4 >= 50 ||
                $art >= 0 and $art2 >= 50 and $art3 >= 0 and $art4 >= 50 ||
                $art >= 0 and $art2 >= 50 and $art3 >= 50 and $art4 >= 0 

                ){
                $grade = 1;
                }elseif(
                $art >= 0 and $art2 >= 0 and $art3 >= 40 and $art4 >= 40 ||
                $art >= 40 and $art2 >= 40 and $art3 >= 0 and $art4 >= 0 ||
                $art >= 40 and $art2 >= 0 and $art3 >= 0 and $art4 >= 40 ||
                $art >= 0 and $art2 >= 40 and $art3 >= 0 and $art4 >= 40 ||
                $art >= 0 and $art2 >= 40 and $art3 >= 40 and $art4 >= 0 || 
                $art >= 0 and $art2 >= 0 and $art3 >= 0 and $art4 >= 0

                ){
                $grade = 0;
                }

                array_push($this->points, $grade);

                return array_sum($this->points);
                
            })
            ->make(true);
    }

    //Print Marksheet here
    public function marksheetpdf($result_set, $classname){
        $school = 'CORNERSTONE HIGH SCHOOL NANGABO';
        $result = $result_set;
        $class = $classname;

        $data = DB::select('
        select * from
        students
        inner join
        '.$result.'
        where
        students.stdID = '.$result.'.stdID
        and class="'.$class.'"
        ');

        //$pdf = PDF::loadView('marksheet.marksheetpdf', $data)->setPaper('A4','landscape');

        //Subjects here
        $ones = ['G.P', 'Submth'];

        $twos = ['Mtc1', 'Mtc2', 'His', 'His2', 'Econ', 'Econ2'];

        $threes = ['Lug', 'Lug2', 'Lug3', 'Phy', 'Phy2', 'Ph3', 'Chem', 'Chem2', 'Chem3', 'Bio', 'Bio2', 'Bio3', 'Geo', 'Geo2', 'Geo3', 'Div', 'Div2', 'Div4', 'Lit', 'Lit2', 'Lit3', 'Ent', 'Ent2', 'Ent3'];

        $fours = ['Art', 'Art2', 'Art3', 'Art4'];

        $subs = ['Subict', 'Subict2'];

        $merged = array_merge($twos, $threes, $fours, $ones, $subs);


        //Subjects here
        $one = [
            "general_paper",
            "Submath"
        ];

        $two = [
            "Mathematics",
            "Mathematics2",
            "History",
            "History2",
            "Economics",
            "Economics2"
        ];

        $three = [
            "Luganda",
            "Luganda2",
            "Luganda3",
            "Physics",
            "Physics2",
            "Physics3",
            "Chemistry",
            "Chemistry2",
            "Chemistry3",
            "Biology",
            "Biology2",
            "Biology3",
            "Geography",
            "Geography2",
            "Geography3",
            "Divinity",
            "Divinity2",
            "Divinity3",
            "Literature",
            "Literature2",
            "Literature3",
            "Entrepreneurship",
            "Entrepreneurship2",
            "Entrepreneurship3",
            
        ];

        $four = [
            "Art",
            "Art2",
            "Art3",
            "Art4",
        ];

        $sub = [
            "Subict",
            "Subict2",
        ];

        $subs_merged = array_merge($two, $three, $four, $one, $sub);

        $points = [];

        //Points accumulation here 
        //grading subjects
    $two_subs = [
        "Mathematics",
        "History",
        "Economics",
    ];

    $three_subs = [
        "Geography",
        "Divinity",
        "Literature",
        "Entrepreneurship",
        "Luganda"
    ];

    $one_subs = [
        "Submath",
        "general_paper"
    ];

        $html = '

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>'.$class.' - Marksheet</title>
        </head>
        <body>
            <style>
                table{
                    border-collapse: collapse;
                }
                th,td{
                    font-size:8px;
                    border:1px black solid;
                    padding:5px 2px 5px 2px;
                    text-align:center;
                }
                .schoolname{
                    margin:0px;
                    text-align:center;
                }
                .marksheet{
                    /*margin-top:-30px;*/
                }
            </style>
            
            <div class="" style="margin:-30px;">
                <h3 class="text-center text-uppercase schoolname">'.$school.'</h3>
                <h4 class="text-center fw-bold" style="text-align:center; margin:5px; text-transform:uppercase;">'.$class.' - MARKSHEET</h4>

                <div class="card marksheet">
                    <div class="card-body overflow-scroll">
                        <table id="alevel" class="">
                            <thead style="" class="text-white">
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Comb</th>
                                <th scope="col">Class</th>';
                                foreach ($merged as $m){
$html .='                                    <th class="text-uppercase" scope="col">'.$m.'</th>';
                                }
$html .='                                <th>Points</th>
                            </thead>
                            <tbody class="table-light">';
foreach( $data as $d ){
        //TWO PAPER GRADE
        foreach($two_subs as $two){
            $paper_one = $two;
            $paper_two = $two.'2';

            if (($d->$paper_one >= 75 and $d->$paper_two >= 75) and ($d->$paper_one <= 100 and $d->$paper_two <= 100)) {
                $grade = 6;
            } elseif (($d->$paper_one >= 65 and $d->$paper_two >= 75) || ($d->$paper_one >= 75 and $d->$paper_two >= 65)
            || ($d->$paper_one >= 65 and $d->$paper_two >= 65)  and ($d->$paper_one <= 100 and $d->$paper_two <= 100)
            ) {
                $grade = 5;
            } elseif (($d->$paper_one >= 60 and $d->$paper_two >= 65) || ($d->$paper_one >= 65 and $d->$paper_two >= 60)
            || ($d->$paper_one >= 60 and $d->$paper_two >= 60)  and ($d->$paper_one <= 100 and $d->$paper_two <= 100)
            ) {
                $grade = 4;
            } elseif (($d->$paper_one >= 55 and $d->$paper_two >= 60) || ($d->$paper_one >= 60 and $d->$paper_two >= 55)
            || ($d->$paper_one >= 55 and $d->$paper_two >= 55)  and ($d->$paper_one <= 100 and $d->$paper_two <= 100)
            ) {
                $grade = 3;
            } elseif (($d->$paper_one >= 50 and $d->$paper_two >= 55) || ($d->$paper_one >= 55 and $d->$paper_two >= 50)
            || ($d->$paper_one >= 50 and $d->$paper_two >= 50) || ($d->$paper_one >= 50 and $d->$paper_two >= 55) || ($d->$paper_one >= 55 and $d->$paper_two >= 50)
            || ($d->$paper_one >= 45 and $d->$paper_two >= 55) || ($d->$paper_one >= 55 and $d->$paper_two >= 45)  and ($d->$paper_one <= 100 and $d->$paper_two <= 100)
            ) {
                $grade = 2;
            } elseif (($d->$paper_one >= 50 and $d->$paper_two >= 40) || ($d->$paper_one >= 40 and $d->$paper_two >= 50)
            || ($d->$paper_one >= 50 and $d->$paper_two >= 60) || ($d->$paper_one >= 60 and $d->$paper_two >= 50) || ($d->$paper_one >= 65 and $d->$paper_two >= 0)
            || ($d->$paper_one >= 0 and $d->$paper_two >= 65) || ($d->$paper_one >= 60 and $d->$paper_two >= 0) || ($d->$paper_one >= 0 and $d->$paper_two >= 60)
            || ($d->$paper_one >= 50 and $d->$paper_two >= 50) || ($d->$paper_one >= 50 and $d->$paper_two >= 0) || ($d->$paper_one >= 0 and $d->$paper_two >= 50)
            || ($d->$paper_one >= 40 and $d->$paper_two >= 40)  and ($d->$paper_one <= 100 and $d->$paper_two <= 100)
            ) {
                $grade = 1;
            } elseif (($d->$paper_one >= 40 and $d->$paper_two >= 0) || ($d->$paper_one >= 0 and $d->$paper_two >= 40) || ($d->$paper_one >= 0 and $d->$paper_two >= 0)
                and ($d->$paper_one <= 49 and $d->$paper_two <= 49)
            ) {
                $grade = 0;
            } else {
                $grade = 0;
            }

            array_push($points, $grade);
        }

        //THREE PAPER GRADE
        foreach($three_subs as $three){
            $paper_one = $three;
            $paper_two = $three.'2';
            $paper_three = $three.'3';

                if ((($d->$paper_one >= 75 and $d->$paper_two >= 75 and $d->$paper_three >= 64) || ($d->$paper_one >= 64 and $d->$paper_two >= 75 and $d->$paper_three >= 75)
                || ($d->$paper_one >= 75 and $d->$paper_two >= 64 and $d->$paper_three >= 75) and ($d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100))) {
                $grade = 6;
            } elseif ((($d->$paper_one >= 65 and $d->$paper_two >= 65 and $d->$paper_three >= 60) || ($d->$paper_one >= 60 and $d->$paper_two >= 65 and $d->$paper_three >= 65)
                || ($d->$paper_one >= 65 and $d->$paper_two >= 60 and $d->$paper_three >= 65) and ($d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100))) {
                $grade = 5;
            } elseif ((($d->$paper_one >= 60 and $d->$paper_two >= 60 and $d->$paper_three >= 55) || ($d->$paper_one >= 55 and $d->$paper_two >= 60 and $d->$paper_three >= 60)
                || ($d->$paper_one >= 60 and $d->$paper_two >= 55 and $d->$paper_three >= 60) and ($d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100))) {
                $grade = 4;
            } elseif ((($d->$paper_one >= 55 and $d->$paper_two >= 55 and $d->$paper_three >= 50) || ($d->$paper_one >= 50 and $d->$paper_two >= 55 and $d->$paper_three >= 55)
                || ($d->$paper_one >= 55 and $d->$paper_two >= 50 and $d->$paper_three >= 55) and ($d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100))) {
                $grade = 3;
            } elseif ((($d->$paper_one >= 50 and $d->$paper_two >= 50 and $d->$paper_three >= 45)
                || ($d->$paper_one >= 45 and $d->$paper_two >= 50 and $d->$paper_three >= 50) || ($d->$paper_one >= 50 and $d->$paper_two >= 45 and $d->$paper_three >= 50)
                || ($d->$paper_one >= 40 and $d->$paper_two >= 50 and $d->$paper_three >= 65) || ($d->$paper_one >= 65 and $d->$paper_two >= 40 and $d->$paper_three >= 50)
                || ($d->$paper_one >= 50 and $d->$paper_two >= 65 and $d->$paper_three >= 40) and ($d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100))) {
                $grade = 2;
            } elseif (($d->$paper_one >= 50 and $d->$paper_two >= 50 and $d->$paper_three >= 50) || ($d->$paper_one >= 40 and $d->$paper_two >= 40 and $d->$paper_three >= 40)
                || ($d->$paper_one >= 0 and $d->$paper_two >= 40 and $d->$paper_three >= 40) || ($d->$paper_one >= 40 and $d->$paper_two >= 0 and $d->$paper_three >= 40)
                || ($d->$paper_one >= 40 and $d->$paper_two >= 40 and $d->$paper_three >= 0) || ($d->$paper_one >= 50 and $d->$paper_two >= 0 and $d->$paper_three >= 50)
                || ($d->$paper_one >= 50 and $d->$paper_two >= 50 and $d->$paper_three >= 0) || ($d->$paper_one >= 0 and $d->$paper_two >= 50 and $d->$paper_three >= 50 and $d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100)
            ) {
                $grade = 1;
            } elseif (($d->$paper_one >= 0 and $d->$paper_two >= 40 and $d->$paper_three >= 40) || ($d->$paper_one >= 40 and $d->$paper_two >= 0 and $d->$paper_three >= 40)
                || ($d->$paper_one >= 40 and $d->$paper_two >= 40 and $d->$paper_three >= 0) || ($d->$paper_one >= 0 and $d->$paper_two >= 0 and $d->$paper_three >= 0 and $d->$paper_one <= 100 and $d->$paper_two <= 100 and $d->$paper_three <= 100)
            ) {
                $grade = 0;
            } else {
                $grade = 0;
            }
            array_push($points, $grade);
        }    

        //ONE PAPER GRADE
        foreach($one_subs as $one){
            $paper_one = $one;
            if (($d->$paper_one >= 50 and $d->$paper_one <= 100)) {
                $grade = 1;
            } else {
                $grade = 0;
            }
            array_push($points, $grade);
        }

        //SUBICT PAPER GRADE
        $subict = round((($d->Subict + $d->Subict2)/2),0);
        if (($subict >= 50 and $subict <= 100)) {
            $grade = 1;
            array_push($points, $grade);
        } else {
            $grade = 0;
            array_push($points, $grade);
        }

        //FOUR PAPER GRADE
        $art = $d->Art;
        $art2 = $d->Art2;
        $art3 = $d->Art3;
        $art4 = $d->Art4;

        if($art >= 75 and $art2 >= 80 and $art3 >= 80 and $art4 >= 80 ||
        $art >= 80 and $art2 >= 75 and $art3 >= 80 and $art4 >= 80 ||
        $art >= 80 and $art2 >= 80 and $art3 >= 75 and $art4 >= 80 ||
        $art >= 80 and $art2 >= 80 and $art3 >= 80 and $art4 >= 75

        ) {
        $grade = 6;
        }elseif(
        $art >= 70 and $art2 >= 75 and $art3 >= 75 and $art4 >= 75 ||
        $art >= 75 and $art2 >= 70 and $art3 >= 75 and $art4 >= 75 ||
        $art >= 75 and $art2 >= 75 and $art3 >= 70 and $art4 >= 75 ||
        $art >= 75 and $art2 >= 75 and $art3 >= 75 and $art4 >= 70

        ){
        $grade = 5;
        }elseif(
        $art >= 65 and $art2 >= 70 and $art3 >= 70 and $art4 >= 70 ||
        $art >= 70 and $art2 >= 65 and $art3 >= 70 and $art4 >= 70 ||
        $art >= 70 and $art2 >= 70 and $art3 >= 65 and $art4 >= 70 ||
        $art >= 70 and $art2 >= 70 and $art3 >= 70 and $art4 >= 65

        ){
        $grade = 4;
        }elseif(
        $art >= 60 and $art2 >= 65 and $art3 >= 65 and $art4 >= 65 ||
        $art >= 65 and $art2 >= 60 and $art3 >= 65 and $art4 >= 65 ||
        $art >= 65 and $art2 >= 65 and $art3 >= 60 and $art4 >= 65 ||
        $art >= 65 and $art2 >= 65 and $art3 >= 65 and $art4 >= 60

        ){
        $grade = 3;
        }elseif(
        $art >= 50 and $art2 >= 60 and $art3 >= 60 and $art4 >= 60 ||
        $art >= 60 and $art2 >= 50 and $art3 >= 60 and $art4 >= 60 ||
        $art >= 60 and $art2 >= 60 and $art3 >= 50 and $art4 >= 60 ||
        $art >= 60 and $art2 >= 60 and $art3 >= 60 and $art4 >= 50 ||
        $art >= 40 and $art2 >= 60 and $art3 >= 60 and $art4 >= 65 ||
        $art >= 65 and $art2 >= 60 and $art3 >= 60 and $art4 >= 40 ||
        $art >= 60 and $art2 >= 65 and $art3 >= 40 and $art4 >= 60 ||
        $art >= 60 and $art2 >= 40 and $art3 >= 65 and $art4 >= 60 ||
        $art >= 60 and $art2 >= 60 and $art3 >= 40 and $art4 >= 65 

        ){
        $grade = 2;
        }elseif(
        $art >= 50 and $art2 >= 50 and $art3 >= 50 and $art4 >= 50 ||
        $art >= 40 and $art2 >= 40 and $art3 >= 40 and $art4 >= 40 ||
        $art >= 0 and $art2 >= 40 and $art3 >= 40 and $art4 >= 40 ||
        $art >= 40 and $art2 >= 0 and $art3 >= 40 and $art4 >= 40 ||
        $art >= 40 and $art2 >= 40 and $art3 >= 0 and $art4 >= 40 ||
        $art >= 40 and $art2 >= 40 and $art3 >= 40 and $art4 >= 0 ||
        $art >= 0 and $art2 >= 0 and $art3 >= 50 and $art4 >= 50 ||
        $art >= 50 and $art2 >= 50 and $art3 >= 0 and $art4 >= 0 ||
        $art >= 50 and $art2 >= 0 and $art3 >= 0 and $art4 >= 50 ||
        $art >= 0 and $art2 >= 50 and $art3 >= 0 and $art4 >= 50 ||
        $art >= 0 and $art2 >= 50 and $art3 >= 50 and $art4 >= 0 

        ){
        $grade = 1;
        }elseif(
        $art >= 0 and $art2 >= 0 and $art3 >= 40 and $art4 >= 40 ||
        $art >= 40 and $art2 >= 40 and $art3 >= 0 and $art4 >= 0 ||
        $art >= 40 and $art2 >= 0 and $art3 >= 0 and $art4 >= 40 ||
        $art >= 0 and $art2 >= 40 and $art3 >= 0 and $art4 >= 40 ||
        $art >= 0 and $art2 >= 40 and $art3 >= 40 and $art4 >= 0 || 
        $art >= 0 and $art2 >= 0 and $art3 >= 0 and $art4 >= 0

        ){
        $grade = 0;
        }

        array_push($points, $grade);

        $total = array_sum($points);

$html .=                    '<tr>
                                <td>'.$d->stdID.'</td>
                                <td>'.$d->stdFName.' '.$d->stdLName.'</td>
                                <td>'.$d->combination.'</td>
                                <td>'.$d->class.'</td>';
                            foreach($subs_merged as $s){
$html .=                    '<td>'.$d->$s.'</td>';                               
                            }
                            if($total != 0){
$html .='                       <td>'.$total.'</td>';
                            }else{
$html .=                        '<td style="color:red;">'.$total.'</td>'; 
                            }         
$html .='                   </tr>';
                                $points = [];
                            }
$html .='                  </tbody>
                        </table>
                        <h6>Printed on: '.date('D, d M, Y : h:i:s',strtotime(now())).'</h6>
                    </div>
                </div>
            </div>
        </body>
        </html>    
        ';

        $pdf = PDF::loadHTML($html)->setPaper('A4','landscape');
        return $pdf->stream(''.$class.'_Marksheet');
    }
    
    public function olevel_marksheet(Request $req){
        $result_set  = DB::select('select *  from activities');
        return view('marksheet.olevel', compact('result_set'));
    }

    public function get_olevel_marksheet(Request $req){
        $results_table = $req->result;
        $class = $req->classname;

        return response()->json([
            'result' => $results_table,
            'class' => $class
        ]);
    }

    public function o_level_marksheet_dt(Request $req){
        $results_table = $req->result;
        $class = $req->classname;

        $data = DB::select("
        SELECT 
            students.stdID,
            ".$results_table.".stdID,
            stdFName,stdLName,students.class,level,
            stdHouse,section, stdImage, year, status,
            Initialfees, requirements, registration,Mathematics,History,Luganda,CRE,Agriculture,Physics,Chemistry,Biology,
            Geography,Entrepreneurship,English,ICT,Art,Kiswahili,

            (Mathematics+History+Luganda+CRE+Agriculture+Physics+Chemistry+Biology+Geography+Entrepreneurship+English+ICT+Art+Kiswahili)
        AS
            total,
            ROW_NUMBER() OVER (ORDER BY total desc) as row_num
        FROM 
            students,".$results_table."
        WHERE
        ".$results_table.".stdID = students.stdID AND students.class = '" . $class . "';
        ");

        return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('resultset',function($fetch){
                    
                })
                ->addColumn('identifier',function($fetch){
                    $class = $fetch->class;
                    $total = $fetch->total;
                    if($class == 'senior1' || $class == 'senior2' || $class == 'Senior1' || $class == 'Senior2'){
                        $ident = round(($total/12),1);
                    }elseif($class == 'senior3' || $class == 'senior4' || $class == 'Senior4' || $class == 'Senior3'){
                        $ident = round(($total/9),1);
                    }else{
                        $ident = '';
                    }

                    return $ident;
                    
                })
                ->addColumn('comment',function(){
                    
                })
                ->addColumn('position',function($fetch){
                    return $fetch->row_num;
                })
                ->make(true);
    }

    public function print_olevel_marksheet($class, $result_set){
        info("Class = ".$class.", REsult_set = ".$result_set);

        $head = [
            "Mathematics",
            "History",
            "Luganda",
            "CRE",
            "Agriculture",
            "Physics",
            "Chemistry",
            "Biology",
            "Geography",
            "Entr",
            "English",
            "ICT",
            "Art",
            "Kiswahili",
        ];

        $subjects = [
            "Mathematics",
            "History",
            "Luganda",
            "CRE",
            "Agriculture",
            "Physics",
            "Chemistry",
            "Biology",
            "Geography",
            "Entrepreneurship",
            "English",
            "ICT",
            "Art",
            "Kiswahili",
        ];

        $data = DB::select("
        SELECT 
            students.stdID,
            ".$result_set.".stdID,
            stdFName,stdLName,students.class,level,
            stdHouse,section, stdImage, year, status,
            Initialfees, requirements, registration,Mathematics,History,Luganda,CRE,Agriculture,Physics,Chemistry,Biology,
            Geography,Entrepreneurship,English,ICT,Art,Kiswahili,

            (Mathematics+History+Luganda+CRE+Agriculture+Physics+Chemistry+Biology+Geography+Entrepreneurship+English+ICT+Art+Kiswahili)
        AS
            total,
            ROW_NUMBER() OVER (ORDER BY total desc) as row_num
        FROM 
            students,".$result_set."
        WHERE
        ".$result_set.".stdID = students.stdID AND students.class = '" . $class . "';
        ");

        $html = '
        <html>
            <head>
                <style>
                    table{
                        border-collapse:collapse;
                    }
                    th,td{
                        border:black 1px solid;
                        padding:5px;
                        font-size:11px;
                    }
                    .table-container{
                        margin-top:-30px;
                        margin-left:-30px;
                    }
                    .title{
                        padding:0;
                        margin-bottom:5px;
                        margin-top:0px;
                        width:100%;
                        text-align:center;
                        text-transform:uppercase;
                    }
                    .school{
                        margin-bottom:5px;
                        margin-top:0px;
                        text-align:center;
                        font-size:;
                    }
                    td{
                        text-align:center;
                    }
                <style>
            </head>

            <body>
                <div class="table-container">
                <h3 class="school">CORNERSTONE HIGH SCHOOL NANGABO</h3>
                <h4 class="title">'.$class.' Marksheet '.$result_set.'</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class</th>';

    foreach($head as $h){
    $html .='
                    <th>'.$h.'</th>
    ';
    }

    $html .='
                    <th>Idemtifier</th>
                    <th>Comment</th>
                    <th>Position</th>
                           </tr>
                        </thead>
                        <tbody>';
    foreach($data as $d){
    $html .= '
                <tr>
                    <td>'.$d->stdID.'</td>
                    <td>'.$d->stdFName.' '.$d->stdLName.'</td>
                    <td>'.$d->class.'</td>';
            foreach($subjects as $h){
                
    $html .= '     <td>'.$d->$h.'</td>';

            }
    if(($d->class) == 'senior1' || ($d->class) == 'Senior1' || ($d->class) == 'senior2' || ($d->class) == 'Senior2'){
        $ident = round($d->total/12,1);
        $html .= '   <td>'.$ident.'</td>';
    }elseif(($d->class) == 'senior3' || ($d->class) == 'Senior3' || ($d->class) == 'senior4' || ($d->class) == 'Senior4'){
        $ident = round($d->total/9,1);
        $html .= '   <td>'.$ident.'</td>';
    }

    if($ident >= 2.5 and $ident <= 3.0){
        $html .= '   <td>OUTSTANDING</td>';
    }elseif($ident >= 1.5 and $ident <= 2.4){
        $html .= '   <td>MODERATE</td>';
    }elseif($ident >= 0.1 and $ident <= 1.4){
        $html .= '   <td>BASIC</td>';
    }else{
        $html .= '   <td>No LOs</td>';
    }
    $html .= '
                    <td>'.$d->row_num.'</td>
                    </tr> ';

    }

    $html .= '
                        </tbody>
                    </table>

                    <p>
                    <h5 style="font-size:20px; margin-bottom:5px;">Grading</h5>
                        <span>2.5 - 3.0 : <b>OUTSTANDING</b></span> <br>
                        <span>1.5 - 2.4 : <b>MODERATE</b></span> <br>
                        <span>0.1 - 1.4 : <b>BASIC</b></span> <br>
                        <span>0 : No LOs (No Learner Outcomes)</span> <br>
                    </p>
            </body>
        </html>
                ';

        $pdf = PDF::loadHTML($html)->setPaper('A4','landscape');
        return $pdf->stream(''.$class.'_Marksheet');
    }
}
