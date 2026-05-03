const classNav = document.getElementById("classNav");
const yearNav = document.getElementById("yearNav");
const professorNav = document.getElementById("professorNav");
const assistantNav = document.getElementById("assistantNav");
const userNav = document.getElementById("clientNav");
const adminNav = document.getElementById("adminNav");
const classSection = document.getElementById("classes");
const yearSection = document.getElementById("years");
const professorSection = document.getElementById("professors");
const assistantSection = document.getElementById("assistants");
const userSection = document.getElementById("clients");
const adminSection = document.getElementById("admins");
let userDisplayed = false;


function extractAnchor(url) {
  const res = url.match("#([a-zA-Z0-9]+)$");
  return res == null ? null : res[1];
}

/**
 * NAV
 */

function setLiNavStatus(id, active = false) {
    if (active) {
        id.style.backgroundColor = "var(--secondary-bg)";
    } else {
        id.style.backgroundColor = "var(--primary-bg)";
    }
}

function removeHidden(id) {
    id.removeAttribute("hidden");
}

function setHidden(...ids) {
    ids.forEach(id => {
        id.setAttribute("hidden", "");
    });
}

function classActive() {
    userDisplayed = false;
    setLiNavStatus(classNav, true);
    setLiNavStatus(yearNav);
    setLiNavStatus(professorNav);
    setLiNavStatus(assistantNav);
    setLiNavStatus(userNav);
    setLiNavStatus(adminNav);
    removeHidden(classSection);
    setHidden(yearSection, professorSection, assistantSection, userSection, adminSection);
}

function yearActive() {
    userDisplayed = false;
    setLiNavStatus(classNav);
    setLiNavStatus(yearNav, true);
    setLiNavStatus(professorNav);
    setLiNavStatus(assistantNav);
    setLiNavStatus(userNav);
    setLiNavStatus(adminNav);
    removeHidden(yearSection);
    setHidden(classSection, professorSection, assistantSection, userSection, adminSection);
}

function professorActive() {
    userDisplayed = false;
    setLiNavStatus(classNav);
    setLiNavStatus(yearNav);
    setLiNavStatus(professorNav, true);
    setLiNavStatus(assistantNav);
    setLiNavStatus(userNav);
    setLiNavStatus(adminNav);
    removeHidden(professorSection);
    setHidden(classSection, yearSection, assistantSection, userSection, adminSection);
}

function assistantActive() {
    userDisplayed = false;
    setLiNavStatus(classNav);
    setLiNavStatus(yearNav);
    setLiNavStatus(professorNav);
    setLiNavStatus(assistantNav, true);
    setLiNavStatus(userNav);
    setLiNavStatus(adminNav);
    removeHidden(assistantSection);
    setHidden(classSection, yearSection, professorSection, userSection, adminSection);
}

function userActive() {
    userDisplayed = true;
    setLiNavStatus(classNav);
    setLiNavStatus(yearNav);
    setLiNavStatus(professorNav);
    setLiNavStatus(assistantNav);
    setLiNavStatus(userNav, true);
    setLiNavStatus(adminNav);
    removeHidden(userSection);
    setHidden(classSection, yearSection, professorSection, assistantSection, adminSection);
}

function adminActive() {
    userDisplayed = false;
    setLiNavStatus(classNav);
    setLiNavStatus(yearNav);
    setLiNavStatus(professorNav);
    setLiNavStatus(assistantNav);
    setLiNavStatus(userNav);
    setLiNavStatus(adminNav, true);
    removeHidden(adminSection, true);
    setHidden(classSection, yearSection, professorSection, assistantSection, userSection);
}

document.querySelector("#classNav > a").addEventListener("click", () => classActive());
document.querySelector("#yearNav > a").addEventListener("click", () => yearActive());
document.querySelector("#professorNav > a").addEventListener("click", () => professorActive());
document.querySelector("#assistantNav > a").addEventListener("click", () => assistantActive());
document.querySelector("#clientNav > a").addEventListener("click", () => userActive());
document.querySelector("#adminNav > a").addEventListener("click", () => adminActive());

/**
 * FORM
 */

document.querySelectorAll("main form").forEach(form => {
    form.addEventListener("submit", function(event) {
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
            alert(data.message);
        } else {
            alert("The insertion was successful")
            location.reload();
        }
    })
})
});


const tmp_anchor = extractAnchor(window.location.href) || "class";
document.querySelector(
    "nav ul li a[href='#" + tmp_anchor + "']"
).click();