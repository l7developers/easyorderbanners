<script type="text/javascript">

$(document).ready(function() {

$( function() {
	$('span.information').tooltip();
});

var combinations = <?php echo json_encode($sets,JSON_PRETTY_PRINT);?>;
var variable_price = <?php echo json_encode($variable_price,JSON_PRETTY_PRINT);?>;
var discount_arr = <?php echo json_encode($discount,JSON_PRETTY_PRINT);?>;

var orderObj={};

orderObj.shipping_type = '<?php echo (isset($product->shipping->type))?$product->shipping->type:'';?>';
orderObj.shipping_min_value = '<?php echo (isset($product->shipping->min_value))?$product->shipping->min_value:'';?>';
orderObj.shipping_weight = '<?php echo (isset($product->shipping->weight))?$product->shipping->weight:'';?>';
orderObj.shipping_price = '<?php echo (isset($product->shipping->price))?$product->shipping->price:'';?>';

orderObj.price_default=Number($('input[name="price_default"]').val());
if($('#quantity').val() != ''){
	orderObj.quantity = Number($('#quantity').val());
}else{
	orderObj.quantity = 1;
}

orderObj.product_name = '{{$product->name}}';
orderObj.price_sqft_area = Number('{{$price_sqft_area}}');
orderObj.variant = Number('{{$variant_check}}');
orderObj.show_width_height = Number('{{$show_width_height}}');

orderObj.productMinPrice = Number('{{(isset($min_price))?$min_price:0}}');

if(orderObj.show_width_height == 1){
	orderObj.width = Number($('input#width').val());
	orderObj.height = Number($('input#height').val());
	
	orderObj.productMinSQFT = Number('{{(isset($product->min_sqft))?$product->min_sqft:0}}');
}else{
	orderObj.width = 1;
	orderObj.height = 1;
	orderObj.productMinSQFT = 0;
}

if($("input#width").length > 0 && $("input#height").length > 0){
	orderObj.width = Number($('input#width').val());
	orderObj.height = Number($('input#height').val());
}
orderObj.variant_price=Number($('input[name="price_default"]').val());
orderObj.variant_option_price=Number($('input[name="price_default"]').val());

var comb_str = '';
$('.variants_option').each(function(index,val){
	comb_str += $('option:selected', this).val()+'-';
});
comb_str = comb_str.substring(0, comb_str.length - 1);

orderObj.comb_str = comb_str;
var sqft_area = orderObj.width * orderObj.height;


orderObj.custom_option = new Object();

$('.option_custom').each(function(i,v){
	
	var sid=$(this).attr('id');
	
	custom_option = new Object();

	custom_option.id = $(this).attr('data-id');
	custom_option.name = $(this).attr('data-name');		
	custom_option.value = $('#'+sid+' :selected').attr('value');
	custom_option.price = parseFloat($('#'+sid+' :selected').attr('data-price'));
	custom_option.weight = parseFloat($('#'+sid+' :selected').attr('data-weight'));
	custom_option.price_type = $('#'+sid+' :selected').attr('data-price-type');
	custom_option.flat_rate_additional_price = parseFloat($('#'+sid+' :selected').attr('data-flat_rate_additional_price'));
	
	orderObj.custom_option[custom_option.id]= custom_option;
});

custom_option = new Object();

custom_option.value = 'Production Turnaround Time is '+'{{$product->turnaround_time}}';
custom_option.price = 0;

orderObj.custom_option['turnaround_time']= custom_option;


orderObj.product_weight = '{{$product->shipping_weight}}';
orderObj.gross_price=0;
orderObj.gross_total=0;

orderObj.discount_per = 0;
$.each(discount_arr,function(index,val){
	if(index <= orderObj.quantity)
		orderObj.discount_per=Number(val);
});

orderObj.qty_discount=0;
orderObj.total=0;

function calculation(){
	var gross_price = orderObj.variant_price * (orderObj.width * orderObj.height);

	$.each(orderObj.custom_option, function (cid, coption) {
		if(coption.price_type == "area")
		{
			gross_price = gross_price + ( (orderObj.width * orderObj.height) * coption.price);
		}
		else if(coption.price_type == "parimeter")
		{
			gross_price = gross_price + ( ((orderObj.width + orderObj.height) * 2 ) * coption.price);
		}
		else if(coption.price_type == "item")
		{
			gross_price = (gross_price + coption.price );
		}
		else if(coption.price_type == "line_item")
		{
			gross_price = gross_price + ( Number(coption.price) / orderObj.quantity ) ;
		}
	});	
	
	gross_price = gross_price.toFixed(2);		
	/* if(orderObj.productMinPrice != 0 && gross_price < orderObj.productMinPrice){
		orderObj.gross_price = orderObj.productMinPrice.toFixed(2);	
	}else{
		orderObj.gross_price = gross_price;	
	} */
	orderObj.gross_price = gross_price;	
	orderObj.gross_total = (orderObj.gross_price * orderObj.quantity).toFixed(2);
	
	if(orderObj.productMinPrice != 0 && orderObj.gross_total < orderObj.productMinPrice){
		orderObj.gross_total = orderObj.productMinPrice.toFixed(2);	
		orderObj.gross_price = (orderObj.gross_total / orderObj.quantity).toFixed(2);
	}	
	
	orderObj.qty_discount = Number((orderObj.gross_total *  orderObj.discount_per)/100).toFixed(2);

	orderObj.total =  orderObj.gross_total - orderObj.qty_discount;
	orderObj.total =  orderObj.total.toFixed(2);
	
	//alert('gross price'+orderObj.gross_price+'gross total'+orderObj.gross_total+'qty dis'+orderObj.qty_discount+'total'+orderObj.total);

	if(orderObj.productMinPrice != 0 && orderObj.total < orderObj.productMinPrice){
		orderObj.total =  orderObj.productMinPrice.toFixed(2);
		
		$("#product_each_amount").html("$"+ formatMoney(orderObj.total) +" each");
	}else{
		$("#product_each_amount").html("$"+ formatMoney(orderObj.gross_price) +" each");
	}
	
	$("#product_amount").html("$"+ formatMoney(orderObj.total) );
	$(".sub_total").val(orderObj.total);

	return true;		
}

var min_width = Number($('input[name="min_width"]').val());
var max_width = Number($('input[name="max_width"]').val());

var min_height = Number($('input[name="min_height"]').val());
var max_height = Number($('input[name="max_height"]').val());

$('input[name="quantity"],#width,#height').on('keyup input change', function(e) {
    if($(this).val() < 1)
		$(this).val('1');
	
	$(this).closest('.main_box').removeClass('has-error');
	$($(this)).closest('div').nextAll('.help-block').remove();
	if($('input[name="quantity"]').val()>1)
		$('span.qty_help').show();
	else
		$('span.qty_help').hide();
	
	var eid = $(this).attr("id");
	var on = 1;
	if(eid=='width'){	
		var width = Number($(this).val());
		if(width < min_width){
			on = 0;
			$(this).closest('.main_box').addClass('has-error');
			$("<div class='clearfix'></div><span class='help-block'>Minimum width "+min_width+" is required.</span>").insertAfter($(this).closest('div'));
		}
		if(max_width != 0){
			if(width > max_width){
				on = 0;
				$(this).closest('.main_box').addClass('has-error');
				$("<div class='clearfix'></div><span class='help-block'>Maximum width "+max_width+" is required.</span>").insertAfter($(this).closest('div'));
			}
		}
		
		if(on == 1){
			orderObj.width = width;
			var sqft_area = orderObj.width * orderObj.height;
			sqft_area = Number(sqft_area);
			calculate_price(orderObj.comb_str,sqft_area);
			//check_price();
		}
	}
	else if(eid=='height'){
		var height = Number($(this).val());
		
		if(height < min_height){
			on = 0;
			$(this).closest('.main_box').addClass('has-error');
			$("<div class='clearfix'></div><span class='help-block'>Minimum height "+min_height+" is required.</span>").insertAfter($(this).closest('div'));
		}
		if(max_height != 0){
			if(height > max_height){
				on = 0;
				$(this).closest('.main_box').addClass('has-error');
				$("<div class='clearfix'></div><span class='help-block'>Maximum height "+max_height+" is required.</span>").insertAfter($(this).closest('div'));
			}
		}
		
		if(on == 1){
			orderObj.height = height;
			var sqft_area = orderObj.width * orderObj.height;
			sqft_area = Number(sqft_area);
			calculate_price(orderObj.comb_str,sqft_area);
			//check_price();
		}
	}
	else if(eid=='quantity'){
		orderObj.quantity = Number($(this).val());
		orderObj.discount_per = 0;
		$.each(discount_arr,function(index,val){
			if(index <= orderObj.quantity)
				orderObj.discount_per=Number(val);
		});
	}

	calculation();		
});

$('.option_custom').change(function(){

	var sid=$(this).attr('id');

	custom_option = new Object();

	custom_option.id = $(this).attr('data-id');
	custom_option.name = $(this).attr('data-name');		
	custom_option.value = $('#'+sid+' :selected').attr('value');
	custom_option.price = parseFloat($('#'+sid+' :selected').attr('data-price'));
	custom_option.weight = parseFloat($('#'+sid+' :selected').attr('data-weight'));
	custom_option.price_type = $('#'+sid+' :selected').attr('data-price-type');
	custom_option.flat_rate_additional_price = parseFloat($('#'+sid+' :selected').attr('data-flat_rate_additional_price'));

	orderObj.custom_option[custom_option.id]= custom_option;

	calculation();

});

$('.variants_option').change(function(){

	var comb_str = '';
	$('.variants_option').each(function(index,val){
		comb_str += $('option:selected', this).val()+'_';
	});
	comb_str = comb_str.substring(0, comb_str.length - 1);

	orderObj.comb_str = comb_str;

	var sqft_area = orderObj.width * orderObj.height;
	sqft_area = Number(sqft_area);
	calculate_price(comb_str,sqft_area);

	calculation();
		
});

function calculate_price(comb_str,area){

	if(comb_str == "" && variable_price !="")
	{
		$.each(variable_price, function (index, value) {

			if(area >= value.min_area && area <= value.max_area)
			{
				orderObj.variant_price = Number(value.price);
			}			
		});	
	}

	$.each(combinations, function (index, value) {
        if(index.replace('-','_') == comb_str.replace('-','_')){
			if(value.variable_price == 1){
				var first_key = 0;
				var j = 0;
				var change = 0;
				$.each(value.price, function (i, v) {
					if(j == 0){
						first_key = i;
						j++;
					}
					if(area >= Number(v.min_area) && area <= Number(v.max_area)){
						orderObj.variant_price = Number(v.price);
						$('input[name="price_default"]').val(Number(v.price));
						change = 1;
					}
				});
				if(change == 0){
					$.each(value.price, function (i, v) {
						if(i == first_key){
							orderObj.variant_price = Number(v.price);
							$('input[name="price_default"]').val(Number(v.price));
						}
					});
				}
			}else{
				orderObj.variant_price = Number(value.price);
				$('input[name="price_default"]').val(Number(value.price));
			}
		}
	});
}

calculate_price(comb_str,sqft_area)

/* function check_price()
{
	if(orderObj.price_sqft_area == 0){
		orderObj.variant_price = orderObj.variant_option_price;
		$('input[name="price_default"]').val(orderObj.variant_price);
	}else{
		var sqft_area = orderObj.width * orderObj.height;
		if(sqft_area > 0 && sqft_area <= 300){
			orderObj.variant_price = orderObj.variant_option_price;
			$('input[name="price_default"]').val(orderObj.variant_price);
		}else if(sqft_area > 300 && sqft_area <= 500){
			orderObj.variant_price = orderObj.variant_option_price_300;
			$('input[name="price_default"]').val(orderObj.variant_price);
		}else if(sqft_area > 500 && sqft_area <= 1000){
			orderObj.variant_price = orderObj.variant_option_price_500;
			$('input[name="price_default"]').val(orderObj.variant_price);
		}else if(sqft_area > 1000){
			orderObj.variant_price = orderObj.variant_option_price_1000;
			$('input[name="price_default"]').val(orderObj.variant_price);
		}
	}
	//alert("check "+orderObj.variant_price)
} */

//check_price();
calculation();


$('.addcart').click(function(){
	var check = validate();
	if(check){
		// check if product total is less them then min. sale price
		
			min_price = '<?php echo (isset($product->min_price))?$product->min_price:'';?>';
			
			if((orderObj.width*orderObj.height) < orderObj.productMinSQFT){
				alert("The area of your order does not meet our minimum area which is "+orderObj.productMinSQFT+" sqft. Please adjust your order width and height.");
				return false;
			}else if(Number(orderObj.total) < Number(min_price))
			{
				/* alert("The size for your order does not meet our minimum requirements. Please adjust your order or call us at {{config('constants.store_phone_number')}} to discuss your order.");
				return false; */
			}
			
		// end if product total check with min sale price

		var form = $('#product_form');
		$.ajax({
			url:'{{url("cart/add")}}',
			type:'post',
			dataType:'json',
			data:{'object':orderObj,'form':form.serialize()},
			beforeSend: function() {
				$('#fade').fadeIn();
				$('.fa-spinner').removeClass('hide');
			},
			success:function(data){
				$('#fade').fadeOut();
				$('.fa-spinner').addClass('hide');
				if(data.status == 'success'){
					window.location.href = "{{url('cart')}}";
					/* $('.cart_menu').html(data.res);
					if(data.session_count > 0){
						$('#cart_count').html(data.session_count).removeClass('hide');
					}else{
						$('#cart_count').html('').removeClass('hide');
					} */
				}
			}
		});
	}
});


<!-------flexslider---->	
$('#imageGallery').lightSlider({
	gallery: true,
	item: 1,
	loop: true,
	thumbItem: 4,
	slideMargin: 0,
	enableDrag: false,
	currentPagerPosition: 'left',
	onSliderLoad: function(el) {
		/*el.lightGallery({
			selector: '#imageGallery .lslide'
		}); */
	}
});


$('.quote_email_btn').click(function(){
	var check = validate();
	if(check){
		
		// check if product total is less them then min. sale price
			min_price = '<?php echo (isset($product->min_price))?$product->min_price:'';?>';
			
			if((orderObj.width*orderObj.height) < orderObj.productMinSQFT){
				alert("The area of your order does not meet our minimum area which is "+orderObj.productMinSQFT+" sqft. Please adjust your order width and height.");
				return false;
			}else if(Number(orderObj.total) < Number(min_price))
			{
				/* alert("The size for your order does not meet our minimum requirements. Please adjust your order or call us at {{config('constants.store_phone_number')}} to discuss your order.");
				return false; */
			}
		// end if product total check with min sale price
		
		var type = $(this).attr('data-type');
		if(type == 'shipping'){
			//$('.shipping_detail').removeClass('hide');
		}else{
			//$('.shipping_detail').addClass('hide');
		}
		document.getElementById("email_quote").reset();
		$('#quote_type').val(type);
		$('#product_quote').modal('show');
	}
});

$(document).on('click','.send_quote',function(){	
	$('.quote_input').closest('.form-group').removeClass('has-error');
	$('.quote_input').nextAll().remove();
	var form = $('#product_form');
	$.ajax({
		url:'{{url("send-quote")}}',
		type:'post',
		dataType:'json',
		data:{'object':orderObj,'product_form':form.serialize(),'form':$('form#email_quote').serialize()},
		beforeSend: function() {
			$('#quote_loader_img').fadeIn();
			$('.send_quote').prop('disabled',true);
		},
		success:function(data){
			$('#quote_loader_img').fadeOut();
			$('.send_quote').prop('disabled',false);
			if(data.status == 'success'){
				$('#product_quote').modal('hide');
			}else{
				if(data.msg != ''){
					alert(data.msg);
				}
				
				$.each(data.errors,function(i,v){
					$('#'+i).closest('.form-group').addClass('has-error');
					$("<span class='help-block'>"+v+"</span>").insertAfter($('#'+i));
				});
			}
		}
	});
});


$('.quote_shipping_btn').click(function(){
	var check = validate();
	if(check){
		document.getElementById("shipping_quote").reset();
		$('.shipping_detail').html('');
		$('#product_shipping_quote').modal('show');
	}
});

$('#shipping_quote').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        $('.get_shipping').click();
    }
});

$('#email_quote').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        $('.send_quote').click();
    }
});

$(document).on('click','.get_shipping',function(ev){
    ev.preventDefault();
	$('.quote_input').closest('div').removeClass('has-error');
	$('.quote_input').nextAll().remove();
	var form = $('#product_form');
	$.ajax({
		url:'{{url("get-shipping")}}',
		type:'post',
		dataType:'json',
		data:{'object':orderObj,'product_form':form.serialize(),'form':$('form#shipping_quote').serialize()},
		beforeSend: function() {
			$('#shipping_quote_loader_img').fadeIn();
			$('.get_shipping').prop('disabled',true);
		},
		success:function(data){
			$('#shipping_quote_loader_img').fadeOut();
			$('.get_shipping').prop('disabled',false);
			if(data.status == 'success'){
				$('.shipping_detail').html(data.detail);
			}else{
				if(data.msg != ''){
					alert(data.msg);
				}
				$('.shipping_detail').html('');
				$.each(data.errors,function(i,v){
					$('#'+i).closest('.form-group').addClass('has-error');
					$("<span class='help-block'>"+v+"</span>").insertAfter($('#'+i));
				});
			}
		}
	});
});

function validate(){
	var on = 1;
	$('.option_fields').each( function (index, data) {
		$(this).closest('.main_box').removeClass('has-error');
		$($(this)).closest('div').nextAll().remove();
		if($(this).val() == ''){
			on = 0;
			$(this).closest('.main_box').addClass('has-error');
			$("<div class='clearfix'></div><span class='help-block'>This field is required</span>").insertAfter($(this).closest('div'));
		}
	});
	return on;
}

});



$(function () {
	function put_star(){
		$('.comment_star').each(function(){
			var star = $(this).attr('data');
			$(this).rateYo({
				normalFill: "#A0A0A0",
				rating: Number(star),
				halfStar: true,
				readOnly: true,
				starWidth: "20px",
			  });
		});
	}
	
  $("#rateYo").rateYo({
    normalFill: "#A0A0A0",
	rating: Number('{{$avg}}'),
    halfStar: true,
	readOnly: true,
	starWidth: "20px",
  });
  
  var current_page = 1;
  var dataGet = 1;
  $('.view_more').click(function(){
	  if(dataGet == 1){
		$.ajax({
			url:'{{url("reviews/get?page=")}}'+(++current_page),
			type:'get',
			data:{'product_id':'{{$product->id}}'},
			dataType:'json',
			success:function(data){
				if(data.status == 'success'){
					if(data.data == 1){
						$('.show_reviews:last').append(data.str);
						put_star();
					}else{
						dataGet = 0;
						$('.view_more').css({'display':'none'});
					}
				}
			}
		});
	  }
  });

  put_star();
});

if($(window).width() < 1024){
	$('.mobile_custom_tab').html($('.custom_tab').html());
	$('.mobile_custom_tab').attr('id','accordion');
	$('.custom_tab').remove();
}
</script>