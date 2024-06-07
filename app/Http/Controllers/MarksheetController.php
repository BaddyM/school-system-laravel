<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MarksheetController extends Controller
{
    //Declare global variable in the class
    var $points = [];

    public function alevel()
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $classes = DB::select("
            SELECT
                *
            FROM
                std_class
            WHERE
                level = 'A Level'
        ");

        $subjects = DB::select("
            SELECT
                name,paper
            FROM
                subjects
            WHERE
                level = 'A Level'
        ");

        $results = DB::select("
            SELECT
                *
            FROM
                results_table
            WHERE
                level = 'A Level'
            AND
                term = " . $term . "
            AND
                year = " . $year . "
        ");


        return view('marksheet.alevel', compact('classes', 'subjects', 'results'));
    }

    public function olevel_marksheet()
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $classes = DB::select("
            SELECT
                *
            FROM
                std_class
            WHERE
                level = 'O Level'
        ");

        $subjects = DB::select("
            SELECT
                DISTINCT name
            FROM
                subjects
            WHERE
                level = 'O Level'
        ");

        $results = DB::select("
            SELECT
                *
            FROM
                results_table
            WHERE
                level = 'O Level'
            AND
                term = " . $term . "
            AND
                year = " . $year . "
        ");

        return view('marksheet.olevel', compact('classes', 'subjects', 'results'));
    }

    public function marksheet(Request $request)
    {
        $class = $request->classname;
        $table = $request->tablename;
        $level = $request->level;

        $subjects = DB::select("
            SELECT
                DISTINCT name
            FROM
                subjects
            WHERE
                level = '" . $level . "'
        ");

        //Deal with the subjects here
        if ($level == 'O Level') {
            //O Level Subjects
            $subjects_array = array();
            $subjects_array2 = array();
            $subjects = DB::table('subjects')->distinct()->select('name')->where('level', $level)->get();
            foreach ($subjects as $subject) {
                array_push($subjects_array, ($subject->name) . "_1");
                array_push($subjects_array2, "IFNULL(" . ($subject->name) . "_1,0)");
            }
            $subjects_list = implode(' , ', $subjects_array);
            $subjects_list2 = implode(',', $subjects_array2);
            $subjects_list_sum = str_replace('),', ')+', $subjects_list2);
        } else {
            //A Level Subjects
            $subjects_array = array();
            $subjects_array2 = array();
            $subjects = DB::table('subjects')->distinct()->select(['name', 'paper'])->where('level', $level)->get();
            foreach ($subjects as $subject) {
                array_push($subjects_array, ($subject->name) . "_" . $subject->paper);
                array_push($subjects_array2, "IFNULL(" . ($subject->name) . "_" . $subject->paper . ",0)");
            }
            $subjects_list = implode(' , ', $subjects_array);
            $subjects_list2 = implode(',', $subjects_array2);
            $subjects_list_sum = str_replace('),', ')+', $subjects_list2);
        }

        //Student data here
        $data = DB::select("
                SELECT
                    concat_ws(' ',student.lname, IF(student.mname = 'NULL','',student.mname), student.fname) as name,
                    student.class,
                    " . $subjects_list . ",
                    (" . $subjects_list_sum . ")as total                    
                FROM
                    student
                RIGHT OUTER JOIN
                    " . $table . "
                ON
                    student.std_id = " . $table . ".std_id
                WHERE
                    " . $table . ".class = '" . $class . "'
                AND
                    status = 'continuing'
                ORDER BY
                    total DESC
            ");

        /*------------------------------------- CALCULATE STUDENT POINTS --------------------------------*/
        if ($level == 'A Level') {
            $subject_points = DB::select("
                            SELECT 
                                DISTINCT name,
                                GROUP_CONCAT(paper) as papers,
                                GROUP_CONCAT(paper) as paper_group,
                                GROUP_CONCAT(DISTINCT level) 
                            FROM
                                subjects
                            WHERE
                                level = 'A Level'
                            GROUP BY
                                name
                        ");
        
            //info($subject_points);

            //Replace papers with paper_number
            foreach ($subject_points as $point) {
                $point->papers = strlen(str_replace(',', '', $point->papers));
            }

            foreach ($data as $d) {
                $points_collector = 0;
                foreach ($subject_points  as $subject_p) {
                    $paper_name = $subject_p->name;
                    //Two Paper grade
                    if ($subject_p->papers == 2 && $subject_p->name != 'SubICT') {
                        $two_papers = explode(',', $subject_p->paper_group);
                        $p1 = $paper_name . "_" . $two_papers[0];
                        $p2 = $paper_name . "_" . $two_papers[1];

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null) {
                            $grade = $this->two_papers($d->$p1, $d->$p2);
                            $points_collector += $grade;
                        }
                    } elseif ($subject_p->papers == 3) {
                        //Three paper grade
                        $three_papers = explode(',', $subject_p->paper_group);
                        $p1 = $paper_name . "_" . $three_papers[0];
                        $p2 = $paper_name . "_" . $three_papers[1];
                        $p3 = $paper_name . "_" . $three_papers[2];

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null || $d->$p3 != null) {
                            $grade = $this->three_papers($d->$p1, $d->$p2, $d->$p3);
                            $points_collector += $grade;
                        }
                    } elseif ($subject_p->papers == 4) {
                        //Four paper grade
                        $four_papers = explode(',', $subject_p->paper_group);

                        $p1 = $paper_name . "_" . $four_papers[0];
                        $p2 = $paper_name . "_" . $four_papers[1];
                        $p3 = $paper_name . "_" . $four_papers[2];
                        $p4 = $paper_name . "_" . $four_papers[3];

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null || $d->$p3 != null || $d->$p4 != null) {
                            $grade = $this->four_papers($d->$p1, $d->$p2, $d->$p3, $d->$p4);
                            $points_collector += $grade;
                        }
                    } elseif ($subject_p->name == 'SubICT') {
                        //SubICT paper grade
                        $p1 = $paper_name . "_1";
                        $p2 = $paper_name . "_2";

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null) {
                            $grade = $this->sub_ict($d->$p1, $d->$p2);
                            $points_collector += $grade;
                        }
                    } else {
                        //One paper grade
                        $p1 = $paper_name . "_1";

                        //Check nullability
                        if ($d->$p1 != null) {
                            $grade = $this->one_paper($d->$p1);
                            $points_collector += $grade;
                        }
                    }
                }

                $d->total = $points_collector;
            }
        }

        //Remove Extra space from the name
        foreach ($data as $d) {
            $d->name = str_replace("  ", " ", $d->name);
        }

        //info($data);

        if (count($data) > 0) {
            $response = $data;
        } else {
            $response = 'empty';
        }

        return response($response);
    }

    public function print_marksheet($class, $table, $level){
        $subjects = DB::select("
            SELECT
                DISTINCT name
            FROM
                subjects
            WHERE
                level = '" . $level . "'
        ");

        //info($subjects);

        //Deal with the subjects here
        if ($level == 'O Level') {
            //O Level Subjects
            $subjects_array = array();
            $subjects_array2 = array();
            $subjects = DB::table('subjects')->distinct()->select('name')->where('level', $level)->get();
            foreach ($subjects as $subject) {
                array_push($subjects_array, ($subject->name) . "_1");
                array_push($subjects_array2, "IFNULL(" . ($subject->name) . "_1,0)");
            }
            $subjects_list = implode(' , ', $subjects_array);
            $subjects_list2 = implode(',', $subjects_array2);
            $subjects_list_sum = str_replace('),', ')+', $subjects_list2);
        } else {
            //A Level Subjects
            $subjects_array = array();
            $subjects_array2 = array();
            $subjects = DB::table('subjects')->select(['name', 'paper'])->where('level', $level)->get();
            foreach ($subjects as $subject) {
                array_push($subjects_array, ($subject->name) . "_" . $subject->paper . "");
                array_push($subjects_array2, "IFNULL(" . ($subject->name) . "_" . $subject->paper . ",0)");
            }
            $subjects_list = implode(' , ', $subjects_array);
            $subjects_list2 = implode(',', $subjects_array2);
            $subjects_list_sum = str_replace('),', ')+', $subjects_list2);
        }

        //Student data here
        $data = DB::select("
                SELECT
                    concat_ws(' ',student.lname, student.mname, student.fname) as name,
                    student.class,
                    " . $subjects_list . ",
                    (" . $subjects_list_sum . ")as total                    
                FROM
                    student
                RIGHT OUTER JOIN
                    " . $table . "
                ON
                    student.std_id = " . $table . ".std_id
                WHERE
                    " . $table . ".class = '" . $class . "'
                AND
                    status = 'continuing'
                ORDER BY
                    total DESC
            ");

        /*------------------------------------- CALCULATE STUDENT POINTS --------------------------------*/
        if ($level == 'A Level') {
            $subject_points = DB::select("
                            SELECT 
                                DISTINCT name,
                                GROUP_CONCAT(paper) as papers,
                                GROUP_CONCAT(paper) as paper_group,
                                GROUP_CONCAT(DISTINCT level) 
                            FROM
                                subjects
                            WHERE
                                level = 'A Level'
                            GROUP BY
                                name
                        ");

            //Replace papers with paper_number
            foreach ($subject_points as $point) {
                $point->papers = strlen(str_replace(',', '', $point->papers));
            }

            foreach ($data as $d) {
                $points_collector = 0;
                foreach ($subject_points  as $subject_p) {
                    $paper_name = $subject_p->name;
                    //Two Paper grade
                    if ($subject_p->papers == 2 && $subject_p->name != 'SubICT') {
                        $two_papers = explode(',', $subject_p->paper_group);
                        $p1 = $paper_name . "_" . $two_papers[0];
                        $p2 = $paper_name . "_" . $two_papers[1];

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null) {
                            $grade = $this->two_papers($d->$p1, $d->$p2);
                            $points_collector += $grade;
                        }
                    } elseif ($subject_p->papers == 3) {
                        //Three paper grade
                        $three_papers = explode(',', $subject_p->paper_group);
                        $p1 = $paper_name . "_" . $three_papers[0];
                        $p2 = $paper_name . "_" . $three_papers[1];
                        $p3 = $paper_name . "_" . $three_papers[2];

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null || $d->$p3 != null) {
                            $grade = $this->three_papers($d->$p1, $d->$p2, $d->$p3);
                            $points_collector += $grade;
                        }
                    } elseif ($subject_p->papers == 4) {
                        //Four paper grade
                        $four_papers = explode(',', $subject_p->paper_group);

                        $p1 = $paper_name . "_" . $four_papers[0];
                        $p2 = $paper_name . "_" . $four_papers[1];
                        $p3 = $paper_name . "_" . $four_papers[2];
                        $p4 = $paper_name . "_" . $four_papers[3];

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null || $d->$p3 != null || $d->$p4 != null) {
                            $grade = $this->four_papers($d->$p1, $d->$p2, $d->$p3, $d->$p4);
                            $points_collector += $grade;
                        }

                    } elseif ($subject_p->name == 'SubICT') {
                        //SubICT paper grade
                        $p1 = $paper_name . "_1";
                        $p2 = $paper_name . "_2";

                        //Check nullability
                        if ($d->$p1 != null || $d->$p2 != null) {
                            $grade = $this->sub_ict($d->$p1, $d->$p2);
                            $points_collector += $grade;
                        }
                    } else {
                        //One paper grade
                        $p1 = $paper_name . "_1";

                        //Check nullability
                        if ($d->$p1 != null) {
                            $grade = $this->one_paper($d->$p1);
                            $points_collector += $grade;
                        }
                    }
                }

                $d->total = $points_collector;
            }
        } else {
            foreach ($data as $d) {
                if ($d->class == 'Senior 1'){
                    $d->total = round((($d->total) / 14), 1);
                }elseif($d->class == 'Senior 2') {
                    $d->total = round((($d->total) / 11), 1);
                } else {
                    $d->total = round((($d->total) / 9), 1);
                }
            }
        }

        //info($data);

        $html = '
                    <style>
                        thead tr{
                            background:black;
                        }
                        th{
                            color:white;
                        }
                        .container{
                            margin-left:-1cm;
                            margin-right:-1cm;
                            margin-top:-1cm;
                        }
                        .title{
                            text-align:center;
                            font-weight:bold;
                            font-size:20px;
                            text-transform:uppercase;
                            margin:5px;
                        }
                        table{
                            border-collapse: collapse;
                        }
                        th,td{
                            font-size:10px;
                            border:1px black solid;
                            padding:5px 2px 5px 2px;
                            text-align:center;
                        }
                    </style>

        <div class="container">
            <p class="title">
                ' . $class . ' Marksheet
            </p>
            
            <table style="width:100%;" class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Class</th>';

        foreach ($subjects as $subjects) {
            $html .= '       <th scope="col">' .(($level == 'A Level')?substr(($subjects->name),0,3):$subjects->name). ' ' . (($level == 'A Level') ? $subjects->paper : '') . '</th>';
        }

        $html .=     '<th scope="col">' . (($level == 'O Level') ? 'Identifier' : 'Points') . '</th>
                        <th scope="col">Position</th>
                    </tr>
                </thead>
                <tbody>';

        $position = 0;
        foreach ($data as $l1) {
            $html .=   '<tr>';
            foreach ($l1 as $l2) {
                $html .=    '<td>' . $l2 . '</td>';
            }
            $html .=   '<td>' . ($position += 1) . '</td>';
            $html .=  '</tr>';
        }

        $html  .= '  </tbody>
            </table>
        </div>
        ';

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'landscape');
        return $pdf->stream('' . $class . '_Marksheet');
    }

    function two_papers($p1, $p2) {
        if (
            ($p1 >= 80 and $p2 >= 80) and 
            ($p1 <= 100 and $p2 <= 100)
            ) {
            $grade = 'A';
            $points = 6;
        } elseif (
            ($p1 >= 80 and $p2 >= 75) || 
            ($p1 >= 75 and $p2 >= 80)  and 
            ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'B';
            $points = 5;
        } elseif (
            ($p1 >= 70 and $p2 >= 75) || 
            ($p1 >= 75 and $p2 >= 70)  and 
            ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'C';
            $points = 4;
        } elseif (
            ($p1 >= 65 and $p2 >= 70) || 
            ($p1 >= 70 and $p2 >= 65) ||
            ($p1 >= 65 and $p2 >= 65)  and 
            ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'D';
            $points = 3;
        } elseif (
            ($p1 >= 60 and $p2 >= 65) || 
            ($p1 >= 65 and $p2 >= 60) ||
            ($p1 >= 60 and $p2 >= 60) || 
            ($p1 >= 50 and $p2 >= 55) || 
            ($p1 >= 55 and $p2 >= 50) ||
            ($p1 >= 45 and $p2 >= 55) || 
            ($p1 >= 55 and $p2 >= 45)  and 
            ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'E';
            $points = 2;
        } elseif (
            ($p1 >= 50 and $p2 >= 40) || 
            ($p1 >= 40 and $p2 >= 50) ||
            ($p1 >= 50 and $p2 >= 60) || 
            ($p1 >= 60 and $p2 >= 50) || 
            ($p1 >= 65 and $p2 >= 0) ||
            ($p1 >= 0 and $p2 >= 65) || 
            ($p1 >= 60 and $p2 >= 0) || 
            ($p1 >= 0 and $p2 >= 60) ||
            ($p1 >= 50 and $p2 >= 50) || 
            ($p1 >= 50 and $p2 >= 0) || 
            ($p1 >= 0 and $p2 >= 50) ||
            ($p1 >= 40 and $p2 >= 40) and 
            ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'O';
            $points = 1;
        } elseif (($p1 >= 40 and $p2 >= 0) || ($p1 >= 0 and $p2 >= 40) || ($p1 >= 0 and $p2 >= 0)
            and ($p1 <= 49 and $p2 <= 49)
        ) {
            $grade = 'F';
            $points = 0;
        } else {
            $grade = 'F';
            $points = 0;
        }

        return $points;
    }

    function three_papers($p1, $p2, $p3)
    {
        if (($p1 >= 80 and $p2 >= 80 and $p3 >= 75) || 
            ($p1 >= 75 and $p2 >= 80 and $p3 >= 80) || 
            ($p1 >= 80 and $p2 >= 75 and $p3 >= 80) and 
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
        ) {
            $grade = 'A';
            $points = 6;
        } elseif (
            ($p1 >= 75 and $p2 >= 75 and $p3 >= 70) || 
            ($p1 >= 70 and $p2 >= 75 and $p3 >= 75) ||
            ($p1 >= 75 and $p2 >= 70 and $p3 >= 75) and 
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
            ) {
            $grade = 'B';
            $points = 5;
        } elseif (
            ($p1 >= 70 and $p2 >= 70 and $p3 >= 65) || 
            ($p1 >= 65 and $p2 >= 70 and $p3 >= 70) || 
            ($p1 >= 70 and $p2 >= 65 and $p3 >= 70) and 
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
            ) {
            $grade = 'C';
            $points = 4;
        }elseif (
            ($p1 >= 65 and $p2 >= 65 and $p3 >= 60) || 
            ($p1 >= 60 and $p2 >= 65 and $p3 >= 65) || 
            ($p1 >= 65 and $p2 >= 60 and $p3 >= 65) and 
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
            ) {
            $grade = 'D';
            $points = 3;
        } elseif (
            ($p1 >= 50 and $p2 >= 60 and $p3 >= 60) ||
            ($p1 >= 60 and $p2 >= 50 and $p3 >= 60) || 
            ($p1 >= 60 and $p2 >= 60 and $p3 >= 50) ||
            ($p1 >= 40 and $p2 >= 60 and $p3 >= 65) || 
            ($p1 >= 65 and $p2 >= 40 and $p3 >= 60) ||
            ($p1 >= 60 and $p2 >= 65 and $p3 >= 40) and 
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
            ) {
            $grade = 'E';
            $points = 2;
        } elseif (
            ($p1 >= 50 and $p2 >= 50 and $p3 >= 50) || 
            ($p1 >= 40 and $p2 >= 40 and $p3 >= 40) ||
            ($p1 >= 0 and $p2 >= 40 and $p3 >= 40) || 
            ($p1 >= 40 and $p2 >= 0 and $p3 >= 40) ||
            ($p1 >= 40 and $p2 >= 40 and $p3 >= 0) || 
            ($p1 >= 50 and $p2 >= 0 and $p3 >= 0) ||
            ($p1 >= 0 and $p2 >= 50 and $p3 >= 0) || 
            ($p1 >= 0 and $p2 >= 0 and $p3 >= 50) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
            ) {
            $grade = 'O';
            $points = 1;
        } elseif (
            ($p1 >= 0 and $p2 >= 0 and $p3 >= 40) || 
            ($p1 >= 40 and $p2 >= 0 and $p3 >= 0) ||
            ($p1 >= 0 and $p2 >= 40 and $p3 >= 0) || 
            ($p1 >= 0 and $p2 >= 0 and $p3 >= 0) and 
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100)
        ) {
            $grade = 'F';
            $points = 0;
        } else {
            $grade = 'F';
            $points = 0;
        }

        return $points;
    }

    function four_papers($p1, $p2, $p3, $p4)
    {
        if (
            ($p1 >= 75 and $p2 >= 80 and $p3 >= 80 and $p4 >= 80) ||
            ($p1 >= 80 and $p2 >= 75 and $p3 >= 80 and $p4 >= 80) ||
            ($p1 >= 80 and $p2 >= 80 and $p3 >= 75 and $p4 >= 80) ||
            ($p1 >= 80 and $p2 >= 80 and $p3 >= 80 and $p4 >= 75) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        
        ) {
            $grade = 'A';
            $points = 6;
        } elseif (
            ($p1 >= 70 and $p2 >= 75 and $p3 >= 75 and $p4 >= 75) ||
            ($p1 >= 75 and $p2 >= 70 and $p3 >= 75 and $p4 >= 75) ||
            ($p1 >= 75 and $p2 >= 75 and $p3 >= 70 and $p4 >= 75) ||
            ($p1 >= 75 and $p2 >= 75 and $p3 >= 75 and $p4 >= 70) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        
        ) {
            $grade = 'B';
            $points = 5;
        } elseif (
            ($p1 >= 65 and $p2 >= 70 and $p3 >= 70 and $p4 >= 70) ||
            ($p1 >= 70 and $p2 >= 65 and $p3 >= 70 and $p4 >= 70) ||
            ($p1 >= 70 and $p2 >= 70 and $p3 >= 65 and $p4 >= 70) ||
            ($p1 >= 70 and $p2 >= 70 and $p3 >= 70 and $p4 >= 65) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        
        ) {
            $grade = 'C';
            $points = 4;
        } elseif (
            ($p1 >= 60 and $p2 >= 65 and $p3 >= 65 and $p4 >= 65) ||
            ($p1 >= 65 and $p2 >= 60 and $p3 >= 65 and $p4 >= 65) ||
            ($p1 >= 65 and $p2 >= 65 and $p3 >= 60 and $p4 >= 65) ||
            ($p1 >= 65 and $p2 >= 65 and $p3 >= 65 and $p4 >= 60) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        
        ) {
            $grade = 'D';
            $points = 3;
        } elseif (
            ($p1 >= 50 and $p2 >= 60 and $p3 >= 60 and $p4 >= 60) ||
            ($p1 >= 60 and $p2 >= 50 and $p3 >= 60 and $p4 >= 60) ||
            ($p1 >= 60 and $p2 >= 60 and $p3 >= 50 and $p4 >= 60) ||
            ($p1 >= 60 and $p2 >= 60 and $p3 >= 60 and $p4 >= 50) ||
            ($p1 >= 40 and $p2 >= 60 and $p3 >= 60 and $p4 >= 65) ||
            ($p1 >= 65 and $p2 >= 60 and $p3 >= 60 and $p4 >= 40) ||
            ($p1 >= 60 and $p2 >= 65 and $p3 >= 40 and $p4 >= 60) ||
            ($p1 >= 60 and $p2 >= 40 and $p3 >= 65 and $p4 >= 60) ||
            ($p1 >= 60 and $p2 >= 60 and $p3 >= 40 and $p4 >= 65) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        ) {
            $grade = 'E';
            $points = 2;
        } elseif (
            ($p1 >= 50 and $p2 >= 50 and $p3 >= 50 and $p4 >= 50) ||
            ($p1 >= 40 and $p2 >= 40 and $p3 >= 40 and $p4 >= 40) ||
            ($p1 >= 0 and $p2 >= 40 and $p3 >= 40 and $p4 >= 40) ||
            ($p1 >= 40 and $p2 >= 0 and $p3 >= 40 and $p4 >= 40) ||
            ($p1 >= 40 and $p2 >= 40 and $p3 >= 0 and $p4 >= 40) ||
            ($p1 >= 40 and $p2 >= 40 and $p3 >= 40 and $p4 >= 0) ||
            ($p1 >= 0 and $p2 >= 0 and $p3 >= 50 and $p4 >= 50) ||
            ($p1 >= 50 and $p2 >= 50 and $p3 >= 0 and $p4 >= 0) ||
            ($p1 >= 50 and $p2 >= 0 and $p3 >= 0 and $p4 >= 50) ||
            ($p1 >= 0 and $p2 >= 50 and $p3 >= 0 and $p4 >= 50) ||
            ($p1 >= 0 and $p2 >= 50 and $p3 >= 50 and $p4 >= 0) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        ) {
            $grade = 'O';
            $points = 1;
        } elseif (
            ($p1 >= 0 and $p2 >= 0 and $p3 >= 40 and $p4 >= 40) ||
            ($p1 >= 40 and $p2 >= 40 and $p3 >= 0 and $p4 >= 0) ||
            ($p1 >= 40 and $p2 >= 0 and $p3 >= 0 and $p4 >= 40) ||
            ($p1 >= 0 and $p2 >= 40 and $p3 >= 0 and $p4 >= 40) ||
            ($p1 >= 0 and $p2 >= 40 and $p3 >= 40 and $p4 >= 0) ||
            ($p1 >= 0 and $p2 >= 0 and $p3 >= 0 and $p4 >= 0) and
            ($p1 <= 100 and $p2 <= 100 and $p3 <= 100 and $p4 <= 100)
        
        ) {
            $grade = 'F';
            $points = 0;
        } else {
            $grade = 'F';
            $points = 0;
        }

        return $points;
    }

    function sub_ict($p1, $p2)
    {
        $avg = (($p1 + $p2) / 2);

        if ($avg >= 50) {
            $grade = 1;
        } else {
            $grade = 0;
        }
        return $grade;
    }

    function one_paper($p1)
    {
        if ($p1 >= 50) {
            $grade = 1;
        } else {
            $grade = 0;
        }
        return $grade;
    }
}
