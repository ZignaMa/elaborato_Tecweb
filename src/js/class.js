const params = new URLSearchParams(window.location.search);
const classId = params.get("classe_id");
const postsNav = document.getElementById("postsNav");
const infoNav = document.getElementById("infoNav");
const resourcesNav = document.getElementById("resourcesNav");
const postsClick = document.querySelector("#postsNav a");
const infoClick = document.querySelector("#infoNav a");
const resourcesClick = document.querySelector("#resourcesNav a");
const documentPosts = document.getElementById("posts");
const documentInfo = document.getElementById("info");
const documentResources = document.getElementById("resources");
let postsSectionActive = true;
let resourcesSectionActive = false;
const profileImgType = 0;
const postImgType = 1;
let postsScrollNumber = 0;
const postsDbLimit = 5;
let maxNumberPosts = 0;
let maxNumberResources = 0;
let postsDbOffset = 0
let resourcesScrollNumber = 0;
const resourcesDbLimit = 40;
let resourcesDbOffset = 0


/**
 * NAV SECTION
 */

function postsActive() {
    postsSectionActive = true;
    resourcesSectionActive = false;
    postsNav.style.backgroundColor = "var(--secondary-bg)";
    infoNav.style.backgroundColor = "var(--primary-bg)";
    resourcesNav.style.backgroundColor = "var(--primary-bg)";
    postsNav.style.borderBottom = "0";
    infoNav.style.borderBottom = "2px solid var(--border)";
    resourcesNav.style.borderBottom = "2px solid var(--border)";
    documentPosts.removeAttribute("hidden");
    documentInfo.setAttribute("hidden", "");
    documentResources.setAttribute("hidden", "");
}

function infoActive() {
    resourcesSectionActive = false;
    postsSectionActive = false;
    postsNav.style.backgroundColor = "var(--primary-bg)";
    infoNav.style.backgroundColor = "var(--secondary-bg)";
    resourcesNav.style.backgroundColor = "var(--primary-bg)";
    postsNav.style.borderBottom = "2px solid var(--border)";
    infoNav.style.borderBottom = "0";
    resourcesNav.style.borderBottom = "2px solid var(--border)";
    documentPosts.setAttribute("hidden", "");
    documentInfo.removeAttribute("hidden");
    documentResources.setAttribute("hidden", "");
}

function resourcesActive() {
    resourcesSectionActive = true;
    postsSectionActive = false;
    postsNav.style.backgroundColor = "var(--primary-bg)";
    infoNav.style.backgroundColor = "var(--primary-bg)";
    resourcesNav.style.backgroundColor = "var(--secondary-bg)";
    postsNav.style.borderBottom = "2px solid var(--border)";
    infoNav.style.borderBottom = "2px solid var(--border)";
    resourcesNav.style.borderBottom = "0";
    documentPosts.setAttribute("hidden", "");
    documentInfo.setAttribute("hidden", "");
    documentResources.removeAttribute("hidden");
    switchAddResources();
}


function sectionSwitching() {
    document.addEventListener("DOMContentLoaded", () => postsActive());
    postsClick.addEventListener("click", () => postsActive());
    infoClick.addEventListener("click", () => infoActive());
    resourcesClick.addEventListener("click", () => resourcesActive());
}

function scrollingListener() {
    window.addEventListener("scroll", () => {
        if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 5) {
            if (postsSectionActive && maxNumberPosts > postsScrollNumber * postsDbLimit) {
                getPosts();
            } else if (resourcesSectionActive && maxNumberResources > resourcesScrollNumber * resourcesDbLimit) {
                getResources();
            }
        }
    });
}

/**
 * POST SECTION
 */

async function setMaxNumberPosts() {
    try {
    const response = await fetch(`api/api-class.php?action=1&classe_id=${classId}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        maxNumberPosts = json[0]["nPosts"];
    } catch (error) {
        console.log(error.message);
    }
}

function checkImgsPath(path, imgType) {
    const realPath = "uploads/media/" + path;
    if (path == null) {
        if (imgType == profileImgType) {
            return "uploads/static/icons/user.svg";
        }
        return "";
    }
    if (imgType == postImgType) {
        return `<img src="${realPath}" alt="" />`;
    }
    return realPath;
}

async function deletePost(idPost) {
    try {
        const response = await fetch(`api/api-single-post.php?action=7&idPost=${idPost}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        //const json = await response.json();
        documentPosts.innerHTML = "";
        postsScrollNumber = 0;
        postsDbOffset = 0;
        getPosts();
    } catch (error) {
        console.log(error.message);
    }
}

function createPostsHtml(posts) {
    posts.forEach(post => {
        const article = document.createElement("article");

        article.innerHTML = `
            <header>
                    <a href="user.php?email=${post["email"]}">
                    <img src="${checkImgsPath(post["img_profilo"], profileImgType)}" alt="Foto profilo di ${post["nome_utente"]}" />
                    <p>${post["nome_utente"]}</p>
                </a>
            </header>
            <a href="posts.php?idpost=${post["pubblicazione_id"]}">
                ${checkImgsPath(post["percorso"], postImgType)}
                <p>${post["testo"]}</p>
                <p>${post["data_e_ora"]}</p>
            </a>`;

        if (documentPosts.dataset.admin === "1") {
            const button = document.createElement("button");
            button.addEventListener("click", async () => await deletePost(post["pubblicazione_id"]));
            button.textContent = "Elimina post";
            article.appendChild(button);
        }

        documentPosts.appendChild(article);
    });
}

async function getPosts() {
    try {
    const response = await fetch(`api/api-class.php?action=3&classe_id=${classId}&limit=${postsDbLimit}&offset=${postsDbOffset}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        createPostsHtml(json);
    } catch (error) {
        console.log(error.message);
    }
    postsScrollNumber += 1;
    postsDbOffset = postsScrollNumber * postsDbLimit;

}

/**
 * RESOURCE SECTION
 */

async function setMaxNumberResources() {
    try {
    const response = await fetch(`api/api-class.php?action=2&classe_id=${classId}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        maxNumberResources = json[0]["nResources"];
    } catch (error) {
        console.log(error.message);
    }
}

function switchAddResources() {
    if (resourcesSectionActive && document.querySelector("#followButton").hasAttribute("hidden")) {
        document.querySelector("#resources > button").removeAttribute("hidden");
    } else {
        document.querySelector("#resources > button").setAttribute("hidden", "");
    }
}

async function deleteResource(resource) {
    try {
        const response = await fetch(`api/api-class.php?action=5&percorso=${encodeURIComponent(resource)}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        documentResources.querySelector("ul").innerHTML = "";
        resourcesScrollNumber = 0;
        resourcesDbOffset = 0;
        await getResources();

    } catch (error) {
        console.log(error.message);
    }
}

function createResourcesHtml(resources) {
    const ulResources = document.querySelector("#resources > ul");
    const profilePath = "uploads/media/";

    resources.forEach(resource => {
        let nameFile = resource["percorso"].split("/");
        let pathFile = profilePath + resource["percorso"];
        const li = document.createElement("li");

    if (resource["percorso"].toLowerCase().endsWith(".pdf")) {
            li.innerHTML = `
            <a href="${pathFile}" >
            <img src="uploads/static/icons/file-pdf.svg" alt="${nameFile[nameFile.length - 1]}"/>
            </a>`;
        } else {
            li.innerHTML = `<img src="${pathFile}" alt=""/>`;
        }

        if (documentResources.dataset.admin === "1") {
            const button = document.createElement("button");
            button.addEventListener("click", async () => await deleteResource(resource["percorso"]));
            button.textContent = "Elimina risorsa";
            li.appendChild(button);
        }

        ulResources.appendChild(li);

    });
}


async function getResources() {
    try {
    const response = await fetch(`api/api-class.php?action=4&classe_id=${classId}&limit=${resourcesDbLimit}&offset=${resourcesDbOffset}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        createResourcesHtml(json);
    } catch (error) {
        console.log(error.message)
    }
    resourcesScrollNumber = resourcesScrollNumber + 1;
    resourcesDbOffset = resourcesScrollNumber * resourcesDbLimit;
}

async function init() {
    sectionSwitching();
    await setMaxNumberPosts();
    await getPosts();
    await setMaxNumberResources();
    await getResources();
    scrollingListener();
}

init();