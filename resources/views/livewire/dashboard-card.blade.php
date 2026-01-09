<div x-data
    class="{{ $showDashboardCards ? 'flex' : 'hidden' }} transition-all duration-500 bg-gray-900 border-2 border-blue-900 rounded-xl lg:h-90 xl:h-80 2xl:h-75 p-2 sm:p-5 flex-col items-center gap-0 justify-start w-full">
    <div
        class="-translate-y-15 p-5 bg-blue-500 flex items-center justify-center border-7 border-black rounded-full h-22 w-22 xl:h-25 2xl:w-25">
        @if ($updatedFaculty?->id === $faculty->id)
            <span class="text-xl 2xl:text-2xl font-bold">{{ $updatedfaculty->programmes_count }}</span>
        @else
            <span class="text-xl 2xl:text-2xl font-bold">{{ $faculty->programmes_count }}</span>
        @endif
    </div>
    <div
        class="h-full flex flex-col items-start justify-center border border-white/20 gap-0 rounded-xl p-5 bg-black/20 -translate-y-8 w-full ">
        @if ($updatedFaculty?->id === $faculty->id)
            <p class="text-sm 2xl:text-lg font-light text-white"><span
                    class="bi bi-buildings mr-2"></span>{{ $updatedFaculty->faculty_name }}
                {{ Str::plural('Programme', $updatedFaculty->programmes_count) }}</p>
        @else
            <p class="text-sm 2xl:text-lg font-light text-white"><span
                    class="bi bi-buildings mr-2"></span>{{ $faculty->faculty_name }}
                {{ Str::plural('Programme', $faculty->programmes_count) }}</p>
        @endif
    </div>
    <hr class="w-full text-white/20">
    <div
        class="mt-2 w-full p-2 bg-white/20 rounded-xl flex flex-col 2xl:flex-row justify-between items-center text-sm gap-3 2xl:gap-0">
        <button wire:click.prevent="openAddProgrammeModal('{{ $faculty }}')"
            class="w-full 2xl:w-auto transition-colors duration-500 hover:bg-blue-700 h-full rounded-xl p-2 flex justify-center items-center gap-2 bg-blue-500 text-black"><span>Add
                Programme</span><i class="bi bi-plus-circle"></i></button>
        <button
            class="w-full 2xl:w-auto transition-transform duration-500 hover:scale-105 h-full rounded-xl p-2 flex justify-center items-center gap-2 bg-black text-white"><span>Bulk
                Upload</span><i class="bi bi-cloud-plus"></i></button>

    </div>
</div>
