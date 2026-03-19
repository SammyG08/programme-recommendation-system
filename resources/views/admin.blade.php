@extends('layout.base')
@section('content')
    <div class="w-full min-h-screen h-full flex flex-col justify-center items-center bg-no-repeat bg-cover bg-center relative"
        style="background-image: url({{ asset('assets/images/gctu3.jpg') }})">
        <div x-data x-cloak class="flex w-full justify-end items-start fixed top-5 right-5 rounded-lg mb-5 z-20">
            <form action="{{ route('logout') }}" class="" method="post">
                @csrf
                <button
                    class="rounded-lg bg-blue-900 px-6 py-2 flex items-center text-white text-sm font-bold transition-colors duration-500 hover:bg-gray-950 uppercase">Logout
                    <span class="ml-1 bi bi-door-closed"></span></button>
            </form>
        </div>

        <div
            class="flex flex-col min-h-screen h-full justify-center items-center bg-black/30 backdrop-blur-md w-full max-[350px]:px-5 px-5 py-20 relative ">
            @livewire('dashboard-card')

            @livewire('programme-add', ['electives' => $electives])

            @livewire('programme-component')


        </div>

        <div class="flex w-full fixed top-0 bottom-0 items-center justify-center bg-black/80" x-data="{ show: false }" x-cloak
            x-show="show" x-on:bulk.window="show = true" x-on:upload-done.window="show=false" x-on:upload-failed.window="show=false">

            <div class="flex flex-col items-center justify-center rounded-xl p-5 bg-neutral-900">
                <div class="spinner"></div>
                <p class="text-white font-medium mt-2">Processing upload...</p>
            </div>
        </div>

        <div x-data="{ showError: false }" x-show="showError" x-cloak
            x-on:upload-failed.window="showError=true; setTimeout(() => showError = false, 3000)"
            x-transition.opacity.duration.500ms
            class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-black/80 h-full w-full">
            <div class=" bg-neutral-900 p-5 rounded-xl">
                <span class="text-sm text-white">Upload failed please try again.</span>
            </div>
        </div>



    </div>
@endsection
