<?php

namespace App\Http\Controllers;

use App\ElectivesEnum;
use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Programme;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;;

class ProgrammeController extends Controller
{

    // private $gradeMap = ['A1 - C6' => 'credit', 'D7' => 'pass'];

    public function validateCoreInput(Request $request)
    {
        try {
            $request->validate(['englishGrade' => 'required|string|regex:/^[A-F][1-9]$/', 'cMathGrade' => 'required|string|regex:/^[A-F][1-9]$/', 'scienceGrade' => 'required|string|regex:/^[A-F][1-9]$/', 'socialGrade' => 'required|string|regex:/^[A-F][1-9]$/']);
            return response()->json(['statusCode' => 808]);
        } catch (ValidationException $e) {
            return response()->json(['statusCode' => 999, 'msg' => 'Please ensure all fields are filled with valid data']);
        }
    }

    public function validateElectiveInput(Request $request)
    {
        try {
            $request->validate([
                'electiveOne' => ['required', 'string', new Enum(ElectivesEnum::class)],
                'electiveTwo' => ['required', 'string',  new Enum(ElectivesEnum::class)],
                'electiveThree' => ['required', 'string',  new Enum(ElectivesEnum::class)],
                'electiveFour' => ['required', 'string',  new Enum(ElectivesEnum::class)],
                'electiveOneGrade' => 'required|string|regex:/^[A-F][1-9]$/',
                'electiveTwoGrade' => 'required|string|regex:/^[A-F][1-9]$/',
                'electiveThreeGrade' => 'required|string|regex:/^[A-F][1-9]$/',
                'electiveFourGrade' => 'required|string|regex:/^[A-F][1-9]$/',
            ]);
            return response()->json(['statusCode' => 808, 'url' => route('programmes-recommended')]);
        } catch (ValidationException $e) {
            return response()->json(['statusCode' => 999, 'msg' => 'Please ensure all fields are filled with valid data']);
        }
    }

    public function programmesRecommended(Request $request, int $step = 1)
    {

        try {
            $elligibleProgrammesIdBasedOnCores = $this->processCoreResults($request);
            $focisProgrammesUserElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Faculty of Computing & Information Systems');
            $foeProgrammesUserElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Faculty of Engineering');
            $businessSchoolProgrammesElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Business School');

            $focisProgrammesIds = $this->filterArrayBasedOnSimilarIds($focisProgrammesUserElligibleToStudyBasedOnElectives, $elligibleProgrammesIdBasedOnCores);
            $foeProgrammesIds = $this->filterArrayBasedOnSimilarIds($foeProgrammesUserElligibleToStudyBasedOnElectives, $elligibleProgrammesIdBasedOnCores);

            $businessSchoolProgrammesIds = $this->filterArrayBasedOnSimilarIds($businessSchoolProgrammesElligibleToStudyBasedOnElectives, $elligibleProgrammesIdBasedOnCores);

            $focisProgrammes = $this->getProgrammesFromId($focisProgrammesIds);
            $foeProgrammes = $this->getProgrammesFromId($foeProgrammesIds);
            $businessSchoolProgrammes = $this->getProgrammesFromId($businessSchoolProgrammesIds);

            $data = ['Faculty of Computing & Information Systems' => $focisProgrammes, 'Faculty of Engineering' => $foeProgrammes, 'Business School' => $businessSchoolProgrammes];
            $data = $this->filterEmptyProgrammesOut($data);
            $returnInfo = count($data) ?
                response()->json(['statusCode' => 808, 'data' => $data]) : response()->json(['statusCode' => 444]);
            return $returnInfo;
        } catch (Exception $e) {
            return response()->json(['statusCode' => 999, 'msg' => $e->getMessage()]);
        }
    }

    private function filterEmptyProgrammesOut($data)
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (count($value)) {
                $filteredData[$key] = $value;
            }
        }
        return $filteredData;
    }
    private function filterArrayBasedOnSimilarIds($programmesElligibleToStudyBasedOnElectives, $elligibleProgrammesBasedOnCores)
    {
        $result = $elligibleProgrammesBasedOnCores->pluck('id')->intersect($programmesElligibleToStudyBasedOnElectives)->toArray();

        return $result;
    }
    private function getProgrammesFromId($programmesIdArray)
    {
        $programmes = [];
        if ($programmesIdArray) {
            foreach ($programmesIdArray as $id) {
                $programme_name = Programme::find($id)->value('programme_name');
                array_push($programmes, $programme_name);
            }
        }
        return $programmes;
    }
    public function processCoreResults(Request $request)
    {

        $this->validateCoreInput($request);
        $gradeArray = ['english' => $request->get('englishGrade'), 'mathematics' => $request->get('cMathGrade'), 'science' => $request->get('scienceGrade'), 'social' => $request->get('socialGrade')];
        $coresWithBestGrades = $this->getSubjectsWithBestGrades($gradeArray);
        $sortedCoresWithBestGrades = $this->sortSubjectsBasedOnGrade($coresWithBestGrades);
        $elligibleProgrammesId = $this->filterProgrammeBasedOnCoreGrade($sortedCoresWithBestGrades);
        return $elligibleProgrammesId;
    }

    public function processElectiveResults(Request $request, $facultyName)
    {

        $this->validateElectiveInput($request);
        $electiveOne = ucwords($request->get('electiveOne'));
        $electiveTwo = ucwords($request->get('electiveTwo'));
        $electiveThree = ucwords($request->get('electiveThree'));
        $electiveFour = ucwords($request->get('electiveFour'));

        $subjectGradesArray = [$electiveOne => $request->get('electiveOneGrade'), $electiveTwo => $request->get('electiveTwoGrade'), $electiveThree => $request->get('electiveThreeGrade'), $electiveFour => $request->get('electiveFourGrade')];
        $electivesWithBestGrades = $this->getSubjectsWithBestGrades($subjectGradesArray);
        $electivesWithBestGradesSorted = $this->sortSubjectsBasedOnGrade($electivesWithBestGrades);
        try {
            $elligbleProgramesIds = $this->elligibleProgrammesIds(electives: $electivesWithBestGradesSorted, faculty_name: $facultyName);
            return $elligbleProgramesIds;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getSubjectsWithBestGrades(array $gradeArray, int $unacceptableGrade = 8)
    {
        $lowestGrade = ['subject' => []];
        foreach ($gradeArray as $subj => $grade) {
            $num = (int) str_split($grade)[1];
            if ($num >= $unacceptableGrade) {
                $lowestGrade['subject'][] =  $subj;
            }
        }

        if (array_key_exists('subject', $lowestGrade)) {
            $subjectsWithBestGrades = [];
            foreach (array_keys($gradeArray) as $key) {
                if (!in_array($key, $lowestGrade['subject'])) {
                    $subjectsWithBestGrades[$key] = $gradeArray[$key];
                }
            }
        } else $subjectsWithBestGrades = $gradeArray;

        return $subjectsWithBestGrades;
    }

    private function sortSubjectsBasedOnGrade(array $subjectArray)
    {
        $sortedCourseAndGrade = [];
        foreach ($subjectArray as $subject => $grade) {
            $sortedCourseAndGrade[$subject] = Grade::where('grade', '=', $grade)->value('value');
        }
        uasort($sortedCourseAndGrade, fn($a, $b) => $a <=> $b);
        return $sortedCourseAndGrade;
    }

    public function filterProgrammeBasedOnCoreGrade($subjectsWithBestGrades)
    {
        $coreQuery = CoreSubject::query();
        $lowestGrade = array_last($subjectsWithBestGrades);
        $lowestGradeSubject = array_last(array_keys($subjectsWithBestGrades));
        $programmes = collect();
        if (isset($subjectsWithBestGrades['english']) && isset($subjectsWithBestGrades['mathematics'])) {
            if (count($subjectsWithBestGrades) === 3) {
                $coreModel = $coreQuery->where('english', '=', 'required')->where('mathematics', '=', 'required')->where($lowestGradeSubject, '=', 'required')->first();
                $programmes = Programme::where('core_subject_id', '=', $coreModel->id)->where('lowest_grade_for_cores', '>=', $lowestGrade)->get();
            } elseif (count($subjectsWithBestGrades) === 4) {
                $coreModel = null;
                if ($lowestGradeSubject !== 'english' && $lowestGradeSubject !== 'mathematics') {
                    $coreModel = $coreQuery->where($lowestGradeSubject, '=', 'not required')->first();
                    $keys = array_keys($subjectsWithBestGrades);
                    $secondLowestSubject = $keys[count($keys) - 2];
                    $secondLowestGrade = $subjectsWithBestGrades[$secondLowestSubject];
                    $programmes = Programme::where('core_subject_id', '=', $coreModel?->id)->where('lowest_grade_for_cores', '>=', $secondLowestGrade)->get();
                } else {
                    $coreModel = $coreQuery->where('social', '=', 'not required')->first();
                    $programmes = Programme::where('core_subject_id', '=', $coreModel?->id)->where('lowest_grade_for_cores', '>=', $lowestGrade)->get();
                }
            }
        }
        return $programmes;
    }

    public function elligibleProgrammesIds($electives, $faculty_name)
    {
        $idOfFacultyProgrammesUserCanOffer = [];
        $lowestGrade = array_last($electives);
        $keys = array_keys($electives);
        $courseWithSecondLowestGrade = $keys[count($keys) - 2];
        $secondLowestGrade = $electives[$courseWithSecondLowestGrade];
        if (count($electives) >= 3 && count($electives) <= 4) {
            try {
                $this->selectingElectivesEngine($idOfFacultyProgrammesUserCanOffer, $electives, $faculty_name, $lowestGrade);
                if (count($electives) === 4) {
                    $this->selectingElectivesEngine($idOfFacultyProgrammesUserCanOffer, $electives, $faculty_name, $secondLowestGrade);
                }
            } catch (ModelNotFoundException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return array_unique($idOfFacultyProgrammesUserCanOffer);
    }

    private function selectingElectivesEngine(&$idOfFacultyProgrammesUserCanOffer, $electives, $faculty_name, $grade)
    {
        $seen = false;
        $faculty = null;
        try {
            $faculty = Faculty::where('faculty_name', '=', $faculty_name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Contact administration to update available faculties in the school');
        }
        $facultyProgrammes = $faculty->programmes()->where('lowest_grade_for_electives', '>=', $grade)->get();
        if ($facultyProgrammes->count()) {
            foreach ($facultyProgrammes as $facultyProgramme) {
                $electiveSubject = ElectiveSubject::find($facultyProgramme->elective_subject_id);
                $electiveSubjects = [$electiveSubject->elective_one, $electiveSubject->elective_two, $electiveSubject->elective_three];
                $subjectsOccurence = array_count_values($electiveSubjects);

                if (isset($subjectsOccurence['any']) || isset($subjectsOccurence['science-related subject'])) {
                    $seen = true;
                    $remainingElectives = array_keys(array_filter($subjectsOccurence, function ($value, $key) {
                        return $key !== 'any' && $key !== 'science-related subject';
                    }, ARRAY_FILTER_USE_BOTH));
                    if (count($remainingElectives)) {
                        foreach ($remainingElectives as $elective) {
                            if (array_search($elective, array_keys($electives)) === false) {
                                $seen = false;
                                break;
                            }
                        }
                    }
                } else {
                    foreach ($electiveSubjects as $subject) {
                        $subjectArray = explode('/', $subject);
                        foreach ($subjectArray as $s) {
                            if (in_array($s, array_keys($electives))) {
                                $seen = true;
                                break;
                            }
                            $seen = false;
                        }
                        if (!$seen) break;
                    }
                }
                if ($seen) {
                    array_push($idOfFacultyProgrammesUserCanOffer, $facultyProgramme->id);
                }
            }
        }
    }
}
