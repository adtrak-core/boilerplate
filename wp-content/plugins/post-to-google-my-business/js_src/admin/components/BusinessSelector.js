/**
 * @property {string} ajaxurl URL for ajax request set by WordPress
 */

import * as $ from 'jquery';

/**
 * Class to make the business selector work
 *
 * @param container Parent container selector
 * @param {string} ajax_prefix Prefix for ajax calls made to WordPress
 * @constructor
 */
let BusinessSelector = function(container, ajax_prefix){

    let instance = this;
    let fieldContainer = $('.mbp-business-selector', container);
    let locationBlockedInfo = $('.mbp-location-blocked-info', container);
    let refreshApiCacheButton = $('.refresh-api-cache', container);
    let businessSelectorSelectedLocation = $('input:checked', fieldContainer);

    /**
     * Case insentive filter function for locations
     */
    $.extend($.expr[":"], {
        "containsi": function(elem, i, match, array) {
            return (elem.textContent || elem.innerText || "").toLowerCase()
                .indexOf((match[3] || "").toLowerCase()) >= 0;
        }
    });

    /**
     * Filter the location list and keep only items that match the text
     */
    $(".mbp-filter-locations", container).keyup(function(){
        let search = $(this).val();

        $( ".mbp-business-selector tr.mbp-business-item", container).hide()
        .filter(":containsi(" + search + ")")
        .show();
    });

    /**
     * Hook function to select all locations to the appropriate button
     */
    $(".mbp-select-all-locations", container).click(function(event){
        event.preventDefault();
        $(".mbp-checkbox-container input:checkbox:visible", container).prop("checked", true);
    });

    /**
     * Hook function to select no locations to its' button
     */
    $(".mbp-select-no-locations", container).click(function(event){
        event.preventDefault();
        $(".mbp-checkbox-container input:checkbox:visible", container).prop("checked", false);
    });

    /**
     * Hook function to toggle the selection of groups
     */
    $(".pgmb-toggle-group", container).click(function(event){
        event.preventDefault();

        let checkboxes = $(this).closest('tbody').find('.mbp-checkbox-container input:checkbox:visible');

        checkboxes.prop("checked", !checkboxes.prop("checked"));
    });

    /**
     * Checks if any of the businesses are not allowed to use the localPostAPI and show an informational message if one is
     */
    this.checkForDisabledLocations = function(){
        if($('input:disabled', fieldContainer).length){
            locationBlockedInfo.show();
            return;
        }
        locationBlockedInfo.hide();
    };
    this.checkForDisabledLocations();

    // this.scrollToSelectedLocation = function(){
    //     let selectedItem = $(".mbp-checkbox-container input[type='radio']:checked", container);
    //     console.log(selectedItem);
    //     fieldContainer.scrollTop(fieldContainer.scrollTop() + selectedItem.position().top
    //         - fieldContainer.height()/2 + selectedItem.height()/2);
    // }
    // this.scrollToSelectedLocation();

    /**
     * Refreshes the location listing
     *
     * @param {boolean} refresh When set to true - Forces a call to the Google API instead of relying on the local cache
     * @param {array} selected Array of selected locations
     */
    this.refreshBusinesses = function(refresh, selected){
        refresh = refresh || false;
        let data = {
            'action': ajax_prefix + '_refresh_locations',
            'refresh': refresh,
            'selected': selected
        };
        fieldContainer.empty();
        $.post(ajaxurl, data, function(response) {
            fieldContainer.replaceWith(response);
            //Refresh our reference to the field container
            fieldContainer = $('.mbp-business-selector', container);
            refreshApiCacheButton.html(mbp_localize_script.refresh_locations).attr('disabled', false);
            instance.checkForDisabledLocations();
        });
    };

    if(businessSelectorSelectedLocation.val() === '0'){
        this.refreshBusinesses(false);
    }

    /**
     * Obtain refreshed list of locations from the Google API
     */
    refreshApiCacheButton.click(function(event){
        let selectedBusinesses = [];

        $.each($('input:checked', fieldContainer), function(){
           selectedBusinesses.push($(this).val());
        });
        event.preventDefault();
        instance.refreshBusinesses(true, selectedBusinesses);
        refreshApiCacheButton.html(mbp_localize_script.please_wait).attr('disabled', true);
    });
};


export default BusinessSelector;
