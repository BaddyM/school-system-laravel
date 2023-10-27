<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarksheetController extends Controller{
    public function alevel(){
        return view('marksheet.alevel');
    }
}
