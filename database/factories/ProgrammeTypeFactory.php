<?php

namespace Database\Factories;

use App\Models\ProgrammeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProgrammeType>
 */
class ProgrammeTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProgrammeType::class;
    public function definition(): array
    {
        return [
            //
        ];
    }
}
