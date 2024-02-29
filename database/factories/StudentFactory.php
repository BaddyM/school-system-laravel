<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Student;

class StudentFactory extends Factory {

    protected $model = Student::class;

    public function definition() {
        return [
            'std_id' => random_int(100000001, 100000001000),
            'fname' => $this->faker->firstName(),
            'lname' => $this->faker->lastName(),
            'class' => $this->faker->randomElement($array = ['Senior 1','Senior 2','Senior 3','Senior 4','Senior 5','Senior 6']),
            'stream' => $this->faker->randomElement($array = ['North','East','South','West']),
            'house' => $this->faker->randomElement($array = ['Katonga','Nile','Kisozi']),
            'section' => $this->faker->randomElement($array = ['Boarding', 'Day']),
            'image' => $this->faker->randomElement($array = ['badge.jpg','Lizzy.jpg']),
            'year_of_entry' => '2024',
            'status' => 'continuing',
            'combination' => $this->faker->randomElement($array = ['MEA/ICT','PCM/ICT','PCB/SUBMTH','HEG/SUBMTH']),
            'password' => random_int(10000000001, 10000000001000),
            'dob' => now(),
            'lin' => random_int(10000000001, 10000000001000),
            'residence' => 'Kampala',
            'nationality' => 'Ugandan',
            'gender' => $this->faker->randomElement($array = ['Male','Female'])
        ];
    }
}
