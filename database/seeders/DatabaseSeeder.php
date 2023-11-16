<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class DatabaseSeeder extends Seeder {

    public function run() {
        // \App\Models\User::factory(10)->create();
        Staff::factory(10)->create();
    }
    
}
