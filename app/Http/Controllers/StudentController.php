<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function addStudentIndex() {
        $classes = DB::table('std_class')->select('class')->get();
        $streams = DB::table('class_stream')->select('stream')->get();
        return view('student.add', compact('classes', 'streams'));
    }

    public function addStudentToDB(Request $req) {
        //Student variables
        $fname = ucfirst($req->fname);
        $lname = ucfirst($req->lname);
        $mname = ucfirst($req->mname);
        $house = $req->house;
        $dob = $req->dob;
        $std_class = $req->std_class;
        $std_stream = $req->std_stream;
        $combination = $req->combination;
        $file_name = $req->std_image;
        $section = $req->section;
        $gender = $req->gender;
        $year = date('Y', strtotime(now()));
        $lin = $req->lin;;
        $residence = $req->residence;
        $nationality = $req->nationality;

        //Parent/Guradian variables
        $guard_fname = $req->gfname_1;
        $guard_lname = $req->glname_1;
        $occupation = $req->occupation;
        $nin = $req->nin;
        $contact = $req->contact;
        $relationship = $req->relationship;

        if ($guard_fname == '') {
            $guard_fname = '';
        } elseif ($guard_lname == '') {
            $guard_lname = '';
        } elseif ($occupation == '') {
            $occupation = '';
        } elseif ($nin == '') {
            $nin = '';
        } elseif ($contact == '') {
            $contact = '';
        } elseif ($relationship == '') {
            $relationship = '';
        } elseif ($dob == '') {
            $dob = '';
        } elseif ($house == '') {
            $house = '';
        }

        if ($dob != null) {
            $dob = date('y-m-d', strtotime($req->dob));
        }


        //Save into the DB
        //Create a student ID
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $id_counter = count(DB::table('student_'.$current_year.'')->get());
        $std_id = $year . "" . (5000 + $id_counter);

        //Apply fixed values for combination and registration
        if ($combination == null) {
            $combination = 0;
        }

        //Upload the image
        if ($req->std_image != null) {
            //Create a 16bit filename
            //$file_name = strtolower(Str::random(16) . '.' . $req->std_image->extension());  

            //Create file_name as student ID
            $file_name = strtolower($std_id . '.' . $req->std_image->extension());

            //Create directory per year
            $file_check = file_exists(public_path('images/student_photos'));

            if ($file_check != 1) {
                mkdir(public_path('images/student_photos'), 0754);
            }

            //Upload to the specified directory
            $req->std_image->move(public_path('images/student_photos'), $file_name);
        } else {
            //If the Image is null, assign one
            if ($gender == 'Male') {
                $file_name = 'male.jpg';
            } elseif ($gender == 'Female') {
                $file_name = 'female.jpg';
            }
        }

        try {
            //Check if data exists
            //$exists = Student::where(['fname' => $fname, 'mname' => $mname, 'lname' => $lname])->exists();
            $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
            $exists = DB::table('student_'.$current_year.'')->where(['fname' => $fname, 'mname' => $mname, 'lname' => $lname])->exists();

            if($exists == null){
                //Add to the Student Table 
                DB::table('student_'.$current_year.'')->insert(
                    [
                        'std_id' => $std_id,
                        'fname' => $fname,
                        'mname' => $mname,
                        'lname' => $lname,
                        'dob' => $dob,
                        'class' => $std_class,
                        'stream' => $std_stream,
                        'house' => $house,
                        'section' => $section,
                        'image' => $file_name,
                        'year_of_entry' => $year,
                        'status' => 'continuing',
                        'gender' => $gender,
                        'combination' => $combination,
                        'password' => $std_id,
                        'lin' => $lin,
                        'residence' => $residence,
                        'nationality' => $nationality
                    ]
                );

                //Add to the Guardian/Parent table
                if ($guard_fname != 'NULL') {
                    DB::table('student_guardian')->insert(
                        [
                            'std_id' => $std_id,
                            'guard_fname' => ucfirst($guard_fname),
                            'guard_lname' => ucfirst($guard_lname),
                            'occupation' => $occupation,
                            'nin' => $nin,
                            'contact' => $contact,
                            'relationship' => $relationship
                        ]
                    );
                }
                
                $response = "Student Added Successfully";
            }else{
                $response = "Student Already Exists";
            }

        } catch (Exception $e) {
            info($e);
        }

        return response($response);
    }

    public function viewStudentIndex() {
        $status = DB::table('std_status')->select('status')->get();
        $classes = DB::table('std_class')->select('class')->get();
        $streams = DB::table('class_stream')->select('stream')->get();
        return view('student.view', compact('status', 'classes', 'streams'));
    }

    public function fetchStudentData(Request $req) {
        $class_name = $req->classname;
        $category = $req->category;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $data = DB::table('student_'.$current_year.'')->where(['class' => $class_name,'status' => $category])->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($fetched) {
            })
            ->make(true);
    }

    public function getDataForModal(Request $req) {
        $std_id = $req->std_id;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $data = DB::table('student_'.$current_year.'')->where("std_id", $std_id)->first();
        return response()->json($data);
    }

    public function updateStudentData(Request $req) {
        $std_id = $req->std_id;
        $fname = $req->fname;
        $lname = $req->lname;
        $mname = $req->mname;
        $class = $req->std_class;
        $combination = $req->combination;
        $lin = $req->lin;
        $nationality = $req->nationality;
        $house = $req->house;
        $stream = $req->std_stream;
        $password = $req->password;
        $section = $req->section;
        $status = $req->std_status;
        $year = $req->year;
        $gender = $req->gender;

        if ($combination == null) {
            $combination = '-';
        } elseif ($lin == null) {
            $lin = '';
        } elseif ($nationality == null) {
            $nationality = '';
        }elseif ($password == null) {
            $password = '-';
        }

        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

        try{
            DB::table('student_'.$current_year.'')->where('std_id', $std_id)->update([
                'fname' => ucfirst($fname),
                'lname' => ucfirst($lname),
                'mname' => ucfirst($mname),
                'class' => $class,
                'section' => $section,
                'house' => $house,
                'stream' => $stream,
                'year_of_entry' => $year,
                'status' => $status,
                'password' => $password,
                'nationality' => $nationality,
                'gender' => $gender,
                'lin' => $lin,
                'combination' => $combination
            ]);
        }catch(Exception $e){
            info($e);
        }
    }

    public function import_index() {
        $classes = DB::table('std_class')->select('class')->get();
        $streams = DB::table('class_stream')->select('stream')->get();
        return view('student.import_student',compact('classes','streams'));
    }

    public function import_students(Request $req) {
        $file = $req->std_upload_file->getClientOriginalName();
        $class = $req->std_class;
        $stream = $req->std_stream;

        //Move the file temporarily to a temp location
        $req->std_upload_file->move(public_path('temp_import'), $file);
        $path = (public_path('temp_import') . '/' . $file);

        $rows = SimpleExcelReader::create($path)->getRows();

        //Exists array
        $response = array();

        foreach ($rows as $row) {
            $fname = ucfirst($row['fname']);
            $mname = ucfirst($row['mname']);
            $lname = ucfirst($row['lname']);
            if(($row['dob']) != null){
                $dob = date('y-m-d', strtotime($row['dob']));
            }else{
                $dob = date('y-m-d', strtotime(now()));
            }            
            //$class = $row['class'];
            //$stream = $row['stream'];
            $house = $row['house'];
            $section = $row['section'];
            $image = $row['image'];
            $gender = $row['gender'];
            $year_of_entry = $row['year_of_entry'];
            $combination = $row['combination'];
            $lin = $row['lin'];
            $residence = $row['residence'];
            $nationality = $row['nationality'];

            //Check nullability
            if ($mname == null) {
                $mname = '';
            } elseif ($dob == null) {
                $dob = '';
            } elseif ($house == null) {
                $house = '';
            } elseif ($image == null) {
                $image = '';
            } elseif ($combination == null) {
                $combination = '';
            } elseif ($lin == null) {
                $lin = '';
            } elseif ($residence == null) {
                $residence = '';
            } elseif ($nationality == null) {
                $nationality = '';
            }

            $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

            try {
                //Check if data exists
                $exists = DB::table('student_'.$current_year.'')->where(['fname' => $fname, 'mname' => $mname, 'lname' => $lname])->exists();

                if($exists == null){
                    //Upload to the Student table
                    $year = date('Y', strtotime(now()));
                    $id_counter = count(DB::table('student_'.$current_year.'')->get());
                    $std_id = $year . "" . (5000 + $id_counter);

                    DB::table('student_'.$current_year.'')->insert([
                        'std_id' => $std_id,
                        'fname' => $fname,
                        'lname' => $lname,
                        'mname' => $mname,
                        'dob' => $dob,
                        'class' => $class,
                        'stream' => $stream,
                        'house' => $house,
                        'section' => $section,
                        'image' => $image,
                        'gender' => $gender,
                        'year_of_entry' => $year_of_entry,
                        'status' => 'continuing',
                        'combination' => $combination,
                        'password' => $std_id,
                        'lin' => $lin,
                        'residence' => $residence,
                        'nationality' => $nationality
                    ]);

                    array_push($response, $lname." ".$mname." ".$fname." Uploaded");
                }else{
                    array_push($response, $lname." ".$mname." ".$fname." Exists");
                }
                
            } catch (Exception $e) {
                info($e);
            }
        }

        //Delete the temporary file after upload
        unlink($path);

        return response($response);
    }

    public function print_students($class, $status) {
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $data = DB::table('student_'.$current_year.'')->where(['class' => $class, 'status' => $status])->get();
        $row_num = 0;

        //Student's list HTML
        $html = "
            <style>
                table, th, td {
                    border: 1px solid black;
                    border-collapse: collapse;
                  }
                th{
                    width:107px;
                }

                th{
                    background:grey;
                    color:white;
                }

                .body{
                    margin-top:-1cm;
                    margin-left:-1cm;
                    margin-right:-1cm;
                }

                .header{
                    display:flex;
                    justify-content:space-between;
                }

                .header img{
                    width:70px;
                }

            </style>

            <div class='body'>
                <h3 style='text-align:center;'>" . $class . " ".strtoupper($status)." Student List</h3>

                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Class</th>
                            <th>Stream</th>
                            <th>Status</th>
                            <th>Year Of Entry</th>
                            <th>Combination</th>
                        </tr>
                    </thead>

                    <tbody>";

        foreach ($data as $d) {
            $row_num++;
            if ($d->combination == null) {
                $d->combination = '-';
            }
            $html .= "
                <tr>
                    <td style='text-align:center;'>" . $row_num. "</td>
                    <td style='text-align:center;'>" . $d->std_id . "</td>
                    <td>" . $d->fname . "</td>
                    <td>" . $d->mname . "</td>
                    <td>" . $d->lname . "</td>
                    <td>" . $d->class . "</td>
                    <td>" . $d->stream . "</td>
                    <td>" . $d->status . "</td>
                    <td style='text-align:center;'>" . $d->year_of_entry . "</td>
                    <td style='text-align:center;'>" . $d->combination . "</td>
                </tr>
            ";
        }

        $html .= "  </tbody>
                </table>

                <h4 style='color:red; text-align:center; font-weight:bold; margin-top:0.5cm; font-size:16px;'>
                    The End
                </h4>
            </div>
        ";

        $check_data = count($data);

        if($check_data != 0){
            $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
        }else{
            $pdf = PDF::loadHTML('<h3 style="color:red; text-align:center;">Empty Selection!</h3>')->setPaper('a4', 'landscape');
        }

        return $pdf->stream();
    }

    public function fetch_std_records(Request $req){
        $std_id = $req->std_id;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $data = DB::table('student_'.$current_year.'')->where('std_id',$std_id)->first();
        return response()->json([
            'data' => $data
        ]);
    }

    public function update_std_image(Request $req){
        $std_id = $req->std_img_id;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
        
        $original_record = DB::table('student_'.$current_year.'')->where('std_id',$std_id)->first();
        $std_image = $original_record->image;

        //First delete the original file
        if($std_image == null || $std_image == ' ' || $std_image == 'NULL' || $std_image == 'male.jpg' || $std_image == 'female.jpg'){
            
        }else{
            unlink(public_path('images/student_photos/'.$original_record->image.''));
        }

        try{
            $file_name = strtolower($std_id . '.' . $req->std_image->extension());
            //Insert new filename
            $req->std_image->move(public_path('images/student_photos'),$file_name);

            //Update the DB
            $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
            DB::table('student_'.$current_year.'')->where('std_id',$std_id)->update([
                'image' => $file_name
            ]);

        }catch(Exception $e){
            info($e);
        }

        return response("Image updated succesfully");
    }

    public function disable_student(Request $req){
        $std_id = $req->std_id;
        $status = $req->status;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

        try{
            DB::table('student_'.$current_year.'')->where('std_id',$std_id)->update([
                'status' => $status
            ]);
        }catch(Exception $e){
            info($e);
        }
        return response("Student Updated Successfully");
    }

    public function student_status_index(){
        $classes = DB::table('std_class')->select('class')->get();
        $status = DB::table('std_status')->select('status')->get();
        return view('student.std_status',compact('classes','status'));
    }

    public function display_std_status(Request $req){
        $class = $req->classname;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $data = DB::table('student_'.$current_year.'')->where('class',$class)->get();

        return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
    }

    //Update Student Status
    public function update_std_status(Request $req){
        $std_list = implode(',',$req->selected);
        $status = $req->std_status;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

        try{
            DB::update('
                UPDATE
                    student_'.$current_year.'
                SET
                    status = "'.$status.'"
                WHERE 
                    std_id
                IN('.$std_list.')
            ');
            $response = "Student Status Updated";
        }catch(Exception $e){
            info($e);
            $response = "There was an Error!";
        }

        return response($response);
    }

    //Promotion
    public function promote_index(){
        $classes = DB::table('std_class')->select('class')->get();
        $streams = DB::table('class_stream')->select('stream')->get();
        $academic_year = DB::table('term')->select('year')->distinct()->get();

        return view('student.promote', compact('classes','streams', 'academic_year'));
    }

    public function fetch_promote_students(Request $req){
        $class = $req->classname;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $table = "student_".$year;

        $data = DB::table(''.$table.'')->where(['class' => $class, 'status' => 'continuing'])->get();
        return response($data);        
    }

    public function promote_students(Request $req){
        $ids = $req->std_id;
        $class = $req->classname;
        $next_year = $req->year;

        //Current Term and Year
        $current_year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //Create table if it doesn't exist
        DB::statement("
            CREATE TABLE IF NOT EXISTS student_".$next_year."
            (
                `std_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `fname` varchar(255) NOT NULL,
                `mname` varchar(255) DEFAULT NULL,
                `lname` varchar(255) NOT NULL,
                `dob` text DEFAULT '0',
                `class` varchar(255) NOT NULL,
                `stream` varchar(255) DEFAULT NULL,
                `house` varchar(255) DEFAULT NULL,
                `section` varchar(255) DEFAULT NULL,
                `image` varchar(255) NOT NULL,
                `gender` varchar(255) NOT NULL,
                `year_of_entry` int(11) NOT NULL,
                `status` varchar(255) DEFAULT NULL,
                `combination` varchar(255) DEFAULT NULL,
                `password` varchar(255) DEFAULT NULL,
                `lin` varchar(255) DEFAULT NULL,
                `residence` varchar(255) DEFAULT NULL,
                `nationality` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`std_id`)
            )
        ");

        foreach($ids as $id){
            //Get the Data
            $new_data = DB::table("student_".$current_year."")->where(['std_id' => $id, 'status' => 'continuing'])->first();
            $exists = DB::table("student_".$next_year."")->where('std_id', $id)->exists();

            //Add new Data if null
            if($exists != 1){
                $std_id = $new_data->std_id;
                $fname = $new_data->fname;
                $lname = $new_data->lname;
                $mname = $new_data->mname;
                $dob = $new_data->dob;
                $stream = $new_data->stream;
                $house = $new_data->house;
                $section = $new_data->section;
                $image = $new_data->image;
                $gender = $new_data->gender;
                $year_of_entry = $new_data->year_of_entry;
                $status = $new_data->status;
                $combination = $new_data->combination;
                $password = $new_data->password;
                $lin = $new_data->lin;
                $residence = $new_data->residence;
                $nationality = $new_data->nationality;
                $created_at = $new_data->created_at;
                $updated_at = $new_data->updated_at;

                DB::table("student_".$next_year."")->insert([
                    'std_id' => $std_id,
                    'fname' => $fname,
                    'mname' => $mname,
                    'lname' => $lname,
                    'dob' => $dob,
                    'class' => $class,
                    'stream' => $stream,
                    'house' => $house,
                    'section' => $section,
                    'image' => $image,
                    'gender' => $gender,
                    'year_of_entry' => $year_of_entry,
                    'status' => $status,
                    'combination' => $combination,
                    'password' => $password,
                    'lin' => $lin,
                    'residence' => $residence,
                    'nationality' => $nationality,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ]);

                $response = "Records Added";
            }else{
                //If yes, Update
                DB::table("student_".$next_year."")->where('std_id',$id)->update([
                    'class' => $class,
                    'updated_at' => now()
                ]);

                $response = "Some Records Exist";
            }
        }        

        return response($response);
    }

}
