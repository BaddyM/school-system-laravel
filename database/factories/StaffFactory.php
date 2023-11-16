<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Staff;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition()
    {
        return [
            'staffid' => rand(1000, 2000),
            'FName' => $this->faker->firstName(),
            'LName'  => $this->faker->lastName(),
            'position' => $this->faker->randomElement($array = ['Cook','Teacher','Driver']),
            'gender' => $this->faker->randomElement( $array = ['Male','Female']),
            'status' => $this->faker->randomElement( $array = ['Unavailable','Continuing','Dismissed']),
            'location' => $this->faker->randomElement( $array =  ['Mukono','Nangabo','Makindye','Lugazi']),
            'subjects' => $this->faker->randomElement($array = ['Sciences','Arts','None']),
            'Class'=> $this->faker->randomElement($array = ['A level','O-Level'])
        ];
    }
}
