<?php

namespace App\Livewire;

use App\ElectivesEnum;
use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Programme;
use App\Models\ProgrammeType;
use Exception;
use Livewire\Component;
use Livewire\Attributes\On;
use Throwable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class ProgrammeAdd extends Component
{
    public $selectAllElectivesForElectiveOne = false;
    public $selectAllElectivesForElectiveTwo = false;
    public $selectAllElectivesForElectiveThree = false;
    public $electiveOne = [];
    public $electiveTwo = [];
    public $electiveThree = [];

    public $coreSubjects = ['english', 'mathematics', 'science', 'social'];
    // public $minimumCoreGrade;
    // public $minimumElectiveGrade;

    public $selectedCores = [];

    public $facultyId;
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
        Log::info('Submitted data:', [$this->electiveOne, $this->electiveTwo, $this->electiveThree, $this->selectedCores, $this->programme_name, $this->programme_type]);

        // dd($this->validateProgrammeType($this->programme_type));
        if (count($this->electiveOne) !== 0 && count($this->electiveTwo) !== 0 && count($this->electiveThree) !== 0 && count($this->selectedCores) !== 0 && !$this->programme_name == '' && !$this->programme_type == '') {
            try {
                $this->validateElectives($this->electiveOne);
                $this->validateElectives($this->electiveTwo);
                $this->validateElectives($this->electiveThree);
                $this->ensureSelectedElectivesAreNotAllTheSame($this->electiveOne, $this->electiveTwo, $this->electiveThree);
                $this->validateProgrammeName($this->programme_name);
                $this->validateProgrammeType($this->programme_type);
                $this->validateCores($this->selectedCores);

                $elective = $this->getElective($this->electiveOne, $this->electiveTwo, $this->electiveThree);
                $faculty = Faculty::find($this->facultyId);
                $cores = $this->getCores();
                $grade = $this->getGrade($this->programme_type);
                $minimumCoreGradeId = $this->getGradeId($grade);
                $minimumElectiveGradeId = $minimumCoreGradeId;
                $programmeTypeId = $this->getProgrammeTypeId($this->programme_type);
                $programme_name = $this->programme_name;
                $p = Programme::where('programme_name', $programme_name)->get();
                if ($p->count()) session()->flash('error', 'Programme already exists');
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
                    }
                }
                $this->resetSelections();
                $this->dispatch('programme-added', id: $this->facultyId);
            } catch (Exception $e) {
                session()->flash('error', $e->getMessage());
                $this->dispatch('adding-programme-error');
            }
        } else {
            session()->flash('error', 'Please fill all fields from each category.');
            // dd('Please fill all fields from each category.');
            $this->dispatch('adding-programme-error');
        }
    }

    private function validateCores($cores)
    {

        if (count($cores) < 3 || count($cores) > 4) throw new Exception('Please make sure to select three or more core subjects.');
        foreach ($cores as $core) {
            if (!in_array(strtolower($core), $this->coreSubjects)) throw new Exception('Please ensure all core subjects selected are from the list given.');
            if (!preg_match('/^[A-Za-z ]+$/', $core)) throw new Exception('Please ensure the format of all core subjects selected is valid.');
        }
        Log::info('Core subjects validated successfully.');
    }

    private function validateElectives($electives)
    {
        foreach ($electives as $elective) {
            if (ElectivesEnum::tryFrom($elective) === null) throw new Exception('Please ensure all elective subjects selected are from the list given.');
        }
        Log::info('Electives validated successfully.');
    }

    private function ensureSelectedElectivesAreNotAllTheSame($e1, $e2, $e3)
    {
        $combined = array_unique([...$e1, ...$e2, ...$e3]);
        if (count($combined) < 3) throw new Exception('Selected electives must result in a minimum of three unique subjects.');
    }

    private function validateProgrammeName($name)
    {
        $result = preg_match('/^[A-Za-z ]+$/', $name);
        if (!$result) throw new Exception('Please ensure the programme name you type is in a valid format.');
    }

    private function validateProgrammeType($type)
    {
        Log::info('Programme type exists: ' . ProgrammeType::where('type', '=', $type)->exists());
        $result = ProgrammeType::where('type', '=', $type)->exists();
        if (!$result) throw new Exception('Please ensure the programme type you select is from the list given');
    }

    private function validateGrade($grade)
    {
        $result = preg_match('/^[A-F][1-9]$/', $grade);
        if (!$result) throw new Exception('Please ensure the grade you select is from the list given.');
    }

    private function getGrade($programmeType)
    {
        $grade = match ($programmeType) {
            'Degree' => 'C6',
            'Diploma' => 'D7'
        };

        return $grade;
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

    private function getElective($e1, $e2, $e3)
    {

        $electives = ElectiveSubject::where('elective_one', '=', 'any')->where('elective_two', '=', 'any')->where('elective_three', '=', 'any')->get();

        if ($electives->count() === 0) {
            ElectiveSubject::create(['elective_one' => 'any', 'elective_two' => 'any', 'elective_three' => 'any']);
            $elective = $this->getElective($e1, $e2, $e3);
            return $elective;
        }

        // create extra elective with any any any for editable purposes
        else if ($electives->count() === 1) {
            $attributes = [];
            $attributes['elective_one'] = $this->updateElectiveValue(1, $e1, $e2, $e3);
            $attributes['elective_two'] = $this->updateElectiveValue(2, $e1, $e2, $e3);
            $attributes['elective_three'] = $this->updateElectiveValue(3, $e1, $e2, $e3);

            $alreadyExists = ElectiveSubject::where('elective_one', '=', $attributes['elective_one'])->where('elective_two', '=', $attributes['elective_two'])->where('elective_three', '=', $attributes['elective_three'])->first();
            if ($alreadyExists) return $alreadyExists;
            else return ElectiveSubject::create($attributes);
        }
    }

    private function updateElectiveValue($electiveNumber, $e1, $e2, $e3)
    {
        $elective = match ($electiveNumber) {
            1 => $this->electivesCanBeClassifiedAsAny($e1) ? 'any' : $this->makeElectiveString($e1),
            2 => $this->electivesCanBeClassifiedAsAny($e2) ? 'any' : $this->makeElectiveString($e2),
            3 => $this->electivesCanBeClassifiedAsAny($e3) ? 'any' : $this->makeElectiveString($e3),
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

    #[On('openModal')]
    public function openModal($fid)
    {
        $this->facultyId = $fid;
    }

    #[Computed]
    public function getFaculty()
    {
        return Faculty::withCount('programmes')->find($this->facultyId);
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
        $this->programme_name = null;
        $this->programme_type = null;
        $this->selectedCores = [];
        $this->electiveOne = [];
        $this->electiveTwo = [];
        $this->electiveThree = [];
    }


    #[On('update-programme')]
    public function handleUpdate($pid, $uname, $utype, $ucores, $ue1, $ue2, $ue3)
    {
        try {
            $this->validateProgrammeName($uname);
            $this->validateProgrammeType($utype);
            $this->validateCores($ucores);
            $this->validateElectives($ue1);
            $this->validateElectives($ue2);
            $this->validateElectives($ue3);
            $this->ensureSelectedElectivesAreNotAllTheSame($ue1, $ue2, $ue3);

            $this->selectedCores = $ucores;
            $programme = Programme::find($pid);
            if ($programme->twoOrMoreProgrammesWithSameName())
                Programme::where('programme_name', '=', $programme->programme_name)->where('id', '!=', $pid)->delete();

            $elective = $this->getElective($ue1, $ue2, $ue3);
            $cores = $this->getCores();
            $grade = $this->getGrade($utype);
            $programmeTypeId = $this->getProgrammeTypeId($utype);
            $gradeId = $this->getGradeId($grade);
            foreach ($cores as $idx => $core) {
                if ($core) {
                    $data = ['elective_subject_id' => $elective->id, 'core_subject_id' => $core->id, 'programme_type_id' => $programmeTypeId, 'lowest_grade_for_cores' => $gradeId, 'lowest_grade_for_electives' => $gradeId, 'programme_name' => $uname];
                    if ($idx === 0)
                        $programme->update($data);
                    else {
                        $data['faculty_id'] = $programme->faculty_id;
                        Programme::create($data);
                    }
                }
            }
        } catch (Throwable $e) {
            Log::info('Error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
