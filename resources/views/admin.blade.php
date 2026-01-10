@extends('layout.base')
@section('content')
    <div
        class="w-full min-h-screen h-full bg-black flex flex-col justify-center items-center max-[350px]:px-5 px-10 py-20 relative">
        @if ($faculties->count())
            <div class="flex flex-col lg:flex-row justify-evenly items-start lg:gap-5 gap-20 2xl:w-[80%] h-full lg:w-full">
                @foreach ($faculties as $faculty)
                    @livewire('dashboard-card', ['faculty' => $faculty], key($faculty->id))
                @endforeach
            </div>
        @endif

        @livewire('programme-add', ['electives' => $electives])

        @livewire('programme-component')


    </div>
@endsection
