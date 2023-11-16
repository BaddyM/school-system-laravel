<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;
use Yajra\DataTables\DataTables;

class StaffController extends Controller
{
    public function index(){
        return view('staff.staff');
    }

    public function staff_details(){
        $data = Staff::all();
        
        return DataTables::of($data)
        ->addIndexColumn()
        ->editColumn('created_at',function($created){
            return date('D M Y, H:i',strtotime($created->created_at));
        })
        ->editColumn('updated_at',function($created){
            return date('D M Y, H:i',strtotime($created->created_at));
        })
        ->make(true);
    }
}
