$(document).ready(function(){
	//$('.tooltipAdd').tooltip();
	$(document).on('hover','.tooltipAdd',function(){
		$(this).tooltip();
	});

	$('.client_review, .canopy_feather').owlCarousel({
		autoplay: false,
		autoplayTimeout: 2000,
		autoplayHoverPause: true,
		margin: 15,
		loop: false,
		dots: false,
		nav: true,
		responsive: {
			320: {
				items: 1,
				margin: 40
			},
			480: {
				items: 1,
				margin: 40
			},
			577: {
				items: 1,
				margin: 60
			},
			768: {
				items: 1,
				margin: 80
			},
			992: {
				items: 1,
				margin: 110
			}
		}
	});
	$('.client_partners').owlCarousel({
		autoplay: false,
		autoplayTimeout: 2000,
		autoplayHoverPause: true,
		margin: 15,
		loop: false,
		dots: false,
		nav: true,
		 responsive:{
        0:{
            items:1,
            nav:true,
			margin: 0,
        },
        600:{
            items:3,
            nav:false
        },
        1000:{
            items:5,
            nav:true,
            loop:false
        }
    }
	});
	$('.custom_banner_products').owlCarousel({
		autoplay: false,
		autoplayTimeout: 2000,
		autoplayHoverPause: true,
		margin: 15,
		loop: false,
		dots: false,
		nav: true,
	 responsive:{
        0:{
            items:1,
            nav:true,
			margin: 0,
        },
        600:{
            items:3,
            nav:false
        },
        1000:{
            items:3,
            nav:true,
            loop:false
        }
    }
	});
});