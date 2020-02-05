/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 *
 * Translations
 * @property {Array} mbp_localize_script[] Array containing translations
 * @property {string} mbp_localize_script.refresh_locations "Refresh Locations"
 * @property {string} mbp_localize_script.please_wait "Please wait..."
 */

import "jquery";

jQuery(document).ready(function($) {
    let businessSelectorContainer = $('.mbp-business-selector');
    let refreshApiCacheButton = $('#refresh-api-cache');
    let businessSelectorSelectedLocation = $('input[name="mbp_google_settings[google_location]"]:checked');
    let locationBlockedInfo = $('.mbp-location-blocked-info');

    /**
     * Checks if any of the businesses are not allowed to use the localPostAPI and show an informational message if one is
     */
    function checkForDisabledLocations(){
        if($('input:disabled', businessSelectorContainer).length){
            locationBlockedInfo.show();
            return;
        }
        locationBlockedInfo.hide();
    }
    checkForDisabledLocations();

    /**
     * Refreshes the location listing
     *
     * @param {boolean} refresh When set to true - Forces a call to the Google API instead of relying on the local cache
     */
    function refreshBusinesses(refresh){
        refresh = refresh || false;
        let data = {
            'action': 'mbp_get_businesses',
            'refresh': refresh,
            'selected': businessSelectorSelectedLocation.val(),
        };
        businessSelectorContainer.empty();
        jQuery.post(ajaxurl, data, function(response) {
            businessSelectorContainer.replaceWith(response);
            refreshApiCacheButton.html(mbp_localize_script.refresh_locations).attr('disabled', false);
            checkForDisabledLocations();
        });
    }

    if(businessSelectorSelectedLocation.val() === '0'){
        refreshBusinesses(false);
    }

    /**
     * Obtain refreshed list of locations from the Google API
     */
    refreshApiCacheButton.click(function(){
        refreshBusinesses(true);
        refreshApiCacheButton.html(mbp_localize_script.please_wait).attr('disabled', true);
    });

});

/*
import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';

import { Calendar } from '@fullcalendar/core';
import timeGridPlugin  from '@fullcalendar/timegrid';

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new Calendar(calendarEl, {
        plugins: [ timeGridPlugin  ],
        defaultView: 'timeGridWeek'
    });

    calendar.render();
});
*/
