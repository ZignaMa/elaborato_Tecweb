const buttons = document.querySelectorAll("article section nav ul li");
const divs = document.querySelectorAll("article section div");
const isAdmin = document.querySelector("main div form input[type='submit']").value != "Aggiorna";

// return null if the anchor is not present or if it is empty
function extractAnchor(url) {
  const res = url.match("#([a-zA-Z0-9]+)$");
  return res == null ? null : res[1];
}

// return true if a section must be shown
function showSection(name, anchor) {
  if (anchor == null) {
    return name == "info";
  }
  return anchor == name;
}

// action: string to pass as "action" via GET parameter
// data: key-value pairs to be passed as POST parameters
async function callApi(action, data) {
  if (
    typeof action != "string" ||
    action.length <= 0 ||
    !(data instanceof Object)
  ) {
    return { status: "error", msg: "Input non validi", raw: "" };
  }

  const url = "api/user.php?action=" + action;
  const formData = new FormData();
  for (const [key, value] of Object.entries(data)) {
    formData.append(key, value);
  }
  const response = await fetch(url, {
    method: "POST",
    body: formData,
  });
  if (!response.ok) {
    return { status: "error", msg: response.statusText, raw: await response.text() };
  }
  const text = await response.clone().text();
  try {
    const json = await response.json();
    return {
      status: "ok",
      msg: json.message,
      raw: text
    };
  } catch (exception) {
    return { status: "error", msg: exception.message, raw: text };
  }
}

const ScrollerType = Object.freeze({
  COMMENTS: "comments",
  POSTS: "posts",
  CLASSES: "classes",
});
function isTypeValid(type) {
  return Object.values(ScrollerType).includes(type);
}

class Scroller {
  // Use `email` query param
  static #email = (() => {
    const params = new URLSearchParams(window.location.search);
    return params.get("email");
  })();
  #pageNumber = 0;
  #itemsPerPage = 20;
  #itemCount = -1;
  #type = null;

  // Do not call directly, use build(type) instead.
  constructor(type, itemCount) {
    this.#type = type;
    this.#itemCount = itemCount;
  }

  static getEndpoint(type) {
    switch (type) {
      case ScrollerType.COMMENTS:
        return "get_comments";
      case ScrollerType.POSTS:
        return "get_posts";
      case ScrollerType.CLASSES:
        return "get_classes";
      default:
        throw new Error("Tipo di scroller non valido");
    };
  }

  getEndpoint() {
    return Scroller.getEndpoint(this.#type);
  }

  static getSelector(type) {
    switch (type) {
      case ScrollerType.COMMENTS:
        return "main div#comments";
      case ScrollerType.POSTS:
        return "main div#posts";
      case ScrollerType.CLASSES:
        return "main div#classes";
      default:
        throw new Error("Tipo di scroller non valido");
    };
  }

  getSelector() {
    return Scroller.getSelector(this.#type);
  }

  static async build(type) {
    if (!isTypeValid(type)) {
      throw new Error("Tipo di scroller non valido");
    }
    const res = (
      await callApi(Scroller.getEndpoint(type), {
  // send canonical `email` param
  email: Scroller.#email,
        count: 0,
      })
    );
    if (res.status != "ok") {
      console.log(res.raw);
      throw new Error(res.msg);
    }
    return new Scroller(type, res.msg);
  }

  async getItems() {
    const items = await callApi(this.getEndpoint(), {
      email: Scroller.#email,
      page_number: this.#pageNumber,
      items_count: this.#itemsPerPage
    });
    if (items.status != "ok") {
      console.log(items.raw);
      throw new Error(items.msg);
    }
    this.#pageNumber += 1;
    return items.msg;
  }

  // Return the html-formatted items
  async compose() {
    const items = await this.getItems();
    let list = "";
    switch (this.#type) {
      case ScrollerType.POSTS:
        items.forEach((post) => {
          list += `
            <article>
                <header>
                    <a href="class.php?classe_id=${post["classe_id"]}">${post["corso_nome"]} | ${post["classe_nome"]} | sezione ${post["sezione"]} | anno ${post["anno_accademico"]}</a>
                    ${isAdmin ? `<button type='button' name='delete_post' value='${post["pubblicazione_id"]}'>Elimina post</button>` : ""}
                </header>
                <a href="posts.php?idpost=${post["pubblicazione_id"]}">
                    ${post["percorso"] != null ? `<img src="uploads/media/${post["percorso"]}" alt="immagine del post" />` : ""}
                    <p>${post["testo"]}</p>
                    <p>${post["data_e_ora"]}</p>
                </a>
            </article>
          `;
        });
        break;
      case ScrollerType.COMMENTS:
        items.forEach((comment) => {
          list += `
      <article>
        <header>
          <a href="class.php?classe_id=${comment["classe_id"]}">${comment["corso_nome"]} | ${comment["classe_nome"]} | sezione ${comment["sezione"]} | anno ${comment["anno_accademico"]}</a>
                    ${isAdmin ? `<button type='button' name='delete_comment' value='${comment["commento_id"]}'>Elimina commento</button>` : ""}
                </header>
                <a href="posts.php?idpost=${comment["pubblicazione_id"]}">
                    ${comment["percorso_pubblicazione"] != null ? `<img src="uploads/media/${comment["percorso_pubblicazione"]}" alt="immagine del post" />` : ""}
                    <p>${comment["testo_pubblicazione"]}</p>
                    ${comment["percorso_commento"] != null ? `<img src="uploads/media/${comment["percorso_commento"]}" alt="immagine del commento" />` : ""}
                    <p>${comment["testo_commento"]}</p>
                    <p>${comment["data_e_ora"]}</p>
                </a>
            </article>
          `;
        });
        break;
      case ScrollerType.CLASSES:
    // Safe because this will never be called more then once
    list += `
      <p>
        <a href="classes.php">Elenco classi</a>
        <a href="courses.php">Elenco corsi</a>
      </p>
    `;
    for (let course in items) {
      list += `<h2>${course}</h2>\n`;
      let classes = items[course];
      Array.from(classes).sort((a, b) => b["anno_accademico"].localeCompare(a["anno_accademico"])).forEach(cla => {
      list += `
      <article>
        <header>
          ${cla["classe_nome"]} - ${cla["sezione"]}
          <p>${cla["anno_accademico"]}</p>
        </header>
        <ul hidden>
          <li>
            <a href="class.php?classe_id=${cla["classe_id"]}">Apri classe</a>
          </li>
          <li>
            Corso: ${cla["corso_nome"]}
          </li>
          <li>
            Anno: ${cla["anno_accademico"]}
          </li>
          <li>
            Professore: ${cla["professore"]}
          </li>
          <li>
            Assistente: ${cla["assistente"] == null ? "Nessuno" : cla["assistente"]}
          </li>
        </ul>
      </article>
      `;
      });
        }
        break;
    }
    return list;
  }

  // Handle all buttons present only if the user is an admin
  #adminButtonHandler() {
    const tmp = document.querySelector(this.getSelector());
    const buttons = Array.from(tmp.querySelectorAll("button"));
    buttons.forEach((button) => {
      if (typeof button.onclick != "function") {
        switch (button.name) {
          case "delete_post":
            button.onclick = async () => {
              const res = await fetch("api/api-single-post.php?action=7&idPost=" + button.value);
              if (!res.ok) {
                alert("Delation of post failed: " + res.statusText);
              } else {
                location.reload();
              }
            };
            break;
          case "delete_comment":
            button.onclick = async () => {
              const res = await fetch("api/api-single-post.php?action=4&idComment=" + button.value);
              if (!res.ok) {
                alert("Delation of comment failed: " + res.statusText);
              } else {
                location.reload();
              }
            };
            break;
          default:
            // Invalid
        }
      }
    });
  }

  async initSection() {
    const tmp = document.querySelector(this.getSelector());
    // Init only if the element is empty
    if (tmp.innerHTML.trim().length == 0) {
        if (this.#itemCount == 0) {
        if (this.#type == ScrollerType.CLASSES) {
          tmp.innerHTML += `
              <p>
                  <a href="classes.php">Elenco classi</a>
                  <a href="courses.php">Elenco corsi</a>
              </p>
          `;
        }
        // Localized empty-list message
        const localizedZero = (type) => {
          switch (type) {
            case ScrollerType.POSTS:
              return "0 post trovati.";
            case ScrollerType.COMMENTS:
              return "0 commenti trovati.";
            case ScrollerType.CLASSES:
              return "0 classi trovate.";
            default:
              return "0 elementi trovati.";
          }
        };
        tmp.innerHTML += localizedZero(this.#type);
      } else {
        tmp.innerHTML = await this.compose();
        if (isAdmin) this.#adminButtonHandler();
        if (this.#type == ScrollerType.CLASSES) {
          // Click handler for classes' infos
          tmp.querySelectorAll("article header").forEach((item) => {
            item.addEventListener("click", (event) => {
              if (item.nextElementSibling.hidden) {
                item.nextElementSibling.removeAttribute("hidden");
              } else {
                item.nextElementSibling.setAttribute("hidden", "");
              }
            });
          });
        }
      }
    }
  }

  async #scrollHandler() {
    if (
      (window.innerHeight + window.scrollY >= document.body.scrollHeight - 5)
      && this.#pageNumber != 0
    ) {
      document.querySelector(this.getSelector()).innerHTML += await this.compose();
      if (isAdmin) this.#adminButtonHandler();
    }
  }

  addScrollHandler() {
    if (this.#itemCount <= (this.#pageNumber + 1) * this.#itemsPerPage) {
      return;
    }
    if (this.#type == ScrollerType.CLASSES) return;
    window.addEventListener("scroll", this.#scrollHandler.bind(this));
  }

  removeScrollHandler() {
    window.removeEventListener("scroll", this.#scrollHandler.bind(this));
  }
}

async function init() {
  const postScroller = await Scroller.build(ScrollerType.POSTS);
  postScroller.removeScrollHandler();

  const commentScroller = await Scroller.build(ScrollerType.COMMENTS);
  commentScroller.removeScrollHandler();

  // Classes do not have pagination, so this is not a real scroller
  const classesScroller = await Scroller.build(ScrollerType.CLASSES);

  // Set buttons actions
  buttons.forEach((button) => {
    button.addEventListener("click", async (event) => {
      event.preventDefault();
      const anchor = extractAnchor(button.firstChild.href);
      // Update anchor in the URL
      window.location.hash = anchor;

      // Add class "active" to the clicked button, remove to the others
      buttons.forEach((item) => {
        const bHref = item.firstChild.href;
        if (showSection(extractAnchor(bHref), anchor)) {
          if (!item.classList.contains("active")) {
            item.classList.add("active");
          }
        } else {
          if (item.classList.contains("active")) {
            item.classList.remove("active");
          }
        }
      });
      // Show the relevant div based on its id
      divs.forEach((item) => {
        const divId = item.attributes["id"].value;
        if (showSection(divId, anchor)) {
          if (item.hidden) {
            item.removeAttribute("hidden");
          }
        } else {
          if (!item.hidden) {
            item.setAttribute("hidden", "");
          }
        }
      });
      // Handle loading of dynamic content
      switch (anchor) {
        case "info":
          // Do nothing
          break;
        case "posts":
          await postScroller.initSection();
          postScroller.addScrollHandler();
          break;
        case "comments":
          await commentScroller.initSection();
          commentScroller.addScrollHandler();
          break;
        case "classes":
          await classesScroller.initSection();
          break;
        default:
        // Do nothing
      }
    });
  });

  // Form event listener
  document.querySelector("main div form").addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!isAdmin) {
      const res = await callApi("update", {
        // send canonical italian key `img_profilo`
        img_profilo: document.querySelector("main div form input[type='file']").files[0],
      });
      console.log(res.msg);
      if (res.status != "ok") {
        console.log(res.raw);
        alert(res.msg);
      } else {
        location.reload();
      }
    } else {
      const res = await callApi("toggle_active", {
        // use canonical `email` param
        email: (new URLSearchParams(window.location.search)).get("email")
      });
      if (res.status != "ok") {
        console.log(res.raw);
        alert(res.msg);
      } else {
        location.reload();
      }
    }
  });

  // Set initial status
  const tmp_anchor = extractAnchor(window.location.href) || "info";
  document.querySelector(
    "article section nav ul li a[href='#" + tmp_anchor + "'"
  ).parentNode.click();
}

init();
