// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
(function($) {
    "use strict";

	// add all of your code within here, not above or below
	$(function() {

		// MMenu (clones for mobile)

		$("#primary-navigation").mmenu(
			{
			"offCanvas": {
				position: "right"
				}
			}, {
				clone: true
			}
		);

		// Back to top
		$("#back-top").hide();
		$(function () {
			$(window).scroll(function () {
				if ($(this).scrollTop() > 300) {
					$('#back-top').fadeIn();
				} else {
					$('#back-top').fadeOut();
				}
			});
		});
		$("#back-top").click(function() {
			$("html, body").animate({
			scrollTop: $("header").offset().top
			}, 750);
		});

		
		// Toggle location numbers
		$('.js-toggle-location-numbers').click(function(){
			$('.mobile-top-bar__location-numbers').toggleClass('mobile-top-bar--visible');
		});

	});

}(jQuery));