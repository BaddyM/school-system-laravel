<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;
use Yajra\DataTables\DataTables;

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
    public function select_students(Request $req) {
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
                        " . $results_table . ".".$subject_paper." as mark
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
        $subject = (($req->subject)."_".$paper);
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

        //info("Class = " . $class . ", subject = " . $subject . ", Result_table = " . $results_table);

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
}
