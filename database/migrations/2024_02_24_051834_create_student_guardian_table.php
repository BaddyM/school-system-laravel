<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentGuardianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_guardian', function (Blueprint $table) {
            $table->id('guard_id');
            $table->integer('std_id');
            $table->string('guard_fname')->nullable(true);
            $table->string('guard_lname')->nullable(true);
            $table->string('occupation')->nullable(true);
            $table->string('nin')->nullable(true);
            $table->string('contact')->nullable(true);
            $table->string('relationship')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_guardian');
    }
}
