<?php

namespace Database\Seeders;

use App\Models\CoreSubject;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        $this->call(CoreSubjectsSeeder::class);
        $this->call(GradeSeeder::class);
        $this->call(ProgrammeTypeSeeder::class);
        $this->call(FacultySeeder::class);
        $this->call(ElectiveSubjectSeeder::class);
    }
}
