<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $grades = ['A1' => 1, 'B2' => 2, 'B3' => 3, 'C4' => 4, 'C5' => 5, 'C6' => 6, 'D7' => 7, 'E8' => 8];
    public function run(): void
    {
        foreach ($this->grades as $grade => $value) {
            Grade::factory()->create([
                'grade' => $grade,
                'value' => $value,
            ]);
        }
    }
}
