<div class="flex flex-col gap-4 items-start justify-start md:w-[70%] lg:w-full 2xl:w-[80%] w-full mt-20 2xl:mt-5" x-cloak
    x-data="{ showing: true }" x-on:modal-open.window="showing = false" x-show="showing"
    x-on:modal-closed.window="showing = true" x-on:programme-added.window="$wire.$refresh(); showing=true">
    @if (!$selectedProgrammeId)
        <div class="transition-all duration-500 flex-col justify-evenly items-start gap-5 w-full h-full border border-blue-900 p-2 2xl:p-5  rounded-xl"
            wire:key="programme-list">
            <div class="w-full flex justify-end items-center bg-gray-700 p-1 border-2 border-blue-800">
                <div class="w-full flex justify-center items-center gap-2">
                    <input type="text"
                        class="w-full lg:w-[50%] 2xl:w-[30%] bg-slate-800 text-white px-2 py-1 rounded-lg border border-white/20"
                        placeholder="Programme name">
                </div>
                <button wire:click="updateProgrammeType()"
                    class="flex justify-end items-center px-2 py-1 bg-blue-500 rounded-lg transition-colors duration-500 hover:bg-blue-700">
                    <i class="bi bi-filter-circle text-white text-md"></i>
                </button>

            </div>
            <div class="overflow-x-auto w-full flex flex-col gap-5">
                <table class="w-full text-sm text-left text-white">
                    <thead class="text-xs text-white uppercase bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 whitespace-nowrap">
                                Programme Name
                            </th>
                            <th class="px-6 py-3 whitespace-nowrap">
                                Faculty
                            </th>
                            <th class="px-6 py-3 whitespace-nowrap">
                                Added on <i class="{{ $order === 'asc' ? 'bi bi-sort-down' : 'bi bi-sort-up' }} ml-1"
                                    wire:click="updateOrder()"></i>
                            </th>
                            <th class="px-6 py-3 whitespace-nowrap">
                                Last Updated<i class="{{ $order === 'asc' ? 'bi bi-sort-down' : 'bi bi-sort-up' }} ml-1"
                                    wire:click="updateOrder()"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programmes as $programme)
                            <tr class="bg-gray-900 border-b border-gray-700 hover:bg-gray-800"
                                wire:click="$set('selectedProgrammeId', {{ $programme->id }})"
                                wire:key="programme-{{ $programme->id }}">
                                <td class="text-xs lg:text-sm px-6 py-4 cursor-default whitespace-nowrap">
                                    {{ $programme->programme_name }}
                                </td>
                                <td class="text-xs px-6 lg:text-sm py-4 cursor-default whitespace-nowrap">
                                    {{ $programme->faculty->faculty_name }}
                                </td>
                                <td class="text-xs lg:text-sm px-6 py-4 cursor-default whitespace-nowrap">
                                    {{ $programme->created_at->toFormattedDateString() }}
                                </td>
                                <td class="text-xs lg:text-sm px-6 py-4 cursor-default whitespace-nowrap">
                                    {{ $programme->updated_at->toFormattedDateString() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="w-full bg-gray-900/15 px-2 mt-3">
                {{ $programmes->links() }}

            </div>
        </div>
    @else
        <div class="transition-all duration-500 flex-col justify-evenly items-center gap-5 w-full h-full p-2 2xl:p-5 rounded-xl relative border border-blue-950"
            x-data="{ showing: true}" x-cloak x-show="showing" x-on:edit-programme.window="showing = false" x-on:update-cancelled.window="showing=true" x-on:update-complete.window="$wire.$refresh(); showing=true;"
            wire:key="selected-programme-{{ $selectedProgrammeId }}">

            <div class="w-full flex items-center justify-center rounded-lg gap-2 bg-slate-950 p-1 lg:p-5">
                <div
                    class="flex flex-col justify-start items-center gap-2 p-3 text-white w-full rounded-lg bg-black border border-blue-950 relative">

                    <div
                        class="font-semibold text-white bg-gray-700 w-auto flex items-center justify-center p-3 rounded-lg -translate-y-9 border-2 border-white">
                        <i class="bi bi-mortarboard-fill mr-2 text-xs lg:text-sm"></i>
                        <span class="uppercase text-xs lg:text-sm">{{ $this->selectedProgramme->programme_name }}
                            Details</span>
                    </div>
                    <i class="absolute top-0 right-0 p-2 bi bi-x-circle text-sm" wire:click="$set('selectedProgrammeId', null)"></i>

                    <div
                        class="w-full flex flex-wrap lg:flex-nowrap justify-around items-center gap-3 p-3 bg-gray-900 rounded-lg">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-xs lg:text-sm text-white/70">Faculty</span>
                            <span class="text-sm lg:text-md font-bold text-white">
                                {{ $this->selectedProgramme->faculty->faculty_name }}
                            </span>
                        </div>

                        <div class="flex flex-col items-center justify-center">
                            <span class="text-xs lg:text-sm text-white/70">Programme Type</span>
                            <span class="text-sm lg:text-md font-bold text-white">
                                {{ $this->selectedProgramme->programmeType->type }}
                            </span>
                        </div>

                        <div class="flex flex-col items-center justify-center">
                            <span class="text-xs lg:text-sm text-white/70">Added on</span>
                            <span class="text-sm lg:text-md font-bold text-white">
                                {{ $this->selectedProgramme->created_at->toFormattedDateString() }}
                            </span>
                        </div>

                        <div class="flex flex-col items-center justify-center">
                            <span class="text-xs lg:text-sm text-white/70">Last Updated</span>
                            <span class="text-sm lg:text-md font-bold text-white">
                                {{ $this->selectedProgramme->updated_at->toFormattedDateString() }}
                            </span>
                        </div>
                    </div>


                    <div class="flex lg:flex-row flex-col justify-start items-center w-full gap-7">

                        @if ($this->selectedProgramme->coreSubject)
                            <div
                                class="flex flex-col items-start justify-start p-2 lg:p-5 gap-2 w-full lg:w-[50%] border border-white/20 bg-gray-700 rounded-lg h-full">
                                <h2 class="text-xs lg:text-sm font-semibold">Core subjects required for
                                    programme</h2>
                                <div
                                    class="flex justify-start items-center overflow-x-auto bg-neutral-9500 p-2 rounded-lg flex-nowrap gap-2 w-full">

                                    @foreach ($this->selectedProgramme->coreSubject->coreSubjects($this->selectedProgramme->programme_name) as $core)
                                        <span
                                            class="bg-slate-800 text-white px-3 py-2 rounded-full mr-2 whitespace-nowrap text-xs lg:text-smcursor-default transition-colors hover:bg-slate-900">{{ $core }}</span>
                                    @endforeach
                                </div>
                                <hr class="w-full text-white/30">
                                <div
                                    class="flex justify-start items-start gap-2 p-2 border-2 border-blue-900 rounded-lg bg-black w-full">
                                    <div class="flex gap-2 items-center justify-center text-xs lg:text-sm uppercase">
                                        <i class="bi bi-check-circle-fill text-blue-500"></i>
                                        <span class="text-white uppercase">Pass grade for cores:</span>
                                    </div>
                                    <span class="text-sm font-bold">
                                        {{ $this->selectedProgramme->passGradeForCores->grade }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        @if ($this->selectedProgramme->electiveSubject)
                            <div
                                class="flex flex-col items-start justify-start p-2 lg:p-5 gap-2 w-full lg:w-[50%] border border-white/20 bg-gray-700 rounded-lg h-full">
                                <h2 class="text-xs lg:text-sm font-semibold">Elective subjects required for
                                    programme</h2>
                                <div
                                    class="flex justify-start items-center overflow-x-auto bg-neutral-9500 p-2 rounded-lg flex-nowrap gap-2 w-full">

                                    @foreach ($this->selectedProgramme->electiveSubject->getElectives() as $elective)
                                        <span
                                            class="bg-slate-800 text-white px-3 py-2 rounded-full mr-2 whitespace-nowrap text-xs lg:text-smcursor-default transition-colors hover:bg-slate-900">{{ $elective }}</span>
                                    @endforeach
                                </div>
                                <hr class="w-full text-white/30">
                                <div
                                    class="flex justify-start items-start gap-2 p-2 border-2 border-blue-900 rounded-lg bg-black w-full">
                                    <div class="flex gap-2 items-center justify-center text-xs lg:text-sm uppercase">
                                        <i class="bi bi-check-circle-fill text-blue-500"></i>
                                        <span class="text-white uppercase">Pass grade for electives:</span>
                                    </div>
                                    <span class="text-xs lg:text-sm font-bold">
                                        {{ $this->selectedProgramme->passGradeForElectives->grade }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div
                        class="w-full flex flex-col lg:flex-row justify-between items-center bg-gray-700/30 rounded-lg gap-2">
                        <button x-data x-on:click="$dispatch('delete-programme')"
                            class="w-full lg:w-auto bg-red-500 text-white flex justify-center items-center gap-2 px-3 py-1 rounded-lg hover:bg-red-700 transition-colors duration-500">
                            <i class="bi bi-trash"></i>
                            <span class="text-sm">Delete Programme</span>
                        </button>
                        <button x-data x-on:click="$dispatch('edit-programme'); $wire.setFields()"
                            class="w-full lg:w-auto bg-blue-700 text-white flex justify-center items-center gap-2 px-3 py-1 rounded-lg hover:bg-blue-800 transition-colors duration-500">
                            <i class="bi bi-pencil-square"></i>
                            <span class="text-sm">Edit Programme</span>
                        </button>
                    </div>

                    <div class="flex flex-col justify-center items-center gap-2 bg-black/80 absolute top-0 h-full left-0 right-0 w-full"
                        x-data={showing:false} x-on:delete-programme.window = "showing = true" x-show = "showing"
                        x-on:programme-deleted.window="showing = false">
                        <div class="flex justify-center items-center p-5 flex-col gap-2 rounded-lg bg-neutral-800"
                            x-on:click.outside="showing = false">
                            <span class="text-white text-sm font-bold">This action is irreversible. </span>
                            <span class="text-gray-300 text-sm font-bold">Are you sure you want to continue with it?
                            </span>
                            <div class="w-full flex justify-between items-center gap-2 text-white ">
                                <button
                                    class="bg-blue-500 rounded-lg px-2 py-1 flex items-center justify-center text-xs transition-colors duration-500 hover:bg-blue-700"
                                    x-on:click="$wire.delete({{ $this->selectedProgramme->id }})">Delete</button>
                                <button
                                    class="bg-red-500 rounded-lg px-2 py-1 flex items-center justify-center text-xs transition-colors duration-500 hover:bg-red-800"
                                    x-on:click="showing = false">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- editing programme modal-->

        <div x-data = "{showModal: false, isDisabled:true}" x-show = "showModal" x-cloak x-on:click.outside="showModal=false; $dispatch('update-cancelled')"
            x-on:edit-programme.window="showModal = true" x-on:setting-complete.window="isDisabled=false" x-on:update-complete.window="showModal = false"
            :class="isDisabled ? 'pointer-events-none opacity-50' : 'opacity-100 pointer-events-auto'"
            class=" flex transition-all duration-500 flex-col justify-evenly items-start gap-5 w-full h-full border border-blue-900 p-2 2xl:p-5 mt-20 2xl:mt-5 rounded-xl overflow-hidden">
            <div class="w-full flex items-center justify-end gap-5 p-2 2xl:p-0">
                <div class="flex items-center justify-center gap-5 w-full">
                    <h3 class="text-md md:text-lg font-bold text-white">Updating
                        {{ $this->selectedProgramme->programme_name }}</h3>
                </div>
                <button class="px-2 bg-blue-500 rounded-lg transition-colors duration-400 hover:bg-blue-700 text-white"
                    wire:click="updateProgramme"><span class="text-sm">Update</span></button>
            </div>
            <div
                class="flex lg:flex-row flex-col w-full items-center justify-center bg-gray-900 p-2 2xl:p-5 rounded-xl gap-10">
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
                <span class="text-white font-bold text-sm md:text-md 2xl:text-lg rounded-xl translate-y-2">Select
                    required
                    core
                    subjects</span>
                <hr class="w-full text-white/20">
                <form class="flex overflow-x-auto justify-start items-center flex-nowrap w-full gap-7 pb-5 2xl:pb-0">
                    @foreach ($cores as $core)
                        <button
                            class="transition-colors duration-300 hover:bg-blue-500 w-full text-sm 2xl:text-md {{ in_array($core, $selectedCores) ? 'bg-blue-500 border-white/50 border-2' : 'border bg-cyan-900 border-white/30' }} rounded-xl flex p-2 justify-center items-center gap-5 text-white"
                            value="{{ $core }}"
                            wire:click.prevent="toggleCoreSubject('{{ $core }}')">
                            <i class="bi bi-mortarboard"></i>
                            <span class="font-bold">{{ ucwords($core) }}</span>
                        </button>
                    @endforeach
                </form>
            </div>

            <div
                class="flex lg:flex-row flex-col items-start justify-center gap-3 w-full bg-gray-900 p-2 2xl:p-5 rounded-xl">
                <div class="flex flex-col lg:w-[33.3%] w-full items-start justify-start relative">
                    <div
                        class="flex justify-between gap-2 items-start p-3 w-full bg-neutral-950 rounded-xl border border-white/20">
                        <span class="text-white font-bold text-sm md:text-md 2xl:text-lg">Elective One</span>
                        <button
                            wire:click.prevent="$dispatchTo('programme-component','toggleSelectAllElectives', 'e1')"
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
                        <button
                            wire:click.prevent="$dispatchTo('programme-component','toggleSelectAllElectives', 'e2')"
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
                        <button
                            wire:click.prevent="$dispatchTo('programme-component','toggleSelectAllElectives', 'e3')"
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
        </div>
    @endif


</div>
