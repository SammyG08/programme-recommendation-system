<div class="flex flex-col lg:flex-row justify-evenly items-start lg:gap-5 gap-20 2xl:w-[80%] h-full lg:w-full"
    x-data = "{showCards: true}" x-show = "showCards" x-cloak x-on:modal-open.window="showCards= false"
    x-on:programme-added.window = "showCards = true; $wire.set('updatedFacultyId', $event.detail.id)" x-on:modal-closed.window="showCards = true" x-on:programme-deleted.window="$wire.$refresh()" x-on:edit-programme.window="showCards = false" x-on:update-complete.window="showCards=true; $wire.$refresh()" x-on:update-cancelled.window="showCards =true">
    @if ($faculties)
        @foreach ($faculties as $faculty)
            <div
                class="flex transition-all duration-500 bg-gray-900 border-2 border-blue-900 rounded-xl h-full p-2 sm:p-5 flex-col items-center gap-0 justify-start w-full">
                <div
                    class="-translate-y-15 p-5 bg-blue-500 flex items-center justify-center border-7 border-black rounded-full h-22 w-22 xl:h-25 2xl:w-25">
                    <span class="text-xl 2xl:text-2xl font-bold">{{ $faculty->programmes_count }}</span>
                </div>
                <div class="flex w-full justify-end items-center -translate-y-13 rounded-lg h-1 bg-blue-500/30 animate-pulse">
                    @if ($updatedFacultyId === $faculty->id)
                        <button
                            class="px-3 bg-black text-white rounded-lg flex items-center justify-center py-1 border-2 border-blue-500">
                            <span class="text-xs font-bold">NEW PROGRAMME ADDED</span>
                        </button>
                    @endif
                </div>
                <div
                    class="h-30 flex flex-col items-start justify-center border border-white/20 gap-0 rounded-xl p-5 bg-black/20 -translate-y-8 w-full ">

                    <p class="text-sm 2xl:text-lg font-light text-white"><span
                            class="bi bi-buildings mr-2"></span>{{ $faculty->faculty_name }}
                        {{ Str::plural('Programme', $faculty->programmes_count) }}</p>
                    @if ($updatedFacultyId === $faculty->id)
                        <p class="text-xs text-green-400">
                            <span class="bi bi-mortarboard mr-1">{{ strtoupper('  recently added programme') }}:</span>
                            <span
                                class="">{{ strtoupper($this->getUpdatedFaculty->latestProgramme->programme_name) }}</span>
                        </p>
                    @endif

                </div>
                <hr class="w-full text-white/30">
                <div
                    class="mt-2 w-full p-2 bg-blue-500/20 rounded-xl flex flex-col 2xl:flex-row justify-between items-center text-sm gap-3 2xl:gap-0">
                    <button x-on:click="$dispatch('modal-open', {fid: {{ $faculty->id }}})"
                        class="w-full 2xl:w-auto transition-colors duration-500 hover:bg-blue-700 h-full rounded-xl p-2 flex justify-center items-center gap-2 bg-blue-500 text-black"><span>Add
                            Programme</span><i class="bi bi-plus-circle"></i></button>
                    <button
                        class="w-full 2xl:w-auto transition-transform duration-500 hover:scale-105 h-full rounded-xl p-2 flex justify-center items-center gap-2 bg-black text-white"><span>Bulk
                            Upload</span><i class="bi bi-cloud-plus"></i></button>

                </div>
            </div>
        @endforeach
    @endif
</div>
