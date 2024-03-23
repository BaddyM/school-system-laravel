<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTableTable extends Migration {

    public function up() {
        Schema::create('results_table', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->string('level');
            $table->tinyInteger('term')->default(0);
            $table->string('year');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('results_table');
    }
}
