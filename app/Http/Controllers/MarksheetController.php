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
            //info($subjects_list2);
            //info($subjects_list_sum);
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

    public function print_marksheet($class, $table, $level)
    {
        //info("Class = " . $class . ", table = " . $table);

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
            
            <table class="table">
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

    function two_papers($p1, $p2)
    {
        if (($p1 >= 75 and $p2 >= 75) and ($p1 <= 100 and $p2 <= 100)) {
            $grade = 6;
        } elseif (($p1 >= 65 and $p2 >= 75) || ($p1 >= 75 and $p2 >= 65)
            || ($p1 >= 65 and $p2 >= 65)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 5;
        } elseif (($p1 >= 60 and $p2 >= 65) || ($p1 >= 65 and $p2 >= 60)
            || ($p1 >= 60 and $p2 >= 60)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 4;
        } elseif (($p1 >= 55 and $p2 >= 60) || ($p1 >= 60 and $p2 >= 55)
            || ($p1 >= 55 and $p2 >= 55)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 3;
        } elseif (($p1 >= 50 and $p2 >= 55) || ($p1 >= 55 and $p2 >= 50)
            || ($p1 >= 50 and $p2 >= 50) || ($p1 >= 50 and $p2 >= 55) || ($p1 >= 55 and $p2 >= 50)
            || ($p1 >= 45 and $p2 >= 55) || ($p1 >= 55 and $p2 >= 45)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 2;
        } elseif (($p1 >= 50 and $p2 >= 40) || ($p1 >= 40 and $p2 >= 50)
            || ($p1 >= 50 and $p2 >= 60) || ($p1 >= 60 and $p2 >= 50) || ($p1 >= 65 and $p2 >= 0)
            || ($p1 >= 0 and $p2 >= 65) || ($p1 >= 60 and $p2 >= 0) || ($p1 >= 0 and $p2 >= 60)
            || ($p1 >= 50 and $p2 >= 50) || ($p1 >= 50 and $p2 >= 0) || ($p1 >= 0 and $p2 >= 50)
            || ($p1 >= 40 and $p2 >= 40)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 1;
        } elseif (($p1 >= 40 and $p2 >= 0) || ($p1 >= 0 and $p2 >= 40) || ($p1 >= 0 and $p2 >= 0)
            and ($p1 <= 49 and $p2 <= 49)
        ) {
            $grade = 0;
        } else {
            $grade = 0;
        }

        return $grade;
    }

    function three_papers($p1, $p2, $p3)
    {
        if ((($p1 >= 75 and $p2 >= 75 and $p3 >= 64) || ($p1 >= 64 and $p2 >= 75 and $p3 >= 75)
            || ($p1 >= 75 and $p2 >= 64 and $p3 >= 75) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 6;
        } elseif ((($p1 >= 65 and $p2 >= 65 and $p3 >= 60) || ($p1 >= 60 and $p2 >= 65 and $p3 >= 65)
            || ($p1 >= 65 and $p2 >= 60 and $p3 >= 65) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 5;
        } elseif ((($p1 >= 60 and $p2 >= 60 and $p3 >= 55) || ($p1 >= 55 and $p2 >= 60 and $p3 >= 60)
            || ($p1 >= 60 and $p2 >= 55 and $p3 >= 60) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 4;
        } elseif ((($p1 >= 55 and $p2 >= 55 and $p3 >= 50) || ($p1 >= 50 and $p2 >= 55 and $p3 >= 55)
            || ($p1 >= 55 and $p2 >= 50 and $p3 >= 55) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 3;
        } elseif ((($p1 >= 50 and $p2 >= 50 and $p3 >= 45)
            || ($p1 >= 45 and $p2 >= 50 and $p3 >= 50) || ($p1 >= 50 and $p2 >= 45 and $p3 >= 50)
            || ($p1 >= 40 and $p2 >= 50 and $p3 >= 65) || ($p1 >= 65 and $p2 >= 40 and $p3 >= 50)
            || ($p1 >= 50 and $p2 >= 65 and $p3 >= 40) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 2;
        } elseif (($p1 >= 50 and $p2 >= 50 and $p3 >= 50) || ($p1 >= 40 and $p2 >= 40 and $p3 >= 40)
            || ($p1 >= 0 and $p2 >= 40 and $p3 >= 40) || ($p1 >= 40 and $p2 >= 0 and $p3 >= 40)
            || ($p1 >= 40 and $p2 >= 40 and $p3 >= 0) || ($p1 >= 50 and $p2 >= 0 and $p3 >= 50)
            || ($p1 >= 50 and $p2 >= 50 and $p3 >= 0) || ($p1 >= 0 and $p2 >= 50 and $p3 >= 50 and $p1 <= 100 and $p2 <= 100 and $p3 <= 100)
        ) {
            $grade = 1;
        } elseif (($p1 >= 0 and $p2 >= 40 and $p3 >= 40) || ($p1 >= 40 and $p2 >= 0 and $p3 >= 40)
            || ($p1 >= 40 and $p2 >= 40 and $p3 >= 0) || ($p1 >= 0 and $p2 >= 0 and $p3 >= 0 and $p1 <= 100 and $p2 <= 100 and $p3 <= 100)
        ) {
            $grade = 0;
        } else {
            $grade = 0;
        }

        return $grade;
    }

    function four_papers($p1, $p2, $p3, $p4)
    {
        if (
            $p1 >= 75 and $p2 >= 80 and $p3 >= 80 and $p4 >= 80 ||
            $p1 >= 80 and $p2 >= 75 and $p3 >= 80 and $p4 >= 80 ||
            $p1 >= 80 and $p2 >= 80 and $p3 >= 75 and $p4 >= 80 ||
            $p1 >= 80 and $p2 >= 80 and $p3 >= 80 and $p4 >= 75

        ) {
            $grade = 6;
        } elseif (
            $p1 >= 70 and $p2 >= 75 and $p3 >= 75 and $p4 >= 75 ||
            $p1 >= 75 and $p2 >= 70 and $p3 >= 75 and $p4 >= 75 ||
            $p1 >= 75 and $p2 >= 75 and $p3 >= 70 and $p4 >= 75 ||
            $p1 >= 75 and $p2 >= 75 and $p3 >= 75 and $p4 >= 70

        ) {
            $grade = 5;
        } elseif (
            $p1 >= 65 and $p2 >= 70 and $p3 >= 70 and $p4 >= 70 ||
            $p1 >= 70 and $p2 >= 65 and $p3 >= 70 and $p4 >= 70 ||
            $p1 >= 70 and $p2 >= 70 and $p3 >= 65 and $p4 >= 70 ||
            $p1 >= 70 and $p2 >= 70 and $p3 >= 70 and $p4 >= 65

        ) {
            $grade = 4;
        } elseif (
            $p1 >= 60 and $p2 >= 65 and $p3 >= 65 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 60 and $p3 >= 65 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 65 and $p3 >= 60 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 65 and $p3 >= 65 and $p4 >= 60

        ) {
            $grade = 3;
        } elseif (
            $p1 >= 50 and $p2 >= 60 and $p3 >= 60 and $p4 >= 60 ||
            $p1 >= 60 and $p2 >= 50 and $p3 >= 60 and $p4 >= 60 ||
            $p1 >= 60 and $p2 >= 60 and $p3 >= 50 and $p4 >= 60 ||
            $p1 >= 60 and $p2 >= 60 and $p3 >= 60 and $p4 >= 50 ||
            $p1 >= 40 and $p2 >= 60 and $p3 >= 60 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 60 and $p3 >= 60 and $p4 >= 40 ||
            $p1 >= 60 and $p2 >= 65 and $p3 >= 40 and $p4 >= 60 ||
            $p1 >= 60 and $p2 >= 40 and $p3 >= 65 and $p4 >= 60 ||
            $p1 >= 60 and $p2 >= 60 and $p3 >= 40 and $p4 >= 65

        ) {
            $grade = 2;
        } elseif (
            $p1 >= 50 and $p2 >= 50 and $p3 >= 50 and $p4 >= 50 ||
            $p1 >= 40 and $p2 >= 40 and $p3 >= 40 and $p4 >= 40 ||
            $p1 >= 0 and $p2 >= 40 and $p3 >= 40 and $p4 >= 40 ||
            $p1 >= 40 and $p2 >= 0 and $p3 >= 40 and $p4 >= 40 ||
            $p1 >= 40 and $p2 >= 40 and $p3 >= 0 and $p4 >= 40 ||
            $p1 >= 40 and $p2 >= 40 and $p3 >= 40 and $p4 >= 0 ||
            $p1 >= 0 and $p2 >= 0 and $p3 >= 50 and $p4 >= 50 ||
            $p1 >= 50 and $p2 >= 50 and $p3 >= 0 and $p4 >= 0 ||
            $p1 >= 50 and $p2 >= 0 and $p3 >= 0 and $p4 >= 50 ||
            $p1 >= 0 and $p2 >= 50 and $p3 >= 0 and $p4 >= 50 ||
            $p1 >= 0 and $p2 >= 50 and $p3 >= 50 and $p4 >= 0

        ) {
            $grade = 1;
        } elseif (
            $p1 >= 0 and $p2 >= 0 and $p3 >= 40 and $p4 >= 40 ||
            $p1 >= 40 and $p2 >= 40 and $p3 >= 0 and $p4 >= 0 ||
            $p1 >= 40 and $p2 >= 0 and $p3 >= 0 and $p4 >= 40 ||
            $p1 >= 0 and $p2 >= 40 and $p3 >= 0 and $p4 >= 40 ||
            $p1 >= 0 and $p2 >= 40 and $p3 >= 40 and $p4 >= 0 ||
            $p1 >= 0 and $p2 >= 0 and $p3 >= 0 and $p4 >= 0

        ) {
            $grade = 0;
        }else{
            $grade = 0;
        }

        return $grade;
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





    /*------------------------------ OLD CODE ---------------------------------*/
    public function fetchdata(Request $request)
    {
        $class = $request->classname;
        $result = $request->result;

        $data = DB::select('
                select * from
                students
                inner join
                ' . $result . '
                where
                students.stdID = ' . $result . '.stdID
                and class="' . $class . '"
                and students.status = "continuing"
            ');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('result_set', function () {
            })
            ->addColumn('points', function ($fetch) {
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
                foreach ($two_subs as $two) {
                    $paper_one = $two;
                    $paper_two = $two . '2';

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
                foreach ($three_subs as $three) {
                    $paper_one = $three;
                    $paper_two = $three . '2';
                    $paper_three = $three . '3';

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
                foreach ($one_subs as $one) {
                    $paper_one = $one;
                    if (($fetch->$paper_one >= 50 and $fetch->$paper_one <= 100)) {
                        $grade = 1;
                    } else {
                        $grade = 0;
                    }
                    array_push($this->points, $grade);
                }

                //SubICT paper grade
                $subict = round((($fetch->Subict + $fetch->Subict2) / 2), 0);
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

                if (
                    $art >= 75 and $art2 >= 80 and $art3 >= 80 and $art4 >= 80 ||
                    $art >= 80 and $art2 >= 75 and $art3 >= 80 and $art4 >= 80 ||
                    $art >= 80 and $art2 >= 80 and $art3 >= 75 and $art4 >= 80 ||
                    $art >= 80 and $art2 >= 80 and $art3 >= 80 and $art4 >= 75

                ) {
                    $grade = 6;
                } elseif (
                    $art >= 70 and $art2 >= 75 and $art3 >= 75 and $art4 >= 75 ||
                    $art >= 75 and $art2 >= 70 and $art3 >= 75 and $art4 >= 75 ||
                    $art >= 75 and $art2 >= 75 and $art3 >= 70 and $art4 >= 75 ||
                    $art >= 75 and $art2 >= 75 and $art3 >= 75 and $art4 >= 70

                ) {
                    $grade = 5;
                } elseif (
                    $art >= 65 and $art2 >= 70 and $art3 >= 70 and $art4 >= 70 ||
                    $art >= 70 and $art2 >= 65 and $art3 >= 70 and $art4 >= 70 ||
                    $art >= 70 and $art2 >= 70 and $art3 >= 65 and $art4 >= 70 ||
                    $art >= 70 and $art2 >= 70 and $art3 >= 70 and $art4 >= 65

                ) {
                    $grade = 4;
                } elseif (
                    $art >= 60 and $art2 >= 65 and $art3 >= 65 and $art4 >= 65 ||
                    $art >= 65 and $art2 >= 60 and $art3 >= 65 and $art4 >= 65 ||
                    $art >= 65 and $art2 >= 65 and $art3 >= 60 and $art4 >= 65 ||
                    $art >= 65 and $art2 >= 65 and $art3 >= 65 and $art4 >= 60

                ) {
                    $grade = 3;
                } elseif (
                    $art >= 50 and $art2 >= 60 and $art3 >= 60 and $art4 >= 60 ||
                    $art >= 60 and $art2 >= 50 and $art3 >= 60 and $art4 >= 60 ||
                    $art >= 60 and $art2 >= 60 and $art3 >= 50 and $art4 >= 60 ||
                    $art >= 60 and $art2 >= 60 and $art3 >= 60 and $art4 >= 50 ||
                    $art >= 40 and $art2 >= 60 and $art3 >= 60 and $art4 >= 65 ||
                    $art >= 65 and $art2 >= 60 and $art3 >= 60 and $art4 >= 40 ||
                    $art >= 60 and $art2 >= 65 and $art3 >= 40 and $art4 >= 60 ||
                    $art >= 60 and $art2 >= 40 and $art3 >= 65 and $art4 >= 60 ||
                    $art >= 60 and $art2 >= 60 and $art3 >= 40 and $art4 >= 65

                ) {
                    $grade = 2;
                } elseif (
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

                ) {
                    $grade = 1;
                } elseif (
                    $art >= 0 and $art2 >= 0 and $art3 >= 40 and $art4 >= 40 ||
                    $art >= 40 and $art2 >= 40 and $art3 >= 0 and $art4 >= 0 ||
                    $art >= 40 and $art2 >= 0 and $art3 >= 0 and $art4 >= 40 ||
                    $art >= 0 and $art2 >= 40 and $art3 >= 0 and $art4 >= 40 ||
                    $art >= 0 and $art2 >= 40 and $art3 >= 40 and $art4 >= 0 ||
                    $art >= 0 and $art2 >= 0 and $art3 >= 0 and $art4 >= 0

                ) {
                    $grade = 0;
                }

                array_push($this->points, $grade);

                return array_sum($this->points);
            })
            ->make(true);
    }

    //Print Marksheet here
    public function marksheetpdf($result_set, $classname)
    {
        $school = 'CORNERSTONE HIGH SCHOOL NANGABO';
        $result = $result_set;
        $class = $classname;

        $data = DB::select('
        select * from
        students
        inner join
        ' . $result . '
        where
        students.stdID = ' . $result . '.stdID
        and class="' . $class . '"
        and status = "continuing"
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
            <title>' . $class . ' - Marksheet</title>
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
                <h3 class="text-center text-uppercase schoolname">' . $school . '</h3>
                <h4 class="text-center fw-bold" style="text-align:center; margin:5px; text-transform:uppercase;">' . $class . ' - MARKSHEET</h4>

                <div class="card marksheet">
                    <div class="card-body overflow-scroll">
                        <table id="alevel" class="">
                            <thead style="" class="text-white">
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Comb</th>
                                <th scope="col">Class</th>';
        foreach ($merged as $m) {
            $html .= '                                    <th class="text-uppercase" scope="col">' . $m . '</th>';
        }
        $html .= '                                <th>Points</th>
                            </thead>
                            <tbody class="table-light">';
        foreach ($data as $d) {
            //TWO PAPER GRADE
            foreach ($two_subs as $two) {
                $paper_one = $two;
                $paper_two = $two . '2';

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
            foreach ($three_subs as $three) {
                $paper_one = $three;
                $paper_two = $three . '2';
                $paper_three = $three . '3';

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
            foreach ($one_subs as $one) {
                $paper_one = $one;
                if (($d->$paper_one >= 50 and $d->$paper_one <= 100)) {
                    $grade = 1;
                } else {
                    $grade = 0;
                }
                array_push($points, $grade);
            }

            //SUBICT PAPER GRADE
            $subict = round((($d->Subict + $d->Subict2) / 2), 0);
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

            if (
                $art >= 75 and $art2 >= 80 and $art3 >= 80 and $art4 >= 80 ||
                $art >= 80 and $art2 >= 75 and $art3 >= 80 and $art4 >= 80 ||
                $art >= 80 and $art2 >= 80 and $art3 >= 75 and $art4 >= 80 ||
                $art >= 80 and $art2 >= 80 and $art3 >= 80 and $art4 >= 75

            ) {
                $grade = 6;
            } elseif (
                $art >= 70 and $art2 >= 75 and $art3 >= 75 and $art4 >= 75 ||
                $art >= 75 and $art2 >= 70 and $art3 >= 75 and $art4 >= 75 ||
                $art >= 75 and $art2 >= 75 and $art3 >= 70 and $art4 >= 75 ||
                $art >= 75 and $art2 >= 75 and $art3 >= 75 and $art4 >= 70

            ) {
                $grade = 5;
            } elseif (
                $art >= 65 and $art2 >= 70 and $art3 >= 70 and $art4 >= 70 ||
                $art >= 70 and $art2 >= 65 and $art3 >= 70 and $art4 >= 70 ||
                $art >= 70 and $art2 >= 70 and $art3 >= 65 and $art4 >= 70 ||
                $art >= 70 and $art2 >= 70 and $art3 >= 70 and $art4 >= 65

            ) {
                $grade = 4;
            } elseif (
                $art >= 60 and $art2 >= 65 and $art3 >= 65 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 60 and $art3 >= 65 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 65 and $art3 >= 60 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 65 and $art3 >= 65 and $art4 >= 60

            ) {
                $grade = 3;
            } elseif (
                $art >= 50 and $art2 >= 60 and $art3 >= 60 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 50 and $art3 >= 60 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 60 and $art3 >= 50 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 60 and $art3 >= 60 and $art4 >= 50 ||
                $art >= 40 and $art2 >= 60 and $art3 >= 60 and $art4 >= 65 ||
                $art >= 65 and $art2 >= 60 and $art3 >= 60 and $art4 >= 40 ||
                $art >= 60 and $art2 >= 65 and $art3 >= 40 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 40 and $art3 >= 65 and $art4 >= 60 ||
                $art >= 60 and $art2 >= 60 and $art3 >= 40 and $art4 >= 65

            ) {
                $grade = 2;
            } elseif (
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

            ) {
                $grade = 1;
            } elseif (
                $art >= 0 and $art2 >= 0 and $art3 >= 40 and $art4 >= 40 ||
                $art >= 40 and $art2 >= 40 and $art3 >= 0 and $art4 >= 0 ||
                $art >= 40 and $art2 >= 0 and $art3 >= 0 and $art4 >= 40 ||
                $art >= 0 and $art2 >= 40 and $art3 >= 0 and $art4 >= 40 ||
                $art >= 0 and $art2 >= 40 and $art3 >= 40 and $art4 >= 0 ||
                $art >= 0 and $art2 >= 0 and $art3 >= 0 and $art4 >= 0

            ) {
                $grade = 0;
            }

            array_push($points, $grade);

            $total = array_sum($points);

            $html .=                    '<tr>
                                <td>' . $d->stdID . '</td>
                                <td>' . $d->stdFName . ' ' . $d->stdLName . '</td>
                                <td>' . $d->combination . '</td>
                                <td>' . $d->class . '</td>';
            foreach ($subs_merged as $s) {
                $html .=                    '<td>' . $d->$s . '</td>';
            }
            if ($total != 0) {
                $html .= '                       <td>' . $total . '</td>';
            } else {
                $html .=                        '<td style="color:red;">' . $total . '</td>';
            }
            $html .= '                   </tr>';
            $points = [];
        }
        $html .= '                  </tbody>
                        </table>
                        <h6>Printed on: ' . date('D, d M, Y : h:i:s', strtotime(now())) . '</h6>
                    </div>
                </div>
            </div>
        </body>
        </html>    
        ';

        $pdf = PDF::loadHTML($html)->setPaper('A4', 'landscape');
        return $pdf->stream('' . $class . '_Marksheet');
    }

    public function get_olevel_marksheet(Request $req)
    {
        $results_table = $req->result;
        $class = $req->classname;

        return response()->json([
            'result' => $results_table,
            'class' => $class
        ]);
    }

    public function o_level_marksheet_dt(Request $req)
    {
        $results_table = $req->result;
        $class = $req->classname;

        $data = DB::select("
        SELECT 
            students.stdID,
            " . $results_table . ".stdID,
            stdFName,stdLName,students.class,level,
            stdHouse,section, stdImage, year, status,
            Initialfees, requirements, registration,Mathematics,History,Luganda,CRE,Agriculture,Physics,Chemistry,Biology,
            Geography,Entrepreneurship,English,ICT,Art,Kiswahili,

            (Mathematics+History+Luganda+CRE+Agriculture+Physics+Chemistry+Biology+Geography+Entrepreneurship+English+ICT+Art+Kiswahili)
        AS
            total,
            ROW_NUMBER() OVER (ORDER BY total desc) as row_num
        FROM 
            students," . $results_table . "
        WHERE
        " . $results_table . ".stdID = students.stdID AND students.class = '" . $class . "'
        and students.status = 'continuing';
        ");

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('resultset', function ($fetch) {
            })
            ->addColumn('identifier', function ($fetch) {
                $class = $fetch->class;
                $total = $fetch->total;
                if ($class == 'senior1' || $class == 'senior2' || $class == 'Senior1' || $class == 'Senior2') {
                    $ident = round(($total / 12), 1);
                } elseif ($class == 'senior3' || $class == 'senior4' || $class == 'Senior4' || $class == 'Senior3') {
                    $ident = round(($total / 9), 1);
                } else {
                    $ident = '';
                }

                return $ident;
            })
            ->addColumn('comment', function () {
            })
            ->addColumn('position', function ($fetch) {
                return $fetch->row_num;
            })
            ->make(true);
    }

    public function print_olevel_marksheet($class, $result_set)
    {
        //info("Class = " . $class . ", REsult_set = " . $result_set);

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
            " . $result_set . ".stdID,
            stdFName,stdLName,students.class,level,
            stdHouse,section, stdImage, year, status,
            Initialfees, requirements, registration,Mathematics,History,Luganda,CRE,Agriculture,Physics,Chemistry,Biology,
            Geography,Entrepreneurship,English,ICT,Art,Kiswahili,

            (Mathematics+History+Luganda+CRE+Agriculture+Physics+Chemistry+Biology+Geography+Entrepreneurship+English+ICT+Art+Kiswahili)
        AS
            total,
            ROW_NUMBER() OVER (ORDER BY total desc) as row_num
        FROM 
            students," . $result_set . "
        WHERE
        " . $result_set . ".stdID = students.stdID AND students.class = '" . $class . "';
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
                <h4 class="title">' . $class . ' Marksheet ' . $result_set . '</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class</th>';

        foreach ($head as $h) {
            $html .= '
                    <th>' . $h . '</th>
    ';
        }

        $html .= '
                    <th>Idemtifier</th>
                    <th>Comment</th>
                    <th>Position</th>
                           </tr>
                        </thead>
                        <tbody>';
        foreach ($data as $d) {
            $html .= '
                <tr>
                    <td>' . $d->stdID . '</td>
                    <td>' . $d->stdFName . ' ' . $d->stdLName . '</td>
                    <td>' . $d->class . '</td>';
            foreach ($subjects as $h) {

                $html .= '     <td>' . $d->$h . '</td>';
            }
            if (($d->class) == 'senior1' || ($d->class) == 'Senior1' || ($d->class) == 'senior2' || ($d->class) == 'Senior2') {
                $ident = round($d->total / 12, 1);
                $html .= '   <td>' . $ident . '</td>';
            } elseif (($d->class) == 'senior3' || ($d->class) == 'Senior3' || ($d->class) == 'senior4' || ($d->class) == 'Senior4') {
                $ident = round($d->total / 9, 1);
                $html .= '   <td>' . $ident . '</td>';
            }

            if ($ident >= 2.5 and $ident <= 3.0) {
                $html .= '   <td>OUTSTANDING</td>';
            } elseif ($ident >= 1.5 and $ident <= 2.4) {
                $html .= '   <td>MODERATE</td>';
            } elseif ($ident >= 0.1 and $ident <= 1.4) {
                $html .= '   <td>BASIC</td>';
            } else {
                $html .= '   <td>No LOs</td>';
            }
            $html .= '
                    <td>' . $d->row_num . '</td>
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

        $pdf = PDF::loadHTML($html)->setPaper('A4', 'landscape');
        return $pdf->stream('' . $class . '_Marksheet');
    }
}
