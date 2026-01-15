<?php

namespace App\Livewire;

use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\On;

class DashboardCard extends Component
{
    public $updatedFacultyId;

    public $showDashboardCards = true;

    public function render()
    {
        $faculties = Faculty::withCount('programmes')->get();
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
}
