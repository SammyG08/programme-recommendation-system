<?php

namespace Database\Factories;

use App\Models\ElectiveSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElectiveSubject>
 */
class ElectiveSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = ElectiveSubject::class;
    public function definition(): array
    {
        return [
            //
        ];
    }
}
