import "jquery";

import { select, subscribe } from "@wordpress/data";

//import { show_error } from "./metabox";

function show_error(error){

    const error_notice = jQuery('.mbp-error-notice');
    error_notice.html(error);
    error_notice.show();
}

jQuery(document).ready(function($) {
// temporary hack for semi-Gutenberg compatibility
    function gutenbergMetaboxRefresh() {
        // if (!wp.data || !wp.data.hasOwnProperty('subscribe')) {
        //     return;
        // }
        subscribe(function () {
            let autoPostCheckbox = $('#mbp_create_post');
            let isSavingPost = select('core/editor').isSavingPost();
            let isAutosavingPost = select('core/editor').isAutosavingPost();

            if (isSavingPost && !isAutosavingPost && autoPostCheckbox.is(":checked")) {

                const data = {
                    'action': 'mbp_get_post_rows',
                    'mbp_post_nonce': mbp_localize_script.post_nonce,
                    'mbp_post_id': mbp_localize_script.post_id
                };
                setTimeout(function () {
                    $.post(ajaxurl, data, function (response) {
                        if (response.error) {
                            show_error(response.error);
                            return;
                        }
                        if (response.success) {
                            $(".mbp-existing-posts tbody").html(response.data.rows);
                            autoPostCheckbox.attr('checked', false);
                        }
                    });
                }, 2000); //Some ugly delay to make sure the post was created before reloading the list
            }
        });
    }

    gutenbergMetaboxRefresh();

});
