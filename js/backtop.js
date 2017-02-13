jQuery(document).ready(function() {
// BEGIN of script for fixed header and .btn-to-top
	var stickyNavTop = $('.main-menu').offset().top;
	var stickyNav = function(){
		var scrollTop = $(window).scrollTop();
		if (scrollTop > stickyNavTop) {
			$('.main-menu').addClass('fixed-header');
			$('.only').addClass('fixed-header-only');
			$('body').addClass('fixed-header-content-margin');
		} else {
			$('.main-menu').removeClass('fixed-header');
			$('.only').removeClass('fixed-header-only');
			$('body').removeClass('fixed-header-content-margin');
		}
	};
	stickyNav();
	$(window).scroll(function() {
		stickyNav();
		var scrollTop = $(window).scrollTop();
		if (scrollTop > 500) {
			$('.btn-to-top').fadeIn();
		} else {
			$('.btn-to-top').fadeOut();
		};
	});
	$('.btn-to-top').click(function(e){
		var _target = $($(this).attr('href'));
		$(window).scrollTo(_target.offset().top - 100, 800);
		e.preventDefault();
	});
// END of script for fixed header and .btn-to-top
});