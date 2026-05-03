class Post {
    constructor(id_corso="", id_classe="", sezione="", id_anno="") {
        this.id_corso = id_corso;
        this.id_classe = id_classe;
        this.sezione = sezione;
        this.id_anno = id_anno;
    }
}

const post = new Post();

function generaCorsi(corsi) {
    let select = "";
    if(corsi.length==0) {
        select = `<option value="">-- Nessun corso selezionabile --</option>`;
    } else if(corsi.length==1) {
    // backend returns italian keys: corso_id / corso_nome
    let row_value = corsi[0]["corso_id"];
    let label = corsi[0]["corso_nome"] || "";
        select = `<option value="${row_value}">${label}</option>`;
    } else {
        select = ` <option value="">-- Corso --</option>`;
        for(let i = 0; i < corsi.length; i++){
            let row_value = corsi[i]["corso_id"];
            let label = corsi[i]["corso_nome"] || "";
            select += `<option value="${row_value}">${label}</option>`;
        }
    }
    return select;
}

function generaMaterie(materie){
    let select = "";
    if(materie.length==0) {
        select = `<option value="">-- Nessuna materia selezionabile --</option>`;
    } else if(materie.length==1) {
        let row_value = materie[0]["nome"];
        let label = materie[0]["nome"] || "";
        select = `<option value="${row_value}">${label}</option>`;
    } else {
        select = ` <option value="">-- Materia --</option>`;
        for(let i = 0; i < materie.length; i++){
            let row_value = materie[i]["nome"];
            let label = materie[i]["nome"] || "";
            select += `<option value="${row_value}">${label}</option>`;
        }
    }
    return select;
}

function generaAnni(anni) {
    let select = "";
    if(anni.length==0) {
        select = `<option value="">-- Nessun anno selezionabile --</option>`;
    } else if(anni.length==1) {
        let row_value = anni[0]["anno_accademico"];
        let label = anni[0]["anno_accademico"] || "";
        select = `<option value="${row_value}">${label}</option>`;
    } else {
        select = `<option value="">-- Anno --</option>`;
        for(let i = 0; i < anni.length; i++){
            let row_value = anni[i]["anno_accademico"];
            let label = anni[i]["anno_accademico"] || "";
            select += `<option value="${row_value}">${label}</option>`;
        }
    }
    return select;
}

function generaSezioni(sezioni) {
    let select = "";
    if(sezioni.length==0) {
        select = `<option value="">-- Nessuna sezione selezionabile --</option>`;
    } else if(sezioni.length==1) {
        let row_value = sezioni[0]["sezione"];
        let label = sezioni[0]["sezione"] || "";
        select = `<option value="${row_value}">${label}</option>`;
    } else {
        select = ` <option value="">-- Sezione --</option>`;
        for(let i = 0; i < sezioni.length; i++){
            let row_value = sezioni[i]["sezione"];
            let label = sezioni[i]["sezione"] || "";
            select += `<option value="${row_value}">${label}</option>`;
        }
    }
    return select;
}

async function getCoursesOfUser(){
    const url = "api/api-posts.php?action=1";
    try{
        const response = await fetch(url);
        if(!response.ok){
            throw new Error("Response status: "+response.status);
        }
        const json = await response.json();
        console.log(json);
        const corsi = generaCorsi(json);
        const select = document.querySelector("main > form > ul > li:nth-child(1) > select");
        select.innerHTML = corsi;
        select.onchange = () => {
            post.id_corso = select.value;
            console.log("Post aggiornato:", post);
            getClassesByCourseOfUser();
        };
        if (select.value) {
            post.id_corso = select.value;
        } else {
            post.id_corso = "";
        }
    }
    catch(error){
        console.log(error.message);
    }
    getClassesByCourseOfUser();
    console.log("Post aggiornato:", post);
}

async function getClassesByCourseOfUser() {
    const select = document.querySelector("main > form > ul > li:nth-child(2) > select");
    if(post.id_corso != ""){
        const url = "api/api-posts.php?action=2&corso="+post.id_corso;
        try{
            const response = await fetch(url);
            if(!response.ok){
                throw new Error("Response status: "+response.status);
            }
            const json = await response.json();
            console.log(json);
            const materie = generaMaterie(json);
            select.innerHTML = materie;
            select.onchange = () => {
                post.id_classe = select.value;
                console.log("Post aggiornato:", post);
                getYearsByCourseAndClass();
            };
            if (select.value) {
                post.id_classe = select.value;
            } else {
                post.id_classe = "";
            }
        }
        catch(error){
            console.log(error.message);
        }
    } else {
        select.innerHTML = `<option value=""></option>`;
        post.id_classe = "";

    }
    getYearsByCourseAndClass();
    console.log("Post aggiornato:", post);
}

async function getYearsByCourseAndClass() {
    const select = document.querySelector("main > form > ul > li:nth-child(3) > select");
    if(post.id_classe != "") {
    const url = "api/api-posts.php?action=3&corso="+post.id_corso+"&classe="+post.id_classe;
        try{
            const response = await fetch(url);
            if(!response.ok){
                throw new Error("Response status: "+response.status);
            }
            const json = await response.json();
            console.log(json);
            const anni = generaAnni(json);
            select.innerHTML = anni;
            select.onchange = () => {
                post.id_anno = select.value;
                console.log("Post aggiornato:", post);
                getSectionOfUser();
            };
            if (select.value) {
                post.id_anno = select.value;
                console.log("Post aggiornato:", post);
            } else {
                post.id_anno = "";
            }
        }
        catch(error){
            console.log(error.message);
        }
    } else {
        select.innerHTML = `<option value=""></option>`;
        post.id_anno = "";
    }
    getSectionOfUser();
}

async function getSectionOfUser(){
    const select = document.querySelector("main > form > ul > li:nth-child(4) > select");
    if(post.id_corso != "" && post.id_classe != "" && post.id_anno != "") {
    const url = "api/api-posts.php?action=4&corso="+post.id_corso+"&classe="+post.id_classe+"&anno="+post.id_anno;
        try{
            const response = await fetch(url);
            if(!response.ok){
                throw new Error("Response status: "+response.status);
            }
            const json = await response.json();
            console.log(json);
            const sezioni = generaSezioni(json);
            select.innerHTML = sezioni;
            select.onchange = () => {
                post.sezione = select.value;
                console.log("Post aggiornato:", post);
            };
            if (select.value) {
                post.sezione = select.value;
                console.log("Post aggiornato:", post);
            } else {
                post.sezione = "";
            }
        }
        catch(error){
            console.log(error.message);
        }
    } else {
        select.innerHTML = `<option value=""></option>`;
        post.sezione = "";
        console.log("Post aggiornato:", post);
    }
}

document.getElementById("imgpost").addEventListener("change", function () {
    const spanElement = this.nextElementSibling;
    if (this.files && this.files.length > 0) {
        spanElement.textContent = this.files[0].name;
    } else {
        spanElement.textContent = 'Nessuna immagine';
    }
});

document.querySelector("main > form").addEventListener("submit", function(event) {
    event.preventDefault();

    const form = event.target;

    const formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Risposta dal server:", data);

        window.location.href = "posts.php?idpost="+data["idPost"];
    })
    .catch(error => console.error("Errore:", error));

});

getCoursesOfUser();