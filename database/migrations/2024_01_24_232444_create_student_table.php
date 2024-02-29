<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTable extends Migration {

    public function up() {
        Schema::create('student', function (Blueprint $table) {
            $table->id('std_id');
            $table->string('fname');
            $table->string('mname')->nullable(true);
            $table->string('lname');
            $table->dateTime('dob')->nullable(true);
            $table->string('class');
            $table->string('stream')->nullable(true);
            $table->string('house')->nullable(true);
            $table->string('section')->nullable(true);
            $table->string('image');
            $table->string('gender');
            $table->integer('year_of_entry');
            $table->string('status')->nullable(true);
            $table->string('combination')->nullable(true);
            $table->string('password')->nullable(true);
            $table->string('lin')->nullable(true);
            $table->string('residence')->nullable(true);
            $table->string('nationality')->nullable(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('student');
    }
    
}
