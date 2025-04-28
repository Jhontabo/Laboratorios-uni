<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laboratory;
use App\Models\User;

class LaboratoriesSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are users with the 'LABORATORISTA' role
        $laboratoristas = User::role('LABORATORISTA')->get();

        if ($laboratoristas->isEmpty()) {
            $this->command->info('No users found with the "LABORATORISTA" role.');
            return;
        }

        // Insert 10 laboratories
        foreach (range(1, 10) as $i) {
            Laboratory::create([
                'name' => "Laboratory $i",
                'location' => "Building $i, Floor $i",
                'capacity' => rand(10, 50),
                'user_id' => $laboratoristas->random()->id, // Correct reference to 'id'
            ]);
        }
    }
}

