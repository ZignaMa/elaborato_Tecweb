const urlParams = new URLSearchParams(window.location.search);
const followClassId = urlParams.get("classe_id");
const documentFollowButton = document.getElementById("followButton");
const documentUnfollowButton = document.getElementById("unfollowButton");
const documentAddResourcesButton = document.querySelector("#resources > button")
let startStatus = true;

function switchAddResources() {
    if (resourcesSectionActive && documentFollowButton.hasAttribute("hidden")) {
        documentAddResourcesButton.removeAttribute("hidden");
    } else {
        documentAddResourcesButton.setAttribute("hidden", "");
    }
}

function resetButton(){
    documentFollowButton.setAttribute("hidden", "");
    documentUnfollowButton.setAttribute("hidden", "");
    switchAddResources();
}

async function actualStatus() {
    try {
    const response = await fetch(`api/api-class-follow.php?getStatus=1&classe_id=${followClassId}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        return json[0]["count"];
    } catch (error) {
        console.log(error.message);
    }
}

async function unfollow() {
    try {
    const response = await fetch(`api/api-class-follow.php?getStatus=0&followOperation=0&classe_id=${followClassId}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        if (json) {
            resetButton();
            documentFollowButton.removeAttribute("hidden");
            switchAddResources();
        }
    } catch (error) {
        console.log(error.message);
    }

}

async function follow() {
    try {
    const response = await fetch(`api/api-class-follow.php?getStatus=0&followOperation=1&classe_id=${followClassId}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        if (json) {
            resetButton();
            documentUnfollowButton.removeAttribute("hidden");
            switchAddResources();
        }
    } catch (error) {
        console.log(error.message);
    }
}

function followSwitching(){
    documentFollowButton.addEventListener("click", async () => {
        if (await actualStatus() == 0 && !startStatus) {
            await follow()
        }
    });
    documentUnfollowButton.addEventListener("click", async () => {
        if (await actualStatus() == 1 && !startStatus) {
            await unfollow()
        }

    });
}

async function init() {
    resetButton();
    if (document.querySelector("#resources").dataset.admin == 1) {
        switchAddResources();
    } else if (await actualStatus()==0) {
        documentFollowButton.removeAttribute("hidden");
        switchAddResources();
    } else {
        documentUnfollowButton.removeAttribute("hidden");
        switchAddResources();
    }
    startStatus = false;
    await followSwitching();
}

init();
