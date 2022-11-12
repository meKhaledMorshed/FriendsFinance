setTimeout(() => {
    document.getElementById("navbar-info").style.display = "none";
}, 10000);

// preview notice in navbar
function handleNotice(noticeText, addClass = "alert-info") {
    let noticeBX = document.getElementById("notice-bx");

    let notice = document.getElementById("notice");

    noticeBX.classList.remove(
        "alert-success",
        "alert-info",
        "alert-danger",
        "d-none"
    );
    noticeBX.classList.add(addClass);
    notice.innerHTML = noticeText;

    setTimeout(() => {
        noticeBX.classList.add("d-none");
    }, 5000);
}

// preview image
function showimage(image, preview, defaultImg) {
    let view = document.getElementById(preview);
    if (image) {
        view.src = URL.createObjectURL(image);
    } else {
        view.src = defaultImg;
    }
}

// check input field
function checkInput(elmnt) {
    if (elmnt.value == "") {
        elmnt.style.backgroundColor = "#FFE3E1";
    } else {
        elmnt.style.backgroundColor = "#ffffff";
    }
}

// toggle input Fields disabled
function toggleInputDisabled(className = "") {
    try {
        if (className == "" || typeof className != "string") {
            throw Error("Invalid class name");
        }

        let inputs = document.getElementsByClassName(className);

        for (let input of inputs) {
            if (input.disabled == false) {
                input.disabled = true;
            } else {
                input.disabled = false;
            }
        }
    } catch (error) {
        console.log(error.message);
    }
}
// Populate Data List
async function populateDataList(datalist = "", parent = "", group = "") {
    try {
        if (
            datalist == "" ||
            parent == "" ||
            typeof datalist != "string" ||
            typeof parent != "string" ||
            typeof group != "string"
        ) {
            throw Error("Required data missing or invalid.");
        }

        const url = `${location.origin}/api/select-option/value/${parent}/${group}`;

        const res = await fetch(url);

        const results = res.status == 200 ? await res.json() : null;

        if (results != null) {
            let options;
            for (let result of results) {
                options += `<option value="${result}"></option>`;
            }
            document.getElementById(datalist).innerHTML = options;
        } else {
            throw Error(`No datalist found for ${group}.`);
        }
    } catch (err) {
        document.getElementById(datalist).innerHTML = "";
        console.log(err.message);
    }
}

// post data by fetch after form submit
async function postdata(url, data) {
    try {
        if (url == "" || data == "") {
            throw Error("Required data missing or invalid.");
        }

        const res = await fetch(url, {
            method: "POST",
            body: data,
        });

        const errResCode = [400, 401, 402, 403, 404, 405, 406];

        if (res.status == 201 || res.status == 202) {
            res.text().then((text) => swal(text, "", "success"));
            return true;
        } else if (errResCode.includes(res.status)) {
            res.text().then((text) => swal(text, "", "error"));
            return false;
        } else {
            throw Error("Request failed.");
        }
    } catch (error) {
        swal(error.message, "", "error");
        return false;
    }
}
