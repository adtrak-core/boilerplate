// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
(function($) {
    "use strict";

	// add all of your code within here, not above or below
	$(function() {

		// MMenu (clones for mobile)

		$("#navigation").mmenu(
			{
			"offCanvas": {
				position: "right"
				}
			}, {
				clone: true
			}
		);

		// Moves the non-cloned top-menu div to the top bar

		$("#menu-secondary-menu").prependTo(".top-bar .container");

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

		// Defer loading of iframes and images

		function init() {
		    var imgDefer = document.querySelectorAll('iframe, img');
		    for (var i=0; i<imgDefer.length; i++) {
		        if(imgDefer[i].getAttribute('data-src')) {
		            imgDefer[i].setAttribute('src',imgDefer[i].getAttribute('data-src'));
		            imgDefer[i].setAttribute('style','opacity:1;');
		        }
		    }
		}
		window.onload = init;

	});

}(jQuery));