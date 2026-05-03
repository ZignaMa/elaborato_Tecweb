const clientSection = document.querySelector("#clients");
const clientsLimit = 20;
let clientOffset = 0;
let maxClientsNumber = 0;
let clientsScrollNumber = 0;


async function setMaxClientsNumber() {
    try {
        const response = await fetch(`api/api-admin.php?action=clientsNumber`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        maxClientsNumber = json[0]["count"];
    } catch (error) {
        console.log(error.message);
    }
}

function checkImgsPath(path) {
    realPath = "uploads/media" + path;
    if (path == null) {
        return "uploads/static/icons/user.svg";
    }
    return realPath;
}


function createClientsHtml(json) {
    const ul  = clientSection.querySelector("ul");

    json.forEach(client => {
        const li = document.createElement("li");

        li.innerHTML = `
            <a href=user.php?email=${client.email}>
            <img src="${checkImgsPath(client.img_profilo)}" alt="profile picture"/>
                <ul>
                    <li><p>E-mail</p><p>${client.email}</p></li>
                    <li><p>Nome utente</p><p>${client.nome_utente}</p></li>
                </ul>
            </a>
            <button data-active="${client.attivo}"></button>`;

            const button = li.querySelector("button");
            setFirstButtonState(button, client.email);
            ul.appendChild(li);
    });
}

async function getClientsList() {
    try {
        const response = await fetch(`api/api-admin.php?action=getClients&limit=${clientsLimit}&offset=${clientOffset}`);
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        const json = await response.json();
        createClientsHtml(json);
    } catch (error) {
        console.log(error.message);
    }
    clientsScrollNumber += 1;
    clientOffset = clientsScrollNumber * clientsLimit;
}

function scrollingListener() {
    window.addEventListener("scroll", () => {
        if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 5) {
            if (userDisplayed && maxClientsNumber > clientsScrollNumber * clientsLimit) {
                getClientsList();
            }
        }
    });
}

function setFirstButtonState(button, email){
    const active = button.dataset.active;
    if (active == 1) {
        button.style.backgroundColor = "var(--brand-bg)";
        button.textContent = "Blocca amministratore";
    } else {
        button.textContent = "Attiva amministratore";
        button.style.color = "var(--primary-fg)";
    }
    button.addEventListener("click", () => switchState(button, email));
}

async function init() {
    await setMaxClientsNumber();
    await getClientsList();
    scrollingListener();
}



init();
