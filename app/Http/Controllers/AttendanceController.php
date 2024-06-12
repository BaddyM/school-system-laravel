<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

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
                        student_".$current_year.".class as class,
                        student_".$current_year.".std_id as std_id,
                        student_attend_" . $term . "_" . $year . ".status as status,
                        student_attend_" . $term . "_" . $year . ".note as note
                    FROM
                        student_".$current_year."
                    LEFT OUTER JOIN
                        student_attend_" . $term . "_" . $year . "
                    ON
                        student_".$current_year.".std_id = student_attend_" . $term . "_" . $year . ".std_id
                    WHERE
                        student_".$current_year.".class = '" . $class . "'
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
                        student_".$current_year.".class as class,
                        student_".$current_year.".std_id as std_id
                    FROM
                        student_".$current_year."
                    WHERE
                        student_".$current_year.".class = '" . $class . "'
                    AND
                        student_".$current_year.".status = 'continuing'
                ");
            }
        } else {
            $date = array();
        }

        return response($data);
    }

    public function fetch_staff(Request $req)
    {
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
                CREATE TABLE IF NOT EXISTS staff_attend_" . $term . "_" . $year . " 
                (id int(10) primary key auto_increment, std_id varchar(255), status varchar(255), note text, created_at datetime, updated_at datetime)
            ");

            //Get the data for the students here
            $data = DB::select("
                    SELECT 
                        fname,
                        lname,
                        mname,
                        staff.id as std_id,
                        staff_attend_" . $term . "_" . $year . ".status as status,
                        staff_attend_" . $term . "_" . $year . ".note as note
                    FROM
                        staff
                    LEFT OUTER JOIN
                        staff_attend_" . $term . "_" . $year . "
                    ON
                        staff.id = staff_attend_" . $term . "_" . $year . ".std_id
                    WHERE
                        staff_attend_" . $term . "_" . $year . ".created_at
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
                        staff.id as std_id
                    FROM
                        staff
                    WHERE
                        status = 'continuing'
                ");
            }
        } else {
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

    public function save_staff_attendance(Request $req)
    {
        $id = $req->std_id;
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
            $exists = DB::table("staff_attend_" . $term . "_" . $year . "")->where(['std_id' => $result['std_id']])->whereBetween('created_at', [$min_time, $max_time])->exists();

            if ($exists != 1) {
                //Insert new record
                try {
                    DB::table("staff_attend_" . $term . "_" . $year . "")->insert([
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
                DB::table("staff_attend_" . $term . "_" . $year . "")->where(['std_id' => $result['std_id'], 'created_at' => $date])->update([
                    'status' => $result['status'],
                    'note' => $result['note'],
                    'updated_at' => now()
                ]);

                $response = "Attendance Update Successfull";
            }
        }

        return response($response);
    }

    //Collect the daily student attendance
    public function fetch_student_attendance()
    {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //check if exists today
        $min_time = date('Y-m-d 00:00:00', strtotime(now()));
        $max_time = date('Y-m-d 23:59:59', strtotime(now()));

        try {
            $present = DB::table('student_attend_' . $term . '_' . $year . '')->where('status', 'present')->whereBetween('created_at', [$min_time, $max_time])->count();
            $absent = DB::table('student_attend_' . $term . '_' . $year . '')->where('status', 'absent')->whereBetween('created_at', [$min_time, $max_time])->count();
            $total = DB::table('student_'.$current_year.'')->where('status', 'continuing')->count();

            return response()->json([
                'total' => $total,
                'present' => $present,
                'absent' => $absent
            ]);
        } catch (Exception $e) {
            //If Table doesn't Exist
            return response()->json([
                'total' => 0,
                'present' => 0,
                'absent' => 0
            ]);
        }
    }

    public function fetch_staff_attendance()
    {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //check if exists today
        $min_time = date('Y-m-d 00:00:00', strtotime(now()));
        $max_time = date('Y-m-d 23:59:59', strtotime(now()));

        try {
            $present = DB::table('staff_attend_' . $term . '_' . $year . '')->where('status', 'present')->whereBetween('created_at', [$min_time, $max_time])->count();
            $absent = DB::table('staff_attend_' . $term . '_' . $year . '')->where('status', 'absent')->whereBetween('created_at', [$min_time, $max_time])->count();

            $total = DB::table('staff')->where('status', 'continuing')->count();

            return response()->json([
                'total' => $total,
                'present' => $present,
                'absent' => $absent
            ]);
        } catch (Exception $e) {
            //If Table doesn't Exist
            return response()->json([
                'total' => 0,
                'present' => 0,
                'absent' => 0
            ]);
        }
    }

    public function print_std_attendance($class, $from, $to)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;
        $school_name = DB::table('school_details')->where('id',1)->first();
        $current_year = (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $data = DB::select("
            SELECT 
                fname, 
                lname, 
                mname, 
                student_attend_" . $term . "_" . $year . ".status as status, 
                note, 
                student_attend_" . $term . "_" . $year . ".created_at as created_at 
            FROM 
                student_".$current_year." 
            RIGHT OUTER JOIN
                student_attend_" . $term . "_" . $year . " 
            ON 
                student_".$current_year.".std_id = student_attend_" . $term . "_" . $year . ".std_id  
            WHERE
                student_attend_" . $term . "_" . $year . ".created_at
            BETWEEN
                '" . date('Y-m-d', strtotime($from)) . "'
            AND
                '" . date('Y-m-d', strtotime($to)) . "'
            ORDER BY
                student_attend_" . $term . "_" . $year . ".created_at 
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

        //Title Table
        $html .= "
                    <table style='margin-bottom:; border:none !important;'>
                        <tbody>
                            <tr>
                            <td style='border:none !important'><img style='width:70px;' src='".public_path('images/'.$school_name->school_badge.'')."'></td>
                                <td style='border:none !important; font-size:25px; text-align:center; text-transform:uppercase; font-weight:bold;'>".(($school_name->school_name != null)?$school_name->school_name:'')."</td>
                                <td style='border:none !important'><img style='width:70px;' src='".public_path('images/'.$school_name->school_badge.'')."'></td>
                            </tr>
                        </tbody>
                    </table>";
        
        
        for($i=0; $i<2; $i++){
            //Separator
            $html .= "
            <div style='width:100%; height:1px; margin-bottom:2px; background:black;'></div>
            ";
        }

        //Title
        $html .= "<div style='text-align:center;'><p style='font-size:20px;'><strong>" . $class . "</strong> Attendance List From <strong style='text-decoration:underline;'>" . date('D, d M, Y', strtotime($from)) . "</strong> To <strong style='text-decoration:underline;'>" . date('D, d M, Y', strtotime($to)) . "</strong></p></div>";

        //Table
        $html .= "<table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>";

        if (count($data) > 0) {
            foreach ($data as $d) {
                $html .=        "<tr>
                                    <td style='text-transform:capitalize;'>" . $d->lname . " " . (($d->mname) ? $d->mname : "") . " " . $d->fname . "</td>
                                    <td style='text-align:center;'>" . date('D, d M, Y', (strtotime($d->created_at))) . "</td>
                                    <td style='text-align:center; text-transform:capitalize;'>" . ($d->status) . "</td>
                                    <td style='text-transform:capitalize;'>" . ($d->note) . "</td>
                                </tr>";
            }
        } else {
            $html .=        "<tr>
                                    <td colspan=4 style='text-align:center; font-weight:bold; color:red;'>Empty Set</td>
                                </tr>";
        }

        $html .= "       </tbody>
                </table>";

        $html .= "</div>";

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');
        return $pdf->stream('' . $class . ' Attenance List');
    }

    //Print staff attendance
    public function print_staff_attendance($from, $to)
    {
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $school_name = DB::table('school_details')->where('id',1)->first();

        $data = DB::select("
            SELECT 
                fname, 
                lname, 
                mname, 
                staff_attend_" . $term . "_" . $year . ".status as status, 
                note, 
                staff_attend_" . $term . "_" . $year . ".created_at as created_at 
            FROM 
                staff 
            RIGHT OUTER JOIN
                staff_attend_" . $term . "_" . $year . " 
            ON 
                staff.id = staff_attend_" . $term . "_" . $year . ".std_id  
            WHERE
                staff_attend_" . $term . "_" . $year . ".created_at
            BETWEEN
                '" . date('Y-m-d', strtotime($from)) . "'
            AND
                '" . date('Y-m-d', strtotime($to)) . "'
            ORDER BY
                staff_attend_" . $term . "_" . $year . ".created_at 
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

        //Title Table
        $html .= "
                    <table style='margin-bottom:; border:none !important;'>
                        <tbody>
                            <tr>
                            <td style='border:none !important'><img style='width:70px;' src='".public_path('images/'.$school_name->school_badge.'')."'></td>
                                <td style='border:none !important; font-size:25px; text-align:center; text-transform:uppercase; font-weight:bold;'>".(($school_name->school_name != null)?$school_name->school_name:'')."</td>
                                <td style='border:none !important'><img style='width:70px;' src='".public_path('images/'.$school_name->school_badge.'')."'></td>
                            </tr>
                        </tbody>
                    </table>";
        
        
        for($i=0; $i<2; $i++){
            //Separator
            $html .= "
            <div style='width:100%; height:1px; margin-bottom:2px; background:black;'></div>
            ";
        }

        //Title
        $html .= "<div style='text-align:center;'><p style='font-size:20px;'><strong>Staff</strong> Attendance List From <strong style='text-decoration:underline;'>" . date('D, d M, Y', strtotime($from)) . "</strong> To <strong style='text-decoration:underline;'>" . date('D, d M, Y', strtotime($to)) . "</strong></p></div>";

        //Resultant table
        $html .="   <table>
                        <thead>
                            <tr>
                                <th>Staff Name</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>";

        if (count($data) > 0) {
            foreach ($data as $d) {
                $html .=        "<tr>
                                    <td style='text-transform:capitalize;'>" . $d->lname . " " . (($d->mname) ? $d->mname : "") . " " . $d->fname . "</td>
                                    <td style='text-align:center;'>" . date('D, d M, Y', (strtotime($d->created_at))) . "</td>
                                    <td style='text-align:center; text-transform:capitalize;'>" . ($d->status) . "</td>
                                    <td style='text-transform:capitalize;'>" . ($d->note) . "</td>
                                </tr>";
            }
        } else {
            $html .=        "<tr>
                                    <td colspan=4 style='text-align:center; font-weight:bold; color:red;'>Empty Set</td>
                                </tr>";
        }

        $html .= "       </tbody>
                </table>";

        $html .= "</div>";

        $pdf = Pdf::loadHTML($html)->setOption('a4', 'portrait');
        return $pdf->stream('Staff Attenance List');
    }

    public function create_attendance_table_index()
    {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        //$staff = 

        return view('attendance.create');
    }

    public function create_attendance_table(Request $req)
    {
        //Get the active term
        $term = (DB::table('term')->select('term')->where('active', 1)->first())->term;
        $year =  (DB::table('term')->select('year')->where('active', 1)->first())->year;

        $value = $req->attendance;
        $table = $value . "_" . $term . "_" . $year;

        try {
            $exists = DB::table("" . $table . "")->exists();
            $response = "Table Already Exists!";
        } catch (Exception $e) {
            DB::statement("
                CREATE TABLE " . $table . " 
                    (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `std_id` varchar(255) DEFAULT NULL,
                        `status` varchar(255) DEFAULT NULL,
                        `note` text DEFAULT NULL,
                        `created_at` datetime DEFAULT NULL,
                        `updated_at` datetime DEFAULT NULL,
                        PRIMARY KEY (`id`)
                    )
            ");

            $response = "Table Created";
        }

        return response($response);
    }
}
