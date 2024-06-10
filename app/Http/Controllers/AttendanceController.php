<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
    public function student_index()
    {
        $class = DB::table('std_class')->get();
        return view('attendance.student', compact('class'));
    }

    public function staff_index()
    {
        return view('attendance.staff');
    }

    public function fetch_students(Request $req)
    {
        $class = $req->classname;
        $date = $req->date;

        if ($date != null) {
            //Get the active term
            $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
            $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

            //check if exists today
            $min_time = date('Y-m-d 00:00:00', strtotime($date));
            $max_time = date('Y-m-d 23:59:59', strtotime($date));

            //Create attendance table if it doesn't exist
            DB::statement("
            CREATE TABLE IF NOT EXISTS student_attend_" . $term . "_" . $year . " 
            (id int(10) primary key auto_increment, std_id varchar(255), status varchar(255), note text, created_at datetime, updated_at datetime)
        ");

            //Get the data for the students here
            $data = DB::select("
                    SELECT 
                        fname,
                        lname,
                        mname,
                        student.class as class,
                        student.std_id as std_id,
                        student_attend_" . $term . "_" . $year . ".status as status,
                        student_attend_" . $term . "_" . $year . ".note as note
                    FROM
                        student
                    LEFT OUTER JOIN
                        student_attend_" . $term . "_" . $year . "
                    ON
                        student.std_id = student_attend_" . $term . "_" . $year . ".std_id
                    WHERE
                        student.class = '" . $class . "'
                    AND
                        student_attend_" . $term . "_" . $year . ".created_at
                    BETWEEN 
                        '" . $min_time . "'
                    AND
                        '" . $max_time . "'
                ");

            //If empty, fetch empty set
            if (count($data) == 0) {
                $data = DB::select("
                    SELECT 
                        fname,
                        lname,
                        mname,
                        student.class as class,
                        student.std_id as std_id
                    FROM
                        student
                    WHERE
                        student.class = '" . $class . "'
                ");
            }
        }else{
            $date = array();
        }

        return response($data);
    }

    public function save_student_attendance(Request $req)
    {
        $id = $req->std_id;
        $class = $req->classname;
        $date = date('Y-m-d H:i:s', strtotime($req->update_date));
        $results = array();

        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        foreach ($id as $i) {
            $std_id = 'note_' . $i . '';
            $note = $req->$std_id;
            array_push($results, ['std_id' => $i, 'status' => $req->$i, 'note' => $note]);
        }

        //check if exists today
        $min_time = date('Y-m-d 00:00:00', strtotime($date));
        $max_time = date('Y-m-d 23:59:59', strtotime($date));

        foreach ($results as $result) {
            $exists = DB::table("student_attend_" . $term . "_" . $year . "")->where(['std_id' => $result['std_id']])->whereBetween('created_at', [$min_time, $max_time])->exists();

            if ($exists != 1) {
                //Insert new record
                try {
                    DB::table("student_attend_" . $term . "_" . $year . "")->insert([
                        'std_id' => $result['std_id'],
                        'status' => $result['status'],
                        'note' => $result['note'],
                        'created_at' => $date
                    ]);

                    $response = "Attendance Successfull";
                } catch (Exception $e) {
                    info($e);
                    $response = "Failed!";
                }
            } else {
                //Update the current record
                DB::table("student_attend_" . $term . "_" . $year . "")->where(['std_id' => $result['std_id'], 'created_at' => $date])->update([
                    'status' => $result['status'],
                    'note' => $result['note'],
                    'updated_at' => now()
                ]);

                $response = "Attendance Update Successfull";
            }
        }

        return response($response);
    }

    public function fetch_student_attendance()
    {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //check if exists today
        $min_time = date('Y-m-d 00:00:00', strtotime(now()));
        $max_time = date('Y-m-d 23:59:59', strtotime(now()));

        $today = DB::table('student_attend_' . $term . '_' . $year . '')->whereBetween('created_at', [$min_time, $max_time])->count();
        $total = Student::where('status', 'continuing')->count();
    }

    public function print_std_attendance($class, $from, $to){
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $data = DB::select("
            SELECT 
                fname, 
                lname, 
                mname, 
                student_attend_".$term."_".$year.".status as status, 
                note, 
                student_attend_".$term."_".$year.".created_at as created_at 
            FROM 
                student 
            RIGHT OUTER JOIN
                student_attend_".$term."_".$year." 
            ON 
                student.std_id = student_attend_".$term."_".$year.".std_id  
            WHERE
                student_attend_".$term."_".$year.".created_at
            BETWEEN
                '".date('Y-m-d',strtotime($from))."'
            AND
                '".date('Y-m-d',strtotime($to))."'
            ORDER BY
                student_attend_".$term."_".$year.".created_at 
            DESC
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

        //Title
        $html .= "<div style='text-align:center;'><p style='font-size:20px;'><strong>".$class."</strong> Attendance List From <strong style='text-decoration:underline;'>".date('D, d M, Y', strtotime($from))."</strong> To <strong style='text-decoration:underline;'>".date('D, d M, Y', strtotime($to))."</strong></p></div>";

        //Table
        $html .= "<table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>";
        
        foreach($data as $d){
            $html .=        "<tr>
                                <td>".$d->lname." ".(($d->mname)?$d->mname:"")." ".$d->fname."</td>
                                <td style='text-align:center;'>".date('D, d M, Y',(strtotime($d->created_at)))."</td>
                            </tr>";
        }
                        
        $html .="       </tbody>
                </table>";

        $html .= "</div>";

        info($from);

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');
        return $pdf->stream(''.$class.' Attenance List');
    }
}
