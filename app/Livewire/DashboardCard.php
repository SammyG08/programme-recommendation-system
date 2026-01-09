<?php

namespace App\Livewire;

use App\Models\Faculty;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class DashboardCard extends Component
{
    public $faculty;
    public $updatedFaculty;

    public $showDashboardCards = true;

    public function mount(Faculty $faculty)
    {
        $this->faculty = $faculty;
    }
    public function render()
    {

        return view('livewire.dashboard-card');
    }

    #[On('programmeAdded')]
    public function refreshCount($facultyId)
    {
        $this->updatedFaculty = Faculty::withCount('programmes')->find($facultyId);
        $this->showDashboardCards = true;
    }

    public function openAddProgrammeModal()
    {
        $this->dispatch('openProgrammeAddModal', $this->faculty->faculty_name);
    }

    #[On('programmeAddModelOpen')]
    public function hideDashboardCards()
    {
        $this->showDashboardCards = false;
    }
}
