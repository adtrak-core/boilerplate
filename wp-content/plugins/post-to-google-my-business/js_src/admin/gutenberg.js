/**
 * Temporary fix to refresh the metabox when publishing a post through gutenberg
 *
 * @property {string} ajaxurl URL for ajax request set by WordPress
 *
 * Translations
 * @property {Array} mbp_localize_gutenberg[] Array containing translations
 */
import "jquery";

import {dispatch, select, subscribe} from "@wordpress/data";

import AdminNotice from "./components/AdminNotice";

(function($) {
// temporary hack for semi-Gutenberg compatibility
    function gutenbergMetaboxRefresh() {
        // if (!wp.data || !wp.data.hasOwnProperty('subscribe')) {
        //     return;
        // }
        subscribe(function () {

            let isSavingPost = select('core/editor').isSavingPost();
            let isAutosavingPost = select('core/editor').isAutosavingPost();
            let isAlreadyUpdating = false

            if (isSavingPost && !isAutosavingPost) {
                if(AutoPostCheckBoxValue() && !isAlreadyUpdating) {
                    isAlreadyUpdating = true;
                    const data = {
                        'action': 'mbp_get_post_rows',
                        'mbp_post_nonce': mbp_localize_gutenberg.post_nonce,
                        'mbp_post_id': mbp_localize_gutenberg.post_id
                    };
                    setTimeout(function () {
                        $.post(ajaxurl, data, function (response) {
                            if (response.error) {
                                AdminNotice(response.error, 'notice-error')
                                return;
                            }
                            if (response.success) {
                                $(".mbp-existing-posts tbody").html(response.data.rows);
                                updateGMBAutoPostCheckBox(false);
                            }
                        });
                        isAlreadyUpdating = false;
                    }, 3000); //Some ugly delay to make sure the post was created before reloading the list
                }

            }
        });
    }

    gutenbergMetaboxRefresh();

})(jQuery);



import { __ } from '@wordpress/i18n';

import { registerPlugin } from "@wordpress/plugins";
import { PluginPostStatusInfo, PluginPrePublishPanel } from "@wordpress/edit-post";
import { ToggleControl } from "@wordpress/components";
import { withSelect, withDispatch } from "@wordpress/data";
import { useEffect } from "@wordpress/element";
import icons from "./icons"

const checkedByDefault = mbp_localize_gutenberg.checked_by_default;

let defaultLoaded = false;
const unsubscribe = subscribe(() => {
    let isCleanNewPost = select('core/editor').isCleanNewPost();
    if(isCleanNewPost && checkedByDefault && !defaultLoaded){
        defaultLoaded = true;
        updateGMBAutoPostCheckBox(true);
        unsubscribe();
    }
});

let AutoPostCheckBoxValue = () => {
    return select('core/editor').getEditedPostAttribute('meta')['_mbp_gutenberg_autopost'];
}


let updateGMBAutoPostCheckBox = (value) => {
    dispatch('core/editor').editPost({meta: {_mbp_gutenberg_autopost: value}})
}

let PluginPostStatusInfoTest = (props) => {

    return(
        <PluginPostStatusInfo>
            <ToggleControl
                label={__("Auto-post to GMB", "post-to-google-my-business")}
                checked={ props.gmb_checkbox }
                onChange={(value) => props.onMetaFieldChange(value)}
            />
        </PluginPostStatusInfo>
    )
}

PluginPostStatusInfoTest = withSelect(
    (select) => {
        return {
            gmb_checkbox: AutoPostCheckBoxValue()
        }
    }
)(PluginPostStatusInfoTest);

PluginPostStatusInfoTest = withDispatch(
    (dispatch) => {
        return {
            onMetaFieldChange: updateGMBAutoPostCheckBox
        }
    }
)(PluginPostStatusInfoTest)


registerPlugin( 'post-to-gmb-checkbox', { render: PluginPostStatusInfoTest } );


let PrePublishChecks = (props) => {
    return (
        <PluginPrePublishPanel title={__('Post to GMB', 'post-to-google-my-business')} initialOpen='true'>
            <ToggleControl
                label={__("Auto-post to GMB", "post-to-google-my-business")}
                checked={ props.gmb_checkbox }
                onChange={(value) => props.onMetaFieldChange(value)}
            />
        </PluginPrePublishPanel>
    );
}

PrePublishChecks = withSelect(
    (select) => {
        return {
            gmb_checkbox: AutoPostCheckBoxValue()
        }
    }
)(PrePublishChecks);

PrePublishChecks = withDispatch(
    (dispatch) => {
        return {
            onMetaFieldChange: updateGMBAutoPostCheckBox
        }
    }
)(PrePublishChecks)

registerPlugin('pgmb-pre-publish', { render: PrePublishChecks, icon: icons.pgmb });

// --------------------------------------------

// The array/object key that will be sent with the REST request
const key = 'isGutenbergPost';

let PluginIsGutenbergPost = ( { setIsGutenbergPost, isDirty } ) => {
    useEffect( () => {
        setIsGutenbergPost();
    }, [ isDirty ] );
    return (
        <PluginPostStatusInfo>
            { null }
        </PluginPostStatusInfo>
    );
};


PluginIsGutenbergPost = withSelect( ( select ) => {
        return {
            isDirty: select( 'core/editor' ).isEditedPostDirty(),
        };
    } )(PluginIsGutenbergPost);

PluginIsGutenbergPost = withDispatch( ( dispatch, _, { select } ) => {
        return {
            setIsGutenbergPost: () => {
                const isDirty = select( 'core/editor' ).isEditedPostDirty();
                const isGBPost = select( 'core/editor' ).getEditedPostAttribute( key ) || false;
                if ( ! isGBPost && isDirty ) {
                    dispatch( 'core/editor' ).editPost( { [ key ]: true }, { undoIgnore: true } );
                }
            },
        };
    } )(PluginIsGutenbergPost);

registerPlugin( 'is-gutenberg-post', { render: PluginIsGutenbergPost } );
