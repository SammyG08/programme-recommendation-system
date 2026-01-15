<?php

namespace App\Livewire;

use App\ElectivesEnum;
use App\Models\Programme;
use App\Models\ProgrammeType;
use Exception;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ProgrammeComponent extends Component
{
    use WithPagination;

    public $order = 'desc';
    public $programmeType = 'all';
    public $selectedProgrammeId = null;

    public $programme_name = null;
    public $programme_type = null;
    public $selectedElectives = [];
    public $selectedCores = [];

    public $electiveOne = [];
    public $electiveTwo = [];
    public $electiveThree = [];

    public $selectAllElectivesForElectiveOne = false;
    public $selectAllElectivesForElectiveTwo = false;
    public $selectAllElectivesForElectiveThree = false;


    public function render()
    {
        $programmes = $this->getProgrammes();
        $electives = ElectivesEnum::cases();
        $cores = ['English', 'Mathematics', 'Science', 'Social'];

        return view('livewire.programme-component', compact('programmes', 'electives', 'cores'));
    }

    private function getProgrammes()
    {

        $programQuery = Programme::query();

        $programmeTypeId = $this->getProgrammeTypes($this->programmeType);
        // dd($programmeTypeId);
        $programmesQuery = match ($this->order) {
            'asc' => $programQuery->orderBy('created_at', 'asc')->whereIn('programme_type_id', $programmeTypeId),
            'desc' => $programQuery->orderBy('created_at', 'desc')->whereIn('programme_type_id', $programmeTypeId),
        };

        return $this->formatProgrammes($programmesQuery);
    }

    public function updateOrder()
    {
        if ($this->order === 'asc') {
            $this->order = 'desc';
        } elseif ($this->order === 'desc') {
            $this->order = 'asc';
        }
    }


    public function updateProgrammeType()
    {
        $this->programmeType = match ($this->programmeType) {
            'all' => 'Degree',
            'Degree' => 'Diploma',
            'Diploma' => 'all',
        };
    }

    private function getProgrammeTypes($type)
    {
        if ($type === 'all') {
            $pType = ProgrammeType::pluck('id');
            return $pType->toArray();
        }
        $pType = ProgrammeType::where('type', '=', $type)->first();
        return [$pType->id];
    }

    #[Computed]
    public function selectedProgramme()
    {
        return Programme::with('faculty', 'electiveSubject', 'coreSubject', 'passGradeForCores', 'passGradeForElectives', 'programmeType')->find($this->selectedProgrammeId);
    }

    public function setFields()
    {
        $programme = $this->selectedProgramme;
        $this->selectedCores = $programme->coreSubject->coreSubjects($programme->programme_name);
        $this->programme_name = $programme->programme_name;
        $this->programme_type = $programme->programmeType->type;
        $this->dispatch('setting-complete');
    }

    private function formatProgrammes($programmesQuery)
    {
        $programmesWithNoDuplicateFiltered = $programmesQuery->get();

        $programmesWithSameNameName = collect();
        $tempProgrammesId = collect();
        foreach ($programmesWithNoDuplicateFiltered as $id => $programme) {
            if ($programme->twoOrMoreProgrammesWithSameName()) $programmesWithSameNameName->push($programme->programme_name);
            else $tempProgrammesId->push($programme->id);
        }

        $programmesWithSameNameName = $programmesWithSameNameName->unique();
        foreach ($programmesWithSameNameName as $programmeName) {
            $matchingProgrammes = Programme::where('programme_name', '=', $programmeName)->first();
            $tempProgrammesId->push($matchingProgrammes->id);
        }

        $uniqueProgrammes = $programmesQuery->whereIn('id', $tempProgrammesId->toArray())->paginate(5);
        return $uniqueProgrammes;
    }

    public function delete($programmeId)
    {
        $programme = Programme::find($programmeId);
        if ($programme->twoOrMoreProgrammesWithSameName())
            Programme::where('programme_name', '=', $programme->programme_name)->delete();
        else $programme->delete();
        $this->dispatch('programme-deleted');
        $this->selectedProgrammeId = null;
    }

    public function updateProgramme()
    {
        try {
            $this->dispatch('update-programme', pid: $this->selectedProgrammeId, uname: $this->programme_name, utype: $this->programme_type, ucores: $this->selectedCores, ue1: $this->electiveOne, ue2: $this->electiveTwo, ue3: $this->electiveThree)->to(ProgrammeAdd::class);
            $this->dispatch('update-complete');
        } catch (Exception $e) {
            dd($e->getMessage());
        }
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
}
