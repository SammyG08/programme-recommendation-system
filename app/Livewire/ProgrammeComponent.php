<?php

namespace App\Livewire;

use App\Models\Programme;
use App\Models\ProgrammeType;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ProgrammeComponent extends Component
{
    use WithPagination;

    public $order = 'desc';
    public $programmeType = 'all';

    public $selectedProgrammeId = null;

    public function render()
    {
        $programmes = $this->getProgrammes();

        return view('livewire.programme-component', ['programmes' => $programmes]);
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
        return Programme::with('faculty', 'electiveSubject', 'coreSubject', 'passGradeForCores', 'passGradeForElectives')->find($this->selectedProgrammeId);
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

        $uniqueProgrammes = $programmesQuery->whereIn('id', $tempProgrammesId->toArray())->paginate(7);
        return $uniqueProgrammes;
    }
}
