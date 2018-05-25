var app = app || {};

// Utils
(function ($, app) {
    'use strict';

    app.utils = {};

    app.utils.formDataSupported = (function () {
        return !!('FormData' in window);
    }());

}(jQuery, app));

// Parsley validators
(function ($, app) {
    'use strict';

    window.Parsley
        .addValidator('filemaxmegabytes', {
            requirementType: 'string',
            validateString: function (value, requirement, parsleyInstance) {

                if (!app.utils.formDataSupported) {
                    return true;
                }

                var file = parsleyInstance.$element[0].files;
                var maxBytes = requirement * 1048576;

                if (file.length == 0) {
                    return true;
                }

                return file.length === 1 && file[0].size <= maxBytes;

            },
            messages: {
                en: 'File size exceeded'
            }
        })
        .addValidator('filemimetypes', {
            requirementType: 'string',
            validateString: function (value, requirement, parsleyInstance) {

                if (!app.utils.formDataSupported) {
                    return true;
                }

                var file = parsleyInstance.$element[0].files;

                if (file.length == 0) {
                    return true;
                }

                var allowedMimeTypes = requirement.replace(/\s/g, "").split(',');
                return allowedMimeTypes.indexOf(file[0].type) !== -1;

            },
            messages: {
                en: 'File type not allowed'
            }
        });

}(jQuery, app));


jQuery(function ($) {
	"use strict";
	$(document).ready(function() {
		var files = new FormData();

		$('.adtrakform').parsley();
        $(".datepicker").datepicker({ dateFormat: 'dd/mm/yy' });

		$('.adtrakform').submit(function(e) {
			e.preventDefault();
            var form_name = $(this).attr("name");
			var form = new FormData(this);
			var nonce = false;
			var id = $(this).attr("name");

            $(this).find("input[type=submit]").attr("disabled", "disabled");
            var subbutton = $(this).find("input[type=submit]").val();
            var button = $(this).find("input[type=submit]");
            $(this).find("input[type=submit]").val("Sending");

			$.ajax({
                type: 'post',
                async: true,
                url: CFajax.ajaxurl,
                processData: false,
                contentType: false,
		        dataType: 'json',
                data: form,
                success: function(result) {
                    if(result.type == "error") {
                        $("body").append("<div id='acpf-failure'>"+ result.message +"</div>");
                        button.val(subbutton);
                        button.attr("disabled", "enabled");
                    } else {
                        $("body").append("<div id='acpf-success'>"+ result.message +"</div>");
                        button.val("Sent");
                        if(result.old_analytics) {
                            ga('send', 'event', form_name, 'Success', 'Successful '+form_name+' Enquiry');
                        } else {
                            gtag('event', 'conversion', {'event_category': form_name,'event_action': 'Send Form','event_label': 'Successful '+form_name+' Enquiry'});
                        }
                    }
                },
                error: function(result) {
                    $("body").append("<div id='acpf-failure'>"+ result.message +"</div>");
                    $(this).find("input[type=submit]").val(subbutton);
                    $(this).find("input[type=submit]").attr("disabled", "enabled");
                }
            });


		});
		
	});
});

function capitalize(str) {
    strVal = '';
    str = str.split(' ');
    for (var chr = 0; chr < str.length; chr++) {
        strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
    }
    return strVal
}