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

    function addStudentIndex()
    {
        $classes = DB::table('std_class')->select('class')->get();
        $streams = DB::table('class_stream')->select('stream')->get();

        return view('student.add', compact('classes', 'streams'));
    }

    function addStudentToDB(Request $req)
    {
        //Student variables
        $fname = $req->fname;
        $lname = $req->lname;
        $mname = $req->mname;
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
            $guard_fname = 'NULL';
        } elseif ($guard_lname == '') {
            $guard_lname = 'NULL';
        } elseif ($occupation == '') {
            $occupation = 'NULL';
        } elseif ($nin == '') {
            $nin = 'NULL';
        } elseif ($contact == '') {
            $contact = 'NULL';
        } elseif ($relationship == '') {
            $relationship = 'NULL';
        } elseif ($dob == '') {
            $dob = 'NULL';
        } elseif ($house == '') {
            $house = 'NULL';
        }

        if ($dob != null) {
            $dob = date('y-m-d', strtotime($req->dob));
        }


        //Save into the DB
        //Create a student ID
        $std_id = $year . "" . random_int(1000, 5000);

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
            $file_check = file_exists(public_path('images/student_photos/' . $year . ''));

            if ($file_check != 1) {
                mkdir(public_path('images/student_photos/' . $year . ''), 0754);
            }

            //Upload to the specified directory
            $req->std_image->move(public_path('images/student_photos/' . $year . ''), $file_name);
        } else {
            //If the Image is null, assign one
            if ($gender == 'Male') {
                $file_name = 'male.jpg';
            } elseif ($gender == 'Female') {
                $file_name = 'female.jpg';
            }
        }

        info("Year_of_Entry = " . $year);

        try {

            //Add to the Student Table 
            Student::create(
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
                        'guard_fname' => $guard_fname,
                        'guard_lname' => $guard_lname,
                        'occupation' => $occupation,
                        'nin' => $nin,
                        'contact' => $contact,
                        'relationship' => $relationship
                    ]
                );
            }
        } catch (Exception $e) {
            info($e);
        }

        $std_details = array(
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
        );

        $parents_details = array(
            'std_id' => $std_id,
            'guard_fname' => $guard_fname,
            'guard_lname' => $guard_lname,
            'occupation' => $occupation,
            'nin' => $nin,
            'contact' => $contact,
            'relationship' => $relationship
        );

        //info(json_encode($std_details).'\n'.json_encode($parents_details));

        return response("Student Added Successfully");
    }

    function viewStudentIndex()
    {
        $status = DB::table('std_status')->select('status')->get();
        $classes = DB::table('std_class')->select('class')->get();
        $streams = DB::table('class_stream')->select('stream')->get();
        return view('student.view', compact('status', 'classes', 'streams'));
    }

    function fetchStudentData(Request $req)
    {
        $class_name = $req->classname;
        $category = $req->category;
        //info("Class_Name = ".$class_name.", category = ".$category);
        $data = Student::where([['class', $class_name], ['status', $category]])->get();

        //info($data);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($fetched) {
            })
            ->make(true);
    }

    function getDataForModal(Request $req)
    {
        $std_id = $req->std_id;
        $data = Student::where("std_id", $std_id)->first();
        return response()->json($data);
    }

    function updateStudentData(Request $req)
    {
        $std_id = $req->std_id;
        $fname = $req->fname;
        $lname = $req->lname;
        $mname = $req->mname;
        $class = $req->std_class;
        $combination = $req->combination;
        $fees = $req->fees;
        $registration = $req->std_reg;
        $requirements = $req->std_req;
        $house = $req->house;
        $level = $req->std_stream;
        $password = $req->password;
        $section = $req->section;
        $status = $req->std_status;
        $year = $req->year;
        $gender = $req->gender;

        if ($combination == null) {
            $combination = '-';
        } elseif ($registration == null) {
            $registration = 0;
        } elseif ($requirements == null) {
            $requirements = 0;
        } elseif ($fees == null) {
            $fees = 0;
        } elseif ($password == null) {
            $password = '-';
        }

        Student::where('std_id', $std_id)->update([
            'fname' => $fname,
            'lname' => $lname,
            'mname' => $mname,
            'class' => $class,
            'level' => $level,
            'section' => $section,
            'house' => $house,
            'year' => $year,
            'status' => $status,
            'password' => $password,
            'fees' => $fees,
            'gender' => $gender,
            'requirements' => $requirements,
            'registration' => $registration,
            'combination' => $combination
        ]);
    }

    public function import_index()
    {
        return view('student.import_student');
    }

    public function import_students(Request $req)
    {
        $file = $req->std_upload_file->getClientOriginalName();

        //Move the file temporarily
        $req->std_upload_file->move(public_path('temp_import'), $file);
        $path = (public_path('temp_import') . '/' . $file);

        $rows = SimpleExcelReader::create($path)->getRows();

        foreach ($rows as $row) {
            $fname = $row['fname'];
            $mname = $row['mname'];
            $lname = $row['lname'];
            $dob = date('y-m-d', strtotime($row['dob']));
            $class = $row['class'];
            $stream = $row['stream'];
            $house = $row['house'];
            $section = $row['section'];
            $image = $row['image'];
            $gender = $row['gender'];
            $year_of_entry = $row['year_of_entry'];
            $status = $row['status'];
            $combination = $row['combination'];
            $lin = $row['lin'];
            $residence = $row['residence'];
            $nationality = $row['nationality'];

            //Check nullability
            if ($mname == null) {
                $mname = 'NULL';
            } elseif ($dob == null) {
                $dob = 'NULL';
            } elseif ($house == null) {
                $house = 'NULL';
            } elseif ($image == null) {
                $image = 'NULL';
            } elseif ($combination == null) {
                $combination = 'NULL';
            } elseif ($lin == null) {
                $lin = 'NULL';
            } elseif ($residence == null) {
                $residence = 'NULL';
            } elseif ($nationality == null) {
                $nationality = 'NULL';
            }

            try {
                //Upload to the Student table
                $year = date('Y', strtotime(now()));
                $std_id = $year . "" . random_int(1000, 5000);

                Student::create([
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
            } catch (Exception $e) {
                info($e);
            }
        }

        //Delete the temporary file after upload
        unlink($path);

        return response("File Successfully Uploaded");
    }

    public function print_students($class, $status) {
        $data = Student::where(['class' => $class, 'status' => $status])->get();
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
                <h3 style='text-align:center;'>" . $class . " Student List</h3>

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
}