/*
 * Customizer Scripts
 * Need to rewrite and clean up this file.
 */

jQuery(document).ready(function() {

    /**
     * Change description
     */
    jQuery('#customize-info .customize-panel-description').html(kt_woomail.labels.description);
    jQuery('#customize-info .panel-title.site-title').html(kt_woomail.labels.customtitle);
    // Add reset button
    jQuery('#customize-header-actions input#save').after('<input type="submit" name="kt_woomail_reset" id="kt_woomail_reset" class="button button-secondary" value="' + kt_woomail.labels.reset + '" style="float: right; margin-left: 8px; margin-top: 0px;">');

    // Handle reset button click
    jQuery('#customize-header-actions #kt_woomail_reset').click(function(e) {

        // Prevent form submit
        e.preventDefault();

        // Display confirmation prompt
        var confirmation = confirm(kt_woomail.labels.reset_confirmation);

        // Check user input
        if ( ! confirmation ) {
            return;
        }

        // Disable reset button
        jQuery(this).prop('disabled', true);

        // Populate request data object
        var data = {
            wp_customize:   'on',
            action:         'kt_woomail_reset',
        };

        // Send request to server
        jQuery.post(kt_woomail.ajax_url, data, function() {
            wp.customize.state('saved').set(true);
            window.location.replace(kt_woomail.customizer_url);
        });
    });
    wp.customize.state('saved').bind( 'change', function() {
    	if( wp.customize.state( 'saved' ).get() ) {
    		jQuery('input[name=kadence-woomail-send-email]').prop('disabled', false);
    	} else {
    		jQuery('input[name=kadence-woomail-send-email]').prop('disabled', true);
    	}
    });

    // Handle send email button click
    jQuery('input[name=kadence-woomail-send-email]').click(function(e) {

        // Prevent form submit
        e.preventDefault();

        // Get recipients
        var recipients = jQuery('input#_customize-input-kt_woomail_email_recipient').val();
		// Display confirmation prompt
		var confirmation = confirm(kt_woomail.labels.send_confirmation);

		// Check user input
		if ( ! confirmation ) {
		    return;
		}

		// Disable send button
		jQuery(this).prop('disabled', true);

		// Populate request data object
		var data = {
			wp_customize:   'on',
			action:         'kt_woomail_send_email',
			recipients:     recipients,
		};
		// Send request to server
		jQuery.post(kt_woomail.ajax_url, data, function( result ) {
			 if ( result != 0 ) {
		    	alert( kt_woomail.labels.sent );
		    } else {
		    	alert( kt_woomail.labels.failed );
		    }
		    jQuery(this).prop('disabled', false);
		});
    });

     jQuery( '.image-radio-select label' ).on( 'click', function(e) {
    	var new_val = jQuery(this).attr('data-image-value');
    	jQuery('#kt-woomail-prebuilt-template').val(new_val);
    	jQuery('.image-radio-select label.ktactive').each( function () {
    		jQuery(this).removeClass("ktactive");
    	});
    	jQuery(this).addClass("ktactive");
    });

    // Handle mobile button click
    function custom_size_mobile() {
    	// get email width.
    	var email_width = parseInt( jQuery('#customize-control-kt_woomail_content_width .range-slider__range').val() );
    	var ratio = 380/email_width;
    	var framescale = 100/ratio;
    	var framescale = framescale/100;
    	jQuery('#customize-preview iframe').width(email_width+'px');
    	jQuery('#customize-preview iframe').css({
				'-webkit-transform' : 'scale(' + ratio + ')',
				'-moz-transform'    : 'scale(' + ratio + ')',
				'-ms-transform'     : 'scale(' + ratio + ')',
				'-o-transform'      : 'scale(' + ratio + ')',
				'transform'         : 'scale(' + ratio + ')'
		});
    }
	jQuery('#customize-footer-actions .preview-mobile').click(function(e) {
		if ( kt_woomail.responsive_mode ) {
			jQuery('#customize-preview iframe').width('100%');
			jQuery('#customize-preview iframe').css({
					'-webkit-transform' : 'scale(1)',
					'-moz-transform'    : 'scale(1)',
					'-ms-transform'     : 'scale(1)',
					'-o-transform'      : 'scale(1)',
					'transform'         : 'scale(1)'
			});
		} else {
			custom_size_mobile();
		}
	});

	jQuery('#customize-footer-actions .preview-desktop').click(function(e) {
		jQuery('#customize-preview iframe').width('100%');
		jQuery('#customize-preview iframe').css({
				'-webkit-transform' : 'scale(1)',
				'-moz-transform'    : 'scale(1)',
				'-ms-transform'     : 'scale(1)',
				'-o-transform'      : 'scale(1)',
				'transform'         : 'scale(1)'
		});
	});
	jQuery('#customize-footer-actions .preview-tablet').click(function(e) {
		jQuery('#customize-preview iframe').width('100%');
		jQuery('#customize-preview iframe').css({
				'-webkit-transform' : 'scale(1)',
				'-moz-transform'    : 'scale(1)',
				'-ms-transform'     : 'scale(1)',
				'-o-transform'      : 'scale(1)',
				'transform'         : 'scale(1)'
		});
	});

});

( function( $ ) {
	
	var KWMDIE = {
	
		init: function() {
			$( 'input[name=kt-woomail-export-button]' ).on( 'click', KWMDIE._export );
			$( 'input[name=kt-woomail-import-button]' ).on( 'click', KWMDIE._import );
		},
	
		_export: function() {
			window.location.href = KWMDIEConfig.customizerURL + '&kt-woomail-export=' + KWMDIEConfig.exportNonce;
		},
	
		_import: function() {

			// Display confirmation prompt
			var confirmation = confirm(KWMDIEl10n.confrim_override);

			// Check user input
			if ( ! confirmation ) {
				return;
			}

			var win			= $( window ),
				body		= $( 'body' ),
				form		= $( '<form class="kadence-woomail-form" method="POST" enctype="multipart/form-data"></form>' ),
				controls	= $( '.kadence-woomail-import-controls' ),
				file		= $( 'input[name=kadence-woomail-import-file]' ),
				message		= $( '.kadence-woomail-uploading' );
			
			if ( '' == file.val() ) {
				alert( KWMDIEl10n.emptyImport );
			} else {
				win.off( 'beforeunload' );
				body.append( form );
				form.append( controls );
				message.show();
				form.submit();
			}
		}
	};
	
	$( KWMDIE.init );
	
})( jQuery );

( function( $ ) {
	
	var KWMDTL = {
	
		init: function() {
			$( 'input[name=kadence-woomail-template-button]' ).on( 'click', KWMDTL._import_template );
		},
		_import_template: function() {

			// Display confirmation prompt
			var confirmation = confirm(KWMDIEl10n.confrim_override);

			// Check user input
			if ( ! confirmation ) {
				return;
			}
			var win			= $( window ),
				body		= $( 'body' ),
				form		= $( '<form class="kadence-woomail-template-form" method="POST" enctype="multipart/form-data"></form>' ),
				controls	= $( '.kt-template-woomail-load-controls' ),
				message		= $( '.kadence-woomail-loading' );
			
				win.off( 'beforeunload' );
				body.append( form );
				form.append( controls );
				message.show();
				form.submit();
		}
	};
	
	$( KWMDTL.init );
	
})( jQuery );
