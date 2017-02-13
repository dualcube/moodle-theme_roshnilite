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
		infiniteLoop: true,
		pager: false,
		adaptiveWidth: true,
		controls: true
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
		$('.nav-collapse').toggleClass('in collapse').removeClass('collapse').removeAttr("style");
	});
	$('.navbar .dropdown').click(function() {
		$(this).each(function(){
			$(this).children('ul.dropdown-menu').slideToggle();
		});
	});
	$( window ).load(function() {
  		var outerHeight = $('#page-content').outerHeight();
		$('#block-region-side-post').css('min-height', outerHeight+'px');
		$('#block-region-side-pre').css('min-height', outerHeight+'px');
	});

	$('ul li span.filler').hide();
	$('ul li.divider').hide();

	if ($('.usermenu span').hasClass("login")) {
		$('.usermenu').hide();
	}
});

