const classDataList = document.querySelector("#class");


async function getClassForCourse(course) {
    console.log(course);
    try {
    const response = await fetch(`api/api-admin.php?action=getClassesByCourseId&corso_id=${course}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        return json;
    } catch (error) {
        console.log(error.message);
    }
}

async function fillClassDatalist(course) {
    let html = "";
    const classes = await getClassForCourse(course);
    classes.forEach(classRow => {
        const value = classRow["nome"] || "";
        html += `
        <option value="${value}"></option>`;
    });
    document.querySelector("#classes #classList").innerHTML = html;
}

document.querySelector("#classes #corso").addEventListener("change",(event) => {
    fillClassDatalist(event.target.value);
});