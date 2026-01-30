<?php

namespace App\Livewire;

use App\Imports\BulkProgrammeImport;
use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use League\Config\Exception\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class DashboardCard extends Component
{
    use WithFileUploads;
    public $updatedFacultyId;

    #[Validate(['file.*' => 'required|file|mimes:xlsx|max:10240'])]
    public $file = [];

    public $showDashboardCards = true;

    public function render()
    {
        $faculties = Faculty::with('programmes')->get();
        return view('livewire.dashboard-card', compact('faculties'));
    }

    #[Computed]
    public function getUpdatedFaculty()
    {
        return Faculty::withCount('programmes')->with('latestProgramme')->find($this->updatedFacultyId);
    }

    public function openAddProgrammeModal($faculty)
    {
        $this->dispatch('openProgrammeAddModal', $faculty->faculty_name);
    }

    #[On('programmeAddModelOpen')]
    public function hideDashboardCards()
    {
        $this->showDashboardCards = false;
    }

    public function uploadFile($facultyId)
    {
        try {
            $uploadedFile = $this->file[$facultyId] ?? null;
            Excel::import(new BulkProgrammeImport($facultyId), $uploadedFile->getRealPath());
            $this->file = [];
            $this->dispatch('upload-done');
        } catch (Throwable $e) {
            $this->dispatch('upload-failed');
        }
    }
}
