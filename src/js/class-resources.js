const resourcesParams = new URLSearchParams(window.location.search);
const resourcesClassId = resourcesParams.get("classe_id");
const resourcesButton = document.querySelector("#resources button");
const resourcesErrorDisplay = document.getElementById("errors");
const resourcesAddFile = document.getElementById("newResources");
const resourcesSubmit = document.getElementById("submit");
let prepared = false;



resourcesButton.addEventListener("click", () => {
    if (!prepared) {
    document.querySelector("main section:first-of-type").setAttribute("hidden", "");
    document.querySelector("main section:last-child").removeAttribute("hidden");
    document.querySelector("#unfollowButton").setAttribute("hidden", "");
    reset();
    prepared = true;
    resourcesAddFile.click()
    } else {
        reset();
        resourcesAddFile.click()
    }
})

function reset() {
    resourcesErrorDisplay.setAttribute("hidden", "");
    resourcesAddFile.setAttribute("hidden", "");
    resourcesSubmit.setAttribute("hidden", "");
}

resourcesAddFile.addEventListener("change", () => {
    let files = resourcesAddFile.files;
    console.log(files);
    if (checkFiles(files)) {
        resourcesSubmit.removeAttribute("hidden");
    }
});

function wrongTypeFile(type) {
    return !(type == "image/jpeg" || type == "image/png" || type == "image/gif" || type == "application/pdf");
}

function printError(error) {
    reset();
    resourcesErrorDisplay.removeAttribute("hidden");
    resourcesErrorDisplay.innerHTML = error;
    console.log(error);

}

function checkFiles(files){
    let maxSize = 4194304;
    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        if (wrongTypeFile(file.type)) {
            printError("Unsupported file format: " + file.name);
            return false;
        } else if (file.size > maxSize) {
            printError("file: " + file.name + " exceeds the maximum allowed size (4MiB)");
            return false;
        }
    }
    return true;
}

document.querySelector("#resources form").addEventListener("submit", function(event) {
    event.preventDefault();

    const form = event.target;

    const formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error(data.error);
        }
    window.location.href = "class.php?classe_id=" + resourcesClassId;
    })
});
