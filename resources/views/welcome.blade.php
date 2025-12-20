<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/main.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body>
    <div class="w-full max-w-screen h-full relative overflow-x-hidden flex min-h-screen bg-black">
        <div class="bg-black w-full flex flex-col items-center justify-center  min-h-screen py-4 2xl:p-0 min-[320px]:p-7 showing gap-20"
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
                class="flex flex-col sm:flex-row text-white rounded-3xl lg:max-w-[70%] 2xl:max-w-[50%] w-full p-5 min-[320px]:p-10 justify-between gap-10 bg-neutral-950">
                <button class="w-full bg-blue-500 rounded-3xl px-10 py-2 animate-scale" id="getStartedBtn"><span
                        class="bi bi-mortarboard mr-2"></span>Get
                    Started</button>
                <button class="w-full bg-transparent rounded-3xl px-10 py-2 border-2 border-white "><span
                        class="bi bi-gear mr-2"></span>Administrator</button>
            </div>
        </div>

        <div class="bg-black w-full h-full flex flex-col items-center justify-start 2xl:justify-start min-h-screen py-4 2xl:p-0 min-[320px]:p-7 hide"
            id="uploadResultsContainer">
            <div class="w-full lg:max-w-2xl mx-auto my-20">
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



            <div class="w-full flex justify-center overflow-hidden relative">
                <div class="flex flex-col w-full items-center justify-evenly showing" id="coreSubjectsContainer">
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
                        class="flex flex-col text-white 2xl:rounded-3xl lg:max-w-[70%] 2xl:max-w-[50%] w-full p-5 min-[320px]:p-10 justify-between items-end gap-10 bg-neutral-950">
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
                            class="flex flex-col lg:flex-row text-white rounded-3xl w-full justify-between gap-10 bg-neutral-950">
                            <button
                                class=" border border-white transition-all duration-300 bg-transparent hover:scale-102 rounded-3xl px-10 py-2"
                                id="homeBtn">
                                <span class="bi bi-arrow-left mr-2"></span>Go back</button>
                            <button
                                class="transition-all duration-300 bg-blue-500 rounded-3xl px-10 py-2 hover:bg-blue-700"
                                id="firstNextBtn">Next
                                <span class="bi bi-arrow-right ml-2"></span></button>

                        </div>

                    </div>
                </div>

                <div class="flex flex-col w-full items-center justify-evenly hide" id="electiveSubjectsContainer">
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
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 w-full gap-10"
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
                            class="flex flex-col lg:flex-row text-white rounded-3xl w-full justify-between gap-10 bg-neutral-950 p-1">
                            <button
                                class=" border border-white transition-all duration-300 bg-transparent hover:scale-102 rounded-3xl px-10 py-2"
                                id="backBtn">
                                <span class="bi bi-arrow-left mr-2"></span>Go back</button>
                            <button
                                class="transition-all duration-300 bg-blue-500 rounded-3xl px-10 py-2 hover:bg-blue-700"
                                id="secondNextBtn">Next
                                <span class="bi bi-arrow-right ml-2"></span></button>

                        </div>

                    </div>
                </div>

                <div class="flex flex-col w-full items-center justify-evenly hide" id="curatingProgrammesContainer">
                    <div
                        class=" text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-between items-center p-5 lg:p-10 2xl:text-justify gap-5">
                        <div class="flex flex-col items-start gap-4 w-full">
                            <div
                                class="flex w-full justify-center bg-neutral-900/25 mb-7 p-5 gap-2 text-md md:text-2xl items-center rounded-xl relative">
                                <i class="bi bi-cpu-fill"></i>
                                <h1 class="">Curating Eligible Programmes</h1>
                                {{-- <div class="rounded-xl absolute -top-3 right-0 p-2 bg-blue-500/50 text-xs">processing</div> --}}
                            </div>
                            {{-- <p class="text-normal text-md md:text-xl w-full mb-3">
                                We are curating a list of programmes you are eligible to study at
                                Ghana Communication Technology University.
                            </p> --}}
                            <p class="text-normal text-sm md:text-md w-full text-red-500"><i
                                    class="bi bi-exclamation-triangle-fill mr-2"></i>Please do not
                                leave this page
                                during the process</p>
                        </div>
                        <div
                            class="flex flex-col items-start gap-5 w-full bg-neutral-900 rounded-xl shadow shadow-white">
                            <ul class="list-disc list-inside p-2 w-full flex flex-col gap-3">
                                <li class="flex gap-2 w-full bg-black p-5 rounded-xl transition-colors duration-100">
                                    <span class="step bi bi-check-circle-fill"></span><span class="">Checking
                                        results</span></li>
                                <li class="flex gap-2 w-full bg-black p-5 rounded-xl transition-colors duration-100">
                                    <span class="step bi bi-check-circle-fill"></span><span class="">Matching
                                        programmes</span></li>
                                <li class="flex gap-2 w-full bg-black p-5 rounded-xl transition-colors duration-100">
                                    <span class="step bi bi-check-circle-fill"></span><span class="">Filtering
                                        programmes </span></li>
                                <li class="flex gap-2 w-full bg-black p-5 rounded-xl transition-colors duration-100">
                                    <span class="step bi bi-check-circle-fill"></span><span class="">Compiling
                                        programmes</span></li>

                            </ul>
                        </div>
                        <div class="flex justify-start items-center w-full rounded-xl bg-neutral-900 mt-5 relative overflow-hidden transition-all duration-300 scale-0 opacity-0 "
                            id="viewProgrammesContainer">
                            <button
                                class="transition-all duration-3000 bg-black text-white rounded-xl px-10 py-2 hover:bg-neutral-950"
                                id="viewProgrammesBtn">Programmes
                                <span class="bi bi-caret-right-fill ml-2"></span></button>

                        </div>
                    </div>

                </div>

            </div>


        </div>

        <div class="class bg-black w-full h-full flex flex-col items-center justify-center min-h-screen py-20 min-[320px]:px-7 zoomOut"
            id="resultsContainer">
            <div
                class="text-white font-bold lg:max-w-[70%] 2xl:max-w-[50%] w-full flex flex-col justify-center items-start p-5 min-[320px]:p-10 2xl:text-justify border border-white/50 bg-white/5 rounded-xl">
                <div class="flex justify-center w-full p-5 text-xl md:text-5xl bg-black text-white rounded-xl -translate-y-20 border animate-border"><i class="bi bi-mortarboard"></i></div>
                <h1 class="text-xl md:text-3xl mb-7 -mt-10">
                    Welcome to your academic journey!
                </h1>
                <p class="text-normal text-md md:text-xl w-full mb-7">
                    Take a deep breath and explore the opportunities ahead.
                    Each faculty opens doors to exciting courses designed to help you grow,
                    discover your strengths, and shape your future with confidence.
                </p>
            </div>
            <div class ="-translate-y-10 lg:max-w-[75%] 2xl:max-w-[55%] flex flex-col text-white rounded-xl w-full p-5 justify-between gap-10 bg-gray-950 border animate-border" id="programmesAccordionContainer">
                {{-- <div class="w-full flex flex-col justify-center items-start p-5 shadow shadow-white/40 text-xs md:text-xl rounded-xl gap-5">
                    <div class="flex justify-between items-center w-full">
                        <p class="font-semibold">Faculty of Computing & Information Systems</p>
                        <i class="bi bi-caret-right-fill"></i>
                    </div>
                    <div class="text-xs md:text-lg flex flex-col items-start justify-start gap-5 p-5 bg-white/5 w-full rounded-xl">
                        <p class=""><i class="bi bi-mortarboard mr-2 "></i>BSc Computer Science</p>
                        <p class=""><i class="bi bi-mortarboard mr-2 "></i>BSc Computer Science</p>
                        <p class=""><i class="bi bi-mortarboard mr-2 "></i>BSc Computer Science</p>
                    </div>
                </div> --}}


            </div>
        </div>
    </div>


    <script src="{{ asset('assets/main.js') }}"></script>
</body>

</html>
