function initSlider(width) {
	var $slider = $('.popular__list'),
			itemsNum = $slider.children('li').length,
			sliderInited = false,
			w;

	function buildSlider() {
		// extrasmall devices
		if(w <= width && !sliderInited) {
			$slider.owlCarousel({
				loop: false,
	      margin: 0,
	      nav: true,
	      navText: ['<svg class="icon-arrow"><use xlink:href="#arrow" /></svg>', '<svg class="icon-arrow"><use xlink:href="#arrow" /></svg>'],
	      dots: false,
	      items: 1,
	      responsiveClass: true,
	      navRewind: false,
	      onInitialized: function() {
	      	sliderInited = true;
	      },
	      responsive: {
	      	'580': {
	      		items: 2
	      	}
	      }
			});
		} else if(w > width && sliderInited) {
			$slider.trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
			$slider.find('.owl-stage-outer').children().unwrap();
			sliderInited = false;
		}
	}
	
	if($slider.length) {
		w = window.innerWidth;
		buildSlider();

		$(window).on('resize', function() {
			w = window.innerWidth;
			buildSlider();
		});
	}
}

function bgLazyLoad() {
	var bLazy = new Blazy({
		selector: '.b-lazy',
		offset: 100
	});
}

function fixHeight() {
	var $subtitle = $('.present .subtitle'),
			h = $(window).height(),
			defMargin;
	function setMargin() {
		if(h <= 769 && h >= 600) {
			var newMargin = h*0.033+"px";
			$subtitle.css({'margin-bottom': newMargin});
			console.log('not def')
		} else {
			$subtitle.css({'margin-bottom': defMargin});
			console.log('def')
		}
	}
	if($subtitle.length) {
		defMargin = $subtitle.css('margin-bottom');
		console.log(defMargin);
		setMargin();
		$(window).on('resize', function() {
			h = $(window).height();
			setMargin();
		})
	}
}

$(document).ready(function() {
	initSlider(767);
	bgLazyLoad();
	// fixHeight();
});