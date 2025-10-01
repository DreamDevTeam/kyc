{
    const phoneInputField = document.querySelector("#mobiledata");

    // phoneInputField.onkeypress = function (e) {
    //     return "1234567890+- ".indexOf(Str.fromCharCode(e.which)) >= 0;
    // }

    function getIp(callback) {
        fetch("https://ipinfo.io/json?token=9b88a33d6b7c75", {
            headers: { Accept: "application/json" },
        })
            .then((resp) => resp.json())
            .catch(() => {
                return {
                    country: "us",
                };
            })
            .then((resp) => callback(resp.country));
    }

    // const options = {
    //     separateDialCode: true,
    //     initialCountry: "auto",
    //     geoIpLookup: getIp,
    //     utilsScript:
    //         "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    // }
    //
    // if(phoneInputField){
    //     window.intlTelInput(phoneInputField,options)
    // }

    $("#start-btn").click(() => {
        console.log("test", activeType);
        if (activeType === "analog") {
            $("#start").css("display", "none");
            $("#step-one-analog").css("display", "flex");
        } else if (activeType === "digital") {
            $("#start").css("display", "none");
            $("#step-one-digital").css("display", "flex");
        }
        $("#back-icon").css("display", "flex");
    });

    // $("#step-one-btn").click(() => {
    //     $("#step-one").css("display", "none");
    //     $("#step-two").css("display", "flex");
    // });

    // $("#step-two-btn").click(() => {
    //     $("#step-two").css("display", "none");
    //     $("#card").css("max-width", "512px");
    //     $("#form").css("display", "flex");
    // });

    $("#btn-close-dialog").click(() => {
        $("#dialog").css("display", "none");
    });

    $("#btn-info").click(() => {
        $("#dialog").css("display", "flex");
    });
}
