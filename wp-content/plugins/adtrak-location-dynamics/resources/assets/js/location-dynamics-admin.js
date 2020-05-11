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
		val = val.replace("'", "");
		$(this).val(val);
		this.setSelectionRange(start, end);
	});

	$(document).on("click", ".delete-row",  function(e) {
		$(this).parent().parent().remove();
		e.preventDefault();
	});

	$('input[name=default-tracking]').keyup(function(e) {
		if ($(this).val() == '') {
			$('.tracking-label').html('Click Phone Number UK Homepage');
		} else {
			$('.tracking-label').html($(this).val() + ' UK Homepage');
		}
	});
});