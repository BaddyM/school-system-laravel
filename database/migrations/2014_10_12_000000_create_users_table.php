<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('dept');
            $table->string('gender');
            $table->string('image')->nullable(true);
            $table->boolean('is_admin')->default(0);
            $table->boolean('is_super_admin')->default(0);
            $table->boolean('is_teacher')->default(0);
            $table->boolean('is_bursar')->default(0);
            $table->boolean('is_librarian')->default(0);
            $table->boolean('is_student')->default(0);
            $table->boolean('is_active')->default(0);
            $table->boolean('email_verified')->default(0);
            $table->string('password')->nullable(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('users');
    }
}
