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

        $class = array('Senior 1', 'Senior 2', 'Senior 3', 'Senior 4');
        $topics = array('Topic 1', 'Topic 2', 'Topic 3');
        $subjects = array('Mathematics','History','Luganda','Literature','ICT','Physics','Chemistry');
        $stdid = array('20241001','20241002','20241003','20241004','20241005');
        $competence = array('can do better', 'relaxed a lot', 'misunderstood the statements','No introductions');

        for($i=0; $i<=10; $i++){
            foreach($stdid as $std_id){
                $subject = $subjects[array_rand($subjects)];
                $topic = $topics[array_rand($topics)];
                $c = $class[array_rand($class)];
                $comptence = $competence[array_rand($competence)];
                $score = rand(1,3);
    
                $exists = DB::table('olevel_2_2024')->where(['std_id'=>$std_id, 'subject'=>$subject, 'topic'=>$topic])->exists();
    
                if($exists != 1){
                    DB::table('olevel_2_2024')->insert([
                        'std_id' => $std_id,
                        'class' => $c,
                        'subject'=>$subject,
                        'competence' => $comptence,
                        'topic'=>$topic,
                        'score' => $score,
                        'created_at' => now()
                    ]);
                }
            }
        }
        
    }
    
}
