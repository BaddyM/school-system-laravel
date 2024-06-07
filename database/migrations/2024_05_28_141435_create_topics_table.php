<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration {
    public function up(){
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('class');
            $table->string('subject');
            $table->text('topic');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('topics');
    }
}
