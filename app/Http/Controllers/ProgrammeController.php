<?php

namespace App\Http\Controllers;

use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Programme;
use Exception;
use Illuminate\Http\Request;
use League\Config\Exception\ValidationException;

class ProgrammeController extends Controller
{

    // private $gradeMap = ['A1 - C6' => 'credit', 'D7' => 'pass'];
    public function processCoreResults(Request $request)
    {
        try {
            $this->validateCoreResult($request);

            $gradeArray = ['english' => $request->get('englishGrade'), 'mathematics' => $request->get('cMathGrade'), 'science' => $request->get('scienceGrade'), 'social' => $request->get('socialGrade')];
            $coresWithBestGrades = $this->getSubjectsWithBestGrades($gradeArray);
            $possibleProgrammes = $this->filterProgrammeBasedOnCoreGrade($coresWithBestGrades);
            dd($possibleProgrammes);
        } catch (ValidationException $e) {
            dd('Invalid grade format');
        }
    }




    public function processElectiveResults(Request $request)
    {
        try {
            $this->validateElectiveResult($request);
            $electiveOne = ucwords($request->get('electiveOne'));
            $electiveTwo = ucwords($request->get('electiveTwo'));
            $electiveThree = ucwords($request->get('electiveThree'));
            $electiveFour = ucwords($request->get('electiveFour'));

            $subjectGradesArray = [$electiveOne => $request->get('electiveOneGrade'), $electiveTwo => $request->get('electiveTwoGrade'), $electiveThree => $request->get('electiveThreeGrade'), $electiveFour => $request->get('electiveFourGrade')];
            $electivesWithBestGrades = $this->getSubjectsWithBestGrades($subjectGradesArray);
            $focisElligbleProgramesIds = $this->elligibleProgrammesIds($electivesWithBestGrades, 'Faculty of Computing & Information Systems');
            dd($focisElligbleProgramesIds);

            $this->processElectiveNames([$electiveOne, $electiveTwo, $electiveThree, $electiveFour]);
        } catch (ValidationException $e) {
            dd($e->getMessage());
        }
        dd($request);
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

        return $subjectsWithBestGrades;
    }

    public function filterProgrammeBasedOnCoreGrade($subjectsWithBestGrades)
    {
        $coreQuery = CoreSubject::query();
        if (isset($subjectsWithBestGrades['english']) && isset($subjectsWithBestGrades['mathematics'])) {
            if (isset($subjectsWithBestGrades['science'])) $coreQuery->where("science", '=', 'required')->where('social', '=', 'not required');
            else if (isset($subjectsWithBestGrades['social'])) $coreQuery->where("science", '=', 'not required')->where('social', '=', 'required');
            $coreSubjectsModel = $coreQuery->first();
            $programmes = Programme::where('core_subject_id', '=', $coreSubjectsModel->id)->get();

            // dd($programmes);
            return $programmes;
        } else return collect();
    }

    public function validateElectiveResult($request)
    {
        try {
            $request->validate([
                'electiveOne' => ['required', 'string', 'regex:/^([A-Za-z]+ [A-Za-z]+|[A-Za-z]+)$/'],
                'electiveTwo' => ['required', 'string', 'regex:/^([A-Za-z]+ [A-Za-z]+|[A-Za-z]+)$/'],
                'electiveThree' => ['required', 'string', 'regex:/^([A-Za-z]+ [A-Za-z]+|[A-Za-z]+)$/'],
                'electiveFour' => ['required', 'string', 'regex:/^([A-Za-z]+ [A-Za-z]+|[A-Za-z]+)$/'],
                'electiveOneGrade' => 'required|string|regex:/^[A-F][1-9]$/',
                'electiveTwoGrade' => 'required|string|regex:/^[A-F][1-9]$/',
                'electiveThreeGrade' => 'required|string|regex:/^[A-F][1-9]$/',
                'electiveFourGrade' => 'required|string|regex:/^[A-F][1-9]$/',
            ]);
        } catch (ValidationException $e) {
            throw new Exception("Invalid input found");
        }
    }

    public function validateCoreResult($request)
    {
        try {
            $request->validate(['englishGrade' => 'required|string|regex:/^[A-F][1-9]$/', 'cMathGrade' => 'required|string|regex:/^[A-F][1-9]$/', 'scienceGrade' => 'required|string|regex:/^[A-F][1-9]$/', 'socialGrade' => 'required|string|regex:/^[A-F][1-9]$/']);
        } catch (ValidationException $e) {
            throw new Exception("Invalid grade selected");
        }
    }
    public function processElectiveNames(array $electiveNames) {}

    public function elligibleProgrammesIds($electives, $faculty_name)
    {
        $faculty = Faculty::where('faculty_name', 'LIKE', "%{$faculty_name}%")->first();
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
                            if (array_search($s, array_keys($electives)) === false) {
                                $seen = false;
                                break;
                            }
                            $seen = true;
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
    }
}
