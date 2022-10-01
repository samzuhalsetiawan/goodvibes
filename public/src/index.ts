import Helper from "./my_module/Helper";
import IsearchUser from "./my_module/IsearchUser";

const BASE_URL = "https://goodvibes-pendikom.000webhostapp.com"
// const BASE_URL = "http://localhost/goodvibes"

const buttonSwitchTheme = document.querySelector(".button-switch-theme") as HTMLButtonElement;
const buttonUploadPostImage = document.querySelector(".input-file > input") as HTMLInputElement;
const inputSearch = document.querySelector(".search-bar > input") as HTMLInputElement;
const searchResultContainer = document.querySelector(".search-result-container") as HTMLDivElement
const postsContainer = document.querySelector(".posts-container") as HTMLDivElement
const buttonChoosePP = document.querySelector(".pilih-profile-picture") as HTMLInputElement

const helper = new Helper()

switchThemeTo(helper.getCookie("theme"))

function switchThemeTo(theme: string | null) {
    switch (theme) {
        case "light":
            document.documentElement.style.setProperty("--main-background-color", "var(--main-background-color-light)");
            document.documentElement.style.setProperty("--main-neumorph-shadow-color1", "var(--box-shadow1-neumorph-light)");
            document.documentElement.style.setProperty("--main-neumorph-shadow-color2", "var(--box-shadow2-neumorph-light)");
            document.documentElement.style.setProperty("--main-text-color", "var(--light-theme-text-color)");
            helper.setCookie("theme", "light", 1);
            break;

        case "dark":
        default:
            document.documentElement.style.setProperty("--main-background-color", "var(--main-background-color-dark)");
            document.documentElement.style.setProperty("--main-neumorph-shadow-color1", "var(--box-shadow1-neumorph-dark)");
            document.documentElement.style.setProperty("--main-neumorph-shadow-color2", "var(--box-shadow2-neumorph-dark)");
            document.documentElement.style.setProperty("--main-text-color", "var(--dark-theme-text-color)");
            helper.setCookie("theme", "dark", 1);
            break;
    }
}

function switchTheme() {
    if (helper.getCookie("theme") == "dark") {
        switchThemeTo("light")
    } else {
        switchThemeTo("dark")
    }
}


function showImageWantToUpload() {
    const imageFile = buttonUploadPostImage.files?.item(0)
    if (imageFile) {
        const fileReader = new FileReader()
        fileReader.onload = () => {
            const contentContainer = document.querySelector(".buat-post-container .content-container") as HTMLDivElement
            if (contentContainer.firstElementChild?.classList.contains("post-picture")) {
                const imgElement = contentContainer.firstElementChild as HTMLImageElement
                imgElement.src = (fileReader.result as string)
            } else {
                const imgElement = document.createElement("img");
                imgElement.alt = "upload-image"
                imgElement.classList.add("post-picture")
                imgElement.src = (fileReader.result as string)
                contentContainer.insertBefore(imgElement, contentContainer.firstChild)
            }
        }
        fileReader.readAsDataURL(imageFile)
    }
}

function createSearchUserCard(profilePicture: string, username: string, uid: string) {
    const img = document.createElement("img")
    img.src = `${BASE_URL}/img/${profilePicture}`
    img.alt = "Profile Picture"

    const name = document.createElement("input")
    name.type = "text"
    name.value = `@${username}`
    name.disabled = true

    const hiddenUid = document.createElement("input")
    hiddenUid.type = "hidden"
    hiddenUid.value = uid

    const card = document.createElement("button")
    card.classList.add("search-result-card")

    card.onclick = () => window.location.href = `${BASE_URL}/pages/account/${uid}`

    card.appendChild(img)
    card.appendChild(name)
    card.appendChild(hiddenUid)

    return card
}

async function doAjaxSearchingUser() {
    const username = inputSearch.value;
    const uid = inputSearch.dataset.uid as string;

    if (username == "" && username.length < 1) {
        searchResultContainer.classList.add("hidden")
        postsContainer.classList.remove("hidden")
    } else {
        searchResultContainer.classList.remove("hidden")
        postsContainer.classList.add("hidden")
    }

    const response = await fetch(`${BASE_URL}/pages/searchUser`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ username })
    });

    if (response.ok) {
        const data = await response.json() as IsearchUser
        searchResultContainer.innerHTML = ""
        data.users.forEach(user => {
            if (user.uid != uid) {
                searchResultContainer.appendChild(createSearchUserCard(user.profile_picture, user.username, user.uid))
            }
        });
    } else {
        console.error("Request Gagal")
    }
}

function previewProfilePicture() {
    const img = document.querySelector(".profile-picture") as HTMLImageElement
    const filelist = buttonChoosePP.files

    const imageFile = filelist?.item(0)
    if (imageFile) {
        const fileReader = new FileReader()
        fileReader.onload = () => {
            img.src = (fileReader.result as string)
        }
        fileReader.readAsDataURL(imageFile)
    }
}


buttonSwitchTheme ? (buttonSwitchTheme.onclick = switchTheme) : '';

buttonUploadPostImage ? (buttonUploadPostImage.onchange = showImageWantToUpload) : '';

inputSearch ? (inputSearch.onkeyup = doAjaxSearchingUser) : '';

buttonChoosePP ? (buttonChoosePP.onchange = previewProfilePicture) : '';