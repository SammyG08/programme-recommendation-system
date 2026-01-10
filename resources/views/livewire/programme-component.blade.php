<div class="flex flex-col gap-4 items-start justify-start md:w-[70%] lg:w-full 2xl:w-[80%] w-full mt-20 2xl:mt-5">
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
        <div class="transition-all duration-500 flex-col justify-evenly items-center gap-5 w-full h-full p-2 2xl:p-5 rounded-xl"
            wire:key="selected-programme-{{ $selectedProgrammeId }}">

            <div class="w-full flex items-center justify-center rounded-lg gap-2 bg-slate-950 p-1 lg:p-5">
                <div
                    class="flex flex-col justify-start items-center gap-2 p-3 text-white w-full rounded-lg bg-black border border-blue-950">

                    <div
                        class="font-semibold text-white bg-gray-700 w-auto flex items-center justify-center p-3 rounded-lg -translate-y-7 border-2 border-white">
                        <i class="bi bi-mortarboard-fill mr-2 text-xs lg:text-sm"></i>
                        <span class="uppercase text-xs lg:text-sm">{{ $this->selectedProgramme->programme_name }}
                            Details</span>
                    </div>

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
                        <button
                            class="w-full lg:w-auto bg-red-500 text-white flex justify-center items-center gap-2 px-3 py-1 rounded-lg hover:bg-red-700 transition-colors duration-500">
                            <i class="bi bi-trash"></i>
                            <span class="text-sm">Delete Programme</span>
                        </button>
                        <button
                            class="w-full lg:w-auto bg-blue-700 text-white flex justify-center items-center gap-2 px-3 py-1 rounded-lg hover:bg-blue-800 transition-colors duration-500">
                            <i class="bi bi-pencil-square"></i>
                            <span class="text-sm">Edit Programme</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
