// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
(function($) {
    "use strict";

	// add all of your code within here, not above or below
	$(function() {

		// --------------------------------------------------------------------------------------------------
		// Toggle Location Numbers
		// --------------------------------------------------------------------------------------------------
		
		$('.js-toggle-location-numbers').click(function(){
			$('.location-numbers').toggleClass('hidden');
		});

	});

}(jQuery));
