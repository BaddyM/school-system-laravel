<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {

    public function run() {
        // \App\Models\User::factory(10)->create();
        //Staff::factory(10)->create();
        Student::factory(100)->create();
        //Term::factory()->create();

        /*
        DB::insert("
            INSERT 
            INTO
                staff(FName,LName,position,gender,status,location,subjects,Class,created_at,updated_at)
            VALUES
                (
            'James','Bbaale','teacher','Male','continuing','Ntinda','History, Kiswahili','Senior 4', NOW(), NOW()
                ),

                (
            'Nakalembe','Sarah','cleaner','Female','continuing','Nansana',NULL,NULL, NOW(), NOW()
                )
                
        ");
        */
    }
    
}
