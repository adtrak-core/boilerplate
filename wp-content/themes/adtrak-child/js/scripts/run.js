// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
(function($) {
    "use strict";

	// add all of your code within here, not above or below
	$(function() {


		// --------------------------------------------------------------------------------------------------
		// Back to top
		// --------------------------------------------------------------------------------------------------
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


		// --------------------------------------------------------------------------------------------------
		// Toggle Location Numbers
		// --------------------------------------------------------------------------------------------------
		$('.js-toggle-location-numbers').click(function(){
			$('.location-numbers').toggleClass('location-numbers-visible');
		});


		// --------------------------------------------------------------------------------------------------
		// Mobile Menu
		// --------------------------------------------------------------------------------------------------
		// Copy primary and secondary menus to .mob-nav element
		var mobNav = document.querySelector('.mob-nav .scroll-container');
		
		var copyPrimaryMenu = document.querySelector('#navigation .menu-primary').cloneNode(true);
		mobNav.appendChild(copyPrimaryMenu);

		if($('#menu-secondary-menu').length) {
			var copySecondaryMenu = document.querySelector('#menu-secondary-menu').cloneNode(true);
			mobNav.appendChild(copySecondaryMenu);
		}

		// Add Close Icon element
		$( "<div class='mob-nav-close'><i class='fa fa-times'></i></div>" ).insertAfter( ".mob-nav .scroll-container" );

		// Add dropdown arrow to links with sub-menus
	    $( "<span class='sub-arrow'><i class='fa fa-angle-down'></i></span>" ).insertAfter( ".mob-nav .menu-item-has-children > a" );

	    // Show sub-menu when dropdown arrow is clicked
	    $('.sub-arrow').click(function() {
	    	$(this).toggleClass('active');
	    	$(this).prev('a').toggleClass('active');
	    	$(this).next('.sub-menu').slideToggle();
	    	$(this).children().toggleClass('fa-angle-down').toggleClass('fa-angle-up');
	    });

	    // Add underlay element after mobile nav
	    $( "<div class='mob-nav-underlay'></div>" ).insertAfter( ".mob-nav" );

	    // Show underlay and fix the body scroll when menu button is clicked
	    $('.menu-btn').click(function() {
	    	$('.mob-nav,.mob-nav-underlay').addClass('active');
	    	$('body').addClass('fixed');
	    });

	    // Hide menu when close icon or underlay is clicked
	    $('.mob-nav-underlay,.mob-nav-close').click(function() {
	    	$('.mob-nav,.mob-nav-underlay').removeClass('active');
	    	$('body').removeClass('fixed');
	    });

	});

}(jQuery));
