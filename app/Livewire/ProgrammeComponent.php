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
use Log;

class ProgrammeComponent extends Component
{
    use WithPagination;

    public $createdAtOrder = 'desc';
    public $updatedAtOrder = 'desc';
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
        [$column, $order] = $this->getColumnAndOrder();
        $programmes = $this->getProgrammes(column: $column, order: $order);
        $electives = ElectivesEnum::cases();
        $cores = ['English', 'Mathematics', 'Science', 'Social'];

        return view('livewire.programme-component', compact('electives', 'cores', 'programmes'));
    }

    private function getProgrammes(string $column = 'created_at', string $order = 'desc')
    {

        $programQuery = Programme::query();

        $programmeTypeId = $this->getProgrammeTypes($this->programmeType);
        // dd($programmeTypeId);
        $programmesQuery = match ($order) {
            'asc' => $programQuery->orderBy($column, 'asc')->whereIn('programme_type_id', $programmeTypeId),
            'desc' => $programQuery->orderBy($column, 'desc')->whereIn('programme_type_id', $programmeTypeId),
        };

        return $this->formatProgrammes($programmesQuery);
    }

    public function updateOrderForCreatedAt()
    {
        $this->updatedAtOrder = 'desc';
        $this->createdAtOrder = $this->createdAtOrder === 'asc' ? 'desc' : 'asc';
    }

    public function updateOrderForUpdatedAt()
    {
        $this->createdAtOrder = 'desc';
        $this->updatedAtOrder = $this->updatedAtOrder === 'asc' ? 'desc' : 'asc';
    }


    private function getColumnAndOrder()
    {
        if ($this->createdAtOrder === $this->updatedAtOrder) return ['created_at', $this->createdAtOrder];
        if ($this->createdAtOrder === 'desc' && $this->updatedAtOrder === 'asc') return ['updated_at', $this->updatedAtOrder];
        if ($this->createdAtOrder === 'asc' && $this->updatedAtOrder === 'desc') return ['created_at', $this->createdAtOrder];
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
        $this->dispatch('update-programme', pid: $this->selectedProgrammeId, uname: $this->programme_name, utype: $this->programme_type, ucores: $this->selectedCores, ue1: $this->electiveOne, ue2: $this->electiveTwo, ue3: $this->electiveThree)->to(ProgrammeAdd::class);
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
