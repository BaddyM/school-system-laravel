<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Term;

class TermFactory extends Factory
{
    protected $model = Term::class;

    public function definition()
    {
        return [
            'term'=>1,
            'year'=>2024,
            'active'=>0
        ];
    }
}
