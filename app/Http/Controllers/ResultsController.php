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
    public function select_students(Request $req){
        $classname = $req->classname;
        $table = $req->result_set;
        $subject = $req->subject;
        $paper = $req->paper;

        //Select Term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $results_table = $table . "_" . $term . "_" . $year;
        $subject_paper = $subject . "_" . $paper;

        //Fetch Data
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
                    ORDER BY lname ASC
                ");

        return response($data);
    }

    //Enter student results 
    public function enter_results_alevel(Request $req){
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $class = $req->classname;
        $results_table = ($req->result_set) . "_" . $term . "_" . $year;
        $results = $req->marks;
        $paper = $req->paper;
        $subject = (($req->subject) . "_" . $paper);
        $level = $req->level;

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

    public function print_marklist($class, $paper, $subject)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $data = DB::select("
                SELECT
                    student.std_id,
                    student.fname,
                    student.mname,
                    student.lname
                FROM
                    student
                WHERE
                    student.class = '" . $class . "'
                AND
                    student.status = 'continuing'
                ORDER BY lname ASC
            ");

        $html = '
        <style>
            .container{
                margin-top:-1.2cm;
            }

            .title{
                text-align:center;
                font-weight:bold;
                font-size:20px;
                text-transform:uppercase;
                text-decoration:underline;
            }

            table{
                width:100%;
            }

            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }

            thead tr{
                background:black;
                color:white;
            }
            th,td{
                padding:5px;
            }
        </style>
            <div class="container">
                <p class="title">' . $class . ' ' . $subject . ' ' . (($class == 'Senior 6' || $class == 'Senior 5') ? $paper : '') . ' List Term ' . $term . ' ' . $year . '</p>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Mark</th>
                        </tr>
                    </thead>
                    <tbody>';

        $counter = 0;
        foreach ($data as $d) {
            $counter += 1;
            $html .= '
            <tr>
                <td style="text-align:center;">' . $counter . '</td>
                <td>' . $d->lname . ' ' . (($d->lname == null || $d->mname == 'NULL' || $d->mname == '') ? '' : $d->mname) . ' ' . $d->fname . '</td>
                <td></td>
            </tr>
        ';
        }

        $html .= '   </tbody>
                </table>
            </div>
        ';

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');
        return $pdf->stream('' . $class . '_ClassList');
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

    //Teacher initials
    function initials($subject, $class)
    {
        return DB::table('initials')->select('initials')->where(['subject' => $subject, 'class' => $class])->value('initials');
    }

    public function oreports_print($table, $term, $year, $std_ids)
    {
        $tables = array();
        $table_collect = explode(',', $table);

        //Deal with the tables here
        foreach ($table_collect as $t) {
            array_push($tables, $t);
        }

        $class = DB::table('student')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();

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

        $school = DB::table('school_details')->select('*')->where('id', 1)->first();

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
                    margin-top:-0.5cm;
                    margin-left:-0.5cm;
                    margin-right:-0.5cm;
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
                    margin-top:cm;
                    margin-left:-0.3cm;
                    position:absolute;
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
                    position:absolute;
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
                    padding:5px;
                }

                .key td{
                    font-size:12px;
                    padding:3px;
                }

                .result_row td{
                    font-size:14px;
                }
                .table_header{
                    margin-bottom:0.7cm;
                }
                .table_header, .table_header tr td{
                    text-align:center;
                    border:none;
                }

                .header_title{
                    font-size:25px;
                    font-weight:bolder; 
                    text-transform:uppercase;
                }

                .signature{
                    width:100px;
                }

            </style>';

        //Student Results
        //Check table numbers
        $table_counter = count($tables);

        try {
            $signature_hm = DB::table('signature')->select('signature')->where('signatory', 'head-teacher')->value('signature');
        } catch (Exception $e) {
            $signature_hm = 'empty';
        }

        try {
            $signature_dos = DB::table('signature')->select('signature')->where('signatory', 'dos')->value('signature');
        } catch (Exception $e) {
            $signature_dos = 'empty';
        }

        foreach ($data as $d) {
            //School Badge Here
            $html .= '<div class="container">';
            $html .= '
                <div >
                    <div>
                        <img class="school_badge" src="' . public_path('/') . 'school_badge/' . $school->school_badge . '">
                    </div>
                    
                    <table class="table_header" style="width:100%;">
                        <tbody>
                            <tr rowspan=4><td class="header_title">' . $school->school_name . '</td></tr>
                            <tr><td>' . $school->address . '</td></tr>
                            <tr><td>' . $school->contact . '</td></tr>
                            <tr><td class="school_motto">"' . $school->motto . '"</td></tr>
                        </tbody>
                    </table>
                    
                    ';

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
            if (count($tables) > 1) {
                //MULTIPLE TABLE RESULTS              
                foreach ($subjects as $subject) {
                    $avg = array();
                    if ($table_counter > 1) {
                        for ($i = 0; $i < $table_counter; $i++) {
                            $mark = DB::table('' . $tables[$i] . '')->select('' . $subject->name . '_1')->where('std_id', $d->std_id)->value('' . $subject->name . '_1');
                            array_push($avg, $mark);

                            if ($i == 0 && $mark > 0) {
                                //OPENING ROW TAG
                                $html .= '<tr class="result_row">';
                                $html .= '<td style="padding:5px;">' . $subject->name . '</td>';
                            }

                            if ($mark > 0) {
                                $html .= '<td style="text-align:center;">' . $mark . '</td>';
                            }
                        }
                    }

                    if (array_sum($avg) > 0) {
                        $average = round((array_sum($avg) / count($avg)), 1);
                        //Push the average
                        array_push($total, $average);

                        if ($average >= 2.5 && $average <= 3) {
                            $identifier = 3;
                            $desc = 'OUTSTANDING';
                        } elseif ($average >= 1.5 && $average <= 2.4) {
                            $identifier = 2;
                            $desc = 'MODERATE';
                        } elseif ($average > 0 && $average <= 1.4) {
                            $identifier = 1;
                            $desc = 'BASIC';
                        } else {
                            $identifier = '';
                            $desc = '';
                        }

                        //Average
                        $html .= '<td style="text-align:center;">' . $average . '</td>';

                        //Identifier
                        $html .= '<td style="text-align:center;">' . $identifier . '</td>';

                        //Descriptor
                        $html .= '<td style="text-align:center;">' . $desc . '</td>';

                        //Teacher Initials
                        $html .= '<td style="text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>';
                    }

                    //CLOSING ROW TAG
                    $html .= '</tr>';
                }
            } else {
                //ONE TABLE RESULTS
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

                foreach ($subjects as $subject) {
                    foreach ($marks as $mark) {
                        $subject_name = $subject->name . "_1";
                        if ($mark->std_id == $d->std_id && floatval(($mark->$subject_name) >= 0.1)) {
                            $result_sets = count($tables);

                            //Marks Row here
                            $html .= '
                            <tr class="result_row">
                                <td style="padding:5px;">' . $subject->name . '</td>';

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
                                <td style="font-size:12px; padding:5px; text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>
                            </tr>';
                            }
                            //Push to the total
                            array_push($total, $mark->$subject_name);
                        }
                    }
                }
            }

            //Close the Results Table here
            $html .= '</tbody>   
            </table>';

            //Deal with the Average row
            $sum = array_sum($total);

            if ($d->class == 'Senior 1') {
                $avg = $sum / 14;
            } elseif ($d->class == 'Senior 2') {
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

            //Average table
            $html .= '
            <table style="width:100%;">
                <tbody>
                    <tr class="average_row" style="background:black; color:white;">
                        <td style="font-weight:bold; text-transform:uppercase; text-align:center;">Average</td>
                        <td style="font-weight:bold; text-transform:uppercase; text-align:center;">' . round($avg, 1) . '</td>
                        <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                        <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                        <td style="font-weight:bold; text-transform:uppercase; text-align:center;">' . $desc . '</td>
                        <td style="font-weight:bold; text-transform:uppercase; text-align:center;"></td>
                    </tr>
                </tbody>
            </table>    
            ';

            //Key
            $html .= '
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


            //Remarks
            $html .= '

                    <table style="width:100%;">
                        <thead>
                            <tr>
                                <th colspan=4>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px; width:50%;">HeadTeacher</td>
                                <td style="width:50%; text-align:center;">';
            if ($signature_hm == null) {
                $html .= '';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_hm . '">';
            }

            $html .= '
                                </td>
                                <td style="padding:10px; width:50%;">Director Of Studies</td>
                                <td style="width:50%; text-align:center;">';
            if ($signature_dos == '') {
                $html .= ' ';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_dos . '">';
            }
            $html .= '
                                </td>
                            </tr>
                        </tbody>
                    </table>';

            //Stamp note
            $html .= '

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

        //Deal with the table counter here
        foreach (explode(',', $table) as $t) {
            array_push($tables, $t);
        }

        $class = DB::table('student')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();

        //Signatures
        $signature_hm = DB::table('signature')->select('signature')->where('signatory', 'head-teacher')->value('signature');
        $signature_dos = DB::table('signature')->select('signature')->where('signatory', 'dos')->value('signature');

        //School Details
        $school = DB::table('school_details')->select('*')->where('id', 1)->first();

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

        function std_marks($table, $std_id, $subject){
            return DB::table('' . $table . '')->select('' . $subject . '')->where('std_id', $std_id)->value('' . $subject . '');
        }

        //info("Paper = ".paper_counter('ART'));

        $subjects = DB::select("
                SELECT 
                    name,
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
                    margin-top:-0.5cm;
                    margin-left:-0.5cm;
                    margin-right:-0.5cm;
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
                    margin-top:cm;
                    margin-left:-0.3cm;
                    position:absolute;
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
                    padding:5px;
                }

                .key td{
                    font-size:12px;
                    padding:3px;
                }

                .signature{
                width:100px;
                }

                .results_table td{
                    padding:3px;
                }

                .table_header{
                    margin-bottom:0.7cm;
                }

                .table_header, .table_header tr td{
                    text-align:center;
                    border:none;
                }

                .header_title{
                    font-size:25px;
                    font-weight:bolder; 
                    text-transform:uppercase;
                }

                .remarks-table td{
                    padding:10px !important;
                }

            </style>';

        foreach ($data as $d) {
            //School Badge Here
            $html .= '<div class="container">';
            $html .= '
                <div>
                    <div>
                        <img class="school_badge" src="' . public_path('/') . 'school_badge/' . $school->school_badge . '">
                    </div>
                    
                    <table class="table_header" style="width:100%;">
                        <tbody>
                            <tr rowspan=4><td class="header_title">' . $school->school_name . '</td></tr>
                            <tr><td>' . $school->address . '</td></tr>
                            <tr><td>' . $school->contact . '</td></tr>
                            <tr><td class="school_motto">"' . $school->motto . '"</td></tr>
                        </tbody>
                    </table>';

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

                $html .= '
                    <div>
                        <img class="student_pic" src="' . public_path('/') . 'images/student_photos/' . $d->image . '">
                    </div>';

                /*
                $html .= '
                    <div>
                        <img class="student_pic" src="' . public_path('/') . 'images/static/male.jpg">
                    </div>';
                    */
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
                    <th>Grade</td>
                    <th></th>
                    <th>Points</th>
                    <th>Teacher Initials</th>
                </tr>
            </thead>
            <tbody>';

            //Deal with the Results
            $total = array();

            foreach ($subjects as $subject) {
                $paper_counter = count(explode(',', $subject->paper));
                $paper_num = explode(',', $subject->paper);
                $subject_name = $subject->name;

                //Two Papers
                if ($paper_counter == 2 && $subject->name != 'SubICT') {
                    $subject_avg = array();
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $subject_name_paper = $subject_name . '_' . $paper_num[$i];

                        for ($t = 0; $t < count($tables); $t++) {
                            $std_mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                            //ONLY ADD SUBJECTS WITH RESULTS
                            if ($std_mark > 0) {
                                //START ROW HERE
                                $html .= '<tr class="results_table">';
                                //Subject Name
                                $html .= '<td>' . $subject->name . ' ' . $paper_num[$i] . '</td>';

                                $avg = array();
                                //Subject Mark
                                for ($t = 0; $t < count($tables); $t++) {
                                    $mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                                    //Add the student marks for each subject
                                    //Add Average for every paper
                                    array_push($avg, $mark);
                                    $html .= '<td style="text-align:center;">' . $mark . '</td>';
                                }

                                //Calculate average for each paper
                                $mark_avg = round((array_sum($avg) / count($avg)), 0);
                                array_push($subject_avg, $mark_avg);

                                //Average for Each Paper
                                $html .= '<td style="text-align:center;">' . (($mark_avg > 0) ? $mark_avg : '') . '</td>';
                                $html .= '<td style="text-align:center; font-weight:bold;">' . (($mark_avg > 0) ? $this->get_grade($mark_avg) : '') . '</td>';

                                //GRADE AND POINTS CALCULATION
                                if (count($subject_avg) == $paper_counter && array_sum($subject_avg) > 0) {
                                    $grade = explode('-', $this->two_papers($subject_avg))[0];
                                    $points = explode('-', $this->two_papers($subject_avg))[1];

                                    //Get total Points
                                    array_push($total, $points); 

                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $grade . '</td>';
                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $points . '</td>';
                                    
                                } else {
                                    $html .= '<td style="text-align:center;">-</td>';
                                    $html .= '<td style="text-align:center;">-</td>';
                                }

                                //Teacher Initials
                                $html .= '<td style="text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>';

                                //END ROW HERE
                                $html .= '</tr>';
                            }
                        }
                    }
                }
                
                //Three Papers
                if ($paper_counter == 3) {
                    $subject_avg = array();
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $subject_name_paper = $subject_name . '_' . $paper_num[$i];

                        for ($t = 0; $t < count($tables); $t++) {
                            $std_mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                            //ONLY ADD SUBJECTS WITH RESULTS
                            if ($std_mark > 0) {
                                //START ROW HERE
                                $html .= '<tr class="results_table">';
                                //Subject Name
                                $html .= '<td>' . $subject->name . ' ' . $paper_num[$i] . '</td>';

                                $avg = array();
                                //Subject Mark
                                for ($t = 0; $t < count($tables); $t++) {
                                    $mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                                    //Add the student marks for each subject
                                    //Add Average for every paper
                                    array_push($avg, $mark);
                                    $html .= '<td style="text-align:center;">' . $mark . '</td>';
                                }

                                //Calculate average for each paper
                                $mark_avg = round((array_sum($avg) / count($avg)), 0);
                                array_push($subject_avg, $mark_avg);

                                //Average for Each Paper
                                $html .= '<td style="text-align:center;">' . (($mark_avg > 0) ? $mark_avg : '') . '</td>';
                                $html .= '<td style="text-align:center; font-weight:bold;">' . (($mark_avg > 0) ? $this->get_grade($mark_avg) : '') . '</td>';

                                //GRADE AND POINTS CALCULATION
                                if (count($subject_avg) == $paper_counter && array_sum($subject_avg) > 0) {
                                    $grade = explode('-', $this->three_papers($subject_avg))[0];
                                    $points = explode('-', $this->three_papers($subject_avg))[1];

                                    //Get total Points
                                    array_push($total, $points); 

                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $grade. '</td>';
                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $points . '</td>';
                                    
                                } else {
                                    $html .= '<td style="text-align:center;">-</td>';
                                    $html .= '<td style="text-align:center;">-</td>';
                                }

                                //Teacher Initials
                                $html .= '<td style="text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>';

                                //END ROW HERE
                                $html .= '</tr>';
                            }
                        }
                    }
                }

                //Four Papers
                if ($paper_counter == 4) {
                    $subject_avg = array();
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $subject_name_paper = $subject_name . '_' . $paper_num[$i];

                        for ($t = 0; $t < count($tables); $t++) {
                            $std_mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                            //ONLY ADD SUBJECTS WITH RESULTS
                            if ($std_mark > 0) {
                                //START ROW HERE
                                $html .= '<tr class="results_table">';
                                //Subject Name
                                $html .= '<td>' . $subject->name . ' ' . $paper_num[$i] . '</td>';

                                $avg = array();
                                //Subject Mark
                                for ($t = 0; $t < count($tables); $t++) {
                                    $mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                                    //Add the student marks for each subject
                                    //Add Average for every paper
                                    array_push($avg, $mark);
                                    $html .= '<td style="text-align:center;">' . $mark . '</td>';
                                }

                                //Calculate average for each paper
                                $mark_avg = round((array_sum($avg) / count($avg)), 0);
                                array_push($subject_avg, $mark_avg);

                                //Average for Each Paper
                                $html .= '<td style="text-align:center;">' . (($mark_avg > 0) ? $mark_avg : '') . '</td>';
                                $html .= '<td style="text-align:center; font-weight:bold;">' . (($mark_avg > 0) ? $this->get_grade($mark_avg) : '') . '</td>';

                                //GRADE AND POINTS CALCULATION
                                if (count($subject_avg) == $paper_counter && array_sum($subject_avg) > 0) {
                                    $grade = explode('-', $this->four_papers($subject_avg))[0];
                                    $points = explode('-', $this->four_papers($subject_avg))[1];

                                    //Get total Points
                                    array_push($total, $points); 

                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $grade. '</td>';
                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $points . '</td>';
                                    
                                } else {
                                    $html .= '<td style="text-align:center;">-</td>';
                                    $html .= '<td style="text-align:center;">-</td>';
                                }

                                //Teacher Initials
                                $html .= '<td style="text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>';

                                //END ROW HERE
                                $html .= '</tr>';
                            }
                        }
                    }
                }

                //One Paper
                if ($paper_counter == 1) {
                    $subject_avg = array();
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $subject_name_paper = $subject_name . '_' . $paper_num[$i];

                        for ($t = 0; $t < count($tables); $t++) {
                            $std_mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                            //ONLY ADD SUBJECTS WITH RESULTS
                            if ($std_mark > 0) {
                                //START ROW HERE
                                $html .= '<tr class="results_table">';
                                //Subject Name
                                $html .= '<td>' . $subject->name . '</td>';

                                $avg = array();
                                //Subject Mark
                                for ($t = 0; $t < count($tables); $t++) {
                                    $mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                                    //Add the student marks for each subject
                                    //Add Average for every paper
                                    array_push($avg, $mark);
                                    $html .= '<td style="text-align:center;">' . $mark . '</td>';
                                }

                                //Calculate average for each paper
                                $mark_avg = round((array_sum($avg) / count($avg)), 0);
                                array_push($subject_avg, $mark_avg);

                                //Average for Each Paper
                                $html .= '<td style="text-align:center;">' . (($mark_avg > 0) ? $mark_avg : '') . '</td>';
                                $html .= '<td style="text-align:center; font-weight:bold;">' . (($mark_avg > 0) ? $this->get_grade($mark_avg) : '') . '</td>';

                                //GRADE AND POINTS CALCULATION
                                if (count($subject_avg) == $paper_counter && array_sum($subject_avg) > 0) {
                                    $grade = explode('-', $this->one_paper($subject_avg))[0];
                                    $points = explode('-', $this->one_paper($subject_avg))[1];

                                    //Get total Points
                                    array_push($total, $points); 

                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $grade. '</td>';
                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $points . '</td>';
                                    
                                } else {
                                    $html .= '<td style="text-align:center;">-</td>';
                                    $html .= '<td style="text-align:center;">-</td>';
                                }

                                //Teacher Initials
                                $html .= '<td style="text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>';

                                //END ROW HERE
                                $html .= '</tr>';
                            }
                        }
                    }
                }

                //SubICT
                if ($subject->name == 'SubICT') {
                    $subject_avg = array();
                    for ($i = 0; $i < $paper_counter; $i++) {
                        $subject_name_paper = $subject_name . '_' . $paper_num[$i];

                        for ($t = 0; $t < count($tables); $t++) {
                            $std_mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                            //ONLY ADD SUBJECTS WITH RESULTS
                            if ($std_mark > 0) {
                                //START ROW HERE
                                $html .= '<tr class="results_table">';
                                //Subject Name
                                $html .= '<td>' . $subject->name .' ' . $paper_num[$i] . '</td>';

                                $avg = array();
                                //Subject Mark
                                for ($t = 0; $t < count($tables); $t++) {
                                    $mark = std_marks($tables[$t], $d->std_id, $subject_name_paper);

                                    //Add the student marks for each subject
                                    //Add Average for every paper
                                    array_push($avg, $mark);
                                    $html .= '<td style="text-align:center;">' . $mark . '</td>';
                                }

                                //Calculate average for each paper
                                $mark_avg = round((array_sum($avg) / count($avg)), 0);
                                array_push($subject_avg, $mark_avg);

                                //Average for Each Paper
                                $html .= '<td style="text-align:center;">' . (($mark_avg > 0) ? $mark_avg : '') . '</td>';
                                $html .= '<td style="text-align:center; font-weight:bold;">' . (($mark_avg > 0) ? $this->get_grade($mark_avg) : '') . '</td>';

                                //GRADE AND POINTS CALCULATION
                                if (count($subject_avg) == $paper_counter && array_sum($subject_avg) > 0) {
                                    $grade = explode('-', $this->sub_ict($subject_avg))[0];
                                    $points = explode('-', $this->sub_ict($subject_avg))[1];

                                    //Get total Points
                                    array_push($total, $points); 

                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $grade. '</td>';
                                    $html .= '<td style="text-align:center; font-weight:bolder;">' . $points . '</td>';
                                    
                                } else {
                                    $html .= '<td style="text-align:center;">-</td>';
                                    $html .= '<td style="text-align:center;">-</td>';
                                }

                                //Teacher Initials
                                $html .= '<td style="text-align:center;">' . $this->initials($subject->name, $d->class) . '</td>';

                                //END ROW HERE
                                $html .= '</tr>';
                            }
                        }
                    }
                }
                
            }

            //Close the Results Table here
            $html .= '
                    </tbody>   
                </table>';

            //Calculate Points here
            $html .= '
                <table style="width:50%;">
                    <tbody>
                        <tr class="average_row" style="background:black; color:white;">
                            <td style="font-weight:bold; width:50%; text-transform:uppercase; text-align:center;">Total Points :</td>
                            <td style="font-weight:bold; width:50%; text-transform:uppercase; text-align:center;">' . array_sum($total) . '</td>
                        </tr>
                    </tbody>
                </table>    
            ';

            //Key
            $html .= '<table class="key" style="width:100%;">
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
            $html .= '
            <table class="remarks-table" style="width:100%;">
                <thead>
                    <tr>
                        <th colspan=4>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:10px; width:50%;" rowspan="2">HeadTeacher</td>
                        <td style="width:50%; text-align:center;">';
            if ($signature_hm == null) {
                $html .= '';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_hm . '">';
            }

            $html .= '
                        </td>
                        <td style="padding:10px; width:50%;" rowspan="2">Director Of Studies</td>
                        <td style="width:50%; text-align:center;">';
            if ($signature_dos == '') {
                $html .= ' ';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_dos . '">';
            }
            $html .= '
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
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

    function two_papers($two_paper)
    {
        $p1 = $two_paper[0];
        $p2 = $two_paper[1];

        if (($p1 >= 75 and $p2 >= 75) and ($p1 <= 100 and $p2 <= 100)) {
            $grade = 'A';
            $points = 6;
        } elseif (($p1 >= 65 and $p2 >= 75) || ($p1 >= 75 and $p2 >= 65)
            || ($p1 >= 65 and $p2 >= 65)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'B';
            $points = 5;
        } elseif (($p1 >= 60 and $p2 >= 65) || ($p1 >= 65 and $p2 >= 60)
            || ($p1 >= 60 and $p2 >= 60)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'C';
            $points = 4;
        } elseif (($p1 >= 55 and $p2 >= 60) || ($p1 >= 60 and $p2 >= 55)
            || ($p1 >= 55 and $p2 >= 55)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'D';
            $points = 3;
        } elseif (($p1 >= 50 and $p2 >= 55) || ($p1 >= 55 and $p2 >= 50)
            || ($p1 >= 50 and $p2 >= 50) || ($p1 >= 50 and $p2 >= 55) || ($p1 >= 55 and $p2 >= 50)
            || ($p1 >= 45 and $p2 >= 55) || ($p1 >= 55 and $p2 >= 45)  and ($p1 <= 100 and $p2 <= 100)
        ) {
            $grade = 'E';
            $points = 2;
        } elseif (($p1 >= 50 and $p2 >= 40) || ($p1 >= 40 and $p2 >= 50)
            || ($p1 >= 50 and $p2 >= 60) || ($p1 >= 60 and $p2 >= 50) || ($p1 >= 65 and $p2 >= 0)
            || ($p1 >= 0 and $p2 >= 65) || ($p1 >= 60 and $p2 >= 0) || ($p1 >= 0 and $p2 >= 60)
            || ($p1 >= 50 and $p2 >= 50) || ($p1 >= 50 and $p2 >= 0) || ($p1 >= 0 and $p2 >= 50)
            || ($p1 >= 40 and $p2 >= 40)  and ($p1 <= 100 and $p2 <= 100)
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

        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function three_papers($three_paper)
    {
        $p1 = $three_paper[0];
        $p2 = $three_paper[1];
        $p3 = $three_paper[2];

        if ((($p1 >= 75 and $p2 >= 75 and $p3 >= 64) || ($p1 >= 64 and $p2 >= 75 and $p3 >= 75)
            || ($p1 >= 75 and $p2 >= 64 and $p3 >= 75) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 'A';
            $points = 6;
        } elseif ((($p1 >= 65 and $p2 >= 65 and $p3 >= 60) || ($p1 >= 60 and $p2 >= 65 and $p3 >= 65)
            || ($p1 >= 65 and $p2 >= 60 and $p3 >= 65) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 'B';
            $points = 5;
        } elseif ((($p1 >= 60 and $p2 >= 60 and $p3 >= 55) || ($p1 >= 55 and $p2 >= 60 and $p3 >= 60)
            || ($p1 >= 60 and $p2 >= 55 and $p3 >= 60) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 'C';
            $points = 4;
        } elseif ((($p1 >= 55 and $p2 >= 55 and $p3 >= 50) || ($p1 >= 50 and $p2 >= 55 and $p3 >= 55)
            || ($p1 >= 55 and $p2 >= 50 and $p3 >= 55) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 'D';
            $points = 3;
        } elseif ((($p1 >= 50 and $p2 >= 50 and $p3 >= 45)
            || ($p1 >= 45 and $p2 >= 50 and $p3 >= 50) || ($p1 >= 50 and $p2 >= 45 and $p3 >= 50)
            || ($p1 >= 40 and $p2 >= 50 and $p3 >= 65) || ($p1 >= 65 and $p2 >= 40 and $p3 >= 50)
            || ($p1 >= 50 and $p2 >= 65 and $p3 >= 40) and ($p1 <= 100 and $p2 <= 100 and $p3 <= 100))) {
            $grade = 'E';
            $points = 2;
        } elseif (($p1 >= 50 and $p2 >= 50 and $p3 >= 50) || ($p1 >= 40 and $p2 >= 40 and $p3 >= 40)
            || ($p1 >= 0 and $p2 >= 40 and $p3 >= 40) || ($p1 >= 40 and $p2 >= 0 and $p3 >= 40)
            || ($p1 >= 40 and $p2 >= 40 and $p3 >= 0) || ($p1 >= 50 and $p2 >= 0 and $p3 >= 50)
            || ($p1 >= 50 and $p2 >= 50 and $p3 >= 0) || ($p1 >= 0 and $p2 >= 50 and $p3 >= 50 and $p1 <= 100 and $p2 <= 100 and $p3 <= 100)
        ) {
            $grade = 'O';
            $points = 1;
        } elseif (($p1 >= 0 and $p2 >= 40 and $p3 >= 40) || ($p1 >= 40 and $p2 >= 0 and $p3 >= 40)
            || ($p1 >= 40 and $p2 >= 40 and $p3 >= 0) || ($p1 >= 0 and $p2 >= 0 and $p3 >= 0 and $p1 <= 100 and $p2 <= 100 and $p3 <= 100)
        ) {
            $grade = 'F';
            $points = 0;
        } else {
            $grade = 'F';
            $points = 0;
        }

        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function four_papers($four_papers){
        $p1 = $four_papers[0];
        $p2 = $four_papers[1];
        $p3 = $four_papers[2];
        $p4 = $four_papers[3];

        if (
            $p1 >= 75 and $p2 >= 80 and $p3 >= 80 and $p4 >= 80 ||
            $p1 >= 80 and $p2 >= 75 and $p3 >= 80 and $p4 >= 80 ||
            $p1 >= 80 and $p2 >= 80 and $p3 >= 75 and $p4 >= 80 ||
            $p1 >= 80 and $p2 >= 80 and $p3 >= 80 and $p4 >= 75

        ) {
            $grade = 'A';
            $points = 6;
        } elseif (
            $p1 >= 70 and $p2 >= 75 and $p3 >= 75 and $p4 >= 75 ||
            $p1 >= 75 and $p2 >= 70 and $p3 >= 75 and $p4 >= 75 ||
            $p1 >= 75 and $p2 >= 75 and $p3 >= 70 and $p4 >= 75 ||
            $p1 >= 75 and $p2 >= 75 and $p3 >= 75 and $p4 >= 70

        ) {
            $grade = 'B';
            $points = 5;
        } elseif (
            $p1 >= 65 and $p2 >= 70 and $p3 >= 70 and $p4 >= 70 ||
            $p1 >= 70 and $p2 >= 65 and $p3 >= 70 and $p4 >= 70 ||
            $p1 >= 70 and $p2 >= 70 and $p3 >= 65 and $p4 >= 70 ||
            $p1 >= 70 and $p2 >= 70 and $p3 >= 70 and $p4 >= 65

        ) {
            $grade = 'C';
            $points = 4;
        } elseif (
            $p1 >= 60 and $p2 >= 65 and $p3 >= 65 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 60 and $p3 >= 65 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 65 and $p3 >= 60 and $p4 >= 65 ||
            $p1 >= 65 and $p2 >= 65 and $p3 >= 65 and $p4 >= 60

        ) {
            $grade = 'D';
            $points = 3;
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
            $grade = 'E';
            $points = 2;
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
            $grade = 'O';
            $points = 1;
        } elseif (
            $p1 >= 0 and $p2 >= 0 and $p3 >= 40 and $p4 >= 40 ||
            $p1 >= 40 and $p2 >= 40 and $p3 >= 0 and $p4 >= 0 ||
            $p1 >= 40 and $p2 >= 0 and $p3 >= 0 and $p4 >= 40 ||
            $p1 >= 0 and $p2 >= 40 and $p3 >= 0 and $p4 >= 40 ||
            $p1 >= 0 and $p2 >= 40 and $p3 >= 40 and $p4 >= 0 ||
            $p1 >= 0 and $p2 >= 0 and $p3 >= 0 and $p4 >= 0

        ) {
            $grade = 'F';
            $points = 0;
        } else {
            $grade = 'F';
            $points = 0;
        }

        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function sub_ict($sub_ict){
        $p1 = $sub_ict[0];
        $p2 = $sub_ict[1];

        $avg = (($p1 + $p2) / 2);

        if ($avg >= 50) {
            $grade = 'O';
            $points = 1;
        } else {
            $grade = 'F';
            $points = 0;
        }
        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function one_paper($one_paper){
        $p1 = $one_paper[0];

        if ($p1 >= 50) {
            $grade = 'O';
            $points = 1;
        } else {
            $grade = 'F';
            $points = 0;
        }
        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function get_grade($value){
        if($value >= 85 and $value <=100){
            $grade = "D1";
        }elseif($value >= 80 and $value <=84){
            $grade = "D2";
        }elseif($value >= 75 and $value <=79){
            $grade = "C3";
        }elseif($value >= 70 and $value <=74){
            $grade = "C4";
        }elseif($value >= 65 and $value <=69){
            $grade = "C5";
        }elseif($value >= 60 and $value <= 64){
            $grade = "C6";
        }elseif($value >= 50 and $value <=59){
            $grade = "P7";
        }elseif($value >= 40 and $value <=49){
            $grade = "P8";
        }elseif($value >= 0 and $value <=39){
            $grade = "F9";
        }else{
            $grade = "F9";
        }
        return $grade;
    }
}
