<?php

namespace Database\Seeders;

use App\Models\ElectiveSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectiveSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ElectiveSubject::factory()->create([
            'elective_one' => 'Elective Mathematics',
            'elective_two' => 'Physics',
            'elective_three' => 'Chemistry/Applied Electricity/Electronics/Technical Drawing',
        ]);
        ElectiveSubject::factory()->create([
            'elective_one' => 'Elective Mathematics',
            'elective_two' => 'any',
            'elective_three' => 'any',
        ]);
        ElectiveSubject::factory()->create([
            'elective_one' => 'Elective Mathematics',
            'elective_two' => 'science-related subject',
            'elective_three' => 'science-related subject',
        ]);
        ElectiveSubject::factory()->create([
            'elective_one' => 'Elective Mathematics',
            'elective_two' => 'Physics',
            'elective_three' => 'any',
        ]);
        ElectiveSubject::factory()->create([
            'elective_one' => 'any',
            'elective_two' => 'any',
            'elective_three' => 'any',
        ]);
    }
}
