jQuery(function ($) {
	"use strict";
	$(document).ready(function() {
		$(".ld-dropdown").hide();
		$(".ld-toggle").click(function(e) {
			$(this).next().slideToggle();
		});
	});
});