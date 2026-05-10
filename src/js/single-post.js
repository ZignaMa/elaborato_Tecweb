class singlePost {
    constructor() {
        let params = new URLSearchParams(window.location.search);
        this.idPost = params.get("idpost");
        this.dataAndHourComment = "";
        this.idComment = "";
        this.maxComments = 20;
        this.isAdmin = false;
        this.newImgComment = [];
    }
}

let loading = false;
const post = new singlePost();

function generaHeader(headerInfo){
    let imgSrc = "uploads/media/"+headerInfo["img_profilo"];
    if(headerInfo["img_profilo"] == null) {
        imgSrc = "uploads/static/icons/user.svg";
    }
    let header = `<a href="user.php?email=${headerInfo["email"]}">
    <img src="${imgSrc}" alt="Foto profilo di ${headerInfo["nome_utente"]}" />${headerInfo["nome_utente"]} - ${headerInfo["data_e_ora"]}</a>
    <p>${headerInfo["corso_nome"]}: ${headerInfo["classe_nome"]}-${headerInfo["sezione"]}: ${headerInfo["anno_accademico"]}</p>`;
    return header;
}

function generaSectionPost(sectionPostTextInfo) {
    let section = "";
    if(post.isAdmin){
        section += `<a href="api/api-single-post.php?action=7&idPost=${post.idPost}" class="delete-post">Elimina post</a><a href="api/api-single-post.php?action=8&idPost=${post.idPost}" class="modify-post">Modifica post</a>`;
    }
    function escapeHTML(str){
        if(str == null) return "";
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    section += `<p>${escapeHTML(sectionPostTextInfo["testo"])}</p>`;
    if(sectionPostTextInfo["percorso"] != null) {
        section += `
            <img src="uploads/media/${sectionPostTextInfo["percorso"]}" alt="" />`;
        post.newImgComment.push(`uploads/media/${sectionPostTextInfo["percorso"]}`);
        if(post.isAdmin) {
            section += `<a href="api/api-single-post.php?action=9&idPost=${post.idPost}" class="delete-post-img">Elimina immagine</a>`;
        }
    }
    console.log("vediamo: ", sectionPostTextInfo["post_img"]);
    return section;
}

function generaSectionNewComments(commentsInfo) {
    let html = "";
    if(commentsInfo.length > 0) {
        post.idComment = commentsInfo[commentsInfo.length-1]["id"];
        post.dataAndHourComment = commentsInfo[commentsInfo.length-1]["data_e_ora"];
    }
    for (let i = 0; i < commentsInfo.length; i++) {
        let userImgSrc = "uploads/static/icons/user.svg";
        if(commentsInfo[i]["img_profilo"] != null) {
            userImgSrc = "uploads/media/"+commentsInfo[i]["img_profilo"];
        }
        let li ="";
        if(post.isAdmin){
            li = `
            <li>
                <a href="user.php?email=${commentsInfo[i]["email"]}"><img src="${userImgSrc}" alt="Foto profilo di ${commentsInfo[i]["nome_utente"]}" />${commentsInfo[i]["nome_utente"]} - ${commentsInfo[i]["data_e_ora"]}</a><a href="api/api-single-post.php?action=4&idComment=${commentsInfo[i]["id"]}" class="delete-comment">Elimina commento</a><a href="api/api-single-post.php?action=5&idComment=${commentsInfo[i]["id"]}" class="modify-comment">Modifica commento</a>
                <p>${escapeHTML(commentsInfo[i]["testo"])}</p>`;
        } else {
            li = `
            <li>
                <a href="user.php?email=${commentsInfo[i]["email"]}"><img src="${userImgSrc}" alt="Foto profilo di ${commentsInfo[i]["nome_utente"]}" />${commentsInfo[i]["nome_utente"]} - ${commentsInfo[i]["data_e_ora"]}</a>
                <p>${escapeHTML(commentsInfo[i]["testo"])}</p>`;
        }
        if(commentsInfo[i]["percorso"] != null) {
            const srcImg = `uploads/media/${commentsInfo[i]["percorso"]}`;
            post.newImgComment.push(srcImg);
            if(post.isAdmin){
                li += `<img src=${srcImg} alt="" /><a href="api/api-single-post.php?action=6&idComment=${commentsInfo[i]["id"]}" class="delete-comment-img">Elimina immagine</a>`;
            } else {
                li += `
                <img src=${srcImg} alt="" />`;
            }
        }
        li += `
            </li>`;
        html += li;
    }
    return html;
}

async function initializePost() {
    const url = "api/api-single-post.php?idpost="+post.idPost+"&action=1";
    try{
        const response = await fetch(url);
        if(!response.ok){
            throw new Error("Response status: "+response.status);
        }
        await checkIsAdmin();
        const json = await response.json();
        console.log(json);
        const header = generaHeader(json);
        const sectionPost = generaSectionPost(json);

        const selectHeader = document.querySelector("main > article > header");
        const selectSectionPost = document.querySelector("main > article > section");
        selectHeader.innerHTML = header;
        selectSectionPost.innerHTML = sectionPost;
        if(post.isAdmin) {
            hideForm(true);
        }
        updateComments();
        await loadComments();
        addHiddenListenerToOverlayImg();
    }
    catch(error){
        console.log(error.message);
    }
}

async function checkIsAdmin(){
    const url = "api/api-single-post.php?action=0";
    try{
        const response = await fetch(url);
        if(!response.ok){
            throw new Error("Response status: "+response.status);
        }
        const isAdmin = await response.json();
        console.log(isAdmin);
        post.isAdmin = isAdmin;
    }
    catch(error){
        console.log(error.message);
    }
}

async function loadComments() {
    const url = "api/api-single-post.php?idpost="+post.idPost+"&idComment="+post.idComment+"&dateAndHourComment="+post.dataAndHourComment+"&maxComments="+post.maxComments+"&action=2";
    try{
        const response = await fetch(url);
        if(!response.ok){
            throw new Error("Response status: "+response.status);
        }
        const json = await response.json();
        console.log(json);
        const sectionComments = generaSectionNewComments(json);
        const selectUlComments = document.querySelector("main > section > ul");
        if(sectionComments != "") {
            selectUlComments.insertAdjacentHTML("beforeend", sectionComments);
        }
        if(post.newImgComment.length > 0) {
            attachImageZoom(post.newImgComment);
        }
    }
    catch(error){
        console.log(error.message);
    }
}

function addHiddenListenerToOverlayImg(){
    const overlayImg = document.querySelector("main > section > img");
    overlayImg.addEventListener("click", () => {
        overlayImg.hidden = true;
        overlayImg.src = "";
    });
}

function attachImageZoom(hrefImgArray){
    const overlayImg = document.querySelector("main > section > img");
    hrefImgArray.forEach(src => {
        const img = document.querySelector(`img[src='${src}']`);
        if (typeof img.onclick != "function") {
            img.onclick = () => {
              overlayImg.src = src;
              overlayImg.hidden = false;
            };
        }
    });
}

function hideForm(action){
    const form = document.querySelector("main > section > form");
    const formImgButton = document.querySelector("main > section > form > ul > li:nth-child(2)");
    if(action) {
        form.hidden = true;
    } else {
        form.hidden = false;
        formImgButton.hidden = true;
    }
}

function updateComments() {
    window.addEventListener("scroll", async () => {
        if(loading) {
            return;
        }

        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= documentHeight - 200) {
            loading = true;
            await loadComments();
            loading = false;
        }
    });
}

document.querySelector("main > section > form").addEventListener("submit", function(event) {
    event.preventDefault();

    const form = event.target;
    if(!post.isAdmin) {
        form.action = "api/api-single-post.php?action=3&idpost="+post.idPost;
    }

    const formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Risposta dal server:", data);

        window.location.reload();
    })
    .catch(error => console.error("Errore:", error));

});

document.getElementById("imgcomment").addEventListener("change", function () {
    spanElement = document.getElementById("file-name");
    if(this.files && this.files.length > 0){
        spanElement.textContent = this.files[0].name;
    } else {
        spanElement.textContent = "";
    }
});

document.addEventListener("click", function (event) {
    const button = event.target;

    if (button.classList.contains("delete-comment") || button.classList.contains("delete-comment-img") || button.classList.contains("delete-post") || button.classList.contains("delete-post-img")) {
        event.preventDefault();

        const url = button.href;
        fetch(url)
            .finally(() => {
                if(button.classList.contains("delete-post")){
                    window.location.href = "admin.php";
                } else {
                    window.location.reload();
                }
            });
    }
    if (button.classList.contains("modify-comment") || button.classList.contains("modify-post")) {
        event.preventDefault();
        const url = button.href;
        const form = document.querySelector("main > section > form");
        form.action = url;
        const p = button.nextElementSibling;
        const textarea = form.querySelector("ul > li:first-child textarea");
        textarea.value = p.textContent;
        if(button.classList.contains("modify-post")) {
            textarea.placeholder = "Modifica testo post...";
        } else {
            textarea.placeholder = "Modifica testo commento...";
        }
        hideForm(false);
    }
});

initializePost();
