<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function view_index(){
        $staff = Staff::all();
        $positions = DB::table('position')->get();
        $status = DB::table('std_status')->get();
        $classes = DB::table('std_class')->get();
        return view('staff.staff',compact('staff','positions','status','classes'));
    }

    public function add_index(){
        $positions = DB::table('position')->get();
        return view('staff.add_staff',compact('positions'));
    }

    public function add_staff_details(Request $req){     
        $fname = strtolower($req->fname);
        $mname = strtolower($req->mname);
        $lname = strtolower($req->lname);
        $nin = $req->nin;
        $position = $req->position;
        $location = $req->location;
        $gender = $req->gender;
        $email = $req->staff_email;
        $contact = $req->contact;

        //Check if the user exists
        $exists = DB::table('staff')->where(['fname' => $fname, 'mname' => $mname, 'lname' => $lname])->exists();

        if(strlen($contact) != 10){
            $response = 'error: Contact is not valid';
        }elseif($email == null){
            $response = 'error: Add Email';
        }elseif($exists == 1){
            $response = 'error: Staff Exists';
        }else{
            //If all data is valid
            function save_to_db($fname, $lname, $mname, $position, $gender, $contact, $email, $filename, $nin, $location){
                return DB::table('staff')->insert([
                            'fname' => $fname,
                            'mname' => $mname,
                            'lname' => $lname,
                            'position' => $position,
                            'gender' => $gender,
                            'contact' => $contact,
                            'email' => $email,
                            'image' => $filename,
                            'nin' => $nin,
                            'location' => $location,
                            'status' => 'continuing',
                            'created_at' => now()
                        ]);
            }

            //Deal with the Image here
            if($req->image != null){
                //IF THE IMAGE IS SET
                $filename = strtolower(Str::random(16).'.'.$req->image->extension());
                //check if image directory exists
                $exists = file_exists(public_path('/images/staff'));

                if($exists != 1){
                    //Create the file if it doesn't exist
                    mkdir(public_path('/images/staff'),0777,true);

                    //Move the file to the directory
                    $req->image->move(public_path('/images/staff'), $filename);

                    //Save into the DB
                    save_to_db($fname, $lname, $mname, $position, $gender, $contact, $email, $filename, $nin, $location);
                }else{
                    $req->image->move(public_path('/images/staff'), $filename);
                    //Save into the DB
                    save_to_db($fname, $lname, $mname, $position, $gender, $contact, $email, $filename, $nin, $location);
                }
            }else{
                //IF THE IMAGE IS NOT SET
                save_to_db($fname, $lname, $mname, $position, $gender, $contact, $email, $filename = null, $nin, $location);
            }

            $response = 'success: Added Staff Records';
        }

        return response($response);
    }

    public function import_staff(Request $req){
        $file = $req->import_file->getClientOriginalName();
        $position = $req->position;
        $response = array();
        
        //Save the file temporarily
        $temp_import_exists = file_exists(public_path('temp_import'));

        if($temp_import_exists == 1){
            //Add the file temporarily there
            $req->import_file->move(public_path('temp_import'),$file);
        }else{
            //Create the directory temp_import
            mkdir(public_path('temp_import'),0777,true);

            //Add the file temporarily there
            $req->import_file->move(public_path('temp_import'),$file);
        }

        //READ THE CONTENTS OF THE FILE
        $path = public_path("temp_import/".$file."");
        $rows = SimpleExcelReader::create($path)->getRows();

        function save_to_db($fname, $mname, $lname, $gender, $image, $nin, $email, $subjects, $class, $location, $position, $contact){
            return DB::table('staff')->insert([
                        'fname' => $fname,
                        'mname' => $mname,
                        'lname' => $lname,
                        'gender' => $gender,
                        'image' => $image,
                        'nin' => $nin,
                        'email' => $email,
                        'subjects' => $subjects,
                        'class' => $class,
                        'location' => $location,
                        'status' => 'continuing',
                        'position' => $position,
                        'contact' => $contact,
                        'created_at' => now()
                    ]);
        }

        //Save Each row into the DB
        foreach($rows as $row){
            $fname = strtolower($row['fname']); 
            $mname = strtolower($row['mname']); 
            $lname = strtolower($row['lname']); 
            $gender = $row['gender'];
            $image = $row['image'];
            $nin = $row['nin'];
            $email = $row['email'];
            $subjects = $row['subjects'];
            $class = $row['class'];
            $location = $row['location'];
            $contact = $row['contact'];

            if($contact == null){
                $contact = '-';
            }
            
            //Check if staff exists
            $staff_exists = DB::table('staff')->where(['fname' => $fname, 'mname' => $mname, 'lname' => $lname])->exists();

            if($staff_exists == null){
                //Save Data
                save_to_db($fname, $mname, $lname, $gender, $image, $nin, $email, $subjects, $class, $location, $position, $contact);
                array_push($response, $lname." ".$mname." ".$fname." Uploaded");
            }else{
                array_push($response, $lname." ".$mname." ".$fname." Exists");
            }
        }

        //Delete the temporary file
        unlink($path);

        return response($response);
    }

    public function modal_data(Request $req){
        $id = $req->id;        
        $data = DB::table('staff')->where('id',$id)->get();
        return response($data);
    }
}
