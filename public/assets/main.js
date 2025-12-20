document.addEventListener("DOMContentLoaded", function () {
    const getStartedBtn = document.getElementById("getStartedBtn");
    if (getStartedBtn) {
        getStartedBtn.addEventListener("click", function () {
            const currentlyShowing = document.querySelector("#callToAction");
            const uploadResultsContainer = document.getElementById(
                "uploadResultsContainer"
            );
            slideRight(currentlyShowing, uploadResultsContainer);
        });
    }

    const firstNextBtn = document.getElementById("firstNextBtn");
    if (firstNextBtn) {
        firstNextBtn.addEventListener("click", async function () {
            let currentStep = 1;
            const currentlyShowing = document.querySelector(
                "#coreSubjectsContainer"
            );
            const electiveSubjectsContainer = document.getElementById(
                "electiveSubjectsContainer"
            );
            const coreResults = document.getElementById("coreResults");
            const formData = new FormData(coreResults);
            try {
                const response = await ajaxRequest(
                    "POST",
                    coreResults.dataset.url,
                    Object.fromEntries(formData)
                );
                if ((response.statusCode = 808)) {
                    slideRight(currentlyShowing, electiveSubjectsContainer);
                    showProgress(currentStep);
                    currentStep++;
                } else console.log(response.msg);
            } catch (err) {
                // console.log("Request failed");
                console.log(err.error);
            }
        });
    }

    const secondNextBtn = document.getElementById("secondNextBtn");
    if (secondNextBtn) {
        secondNextBtn.addEventListener("click", async function () {
            let currentStep = 2;
            const curatingContainer = document.getElementById(
                "curatingProgrammesContainer"
            );
            const currentlyShowing = document.getElementById(
                "electiveSubjectsContainer"
            );
            const coreResultsForm = document.getElementById("coreResults");
            const electiveResultsForm =
                document.getElementById("electiveResults");
            const coreSubjectsInput = Object.fromEntries(
                new FormData(coreResultsForm)
            );
            const electiveSubjectInputs = Object.fromEntries(
                new FormData(electiveResultsForm)
            );

            try {
                const response = await ajaxRequest(
                    "POST",
                    electiveResultsForm.dataset.url,
                    electiveSubjectInputs
                );
                if (response.statusCode === 808) {
                    slideRight(currentlyShowing, curatingContainer);
                    showProgress(currentStep);
                    currentStep++;
                    getProgrammes(response.url, {
                        ...coreSubjectsInput,
                        ...electiveSubjectInputs,
                    });
                } else console.log(response.msg);
            } catch (err) {
                console.log(err.error);
            }
        });
    }

    const homeBtn = document.getElementById("homeBtn");
    if (homeBtn) {
        homeBtn.addEventListener("click", function () {
            const callToAction = document.getElementById("callToAction");
            const uploadResultsContainer = document.getElementById(
                "uploadResultsContainer"
            );
            callToAction.classList.replace("hideCurrent", "showing");
            uploadResultsContainer.classList.replace("showing", "hide");
        });
    }

    const backBtn = document.getElementById("backBtn");
    if (backBtn) {
        backBtn.addEventListener("click", function () {
            const hiddenContainers = document.querySelectorAll(".hideCurrent");
            const recentlyHidden =
                hiddenContainers[hiddenContainers.length - 1];
            if (recentlyHidden) {
                const showing = document.querySelectorAll(".showing");
                const currentlyShowing = showing[showing.length - 1];
                currentlyShowing.classList.replace("showing", "hide");
                recentlyHidden.classList.replace("hideCurrent", "showing");
            }
        });
    }

    const electivesSelect = document.querySelectorAll(".electiveSelect");
    if (electivesSelect) {
        electivesSelect.forEach((select) =>
            select.addEventListener("change", updateOptions)
        );
    }

    const viewProgrammesBtn = document.getElementById("viewProgrammesBtn");
    if (viewProgrammesBtn) {
        const uploadResultsContainer = document.getElementById(
            "uploadResultsContainer"
        );
        const resultsContainer = document.getElementById("resultsContainer");
        viewProgrammesBtn.addEventListener("click", () => {
            zoomIn(uploadResultsContainer, resultsContainer);
            populateProgrammesRecommended();
        });
    }

    function slideRight(currentContainer, nextContainer) {
        if (currentContainer) {
            currentContainer.classList.replace("showing", "hideCurrent");
            if (nextContainer) {
                nextContainer.classList.replace("hide", "showing");
            }
        }
    }

    function zoomIn(current, next) {
        current.classList.replace("showing", "zoomOut");
        next.classList.replace("zoomOut", "zoomIn");
    }

    function showProgress(currentStep) {
        if (currentStep === 1) {
            document.getElementById("line-1").style.width = "100%";
            document
                .getElementById("dot-2")
                .classList.replace("bg-gray-300", "bg-blue-600");
        } else if (currentStep === 2) {
            document.getElementById("line-2").style.width = "100%";
            document
                .getElementById("dot-3")
                .classList.replace("bg-gray-300", "bg-blue-600");
        }
    }

    function ajaxRequest(requestType, url, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: requestType,
                url: url,
                data: data,
                headers: {
                    "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                },
                success: function (response) {
                    if (response.passed) resolve(response);
                    else resolve(response);
                },
                error: function (xhr, status, error) {
                    reject({ xhr, status, error });
                },
            });
        });
    }

    async function getProgrammes(url, data) {
        try {
            const response = await ajaxRequest("POST", url, data);
            if (response.statusCode === 808) {
                // console.log(response.data);
                await updateProgress();
                const progCont = document.getElementById(
                    "viewProgrammesContainer"
                );
                progCont.classList.replace("opacity-0", "opacity-100");
                progCont.classList.replace("scale-0", "scale-100");
                localStorage.setItem(
                    "recommendedProgrammes",
                    JSON.stringify(response.data)
                );
                console.log(response.data);
            } else console.log(response.msg);
        } catch (err) {
            console.log(err);
        }
    }

    function updateProgress() {
        return new Promise((resolve) => {
            const steps = document.querySelectorAll(".step");
            steps.forEach((step, index) => {
                setTimeout(() => {
                    step.classList.add("text-yellow-400");
                    if (index === steps.length - 1) {
                        resolve();
                    }
                }, 2000 * (index + 1));
            });
        });
    }

    function updateOptions() {
        const chosenValues = [...electivesSelect]
            .map((s) => s.value)
            .filter((v) => v);
        electivesSelect.forEach((select) => {
            [...select.options].forEach((opt) => {
                opt.hidden = false;
                if (
                    chosenValues.includes(opt.value) &&
                    select.value !== opt.value
                ) {
                    opt.hidden = true;
                }
            });
        });
    }

    function populateProgrammesRecommended() {
        const programmes = JSON.parse(
            localStorage.getItem("recommendedProgrammes")
        );
        console.log(programmes);
        if (programmes) {
            const programmesAccordionContainer = document.getElementById(
                "programmesAccordionContainer"
            );
            for (const faculty in programmes) {
                const div = document.createElement("div");
                div.classList.add(
                    "bg-black",
                    "transition-all",
                    "duration-500",
                    "w-full",
                    "flex",
                    "flex-col",
                    "justify-center",
                    "items-start",
                    "p-5",
                    "shadow",
                    "shadow-white/40",
                    "text-xs",
                    "md:text-xl",
                    "rounded-xl",
                    "gap-5",
                    "overflow-hidden",
                    "relative"
                );
                div.innerHTML = `<div class='flex justify-between items-center w-full'><p class='font-semibold'>${faculty}</p><i class='bi bi-caret-down-fill transition-all duration-500' onclick="showDropdown(this, '${faculty}')"></i></div>`;
                programmesAccordionContainer.appendChild(div);
                programmesDropdown = populateAccordion(programmes, faculty);
                div.appendChild(programmesDropdown);
            }
        }
    }

    function populateAccordion(programmes, faculty) {
        const div = document.createElement("div");
        div.classList.add(
            "transition-transform",
            "transition-opacity",
            "duration-500",
            "text-xs",
            "md:text-lg",
            "flex",
            "flex-col",
            "items-start",
            "justify-start",
            "gap-5",
            "p-5",
            "bg-white/5",
            "w-full",
            "rounded-xl",
            "slideUp"
        );
        const id = faculty.replaceAll(" ", "");
        div.id = id;
        programmes[faculty].forEach((programme) => {
            const p = document.createElement("p");
            p.innerHTML = `<i class='bi bi-mortarboard mr-2'></i>${programme}`;
            div.appendChild(p);
        });
        return div;
    }
});
function showDropdown(el, faculty) {
    const id = faculty.replaceAll(" ", "");
    const showingDivs = document.querySelectorAll(".slideDown");
    showingDivs.forEach((el) => el.classList.replace("slideDown", "slideUp"));
    document
        .querySelectorAll(".rotate-up")
        .forEach((el) => el.classList.remove("rotate-up"));
    const contentDiv = document.getElementById(id);
    if (![...showingDivs].includes(contentDiv)) {
        console.log("showing");
        contentDiv.classList.replace("slideUp", "slideDown");
        el.classList.add("rotate-up");
    }
}
