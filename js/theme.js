scrollToItem = function(el, optHt ) {
	if (optHt == null) optHt = 0;
	$('html, body').animate({
		scrollTop: $(el).offset().top + optHt
	}, 1000);
}
