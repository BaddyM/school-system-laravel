<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SettingController extends Controller
{
    //SCHOOL DETAILS
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

    //TERM
    public function term_index(){
        $term_list = DB::select("
            SELECT
                *
            FROM
                term
            ORDER BY
                id 
            DESC
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

    public function add_term(Request $req){
        $term = $req->add_term;
        $year = $req->add_year;

        //Check if term exists
        $exists = DB::table('term')->where(['term'=>$term,'year'=>$year])->exists();

        if($exists == 1){
            $response = 'Term Already Exists';
        }else{
            try{
                DB::table('term')->insert([
                    'term'=>$term,
                    'year'=>$year,
                    'active'=>0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }catch(Exception $e){
                info($e);
            }

            $response = 'Term Added Successfully';
        }
        return response($response);
    }

    public function change_term(Request $req){
        $new_term = $req->term;
        $new_year = $req->year;

        //Change old term first
        try{
            DB::table('term')->where('active',1)->update(['active'=>0]);
        }catch(Exception $e){
            info($e);
        }

        //Update to new Term
        try{
            DB::table('term')->where(['term'=>$new_term, 'year'=>$new_year])->update(['active'=>1]);
            $response = 'Term Updated';
        }catch(Exception $e){
            info($e);
        }

        return response($response);
    }

    public function delete_term(Request $req){
        $id = $req->delete_id;

        //Don't delete active term
        $active = DB::table('term')->where(['id'=>$id, 'active'=>1])->exists();

        if($active == 1){
            $response = 'Error: Can\'t Delete Active Term';
        }else{
            //Delete inactive term
            try{
                DB::table('term')->where('id',$id)->delete();
                $response = 'Term Deleted';
            }catch(Exception $e){
                info($e);
            }
        }

        return response($response);
    }

    //SUBJECTS
    public function subjects_index()
    {
        $olevel = DB::select("
            SELECT
                *
            FROM
                subjects
            WHERE
                level = 'O Level'
            ORDER BY
                id
            DESC
        ");

        $alevel = DB::select("
            SELECT
                *
            FROM
                subjects
            WHERE
                level = 'A Level'
            ORDER BY
                id
            DESC
        ");

        return view('settings.subjects', compact('olevel', 'alevel'));
    }

    //Add Subject
    public function add_subject(Request $req){
        $name = strtoupper($req->subject_name);
        $level = $req->level;
        $paper = $req->papers;

        //Check if that subject exists
        $exists = DB::table('subjects')->where(['name'=>$name,'level'=>$level,'paper'=>$paper])->exists();

        if($exists == 1) {
            $response = "".$name." Already Exists";
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

    //Add subsidiary
    public function add_subsidiary(Request $req){
        $subs = $req->subs;
        
        foreach($subs as $sub){
            $level = 'A Level';

            if($sub == 'SubICT'){
                $paper = 2;
            }else{
                $paper = 1;
            }

            $exists = DB::table('subjects')->where(['name'=>$sub,'level'=>$level,'paper'=>$paper])->exists();

            if($exists == 1){
                $response = "".$sub." Already Exists";
            }else{
                DB::table('subjects')->insert([
                    'name' => $sub,
                    'level' => $level,
                    'paper' => $paper
                ]);
                $response = 'Added Subsidiary';
            }
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

    //RESULT TABLES
    public function results_table_index() {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active',1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active',1)->first())->year;   

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

    //SIGNATURES
    public function signature_index(){
        $data = DB::table('signature')->select('*')->get();
        return view('settings.signatures',compact('data'));
    }

    public function upload_signature(Request $req){
        $signatory = $req->signatory;
        $signature = $signatory.'.'.$req->signature->extension();
        $exists = file_exists(public_path('images/signatures'));
        $file_check = DB::table('signature')->select('signature')->where('signatory',$signatory)->exists('signature');

        if($exists == 1){
            //If Directory Exists
            //Check if file exists
            if($file_check == 1){
                //Delete the old file
                $sign = DB::table('signature')->select('signature')->where('signatory',$signatory)->value('signature');
                unlink(public_path('images/signatures/'.$sign.''));

                //Add the new files
                $req->signature->move(public_path('images/signatures'), $signature);
                //Update the DB
                DB::table('signature')->where('signatory',$signatory)->update([
                    'signatory' => $signatory,
                    'signature' => $signature,
                    'updated_at' => now()
                ]);
            }else{
                //Add the new files
                $req->signature->move(public_path('images/signatures'), $signature);

                //Insert into DB
                DB::table('signature')->insert([
                    'signatory' => $signatory,
                    'signature' => $signature,
                    'created_at' => now()
                ]);
            }

        }else{
            //If directory doesn't exist
            //Create the directory
            mkdir(public_path('images/signatures'));

            //Add the new files
            $req->signature->move(public_path('images/signatures'), $signature);

            //Insert into DB
            DB::table('signature')->insert([
                'signatory' => $signatory,
                'signature' => $signature,
                'created_at' => now()
            ]);
        }
    }

    public function delete_signature(Request $req){
        $id = $req->delete_id;
        $signature = DB::table('signature')->select('signature')->where('id',$id)->value('signature');
        //Delete the file
        unlink(public_path('images/signatures/'.$signature.''));
        //Delete from the DB
        DB::table('signature')->where('id',$id)->delete();       
    }

    //TEACHER INITIALS
    public function teacher_initials_index(){
        $data = DB::table('initials')->select('*')->orderBy('id','desc')->get();
        $subjects = DB::table('subjects')->distinct()->select('name')->get();
        $classes = DB::table('std_class')->select('class')->get();
        return view('settings.teacher_initials',compact('data','subjects','classes'));
    }

    public function teacher_initials_save(Request $req){
        $subject = $req->subject;
        $class = $req->classname;
        $teacher_name = $req->teacher_name;
        $initials = $req->initials;

        //Save into the DB
        try{
            DB::table('initials')->insert([
                'subject' => $subject,
                'class' => $class,
                'teacher_name' => $teacher_name,
                'initials' => $initials,
                'created_at' => now()
            ]);
        }catch(Exception $e){
            info($e);
        }
    }

    public function delete_initials(Request $req){
        $id = $req->id;
        DB::table('initials')->where('id',$id)->delete();
    }

    public function show_initials(Request $req){
        $id = $req->id;
        $data = DB::table('initials')->where('id',$id)->get();
        return response()->json([
            'data'=>$data
        ]);
    }

    public function update_initials(Request $req){
        $id = $req->update_initials_id;
        $teacher_name = $req->teacher_name_edit;
        $class = $req->classname_edit;
        $subject = $req->subject_edit;
        $initials = $req->initials_edit;

        DB::table('initials')->where('id',$id)->update([
            'teacher_name'=>$teacher_name,
            'class'=>$class,
            'subject'=>$subject,
            'initials'=>$initials
        ]);
    }

    //STUDENT STATUS
    public function status_list_index(){
        $data = DB::table('std_status')->select("*")->get();
        return view('settings.std_status',compact('data'));
    }

    public function status_list_add(Request $req){
        $status = strtolower($req->add_status);        
        $check = DB::table('std_status')->where('status',$status)->exists();

        if($check == 1){
            $response = 'Status Exists';
        }else{
            try{
                DB::table('std_status')->insert([
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $response = 'Added Status';
            }catch(Exception $e){
                info($e);
            }
        }        
        return response($response);
    }

    public function delete_status(Request $req){
        $id = $req->delete_status;
        DB::table('std_status')->where('id',$id)->delete();
        $response = 'Deleted Status';
        return response($response);
    }

    //Classes
    public function classes_index(){
        $classes = DB::table('std_class')->orderBy('id','desc')->get();
        return view('settings.std_class',compact('classes'));
    }

    public function add_class(Request $req){
        $class = $req->classname;
        $level = $req->level;

        //Check if Class exists
        $old_class = DB::table('std_class')->where('class',$class)->exists();
        
        if($old_class != 1){
            DB::table('std_class')->insert([
                'class' => $class,
                'level'=>$level,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $response = "Added Data successfully!";
        }else{
            $response = "Class Exists!";
        }

        return response($response);
    }

    public function delete_class(Request $req){
        $id = $req->delete_id;
        try{
            DB::table('std_class')->where('id',$id)->delete();
            $response = "Class Deleted";
        }catch(Exception $e){
            info($e);
            $response = "Failed to Delete Class!";
        }
        return response($response);
    }

    //Streams
    public function stream_index(){
        $streams = DB::table('class_stream')->orderBy('id','desc')->get();
        return view('settings.streams',compact('streams'));
    }

    public function add_stream(Request $req){
        $stream = $req->stream;

        //Check if Stream exists
        $old_stream = DB::table('class_stream')->where('stream',$stream)->exists();
        
        if($old_stream != 1){
            DB::table('class_stream')->insert([
                'stream' => $stream,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $response = "Added Data successfully!";
        }else{
            $response = "Stream Exists!";
        }

        return response($response);
    }

    public function delete_stream(Request $req){
        $id = $req->delete_id;
        try{
            DB::table('class_stream')->where('id',$id)->delete();
            $response = "Stream Deleted";
        }catch(Exception $e){
            info($e);
            $response = "Failed to Delete Stream!";
        }
        return response($response);
    }

    //Users
    public function user_index(){
        $users = User::where('is_super_admin','!=',1)->get();
        $position = DB::table('position')->get();
        $departments = DB::table('department')->get();
        return view('settings.users',compact('users','position','departments'));
    }

    public function fetch_user(Request $req){
        $id = $req->user_id;
        $data = User::where('id',$id)->first();
        return response($data);
    }

    public function update_user(Request $req){
        $id = $req->user_update_id;
        info("Update the user");

        //Check Active
        if($req->active == "on"){
            $active = 1;
        }else{
            $active = 0;
        }

        //Check email verified
        if($req->verify_email == "on"){
            $email_verified = 1;
        }else{
            $email_verified = 0;
        }
        
        $username = $req->username;
        $email = $req->email;
        $gender = $req->gender;
        $priviledge = $req->priviledge;
        $dept = $req->department;

        if($priviledge == 'admin'){
            $is_admin = 1;
            $is_bursar = 0;
            $is_teacher = 0;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }elseif($priviledge == 'teacher'){
            $is_admin = 0;
            $is_bursar = 0;
            $is_teacher = 1;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }elseif($priviledge == 'bursar'){
            $is_admin = 0;
            $is_bursar = 1;
            $is_teacher = 0;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }elseif($priviledge == 'librarian'){
            $is_admin = 0;
            $is_bursar = 0;
            $is_teacher = 0;
            $is_librarian = 1;
            $is_super_admin = 0;
            $is_student = 0;
        }else{
            $is_admin = 0;
            $is_bursar = 0;
            $is_teacher = 0;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }

        $data = [
            'is_active' => $active,
            'email_verified' => $email_verified,
            'username' => $username,
            'email' => $email,
            'gender' => $gender,
            'dept' => $dept,
            'is_admin' => $is_admin,
            'is_librarian' => $is_librarian,
            'is_teacher' => $is_teacher,
            'is_super_admin' => $is_super_admin,
            'is_student' => $is_student,
            'is_bursar' => $is_bursar
        ];

        //If Password Field is Not null
        if($req->password != null || $req->password != ""){
            $password = Hash::make($req->password);
            $data['password'] = $password;
        }

        //info($data);

        try{
            //update the DB
            User::where('id',$id)->update($data);
            $response = "User Records Updated";
        }catch(Exception $e){
            $response = "User Update Failed";
        }

        return response($response);
    }

    public function update_image(Request $req){
        $id = $req->update_user_image_id;
        $image = Str::random().'.'.$req->user_image->extension();

        //Delete the old image
        $old_image = (User::where('id',$id)->first())->image;

        //Check if the old image isn't empty
        if($old_image != null){
            unlink(public_path('images/users/'.$old_image.''));
        }        

        //Update to the new Image
        try{
            User::where('id',$id)->update([
                'image' => $image
            ]);

            //Save the image
            $req->user_image->move(public_path('images/users'),$image);
        }catch(Exception $e){
            info("Failed to Save Image");
        }
    }

    public function add_user(Request $req){
        $username = $req->username;
        $email = $req->email;
        $pass = $req->password;
        $priviledge = $req->department;
        $gender = $req->gender;

        if($priviledge == 'admin'){
            $is_admin = 1;
            $is_bursar = 0;
            $is_teacher = 0;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }elseif($priviledge == 'teacher'){
            $is_admin = 0;
            $is_bursar = 0;
            $is_teacher = 1;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }elseif($priviledge == 'bursar'){
            $is_admin = 0;
            $is_bursar = 1;
            $is_teacher = 0;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }elseif($priviledge == 'librarian'){
            $is_admin = 0;
            $is_bursar = 0;
            $is_teacher = 0;
            $is_librarian = 1;
            $is_super_admin = 0;
            $is_student = 0;
        }else{
            $is_admin = 0;
            $is_bursar = 0;
            $is_teacher = 0;
            $is_librarian = 0;
            $is_super_admin = 0;
            $is_student = 0;
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'gender' => $gender,
            'dept' => $priviledge,
            'is_admin' => $is_admin,
            'is_librarian' => $is_librarian,
            'is_teacher' => $is_teacher,
            'is_super_admin' => $is_super_admin,
            'is_student' => $is_student,
            'is_bursar' => $is_bursar,
            'created_at' => Carbon::now()
        ];

        info($data);

        //Check Password
        if($pass != null){
            $password = Hash::make($pass);
            $data['password'] = $password;
        }

        try{
            $check_email = User::where('email', $email)->exists();

            if($check_email == 1){
                $response = "User Already Exists";
            }else{
                //Check Image
                if($req->user_image != null){
                    $image = Str::random().'.'.$req->user_image->extension();
                    $req->user_image->move(public_path('images/users/'),$image);
                    $data['image'] = $image;
                }

                User::insert($data);
                $response = "User Added Successfully";
            }
        }catch(Exception $e){
            info($e);
            $response = "Failed to Save Data";
        }

        return response($response);
    }
}


