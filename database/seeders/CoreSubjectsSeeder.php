<?php

namespace Database\Seeders;

use App\Models\CoreSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoreSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CoreSubject::factory()->create([
            'mathematics' => 'not required',
            'science' => 'required',
            'social' => 'required',
            'english' => 'required'
        ]);
        CoreSubject::factory()->create([
            'mathematics' => 'required',
            'science' => 'not required',
            'social' => 'required',
            'english' => 'required'
        ]);
        CoreSubject::factory()->create([
            'mathematics' => 'required',
            'science' => 'required',
            'social' => 'not required',
            'english' => 'required'
        ]);
        CoreSubject::factory()->create([
            'mathematics' => 'required',
            'science' => 'required',
            'social' => 'required',
            'english' => 'not required'
        ]);
    }
}
