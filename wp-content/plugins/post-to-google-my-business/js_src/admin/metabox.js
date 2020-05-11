/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 *
 * @property {string} mbp_localize_script.post_nonce Post nonce
 * @property {string} mbp_localize_script.post_id ID of the current WordPress post
 *
 * Translations
 * @property {Array} mbp_localize_script[] Array containing translations
 */

import * as $ from "jquery";


import PostEditor from "./components/PostEditor";
import AdminNotice from "./components/AdminNotice";

const AJAX_CALLBACK_PREFIX = mbp_localize_script.AJAX_CALLBACK_PREFIX;
const POST_EDITOR_DEFAULT_FIELDS = mbp_localize_script.POST_EDITOR_DEFAULT_FIELDS;

let postEditor = new PostEditor(true, AJAX_CALLBACK_PREFIX, POST_EDITOR_DEFAULT_FIELDS);


let postFormContainer = $(".mbp-post-form-container");
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



/**
 * Resets the post editing form to its defaults
 *
 * @property {string} mbp_localize_script.publish_button "Publish"
 */
function reset_form(){
	formDataMode = formDataModes.createPost;
	formControlButtons.publishPostButton.html(mbp_localize_script.publish_button);
	formControlButtons.draftPostButton.show();


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
 * Load the auto-post template into the editor
 *
 * Translations
 * @property {string} mbp_localize_script.save_template "Save template"
 */
function load_autopost_template(){
	postEditor.resetForm();
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
			AdminNotice(response.error, 'notice-error');
			return;
		}
		if(response.success){
			if(response.data.fields){
				postEditor.loadFormFields(response.data.fields);
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
			AdminNotice(response.error, 'notice-error');
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
	postEditor.resetForm();
	formControlButtons.publishPostButton.html(mbp_localize_script.publish_button);
	if(edit){
		editing = post_id;
		formDataMode = formDataModes.editPost;
	}else{
		formDataMode = formDataModes.createPost;
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
			AdminNotice(response.error, 'notice-error');
			return;
		}

		if(response.success){
			postEditor.loadFormFields(response.post.form_fields);
			formControlButtons.draftPostButton.show();
			if(editing && response.post.post_status === 'publish'){
				formControlButtons.publishPostButton.html(mbp_localize_script.update_button);
				formControlButtons.draftPostButton.hide();
			}
			if(response.has_error){
				AdminNotice(response.has_error, 'notice-error');
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
	const data = {
		'action': 'mbp_delete_post',
		'mbp_post_id': post_id,
		'mbp_post_nonce': mbp_localize_script.post_nonce
	};
	$.post(ajaxurl, data, function(response) {
		if(response.success){
			return true;
		}else{
			AdminNotice(response.data.error, 'notice-error');
			return false;
		}
	});

}


formControlButtons.newPostButton.click(function(event) {
	event.preventDefault();
	formControlButtons.publishPostButton.html(mbp_localize_script.publish_button);
	postFormContainer.slideUp("slow");
	editing = false;
	postEditor.resetForm();
	postEditor.loadDefaultFormFields();
	postFormContainer.slideDown("slow");
	formControlButtons.draftPostButton.show();
	formDataMode = formDataModes.createPost;
});

formControlButtons.editTemplateButton.click(function(event) {
	event.preventDefault();
	load_autopost_template();
});



/**
 * Reset the form if the user presses the cancel button
 */
formControlButtons.cancelPostButton.click(function(event){
	event.preventDefault();
	postFormContainer.slideUp("slow");
	//reset_form();
});


/**
 * Inform the user if they're still working on a GMB post, and it hasn't been saved yet
 *
 * @property mbp_localize_script.publish_confirmation "You're working on a Google My Business post, but it has not yet been published/scheduled. Press OK to publish/schedule it now, or Cancel to save it as a draft."
 */
$('#publish, #original_publish').click(function(event) {

	//refresh the selector
	postFormContainer = $(postFormContainer.selector);
	if(postFormContainer.not(":visible")) {
		//alert("not visble");
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

	event.preventDefault();


	const publishButton = this;
	$(publishButton).html(mbp_localize_script.please_wait).attr('disabled', true);


	let draft = false;
	if(this.id === 'mbp-draft-post'){
		draft = true;
	}


	let mbp_fields_data = {
		'action': 'mbp_new_post',
		'mbp_serialized_fieldset': $('fieldset#mbp-post-data').serialize(),
		'mbp_post_id': mbp_localize_script.post_id,
		'mbp_post_nonce': mbp_localize_script.post_nonce,
		'mbp_editing': editing,
		'mbp_draft': draft,
		'mbp_data_mode': formDataMode
	};

	$.post(ajaxurl, mbp_fields_data, function(response) {
		if(response.success === false){
			AdminNotice(response.data.error,  'notice-error');
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
	event.preventDefault();
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
				//reset_form();
			}
			const post_tr = $(this).closest('tr');
			post_tr.hide('slow');
			post_tr.remove();
			if($(".mbp-post").length <= 0){
				$(".mbp-existing-posts .no-items").show();
			}
			break;
	}
});

export {AJAX_CALLBACK_PREFIX};
