"use strict";

jQuery(function ($) {
	$(document).ready(function() {

		new Clipboard('.copy-button');

		$(".apcf-control-panel__field-name").each(function() {
			var val = $(this).val();
			$(this).parent().parent().parent().parent().find(".apcf-control-panel__panel").find("span.name").html(val);
		});
		$(".apcf-control-panel__field-type").each(function() {
			var val = $(this).val();
			if(val == 'tel') {
				val = "Telephone";
			}
			$(this).parent().parent().parent().parent().find(".apcf-control-panel__panel").find("span.type").html(val);
			if($(this).val() == "Fieldset") {
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row").hide();
				$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").hide();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row").first().show();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(1)").show();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(5)").show();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(7)").show();
			}
			if($(this).val() == "Hidden") {
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row").hide();
				$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").hide();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row").first().show();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(1)").show();
			}
			if($(this).val() == "Email" || $(this).val() == "tel") {
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(8)").hide();
			}
			if($(this).val() == "Date") {
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(4)").hide();
				$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(8)").hide();
			}
		});
	});

	$(document).on("hover", ".apcf-control-panel__field", function(e) {
		if(e.type == "mouseenter") {
			$(this).find(".apcf-control-panel__panel-controls").css("display", "block");
		} else if (e.type == "mouseleave") {
			$(this).find(".apcf-control-panel__panel-controls").css("display", "none");
		}
	});
	
	$(document).on("change paste keyup", ".apcf-control-panel__field-name",  function() {
		var val = $(this).val();
		var start = this.selectionStart,
        end = this.selectionEnd;
		val = val.toLowerCase();
		val = val.replace(" ", "-");
		val = val.replace("_", "-");
		$(this).val(val);

		if(val == '') {
			val = "Name";
		}
		$(this).parent().parent().parent().parent().find(".apcf-control-panel__panel").find("span.name").html(val);
		this.setSelectionRange(start, end);
	});

	$(document).on("change", ".apcf-control-panel__field-type",  function() {
		var val = $(this).val();
		if(val == 'Please Select') {
			val = "Type";
		}
		if(val == 'tel') {
			val = "Telephone";
		}

		$(this).parent().parent().parent().find(".apcf-control-panel__field-row").show();
		$(this).parent().parent().parent().find(".apcf-control-panel__list-values").hide();
		$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").hide();

		$(this).parent().parent().parent().parent().find(".apcf-control-panel__panel").find("span.type").html(val);

		$(this).parent().parent().parent().find(".apcf-control-panel__list-values").slideUp();
		$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").slideUp();
		if($(this).val() == "List - Dropdown" || $(this).val() == "List - Checkbox" || $(this).val() == "List - Radio") {
			$(this).parent().parent().parent().find(".apcf-control-panel__list-values").slideToggle();
		}
		if($(this).val() == "File") {
			$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").slideToggle();
		}
		if($(this).val() == "Fieldset") {
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row").hide();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row").first().show();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(1)").show();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(5)").show();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(7)").show();
		}
		if($(this).val() == "Hidden") {
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row").hide();
			$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").hide();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row").first().show();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(1)").show();
		}
		if($(this).val() == "Email" || $(this).val() == "tel") {
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row").show();
			$(this).parent().parent().parent().find(".apcf-control-panel__list-values").hide();
			$(this).parent().parent().parent().find(".apcf-control-panel__file-limits").hide();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(8)").hide();
		}
		if($(this).val() == "Date") {
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(4)").hide();
			$(this).parent().parent().parent().find(".apcf-control-panel__field-row:eq(8)").hide();
		}
	});

	$(document).on("click", "li > .apcf-control-panel__panel", function(e) {
		$(this).parent().find(".apcf-control-panel__edit-fields").slideToggle();
		$(this).parent().toggleClass("apcf-control-panel__field_active");
		e.preventDefault();
	});

	$(document).on("click", ".delete-list-item", function(e) {
		$(this).parent().parent().parent().remove();
		e.preventDefault();
	});

	$(document).on("click", ".delete", function(e) {
		$(this).parent().parent().parent().parent().remove();
		$(this).parent().parent().parent().parent().toggleClass("active");
		e.preventDefault();
	});

	$(document).on("click", ".open-details", function(e) {
		$(this).parent().parent().parent().find(".apcf-control-panel__edit-fields").slideToggle();
		e.preventDefault();
	});
});