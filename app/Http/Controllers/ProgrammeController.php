<?php

namespace App\Http\Controllers;

use App\ElectivesEnum;
use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Programme;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use League\Config\Exception\ValidationException;

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

    public function programmesRecommended(Request $request)
    {


        $elligibleProgrammesIdBasedOnCores = $this->processCoreResults($request);
        try {
            $focisProgrammesUserElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Faculty of Computing & Information Systems');
            $foeProgrammesUserElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Faculty of Engineering');
            $businessSchoolProgrammesElligibleToStudyBasedOnElectives = $this->processElectiveResults($request, 'Business School');
            $focisProgrammesIds = $this->filterArrayBasedOnSimilarIds($focisProgrammesUserElligibleToStudyBasedOnElectives, $elligibleProgrammesIdBasedOnCores);
            $foeProgrammesIds = $this->filterArrayBasedOnSimilarIds($foeProgrammesUserElligibleToStudyBasedOnElectives, $elligibleProgrammesIdBasedOnCores);
            // return response()->json(['statusCode' => 808, 'data' => ['foe' => $foeProgrammesIds, 'foes' => $foeProgrammesUserElligibleToStudyBasedOnElectives]]);
            $businessSchoolProgrammesIds = $this->filterArrayBasedOnSimilarIds($businessSchoolProgrammesElligibleToStudyBasedOnElectives, $elligibleProgrammesIdBasedOnCores);
            $focisProgrammes = $this->getProgrammesFromId($focisProgrammesIds);
            $foeProgrammes = $this->getProgrammesFromId($foeProgrammesIds);
            $businessSchoolProgrammes = $this->getProgrammesFromId($businessSchoolProgrammesIds);
            return response()->json(['statusCode' => 808, 'data' => ['Faculty of Computing & Information Systems' => $focisProgrammes, 'Faculty of Engineering' => $foeProgrammes, 'Business School' => $businessSchoolProgrammes]]);
        } catch (Exception $e) {
            return response()->json(['statusCode' => 999, 'msg' => $e->getMessage()]);
        }
    }

    private function filterArrayBasedOnSimilarIds($programmesElligibleToStudyBasedOnElectives, $elligibleProgrammesBasedOnCores)
    {
        $result = collect($programmesElligibleToStudyBasedOnElectives)->intersect($elligibleProgrammesBasedOnCores)->values()->toArray();
        return $result;
    }
    private function getProgrammesFromId($programmesIdArray)
    {
        $programmes = [];
        if ($programmesIdArray) {
            foreach ($programmesIdArray as $id) {
                $programme = Programme::find($id);
                array_push($programmes, $programme?->programme_name);
            }
        }
        return $programmes;
    }
    public function processCoreResults(Request $request)
    {

        $this->validateCoreInput($request);
        $gradeArray = ['english' => $request->get('englishGrade'), 'mathematics' => $request->get('cMathGrade'), 'science' => $request->get('scienceGrade'), 'social' => $request->get('socialGrade')];
        $coresWithBestGrades = $this->getSubjectsWithBestGrades($gradeArray);
        $elligibleProgrammesId = $this->filterProgrammeBasedOnCoreGrade($coresWithBestGrades);
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
        try {
            $elligbleProgramesIds = $this->elligibleProgrammesIds(coreSubjects: null, electives: $electivesWithBestGrades, faculty_name: $facultyName);
            return $elligbleProgramesIds;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getSubjectsWithBestGrades(array $gradeArray, int $lowestAcceptableGrade = 7)
    {
        $lowestGrade = ['grade' => $lowestAcceptableGrade];
        foreach ($gradeArray as $subj => $grade) {
            $num = (int) str_split($grade)[1];
            if ($num > $lowestGrade['grade']) {
                $lowestGrade['grade'] = $num;
                $lowestGrade['subject'] = $subj;
            }
        }

        if (array_key_exists('subject', $lowestGrade)) {
            $subjectsWithBestGrades = [];
            foreach (array_keys($gradeArray) as $key) {
                if ($key !== $lowestGrade['subject']) {
                    $subjectsWithBestGrades[$key] = $gradeArray[$key];
                }
            }
        } else $subjectsWithBestGrades = $gradeArray;

        // Log::info("Subjects with best grades: ", $subjectsWithBestGrades);
        return $subjectsWithBestGrades;
    }

    public function filterProgrammeBasedOnCoreGrade($subjectsWithBestGrades)
    {
        $coreQuery = CoreSubject::query();
        if (isset($subjectsWithBestGrades['english']) && isset($subjectsWithBestGrades['mathematics'])) {
            if (isset($subjectsWithBestGrades['science'])) $coreQuery->where("science", '=', 'required')->where('social', '=', 'not required');
            elseif (isset($subjectsWithBestGrades['social'])) $coreQuery->where("science", '=', 'not required')->where('social', '=', 'required');
            else return collect();
            $programmes = Programme::where('core_subject_id', '=', $coreQuery->first()?->id)->pluck('id');
            return $programmes;
        } else return collect();
    }

    public function elligibleProgrammesIds($coreSubjects, $electives, $faculty_name)
    {
        $faculty = Faculty::where('faculty_name', 'LIKE', "%{$faculty_name}%")->first();
        if ($faculty) {
            $idOfFacultyProgrammesUserCanOffer = [];
            $facultyProgrammes = $faculty->programmes;
            $seen = false;
            foreach ($facultyProgrammes as $facultyProgramme) {
                $electiveSubject = ElectiveSubject::find($facultyProgramme->elective_subject_id);
                $electiveSubjects = [$electiveSubject->elective_one, $electiveSubject->elective_two, $electiveSubject->elective_three];
                $subjectsOccurence = array_count_values($electiveSubjects);

                if (isset($subjectsOccurence['any']) || isset($subjectsOccurence['science-related subject'])) {

                    $remainingElectives = array_keys(array_filter($subjectsOccurence, function ($value, $key) {
                        return $key !== 'any' && $key !== 'science-related subject';
                    }, ARRAY_FILTER_USE_BOTH));
                    // dd($subjectsOccurence, $remainingElectives);
                    $seen = true;
                    if (count($remainingElectives)) {
                        foreach ($remainingElectives as $elective) {
                            if (array_search($elective, array_keys($electives)) === false) {
                                $seen = false;
                                break;
                            }
                            $seen = true;
                        }
                    }
                } else {
                    foreach ($electiveSubjects as $subject) {
                        $subjectArray = explode('/', $subject);
                        if (count($subjectArray) > 1) {
                            foreach ($subjectArray as $s) {
                                if (in_array($s, array_keys($electives))) {
                                    $seen = true;
                                    break;
                                }
                                $seen = false;
                            }
                        } else {
                            if (array_search($subject, array_keys($electives)) === false) {
                                $seen = false;
                                break;
                            }
                            $seen = true;
                        }
                    }
                }
                if ($seen) {
                    array_push($idOfFacultyProgrammesUserCanOffer, $facultyProgramme->id);
                }
            }
            return $idOfFacultyProgrammesUserCanOffer;
        } else throw new Exception("Faculty not yet in the university");
    }
}
