<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('fname');
            $table->string('mname')->nullable(true);
            $table->string('lname');
            $table->string('position');
            $table->string('gender');
            $table->string('status');
            $table->string('contact');
            $table->string('email')->nullable(true);
            $table->string('image')->nullable(true);
            $table->string('nin')->nullable(true);
            $table->string('location')->nullable(true);
            $table->string('subjects')->nullable(true);
            $table->string('class')->nullable(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
}
