<?php

namespace App\Livewire;

use App\ElectivesEnum;
use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Programme;
use App\Models\ProgrammeType;
use Livewire\Component;
use Livewire\Attributes\On;
use Throwable;
use Illuminate\Support\Facades\Log;

class ProgrammeAdd extends Component
{
    public $selectAllElectivesForElectiveOne = false;
    public $selectAllElectivesForElectiveTwo = false;
    public $selectAllElectivesForElectiveThree = false;
    public $showModal = false;
    public $electiveOne = [];
    public $electiveTwo = [];
    public $electiveThree = [];

    public $coreSubjects = ['english', 'mathematics', 'science', 'social'];
    public $minimumCoreGrade;
    public $minimumElectiveGrade;

    public $selectedCores = [];

    public $faculty;
    public $electives;

    public $programme_name;
    public $programme_type;

    public function mount(array $electives)
    {
        $this->electives = $electives;
    }

    public function render()
    {
        return view('livewire.programme-add');
    }

    public function saveProgramme()
    {
        Log::info('Submitted data:', [$this->electiveOne, $this->electiveTwo, $this->electiveThree, $this->selectedCores, $this->programme_name, $this->programme_type, $this->minimumCoreGrade, $this->minimumElectiveGrade]);

        // dd($this->validateProgrammeType($this->programme_type));
        if (count($this->electiveOne) !== 0 && count($this->electiveTwo) !== 0 && count($this->electiveThree) !== 0 && count($this->selectedCores) !== 0 && !$this->programme_name == '' && !$this->programme_type == '' && !$this->minimumCoreGrade == '' && !$this->minimumElectiveGrade == '') {
            if (
                $this->validateElectives($this->electiveOne) &&
                $this->validateElectives($this->electiveTwo) &&
                $this->validateElectives($this->electiveThree) && $this->validateProgrammeName($this->programme_name) && $this->validateProgrammeType($this->programme_type) && $this->validateGrade($this->minimumCoreGrade) && $this->validateGrade($this->minimumElectiveGrade) && $this->validateCores($this->selectedCores)
            ) {
                $elective = $this->getElective();
                $faculty = Faculty::where('faculty_name', '=', $this->faculty)->first();
                $cores = $this->getCores();
                $minimumCoreGradeId = $this->getGradeId($this->minimumCoreGrade);
                $minimumElectiveGradeId = $this->getGradeId($this->minimumElectiveGrade);
                $programmeTypeId = $this->getProgrammeTypeId($this->programme_type);
                $programme_name = $this->programme_name;
                foreach ($cores as $core) {
                    if ($core) {
                        LOG::INFO('Creating programme with core:', [$core]);
                        $faculty->programmes()->create([
                            'programme_name' => $programme_name,
                            'elective_subject_id' => $elective->id,
                            'core_subject_id' => $core->id,
                            'lowest_grade_for_cores' => $minimumCoreGradeId,
                            'lowest_grade_for_electives' => $minimumElectiveGradeId,
                            'programme_type_id' => $programmeTypeId
                        ]);
                        // $this->showModal = false;
                        $this->resetSelections();
                        $this->showModal = false;
                        $this->dispatch('programmeAdded', $faculty->id);
                    }
                }
            } else {
                session()->flash('error', 'One or more selected fields are invalid.');
                dd('One or more selected electives are invalid.');
            }
        } else {
            session()->flash('error', 'Please select at least one elective from each category.');
            dd('Please fill all fields from each category.');
        }
    }

    private function validateCores($cores)
    {

        if (count($cores) < 3 || count($cores) > 4) return false;
        foreach ($cores as $core) {
            if (!in_array(strtolower($core), $this->coreSubjects)) return false;
            if (!preg_match('/^[A-Za-z ]+$/', $core)) return false;
        }
        Log::info('Core subjects validated successfully.');
        return true;
    }

    private function validateElectives($electives)
    {
        foreach ($electives as $elective) {
            if (ElectivesEnum::tryFrom($elective) === null) return false;
        }
        Log::info('Electives validated successfully.');
        return true;
    }

    private function validateProgrammeName($name)
    {
        return preg_match('/^[A-Za-z ]+$/', $name);
    }

    private function validateProgrammeType($type)
    {
        Log::info('Programme type exists: ' . ProgrammeType::where('type', '=', $type)->exists());
        return ProgrammeType::where('type', '=', $type)->exists();
    }

    private function validateGrade($grade)
    {
        return preg_match('/^[A-F][1-9]$/', $grade);
    }

    private function electivesCanBeClassifiedAsAny(array $elective)
    {
        $allPresent = false;
        foreach (ElectivesEnum::cases() as $case) {
            if (in_array($case->value, $elective)) {
                $allPresent = true;
            } else {
                $allPresent = false;
                break;
            };
        }
        return $allPresent;
    }

    private function getElective()
    {

        $electives = ElectiveSubject::where('elective_one', '=', 'any')->where('elective_two', '=', 'any')->where('elective_three', '=', 'any')->get();

        if ($electives->count() === 0) {
            ElectiveSubject::create(['elective_one' => 'any', 'elective_two' => 'any', 'elective_three' => 'any']);
            $elective = $this->getElective();
            return $elective;
        }

        // create extra elective with any any any for editable purposes
        else if ($electives->count() === 1) {
            $attributes = [];
            $attributes['elective_one'] = $this->updateElectiveValue(1);
            $attributes['elective_two'] = $this->updateElectiveValue(2);
            $attributes['elective_three'] = $this->updateElectiveValue(3);

            $alreadyExists = ElectiveSubject::where('elective_one', '=', $attributes['elective_one'])->where('elective_two', '=', $attributes['elective_two'])->where('elective_three', '=', $attributes['elective_three'])->first();
            if ($alreadyExists) return $alreadyExists;
            else return ElectiveSubject::create($attributes);
        }
    }

    private function updateElectiveValue($electiveNumber)
    {
        $elective = match ($electiveNumber) {
            1 => $this->electivesCanBeClassifiedAsAny($this->electiveOne) ? 'any' : $this->makeElectiveString($this->electiveOne),
            2 => $this->electivesCanBeClassifiedAsAny($this->electiveTwo) ? 'any' : $this->makeElectiveString($this->electiveTwo),
            3 => $this->electivesCanBeClassifiedAsAny($this->electiveThree) ? 'any' : $this->makeElectiveString($this->electiveThree),
        };

        return $elective;
    }

    public function getProgrammeTypeId($typeName)
    {
        $type = ProgrammeType::where('type', '=', $typeName)->first();
        return $type?->id;
    }

    public function getCores()
    {
        if (count($this->selectedCores) === 4) {
            $scienceId = array_search('science', $this->selectedCores);
            $socialId = array_search('social', $this->selectedCores);
            $coreWithSocial = $scienceId !== false ? $this->filterCores($scienceId) : null;
            $coreWithScience = $socialId !== false ? $this->filterCores($socialId) : null;
            return [$coreWithScience, $coreWithSocial];
        } else {
            $query = CoreSubject::query();
            foreach ($this->selectedCores as $core) {
                $core = strtolower($core);
                $query->where($core, '=', 'required');
            }
            return [$query->first()];
        }
    }

    public function getGradeId($grade)
    {
        $value = (int) substr($grade, -1);
        $grade = Grade::where('value', '=', $value)->first();
        LOG::INFO('Grade fetched: ',  [$grade]);
        return $grade->id;
    }

    private function filterCores($ID)
    {
        $query = CoreSubject::query();
        foreach ($this->selectedCores as $id => $core) {
            if ($id !== $ID) $query->where(strtolower($core), '=', 'required');
        }
        return $query->first();
    }

    private function makeElectiveString($electives)
    {
        return str_replace(',', '|', implode(',', $electives));
    }

    public function toggleCoreSubject($subject)
    {
        if (in_array($subject, $this->selectedCores)) {
            $this->selectedCores = array_filter(
                $this->selectedCores,
                fn($item) => $item !== $subject
            );
        } else {
            $this->selectedCores[] = $subject;
        }
    }

    public function toggleElectiveOne($elective)
    {
        if (in_array($elective, $this->electiveOne)) {
            $this->electiveOne = array_filter(
                $this->electiveOne,
                fn($item) => $item !== $elective
            );
        } else {
            $this->electiveOne[] = $elective;
        }
    }

    public function toggleElectiveTwo($elective)
    {
        if (in_array($elective, $this->electiveTwo)) {
            $this->electiveTwo = array_filter(
                $this->electiveTwo,
                fn($item) => $item !== $elective
            );
        } else {
            $this->electiveTwo[] = $elective;
        }
    }
    public function toggleElectiveThree($elective)
    {
        if (in_array($elective, $this->electiveThree)) {
            $this->electiveThree = array_filter(
                $this->electiveThree,
                fn($item) => $item !== $elective
            );
        } else {
            $this->electiveThree[] = $elective;
        }
    }

    #[On('openProgrammeAddModal')]
    public function openModal($faculty)
    {
        $this->dispatch('programmeAddModelOpen');
        // sleep(1);
        $this->faculty = $faculty;
        $this->showModal = true;
    }

    #[On('toggleSelectAllElectives')]
    public function toggleSelectAllElectives($elective)
    {
        if ($elective === 'e1') {
            $this->selectAllElectivesForElectiveOne = !$this->selectAllElectivesForElectiveOne;
            $this->electiveOne = $this->selectAllElectivesForElectiveOne ? array_map(fn($e) => $e->value, ElectivesEnum::cases()) : [];
        } elseif ($elective === 'e2') {
            $this->selectAllElectivesForElectiveTwo = !$this->selectAllElectivesForElectiveTwo;
            $this->electiveTwo = $this->selectAllElectivesForElectiveTwo ? array_map(fn($e) => $e->value, ElectivesEnum::cases()) : [];
        } elseif ($elective === 'e3') {
            $this->selectAllElectivesForElectiveThree = !$this->selectAllElectivesForElectiveThree;
            $this->electiveThree = $this->selectAllElectivesForElectiveThree ? array_map(fn($e) => $e->value, ElectivesEnum::cases()) : [];
        }
    }

    public function resetSelections()
    {
        $this->minimumCoreGrade = null;
        $this->minimumElectiveGrade = null;
        $this->programme_name = null;
        $this->programme_type = null;
    }
}
