/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 *
 * @property {string} mbp_localize_script.post_nonce Post nonce
 * @property {string} mbp_localize_script.post_id ID of the current WordPress post
 *
 * Translations
 * @property {Array} mbp_localize_script[] Array containing translations
 */

import "jquery";


let media_uploader = null;

export { media_uploader };


export function show_error(error){

	const error_notice = jQuery('.mbp-error-notice');
	error_notice.html(error);
	error_notice.show();
}



jQuery(document).ready(function($) {

	const postFormContainer = $(".mbp-post-form-container");
	const postTextField = $('#post_text');
	const formControlButtons = {
        metaVideoButton:        $('#meta-video-button'),
        metaImageButton:        $('#meta-image-button'),
        publishPostButton:      $('#mbp-publish-post'),
        draftPostButton:        $('#mbp-draft-post'),
        newPostButton:          $('#mbp-new-post'),
        cancelPostButton:       $('#mbp-cancel-post'),
        editTemplateButton:     $('#mbp-edit-post-template')
    };

	const formDataModes = {
		createPost: 	"create_post",
		editPost: 		"edit_post",
		editTemplate: 	"edit_template",
		getPreview: 	"get_preview",
		saveDraft:		"save_draft"
	};


	let formDataMode = formDataModes.createPost;



	let editing = false;


	formControlButtons.metaImageButton.click(function() {
		media_uploader = wp.media({
			frame:    "post",
			state:    "insert",
			multiple: false
		});

		media_uploader.on("insert", function(){
			let json = media_uploader.state().get("selection").first().toJSON();
			console.log(json);
			let image_url = json.url;

			$('#meta-image').val(image_url);
			$('#meta-image-preview').attr('src',image_url);
			$('input[name="mbp_attachment_type"]').val('PHOTO');
		});

		media_uploader.open();
		return false;
	});

    formControlButtons.metaVideoButton.click(function() {
		tb_show("Post to Google My Business", "#TB_inline?width=600&height=300&inlineId=video-thickbox");
		return false;
	});




	function hide_error(){
		const error_notice = $('.mbp-error-notice');
		error_notice.hide();
	}

	/**
	 * Resets the post editing form to its defaults
	 *
	 * @property {string} mbp_localize_script.publish_button "Publish"
	 */
	function reset_form(){
	    formDataMode = formDataModes.createPost;
		const defaultTab = $('.mbp-tab-default');
		switch_tab(defaultTab);
		$('#meta-image-preview').attr('src','');
		formControlButtons.publishPostButton.html(mbp_localize_script.publish_button);
        formControlButtons.draftPostButton.show();
		$('input[name="mbp_existing_post"]').val('');
		$(':input','fieldset#mbp-post-data').not(':button, :submit, :reset, .mbp-hidden, :radio').removeAttr('checked').removeAttr('selected').not(':checkbox, :radio, select').val('');

		load_form_defaults();
		$(':input','fieldset#mbp-post-data').change();
	}

	function load_form_defaults(){
		$(':input','fieldset#mbp-post-data').each( function( index, element ){
			let defaultVal = $(element).data('default');

			if(!defaultVal){ return; }
			if($(element).is('select')) {
				$('[value="' + defaultVal + '"]', element).attr('selected', true);
			}else if($(element).is(':checkbox')){
				$(element).attr('checked', true);
			}else{
                $(element).val(defaultVal);
            }
		});
		$.event.trigger({
			type: "mbpLoadFormDefaults"
		});

	}
    /**
     * Repopulate the form fields from data object
     *
     * @param form_fields - object containing field names and values
     */
    function load_form_fields(form_fields){
        $.each(form_fields, function(name, value){
            let field = $('[name="' + name + '"], [name="' + name + '[]"]');

            if(field.is(':checkbox') || field.is(':radio')) {

				if ($.isArray(value)) {
					$.each(value, function (key, checkboxVal) {
						$('[name="' + name + '[]"][value="' + checkboxVal + '"]').attr('checked', true);
					});
				} else {
					$('[name="' + name + '"][value="' + value + '"]').attr('checked', true);
				}

            }else{
                field.val(value);
            }
            field.change();
        });
        const tab = $('a[data-topic="'+ form_fields.mbp_topic_type +'"]');
        switch_tab(tab);
    }

	/**
	 * Load the auto-post template into the editor
	 *
	 * Translations
	 * @property {string} mbp_localize_script.save_template "Save template"
	 */
	function load_autopost_template(){
		hide_error();
		reset_form();
		formDataMode = formDataModes.editTemplate;
		postFormContainer.slideUp("slow");
        formControlButtons.draftPostButton.hide();
        formControlButtons.publishPostButton.html(mbp_localize_script.save_template);
		const data = {
			'action': 'mbp_load_autopost_template',
			'mbp_post_nonce': mbp_localize_script.post_nonce,
            'mbp_post_id': mbp_localize_script.post_id
		};
		$.post(ajaxurl, data, function(response) {
			if (response.error) {
				show_error(response.error);
				return;
			}
			if(response.success){
			    if(response.data.template){
                    postTextField.val(response.data.template);
                }
			    if(response.data.fields){
                    load_form_fields(response.data.fields);
                }

				postFormContainer.slideDown("slow");

				$.event.trigger({
					type: "mbpLoadAutopostTemplate"
				});
			}
		});


	}

	function show_created_posts(post_id){
		const createdPostDialog = $("#mbp-created-post-dialog");
		const createdPostTable = $("#mbp-created-post-table");
		const data = {
			'action': 'mbp_get_created_posts',
			'mbp_post_id': post_id,
			'mbp_post_nonce': mbp_localize_script.post_nonce
		};

		$.post(ajaxurl, data, function(response) {
			if(response.error){
				show_error(response.error);
				return;
			}

			if(response.success){
				createdPostTable.html(response.data.table);
				tb_show("Created posts", "#TB_inline?width=600&height=300&inlineId=mbp-created-post-dialog");
				let ajaxContent = $('#TB_ajaxContent');
				ajaxContent.attr("style", "");
			}
		});

	}

	function load_post(post_id, edit){
		hide_error();
		reset_form();
		if(edit){
			editing = post_id;
		}else{
			editing = false;
		}


		postFormContainer.slideUp("slow");

		const data = {
			'action': 'mbp_load_post',
			'mbp_post_id': post_id,
			'mbp_post_nonce': mbp_localize_script.post_nonce
		};

		$.post(ajaxurl, data, function(response) {
			if(response.error){
				show_error(response.error);
				return;
			}

			if(response.success){
                load_form_fields(response.post.form_fields);
				if(editing && response.post.post_status === 'publish'){
                    formControlButtons.publishPostButton.html(mbp_localize_script.update_button);
                    formControlButtons.draftPostButton.hide();
				}
				if(response.has_error){
					show_error(response.has_error);
				}

				$.event.trigger({
					type: "mbpLoadPost",
					fields: response.post.form_fields
				});

				postFormContainer.slideDown("slow");
                postTextField.trigger("keyup");
			}
		});
	}

	function delete_post(post_id){
		hide_error();
		const data = {
			'action': 'mbp_delete_post',
			'mbp_post_id': post_id,
			'mbp_post_nonce': mbp_localize_script.post_nonce
		};
		$.post(ajaxurl, data, function(response) {
			if(response.success){
				return true;
			}else{
				show_error(response.data.error);
				return false;
			}
		});

	}



	function switch_tab(clicked){
		$('.nav-tab', postFormContainer).removeClass("nav-tab-active");
		$(clicked).addClass("nav-tab-active");
		$('.mbp-fields tr').not('.mbp-button-settings').hide(); //Spaghetti
		$('.mbp-fields tr.' + $(clicked).data('fields')).not('.mbp-button-settings').show();
		$('input[name="mbp_topic_type"]').val($(clicked).data("topic"));
	}

    /**
     * Reload the state of the advanced post settings dialog
     */
	if(localStorage.openAdvanced && JSON.parse(localStorage.openAdvanced) === true){
        const advanced_settings = $(".mbp-advanced-post-settings");
        advanced_settings.show();
	}

	formControlButtons.newPostButton.click(function(event) {
        event.preventDefault();
		editing = false;
		reset_form();
		postFormContainer.slideToggle("slow");
		formControlButtons.draftPostButton.show();


	});

	formControlButtons.editTemplateButton.click(function(event) {
        event.preventDefault();
		load_autopost_template();
	});

    /**
     * Open the advanced post settings
     */
	$('.mbp-toggle-advanced').click(function(event) {
        event.preventDefault();
		const advanced_settings = $(".mbp-advanced-post-settings");
		if(advanced_settings.is(":hidden")){
			localStorage.openAdvanced = JSON.stringify(true);
		}else{
            localStorage.openAdvanced = JSON.stringify(false);
		}
		advanced_settings.slideToggle("slow");
	});

    /**
     * Switch tabs
     */
	$('.nav-tab', postFormContainer).click(function(event) {
        event.preventDefault();
		switch_tab(this);
	});

    /**
     * Reset the form if the user presses the cancel button
     */
	formControlButtons.cancelPostButton.click(function(event){
        event.preventDefault();
		postFormContainer.slideUp("slow");
		reset_form();
	});


    /**
     * Inform the user if they're still working on a GMB post, and it hasn't been saved yet
     *
     * @property mbp_localize_script.publish_confirmation "You're working on a Google My Business post, but it has not yet been published/scheduled. Press OK to publish/schedule it now, or Cancel to save it as a draft."
     */
    $('#publish, #original_publish').click(function(event) {
        if(postFormContainer.not(":visible")) {
			return;
		}
		let publish = confirm(mbp_localize_script.publish_confirmation);
		if(publish){
			formControlButtons.publishPostButton.trigger("click");
			return;
		}
		formControlButtons.draftPostButton.trigger("click");
    });


	$('#mbp-publish-post, #mbp-draft-post').click(function(event){

		hide_error();
		event.preventDefault();


		const publishButton = this;
		$(publishButton).html(mbp_localize_script.please_wait).attr('disabled', true);


		let draft = false;
		if(this.id === 'mbp-draft-post'){
           	draft = true;
		}


		var mbp_fields_data = {
			'action': 'mbp_new_post',
			'mbp_form_fields': $('fieldset#mbp-post-data').serializeArray(),
			'mbp_post_id': mbp_localize_script.post_id,
			'mbp_post_nonce': mbp_localize_script.post_nonce,
			'mbp_editing': editing,
			'mbp_draft': draft,
            'mbp_data_mode': formDataMode
		};


		$.post(ajaxurl, mbp_fields_data, function(response) {
			if(response.success === false){
				show_error(response.data.error);
			}else if(response.success && !draft){
				postFormContainer.slideUp("slow");
			}

			if(formDataMode !== formDataModes.editTemplate){
                if(!editing){
                    $(".mbp-existing-posts tbody").prepend(response.data.row).show("slow");
                }else{
                    $(".mbp-existing-posts tbody tr[data-postid='" + editing + "']").replaceWith(response.data.row);
                }
                $(".mbp-existing-posts .no-items").hide();
                editing = response.data.id;
            }

			if(!draft){
				$(publishButton).html(mbp_localize_script.publish_button).attr('disabled', false);
			}else{
                $(publishButton).html(mbp_localize_script.draft_button).attr('disabled', false);
			}

		});



		return true;
	});

    /**
     * Hook functions for editing, duplicating or deleting existing posts
     */
	$('.mbp-existing-posts').on('click', 'a.mbp-action', function(event){
		const post_id = $(this).closest('tr').data('postid');
		const action = $(this).data('action');
		switch(action){
			case 'edit':
				load_post(post_id, true);
				break;
			case 'postlist':
				show_created_posts(post_id);
				break;
			case 'duplicate':
				load_post(post_id, false);
				break;

			case 'trash':
				delete_post(post_id);

				if(editing === post_id){
					postFormContainer.slideUp("slow");
                    reset_form();
				}
				const post_tr = $(this).closest('tr');
                post_tr.hide('slow');
                post_tr.remove();
				if($(".mbp-post").length <= 0){
                    $(".mbp-existing-posts .no-items").show();
				}
				break;
		}
		event.preventDefault();
	});


	let ButtonOptionsOpened = false;

    /**
     * Show/hide Call to Action settings when checking/unchecking the CTA checkbox
     */
	$('#mbp_button').change(function() {
		if(this.checked) {
			$(".mbp-button-settings").fadeIn("slow");
			ButtonOptionsOpened = true;
		}else{
			$(".mbp-button-settings").fadeOut("slow");
			ButtonOptionsOpened = false;
		}
	});

    /**
     * Hide the "alternative URL" field if the CTA is set to "CALL"
     */
	$('input[type=radio][name=mbp_button_type]').change(function() {

	    const alternativeURL = $(".mbp-button-url");
		if($("input[type=radio][name=mbp_button_type]:checked").val() === 'CALL'){
            alternativeURL.fadeOut("slow");
			return;
		}
		if(ButtonOptionsOpened){
			alternativeURL.fadeIn("slow");
		}

	});

    /**
     * Trigger change on the post text field when it is changed externally, to update the character counter
     */
    postTextField.change(function () {
        $(this).trigger("keyup");
    });

    /**
     * Update text and word counter for the text field
     */
    postTextField.keyup(function () {
    	let counter = $('.mbp-character-count');
        let count = $(this).val().length;
        let words = $(this).val().split(' ').length - 1;
        counter.text(count);
        if(count > 1500){
            counter.css('color', 'red');
		}else{
            counter.css('color', 'inherit');
		}
        $('.mbp-word-count').text(words);
    });


});
