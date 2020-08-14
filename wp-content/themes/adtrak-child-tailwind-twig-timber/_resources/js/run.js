// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
(function($) {
    "use strict";

	// add all of your code within here, not above or below
	$(function() {

		var iconAngleUp = "<svg class='icon icon-angle-up'><use xlink:href='"+themeURL.themeURL+"/_resources/images/icons-sprite.svg#icon-angle-up'></use></svg>";
		var iconAngleDown = "<svg class='icon icon-angle-down'><use xlink:href='"+themeURL.themeURL+"/_resources/images/icons-sprite.svg#icon-angle-down'></use></svg>";

		// --------------------------------------------------------------------------------------------------
		// Toggle Location Numbers
		// --------------------------------------------------------------------------------------------------
		$('.js-toggle-location-numbers').click(function(){
			$('.location-numbers').toggleClass('hidden');
		});


		// --------------------------------------------------------------------------------------------------
		// Mobile Menu
		// --------------------------------------------------------------------------------------------------
		// Copy primary and secondary menus to .mob-nav element
		var mobNav = document.querySelector('.mob-nav .scroll-container');

		var copyPrimaryMenu = document.querySelector('#menu-primary .menu-primary').cloneNode(true);
		mobNav.appendChild(copyPrimaryMenu);

		if($('#menu-secondary').length) {
			var copySecondaryMenu = document.querySelector('#menu-secondary .menu-secondary').cloneNode(true);
			mobNav.appendChild(copySecondaryMenu);
		}

		// Add Close Icon element
		$( "<div class='mob-nav-close'><svg class='icon icon-times'><use xlink:href='"+themeURL.themeURL+"/_resources/images/icons-sprite.svg#icon-times'></use></svg></div>" ).insertAfter( ".mob-nav .scroll-container" );

		// Add dropdown arrow to links with sub-menus
        $( "<span class='sub-arrow'>"+iconAngleDown+iconAngleUp+"</span>" ).insertAfter( ".mob-nav .menu-item-has-children > a" );
        $(".sub-arrow .icon-angle-down").addClass('active');

	    // Show sub-menu when dropdown arrow is clicked
	    $('.sub-arrow').click(function() {
	    	$(this).toggleClass('active');
	    	$(this).prev('a').toggleClass('active');
	    	$(this).next('.sub-menu').slideToggle();
	    	$(this).children().toggleClass('active');
	    });

	    // Add underlay element after mobile nav
	    $( "<div class='mob-nav-underlay'></div>" ).insertAfter( ".mob-nav" );

	    // Show underlay and fix the body scroll when menu button is clicked
	    $('.menu-btn').click(function() {
	    	$('.mob-nav,.mob-nav-underlay').addClass('mob-nav--active');
	    	$('body').addClass('fixed');
	    });

	    // Hide menu when close icon or underlay is clicked
	    $('.mob-nav-underlay,.mob-nav-close').click(function() {
	    	$('.mob-nav,.mob-nav-underlay').removeClass('mob-nav--active');
	    	$('body').removeClass('fixed');
        });
        

        // --------------------------------------------------------------------------------------------------
		// Add icon to menu items with children
		// --------------------------------------------------------------------------------------------------
		if(window.innerWidth >= 1000) {
			// Primary Menu
			$(".menu > ul > .menu-item-has-children > a").append(iconAngleDown);
		}
		function addDropdownIcon() {
			if(window.innerWidth < 1000) {
				$('.menu > li > a > .icon').remove();
			}
		}
        window.addEventListener('resize', addDropdownIcon);


		// --------------------------------------------------------------------------------------------------
		// Contact form 7 tracking
		// ** To use this replace the FORMID with the id of your contact form and then replace the FORM NAME with the form name.
		// ** Use as many if statement as required to cover off your forms.
		// --------------------------------------------------------------------------------------------------
		document.addEventListener('wpcf7mailsent', function (event) {
			if ('FORMID' == event.detail.contactFormId) {
				// ga('send', 'event', 'Contact Form', 'Submit', 'Contact Form A');
				gtag('event', 'Course Form Submission', { 'event-category': 'FORM NAME Submission', 'event_label': 'FORM NAME Submission ' + window.location });
			}
			else if ('FORMID' == event.detail.contactFormId) {
				// ga('send', 'event', 'Contact Form', 'Submit', 'Contact Form B');
				gtag('event', 'Contact Form Submission', { 'event-category': 'FORM NAME Submission', 'event_label': 'FORM NAME Submission' });
			}
		}, false);

	});

}(jQuery));
