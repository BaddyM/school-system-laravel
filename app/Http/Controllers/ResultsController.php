<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
{
    public function olevel_index()
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
                *
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

        return view('results.olevel', compact('classes', 'subjects', 'results'));
    }

    public function alevel_index()
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
                DISTINCT name
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

        $papers = DB::table('subjects')->distinct()->select('paper')->where('level', 'A Level')->orderBy('paper', 'asc')->get();

        return view('results.alevel', compact('classes', 'subjects', 'results', 'papers'));
    }

    //Select students to award marks
    public function select_students(Request $req)
    {
        $classname = $req->classname;
        $table = $req->result_set;
        $subject = $req->subject;
        $paper = $req->paper;
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $results_table = $table . "_" . $term . "_" . $year;
        $subject_paper = $subject . "_" . $paper;
        //info("Column = " . $subject_paper.", table = ".$results_table);
        $data = DB::select("
                    SELECT
                        student.std_id,
                        student.fname,
                        student.mname,
                        student.lname,
                        " . $results_table . "." . $subject_paper . " as mark
                    FROM
                        student
                    LEFT OUTER JOIN
                        " . $results_table . "
                    ON
                        student.std_id = " . $results_table . ".std_id
                    WHERE
                        student.class = '" . $classname . "'
                    AND
                        student.status = 'continuing'
                ");
        //info($data);

        return response($data);
    }

    //Enter student results 
    public function enter_results_alevel(Request $req)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $class = $req->classname;
        $results_table = ($req->result_set) . "_" . $term . "_" . $year;
        $results = $req->marks;
        $paper = $req->paper;
        $subject = (($req->subject) . "_" . $paper);
        $level = $req->level;

        //info("Class = " . $class . ", table = " . $results_table . ", subject = " . $subject . ", paper = " . $paper . ", level = " . $level);
        //info($results);

        foreach ($results as $result) {
            $std_id = $result[0];
            $mark = $result[1];

            //Check if the record exists
            $exists = (DB::table('' . $results_table . '')->select('std_id', '' . $subject . '')->where('std_id', $std_id))->first();

            if ($exists == '' || $exists == null || $exists == 'NULL') {
                //New Record for new student
                try {
                    DB::table('' . $results_table . '')->insert([
                        'std_id' => $std_id,
                        'class' => $class,
                        '' . $subject . '' => $mark
                    ]);
                } catch (Exception $e) {
                    info($e);
                }
                $response = 'New Record Inserted';
            } else {
                //Update for Existing
                try {
                    DB::table('' . $results_table . '')->where('std_id', $std_id)->update([
                        'class' => $class,
                        '' . $subject . '' => $mark
                    ]);
                } catch (Exception $e) {
                    info($e);
                }
                $response = 'Record Updated';
            }
        }
        return response($response);
    }

    public function enter_results_olevel(Request $req)
    {
        $results = $req->marks;
        $table = $req->result_set;
        $class = $req->classname;
        $subject = ($req->subject) . "_1";
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $results_table = $table . "_" . $term . "_" . $year;

        foreach ($results as $result) {
            $std_id = $result[0];
            $mark = $result[1];

            //Check if the record exists
            $exists = (DB::table('' . $results_table . '')->select('std_id', '' . $subject . '')->where('std_id', $std_id))->first();

            if ($exists == '' || $exists == null || $exists == 'NULL') {
                //New Record for new student
                try {
                    DB::table('' . $results_table . '')->insert([
                        'std_id' => $std_id,
                        'class' => $class,
                        '' . $subject . '' => $mark
                    ]);
                } catch (Exception $e) {
                    info($e);
                }
                $response = 'New Record Inserted';
            } else {
                //Update for Existing
                try {
                    DB::table('' . $results_table . '')->where('std_id', $std_id)->update([
                        'class' => $class,
                        '' . $subject . '' => $mark
                    ]);
                } catch (Exception $e) {
                    info($e);
                }
                $response = 'Record Updated';
            }
        }
        return response($response);
    }

    public function oreports_index()
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

        return view('results.oreports', compact('classes', 'results'));
    }

    public function areports_index()
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

        return view('results.areports', compact('classes', 'results'));
    }

    public function select_class(Request $req)
    {
        $class = $req->classname;
        try {
            $data = DB::table('student')->select(['std_id', 'lname', 'mname', 'fname'])->where(['class' => $class, 'status' => 'continuing'])->get();
        } catch (Exception $e) {
            info($e);
        }

        return $data;
    }

    public function oreports_print($table, $term, $year, $std_ids)
    {
        //$std_ids = implode(',',($std_id));
        $tables = array();
        array_push($tables, $table);

        //info('Std_ids = ' . $std_ids);

        $class = DB::table('student')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();
        //info("Class = ".$class->class);

        //Check count of the tables
        if (count($tables) > 1) {
            //Multiple tables


        } else {
            //One table
            $school = DB::table('school_details')->select('*')->where('id', 1)->first();

            //info(json_encode($school));

            $data = DB::select("
                SELECT 
                    *
                FROM
                    student                    
                WHERE
                    std_id
                IN
                    (" . $std_ids . ")
            ");

            $marks = DB::select("
                SELECT
                    *
                FROM
                    " . $tables[0] . "
                WHERE
                    std_id
                IN
                    (" . $std_ids . ")
            ");
        }

        $subjects = DB::select("
                SELECT
                    DISTINCT name
                FROM
                    subjects
                WHERE
                    level = 'O Level'
            ");

        $html = '
            <style>
                .page_break{
                    page-break-after:always;
                }

                p{
                    font-family:calibri;
                }

                .school_name{
                    font-weight:bold;
                    text-align:center;
                    font-size:25px;
                    text-transform:uppercase;
                }

                .container{
                    margin-top:-1.3cm;
                    margin-left:-0.7cm;
                }

                .school_address, .school_motto, .school_contact{
                    text-align:center;
                    margin-top:-0.2cm;
                    font-size:18px;
                    font-family:"Helvetica";
                }

                .school_motto{
                    font-weight:bold;
                    font-style:italics;
                }

                .school_badge{
                    margin-top:1.3cm;
                    margin-left:-0.3cm;
                }

                .school_badge, .student_pic{
                    width:120px;
                    height:120px; 
                }

                .school_details{
                    margin-top:-10cm;
                }

                .student_pic{
                    float:right;
                    margin-top:-3.3cm;
                    margin-left:-0.3cm;
                    border-radius:100px;
                }

                table, th, td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }

                table{
                    margin-bottom:10px;
                }

                .row_label{
                    padding:5px;
                    font-size:14px;
                    text-transform:uppercase;
                    text-align:center;
                }

                .row_title{
                    font-size:14px;
                    background:grey;
                    padding:5px;
                    text-transform:uppercase;
                    color:white;
                    font-weight:bold;
                }

                thead th{
                    padding:5px;
                    text-transform:capitalize;
                }

                .average_row td{
                    padding:10px;
                }

                .key td{
                    font-size:12px;
                    padding:3px;
                }

            </style>';

        //info($marks);

        foreach ($data as $d) {
            //School Badge Here
            $html .= '<div class="container">';
            $html .= '
                <div >
                    <div>
                        <img class="school_badge" src="' . public_path('/') . 'school_badge/' . $school->school_badge . '">
                    </div>
                    
                    <div class="school_details">
                        <p class="school_name">' . $school->school_name . '</p>
                        <p class="school_address">' . $school->address . '</p>
                        <p class="school_contact">' . $school->contact . '</p>
                        <p class="school_motto">"' . $school->motto . '"</p>
                    </div>';

            //Student Image Section here
            if ($d->image == null || $d->image == 'NULL' || $d->image == '') {
                if ($d->gender == 'Female') {
                    $html .= '
                        <div>
                            <img class="student_pic" src="' . public_path('/') . 'images/static/female.jpg">
                        </div>';
                } else {
                    $html .= '
                        <div>
                            <img class="student_pic" src="' . public_path('/') . 'images/static/male.jpg">
                        </div>';
                }
            } else {
                /*
                $html .= '
                    <div>
                        <img class="student_pic" src="' . public_path('/') . 'images/student_photos/' . $d->image . '">
                    </div>';
                    */

                    $html .= '
                        <div>
                            <img class="student_pic" src="' . public_path('/') . 'images/static/male.jpg">
                        </div>';
            }

            //Student Details Area
            $html .= '
                <table style="width:100%;">
                    <tr class="row_data" colspan="4">
                        <td class="row_title">Student Name</td>
                        <td class="row_label">
                        ' . $d->lname . ' ' . (($d->mname == null || $d->mname == 'NULL' || $d->mname == '') ? '' : $d->mname) . ' ' . $d->fname . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr class="row_data">
                        <td class="row_title">Section</td>
                        <td class="row_label">' . $d->section . '</td>

                        <td class="row_title">Class</td>
                        <td class="row_label">
                        ' . $d->class . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr class="row_data">
                        <td class="row_title">Gender</td>
                        <td class="row_label">' . $d->gender . '</td>

                        <td class="row_title">House</td>
                        <td class="row_label">
                        ' . (($d->house == null || $d->house == 'NULL' || $d->house == '') ? '' : $d->house) . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr class="row_data">
                        <td class="row_title">Fees</td>
                        <td class="row_label">  </td>

                        <td class="row_title">Student ID</td>
                        <td class="row_label">
                        CHSN-' . $d->std_id . '
                        </td>

                        <td class="row_title">Term</td>
                        <td class="row_label">
                        ' . $term . '
                        </td>

                        <td class="row_title">Year</td>
                        <td class="row_label">
                        ' . $year . '
                        </td>
                    </tr>
                </table>';

            //foreach($subjects as $subject){
            $html .= '
                <p style="text-align:center; margin-bottom:10px; font-weight:bold; text-transform:uppercase; font-size:18px;">Student Results</p>

                    <table style="width:100%;">
                        <thead>
                            <tr style="background:black; color:white;">
                                <th>Subject</th>';
            //Deal with Subject titles
            $t_count = 0;
            foreach ($tables as $table) {
                $t_count += 1;
                $html .= '<th>A' . $t_count . '</th>';
            }

            $html .= '
                    <th>Average</th>
                    <th>Identifier</th>
                    <th>Descriptor</th>
                    <th>Teacher Initials</th>
                </tr>
            </thead>
            <tbody>';

            //Deal with the Results
            $total = array();
            foreach ($subjects as $subject) {
                foreach ($marks as $mark) {
                    $subject_name = $subject->name . "_1";
                    if ($mark->std_id == $d->std_id && floatval(($mark->$subject_name) >= 0.1)) {
                        $result_sets = count($tables);

                        //Marks Row here
                        $html .= '
                        <tr>
                            <td style="font-size:12px; padding:5px;">' . $subject->name . '</td>';

                        //Calculate Average for all results
                        if ($result_sets == 1) {
                            //Single result set
                            $std_mark = $mark->$subject_name;
                            //Grade
                            if ($std_mark >= 2.5 && $std_mark <= 3) {
                                $identifier = 3;
                                $desc = 'OUTSTANDING';
                            } elseif ($std_mark >= 1.5 && $std_mark <= 2.4) {
                                $identifier = 2;
                                $desc = 'MODERATE';
                            } elseif ($std_mark > 0 && $std_mark <= 1.4) {
                                $identifier = 1;
                                $desc = 'BASIC';
                            } else {
                                $identifier = '';
                                $desc = '';
                            }

                            $html .= '
                            <td style="font-size:12px; padding:5px; text-align:center;">' . $mark->$subject_name . '</td>
                            <td style="font-size:12px; padding:5px; text-align:center;">' . $mark->$subject_name . '</td>
                            <td style="font-size:12px; padding:5px; text-align:center;">' . $identifier . '</td>
                            <td style="font-size:12px; padding:5px; text-align:center;">' . $desc . '</td>
                            <td style="font-size:12px; padding:5px; text-align:center;">    </td>
                        </tr>';
                        } else {
                            //Multiple Result Set
                        }
                        //Push to the total
                        array_push($total, $mark->$subject_name);
                    }
                }
            }

            //Deal with the Average row
            $sum = array_sum($total);

            if ($d->class == 'Senior 1'){
                $avg = $sum / 14;
            }
            elseif($d->class == 'Senior 2') {
                $avg = $sum / 11;
            } else {
                $avg = $sum / 9;
            }

            if ($avg >= 2.5 && $avg <= 3) {
                $identifier = 3;
                $desc = 'OUTSTANDING';
            } elseif ($avg >= 1.5 && $avg <= 2.4) {
                $identifier = 2;
                $desc = 'MODERATE';
            } elseif ($avg > 0 && $avg <= 1.4) {
                $identifier = 1;
                $desc = 'BASIC';
            } else {
                $identifier = '';
                $desc = '';
            }

            $html .= '
                <tr class="average_row" style="background:black; color:white;">
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;">Average</td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;">' . round($avg, 1) . '</td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;">' . $desc . '</td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                </tr>';

            $html .= '
                        </tbody>   
                    </table>

                    <table class="key" style="width:70%;">
                        <thead>
                            <tr>
                                <th colspan=2>Key</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:5px; text-align:center;">2.5 - 3.0 </td>
                                <td style="text-align:center;">
                                    OUSTANDING
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:center;">1.5 - 2.4</td>
                                <td style="text-align:center;">
                                    MODERATE
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:center;">0.1 - 1.4</td>
                                <td style="text-align:center;">
                                    BASIC
                                </td>
                            </tr>
                        </tbody>
                    </table>';

                    
                    $html .='

                    <table style="width:50%;">
                        <thead>
                            <tr>
                                <th colspan=2>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px;">HeadTeacher</td>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px;">Director Of Studies</td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                    </table>';

                    
                    $html .='

                    <p style="text-align:center; color:black; font-style:italics; font-size:11px;">This Report is invalid without a stamp</p>
                ';

            $html .= '
                </div>';

            $html .= '</div>';

            $html .= '<div class="page_break"></div>';
        }

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');

        return $pdf->stream('' . $class->class . ' Reports');
    }

    public function areports_print($table, $term, $year, $std_ids)
    {
        $tables = array();
        array_push($tables, $table);

        $class = DB::table('student')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();

        //Check count of the tables
        if (count($tables) > 1) {
            //Multiple tables


        } else {
            //One table
            $school = DB::table('school_details')->select('*')->where('id', 1)->first();

            //info(json_encode($school));

            $data = DB::select("
                SELECT 
                    *
                FROM
                    student                    
                WHERE
                    std_id
                IN
                    (" . $std_ids . ")
            ");

            $marks = DB::select("
                SELECT
                    *
                FROM
                    " . $tables[0] . "
                WHERE
                    std_id
                IN
                    (" . $std_ids . ")
            ");
        }

        $subjects = DB::select("
                SELECT 
                    DISTINCT name,
                    GROUP_CONCAT(paper) as paper
                FROM
                    subjects
                WHERE
                    level = 'A Level'
                GROUP BY
                    name
            ");



        $html = '
            <style>
                .page_break{
                    page-break-after:always;
                }

                p{
                    font-family:calibri;
                }

                .school_name{
                    font-weight:bold;
                    text-align:center;
                    font-size:25px;
                    text-transform:uppercase;
                }

                .container{
                    margin-top:-1.3cm;
                    margin-left:-0.7cm;
                }

                .school_address, .school_motto, .school_contact{
                    text-align:center;
                    margin-top:-0.2cm;
                    font-size:18px;
                    font-family:"Helvetica";
                }

                .school_motto{
                    font-weight:bold;
                    font-style:italics;
                }

                .school_badge{
                    margin-top:1.3cm;
                    margin-left:-0.3cm;
                }

                .school_badge, .student_pic{
                    width:120px;
                    height:120px; 
                }

                .school_details{
                    margin-top:-10cm;
                }

                .student_pic{
                    float:right;
                    margin-top:-3.3cm;
                    margin-left:-0.3cm;
                    border-radius:100px;
                }

                table, th, td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }

                table{
                    margin-bottom:10px;
                }

                .row_label{
                    padding:5px;
                    font-size:14px;
                    text-transform:uppercase;
                    text-align:center;
                }

                .row_title{
                    font-size:14px;
                    background:grey;
                    padding:5px;
                    text-transform:uppercase;
                    color:white;
                    font-weight:bold;
                }

                thead th{
                    padding:5px;
                    text-transform:capitalize;
                }

                .average_row td{
                    padding:10px;
                }

                .key td{
                    font-size:12px;
                    padding:3px;
                }

            </style>';

        //info($marks);

        foreach ($data as $d) {
            //School Badge Here
            $html .= '<div class="container">';
            $html .= '
                <div >
                    <div>
                        <img class="school_badge" src="' . public_path('/') . 'school_badge/' . $school->school_badge . '">
                    </div>
                    
                    <div class="school_details">
                        <p class="school_name">' . $school->school_name . '</p>
                        <p class="school_address">' . $school->address . '</p>
                        <p class="school_contact">' . $school->contact . '</p>
                        <p class="school_motto">"' . $school->motto . '"</p>
                    </div>';

            //Student Image Section here
            if ($d->image == null || $d->image == 'NULL' || $d->image == '') {
                if ($d->gender == 'Female') {
                    $html .= '
                        <div>
                            <img class="student_pic" src="' . public_path('/') . 'images/static/female.jpg">
                        </div>';
                } else {
                    $html .= '
                        <div>
                            <img class="student_pic" src="' . public_path('/') . 'images/static/male.jpg">
                        </div>';
                }
            } else {
                /*
                $html .= '
                    <div>
                        <img class="student_pic" src="' . public_path('/') . 'images/student_photos/' . $d->image . '">
                    </div>';
                    */
                    $html .= '
                        <div>
                            <img class="student_pic" src="' . public_path('/') . 'images/static/male.jpg">
                        </div>';
            }

            //Student Details Area
            $html .= '
                <table style="width:100%;">
                    <tr class="row_data" colspan="4">
                        <td class="row_title">Student Name</td>
                        <td class="row_label">
                        ' . $d->lname . ' ' . (($d->mname == null || $d->mname == 'NULL' || $d->mname == '') ? '' : $d->mname) . ' ' . $d->fname . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr class="row_data">
                        <td class="row_title">Section</td>
                        <td class="row_label">' . $d->section . '</td>

                        <td class="row_title">Class</td>
                        <td class="row_label">
                        ' . $d->class . '
                        </td>

                        <td class="row_title">Combination</td>
                        <td class="row_label">
                        ' . $d->combination . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr class="row_data">
                        <td class="row_title">Gender</td>
                        <td class="row_label">' . $d->gender . '</td>

                        <td class="row_title">House</td>
                        <td class="row_label">
                        ' . (($d->house == null || $d->house == 'NULL' || $d->house == '') ? '' : $d->house) . '
                        </td>
                    </tr>
                </table>

                <table style="width:100%;">
                    <tr class="row_data">
                        <td class="row_title">Fees</td>
                        <td class="row_label">  </td>

                        <td class="row_title">Student ID</td>
                        <td class="row_label">
                        CHSN-' . $d->std_id . '
                        </td>

                        <td class="row_title">Term</td>
                        <td class="row_label">
                        ' . $term . '
                        </td>

                        <td class="row_title">Year</td>
                        <td class="row_label">
                        ' . $year . '
                        </td>
                    </tr>
                </table>';

            //foreach($subjects as $subject){
            $html .= '
                <p style="text-align:center; margin-bottom:10px; font-weight:bold; text-transform:uppercase; font-size:18px;">Student Results</p>

                    <table style="width:100%;">
                        <thead>
                            <tr style="background:black; color:white;">
                                <th>Subject</th>';
            //Deal with Subject titles
            $t_count = 0;
            foreach ($tables as $table) {
                $t_count += 1;
                $html .= '<th>Set ' . $t_count . '</th>';
            }

            $html .= '
                    <th>Average</th>
                    <th>Grade</th>
                    <th>Points</th>
                    <th>Teacher Initials</th>
                </tr>
            </thead>
            <tbody>';

            //Deal with the Results
            $total = array();
            foreach ($subjects as $subject) {
                $papers = explode(',', $subject->paper);
                $paper_name = $subject->name;
                $paper_counter = count($papers);

                //info($marks);

                if ($paper_counter == 2 && $paper_name != 'SubICT') {
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $paper_name_mark = $paper_name."_".$papers[$i];
                        $mark = array();
                        foreach ($marks as $mark) {
                            if ($mark->std_id == $d->std_id && $mark->$paper_name_mark > 0) {
                                $html .= '
                        <tr>
                            <td>' . $paper_name . ' '.$papers[$i].'</td>
                            <td style="text-align:center;">'.$mark->$paper_name_mark.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                            }
                        }
                    }
                }elseif ($paper_counter == 3) {
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $paper_name_mark = $paper_name."_".$papers[$i];
                        $mark_point = array();

                        foreach ($marks as $mark) {
                            array_push($mark_point, $mark->$paper_name_mark);
                            
                        }

                        //$this->three_papers($mark_point);

                        info($paper_name."_".$papers[$i]."  ".count($mark_point));
                        foreach ($marks as $mark) {
                            
                            if ($mark->std_id == $d->std_id && $mark->$paper_name_mark > 0) {
                                $html .= '
                        <tr>
                            <td>' . $paper_name . ' '.$papers[$i].'</td>
                            <td style="text-align:center;">'.$mark->$paper_name_mark.'</td>
                            <td></td>';
                        $html .='
                        <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                            }
                        }
                    }
                }elseif ($paper_counter == 4) {
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $paper_name_mark = $paper_name."_".$papers[$i];
                        $mark = array();
                        foreach ($marks as $mark) {
                            if ($mark->std_id == $d->std_id && $mark->$paper_name_mark > 0) {
                                $html .= '
                        <tr>
                            <td>' . $paper_name . ' '.$papers[$i].'</td>
                            <td style="text-align:center;">'.$mark->$paper_name_mark.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                            }
                        }
                    }
                }elseif($paper_name == 'SubICT'){
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $paper_name_mark = $paper_name."_".$papers[$i];
                        $mark = array();
                        foreach ($marks as $mark) {
                            if ($mark->std_id == $d->std_id && $mark->$paper_name_mark > 0) {
                                $html .= '
                        <tr>
                            <td>' . $paper_name . ' '.$papers[$i].'</td>
                            <td style="text-align:center;">'.$mark->$paper_name_mark.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                            }
                        }
                    }
                }else{
                        $paper_name_mark = $paper_name."_1";
                        $mark = array();
                        foreach ($marks as $mark) {
                            if ($mark->std_id == $d->std_id && $mark->$paper_name_mark > 0) {
                                $html .= '
                        <tr>
                            <td>' . $paper_name . '</td>
                            <td style="text-align:center;">'.$mark->$paper_name_mark.'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                            }
                        }

                }
            }

            $html .= '
                <tr class="average_row" style="background:black; color:white;">
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;">Average</td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                </tr>';

            $html .= '
                    </tbody>   
                </table>';

                
            //Key
            $html .='<table class="key" style="width:100%;">
                        <thead>
                            <tr>
                                <th colspan=18>Grading</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:5px; text-align:center; font-weight:bold;">85 - 100 </td>
                                <td style="text-align:center;">
                                    D1
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">80 - 84 </td>
                                <td style="text-align:center;">
                                    D2
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">75 - 79 </td>
                                <td style="text-align:center;">
                                    C3
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">70 - 74 </td>
                                <td style="text-align:center;">
                                    C4
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">65 - 69 </td>
                                <td style="text-align:center;">
                                    C5
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">60 - 64 </td>
                                <td style="text-align:center;">
                                    C6
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">50 - 59 </td>
                                <td style="text-align:center;">
                                    P7
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">40 - 49 </td>
                                <td style="text-align:center;">
                                    P8
                                </td>

                                <td style="padding:5px; text-align:center; font-weight:bold;">0 - 39 </td>
                                <td style="text-align:center;">
                                    F9
                                </td>

                            </tr>
    
                        </tbody>
                    </table>';
                    

                //Remarks
                $html .='<table style="width:50%;">
                        <thead>
                            <tr>
                                <th colspan=2>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px; width:20%;">HeadTeacher</td>
                                <td>
                                  
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px;">Director Of Studies</td>
                                <td>
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p style="text-align:center; color:black; font-style:italics; font-size:11px;">This Report is invalid without a stamp</p>
                ';

            $html .= '
                </div>';

            $html .= '</div>';

            $html .= '<div class="page_break"></div>';
        }

        

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');

        return $pdf->stream('' . $class->class . ' Reports');
    }
    function two_papers($two_paper){
        $p1 = (explode(',',$two_paper))[0];
        $p2 = (explode(',',$two_paper))[1];

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

function three_papers($three_paper)
{
    $p1 = $three_paper[0];
    $p2 = $three_paper[1];
    $p3 = $three_paper[2];

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
}
