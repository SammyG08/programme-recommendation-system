<div class="flex flex-col lg:flex-row justify-evenly items-start lg:gap-5 gap-20 w-full h-full rounded-xl p-5"
    x-data = "{showCards: true}" x-show = "showCards" x-cloak x-on:modal-open.window="showCards= false"
    x-on:programme-added.window = "showCards = true; $wire.set('updatedFacultyId', $event.detail.id)"
    x-on:modal-closed.window="showCards = true" x-on:programme-deleted.window="$wire.$refresh()"
    x-on:edit-programme.window="showCards = false" x-on:update-complete.window="showCards=true; $wire.$refresh()"
    x-on:update-cancelled.window="showCards =true" x-on:update-programme-failed.window="showCards=true;">

    @if ($faculties)
        @foreach ($faculties as $faculty)
            <div wire:key="faculty-{{ $faculty->id }}"
                class="flex transition-all duration-500 bg-gray-900/40 rounded-xl h-full p-2 sm:p-5 flex-col items-center gap-0 justify-start w-full relative">
                <div
                    class="-translate-y-15 p-5 bg-black text-white flex items-center justify-center border-7 border-blue-950 rounded-full h-22 w-22 xl:h-25 2xl:w-25">
                    <span
                        class="text-xl 2xl:text-2xl font-bold">{{ $faculty->programmes->unique('programme_name')->count() }}</span>
                </div>
                
                <div
                    class="h-30 flex flex-col items-start justify-center border border-white/20 gap-0 rounded-xl p-5 bg-black/20 -translate-y-8 w-full ">

                    <p class="text-sm 2xl:text-lg font-light text-white"><span
                            class="bi bi-buildings mr-2"></span>{{ $faculty->faculty_name }}
                        {{ Str::plural('Programme', $faculty->programmes->unique('programme_name')->count()) }}</p>
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
                    class="mt-2 w-full p-2 bg-blue-500/20 rounded-xl flex justify-between items-center text-sm gap-3 2xl:gap-0">
                    <button x-on:click="$dispatch('modal-open', {fid: {{ $faculty->id }}})"
                        class="w-full transition-colors duration-500 hover:bg-blue-700 h-full rounded-xl p-2 flex justify-center items-center gap-2 bg-black/20 text-white font-bold uppercase"><span>Add
                            Programme</span><i class="bi bi-plus-circle-fill"></i></button>

                </div>
                <form class="flex gap-2 items-center justify-between mt-4 w-full">
                    <input type="file" wire:model="file.{{ $faculty['id'] }}" accept=".xlsx"
                        class="w-full 2xl:w-auto h-full rounded-xl py-1 px-2 flex justify-center items-center gap-2 bg-blue-950 text-white border-2 border-white/30">
                    {{-- <button type="button" wire:click.prevent="uploadFile({{ $faculty }})" class="w-full 2xl:w-auto transition-transform duration-500 hover:scale-101 h-full rounded-xl p-1 px-3 flex justify-between items-center gap-2 bg-black text-white border-2 border-white/50 hover:border-white/70">
                        Upload <i class="bi bi-cloud-arrow-up"></i>
                    </button> --}}
                    <button x-on:click="$dispatch('bulk')" type="button" wire:click.prevent="uploadFile({{ $faculty['id'] }})"
                        wire:loading.attr="disabled" wire:target="file.{{ $faculty['id']}}"
                        class="disabled:opacity-50 disabled:cursor-not-allowed bg-black text-white border-2 border-white/50 hover:border-white/70 flex justify-between items-center gap-2 px-3 py-1 rounded-xl w-full 2xl:w-auto transition-transform duration-500 hover:scale-101 h-full">
                        <span wire:loading.remove wire:target="file.{{ $faculty['id'] }}">Upload <i class="bi bi-cloud-arrow-up"></i></span>
                        <span wire:loading wire:target="file.{{ $faculty['id'] }}" class="text-xs text-white animate-pulse"> Syncing file with server...</span>
                    </button>
                    

                </form>
            </div>
        @endforeach
    @endif
</div>
