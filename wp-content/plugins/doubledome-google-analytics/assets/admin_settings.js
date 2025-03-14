jQuery(document).ready(function() {
	jQuery('.default-hidden').hide();
	jQuery('h2').click(function(){
		jQuery(this).next().slideToggle(350);
		if(jQuery(this).hasClass("open")) {
			jQuery(this).removeClass("open");
			jQuery(this).addClass("close");
		}
		else {
			jQuery(this).removeClass("close");
			jQuery(this).addClass("open");
		}
	});
});