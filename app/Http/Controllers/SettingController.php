<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SettingController extends Controller
{
    public function school_settings_index()
    {
        $data = DB::select("
            SELECT
                *
            FROM
                school_details
        ");

        return view('settings.school_details', compact('data'));
    }

    public function school_settings(Request $req)
    {
        $school_name = $req->school_name;
        $motto = $req->school_motto;
        $address = $req->school_address;
        $contacts = $req->school_contacts;

        //Deal with the image here
        if ($req->school_badge != '' || $req->school_badge != null) {
            $badge = $req->school_badge->getClientOriginalName();

            //Check if directory exists
            $file_check = file_exists(public_path('school_badge'));

            if ($file_check != 1) {
                mkdir(public_path('school_badge'));
                $req->school_badge->move(public_path('school_badge'), $badge);
            } else {
                //Delete the previous files first
                $inner_files = glob(public_path('school_badge/*'));
                unlink($inner_files[0]);
                //Add the file to the directory
                $req->school_badge->move(public_path('school_badge'), $badge);
            }
        } else {
            $badge = 'NULL';
        }

        //Check nullability
        if ($school_name == '') {
            $school_name = 'NULL';
        }
        if ($contacts == '') {
            $contacts = 'NULL';
        }
        if ($motto == '') {
            $motto = 'NULL';
        }
        if ($address == '') {
            $address = 'NULL';
        }

        //Save to the DB
        $data = DB::select("
            SELECT
                *
            FROM
                school_details
        ");

        if (!empty($data)) {
            //If DB is not empty
            //info("DB not empty");

            DB::update("
                UPDATE
                    school_details
                SET
                    school_name = '" . $school_name . "',
                    motto = '" . $motto . "',
                    address = '" . $address . "',
                    contact = '" . $contacts . "',
                    school_badge = '" . $badge . "',
                    updated_at = NOW()
                WHERE
                    id = 1
            ");
        } else {
            //If DB is empty
            //info("DB is empty");

            DB::insert("
                INSERT INTO
                    school_details (school_name, motto, address, contact, school_badge, created_at, updated_at)
                VALUES
                    (
                        '" . $school_name . "',
                        '" . $motto . "',
                        '" . $address . "',
                        '" . $contacts . "',
                        '" . $badge . "',
                        NOW(),
                        NOW()
                    )
            ");
        }

        $new_data = DB::select("
            SELECT
                *
            FROM
                school_details
        ");

        return response($new_data);
    }

    public function term_index()
    {
        $term_list = DB::select("
            SELECT
                *
            FROM
                term
        ");

        $year = DB::select("
                SELECT
                    DISTINCT year
                FROM
                    term
            ");

        $term = DB::select("
                    SELECT
                        DISTINCT term
                    FROM
                        term
                ");

        return view('settings.term', compact('term', 'year', 'term_list'));
    }

    //Deal with subjects here
    public function subjects_index()
    {
        $olevel = DB::select("
            SELECT
                *
            FROM
                subjects
            WHERE
                level = 'O Level'
        ");

        $alevel = DB::select("
            SELECT
                *
            FROM
                subjects
            WHERE
                level = 'A Level'
        ");

        return view('settings.subjects', compact('olevel', 'alevel'));
    }

    //Add Subject
    public function add_subject(Request $req)
    {
        $name = $req->subject_name;
        $level = $req->level;
        $paper = $req->papers;

        if ($name == '' || $name == null) {
            $response = "Subject Is Empty";
        } else {
            try {
                //Save into the DB
                DB::insert("
                    INSERT INTO
                        subjects(`name`,`level`,`paper`,created_at, updated_at)
                    VALUES
                        ('" . $name . "','" . $level . "','" . $paper . "',NOW(),NOW())
                ");
            } catch (Exception $e) {
                info($e);
            }
            $response = "Subject Added Successfully";
        }

        return response($response);
    }

    //Delete Subject
    public function delete_subject(Request $req)
    {
        $id = $req->id;

        try {
            DB::delete("
                DELETE
                FROM
                    subjects
                WHERE
                    id=" . $id . "
            ");
        } catch (Exception $e) {
            info($e);
        }
        return response("Subject Deleted Successfully");
    }

    //Results tables
    public function results_table_index() {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active',1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active',1)->first())->year;   
        
        //info("Term = ".$term.", Term = ".$year);

        $olevel = DB::select("
            SELECT
                *
            FROM
                subjects
            WHERE
                level = 'O Level'
        ");

        $alevel = DB::select("
            SELECT
                *
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
                term = '".$term."'
            AND
                year = '".$year."'
        ");

        return view('settings.results_table', compact('olevel', 'alevel','results'));
    }

    //Create Results Table
    public function create_results_table(Request $req) {
        $table_name = $req->table_name;
        $table_heads = $req->subject_heads;
        $term = $req->term;
        $year = $req->year;
        $level = $req->std_level;

        //Get the active term
        $term = (DB::table('term')->select('term')->where('active',1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active',1)->first())->year; 

        $result_table = "" . $table_name . "_" . $term . "_" . $year . "";

        //Create Table Heads
        $headers = array();
        foreach ($table_heads as $header) {
            array_push($headers, "$header TEXT NULL");
        }

        $created_heads = implode(', ', $headers);

        //Check if table exists
        try {
            DB::statement("
                SELECT
                    * 
                FROM
                    " . $result_table . "
            ");
            $response = 'Table Already Exists';
        } catch (Exception $e) {
            try {
                //Create Resultant Table
                DB::statement("
                    CREATE TABLE
                        " . $result_table . "
                        (id int(10) primary key auto_increment, std_id varchar(255) null, class varchar(255),  " . $created_heads . ")
                ");

                //Add into the results table
                DB::insert("
                    INSERT INTO
                        results_table(`table_name`, `level`, `term`, `year`, `created_at`, `updated_at`)
                    VALUES
                        ('".$result_table."', '".$level."', '".$term."', '".$year."', NOW(), NOW())
                ");

            } catch (Exception $e) {
                info($e);
            }
            $response = 'Table Created Successfuly';
        }

        return response($response);
    }

    //Delete results table
    public function delete_results_table(Request $req){
        $table_id = $req->table_id;
        $table_name = (DB::table('results_table')->select('table_name')->where('id',$table_id)->first())->table_name;
        //info("Table_id = ".$table_id.", table_name = ".$table_name);
        
        try{
            //Drop the table
            DB::statement("
                DROP
                TABLE
                    ".$table_name."
            ");
            $response = 'Table Deleted Successfully';
        }catch(Exception $e){
            info($e);
            $response = 'Failed To Delete Table';
        }

        try{
            //Remove from results_table
            DB::delete("
                DELETE
                FROM
                    results_table
                WHERE
                    id = ".$table_id."
            ");
            $response = 'Table Deleted Successfully';
        }catch(Exception $e){
            info($e);
            $response = 'Failed To Delete Table';
        }

        
        return $response;
    }
}
