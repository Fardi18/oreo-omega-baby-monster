function show_popup(){
	$('.popup_wrapper').fadeIn();
}
$(document).ready(function(){
	// $('.nav_btn').click(function(e){
	// 	if($(window).width()<768){
	// 		 e.preventDefault();
	// 	}
	// })
	$('.section_content_element').each(function(){
		if($(this).find('.ce_column .flex_box').length == 2 || $(this).find('.ce_column .flex_box').length == 4){
			$(this).find('.ce_column').addClass('twoandfour');
		}
		if($(this).find('.ce_column .flex_box').length == 3 || $(this).find('.ce_column .flex_box').length == 5 || $(this).find('.ce_column .flex_box').length == 6){
			$(this).find('.ce_column').addClass('threeandfive');
		}
	})
	$('.section_content_element .container').each(function(){
		if($(this).find('.ce_column .flex_box').length == 2 || $(this).find('.ce_column .flex_box').length == 4){
			$(this).find('.ce_column').removeClass('threeandfive').addClass('twoandfour');
		}
		if($(this).find('.ce_column .flex_box').length == 3 || $(this).find('.ce_column .flex_box').length == 5 || $(this).find('.ce_column .flex_box').length == 6){
			$(this).find('.ce_column').addClass('threeandfive');
		}
	})
	$('.section_content_element .container .ce_column').parents('.container').siblings('.container').find('.ce_button').addClass('minus_top');
	//$('.section_content_element .section_masthead').siblings('.container').find('.ce_button').removeClass('minus_top');
	$('.section_content_element .container .ce_flex .ce_text_left .ce_button').removeClass('minus_top');
	$('.section_content_element .container .ce_flex .ce_text_right .ce_button').removeClass('minus_top');
	if($('.masthead_slider .mh_box').length > 1){
		$('.masthead_slider').slick({
			autoplay:true
		});
	}
	if($('.image_slider .mh_box').length > 1){
		$('.image_slider').slick({
			autoplay:true
		});
	}
	if($('.carousel_image_slider .mh_box').length > 3){
		$('.carousel_image_slider').slick({
			autoplay:true,
			infinite: true,
	  		slidesToShow: 3,
	  		slidesToScroll: 3
		});
	}
	if($('.select2').length>0){
		$('.select2').select2();
	}
	if($('.select3').length>0){
		$('.select3').select2();
	}
	if($('.testimoni_slider').length > 0){
		$('.testimoni_slider').slick({
			// autoplay:true,
			infinite: true,
			slidesToShow: 3,
			slidesToScroll: 3,
			responsive: [
		    {
		      breakpoint: 960,
		      settings: {
		        slidesToShow: 2,
		        slidesToScroll: 2,
		        infinite: true,
		        dots: true
		      }
		    },
		    {
		      breakpoint: 768,
		      settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		      }
		    }
		    ]
		})
	}
	$('.pos_absolute').on('click', '.def_btn', function(){
		var xxx = $(".section_courses_apply").offset().top;
		$("html, body").animate({ scrollTop: xxx }, 800);
		
	})
	$('.toggle_top').click(function(){
		if($(this).siblings('.toggle_bottom').css('display')=="none"){
			$(this).siblings('.toggle_bottom').slideDown();
			$(this).removeClass('default');
			$(this).addClass('active');
		}
		else{
			$(this).siblings('.toggle_bottom').slideUp();
			$(this).removeClass('active');
			$(this).addClass('default');
		}
	})
	$('.anchor_box ul li a').click(function(){
		var gethref = $(this).attr('href');
		$(this).removeClass('active');
		$("html, body").animate({
			scrollTop: $(gethref).offset().top
		}, 600);
		return false;
	});
	$('.close_btn, .popup_wrapper .overlay').click(function(){
		$('.popup_wrapper').fadeOut();
	})
	$('.menu_mobile').click(function(){
		if($('nav').css('display')=="none"){
			var getheight = $('.notif_box').outerHeight();
			var totheight = getheight + 56;
			$('nav').css('top',totheight);
			$(this).addClass('active');
			$('nav').show();
			$('body, html').css('overflow','hidden');
		}
		else{
			$(this).removeClass('active');
			$('nav').hide();
			$('body, html').css('overflow','auto');
		}
	})
	$('.anchor_mobile').click(function(){
		if($('.anchor_wrapper').css('display')=="none"){
			$('.anchor_wrapper').slideDown();
		}
		else{
			$('.anchor_wrapper').slideUp();			
		}
	})
})
$(window).bind('resize',function(){
	if($(window).width()>959){
		$('nav').show();
	}
	// else{
	// 	$('nav').hide();
	// }
})