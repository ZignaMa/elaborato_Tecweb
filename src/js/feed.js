const profileImgType = 0;
const postImgType = 1;
let pageNumber = 0;
const postsForPage = 20;
let dbOffset = 0;
const container = "main > section";
let maxNumberPosts;

async function setMaxNumberPosts() {
    try {
        const response = await fetch("api/api-feed.php?giveMePosts=0");
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        maxNumberPosts = Array.isArray(json) && json.length > 0 ? json[0]["nPosts"] : 0;
    } catch (error) {
        console.log(error.message);
        maxNumberPosts = 0;
    }
}

function checkImgsPath(path, imgType){
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


function createPost(posts) {
    let table = "";
    if (posts.length == 0) {
      table += `
            0 post trovati.
            <a href="classes.php">Clicca qui per iscriverti a nuove classi</a>
        `;
    }
    for (let i = 0; i < posts.length; i++) {
        table += `
            <article>
                <header>
                    <a href="user.php?email=${posts[i]["email"]}">
                            <img src="${checkImgsPath(posts[i]["img_profilo"], profileImgType)}" alt="Foto profilo di ${posts[i]["nome_utente"]}" />
                        <p>${posts[i]["nome_utente"]}</p>
                    </a>
                    <a href="class.php?classe_id=${posts[i]["classe_id"]}">${posts[i]["corso_nome"]} | ${posts[i]["classe_nome"]} | sezione ${posts[i]["sezione"]} | anno ${posts[i]["anno_accademico"]}</a>
                </header>
                <a href="posts.php?idpost=${posts[i]["pubblicazione_id"]}">
                    ${checkImgsPath(posts[i]["percorso"], postImgType)}
                    <p>${posts[i]["testo"]}</p>
                    <p>${posts[i]["data_e_ora"]}</p>
                </a>
            </article>
        `;
    }
    return table;
}

function addHtml(container, text){
    document.querySelector(container).innerHTML += text;
}

async function getPostsData() {
    try {
        const response = await fetch(`api/api-feed.php?giveMePosts=1&offset=${dbOffset}&limit=${postsForPage}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        const posts = createPost(json);
        addHtml(container, posts);
    } catch (error) {
        console.log(error.message);
    }
    pageNumber += 1;
    dbOffset = pageNumber * postsForPage;
}

function scrollingListener(){
    window.addEventListener("scroll", () => {
        if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 5 && maxNumberPosts > pageNumber * postsForPage) {
            getPostsData();
        }
    });
}

async function init() {
    await setMaxNumberPosts();
    await getPostsData();
    scrollingListener();
}

init();
