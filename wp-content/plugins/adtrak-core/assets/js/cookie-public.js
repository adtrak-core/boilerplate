// notification plugin script

//simple create, read, erase cookie

function createCookie(name,value,days) {
    var domainRoot = window.location.hostname;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+";'domain='+domainRoot ;path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

//handle with jquery

(function($) {
    "use strict";

    $(function() {

    //start script

    if (readCookie('PrivacyPolicy') != 'closed'){
        createCookie('PrivacyPolicy', 'init', 7);
          $('#wp-notification').addClass('open');
    }

    $('#wp-notification-toggle').click(function(){
        notificationToggle();
    });

    function notificationToggle (){
        createCookie('PrivacyPolicy', 'closed', 7)
        $('#wp-notification').fadeOut(500);
    }

    //end script

    });
}(jQuery));
