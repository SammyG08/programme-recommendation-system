@extends('layout.base')
@section('content')
    <div class="w-full max-w-screen h-full relative overflow-x-hidden flex min-h-screen" id="first">
        <div class="bg-black/60 backdrop-blur-md w-full flex flex-col items-center justify-center min-h-screen py-4 2xl:p-0 min-[320px]:p-7 showing gap-20"
            id="callToAction">
            <div
                class="text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-center items-start p-5 min-[320px]:p-10 2xl:text-justify">
                <h1 class="text-xl md:text-5xl mb-7">
                    Your journey begins here!
                </h1>
                <p class="text-normal text-md md:text-xl w-full mb-3">
                    Find out which programme at Ghana Communication Technology University matches your ambitions.
                    Tap the button below and explore your future today.

                </p>
                <p class="text-normal text-md 2xl:text-xl w-full text-white/50">For students who have recently completed
                    the
                    WASSCE examinations</p>
            </div>
            <div
                class="flex flex-col sm:flex-row text-white rounded-3xl lg:max-w-[70%] 2xl:max-w-[50%] w-full p-5 min-[320px]:p-10 justify-between gap-10 bg-black/20 border-2 border-blue-950">
                <button class="w-full bg-blue-500 rounded-3xl px-10 py-2 animate-scale" id="getStartedBtn"><span
                        class="bi bi-mortarboard mr-2"></span>Explore Courses</button>
                <a href="{{ route('admin') }}" class="w-full bg-transparent rounded-3xl px-10 py-2 border-2 border-white flex justify-center items-center transition-colors hover:bg-white/5"><span
                        class="bi bi-person-fill-lock mr-2"></span>Administrator</a>
            </div>
        </div>

        <div class="bg-black/60 backdrop-blur-lg w-full flex flex-col items-center justify-start 2xl:justify-start min-h-screen py-4 2xl:p-0 min-[320px]:p-7 hide overflow-hidden h-full sm:h-auto"
            id="uploadResultsContainer">
            <div class="w-full lg:max-w-2xl mx-auto my-20">
                <div class="w-full flex items-center justify-center mb-5 transition-all duration-1000 zoomIn fixed top-0 inset-x-0" id="logo">
                    <img src="{{ asset('assets/images/gctu4.jpg') }}" alt="" class="w-auto h-20 rounded-2xl">
                </div>
                <div class="relative flex items-center justify-between">
                    <!-- Step 1 -->
                    <div class="relative">
                        <div id="dot-1" class="h-4 w-4 rounded-full bg-blue-600 border-2 border-white"></div>
                    </div>

                    <!-- Line segment -->
                    <div class="flex-1 h-1 bg-gray-300 mx-2">
                        <div id="line-1" class="h-1 bg-blue-600 transition-all duration-1000" style="width: 0%;">
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative">
                        <div id="dot-2" class="h-4 w-4 rounded-full bg-gray-300 border-2 border-white"></div>
                    </div>

                    <!-- Line segment -->
                    <div class="flex-1 h-1 bg-gray-300 mx-2">
                        <div id="line-2" class="h-1 bg-blue-600 transition-all duration-1000" style="width: 0%;">
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative">
                        <div id="dot-3" class="h-4 w-4 rounded-full bg-gray-300 border-2 border-white"></div>
                    </div>
                </div>
            </div>



            <div class="w-full flex justify-center relative">
                <div class="flex flex-col w-full items-center justify-start showing" id="coreSubjectsContainer">
                    <div
                        class=" text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-start items-start p-5 md:p-10 2xl:text-justify">
                        <h1 class="text-xl md:text-5xl mb-7">
                            Let's get started together
                        </h1>
                        <p class="text-normal text-md md:text-xl w-full mb-3">
                            Enter your WASSCE core subjects (English, Mathematics, Integrated Science, Social Studies)
                            and
                            their
                            grades. This is the first step toward finding your best‑fit programme
                        </p>
                    </div>
                    <div
                        class="flex flex-col text-white 2xl:rounded-3xl lg:max-w-[70%] 2xl:max-w-[50%] w-full p-5 min-[320px]:p-10 justify-between items-end gap-10 bg-neutral-950/20 border-2 border-blue-950">
                        <form
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 2xl:grid-cols-4 w-full gap-5 2xl:gap-0"
                            id="coreResults" method="POST" data-url="{{ route('validate-core-input') }}">
                            @csrf
                            <div
                                class="flex gap-3 items-center justify-between 2xl:justify-evenly p-2 text-black bg-blue-200 2xl:rounded-s-3xl">
                                <label for="englishGrade" class="font-bold">English:</label>
                                <x-grade-selector-component selectName="englishGrade" :core="true" />
                            </div>
                            <div
                                class="flex gap-3 items-center justify-between 2xl:justify-evenly p-2 text-black bg-white">
                                <label for="cMath" class="font-bold">Mathematics:</label>
                                <x-grade-selector-component selectName="cMathGrade" :core="true" />
                            </div>
                            <div
                                class="flex gap-3 items-center justify-between 2xl:justify-evenly p-2 text-black bg-red-200">
                                <label for="science" class="font-bold">Science:</label>
                                <x-grade-selector-component selectName="scienceGrade" :core="true" />
                            </div>
                            <div
                                class="flex gap-3 items-center justify-between 2xl:justify-evenly p-2 text-black bg-slate-200 2xl:rounded-e-3xl">
                                <label for="social" class="font-bold">Social:</label>
                                <x-grade-selector-component selectName="socialGrade" :core="true" />
                            </div>

                        </form>
                        <div
                            class="flex flex-col lg:flex-row text-white rounded-3xl w-full justify-between gap-10 bg-gray-950/30">
                            <button
                                class=" border border-white transition-all duration-300 bg-transparent hover:scale-101 rounded-3xl px-10 py-2"
                                id="homeBtn">
                                <span class="bi bi-arrow-left mr-2"></span>Go back</button>
                            <button
                                class="transition-all duration-300 bg-blue-500 rounded-3xl px-10 py-2 hover:bg-blue-700"
                                id="firstNextBtn">Next
                                <span class="bi bi-arrow-right ml-2"></span></button>

                        </div>

                    </div>
                </div>

                <div class="flex flex-col w-full items-center justify-start hide" id="electiveSubjectsContainer">
                    <div
                        class=" text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-start items-start p-5 min-[320px]:p-10 2xl:text-justify">
                        <h1 class="text-xl md:text-5xl mb-7">
                            Almost there!
                        </h1>
                        <p class="text-normal text-md md:text-xl w-full mb-3">
                            Enter your elective subjects and their grades. This step completes your profile so we can
                            recommend the best programme for you.
                        </p>
                    </div>
                    <div
                        class="flex flex-col text-white 2xl:rounded-3xl lg:max-w-[70%] 2xl:max-w-[50%] w-full p-5 min-[320px]:p-10 justify-between items-end gap-10">
                        <form id="electiveResults"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 w-full gap-10 bg-black/25 2xl:rounded-xl p-2"
                            method="post" data-url="{{ route('validate-electives-input') }}">
                            @csrf
                            <x-elective-details-component header="Elective Details" name="electiveOne"
                                className="electiveSelect" selectName="electiveOneGrade" :electives="$electives" />
                            <x-elective-details-component header="Elective Details" name="electiveTwo"
                                className="electiveSelect" selectName="electiveTwoGrade" :electives="$electives" />
                            <x-elective-details-component header="Elective Details" name="electiveThree"
                                className="electiveSelect" selectName="electiveThreeGrade" :electives="$electives" />
                            <x-elective-details-component header="Elective Details" name="electiveFour"
                                className="electiveSelect" selectName="electiveFourGrade" :electives="$electives" />
                        </form>
                        <div
                            class="flex flex-col lg:flex-row text-white rounded-3xl w-full justify-end gap-10 bg-black/30 border-2 border-blue-950 p-1">
                            {{-- <button
                                class=" border border-white transition-all duration-300 bg-transparent hover:scale-102 rounded-3xl px-10 py-2"
                                id="backBtn">
                                <span class="bi bi-arrow-left mr-2"></span>Go back</button> --}}
                            <button
                                class="transition-all duration-300 bg-blue-500 rounded-3xl px-10 py-2 hover:bg-blue-700"
                                id="secondNextBtn">Next
                                <span class="bi bi-arrow-right ml-2"></span></button>

                        </div>

                    </div>
                </div>

                <div class="flex flex-col w-full items-center justify-start hide" id="curatingProgrammesContainer">
                    <div
                        class=" text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-between items-center p-5 lg:p-10 2xl:text-justify gap-5">
                        <div class="flex flex-col items-start gap-4 w-full">
                            <div
                                class="flex w-full justify-center bg-neutral-900/30 mb-7 p-5 gap-2 text-md md:text-2xl items-center rounded-xl relative">
                                <i class="bi bi-cpu-fill"></i>
                                <h1 class="">Curating Eligible Programmes</h1>
                            </div>
                            
                            <p class="text-normal text-sm md:text-md w-full text-red-500"><i
                                    class="bi bi-exclamation-triangle-fill mr-2"></i>Please do not
                                leave this page
                                during the process</p>
                        </div>
                        <div
                            class="flex flex-col items-start gap-5 w-full bg-black/25 rounded-xl shadow-md shadow-blue-950">
                            <ul class="list-disc list-inside p-2 w-full flex flex-col gap-3">
                                <li class="flex flex-col gap-2 w-full bg-black/30 p-5 rounded-xl transition-colors duration-100 border border-blue-950">
                                    <div class="flex gap-2 w-full rounded-xl">
                                        <span class="step1 bi bi-check-circle-fill"></span><span class="">Analyzing your results</span>
                                    </div>
                                </li>
                                <li class="flex flex-col gap-2 w-full bg-black/30 p-5 rounded-xl transition-colors duration-100 border border-blue-950">
                                    <div class="flex gap-2 w-full rounded-xl">
                                          <span class="step2 bi bi-check-circle-fill"></span><span class="">Calculating your aggregate score</span>
                                    </div>
                                    <span class="uppercase font-bold hidden" id="aggregate"></span>
                                </li>
                                <li class="flex gap-2 w-full bg-black/30 p-5 rounded-xl transition-colors duration-100 border border-blue-950">
                                    <span class="step3 bi bi-check-circle-fill"></span><span class="">Matching with university requirements</span>
                                </li>
                                <li class="flex gap-2 w-full bg-black/30 p-5 rounded-xl transition-colors duration-100 border border-blue-950">
                                    <span class="step4 bi bi-check-circle-fill"></span><span class="">Finalizing your recommendations</span>
                                </li>

                            </ul>
                        </div>
                        <div class="flex justify-center items-start flex-col w-full rounded-xl bg-black/10 backdrop-blur-2xl mt-5 relative overflow-hidden transition-all duration-300 scale-0 opacity-0 border border-blue-950"
                            id="viewProgrammesContainer">
                            <button
                                class="transition-all duration-1000 bg-blue-950 text-white rounded-xl px-10 py-2 hover:bg-blue-900 hidden"
                                id="viewProgrammesBtn">Programmes
                                <span class="bi bi-caret-right-fill ml-2"></span></button>
                                <span class="text-white text-md p-2" id="noProgrammeFound">We’ve analyzed your results, and while you don't meet the current requirements for GCTU, this is simply a detour, not a dead end.</span>

                        </div>
                    </div>

                </div>

            </div>


        </div>

        <div class="class bg-black/60 backdrop-blur-md w-full h-full flex flex-col items-center justify-center min-h-screen py-20 min-[320px]:px-7 zoomOut"
            id="resultsContainer">
            <div
                class="text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-center items-start p-5 min-[320px]:p-10 2xl:text-justify border border-white/50 bg-white/5 rounded-xl">
                <div
                    class="flex gap-2 justify-center items-center w-full p-5 text-sm md:text-xl bg-blue-950 text-white rounded-xl -translate-y-20 border animate-border" id="aggContainer">
                    <i class="hidden sm:inline-block bi bi-mortarboard"></i>
                    <p class="uppercase text-center">Ghana Communication Technology University</p>
                </div>
                <h1 class="text-xl md:text-3xl mb-7 -mt-10">
                    Welcome to your academic journey!
                </h1>
                <p class="text-normal text-md md:text-xl w-full mb-7">
                    Take a deep breath and explore the opportunities ahead.
                    Each faculty opens doors to exciting courses designed to help you grow,
                    discover your strengths, and shape your future with confidence.
                </p>
            </div>
            <div class ="-translate-y-10 lg:max-w-[70%] 2xl:max-w-[50%] flex flex-col text-white rounded-xl w-full p-5 justify-between gap-2 border border-white/50"
                id="programmesAccordionContainer">
            </div>
        </div>

        <div class="hidden bg-black/70 w-full h-screen bottom-0 absolute z-2 top-0 items-center justify-center"
            id="statusContainer">
            <div class="flex flex-col items-center justify-center rounded-xl  px-10 py-5 bg-neutral-950">
                <div class="spinner"></div>
                <p class="text-white font-medium mt-2">Processing results...</p>
            </div>
        </div>

        <div class="hidden bg-black/70 w-full h-screen bottom-0 absolute z-2 top-0 items-end py-5 justify-center overflow-hidden"
            id="errContainer">
            <div
                class="flex flex-col items-center justify-center rounded-xl px-10 py-5 bg-neutral-900 transition-transform translate-y-20 duration-100">
                <p class="text-white font-medium " id="errMsg"></p>
            </div>
        </div>


    </div>
@endsection
