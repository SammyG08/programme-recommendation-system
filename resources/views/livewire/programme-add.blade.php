<div x-data = "{showModal: false, saving:false, error:false}" x-show = "showModal" x-cloak
    x-on:modal-open.window ="showModal = true; $wire.set('facultyId', $event.detail.fid); $wire.resetSelections()"
    x-on:programme-added.window = "showModal = false" x-on:click.outside="showModal = false; $dispatch('modal-closed')"
    x-on:saving.window="saving = true"
    :class="saving ? 'opacity-50 pointer-events-none' : 'opacity-100 pointer-events-auto'"
    x-on:adding-programme-error.window="saving = false"
    class="relative flex transition-all duration-500 flex-col justify-evenly items-start gap-5 w-full md:w-[70%] lg:w-full 2xl:w-[80%] h-full border border-blue-900 p-2 2xl:p-5 mt-20 2xl:mt-5 rounded-xl overflow-hidden">
    <div x-show="saving" x-cloak class="h-1 bg-white/20 absolute top-0 right-10 left-10 overflow-hidden"
        x-on:saving.window="saving=true">
        <div class="h-1 bg-blue-500/50 w-50" id="loadingBar"></div>
    </div>


    <div x-data="{ showError: false }" x-show="showError" x-cloak
        x-on:adding-programme-error.window="showError=true; setTimeout(() => showError = false, 3000)"
        x-transition.opacity.duration.500ms
        class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/80 h-full w-full">

        <div class=" bg-neutral-800 p-5 rounded-xl">
            <span class="text-sm text-white">{{ session('error') }}</span>
        </div>
    </div>


    <div class="w-full flex items-center justify-end gap-5 p-2 2xl:p-0">
        <div class="flex items-center justify-center gap-5 w-full">
            <h3 class="text-md md:text-lg font-bold text-white">
                {{ $this->getFaculty ? $this->getFaculty->faculty_name : 'Add Programme For: ' }}</h3>
            {{-- @if ($this->getFaculty)<i class="bi bi-x-circle text-lg text-white transition-colors duration-300 hover:text-white/70"></i>@endif --}}
        </div>
        <button class="px-2 bg-blue-500 rounded-lg transition-colors duration-400 hover:bg-blue-700 text-white" x-data
            x-on:click="$dispatch('saving')" wire:click="saveProgramme"><span class="text-sm">Save</span></button>
    </div>
    <div class="flex lg:flex-row flex-col w-full items-center justify-center bg-gray-900 p-2 2xl:p-5 rounded-xl gap-10">
        <input type="text" wire:model="programme_name"
            class="bg-black border border-white/20 rounded-lg p-2 text-white w-full text-md 2xl:w-80"
            value="{{ $programme_name }}" placeholder="Enter programme name here...">
        <select wire:model="programme_type"
            class="bg-black border border-white/20 rounded-lg p-2 text-white text-md w-full 2xl:w-80"
            value="{{ $programme_type }}">
            <option value=""selected hidden>Select programme type</option>
            <option value="Degree">Degree</option>
            <option value="Diploma">Diploma</option>
        </select>
    </div>
    <div class="flex flex-col w-full items-center justify-center bg-gray-900 p-2 2xl:p-5 rounded-xl gap-7">
        <span class="text-white font-bold text-sm md:text-md 2xl:text-lg rounded-xl translate-y-2">Select required
            core
            subjects</span>
        <hr class="w-full text-white/20">
        <form class="flex overflow-x-auto justify-start items-center flex-nowrap w-full gap-7 pb-5 2xl:pb-0">
            @foreach ($coreSubjects as $subj)
                <button
                    class="transition-colors duration-300 hover:bg-blue-500 w-full text-sm 2xl:text-md {{ in_array($subj, $selectedCores) ? 'bg-blue-500 border-white/50 border-2' : 'border bg-cyan-900 border-white/30' }} rounded-xl flex p-2 justify-center items-center gap-5 text-white"
                    value="{{ $subj }}" wire:click.prevent="toggleCoreSubject('{{ $subj }}')">
                    <i class="bi bi-mortarboard"></i>
                    <span class="font-bold">{{ ucwords($subj) }}</span>
                </button>
            @endforeach
        </form>
    </div>

    <div class="flex lg:flex-row flex-col items-start justify-center gap-3 w-full bg-gray-900 p-2 2xl:p-5 rounded-xl">
        <div class="flex flex-col lg:w-[33.3%] w-full items-start justify-start relative">
            <div
                class="flex justify-between gap-2 items-start p-3 w-full bg-neutral-950 rounded-xl border border-white/20">
                <span class="text-white font-bold text-sm md:text-md 2xl:text-lg">Elective One</span>
                <button wire:click.prevent="$dispatch('toggleSelectAllElectives', 'e1')"
                    class="text-sm flex items-center gap-3 rounded-lg {{ $selectAllElectivesForElectiveOne ? 'bg-blue-500' : 'bg-cyan-900' }} border border-white/30 p-1 transition-colors duration-300 hover:bg-cyan-950">
                    <span class="text-white">Select all</span>
                </button>
            </div>
            <div
                class="w-full bg-black/50 p-2 2xl:p-5 mt-2 mb-7 2xl:mb-0 rounded-xl programmeDropdown transition-transform duration-500 slideDown">
                <div class="w-full flex overflow-x-auto flex-nowrap gap-3 items-start justify-start py-5">
                    @if (count($electives))
                        @foreach ($electives as $elective)
                            <button wire:click.prevent="toggleElectiveOne('{{ $elective->value }}')"
                                class="whitespace-nowrap transition-colors duration-300 hover:bg-blue-500  text-sm 2xl:text-md {{ in_array($elective->value, $electiveOne) ? 'bg-blue-500 border-white/50 border-2' : 'border bg-cyan-900 border-white/30' }}  rounded-xl flex p-2 justify-start items-center gap-5 text-white">
                                <span class="font-bold">{{ $elective->value }}</span>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
        <div class="flex flex-col w-full lg:w-[33.3%] items-start justify-start relative">
            <div
                class="flex justify-between gap-2 items-start p-3 w-full bg-neutral-950 rounded-xl border border-white/20">
                <span class="text-white font-bold text-sm md:text-md 2xl:text-lg">Elective Two</span>
                <button wire:click.prevent="$dispatch('toggleSelectAllElectives', 'e2')"
                    class="text-sm flex items-center gap-3 rounded-lg {{ $selectAllElectivesForElectiveTwo ? 'bg-blue-500' : 'bg-cyan-900' }} border border-white/30 p-1 transition-colors duration-300 hover:bg-cyan-950">
                    <span class="text-white">Select all</span>
                </button>
            </div>
            <div
                class="w-full bg-black/50 p-2 2xl:p-5 mt-2 mb-7 2xl:mb-0 rounded-xl programmeDropdown transition-transform duration-500 slideDown">
                <div class="w-full flex overflow-x-auto flex-nowrap gap-3 items-start justify-start py-5">
                    @if (count($electives))
                        @foreach ($electives as $elective)
                            <button wire:click.prevent="toggleElectiveTwo('{{ $elective->value }}')"
                                class="whitespace-nowrap transition-colors border duration-300 hover:bg-blue-500 w-full text-sm 2xl:text-md {{ in_array($elective->value, $electiveTwo) ? 'bg-blue-500 border-white/50 border-2' : 'border bg-cyan-900 border-white/30' }}  rounded-xl flex p-2 justify-start items-center gap-5 text-white">

                                <span class="font-bold">{{ $elective->value }}</span>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
        <div class="flex flex-col w-full lg:w-[33.3%] items-start justify-start relative">
            <div
                class="flex justify-between gap-2 items-start p-3 w-full bg-neutral-950 rounded-xl border border-white/20">
                <span class="text-white font-bold text-sm md:text-md 2xl:text-lg">Elective Three</span>
                <button wire:click.prevent="$dispatch('toggleSelectAllElectives', 'e3')"
                    class="text-sm flex items-center gap-3 rounded-lg {{ $selectAllElectivesForElectiveThree ? 'bg-blue-500' : 'bg-cyan-900' }} border border-white/30 p-1 transition-colors duration-300 hover:bg-cyan-950">
                    <span class="text-white">Select all</span>
                </button>
            </div>
            <div
                class="w-full bg-black/50 p-2 2xl:p-5 mt-2 mb-7 2xl:mb-0 rounded-xl programmeDropdown transition-transform duration-500 slideDown">
                <div class="w-full flex overflow-x-auto flex-nowrap gap-3 items-start justify-start py-5">
                    @if (count($electives))
                        @foreach ($electives as $elective)
                            <button wire:click.prevent="toggleElectiveThree('{{ $elective->value }}')"
                                class="whitespace-nowrap transition-colors border duration-300 hover:bg-blue-500 w-full text-sm 2xl:text-md {{ in_array($elective->value, $electiveThree) ? 'bg-blue-500 border-white/50 border-2' : 'border bg-cyan-900 border-white/30' }} rounded-xl flex p-2 justify-start items-center gap-5 text-white">

                                <span class="font-bold">{{ $elective->value }}</span>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

    </div>

    {{-- <div class="flex lg:flex-row flex-col items-start justify-center gap-3 w-full bg-gray-900 p-2 2xl:p-5 rounded-xl">
        <form action="" class="">
            <select wire:model="minimumCoreGrade" class="bg-black rounded-lg px-3 py-4 text-white w-full lg:w-auto"
                value="{{ $minimumCoreGrade }}">
                <option value="" selected hidden>Minimum grade for core subjects required for programme
                </option>
                <option value="A1">A1</option>
                <option value="B2">B2</option>
                <option value="B3">B3</option>
                <option value="C4">C4</option>
                <option value="C5">C5</option>
                <option value="C6">C6</option>
                <option value="D7">D7</option>
                <option value="E8">E8</option>
                <option value="F9">F9</option>
            </select>
        </form>
        <form action="" class="">
            <select wire:model="minimumElectiveGrade" class="bg-black rounded-lg px-3 py-4 text-white w-full lg:w-auto"
                value="{{ $minimumElectiveGrade }}">
                <option value="" hidden selected>Minimum grade for elective subjects required for
                    programme</option>
                <option value="A1">A1</option>
                <option value="B2">B2</option>
                <option value="B3">B3</option>
                <option value="C4">C4</option>
                <option value="C5">C5</option>
                <option value="C6">C6</option>
                <option value="D7">D7</option>
                <option value="E8">E8</option>
                <option value="F9">F9</option>
            </select>
        </form>
    </div> --}}
</div>
