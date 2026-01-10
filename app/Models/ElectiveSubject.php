<?php

namespace App\Models;

use App\ElectivesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectiveSubject extends Model
{
    use HasFactory;
    protected $fillable = ['elective_one', 'elective_two', 'elective_three'];

    public function programme()
    {
        return $this->hasOne(Programme::class);
    }

    public function getElectives()
    {
        $electiveOneArray = $this->formatElectivesToArray($this->elective_one);
        $electiveTwoArray = $this->formatElectivesToArray($this->elective_two);
        $electiveThreeArray = $this->formatElectivesToArray($this->elective_three);

        $mergedElectives = [...$electiveOneArray, ...$electiveTwoArray, ...$electiveThreeArray];
        $uniqueFromMerge = array_unique($mergedElectives);
        return $uniqueFromMerge;
    }

    public function formatElectivesToArray(string $electiveValue)
    {
        if ($electiveValue === 'any') {
            $courses = [];
            foreach (ElectivesEnum::cases() as $elective) {
                $courses[] = $elective->value;
            }
            return $courses;
        }
        return explode('|', $electiveValue);
    }
}
