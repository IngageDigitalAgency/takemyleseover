function cleanDatePicker() {
	if ($.datepicker != null) {
		var old_fn = $.datepicker._updateDatepicker;
		$.datepicker._updateDatepicker = function(inst) {
			old_fn.call(this, inst);
			var buttonPane = $(this).datepicker("widget").find(".ui-datepicker-buttonpane");
			$("<button type='button' class='ui-datepicker-clean ui-state-default ui-priority-primary ui-corner-all'>Clear</button>").appendTo(buttonPane).click(function(ev) {
				$.datepicker._clearDate(inst.input);
			});
		}
	}
}
removeDependants();

/*	custom */
resetChosen = function() {
	$(".chosen-container").css("width","100%");
}
globalResize = function() {
	$("select").chosen();
	resetChosen();

	$("#home-testimonials .slides li").css("height","auto");
	balanceItems($("#home-testimonials .slides li"));

	$("footer .col-xs-8").css("height","auto");
	balanceItems($("footer .col-xs-8"));
	$('#home-sponsors .slides li').css("height","auto");
	balanceItems($('#home-sponsors .slides li'))
	$('#topnav > .row > div').css("height","auto");
	balanceItems($('#topnav > .row > div'))

	$('#home-sponsors .flexslider').flexslider({
		autoPlay:true,
		animation:"fade",
		controlNav:false
	});
}
$(window).load(function() {
	globalResize();
	if (window.self != window.top)
		if (window.top.setIframe != null) window.top.setIframe();
});
$(window).resize(function () {
	globalResize();
});