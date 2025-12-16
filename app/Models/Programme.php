<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = ['programme_type_id', 'core_subject_id', 'elective_subject_id', 'faculty_id', 'lowest_grade_for_cores', 'lowest_grade_for_electives', 'programme_name'];
    //

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function electiveSubject()
    {
        return $this->belongsTo(ElectiveSubject::class);
    }

    public function coreSubject()
    {
        return $this->belongsTo(CoreSubject::class);
    }

    public function passGradeForCores()
    {
        return $this->belongsTo(Grade::class, 'lowest_grade_for_cores');
    }

    public function passGradeForElectives()
    {
        return $this->belongsTo(Grade::class, 'lowest_grade_for_electives');
    }

    public function addProgramme(String $programmeName, array $electives, array $cores, String $coreGrade, String $electiveGrade, ProgrammeType $type, Faculty $faculty)
    {
        array_walk(
            $cores,
            fn(&$value, $key) => strtolower($key)
        );
        $requiredCores = $this->queryModel(CoreSubject::query(), $cores);
        $requiredElectives = $this->queryModel(ElectiveSubject::query(), $electives);
        if (!$requiredElectives) {
            $requiredElectives = ElectiveSubject::create(['elective_one' => $electives[0], 'elective_two' => $electives[1], 'elective_three' => $electives[2]]);
        }
        try {
            [$coreGradeId, $electiveGradeId] = $this->getGradesRequiredForProgramme($coreGrade, $electiveGrade);
            $this->create(['programme_name' => $programmeName, 'elective_subject_id' => $requiredElectives->id, 'core_subject_id' => $requiredCores->id, 'lowest_grade_for_cores' => $coreGradeId, 'lowest_grade_for_electives' => $electiveGradeId, 'faculty' => $faculty->id, 'programme_type_id' => $type->id]);
        } catch (Throwable $e) {
            dd($e->getMessage());
        }
    }

    public function queryModel($query, array $coursesArray)
    {
        foreach ($coursesArray as $subject => $value) {
            $query->where($subject, '=', $value);
        }

        return $query->first();
    }

    public function getGradesRequiredForProgramme($coreGrade, $electiveGrade)
    {
        $coreGradeId = Grade::where('grade', '=', $coreGrade)->first();
        if ($coreGradeId) {
            try {
                $electiveGradeId = Grade::where('grade', '=', $electiveGrade)->firstOrFail();
                return [$coreGradeId, $electiveGradeId];
            } catch (ModelNotFoundException $e) {
                throw new Exception('Invalid grade for electives inserted');
            }
        } else {
            throw new Exception('Invalid grade for cores inserted');
        }
    }
}
