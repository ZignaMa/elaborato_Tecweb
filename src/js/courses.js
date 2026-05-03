function generateCourse(corsi) {
    let select = `
                        <option value="">-- Select course --</option>`;
    for(let i = 0; i < corsi.length; i++){
        // il backend restituisce campi in italiano: corso_id e corso_nome
        let row_value = "corso_"+corsi[i]["corso_id"];
        select += `
                        <option value="${row_value}">${corsi[i]["corso_nome"]}</option>`;
    }
    return select;
}

async function getAllCourses(){
    const url = "api/api-posts.php?user=0";
    try{
        const response = await fetch(url);
        if(!response.ok){
            throw new Error("Response status: "+response.status);
        }
        const json = await response.json();
        console.log(json);
        const corsi = generateCourse(json);
        const select = document.querySelector("main > form > ul > li > select");
        select.innerHTML = corsi;
    }
    catch(error){
        console.log(error.message);
    }
}

getAllCourses();