let intltel = null;

function isValidState(data) {
    const states = ["VIC", "NSW", "QLD", "TAS", "WA", "SA", "NT", "ACT"];

    if (data) {
        if (states.includes(data)) {
            return true;
        }
    }
    return false;
}

jQuery(document).ready(function () {
    /// Check query string parameters
    var vars = [],
        hash;
    var decodeURL = decodeURIComponent(window.location.href);
    var hashes = decodeURL.slice(decodeURL.indexOf("?") + 1).split("&");
    for (var index = 0; index < hashes.length; index++) {
        hash = hashes[index].split("=");
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }

    var url = new URL(decodeURL);
    var skip_intro = url.searchParams.get("skip_intro");

    if (
        vars.email &&
        vars.email.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) ==
            null
    ) {
        Swal.fire("Email is invalid", "", "error").then((result) => {
            let qString = {};
            vars.forEach((v, k) => {
                if (v != "email") {
                    qString[v] = vars[v];
                }
            });
            let urlParams = new URLSearchParams(qString);
            location.href =
                urlParams.toString().length > 0
                    ? "?" + urlParams.toString()
                    : url.pathname;
        });
    }

    $(document).delegate("#start-btn", "click", async function () {
        $("#start").hide();
        $("#kyc-form").show();
        $("#loader").hide();
        if (vars["firstname"] !== undefined)
            $("#firstnamedata").val(String(vars["firstname"]));
        if (vars["lastname"] !== undefined)
            $("#lastnamedata").val(String(vars["lastname"]));
        if (vars["middlename"] !== undefined)
            $("#middlenamedata").val(String(vars["middlename"]));
        if (vars["mobile"] !== undefined)
            $("#mobiledata").val(String(vars["mobile"]));
        if (vars["email"] !== undefined)
            $("#emaildata").val(String(vars["email"]));
        if (vars["gender"] !== undefined)
            $("#genderdata").val(
                String(
                    vars["gender"].charAt(0).toUpperCase() +
                        vars["gender"].slice(1)
                )
            );
        if (vars["postcode"] !== undefined)
            $("#postcodedata").val(String(vars["postcode"]));
        if (vars["street"] !== undefined)
            $("#streetdata").val(String(vars["street"]));
        if (vars["state"] !== undefined)
            $("#statedata").val(String(vars["state"]));
        if (vars["city"] !== undefined)
            $("#citydata").val(String(vars["city"]));
        if (vars["dob"] !== undefined) $("#dobdata").val(String(vars["dob"]));
        if (vars["ref"] !== undefined) $("#ref").val(String(vars["ref"]));

        if (
            $("#firstnamedata").val() != null &&
            $("#firstnamedata").val() != ""
        ) {
            $("#firstnamelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#firstnamelabel").css("cssText", "color: red !important");
        }
        if (
            $("#lastnamedata").val() != null &&
            $("#lastnamedata").val() != ""
        ) {
            $("#lastnamelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#lastnamelabel").css("cssText", "color: red !important");
        }
        if ($("#mobiledata").val() != "") {
            $("#mobilelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#mobilelabel").css("cssText", "color: red !important");
        }
        if (
            $("#emaildata").val() != "" &&
            $("#emaildata")
                .val()
                .match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) != null
        ) {
            $("#emaillabel").css("cssText", "color: #001737 !important");
            $("#emaildata-error").text("");
        } else {
            $("#emaillabel").css("cssText", "color: red !important");
            $("#emaildata-error").text("Email is invalid");
        }
        if ($("#genderdata").val() != null && $("#genderdata").val() != "") {
            $("#genderlabel").css("cssText", "color: #001737 !important");
        } else {
            $("#genderlabel").css("cssText", "color: red !important");
        }
        if ($("#streetdata").val() != null && $("#streetdata").val() != "") {
            $("#streetlabel").css("cssText", "color: #001737 !important");
        } else {
            $("#streetlabel").css("cssText", "color: red !important");
        }
        if ($("#citydata").val() != null && $("#citydata").val() != "") {
            $("#citylabel").css("cssText", "color: #001737 !important");
        } else {
            $("#citylabel").css("cssText", "color: red !important");
        }
        if ($("#statedata").val() != null && $("#statedata").val() != "") {
            $("#statelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#statelabel").css("cssText", "color: red !important");
        }
        if ($("#dobdata").val() != null && $("#dobdata").val() != "") {
            $("#doblabel").css("cssText", "color: #001737 !important");
        } else {
            $("#doblabel").css("cssText", "color: red !important");
        }
        if (
            $("#postcodedata").val() != null &&
            $("#postcodedata").val() != ""
        ) {
            $("#postcodelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#postcodelabel").css("cssText", "color: red !important");
        }

        setTimeout(async () => {
            intltel = null;
            var input = document.querySelector("#mobiledata");
            if (input && intltel == null) {
                const options = {
                    dropdownContainer: document.body,
                    initialCountry: $("#mobiledata").data("countrycode"),
                    separateDialCode: true,
                    utilsScript: "/js/intlTelInput-utils.js",
                };
                intltel = window.intlTelInput(input, options);
            }
            await maskInput("#mobiledata");
        }, 200);
    });

    $("#mobiledata").on("countrychange", function (e, countryData) {
        $("#mobiledata").val("");
        var mask1 = $("#mobiledata").attr("placeholder").replace(/[0-9]/g, 0);
        $("#mobiledata").mask(mask1);
    });

    $(document).delegate("#redirect-btn", "click", function () {
        var customer_url = $(this).data("customer");
        window.location.replace(customer_url);
    });

    if (skip_intro == "true") {
        $("#start-btn").click();
    }
});

async function maskInput(inputid) {
    var placeholder = $(inputid).attr("placeholder");
    if (placeholder != undefined) {
        placeholder = $(inputid).attr("placeholder").replace(/[0-9]/g, 0);
        $("#mobiledata").mask(placeholder);
    } else {
        setTimeout(() => {
            maskInput(inputid);
        }, 200);
    }
}

function fileChange(obj) {
    let prnt = $(obj).parents(".logo-box-section");
    let filesize = Math.round(obj.files[0].size / 1024); //convert to KB size
    // console.log(filesize);
    if (filesize <= 5000) {
        //allow file less than 5000KB
        prnt.find(".img-thumbnail").prop(
            "src",
            window.URL.createObjectURL(obj.files[0])
        );
        prnt.find(".img-thumbnail").show();
        prnt.find("#upload-btn").text("Choose Another Photo");
    } else {
        Swal.fire("Maximum file size is 5Mb!", null, "error");
        $(obj).val("");
    }
}

function verifyingIdentifyModal(form_data) {
    Swal.fire({
        html: '<p style="font-size: 15px;">Verifying your identity can take some time.</p><p style="font-size: 15px;">Please allow up to 1 minute for the verification process is complete.</p><p style="font-size: 15px; font-weight: bold;">Please do not refresh this page while it is processing.</p>',
        showCancelButton: false,
        showConfirmButton: true,
        confirmButtonText: "Continue",
        closeOnConfirm: false,
    }).then((result) => {
        if (!result.value) return;

        postKYCdata(form_data);
    });
}

function postKYCdata(form_data) {
    $("#myloader").css("display", "flex");
    $("#verify-btn").hide();

    $.ajax({
        url: "/verify-kyc",
        type: "POST",
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        success: function success(response) {
            console.log("verify resp", response);
            if (response.status == "ok") {
                $("#myloader").css("display", "none");
                $("#kyc-form").hide();

                // var title_msg = "Your PayID has been created successfully";
                if (response.is_existing) {
                    var title_msg = "Your PayID already exist";

                    Swal.fire(
                        "Customer has already generated a PayID",
                        title_msg +
                            "<p>" +
                            response.payid +
                            "</p>You can now close this window.",
                        "success"
                    ).then(function () {
                        window.location.replace(response.customer);
                    });
                } else {
                    window.location.replace(response.customer);
                }
            } else {
                console.log(response);
                Swal.fire(
                    "Verification Failure!",
                    response.message,
                    "error"
                ).then(function () {
                    $("#myloader").css("display", "none");
                    $("#verify-btn").show();
                });
            }
        },
        error: function error(jqXHR, textStatus, response) {
            Swal.fire(
                "Verification Failure!",
                jqXHR.responseJSON.message,
                "error"
            ).then(function () {
                $("#myloader").css("display", "none");
                $("#verify-btn").show();
            });
        },
    });
}

$(function () {
    $("#verify-btn").attr("disabled", true);
    $(".datarequired + .label-active").css("cssText", "color: red !important");
    $(".datarequired").change(function () {
        if ($("#merchant-requireId").val()) {
            if (
                $("#mobiledata").val() != "" &&
                $("#emaildata").val() != "" &&
                $("#emaildata")
                    .val()
                    .match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) !=
                    null &&
                $("#genderdata").val() != "" &&
                $("#doctype").val() != "" &&
                $("#frontpicture").val() != "" &&
                $("#backpicture").val() != "" &&
                $("#selfiepicture").val() != ""
            ) {
                $("#mobilelabel").css("cssText", "color: #001737 !important");
                $("#emaillabel").css("cssText", "color: #001737 !important");
                $("#emaildata-error").text("Email is invalid");
                $("#genderlabel").css("cssText", "color: #001737 !important");
                $("#doctypelabel").css("cssText", "color: #001737 !important");

                $("#verify-btn").attr("disabled", false);
                $("#verify-btn").css("background-color", "black");
            } else {
                if ($("#mobiledata").val() != "") {
                    $("#mobilelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#mobilelabel").css("cssText", "color: red !important");
                }
                if (
                    $("#emaildata").val() != "" &&
                    $("#emaildata")
                        .val()
                        .match(
                            /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
                        ) != null
                ) {
                    $("#emaillabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                    $("#emaildata-error").text("");
                } else {
                    $("#emaillabel").css("cssText", "color: red !important");
                    $("#emaildata-error").text("Email is invalid");
                }
                if (
                    $("#genderdata").val() != null &&
                    $("#genderdata").val() != ""
                ) {
                    $("#genderlabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#genderlabel").css("cssText", "color: red !important");
                }
                if ($("#doctype").val() != null && $("#doctype").val() != "") {
                    $("#doctypelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#doctypelabel").css("cssText", "color: red !important");
                }
                $("#verify-btn").attr("disabled", true);
            }
        } else {
            if (
                $("#firstnamedata").val() != "" &&
                $("#lastnamedata").val() != "" &&
                $("#mobiledata").val() != "" &&
                $("#emaildata").val() != "" &&
                $("#emaildata")
                    .val()
                    .match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) !=
                    null &&
                $("#genderdata").val() != "" &&
                $("#dobdata").val() != "" &&
                $("#streetdata").val() != "" &&
                $("#citydata").val() != "" &&
                $("#statedata").val() != "" &&
                $("#postcodedata").val() != "" &&
                parseInt($("#postcodedata").val()) > 0 &&
                parseInt($("#postcodedata").val()) < 10000 &&
                isValidState($("#statedata").val())
            ) {
                $("#firstnamelabel").css(
                    "cssText",
                    "color: #001737 !important"
                );
                $("#lastnamelabel").css("cssText", "color: #001737 !important");
                $("#emaillabel").css("cssText", "color: #001737 !important");
                $("#emaildata-error").text("Email is invalid");
                $("#mobilelabel").css("cssText", "color: #001737 !important");
                $("#genderlabel").css("cssText", "color: #001737 !important");
                $("#doblabel").css("cssText", "color: #001737 !important");
                $("#streetlabel").css("cssText", "color: #001737 !important");
                $("#citylabel").css("cssText", "color: #001737 !important");
                $("#statelabel").css("cssText", "color: #001737 !important");
                $("#postcodelabel").css("cssText", "color: #001737 !important");

                $("#verify-btn").attr("disabled", false);
                $("#verify-btn").css("background-color", "black");
            } else {
                if (
                    $("#firstnamedata").val() != null &&
                    $("#firstnamedata").val() != ""
                ) {
                    $("#firstnamelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#firstnamelabel").css(
                        "cssText",
                        "color: red !important"
                    );
                }
                if (
                    $("#lastnamedata").val() != null &&
                    $("#lastnamedata").val() != ""
                ) {
                    $("#lastnamelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#lastnamelabel").css("cssText", "color: red !important");
                }
                console.log("#mobiledata", $("#mobiledata").val());
                if ($("#mobiledata").val() != "") {
                    $("#mobilelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#mobilelabel").css("cssText", "color: red !important");
                }
                if (
                    $("#emaildata").val() != "" &&
                    $("#emaildata")
                        .val()
                        .match(
                            /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
                        ) != null
                ) {
                    $("#emaillabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                    $("#emaildata-error").text("");
                } else {
                    $("#emaillabel").css("cssText", "color: red !important");
                    $("#emaildata-error").text("Email is invalid");
                }
                if (
                    $("#genderdata").val() != null &&
                    $("#genderdata").val() != ""
                ) {
                    $("#genderlabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#genderlabel").css("cssText", "color: red !important");
                }
                if (
                    $("#streetdata").val() != null &&
                    $("#streetdata").val() != ""
                ) {
                    $("#streetlabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#streetlabel").css("cssText", "color: red !important");
                }
                if (
                    $("#citydata").val() != null &&
                    $("#citydata").val() != ""
                ) {
                    $("#citylabel").css("cssText", "color: #001737 !important");
                } else {
                    $("#citylabel").css("cssText", "color: red !important");
                }
                if (
                    $("#statedata").val() != null &&
                    $("#statedata").val() != ""
                ) {
                    $("#statelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#statelabel").css("cssText", "color: red !important");
                }
                if ($("#dobdata").val() != null && $("#dobdata").val() != "") {
                    $("#doblabel").css("cssText", "color: #001737 !important");
                } else {
                    $("#doblabel").css("cssText", "color: red !important");
                }
                if (
                    $("#postcodedata").val() != null &&
                    $("#postcodedata").val() != "" &&
                    parseInt($("#postcodedata").val()) > 0 &&
                    parseInt($("#postcodedata").val()) < 10000
                ) {
                    $("#postcodelabel").css(
                        "cssText",
                        "color: #001737 !important"
                    );
                } else {
                    $("#postcodelabel").css("cssText", "color: red !important");
                }
                $("#verify-btn").attr("disabled", true);
            }
        }
    });

    $("#profile-kyc-frm").submit(function (e) {
        e.preventDefault();
        if ($("#merchant-requireId").val()) {
            if (
                $("#mobiledata").val() != "" &&
                $("#emaildata").val() != "" &&
                $("#emaildata")
                    .val()
                    .match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) !=
                    null &&
                $("#genderdata").val() != null &&
                $("#doctype").val() != null &&
                $("#frontpicture").val() != "" &&
                $("#backpicture").val() != "" &&
                $("#selfiepicture").val() != ""
            ) {
                var form_data = new FormData();
                form_data.append(
                    "mobile_prefix",
                    $("#mobiledata").val() !== ""
                        ? `+${intltel.getSelectedCountryData().dialCode}`
                        : ""
                );
                form_data.append(
                    "mobile",
                    $("#mobiledata")
                        .val()
                        .replace(/([- ])/g, "")
                );
                form_data.append("email", $("#emaildata").val());
                form_data.append("gender", $("#genderdata").val());
                form_data.append("doctype", $("#doctype").val());
                form_data.append("merchant", $("#merchant-hidden").val());
                form_data.append("require_id", true);
                form_data.append("ref", $("#ref").val());
                form_data.append("tid", $("#tid").val());

                var frontpicture = $("#frontpicture").prop("files")[0];
                var backpicture = $("#backpicture").prop("files")[0];
                var selfiepicture = $("#selfiepicture").prop("files")[0];
                form_data.append("frontpicture", frontpicture);
                form_data.append("backpicture", backpicture);
                form_data.append("selfiepicture", selfiepicture);

                verifyingIdentifyModal(form_data);
            }
        } else {
            $("#firstname-error").text("");
            $("#firstnamelabel").css("cssText", "color: #001737 !important");

            $("#middlename-error").text("");
            $("#middlenamelabel").css("cssText", "color: #001737 !important");

            $("#lastname-error").text("");
            $("#lastnamelabel").css("cssText", "color: #001737 !important");

            if (
                $("#firstnamedata")
                    .val()
                    .match(/^([^0-9]*)$/) == null ||
                $("#emaildata")
                    .val()
                    .match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) ==
                    null ||
                $("#middlenamedata")
                    .val()
                    .match(/^([^0-9]*)$/) == null ||
                $("#lastnamedata")
                    .val()
                    .match(/^([^0-9]*)$/) == null
            ) {
                if (
                    $("#firstnamedata")
                        .val()
                        .match(/^([^0-9]*)$/) == null
                ) {
                    $("#firstname-error").text(
                        "Firstname must be alphabetic only"
                    );
                    $("#firstnamelabel").css(
                        "cssText",
                        "color: red !important"
                    );
                }

                if (
                    $("#middlenamedata")
                        .val()
                        .match(/^([^0-9]*)$/) == null
                ) {
                    $("#middlename-error").text(
                        "Middlename must be alphabetic only"
                    );
                    $("#middlenamelabel").css(
                        "cssText",
                        "color: red !important"
                    );
                }

                if (
                    $("#lastnamedata")
                        .val()
                        .match(/^([^0-9]*)$/) == null
                ) {
                    $("#lastname-error").text(
                        "Lastname must be alphabetic only"
                    );
                    $("#lastnamelabel").css("cssText", "color: red !important");
                }

                if (
                    $("#emaildata")
                        .val()
                        .match(
                            /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
                        ) == null
                ) {
                    $("#emaildata-error").text("Email is invalid");
                    $("#emaildatalabel").css(
                        "cssText",
                        "color: red !important"
                    );
                }
            } else {
                if (
                    $("#mobiledata").val() != "" &&
                    $("#emaildata").val() != "" &&
                    $("#emaildata")
                        .val()
                        .match(
                            /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
                        ) != null &&
                    $("#genderdata").val() != null
                ) {
                    var form_data = new FormData();
                    var postcode = $("#postcodedata").val();

                    form_data.append(
                        "mobile_prefix",
                        $("#mobiledata").val() !== ""
                            ? `+${intltel.getSelectedCountryData().dialCode}`
                            : ""
                    );
                    form_data.append(
                        "mobile",
                        $("#mobiledata")
                            .val()
                            .replace(/([- ])/g, "")
                    );
                    form_data.append("firstname", $("#firstnamedata").val());
                    form_data.append("lastname", $("#lastnamedata").val());
                    form_data.append("middlename", $("#middlenamedata").val());
                    form_data.append("email", $("#emaildata").val());
                    form_data.append("dob", $("#dobdata").val());
                    form_data.append("gender", $("#genderdata").val());
                    form_data.append("merchant", $("#merchant-hidden").val());
                    form_data.append("street", $("#streetdata").val());
                    form_data.append("city", $("#citydata").val());
                    form_data.append("state", $("#statedata").val());
                    form_data.append(
                        "postcode",
                        postcode.toString().padStart(4, "0")
                    );
                    form_data.append("require_id", false);
                    form_data.append("ref", $("#ref").val());
                    form_data.append("tid", $("#tid").val());

                    postKYCdata(form_data);
                }
            }
        }
    });
});
