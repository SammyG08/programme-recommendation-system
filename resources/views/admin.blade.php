@extends('layout.base')
@section('content')
    <div class="w-full min-h-screen h-full bg-black flex flex-col justify-center items-center max-[350px]:px-5 px-10 py-20 relative">

        @livewire('dashboard-card')

        @livewire('programme-add', ['electives' => $electives])

        @livewire('programme-component')


    </div>
@endsection
