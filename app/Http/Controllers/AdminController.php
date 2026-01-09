<?php

namespace App\Http\Controllers;

use App\ElectivesEnum;
use App\Models\Faculty;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function index(Request $requeset)
    {
        $electives = ElectivesEnum::cases();
        $faculties = Faculty::withCount('programmes')->get();
        return view('admin', compact('faculties', 'electives'));
    }
}
