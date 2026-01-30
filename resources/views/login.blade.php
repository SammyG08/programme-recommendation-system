@extends('layout.base')
@section('content')
    <div class="w-full max-w-screen h-full relative overflow-x-hidden flex items-center min-h-screen" id="login">
        <div class="flex w-full flex-col justify-center items-center gap-5 bg-black/50 backdrop-blur-md min-h-screen h-full">
            <div
                class="bg-black/20 w-full lg:w-[80%] 2xl:w-[50%] min-[320px]:p-5 flex flex-col items-center justify-center gap-5 rounded-xl lg:border-2 border-blue-500 min-h-screen lg:min-h-auto">
                <div
                    class="hidden min-[320px]:flex justify-between items-center gap-5 bg-blue-500 rounded-b-[50%] p-2 w-full h-20 -translate-y-8">
                    <div class="flex items-center justify-center bg-white rounded-b-[50%] h-full w-full">
                        <img src="{{ asset('assets/images/gctu4.jpg') }}" alt="" class=" h-10">
                    </div>
                </div>
                <div class="flex flex-col w-full min-[320px]:w-[90%] gap-5 items-start justify-center p-5 bg-blue-950/50 rounded-lg relative">
                    <div class="h-1 bg-white/20 absolute top-0 right-10 left-10 overflow-hidden hidden"
                        id="loadingBarWrapper">
                        <div class="h-1 bg-blue-500/50 w-50" id="loadingBar"></div>
                    </div>
                    <h2 class="text-white font-bold text-xl uppercase"><span class="bi bi-person"></span> Administrator
                        Login
                    </h2>

                    <form data-url="{{ route('process-login') }}" method="post" id="loginForm"
                        class="w-full flex flex-col items-center justify-start gap-5 bg-black/30 rounded-lg p-2">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3 items-start justify-start w-full rounded-lg p-3 border border-gray-700">
                            <div class="flex items-center justify-between p-2 text-white rounded-lg w-full">
                                <p class="text-lg font-light text-gray-300 whitespace-nowrap">Identification Number: </p>
                            </div>
                            <div class="flex items-start gap-3 justify-start w-full">
                                <input type="text" name="aid"
                                    class="bg-white/20 text-md p-2 w-full rounded-lg border border-gray-500 text-gray-300 flex items-center"
                                    placeholder="eg. A1001">
                            </div>
                        </div>
                        <hr class="w-full bg-white">
                        <div class="flex flex-col sm:flex-row gap-3 items-start justify-start w-full rounded-lg p-3 border border-gray-700">
                            <div class="flex items-center justify-between p-2 text-white rounded-lg w-full">
                                <p class="text-lg font-light text-gray-300 whitespace-nowrap">Admin Password: </p>
                            </div>
                            <div class="flex items-start gap-3 justify-start w-full">
                                <input type="password" name="pwd"
                                    class="bg-white/20 text-md p-2 w-full rounded-lg border border-gray-500 text-gray-300 flex items-center"
                                    placeholder="**************">
                            </div>
                        </div>
                        <p class="w-full text-red-500 text-sm xl:text-md font-bold flex justify-center" id="errorMsgP">

                        </p>
                        <div class="w-full bg-blue-500/20 flex flex-col sm:flex-row items-center justify-center p-2 rounded-lg mt-5 gap-5">
                            <button type="button" id="submitBtn"
                                class="bg-blue-500 transition-colors duration-300 hover:bg-blue-700 text-white font-bold uppercase text-md flex items-center gap-1 px-3 py-1 rounded-lg w-full justify-center">
                                <span>Login</span>
                                <span class="bi bi-door-open"></span>
                            </button>
                            <button
                                class="bg-transparent border transition-colors duration-300 border-gray-600 hover:border-blue-500 text-white font-bold uppercase text-md flex items-center gap-1 px-3 py-1 rounded-lg w-full justify-center">
                                <span>Forgot Password</span>
                                <span class="bi bi-door-open"></span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
