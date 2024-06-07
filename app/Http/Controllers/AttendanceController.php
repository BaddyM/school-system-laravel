<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Exception;

class AttendanceController extends Controller
{
    public function student_index(){
        $class = DB::table('std_class')->get();
        return view('attendance.student',compact('class'));
    }

    public function staff_index(){
        return view('attendance.staff');
    }

    public function fetch_students(Request $req){
        $class = $req->classname;
        //$data = Student::select('fname','lname','mname','std_id')->where(['class' => $class, 'status' => 'continuing'])->get();

        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //Create attendance table if it doesn't exist
        DB::statement("
            CREATE TABLE IF NOT EXISTS student_attend_".$term."_".$year." 
            (id int(10) primary key auto_increment, std_id varchar(255), status varchar(255), note text, created_at datetime, updated_at datetime)
        ");

        $data = DB::select("
                    SELECT 
                        fname,
                        lname,
                        mname,
                        student.class as class,
                        student.std_id as std_id,
                        student_attend_".$term."_".$year.".status as status
                    FROM
                        student
                    LEFT OUTER JOIN
                        student_attend_".$term."_".$year."
                    ON
                        student.std_id = student_attend_".$term."_".$year.".std_id
                    WHERE
                        student.class = '".$class."'

                ");

        return response($data);
    }

    public function save_student_attendance(Request $req){
        $id = $req->std_id;
        $class = $req->classname;
        $results = array();

        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        foreach($id as $i){
            $std_id = 'note_'.$i.'';
            $note = $req->$std_id;
            array_push($results, ['std_id' => $i, 'status' => $req->$i, 'note' => $note]);
        }

        //check if exists today
        $min_time = date('Y-m-d 00:00:00',strtotime(now()));
        $max_time = date('Y-m-d 23:59:59',strtotime(now()));

        foreach($results as $result){
            $exists = DB::table("student_attend_".$term."_".$year."")->where(['std_id' => $result['std_id']])->whereBetween('created_at',[$min_time, $max_time])->exists();

            if($exists != 1){
                //Insert new record
                try{
                    DB::table("student_attend_".$term."_".$year."")->insert([
                        'std_id' => $result['std_id'],
                        'status' => $result['status'],
                        'note' => $result['note'],
                        'created_at' => now()
                    ]);
    
                    $response = "Attendance Successfull";
                }catch(Exception $e){
                    info($e);
                    $response = "Failed!";
                }

            }else{
                //Update the current record
                DB::table("student_attend_".$term."_".$year."")->where('std_id',$result['std_id'])->update([
                    'status' => $result['status'],
                    'note' => $result['note'],
                    'updated_at' => now()
                ]);

                $response = "Attendance Update Successfull";
            }
        }
        
        return response($response);
    }
}
