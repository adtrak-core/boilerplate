jQuery(function ($) {
	"use strict";
	$(document).ready(function() {
	});

	$(document).on("change paste keyup", ".location",  function() {
		var val = $(this).val();
		var start = this.selectionStart,
        end = this.selectionEnd;
		val = val.toLowerCase();
		val = val.replace(" ", "-");
		val = val.replace("_", "-");
		$(this).val(val);
		this.setSelectionRange(start, end);
	});

	$(document).on("click", ".delete-row",  function(e) {
		$(this).parent().parent().remove();
		e.preventDefault();
	});
});