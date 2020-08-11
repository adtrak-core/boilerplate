// binds $ to jquery, requires you to write strict code. Will fail validation if it doesn't match requirements.
(function($) {
    "use strict";

	// add all of your code within here, not above or below
	$(function() {


        var iconAngleUp = "<svg class='icon icon-angle-up'><use xlink:href='"+themeURL+"/images/icons-sprite.svg#icon-angle-up'></use></svg>";
        var iconAngleDown = "<svg class='icon icon-angle-down'><use xlink:href='"+themeURL+"/images/icons-sprite.svg#icon-angle-down'></use></svg>";

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
			$('.location-numbers').toggleClass('hidden');
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
		$( "<div class='mob-nav-close'><svg class='icon icon-times'><use xlink:href='"+themeURL+"/images/icons-sprite.svg#icon-times'></use></svg></div>" ).insertAfter( ".mob-nav .scroll-container" );

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
	    	$('body').addClass('overflow-hidden');
	    });

	    // Hide menu when close icon or underlay is clicked
	    $('.mob-nav-underlay,.mob-nav-close').click(function() {
	    	$('.mob-nav,.mob-nav-underlay').removeClass('mob-nav--active');
	    	$('body').removeClass('overflow-hidden');
        });
        

        // --------------------------------------------------------------------------------------------------
		// Add icon to menu items with children
		// --------------------------------------------------------------------------------------------------
		if(window.innerWidth >= 1000) {
			// Primary Menu
			$(".desktop-nav > ul > .menu-item-has-children > a").append(iconAngleDown);
		}
		function addDropdownIcon() {
			if(window.innerWidth < 1000) {
				$('.menu-primary > li > a > .icon').remove();
			}
		}
        window.addEventListener('resize', addDropdownIcon);


        // --------------------------------------------------------------------------------------------------
		// Ninja Forms event tracking | https://www.chrisains.com/seo/tracking-ninja-form-submissions-with-google-analytics-jquery/
		// --------------------------------------------------------------------------------------------------
        jQuery( document ).on( 'nfFormReady', function() {
        	nfRadio.channel('forms').on('submit:response', function(form) {
                gtag('event', 'conversion', {'event_category': form.data.settings.title,'event_action': 'Send Form','event_label': 'Successful '+form.data.settings.title+' Enquiry'});
        		console.log(form.data.settings.title + ' successfully submitted');
        	});
        });

	});

}(jQuery));
