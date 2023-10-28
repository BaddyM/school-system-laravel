<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use Log;
use DB;

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
}
