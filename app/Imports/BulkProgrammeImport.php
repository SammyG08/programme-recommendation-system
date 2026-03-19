<?php

namespace App\Imports;

use App\ElectivesEnum;
use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Grade;
use App\Models\Programme;
use App\Models\ProgrammeType;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BulkProgrammeImport implements ToCollection, WithHeadingRow, WithValidation
{
    public $facultyId;
    public function __construct($facultyId)
    {
        $this->facultyId = $facultyId;
        // dd($this->faculty);
    }
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $programmeName = strtoupper($row['programme']);
            $grade = $this->getGrade($row['elective_grades']);
            $elective = $this->getElectives($row['required_electives']);

            $cores = $this->getNotRequiredCore([
                'science' => strtolower($row['science']),
                'social'  => strtolower($row['social']),
                'english' => strtolower($row['english']),
                'math'    => strtolower($row['core_math']),
            ]);

            $existingRecords = Programme::where('programme_name', $programmeName)->get();
            if ($existingRecords->isNotEmpty()) {
                Programme::where('programme_name', $programmeName)->delete();
            }

            foreach ($cores as $core) {
                Programme::create([
                    'programme_name'             => $programmeName,
                    'faculty_id'                 => $this->facultyId,
                    'lowest_grade_for_cores'     => $grade->id,
                    'lowest_grade_for_electives' => $grade->id,
                    'elective_subject_id'        => $elective->id,
                    'core_subject_id'            => $core->id,
                    'programme_type_id'          => $this->programmeType($grade->value)->id,
                ]);
            }
        }
    }


    public function rules(): array
    {
        return [
            'programme' => ['required', 'string', 'regex:/^[a-z ]+$/i'],
            'english'   => ['required', 'string', 'regex:/^([a-f][1-9]-[a-f][1-9])|N\/A$/i'],
            'science'   => ['required', 'string', 'regex:/^([a-f][1-9]-[a-f][1-9])|N\/A$/i'],
            'core_math' => ['required', 'string', 'regex:/^([a-f][1-9]-[a-f][1-9])|N\/A$/i'],
            'social'    => ['required', 'string', 'regex:/^([a-f][1-9]-[a-f][1-9])|N\/A$/i'],
            'elective_grades'    => ['required', 'string', 'regex:/^[a-f][1-9]-[a-f][1-9]$/i'],
            'required_electives' => ['required', 'string', 'regex:/[a-z ]+\*?(?:\/[a-z ]+\*?){2,}/i'],
        ];
    }

    public function getNotRequiredCore($cores)
    {
        $notRequiredCore = array_filter($cores, fn($v, $k) => str_contains($v, 'n/a'), ARRAY_FILTER_USE_BOTH);
        if (count($notRequiredCore) > 1) throw new Exception('Number of cores must be 3 or more');
        else if (count($notRequiredCore) === 1) return [CoreSubject::where(array_first(array_keys($notRequiredCore)), '=', 'not required')->first()];
        return [CoreSubject::where('social', '=', 'not required')->first(), CoreSubject::where('science', '=', 'not required')->first()];
    }

    public function getElectives($requiredElectives)
    {
        $electives = strtolower($requiredElectives);
        $arr = explode('/', $electives);
        if (count($arr) < 3) throw new Exception('Invalid number of required electives');
        $aCount = array_count_values($arr);
        if (isset($aCount['any'])) {
            if (count($arr) > 3) throw new Exception('When an elective subject is categorized as any in the excel row, only compulsory subjects should be specified next');
            $anyCount = $aCount['any'];
            if ($anyCount === 3) {
                return ElectiveSubject::where('elective_one', 'any')->where('elective_two', 'any')->where('elective_three', 'any')->first();
            } else if ($anyCount === 2) {
                $remainingElectives = array_filter($arr, fn($v) => $v !== 'any');
                $first = substr($remainingElectives[0], -1, 1) === '*' ? substr($remainingElectives[0], 0, -1) : $remainingElectives[0];
                $data = ['elective_one' => ucwords($first), 'elective_two' => 'any', 'elective_three' => 'any'];
                $e = ElectiveSubject::where($data)->first();
                if (!$e) {
                    $e = ElectiveSubject::create($data);
                }
                return $e;
            } else if ($anyCount === 1) {
                $remainingElectives = array_filter($arr, fn($v) => $v !== 'any');
                $first = substr($remainingElectives[0], -1, 1) === '*' ? substr($remainingElectives[0], 0, -1) : $remainingElectives[0];
                $sec = substr($remainingElectives[1], -1, 1) === '*' ? substr($remainingElectives[1], 0, -1) : $remainingElectives[1];
                $data = ['elective_one', ucwords($first), 'elective_two' => ucwords($sec), 'elective_three' => 'any'];
                $e = ElectiveSubject::where($data)->first();
                if (!$e) {
                    $e = ElectiveSubject::create($data);
                }
                return $e;
            }
        }
        $remainingElectives = array_filter($arr, fn($v) => $v !== 'any');
        $compulsoryElectives = array_filter($remainingElectives, function ($val) {
            $lastLetter = substr($val, -1, 1);
            if ($lastLetter === '*') return true;
        });

        $elec = [];
        if (count($compulsoryElectives)) {
            foreach ($compulsoryElectives as $e) {
                $elec[] = substr($e, 0, -1);
            }
        }
        $eString = implode('|', $remainingElectives);
        $elec[] = $eString;

        $data = ['elective_one' => $elec[0], 'elective_two' => $elec[1], 'elective_three' => $elec[2]];
        $e = ElectiveSubject::where($data)->first();
        if (!$e) {
            $e = ElectiveSubject::create($data);
            return $e;
        }
    }

    public function getGrade($grade)
    {
        $g = Grade::where('value', (int)substr($grade, -1, 1))->first();
        return $g;
    }

    public function programmeType($grade)
    {
        $type = $grade <= 6 ? ProgrammeType::where('type', 'Degree')->first() : ProgrammeType::where('type', 'Diploma')->first();
        return $type;
    }
}
