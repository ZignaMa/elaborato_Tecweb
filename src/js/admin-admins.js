const adminsList = document.querySelectorAll("#admins > ul > li");

async function changeStatus(email) {
    try {
        const response =  await fetch(`api/api-admin.php?action=change&email=${email}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        return json;
    } catch (error) {
        console.log(error.message);
    }
}

async function switchState(button, email){
    exitStatus = await changeStatus(email);
    if (!exitStatus) {
        console.error("error throw in the process to change the state of" + email);
    }
    if (button.dataset.active == 1) {
        button.textContent = "Imposta inattivo";
        button.style.color = "var(--primary-fg)";
        button.style.backgroundColor = "var(--secondary-bg)";
        button.dataset.active = 0;
    } else {
        button.textContent = "Blocca amministratore";
        button.style.color = "var(--primary-bg)";
        button.style.backgroundColor = "var(--brand-bg)";
        button.dataset.active = 1;
    }
}

adminsList.forEach(li => {
    const email = li.querySelector("ul > li:first-child > p:last-child").textContent;
    const button = li.querySelector("ul > li > button");
    const active = button.dataset.active;
    if (active == 1) {
        button.style.backgroundColor = "var(--brand-bg)";
        button.textContent = "Blocca amministratore";
    } else {
        button.textContent = "Attiva amministratore";
        button.style.color = "var(--primary-fg)";
    }
    button.addEventListener("click", () => switchState(button, email));
});
