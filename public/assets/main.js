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
        firstNextBtn.addEventListener("click", function () {
            let currentStep = 1;
            showProgress(currentStep);
            currentStep++;

            const currentlyShowing = document.querySelector(
                "#coreSubjectsContainer"
            );
            const electiveSubjectsContainer = document.getElementById(
                "electiveSubjectsContainer"
            );
            // const coreResults = document.getElementById("coreResults");
            // coreResults.submit();
            slideRight(currentlyShowing, electiveSubjectsContainer);
        });
    }

    const secondNextBtn = document.getElementById("secondNextBtn");
    if (secondNextBtn) {
        secondNextBtn.addEventListener("click", function () {
            const coreResults = document.getElementById("coreResults");
            const formData = new FormData(coreResults);
            data = Object.fromEntries(formData);
            const electiveResults = document.getElementById("electiveResults");
            electiveResults.submit();
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

    // const backBtn = document.getElementById("backBtn");
    // if (backBtn) {
    //     backBtn.addEventListener("click", function () {
    //         const hiddenContainers = document.querySelectorAll(".hideCurrent");
    //         const recentlyHidden =
    //             hiddenContainers[hiddenContainers.length - 1];
    //         if (recentlyHidden) {
    //             const showing = document.querySelectorAll(".showing");
    //             const currentlyShowing = showing[showing.length - 1];
    //             currentlyShowing.classList.replace("showing", "hide");
    //             recentlyHidden.classList.replace("hideCurrent", "showing");
    //         }
    //     });
    // }

    function slideRight(currentContainer, nextContainer) {
        if (currentContainer) {
            currentContainer.classList.replace("showing", "hideCurrent");
            if (nextContainer) {
                nextContainer.classList.replace("hide", "showing");
            }
        }
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

    function ajaxRequest(el, data) {
        $.ajax({
            type: "POST",
            url: el.dataset.url,
            data: data,
            success: function (response) {
                console.log("success");
            },
            error: function (xhr, status, error) {
                console.log(error);
            },
        });
    }
});
