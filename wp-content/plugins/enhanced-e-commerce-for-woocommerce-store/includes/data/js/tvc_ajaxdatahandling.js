"use strict";
var myAjaxNonces;

function tvc_getFeedList( callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-list-of-feeds',
			postFeedsListNonce: myAjaxNonces.postFeedsListNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_getBackupsList( callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-list-of-backups',
			postBackupListNonce: myAjaxNonces.postBackupListNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_getSettingsOptions( callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-settings-options',
			postSetupOptionsNonce: myAjaxNonces.postSetupOptionsNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Reads and returns all possible output fields from the selected merchant
 *
 * @param {int} feedId
 * @param {int} channelId
 * @param callback
 * @returns list with output fields
 */
function tvc_getOutputFields( feedId, channelId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-output-fields',
			feedId: feedId,
			channelId: channelId,
			outputFieldsNonce: myAjaxNonces.outputFieldsNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Reads and returns all possible source fields from the selected source
 *
 * @param {int} sourceId
 * @param callback
 * @returns list with input fields
 */
function tvc_getSourceFields( sourceId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-input-fields',
			sourceId: sourceId,
			inputFieldsNonce: myAjaxNonces.inputFieldsNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Get main feed filters
 * @param feedId
 * @param callback
 */
function tvc_getMainFeedFilters( feedId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-main-feed-filters',
			feedId: feedId,
			inputFeedFiltersNonce: myAjaxNonces.inputFeedFiltersNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Fetch next category
 * @param channelId
 * @param requestedLevel
 * @param parentCategory
 * @param language
 * @param callback
 */
function tvc_getNextCategories( channelId, requestedLevel, parentCategory, language, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-next-categories',
			channelId: channelId,
			requestedLevel: requestedLevel,
			parentCategory: parentCategory,
			fileLanguage: language,
			nextCategoryNonce: myAjaxNonces.nextCategoryNonce,

		},
		function( response ) {

			response = response.trim();

			if ( response.substr( response.length - 1 ) === '0' ) {
				response = response.substring( 0, response.length - 1 );
			}

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Get Category Listing from String
 * @param channelId
 * @param mainCategoriesString
 * @param language
 * @param callback
 */
function tvc_getCategoryListsFromString( channelId, mainCategoriesString, language, callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-category-lists',
			channelId: channelId,
			mainCategories: mainCategoriesString,
			fileLanguage: language,
			categoryListsNonce: myAjaxNonces.categoryListsNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Ajax Call for update feed to database
 * @param feedData
 * @param metaData
 * @param feedFilter
 * @param callback
 */
function tvc_updateFeedToDb( feedData, metaData, feedFilter, callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-update-feed-data',
			feed: JSON.stringify( feedData ),
			feedFilter: feedFilter ? feedFilter[ 0 ][ 'meta_value' ] : '',
			metaData: JSON.stringify( metaData ),
			updateFeedDataNonce: myAjaxNonces.updateFeedDataNonce,
		},
		function( response ) {
		console.log(response);
			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Ajax call for update XML feed file
 * @param feed_id
 * @param callback
 */
function tvc_updateFeedFile( feed_id, callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-update-feed-file',
			dataType: 'text',
			feedId: feed_id,
			updateFeedFileNonce: myAjaxNonces.updateFeedFileNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Update current/selected feed status
 * @param feedId
 * @param callback
 */
function tvc_getCurrentFeedStatus( feedId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-feed-status',
			sourceId: feedId,
			feedStatusNonce: myAjaxNonces.feedStatusNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_getFeedData( feedId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-feed-data',
			sourceId: feedId,
			feedDataNonce: myAjaxNonces.feedDataNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Switch feed status auto/manual
 * @param feedId
 * @param callback
 */
function tvc_switchFeedStatus( feedId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-switch-feed-status',
			feedId: feedId,
			switchFeedStatusNonce: myAjaxNonces.switchFeedStatusNonce,

		},
		function( response ) {

			tvc_switchStatusAction( feedId, response );
			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Duplicate/Clone existing feed
 * @param feedId
 * @param callback
 */
function tvc_duplicateExistingFeed( feedId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-duplicate-existing-feed',
			feedId: feedId,
			duplicateFeedNonce: myAjaxNonces.duplicateFeedNonce,

		},
		function( response ) {

			if ( response.trim() ) {
				tvc_resetFeedList();
			}

			callback( tvc_validateResponse( response ) );
		}
	);
}

/**
 * Log message on server
 * @param message
 * @param fileName
 * @param callback
 */
function tvc_logMessageOnServer( message, fileName, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-log-message',
			messageList: message,
			fileName: fileName,
			logMessageNonce: myAjaxNonces.logMessageNonce,

		},
		function( result ) {

			callback( result.trim() );
		}
	);
}

function tvc_auto_feed_fix_mode( selection, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-auto-feed-fix-mode-selection',
			fix_selection: selection,
			updateAutoFeedFixNonce: myAjaxNonces.setAutoFeedFixNonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

/**
 * Ajax for background process of selected feeds
 * @param selection
 * @param callback
 */
function tvc_background_processing_mode( selection, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-background-processing-mode-selection',
			mode_selection: selection,
			backgroundModeNonce: myAjaxNonces.setBackgroundModeNonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

function tvc_feed_logger_status( selection, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-feed-logger-status-selection',
			statusSelection: selection,
			feedLoggerStatusNonce: myAjaxNonces.setFeedLoggerStatusNonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

/**
 * Sets the Show Product Identifiers option.
 *
 * @since 2.10.0.
 *
 * @param selection
 * @param callback
 */
function tvc_show_pi_status( selection, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-show-product-identifiers-selection',
			showPiSelection: selection,
			showPINonce: myAjaxNonces.setShowPINonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

function tvc_change_third_party_attribute_keywords( keywords, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-third-party-attribute-keywords',
			keywords: keywords,
			thirdPartyKeywordsNonce: myAjaxNonces.setThirdPartyKeywordsNonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

function tvc_change_notice_mailaddress( mailAddress, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-set-notice-mailaddress',
			mailaddress: mailAddress,
			noticeMailaddressNonce: myAjaxNonces.setNoticeMailaddressNonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

function tvc_change_background_processing_time_limit( limit, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-background-processing-time-limit',
			limit: limit,
			batchProcessingLimitNonce: myAjaxNonces.setBatchProcessingLimitNonce,

		},
		function( response ) {

			callback( response.trim() );
		}
	);
}

function tvc_clear_feed_process_data( callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-clear-feed-process-data',
			clearFeedNonce: myAjaxNonces.setClearFeedProcessNonce,

		},
		function( response ) {

			callback( response );
		}
	);
}

function tvc_reinitiate_plugin( callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-reinitiate-plugin',
			reInitiateNonce: myAjaxNonces.setReInitiateNonce,

		},
		function( response ) {

			callback( response );
		}
	);
}

/**
 * Takes the response of an ajax call and checks if it's ok. When not, it will display the error and return
 * an empty list.
 *
 * @param {String} response
 *
 * @returns {String}
 */
function tvc_validateResponse( response ) {

	response = response.trim(); // remove php ajax response white spaces

	// when the response contains no error message
	if ( response.indexOf( '<div id=\'error\'>' ) < 0 && response.indexOf( '<b>Fatal error</b>' ) < 0 && response.indexOf( '<b>Notice</b>' ) < 0 && response.indexOf( '<b>Warning</b>' ) < 0 && response.indexOf( '<b>Catchable fatal error</b>' ) < 0 && response.indexOf( '<div id="error">' ) < 0 ) {

		if ( response.indexOf( '[]' ) < 0 ) {

			if ( response !== '' ) {

				return (
					response
				);
			} else {

				return (
					'1'
				);
			}
		} else { // if it has an error message

			// return an empty list
			return (
				'0'
			);
		}
	} else {

		tvc_show_error_message( response.replace( '[]', '' ) );
		tvc_hideFeedSpinner();

		tvc_logMessageOnServer(
			response,
			'error',
			function( result ) {

				// return an empty list
				return (
					'0'
				);
			}
		);
	}
}

/**
 * Deletes a specific feed file
 *
 * This function first removes the file from the server and than from the feed database.
 * After that it will refresh the Feed List.
 *
 * @param {int} id
 * @param {string} feedTitle
 * @returns nothing
 */
function tvc_deleteFeed( id, feedTitle ) {
	var feedSpinnerElement     = jQuery( '#feed-spinner' );
	var feedListMessageElement = jQuery( '#feed-list-message' );

	// clear old messages
	feedListMessageElement.empty();

	// remove the file
	tvc_removeFeedFile(
		function() {
			feedSpinnerElement.show();

			// delete the file entry in the database
			tvc_deleteFeedFromDb(
				id,
				function( response ) {
					feedSpinnerElement.show();

					response = response.trim();

					if ( response === '1' ) {
						// reset the feed list
						tvc_resetFeedList();
						feedSpinnerElement.hide();
					} else {
						// report the result to the user
						feedListMessageElement.append( response );
						feedSpinnerElement.hide();
					}
				},
				id
			);
		},
		feedTitle
	);
}

function tvc_removeFeedFile( callback, feedTitle ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-delete-feed-file',
			fileTitle: feedTitle,
			deleteFeedNonce: myAjaxNonces.deleteFeedNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_deleteFeedFromDb( feedId, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-delete-feed',
			feedId: feedId,
			deleteFeedNonce: myAjaxNonces.deleteFeedNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_checkNextFeedInQueue( callback ) {
	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-get-next-feed-in-queue',
			nextFeedInQueueNonce: myAjaxNonces.nextFeedInQueueNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_initiateBackup( fileName, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-backup-current-data',
			fileName: fileName,
			backupNonce: myAjaxNonces.backupNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_deleteBackup( fileName, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-delete-backup-file',
			fileName: fileName,
			deleteBackupNonce: myAjaxNonces.deleteBackupNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_restoreBackup( fileName, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-restore-backup-file',
			fileName: fileName,
			restoreBackupNonce: myAjaxNonces.restoreBackupNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}

function tvc_duplicateBackup( fileName, callback ) {

	jQuery.post(
		myAjaxNonces.ajaxurl,
		{
			action: 'tvcajax-duplicate-backup-file',
			fileName: fileName,
			duplicateBackupNonce: myAjaxNonces.duplicateBackupNonce,

		},
		function( response ) {

			callback( tvc_validateResponse( response ) );
		}
	);
}