/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 *
 * Translations
 * @property {Array} mbp_localize_script[] Array containing translations
 * @property {string} mbp_localize_script.refresh_locations "Refresh Locations"
 * @property {string} mbp_localize_script.please_wait "Please wait..."
 */

import * as $ from "jquery";
import PostEditor from "./components/PostEditor";
import BusinessSelector from "./components/BusinessSelector";

import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';

import { Calendar } from '@fullcalendar/core';
import timeGridPlugin  from '@fullcalendar/timegrid';

const BUSINESSSELECTOR_CALLBACK_PREFIX = mbp_localize_script.BUSINESSSELECTOR_CALLBACK_PREFIX;
const POST_EDITOR_CALLBACK_PREFIX = mbp_localize_script.POST_EDITOR_CALLBACK_PREFIX;
const FIELD_PREFIX = mbp_localize_script.FIELD_PREFIX;
const CALENDAR_TIMEZONE = mbp_localize_script.CALENDAR_TIMEZONE;

let postEditor = new PostEditor(false, POST_EDITOR_CALLBACK_PREFIX);
postEditor.setFieldPrefix(FIELD_PREFIX);



new BusinessSelector($('.mbp-google-settings-business-selector'), BUSINESSSELECTOR_CALLBACK_PREFIX);

postEditor.mediaUploader.setFieldName(FIELD_PREFIX);

let loadStaticItems = function(){
    let staticImage = $('.mbp-post-attachment');
    if(!staticImage.val()){ return; }
    postEditor.mediaUploader.loadItem($('.mbp-attachment-type').val(), staticImage.val(),staticImage.val());
};
loadStaticItems();

$(".pgmb-message .mbp-notice-dismiss").click(function(event){
    event.preventDefault();
    let theNotification = $(this).closest('.pgmb-message');

    let data = {
        'action': 'mbp_delete_notification',
        'identifier': theNotification.data('identifier'),
        'section': theNotification.data('section'),
        'ignore': $(this).data('ignore')
    };
    let notificationsContainer = $(this).closest('.pgmb-notifications-container');
    let notificationCounter = $('.mbp-notification-count', notificationsContainer);

    theNotification.fadeOut();

    let notificationCount = parseInt(notificationCounter.text()) - 1;

    notificationCounter.text(notificationCount);

    let isMainNotification = theNotification.hasClass("pgmb-notification");

    let pluginMenu = $('li.toplevel_page_post_to_google_my_business');
    if(isMainNotification){
        $('.update-count', pluginMenu).text(notificationCount);
    }

    if(notificationCount <= 0){
        if(isMainNotification) {
            $('.update-plugins', pluginMenu).remove();
        }
        notificationsContainer.fadeOut('slow');
    }
    $.post(ajaxurl, data, function(response){

    });
});




document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');

    let calendar = new Calendar(calendarEl, {
        plugins: [ timeGridPlugin  ],
        timeZone: CALENDAR_TIMEZONE,
        defaultView: 'timeGridWeek',
        allDaySlot: false,
        height: "auto",
        events: {
            url: ajaxurl,
            method: 'POST',
            extraParams: {
                action: 'mbp_get_timegrid_feed'
            }
        },
        eventRender: function (info) {
            let title = $(info.el).find('.fc-title');
            let topicDashicon;
            switch(info.event.extendedProps.topictype){
                case "STANDARD":
                    topicDashicon = 'dashicons-megaphone';
                    break;
                case "EVENT":
                    topicDashicon ='dashicons-calendar';
                    break;
                case "OFFER":
                    topicDashicon = 'dashicons-tag';
                    break;
                case "PRODUCT":
                    topicDashicon = 'dashicons-cart'
                    break;
                case "ALERT":
                    topicDashicon = 'dashicons-sos'
                    break;
            }
            $("<span class=\"dashicons " + topicDashicon + "\"></span> &nbsp;").prependTo(title);

            if (info.event.extendedProps.live && !info.event.extendedProps.hasError) {
                $("<span class=\"dashicons dashicons-admin-site\"></span> &nbsp;").prependTo(title);
            }

            if (info.event.extendedProps.hasError) {
                $("<span class=\"dashicons dashicons-warning\"></span> &nbsp;").prependTo(title);
            }

            if (info.event.extendedProps.repost) {
                $("<span class=\"dashicons dashicons-controls-repeat\"></span> &nbsp;").prependTo(title);
            }

        }
    });

    calendar.render();
});

export { postEditor, FIELD_PREFIX, POST_EDITOR_CALLBACK_PREFIX };
