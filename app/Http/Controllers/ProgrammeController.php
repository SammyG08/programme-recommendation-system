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


    private $aggregate = 0;
    private $gotElectivesAggregate = false;

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

    public function programmesRecommended(Request $request)
    {
        // Log::info('Processing programme recommendation request');
        try {
            $step = (int)$request->get('step');
            if ($step === 1) {
                $elligibleProgrammesIdBasedOnCores = $this->processCoreResults($request);
                // Log::info('core ids: ', $elligibleProgrammesIdBasedOnCores->toArray());
                $focisProgrammesUserElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Faculty of Computing & Information Systems');
                $foeProgrammesUserElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Faculty of Engineering');
                $businessSchoolProgrammesElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Business School');
                $this->storeInSession(['core_ids' => $elligibleProgrammesIdBasedOnCores, 'focis_ids' => $focisProgrammesUserElligibleToStudyBasedOnElectives, 'foe_ids' => $foeProgrammesUserElligibleToStudyBasedOnElectives, 'bs_ids' => $businessSchoolProgrammesElligibleToStudyBasedOnElectives]);
                // Log::info('ids: ', [$foeProgrammesUserElligibleToStudyBasedOnElectives, $businessSchoolProgrammesElligibleToStudyBasedOnElectives, $focisProgrammesUserElligibleToStudyBasedOnElectives]);
                return response()->json(['statusCode' => 808]);
            } elseif ($step === 2) {
                $this->calculateAggregate();
                $this->clearSessionData(['coreGradesSorted', 'electiveGradesSorted']);
                $focisProgrammesIds = $this->filterArrayBasedOnSimilarIds(session('focis_ids'), session('core_ids'));
                $foeProgrammesIds = $this->filterArrayBasedOnSimilarIds(session('foe_ids'), session('core_ids'));
                $businessSchoolProgrammesIds = $this->filterArrayBasedOnSimilarIds(session('bs_ids'), session('core_ids'));
                $this->clearSessionData(['core_ids', 'focis_ids', 'foe_ids', 'bs_ids']);
                $this->storeInSession(['focis' => $focisProgrammesIds, 'foe' => $foeProgrammesIds, 'bs' => $businessSchoolProgrammesIds]);
                return response()->json(['statusCode' => 808, 'aggregate' => $this->aggregate]);
            } elseif ($step === 3) {
                $focisProgrammes = $this->getProgrammesFromId(session('focis'));
                $foeProgrammes = $this->getProgrammesFromId(session('foe'));
                $businessSchoolProgrammes = $this->getProgrammesFromId(session('bs'));
                $this->clearSessionData(['focis', 'bs', 'foe']);
                $this->storeInSession(['focis' => $focisProgrammes, 'foe' => $foeProgrammes, 'bs' => $businessSchoolProgrammes]);
                return response()->json(['statusCode' => 808]);
            } elseif ($step === 4) {
                $data = ['Faculty of Computing & Information Systems' => session('focis'), 'Faculty of Engineering' => session('foe'), 'Business School' => session('bs')];
                $data = $this->filterEmptyProgrammesOut($data);
                $returnInfo = count($data) ?
                    response()->json(['statusCode' => 808, 'data' => $data]) : response()->json(['statusCode' => 444]);
                // Log::info('aggregate scored : ' . $this->aggregate);

                $this->clearSessionData(['focis', 'bs', 'foe']);
                return $returnInfo;
            }
        } catch (Exception $e) {
            // Log::info('Error encountered: ' . $e);
            return response()->json(['statusCode' => 999, 'msg' => $e->getMessage()]);
        }
    }

    private function storeInSession($data)
    {
        foreach ($data as $key => $val) {
            session([$key => $val]);
        }
    }

    private function clearSessionData($data)
    {
        session()->forget($data);
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
        if (empty($programmesIdArray)) return [];

        $programme_names = Programme::whereIn('id', $programmesIdArray)
            ->distinct()
            ->pluck('programme_name')
            ->toArray();
        // Log::info("Programme names: ", $programme_names);
        return $programme_names;
    }
    public function processCoreResults(Request $request)
    {

        $this->validateCoreInput($request);
        $gradeArray = ['english' => $request->get('englishGrade'), 'mathematics' => $request->get('cMathGrade'), 'science' => $request->get('scienceGrade'), 'social' => $request->get('socialGrade')];
        $coresWithBestGrades = $this->getSubjectsWithBestGrades($gradeArray);
        $sortedCoresWithBestGrades = $this->sortSubjectsBasedOnGrade($coresWithBestGrades);
        $this->storeInSession(['coreGradesSorted' => $this->sortSubjectsBasedOnGrade($gradeArray)]);
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
        if (!$this->gotElectivesAggregate) $this->storeInSession(['electiveGradesSorted' => $this->sortSubjectsBasedOnGrade($subjectGradesArray)]);
        $this->gotElectivesAggregate = true;
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

        if (!empty($lowestGrade['subject'])) {
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
        if (count($subjectsWithBestGrades) < 3) return collect();
        $coreQuery = CoreSubject::query();
        // Log::info('Core subjects with best grades:', $subjectsWithBestGrades);
        $lowestGrade = array_last($subjectsWithBestGrades);
        $lowestGradeSubject = array_last(array_keys($subjectsWithBestGrades));
        $programmes = collect();

        if (isset($subjectsWithBestGrades['english']) && isset($subjectsWithBestGrades['mathematics'])) {
            $newQuery = CoreSubject::query()->where('english', '=', 'required')->where('mathematics', '=', 'required');
            $noEnglishOrMath = array_filter(array_keys($subjectsWithBestGrades), fn($value) => $value !== 'english' && $value !== 'mathematics');
            // Log::info('no english or math: ', $noEnglishOrMath);
            $coreModel = $newQuery->where(array_last($noEnglishOrMath), '=', 'required')->first();

            $programmes = Programme::where('core_subject_id', '=', $coreModel->id)->where('lowest_grade_for_cores', '>=', $lowestGrade)->get();
            // Log::info('core model id: ', [$coreModel->id, $lowestGradeSubject]);
            // Log::info('lowest grade: ', [$lowestGrade]);
            if (count($subjectsWithBestGrades) === 4) {
                $cModel = null;
                if ($lowestGradeSubject !== 'english' && $lowestGradeSubject !== 'mathematics') {
                    $cModel = $coreQuery->where($lowestGradeSubject, '=', 'not required')->first();
                    $keys = array_keys($subjectsWithBestGrades);
                    $secondLowestSubject = $keys[count($keys) - 2];
                    $secondLowestGrade = $subjectsWithBestGrades[$secondLowestSubject];
                    $altProgrammes = Programme::where('core_subject_id', '=', $cModel?->id)->where('lowest_grade_for_cores', '>=', $secondLowestGrade)->get();
                    $programmes = $programmes->concat($altProgrammes);
                } else {
                    $cModel = $coreQuery->where('social', '=', 'not required')->first();
                    $altProgrammes = Programme::where('core_subject_id', '=', $cModel?->id)->where('lowest_grade_for_cores', '>=', $lowestGrade)->get();
                    $programmes = $programmes->concat($altProgrammes);
                }
            }
        }
        $programmes = $programmes->unique('id');
        // Log::info('Programmes found:', $programmes->toArray());
        return $programmes;
    }

    private function calculateAggregate()
    {
        $coreSubjects = session('coreGradesSorted');
        $electiveSubjects = session('electiveGradesSorted');

        foreach (array_values($coreSubjects) as $idx => $grade) {
            if ($idx < 3) {
                $g = (int)substr($grade, -1, 1);
                $this->aggregate += $g;
            }
        }
        foreach (array_values($electiveSubjects) as $idx => $grade) {
            if ($idx < 3) {
                $g = (int)substr($grade, -1, 1);
                $this->aggregate += $g;
            }
        }
    }

    public function elligibleProgrammesIds($electives, $faculty_name)
    {
        $idOfFacultyProgrammesUserCanOffer = [];
        // Log::info('Electives with best grades:', $electives);
        if (count($electives) >= 3 && count($electives) <= 4) {
            $lowestGrade = array_last($electives);
            $keys = array_keys($electives);
            $courseWithSecondLowestGrade = $keys[count($keys) - 2];
            $secondLowestGrade = $electives[$courseWithSecondLowestGrade];
            try {
                $this->matchingElectivesEngine($idOfFacultyProgrammesUserCanOffer, $electives, $faculty_name, $lowestGrade);
                // Log::info('ids of elective subjects: ', array_unique($idOfFacultyProgrammesUserCanOffer));
                if (count($electives) === 4) {
                    array_pop($electives);
                    $this->matchingElectivesEngine($idOfFacultyProgrammesUserCanOffer, $electives, $faculty_name, $secondLowestGrade);
                }
            } catch (ModelNotFoundException $e) {
                throw new Exception($e->getMessage());
            }
        }
        // Log::info('ids of elective subjects: ', array_unique($idOfFacultyProgrammesUserCanOffer));

        return array_unique($idOfFacultyProgrammesUserCanOffer);
    }

    private function matchingElectivesEngine(&$idOfFacultyProgrammesUserCanOffer, $electives, $faculty_name, $grade)
    {
        // Log::info('Starting electives for matching: ', $electives);
        $seen = false;
        $faculty = null;
        try {
            $faculty = Faculty::where('faculty_name', '=', $faculty_name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new Exception('Contact administration to update available faculties in the school');
        }
        $facultyProgrammes = $faculty->programmes()->where('lowest_grade_for_electives', '>=', $grade)->get();
        // Log::info("$faculty->faculty_name programmes", $facultyProgrammes->toArray());
        if ($facultyProgrammes->count()) {
            foreach ($facultyProgrammes as $facultyProgramme) {
                $tempElectives = $electives;
                $electiveSubject = ElectiveSubject::find($facultyProgramme->elective_subject_id);
                $electiveSubjects = [$electiveSubject->elective_one, $electiveSubject->elective_two, $electiveSubject->elective_three];
                $subjectsOccurence = array_count_values($electiveSubjects);

                if (isset($subjectsOccurence['any']) || isset($subjectsOccurence['science-related subject'])) {
                    // Log::info('Any or science-related subject found in electives requirements for programme id: ' . $facultyProgramme->id);
                    $seen = true;
                    $remainingElectives = array_keys(array_filter($subjectsOccurence, function ($value, $key) {
                        return $key !== 'any' && $key !== 'science-related subject';
                    }, ARRAY_FILTER_USE_BOTH));
                    if (count($remainingElectives)) {
                        foreach ($remainingElectives as $elective) {
                            $subjectArray = explode('|', $elective);

                            foreach ($subjectArray as $s) {
                                if (in_array($s, array_keys($tempElectives))) {
                                    $seen = true;
                                    unset($tempElectives[$s]);
                                    break;
                                }
                                $seen = false;
                            }
                            if (!$seen) break;
                        }
                    }
                } else {
                    // Log::info('Specific electives found in electives requirements for programme id: ' . $facultyProgramme->id);
                    foreach ($electiveSubjects as $subject) {
                        $subjectArray = explode('|', $subject);
                        // Log::info('Checking subject options: ', $subjectArray);
                        foreach ($subjectArray as $s) {
                            if (in_array($s, array_keys($tempElectives))) {
                                $seen = true;
                                unset($tempElectives[$s]);
                                // Log::info('Remaining electives after matching: ', $tempElectives);
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
