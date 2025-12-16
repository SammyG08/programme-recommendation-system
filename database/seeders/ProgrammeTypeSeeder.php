<?php

namespace Database\Seeders;

use App\Models\ProgrammeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgrammeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $types = ['Degree', 'Diploma', 'BSc. Business Administration Specializations'];
    public function run(): void
    {
        foreach ($this->types as $type) {
            ProgrammeType::factory()->create([
                'type' => $type
            ]);
        }
    }
}
