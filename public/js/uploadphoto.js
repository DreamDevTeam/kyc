let activeType = "analog";

// let btnSwitches = document.querySelectorAll("div");
// console.log("test", btnSwitches);
// btnSwitches.forEach((item) => {
//     item.addEventListener("click", (event) => {
//         console.log(event);
//         const target = event.target;

//         btnSwitches.forEach((item) => {
//             item, classList.remove("active");
//         });

//         target.classList.add("active");
//     });
// });

function back() {
    console.log("back");

    let digitalPhoto = null;
    let digitalSelfie = null;
    let dataStepOne = [];
    let dataStepTwo = null;
    let dataUser = {};

    document.getElementById("digitalphoto").src = "/digital.png";
    document.getElementById("idcardphoto").src = "/card-front.jpg";
    document.getElementById("idcardbackphoto").src = "/card-back.jpg";

    document.getElementById("step-one-analog").style.display = "none";
    document.getElementById("step-one-digital").style.display = "none";
    document.getElementById("start").style.display = "flex";
    document.getElementById("back-icon").style.display = "none";
}

function changeType(event, type) {
    activeType = type;
    const target = event.target;
    let btnSwitches = document.querySelectorAll(".switch");

    btnSwitches.forEach((item) => {
        item.classList.remove("active");
    });

    target.classList.add("active");
}

function selectPhoto(id) {
    // document.getElementById("loader").style.display = "flex";
    console.log("select", id);
    const inputFileButton = document.getElementById(id);
    inputFileButton.click();
}

function openPhoto(event) {
    console.log("open", event.target.files[0]);
    addPhoto(event.target.id, event.target.files[0]);
}

function addPhoto(type, photo) {
    // console.log('upload', photo);
    let json;
    const reader = new FileReader();
    if (type === "digital") {
        digitalPhoto = photo;
    } else if (!photo) return;
    reader.readAsDataURL(photo);
    reader.onload = async () => {
        const encodedFile = reader.result.split(",")[1];

        // VALIDATE
        if (["idcard", "idcardback"].includes(type)) {
            if (
                dataStepOne &&
                !!dataStepOne.find((item) => item.type === type)
            ) {
                dataStepOne = dataStepOne.map((item) => {
                    if (item.type === type)
                        return (item = { type: type, photo: encodedFile });
                    else return item;
                });
            } else {
                dataStepOne.push({ type: type, photo: encodedFile });
            }
        } else if (["selfie"].includes(type)) {
            if (activeType === "analog") {
                dataStepTwo = { type: type, photo: encodedFile };
            } else if (activeType === "digital") {
                digitalSelfie = photo;
                document
                    .getElementById("step-two-btn")
                    .removeAttribute("disabled");
            }
        } else if (["digital"].includes(type)) {
            document
                .getElementById("step-one-btn-digital")
                .removeAttribute("disabled");
        }

        const thumb = document.getElementById(`${type}photo`);
        thumb.parentElement.style.display = "flex";
        thumb.src = reader.result;
        // TEST and update
        if (dataStepOne.length === 2) {
            document
                .getElementById("step-one-btn-analog")
                .removeAttribute("disabled");
        }
        if (!!dataStepTwo) {
            document.getElementById("step-two-btn").removeAttribute("disabled");
        }
        // document.getElementById("loader").style.display = "none";
        // console.log(encodedFile);
    };
}

async function saveIdScreenshot(payId = null) {
    payId = atob(payId);

    document.getElementById("myloader").style.display = "flex";
    const formData = new FormData();
    formData.append("image", digitalPhoto);
    formData.append("payId", payId);

    try {
        let response = await fetch("/api/digital/idcard", {
            method: "POST",
            body: formData,
        });
        document.getElementById("myloader").style.display = "none";
        let result = await response.json();
        if (result.success) {
            const stepOne = (document.getElementById(
                "step-one-digital"
            ).style.display = "none");
            const stepTwo = (document.getElementById("step-two").style.display =
                "flex");
            document.getElementById("back-icon").style.display = "none";

            dataUser = {
                tid: result.tid,
            };
        } else {
            document.getElementById("error-message-one").style.display =
                "block";
            document.getElementById("error-message-one").innerText =
                result.message;
        }
    } catch (error) {
        document.getElementById("myloader").style.display = "none";
        // console.error("Error:", error);
    }
}

async function processStepOnePhotos(payId = null) {
    payId = atob(payId);

    document.getElementById("myloader").style.display = "flex";
    let response = await fetch("/api/kyc-docs", {
        method: "POST",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json;charset=utf-8",
        },
        body: JSON.stringify({ photos: dataStepOne, payId: payId }),
    });

    let result = await response.json();

    document.getElementById("myloader").style.display = "none";
    if (result.success) {
        // NEW CHANGE BLOCK
        const stepOne = (document.getElementById(
            "step-one-analog"
        ).style.display = "none");
        const stepTwo = (document.getElementById("step-two").style.display =
            "flex");

        // const photosarea = (document.getElementById(
        //     "photosarea"
        // ).style.display = "none");
        // const form = (document.getElementById("profile-kyc-frm").style.display =
        //     "block");

        dataUser = {
            tid: result.tid,
            names: result.Text.Names,
            surname: result.Text.Surname,
            dob: result.Text.DOB,
        };
        // document.getElementById("firstnamedata").value = result.Text.Names;
        // document.getElementById("lastnamedata").value = result.Text.Surname;
        // document.getElementById("dobdata").value = result.Text.DOB;
        // document.getElementById("tid").value = result.tid;
    } else {
        document.getElementById("error-message-one").style.display = "block";
        document.getElementById("error-message-one").innerText = result.message;
    }
}

async function processStepTwoPhotos(payId = null) {
    payId = atob(payId);

    document.getElementById("myloader").style.display = "flex";

    if (activeType === "analog") {
        let response = await fetch("/api/kyc-face", {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json;charset=utf-8",
            },
            body: JSON.stringify({
                tid: dataUser.tid,
                photos: [dataStepOne[0], dataStepTwo],
                payId: payId
            }),
        });

        let result = await response.json();

        document.getElementById("myloader").style.display = "none";
        if (result.success) {
            // NEW CHANGE BLOCK
            const stepTwo = (document.getElementById("step-two").style.display =
                "none");
            const form = (document.getElementById("form").style.display =
                "flex");
            document.getElementById("back-icon").style.display = "none";

            // const photosarea = (document.getElementById(
            //     "photosarea"
            // ).style.display = "none");
            // const form = (document.getElementById("profile-kyc-frm").style.display =
            //     "block");

            document.getElementById("firstnamedata").value = dataUser.names;
            document.getElementById("lastnamedata").value = dataUser.surname;
            document.getElementById("dobdata").value = dataUser.dob;
            document.getElementById("tid").value = dataUser.tid;
        } else {
            document.getElementById("error-message-two").style.display =
                "block";
            document.getElementById("error-message-two").innerText =
                result.message;
        }
    } else if (activeType === "digital") {
        const formData = new FormData();
        formData.append("image", digitalSelfie);
        formData.append("tid", dataUser.tid);

        let response = await fetch("/api/digital/selfie", {
            method: "POST",
            body: formData,
        });

        let result = await response.json();
        document.getElementById("myloader").style.display = "none";
        if (result.success) {
            // NEW CHANGE BLOCK
            const stepTwo = (document.getElementById("step-two").style.display =
                "none");
            const form = (document.getElementById("form").style.display =
                "flex");

            document.getElementById("edit-box").style.display = "none";
            document
                .getElementById("firstnamedata")
                .removeAttribute("disabled");
            document
                .getElementById("middlenamedata")
                .removeAttribute("disabled");
            document.getElementById("lastnamedata").removeAttribute("disabled");
            document.getElementById("dobdata").removeAttribute("disabled");

            document.getElementById("tid").value = dataUser.tid;
        } else {
            document.getElementById("error-message-two").style.display =
                "block";
            document.getElementById("error-message-two").innerText =
                result.message;
        }
    }
}

async function cancelDisable(event) {
    event.preventDefault();
    document.getElementById("edit-box").style.display = "none";
    document.getElementById("firstnamedata").removeAttribute("disabled");
    document.getElementById("middlenamedata").removeAttribute("disabled");
    document.getElementById("lastnamedata").removeAttribute("disabled");
    document.getElementById("dobdata").removeAttribute("disabled");

    const tid = document.getElementById("tid").value;

    let response = await fetch(`/api/edited/${tid}`, {
        method: "POST",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json;charset=utf-8",
        },
        // body: JSON.stringify({ photos: photos }),
    });

    // let result = await response.json();
}

let digitalPhoto = null;
let digitalSelfie = null;
let dataStepOne = [];
let dataStepTwo = null;
let dataUser = {};
