<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

    public function run() {
        // \App\Models\User::factory(10)->create();
        //Staff::factory(10)->create();
        //Student::factory(100)->create();
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

        /*
        //$subjects = array('Geography', 'Literature', 'Luganda', 'Biology', 'Physics','Chemistry');
        $subjects = array('Mathematics','English','Agriculture','History');
        
        foreach($subjects as $s){
            DB::table('subjects')->insert([
                'name' => $s,
                'papers' => 2,
                'level' => 'A Level',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        */

        //Add user
        /*
        $data = [
            'Mansoor' => 'male',
            'Annet' => 'female',
            'Sahya' => 'male'
        ];
        */

        /*
        $data = [
            'Patrick' => 'male'
        ];

        function create_user($username, $gender){
            return DB::table('users')->insert([
                'username' => $username,
                //'email' => ''.strtolower($username).'@gmail.com',
                'email' => 'patrick@gmail.com',
                'gender' => $gender,
                'dept' => 'teacher',
                'is_active' => 1,
                'is_teacher' => 1,
                //'password' => Hash::make(''.strtolower($username).''),
                'password' => Hash::make('patrick'),
                'created_at' => now()
            ]);
        }

        foreach($data as $key => $value){
            create_user($key, $value);
        }
        */

        //$class = array('Senior 1', 'Senior 2', 'Senior 3', 'Senior 4');
        
        $subjects = DB::table('subjects')->where('level','O Level')->get();
        $stdid = DB::table('student_2023')->select('std_id')->where('class','Senior 1')->get();
        $competence = array('can do better', 'relaxed a lot', 'misunderstood the statements','No introductions');
        $remark = array('Great','Understood', 'Tried');

        foreach($stdid as $id){
            foreach($subjects as $s){
                $topics = DB::table('topics')->where(['class' => 'Senior 1', 'subject' => $s->name])->get();
                foreach($topics as $t){
                    $exists = DB::table('activity_1_2023')->where(['class' => 'Senior 1', 'topic' => $t->topic, 'std_id' => $id->std_id, 'subject' => $s->name])->exists();

                    if($exists != 1){
                        DB::table('activity_1_2023')->insert([
                            'std_id' => $id->std_id,
                            'subject' => $s->name,
                            'class' => 'Senior 1',
                            'topic' => $t->topic,
                            'competence' => $competence[array_rand($competence)],
                            'score' => rand(1,3),
                            'remark' => $remark[array_rand($remark)]
                        ]);
                    }else{
                        DB::table('activity_1_2023')->where('std_id', $id->std_id)->update([
                            'subject' => $s->name,
                            'class' => 'Senior 1',
                            'topic' => $t->topic,
                            'competence' => $competence[array_rand($competence)],
                            'score' => rand(1,3),
                            'remark' => $remark[array_rand($remark)]
                        ]);
                    }
                }
            }
        }
        
    }
    
}
