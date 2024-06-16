<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;

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
        $topic = $req->topic;
        $level = $req->level;
        $paper = $req->paper;

        //Select Term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $results_table = $table . "_" . $term . "_" . $year;
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        if ($level == 'O Level') {
            //Fetch Data
            $data = DB::select("
                SELECT
                    student_" . $current_year . ".std_id,
                    student_" . $current_year . ".fname,
                    student_" . $current_year . ".mname,
                    student_" . $current_year . ".lname,
                    " . $results_table . ".score as mark,
                    " . $results_table . ".competence as competence,
                    " . $results_table . ".remark as remark
                FROM
                    student_" . $current_year . "
                LEFT OUTER JOIN
                    " . $results_table . "
                ON
                    student_" . $current_year . ".std_id = " . $results_table . ".std_id
                WHERE
                    student_" . $current_year . ".class = '" . $classname . "'
                AND
                    student_" . $current_year . ".status = 'continuing'      
                AND
                    " . $results_table . ".topic = '" . $topic . "'   
                AND
                    " . $results_table . ".subject = '" . $subject . "'               
                ORDER BY lname ASC
            ");
        } elseif ($level == 'A Level') {
            //Fetch Data
            $data = DB::select("
                SELECT
                    student_" . $current_year . ".std_id,
                    student_" . $current_year . ".fname,
                    student_" . $current_year . ".mname,
                    student_" . $current_year . ".lname,
                    " . $results_table . "." . $subject . "_" . $paper . " as mark
                FROM
                    student_" . $current_year . "
                LEFT OUTER JOIN
                    " . $results_table . "
                ON
                    student_" . $current_year . ".std_id = " . $results_table . ".std_id
                WHERE
                    student_" . $current_year . ".class = '" . $classname . "'
                AND
                    student_" . $current_year . ".status = 'continuing'              
                ORDER BY lname ASC
            ");
        }

        if (count($data) == 0) {
            $data = DB::select("
                    SELECT
                        student_" . $current_year . ".std_id,
                        student_" . $current_year . ".fname,
                        student_" . $current_year . ".mname,
                        student_" . $current_year . ".lname
                    FROM
                        student_" . $current_year . "
                    LEFT OUTER JOIN
                        " . $results_table . "
                    ON
                        student_" . $current_year . ".std_id = " . $results_table . ".std_id
                    WHERE
                        student_" . $current_year . ".class = '" . $classname . "'
                    AND
                        student_" . $current_year . ".status = 'continuing'                      
                    ORDER BY lname ASC
                ");
        }

        return response()->json([
            'data' => $data
        ]);
    }

    //Enter student results 
    public function enter_results_alevel(Request $req)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $class = $req->classname_buffer;
        $results_table = ($req->result_set_buffer) . "_" . $term . "_" . $year;
        $marks = $req->std_mark;
        $results = array();
        $std_id = $req->std_id;
        $paper = $req->paper_num_buffer;
        $subject = (($req->subject_buffer) . "_" . $paper);

        for ($i = 0; $i < count($std_id); $i++) {
            array_push($results, ['std_id' => $std_id[$i], 'mark' => $marks[$i]]);
        }

        foreach ($results as $result) {
            //Check if the record exists
            $exists = (DB::table('' . $results_table . '')->select('std_id', '' . $subject . '')->where('std_id', $result['std_id']))->first();

            if ($exists == '' || $exists == null || $exists == 'NULL') {
                //New Record for new student
                try {
                    DB::table('' . $results_table . '')->insert([
                        'std_id' => $result['std_id'],
                        'class' => $class,
                        '' . $subject . '' => $result['mark']
                    ]);
                } catch (Exception $e) {
                    info($e);
                }
                $response = 'New Record Inserted';
            } else {
                //Update for Existing
                try {
                    DB::table('' . $results_table . '')->where('std_id', $result['std_id'])->update([
                        'class' => $class,
                        '' . $subject . '' => $result['mark']
                    ]);
                } catch (Exception $e) {
                    info($e);
                }
                $response = 'Record Updated';
            }
        }
        return response($response);
    }

    public function print_marklist($class, $paper, $subject, $level)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $school_name = DB::table('school_details')->where('id', 1)->first();
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $data = DB::select("
                SELECT
                    student_" . $current_year . ".std_id,
                    student_" . $current_year . ".fname,
                    student_" . $current_year . ".mname,
                    student_" . $current_year . ".lname
                FROM
                    student_" . $current_year . "
                WHERE
                    student_" . $current_year . ".class = '" . $class . "'
                AND
                    student_" . $current_year . ".status = 'continuing'
                ORDER BY lname ASC
            ");

        if ($level == 'O Level') {
            $html = '
                <style>
                    .container{
                        margin-top:-1.2cm;
                        margin-left:-0.7cm;
                        margin-right:-0.7cm;
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
                    td{
                        height:100px;
                    }
                </style>
                <div class="container">
                ';

            //Title Table
            $html .= "
        <table style='margin-bottom:; border:none !important;'>
            <tbody>
                <tr>
                <td style='border:none !important'><img style='width:70px;' src='" . public_path('images/' . $school_name->school_badge . '') . "'></td>
                    <td style='border:none !important; font-size:25px; text-align:center; text-transform:uppercase; font-weight:bold;'>" . (($school_name->school_name != null) ? $school_name->school_name : '') . "</td>
                    <td style='border:none !important'><img style='width:70px;' src='" . public_path('images/' . $school_name->school_badge . '') . "'></td>
                </tr>
            </tbody>
        </table>";


            for ($i = 0; $i < 2; $i++) {
                //Separator
                $html .= "
                    <div style='width:100%; height:1px; margin-bottom:2px; background:black;'></div>
                    ";
            }

            $html .= '
                        <p class="title">' . $class . ' ' . $subject . ' ' . (($class == 'Senior 6' || $class == 'Senior 5') ? $paper : '') . ' List Term ' . $term . ' ' . $year . '</p>

                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Score</th>
                                    <th>Competence</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>';

            $counter = 0;

            if (count($data) > 0) {
                foreach ($data as $d) {
                    $counter += 1;
                    $html .= '
                        <tr>
                            <td style="text-align:center;">' . $counter . '</td>
                            <td>' . $d->lname . ' ' . (($d->lname == null || $d->mname == 'NULL' || $d->mname == '') ? '' : $d->mname) . ' ' . $d->fname . '</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    ';
                }
            } else {
                $html .= '
                    <tr>
                        <td colspan=5 style=" text-align:center;">
                            <img style="width:70px; height:70px;" src="' . public_path('/images/icon/empty_set.png') . '">
                        </td>
                    </tr>
                ';
            }

            $html .= '   </tbody>
                        </table>
                    </div>
                ';
        } elseif ($level == 'A Level') {
            $html = '
            <style>
                .container{
                    margin-top:-1.2cm;
                    margin-left:-0.7cm;
                    margin-right:-0.7cm;
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
                <div class="container">';

            //Title Table
            $html .= "
            <table style='margin-bottom:; border:none !important;'>
                <tbody>
                    <tr>
                    <td style='border:none !important'><img style='width:70px;' src='" . public_path('images/' . $school_name->school_badge . '') . "'></td>
                        <td style='border:none !important; font-size:25px; text-align:center; text-transform:uppercase; font-weight:bold;'>" . (($school_name->school_name != null) ? $school_name->school_name : '') . "</td>
                        <td style='border:none !important'><img style='width:70px;' src='" . public_path('images/' . $school_name->school_badge . '') . "'></td>
                    </tr>
                </tbody>
            </table>";


            for ($i = 0; $i < 2; $i++) {
                //Separator
                $html .= "
                        <div style='width:100%; height:1px; margin-bottom:2px; background:black;'></div>
                        ";
            }

            $html .= '    <p class="title">' . $class . ' ' . $subject . ' ' . (($class == 'Senior 6' || $class == 'Senior 5') ? $paper : '') . ' List Term ' . $term . ' ' . $year . '</p>

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
            if (count($data) > 0) {
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
            } else {
                $html .= '
                    <tr>
                        <td colspan=3 style=" text-align:center;">
                            <img style="width:70px; height:70px;" src="' . public_path('/images/icon/empty_set.png') . '">
                        </td>
                    </tr>
                ';
            }

            $html .= '   </tbody>
                    </table>
                </div>
            ';
        }

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');
        return $pdf->stream('' . $class . '_ClassList');
    }

    public function enter_results_olevel(Request $req)
    {
        $ids = $req->std_ids;
        $marks = $req->std_marks;
        $topic = $req->topic_buffer;
        $competence = $req->competence;
        $remark = $req->remark;
        $subject = $req->subject_buffer;
        $class = $req->classname_buffer;
        $table = $req->result_set_buffer;
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $results_table = $table . "_" . $term . "_" . $year;
        $results = array();

        for ($i = 0; $i < count($ids); $i++) {
            array_push($results, ['std_id' => $ids[$i], 'mark' => $marks[$i], 'topic' => $topic, 'competence' => $competence[$i], 'remark' => $remark[$i]]);
        }

        foreach ($results as $r) {
            $exists = DB::table('' . $results_table . '')->where(['std_id' => $r['std_id'], 'class' => $class, 'topic' => $topic, 'subject' => $subject])->exists();
            if ($exists != 1) {
                //Insert into the DB
                try {
                    DB::table('' . $results_table . '')->insert([
                        'std_id' => $r['std_id'],
                        'class' => $class,
                        'subject' => $subject,
                        'topic' => $topic,
                        'remark' => $r['remark'],
                        'competence' => $r['competence'],
                        'score' => $r['mark']
                    ]);
                    $response = "Successfully Inserted Marks";
                } catch (Exception $e) {
                    info($e);
                    $response = "Failed to Insert marks";
                }
            } else {
                //Update the current record
                try {
                    DB::table('' . $results_table . '')->where(['std_id' => $r['std_id'], 'class' => $class, 'topic' => $r['topic'], 'subject' => $subject])->update([
                        'topic' => $r['topic'],
                        'remark' => $r['remark'],
                        'competence' => $r['competence'],
                        'score' => $r['mark']
                    ]);
                    $response = "Successfully Updated Marks";
                } catch (Exception $e) {
                    info($e);
                    $response = "Failed to update records!";
                }
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
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        try {
            $data = DB::table('student_' . $current_year . '')->select(['std_id', 'lname', 'mname', 'fname'])->where(['class' => $class, 'status' => 'continuing'])->get();
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


    //New PRint Code
    public function oreports_print_fpdf($table, $term, $year, $std_ids)
    {
        $tables = array();
        $table_collect = explode(',', $table);
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;

        //check if keys exists
        function key_check($key, $array)
        {
            $check = array_key_exists($key, $array);

            if ($check == 1) {
                $value = $array[$key];
            } else {
                $value = "";
            }
            return $value;
        }

        $class = DB::table('student_' . $current_year . '')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();

        $data = DB::select("
                SELECT 
                    *
                FROM
                    student_" . $current_year . "                    
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

        //Create PDF instance
        $pdf = new Fpdf();

        foreach($data as $d){
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 20);

            //HEADER SECTION
            $pdf->Image(public_path("school_badge/".$school->school_badge.""),2,2,30,30);
            $pdf->Cell(190, 0, ''.strtoupper($school->school_name).'',0,1,'C');

            //Student Image here
            if($d->image == null){
                if($d->gender == 'Female'){
                    $pdf->Image(public_path("images/static/female.jpg"),178,2,30,30);
                }else{
                    $pdf->Image(public_path("images/static/male.jpg"),178,2,30,30);
                }
            }else{
                $pdf->Image(public_path("images/student_photos/".$d->image.""),178,2,30,30);
            }
            
            $pdf->SetFont('Arial', '', 13);
            $pdf->Cell(190, 11, ''.ucfirst($school->address).'',0,1,'C');
            $pdf->Cell(190, 0, ''.ucfirst($school->contact).'',0,1,'C');
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->Cell(190, 12, '"'.ucfirst($school->motto).'"',0,1,'C');
            $pdf->Cell(190, 0, '------------------------------------------------------------------------------------------------------------------',0,1,'C');
            $pdf->Cell(190, 2, '------------------------------------------------------------------------------------------------------------------',0,1,'C');

            $pdf->Ln('1');

            //Student Details
            $pdf->SetFont('Arial', 'B', 11);

            //Student Name
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(30,7,'Student Name:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(90,7,''.$d->lname.' '.(($d->mname == null)?"":$d->mname).' '.$d->fname.'',1,0,'L',false);

            //Student Class
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(30,7,'Student Class:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(40,7,''.$d->class.'',1,1,'C',false);

            $pdf->Ln('1');

            //Student Section
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(30,7,'Section:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20,7,''.$d->section.'',1,0,'C',false);

            //Student Gender
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(20,7,'Gender:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(20,7,''.$d->gender.'',1,0,'C',false);

            //Student House
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(20,7,'House:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(30,7,''.$d->house.'',1,0,'C',false);

            //Student ID
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(10,7,'ID:',1,0,'C',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(40,7,'CHSN - '.$d->std_id.'',1,1,'C',false);

            $pdf->Ln('1');

            //Term
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(15,7,'Term:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(10,7,''.$term.'',1,0,'C',false);

            //Year
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(15,7,'Year:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(15,7,''.$current_year.'',1,0,'C',false);

            //Student Combination
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(30,7,'Combination:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(30,7,''.$d->combination.'',1,0,'C',false);

            //Student Fees
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(35,7,'Fees Balance:',1,0,'C',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(40,7,'',1,1,'C',false);

            //Student SchoolPay
            /*
            $pdf->SetFillColor(140,140,140);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(140,140,140);
            $pdf->Cell(30,10,'School Pay:',1,0,'L',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->Cell(30,10,'',1,0,'C',false);
            */

            $pdf->Ln('2');

            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFillColor(0,0,0);
            //RESULTS AREA
            $pdf->Cell(190,10,'STUDENT RESULTS',0,1,'C',true);

            $pdf->Ln('1');

            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFillColor(135,135,135);

            //RESULTS TABLE
            $pdf->Cell(30,9,'Subject',1,0,'C',true);
            $pdf->Cell(40,9,'Topic',1,0,'C',true);
            $pdf->Cell(50,9,'Competence',1,0,'C',true);
            $pdf->Cell(50,9,'Remark',1,0,'C',true);
            $pdf->Cell(20,9,'Score',1,1,'C',true);

            //DEAL WITH RESULTS
            $std_results = DB::select('
            select group_concat(distinct std_id) as std_id, group_concat(distinct ' . $table . '.class) as class, group_concat(distinct subject) as subject_title, group_concat(subject) as subject, group_concat(topic) as topic, group_concat(remark) as remark, group_concat(distinct competence) as competence, group_concat(score) as score  from ' . $table . ' where class="Senior 1" and std_id="' . $d->std_id . '" group by subject;
            ');

            $pdf->SetTextColor(0,0,0);
            $pdf->SetDrawColor(0,0,0);

            foreach ($std_results as $r) {
                $subject_label = $r->subject_title;
                $subjects = explode(',', $r->subject);
                $mark = explode(',', $r->score);
                $competence = explode(',', $r->competence);
                $remark = explode(',', $r->remark);
                $topic = explode(',', $r->topic);
                $rowspan = count($subjects);

                $pdf->SetFontSize(10);
                for ($i = 0; $i < $rowspan; $i++) {
                    if($i == 0){
                        $y = $pdf->GetY();
                        $x = $pdf->GetX();
                        $pdf->MultiCell(30,(5*$rowspan),''.$subject_label.'',1,'C',false);
                        $pdf->SetXY($x+30,$y);
                        $pdf->MultiCell(40,5,''.key_check($i, $topic).'',1,'C',false);
                        $pdf->SetXY($pdf->GetX(),$pdf->GetY()+20);
                    }
                }
            }            

            $pdf->Ln('10');

            $pdf->SetTextColor(0,0,0);
            $pdf->SetDrawColor(0,0,0);
            //COMMENT SECTION
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(0,0,0);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(190, 7, 'Remark',1,1,'C',true);
            $pdf->SetTextColor(0,0,0);
            
            //Headteacher
            $pdf->Cell(30, 20, 'HeadTeacher',1,0,'C');
            $pdf->Cell(30, 20, '',0,0,'C');
            $pdf->Cell(10, 20, '',0,0,'C');
            $pdf->Cell(20, 20, 'DOS',1,0,'C');
            $pdf->Cell(30, 20, '',0,0,'C');
            $pdf->Cell(10, 20, '',0,0,'C');
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(20, 20, "ClassTeacher",1,0,'C');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Image(public_path("images/signatures/".$signature_hm.""),43,$pdf->GetY(),28,12);
            $pdf->Image(public_path("images/signatures/".$signature_hm.""),105,$pdf->GetY(),28,12);
            $pdf->Cell(40, 10, '',1,1,'C');
            $pdf->Cell(30, 10, '',0,0,'C');
            $pdf->Cell(40, 10, '',1,0,'C');
            $pdf->Cell(20, 10, '',0,0,'C');
            $pdf->Cell(40, 10, '',1,0,'C');
            $pdf->Cell(20, 10, '',0,0,'C');
            $pdf->Cell(40, 10, '',1,0,'C');

            info("2 ".$pdf->GetY());

        }



        $pdf->Output('',''.$class->class.'_Reports');
        exit;
    }

    //OLD Print Code
    public function oreports_print($table, $term, $year, $std_ids)
    {
        $tables = array();
        $table_collect = explode(',', $table);
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        /*
        //check if keys exists
        function key_check($key, $array){
            $check = array_key_exists($key, $array);

            if($check == 1){
                $value = $array[$key];
            }else{
                $value = "";
            }
            return $value;
        }
        */

        //Deal with the tables here
        foreach ($table_collect as $t) {
            array_push($tables, $t);
        }

        $class = DB::table('student_' . $current_year . '')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();

        $data = DB::select("
                SELECT 
                    *
                FROM
                    student_" . $current_year . "                    
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

                .results_table{
                    
                }

                .results_table th, .results_table td{
                    padding:5px !important;
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

            $total = array();

            //DEAL WITH RESULTS
            $std_results = DB::select('
                select group_concat(distinct std_id) as std_id, group_concat(distinct ' . $table . '.class) as class, group_concat(distinct subject) as subject_title, group_concat(subject) as subject, group_concat(topic) as topic, group_concat(remark) as remark, group_concat(distinct competence) as competence, group_concat(score) as score  from ' . $table . ' where class="Senior 1" and std_id="' . $d->std_id . '" group by subject;
            ');

            $html .= '
                    <table class="results_table" style="width:100%;">
                        <thead>
                            <tr style="background:black; color:white;">
                                <th>Subject</th>
                                <th>Topic</th>
                                <th>Competence</th>
                                <th>Remark on Competence</th>
                                <th>Score</th>
                                <th>Initials</th>
                            </tr>
                        </thead>
                        <tbody>
            ';

            foreach ($std_results as $r) {
                $subject_label = $r->subject_title;
                $subjects = explode(',', $r->subject);
                $mark = explode(',', $r->score);
                $competence = explode(',', $r->competence);
                $remark = explode(',', $r->remark);
                $topic = explode(',', $r->topic);
                $rowspan = count($subjects);

                for ($i = 0; $i < $rowspan; $i++) {
                    if ($i == 0) {
                        $html .= "<tr>
                                    <td style='text-align:center;' rowspan=" . $rowspan . ">" . $subject_label . "</td>
                                    <td>" . key_check($i, $topic) . "</td>
                                    <td>" . key_check($i, $competence) . "</td>
                                    <td>" . key_check($i, $remark) . "</td>
                                    <td style='text-align:center;'>" . key_check($i, $mark) . "</td>
                                    <td></td>
                                </tr>";
                    } else {
                        $html .= "<tr>
                                    <td>" . key_check($i, $topic) . "</td>
                                    <td>" . key_check($i, $competence) . "</td>
                                    <td>" . key_check($i, $remark) . "</td>
                                    <td style='text-align:center;'>" . key_check($i, $mark) . "</td>
                                    <td></td>
                                </tr>";
                    }
                }
            }


            //Close the Results Table here
            $html .= '</tbody>
            </table>
            ';

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

            //Sample Average, Delete after
            $avg = 1;

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
            <table class="remarks-table" style="width:100%;">
                <thead>
                    <tr>
                        <th colspan=6>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:10px; width:10%; text-align:center;" rowspan="2">HeadTeacher</td>
                        <td style="width:20%; text-align:center;">';
            if ($signature_hm == null) {
                $html .= '';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_hm . '">';
            }

            $html .= '
                        </td>
                        <td style="padding:10px; width:10%; text-align:center;" rowspan="2">Director Of Studies</td>
                        <td style="width:20%; text-align:center;">';
            if ($signature_dos == '') {
                $html .= ' ';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_dos . '">';
            }
            $html .= '
                        </td>
                        <td style="padding:10px; width:10%; text-align:center;" rowspan="2">Class Teacher</td>
                        <td style="width:30%; text-align:center;"></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">' . $this->hm_comment(round($avg, 1)) . '</td>
                        <td style="font-weight:bold;">' . $this->dos_comment(round($avg, 1)) . '</td>
                        <td></td>
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
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //Deal with the table counter here
        foreach (explode(',', $table) as $t) {
            array_push($tables, $t);
        }

        $class = DB::table('student_' . $current_year . '')->select('class')->whereIn('std_id', explode(',', $std_ids))->first();

        //Signatures
        $signature_hm = DB::table('signature')->select('signature')->where('signatory', 'head-teacher')->value('signature');
        $signature_dos = DB::table('signature')->select('signature')->where('signatory', 'dos')->value('signature');

        //School Details
        $school = DB::table('school_details')->select('*')->where('id', 1)->first();

        $data = DB::select("
            SELECT 
                *
            FROM
                student_" . $current_year . "                    
            WHERE
                std_id
            IN
                (" . $std_ids . ")
        ");

        function std_marks($table, $std_id, $subject)
        {
            return DB::table('' . $table . '')->select('' . $subject . '')->where('std_id', $std_id)->value('' . $subject . '');
        }

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
                /*

                $html .= '
                    <div>
                        <img class="student_pic" src="' . public_path('/') . 'images/student_photos/' . $d->image . '">
                    </div>';
                    */

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
                                    $grade = explode('-', $this->sub_ict($subject_avg))[0];
                                    $points = explode('-', $this->sub_ict($subject_avg))[1];

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
                        <th colspan=6>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:10px; width:10%; text-align:center;" rowspan="2">HeadTeacher</td>
                        <td style="width:20%; text-align:center;">';
            if ($signature_hm == null) {
                $html .= '';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_hm . '">';
            }

            $html .= '
                        </td>
                        <td style="padding:10px; width:10%; text-align:center;" rowspan="2">Director Of Studies</td>
                        <td style="width:20%; text-align:center;">';
            if ($signature_dos == '') {
                $html .= ' ';
            } else {
                $html .= '<img class="signature" src="' . public_path('/') . 'images/signatures/' . $signature_dos . '">';
            }
            $html .= '
                        </td>
                        <td style="padding:10px; width:10%; text-align:center;" rowspan="2">Class Teacher</td>
                        <td style="width:30%; text-align:center;"></td>
                    </tr>
                    <tr>
                        <td></td>
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

        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function three_papers($three_paper)
    {
        $p1 = $three_paper[0];
        $p2 = $three_paper[1];
        $p3 = $three_paper[2];

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
        } elseif (
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

        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function four_papers($four_papers)
    {
        $p1 = $four_papers[0];
        $p2 = $four_papers[1];
        $p3 = $four_papers[2];
        $p4 = $four_papers[3];

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

        $std_grade = $grade . '-' . $points;
        return $std_grade;
    }

    function sub_ict($sub_ict)
    {
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

    function one_paper($one_paper)
    {
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

    function get_grade($value)
    {
        if ($value >= 85 and $value <= 100) {
            $grade = "D1";
        } elseif ($value >= 80 and $value <= 84) {
            $grade = "D2";
        } elseif ($value >= 75 and $value <= 79) {
            $grade = "C3";
        } elseif ($value >= 70 and $value <= 74) {
            $grade = "C4";
        } elseif ($value >= 65 and $value <= 69) {
            $grade = "C5";
        } elseif ($value >= 60 and $value <= 64) {
            $grade = "C6";
        } elseif ($value >= 50 and $value <= 59) {
            $grade = "P7";
        } elseif ($value >= 40 and $value <= 49) {
            $grade = "P8";
        } elseif ($value >= 0 and $value <= 39) {
            $grade = "F9";
        } else {
            $grade = "F9";
        }
        return $grade;
    }

    function dos_comment($descriptor)
    {
        if ($descriptor >= 0.1 and $descriptor <= 1.9) {
            $comment = "More effort and extensive revision needed.";
        } elseif ($descriptor >= 2.0 and $descriptor <= 2.4) {
            $comment = "Promising results, don't relax.";
        } elseif ($descriptor >= 2.5 and $descriptor <= 3.0) {
            $comment = "Good performance, keep it up.";
        }

        return $comment;
    }

    function hm_comment($descriptor)
    {
        if ($descriptor >= 0.1 and $descriptor <= 1.9) {
            $comment = "There is room for improvement, keep trying.";
        } elseif ($descriptor >= 2.0 and $descriptor <= 2.4) {
            $comment = "A Good performance.";
        } elseif ($descriptor >= 2.5 and $descriptor <= 3.0) {
            $comment = "Thank you, don't relax.";
        }

        return $comment;
    }

    //O'Level Marklist
    public function olevel_marklist_index()
    {
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

        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $table_name = DB::table('results_table')->where(['term' => $term, 'year' => $year, 'level' => 'O Level'])->get();

        return view('results.olevel_marklist', compact('classes', 'subjects', 'table_name'));
    }

    //Fetch marklist results
    public function fetch_olevel_marklist_result(Request $req)
    {
        $class = $req->classname;
        $subject = $req->subject;
        $topic = $req->topic;
        $result_table = $req->table_name;
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $table = $result_table . "_" . $term . "_" . $year;
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $data = DB::select("
            SELECT
                fname,
                lname,
                mname,
                " . $table . ".subject as subject,
                " . $table . ".competence as competence,
                " . $table . ".score as score,
                " . $table . ".remark as remark,
                " . $table . ".topic as topic
            FROM
                student_" . $current_year . "
            RIGHT OUTER JOIN
                " . $table . "
            ON
                student_" . $current_year . ".std_id = " . $table . ".std_id
            WHERE
                " . $table . ".class = '" . $class . "'
            AND
                topic  = '" . $topic . "'
            AND
                subject = '" . $subject . "'
        ");

        return response($data);
    }

    public function print_marklist_olevel($table, $class, $subject, $topic)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $school_name = DB::table('school_details')->where('id', 1)->first();

        $data = DB::select("
                    SELECT
                        fname,
                        lname,
                        mname,
                        " . $table . ".subject as subject,
                        " . $table . ".competence as competence,
                        " . $table . ".score as score,
                        " . $table . ".remark as remark,
                        " . $table . ".topic as topic
                    FROM
                        student_" . $current_year . "
                    RIGHT OUTER JOIN
                        " . $table . "
                    ON
                        student_" . $current_year . ".std_id = " . $table . ".std_id
                    WHERE
                        " . $table . ".class = '" . $class . "'
                    AND
                        topic  = '" . $topic . "'
                    AND
                        subject = '" . $subject . "'
                ");

        $html = "
                <style>
                    .paper_margin{
                        margin-top:-1cm;
                        margin-left:-0.5cm;
                        margin-right:-0.5cm;
                    }
        
                    table{
                        width:100%;
                    }
        
                    table, th, td{
                        border: black 1px solid;
                        border-collapse:collapse;
                    }
        
                    th, td{
                        font-size:17px;
                    }
        
                    th, td {
                        padding:7px;
                    }
                </style>
        
                <div class='paper_margin'>
                ";

        //Title Table
        $html .= "
                            <table style='margin-bottom:; border:none !important;'>
                                <tbody>
                                    <tr>
                                    <td style='border:none !important'><img style='width:70px;' src='" . public_path('images/' . $school_name->school_badge . '') . "'></td>
                                        <td style='border:none !important; font-size:25px; text-align:center; text-transform:uppercase; font-weight:bold;'>" . (($school_name->school_name != null) ? $school_name->school_name : '') . "</td>
                                        <td style='border:none !important'><img style='width:70px;' src='" . public_path('images/' . $school_name->school_badge . '') . "'></td>
                                    </tr>
                                </tbody>
                            </table>";


        for ($i = 0; $i < 2; $i++) {
            //Separator
            $html .= "
                    <div style='width:100%; height:1px; margin-bottom:2px; background:black;'></div>
                    ";
        }

        //Title
        $html .= "<div style='text-align:center;'><p style='font-size:20px;'>" . $class . " <strong style='text-decoration:underline;'>" . $subject . "</strong>  MarkList Term <strong>" . $term . " " . $year . "</strong> </p></div>";

        //Resultant table
        $html .= "   <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Score</th>
                                <th>Competence</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>";

        if (count($data) > 0) {
            $counter = 0;
            foreach ($data as $d) {
                //Results
                $html .= "<tr>
                        <td>" . ($counter += 1) . "</td>
                        <td style='text-transform:capitalize;'>" . $d->lname . " " . ($d->mname == null ? '' : $d->mname) . " " . $d->fname . "</td>
                        <td>" . $d->score . "</td>
                        <td>" . $d->competence . "</td>
                        <td>" . $d->remark . "</td>
                </tr>";
            }
        } else {
            //Empty set
            $html .= "<tr>
                <td colspan=5 style='text-align:center;'><img style='width:100px;' class='fluid' src='" . public_path("/images/icon/empty_set.png") . "'></td>
            </tr>";
        }


        //Close table
        $html .= "
                </tbody>
                </table>
        </div>";

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');
        return $pdf->stream('' . $class . ' ' . $subject . ' Marklist');
    }
}
