// notification plugin script

//simple create, read, erase cookie

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function eraseCookie(name) {
    setCookie(name, "", -1);
}

//handle with jquery

(function ($) {
    "use strict";

    $(function () {

        if (getCookie('PrivacyPolicy') !== 'closed') {
            setCookie('PrivacyPolicy', 'init', 7);
            $('#wp-notification').addClass('open');
        }

        $('#wp-notification-toggle').click(function () {
            notificationToggle();
        });

        function notificationToggle() {
            setCookie('PrivacyPolicy', 'closed', 7)
            $('#wp-notification').fadeOut(500);
        }

        //end script

    });
}(jQuery));
