/* ------------------------------
        SIDEBAR ACTIVE-DEACTIVE
--------------------------------- */
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
});

/* ------------------------------
   JQUERY VALIDATION LANGUAGE CHANGE
--------------------------------- */
jQuery.extend(jQuery.validator.messages, {
    required: "Ez a mező kötelező.",
    remote: "Kérem, javítsa ezt a mezőt.",
    email: "Valós e-mail címet adjon meg.",
    url: "Valós URL címet adjon meg.",
    date: "Érvényes dátum formátumot adjon meg.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Érvényes számot adjon meg.",
    digits: "Csak számjegyeket adjon meg.",
    creditcard: "Érvényes hitelkártya számot adjon meg.",
    equalTo: "Ugyanazt az értéket adja meg.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Maximum megadható hossz {0}."),
    minlength: jQuery.validator.format("Minimum megadható hossz {0}."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Kérem {0} -nél kisebb vagy azonos értéket adjon meg"),
    min: jQuery.validator.format("Kérem {0} -nél nagyobb vagy azonos értéket adjon meg.")
});

/* ------------------------------
   JQUERY VALIDATION NEW EMAIL METHOD
--------------------------------- */
$.validator.addMethod("validate_email", function (value, element) {

    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Valós e-mail címet adjon meg.");


$.validator.addMethod("noSpace", function (value, element) {
    return value.indexOf(" ") < 0 && value != "";
}, "Szóköz nem engedélyezett ennél a mezőnél");


/* ------------------------------
   JQUERY VALIDATION DEFAULT SETTINGS
--------------------------------- */
$.validator.setDefaults({
    rules: {
        username: {
            required: true,
            noSpace: true,
            minlength: 4,
            maxlength: 30
        },
        password: {
            required: true,
            minlength: 6
        },
        passwordre: {
            required: true,
            equalTo: "#password",
        },
        email: {
            required: true,
            validate_email: true,
            maxlength: 40
        },
        name: {
            required: true,
            minlength: 3,
            maxlength: 30
        },
        office: {
            maxlength: 40

        },
        exp_day: {
            required: true,
            maxlength: 4
        },
        DocumentsSelect: {
            required: true
        },
        phone_number: {
            minlength: 10,
            maxlength: 14
        },
        address: {
            required: true,
            minlength: 10,
            maxlength: 40
        },
        PositionSelect: {
            required: true
        },

    }
});

/* ------------------------------
   JQUERY VALIDATION - login-form
--------------------------------- */
$(document).ready(function () {
    $("#login-form").validate();
});

$(document).ready(function () {
    $("#newUser-form").validate();
})

$(document).ready(function () {
    $("#updateuser-form").validate();
})

$(document).ready(function () {
    $("#newPosition-form").validate();
})

$(document).ready(function () {
    $("#updateposition-form").validate();
})

$(document).ready(function () {
    $("#newDocument-form").validate();
})

$(document).ready(function () {
    $("#newEmployee-form").validate();
})

$(document).ready(function () {
    $("#updateemployee-form").validate();
})
