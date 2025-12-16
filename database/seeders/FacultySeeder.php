<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $faculties = ['Faculty of Engineering', 'Faculty of Computing & Information Systems', 'Business School'];
    public function run(): void
    {
        foreach ($this->faculties as $faculty) {
            Faculty::factory()->create([
                'faculty_name' => $faculty
            ]);
        }
    }
}
