<?php

namespace Database\Factories;

use App\Models\CoreSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoreSubject>
 */
class CoreSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = CoreSubject::class;
    public function definition(): array
    {
        return [
            'english' => 'required',
            'science' => 'required',
            'social' => 'not required',
            'mathematics' => 'required'
        ];
    }
}
