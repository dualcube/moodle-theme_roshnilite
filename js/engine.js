jQuery(document).ready(function() {
	var _minSlides, _minSlides2,
	    _width = $(window).width();

	if (_width <= 550) {
	    _minSlides = 1;
	    _minSlides2 = 1;
	} else if (_width > 550 && _width <= 760) {
	    _minSlides = 1;
	    _minSlides2 = 2;
	} else if (_width > 760 && _width <= 1200) {
	    _minSlides = 2;
	    _minSlides2 = 2;
	} else {	
	    _minSlides = 4;
	    _minSlides2 = 2;
	}

	$('.top-slider').bxSlider({
		slideWidth: 5000,
		minSlides: 1,
		maxSlides: 1,
		slideMargin: 0,
		moveSlides: 1,
		controls: false,
		auto: true,
		pause: 10000
	});  

	$('.av-courses-slider').bxSlider({
		slideWidth: 5000,
		minSlides: _minSlides,
		maxSlides: 4,
		slideMargin: 0,
		moveSlides: 1,
		infiniteLoop: false,
		pager: false,
		adaptiveWidth: true
	});

	/******************************************/


	$('.tabs-nav a').click(function(){
		var _index = $(this).data('label'),
			_item = $('.tabs-container').find('[data-target="' + _index + '"]');
		$('.tabs-nav a').removeClass('active');
		$(this).addClass('active');
		$('.tabs-content-item').fadeOut(0);
		_item.removeClass('hidden');
		_item.fadeIn();
	});
	$('.nav-collapse').removeClass('collapse');
	$(".btn-navbar").on("click",function() { 
		$(this).toggleClass("active-drop");
		$('.nav-collapse').toggleClass('in collapse').removeClass('collapse').removeAttr("style");
		$('.usermenu-show').removeClass('usermenu-show');
	});
});
