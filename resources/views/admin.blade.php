@extends('layout.base')
@section('content')
    <div class="w-full min-h-screen h-full bg-black flex flex-col justify-center items-center max-[350px]:px-5 px-10 py-20 relative">

        <div class="flex w-full sm:w-[70%] lg:w-[50%] bg-gray-800 rounded-lg mb-5">
            <form action="{{ route('logout') }}" class="" method="post">
                @csrf
                <button class="rounded-lg bg-blue-900 px-6 py-2 flex items-center text-white text-sm font-bold transition-colors duration-500 hover:bg-gray-950 uppercase">Logout <span class="ml-1 bi bi-door-closed"></span></button>
            </form>
        </div>
        @livewire('dashboard-card')

        @livewire('programme-add', ['electives' => $electives])

        @livewire('programme-component')



    </div>
@endsection
