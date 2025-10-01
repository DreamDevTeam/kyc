let intltel = null;

jQuery(document).ready(function () {
    $("#verify-btn").attr("disabled", true);

    $('#firstnamedata').bind('input', function(){
        if ($("#firstnamedata").val() != null && $("#firstnamedata").val() != "") {
            $("#firstnamelabel").css("cssText", "color: #001737 !important");
        }else {
            $("#firstnamelabel").css("cssText", "color: red !important");
        }
    });
    $('#lastnamedata').bind('input', function(){
        if ($("#lastnamedata").val() != null && $("#lastnamedata").val() != "") {
            $("#lastnamelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#lastnamelabel").css("cssText", "color: red !important");
        }
    });
    $('#mobiledata').bind('input', function(){
        if ($("#mobiledata").val() != "") {
            $("#mobilelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#mobilelabel").css("cssText", "color: red !important");
        }
    });
    $('#emaildata').bind('input', function(){
        if ($("#emaildata").val() != "" && $("#emaildata").val().match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/) != null) {
            $("#emaillabel").css("cssText", "color: #001737 !important");
            $("#emaildata-error").text("")
        } else {
            $("#emaillabel").css("cssText", "color: red !important");
            $("#emaildata-error").text("Email is invalid")
        }
    });
    $('#genderdata').bind('input', function(){
        if ($("#genderdata").val() != null && $("#genderdata").val() != "") {
            $("#genderlabel").css("cssText", "color: #001737 !important");
        } else {
            $("#genderlabel").css("cssText", "color: red !important");
        }
    });
    // $('#streetdata').bind('input', function(){
    //     if ($("#streetdata").val() != null && $("#streetdata").val() != "") {
    //         $("#streetlabel").css("cssText", "color: #001737 !important");
    //     } else {
    //         $("#streetlabel").css("cssText", "color: red !important");
    //     }
    // });
    $('#citydata').bind('input', function(){
        if ($("#citydata").val() != null && $("#citydata").val() != "") {
            $("#citylabel").css("cssText", "color: #001737 !important");
        } else {
            $("#citylabel").css("cssText", "color: red !important");
        }
    });
    $('#statedata').bind('input', function(){
        if ($("#statedata").val() != null && $("#statedata").val() != "") {
            $("#statelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#statelabel").css("cssText", "color: red !important");
        }
    });
    $('#dobdata').bind('input', function(){
        if ($("#dobdata").val() != null && $("#dobdata").val() != "") {
            $("#doblabel").css("cssText", "color: #001737 !important");
        } else {
            $("#doblabel").css("cssText", "color: red !important");
        }
    });
    $('#postcodedata').bind('input', function(){
        if ($("#postcodedata").val() != null && $("#postcodedata").val() != "") {
            $("#postcodelabel").css("cssText", "color: #001737 !important");
        } else {
            $("#postcodelabel").css("cssText", "color: red !important");
        }
    });
    $('#addressdata').bind('input', function(){
        if ($("#addressdata").val() != null && $("#addressdata").val() != "") {
            $("#addresslabel").css("cssText", "color: #001737 !important");
        } else {
            $("#addresslabel").css("cssText", "color: red !important");
        }
    });



    setTimeout(async () => {
        intltel = null;
        const input = document.querySelector("#mobiledata");
        if (input && intltel == null) {
            const options = {
                dropdownContainer: document.body,
                separateDialCode: true,
                initialCountry: "auto",
                geoIpLookup: getIp,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            };
            intltel = window.intlTelInput(input, options);
        }
    }, 200);


    $("#profile-kyc-frm").change(function (){
        if (
            $("#firstnamedata").val() !== "" &&
            $("#lastnamedata").val() !== "" &&
            $("#mobiledata").val() !== "" &&
            $("#emaildata").val() !== "" &&
            $("#genderdata").val() !== "" &&
            $("#dobdata").val() !== "" &&
            $("#citydata").val() !== "" &&
            $("#statedata").val() !== "" &&
            $("#postcodedata").val() !== "" &&
            $("#addressdata").val() !== ""
        ){
            $("#verify-btn").attr("disabled", false);
        }else{
            $("#verify-btn").attr("disabled", true);
        }
    })

    $("#verify-btn").click(function (e){
        e.preventDefault();
        let form_data = new FormData();
        let postcode = $("#postcodedata").val();

        form_data.append("mobile_prefix", $("#mobiledata").val() !== "" ? `+${intltel.getSelectedCountryData().dialCode}` : "");
        form_data.append("country", $("#mobiledata").val() !== "" ? `${intltel.getSelectedCountryData().iso2.toUpperCase()}` : "");
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
        form_data.append("city", $("#citydata").val());
        form_data.append("state", $("#statedata").val());
        form_data.append("address", $("#addressdata").val());
        form_data.append("postcode", postcode.toString().padStart(4, "0"));
        form_data.append("require_id", false);
        form_data.append("ref", $("#ref").val());
        form_data.append("tid", $("#tid").val());

        const urlParams = new URLSearchParams(window.location.search);
        form_data.append("key", urlParams.get('key'))
        // console.log(form_data)
        postKYCdata(form_data);
    })

    function postKYCdata(form_data) {

        $("#loader").show();
        $("#loader-text").show();
        $("#verify-btn").hide();

        $.ajax({
            // url: "http://127.0.0.1:8138/add",
            url: window.location.origin +"/add",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function success(response) {
                window.location = response.route
            },
            error: function error(jqXHR, textStatus, response) {
                Swal.fire("Verification Failure!", jqXHR.responseJSON.message, "error").then(function () {
                    $("#loader").hide();
                    $("#loader-text").hide();
                    $("#verify-btn").show();
                });
            },
        });
    }

})
