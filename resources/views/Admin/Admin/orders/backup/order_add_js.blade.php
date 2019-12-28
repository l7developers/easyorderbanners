<script>

var combinations = null;
var discount_arr = null;

var min_width = null;
var max_width = null;
var min_height = null;
var max_height = null;

var orderObj={};

orderObj.price_default=null;
orderObj.quantity = 1;

orderObj.shipping_type = '';
orderObj.shipping_min_value = '';
orderObj.shipping_weight = '';
orderObj.shipping_price = '';

orderObj.product_weight = 0;
orderObj.price_sqft_area = null;
orderObj.variant = null;
orderObj.show_width_height = null;
orderObj.variant_price=null;
orderObj.variant_option_price=null;

var comb_str = '';

$(document).on('ifChecked','[name="multiple_shipping"]', function(event){
	//$('.shipping_main_div').addClass('hide');
	var formData = $('form#order_form').serialize();
	$.ajax({
		type: "POST",
		url: '{{url("admin/order/productaddress")}}',
		dataType:'json',
		beforeSend: function () {
		  $.blockUI();
		},
		complete: function () {
		  $.unblockUI();
		},
		data: formData,
		success: function (data){
			if(data.status == 1){
				$('.multiple_shipping_add_div').html(data.shipping);
				$('.multiple_shipping_add_div').removeClass('hide');
				$('.shipping_main_div').addClass('hide');
				$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
				  checkboxClass: 'icheckbox_flat-green',
				  radioClass   : 'iradio_flat-green'
				})
			}
		} 
	});
});

$(document).on('ifChecked','.multi_shipping_add_option', function(event){
	var val = $(this).attr('data');
	if($(this).val() == 0){
		$('#multi_shipping_add_'+val).removeClass('hide');
	}else{
		$('#multi_shipping_add_'+val).addClass('hide');
	}
});

$(document).on('ifUnchecked','[name="multiple_shipping"]', function(event){
	$('.multiple_shipping_add_div').addClass('hide');
	$('.shipping_main_div').removeClass('hide');
	$('.shipping_main_div').removeClass('has-error');
});

$(document).on('ifChecked','[name="billing_add_option"]', function(event){
	$('.billing_main_div input[type="text"]').each( function (index, data) {
		$(this).closest('div').removeClass('has-error');
		$($(this)).nextAll().remove();
	});
	if($(this).val() == 0){
		$('.new_billing_add').removeClass('hide');
	}else{
		$('.new_billing_add').addClass('hide');
	}
});

$(document).on('ifChecked','[name="shipping_add_option"]', function(event){
	$('.shipping_main_div input[type="text"]').each( function (index, data) {
		$(this).closest('div').removeClass('has-error');
		$($(this)).nextAll().remove();
	});
	if($(this).val() == 0){
		$('.shipping_add_box').removeClass('hide');
	}else{
		$('.shipping_add_box').addClass('hide');
	}
});

$('.add_more_item').on('click',function(){

	$('select#category').val("");
	$('select#product').val("");
	$('.cart_products').addClass('hide');
	$('.add_more_item').addClass('hide');
});	

$('.next').on('click',function(){
	if($(this).attr('data') == 'customer'){
		$('#customer').closest('div.form-group').removeClass('has-error');
		$('#customer').nextAll().remove();
		if($('#customer').val() == ''){
			$('input.new_customer_input').closest('div').removeClass('has-error');
			$('input.new_customer_input').nextAll().remove();
			$('#customer').closest('div.form-group').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter('#customer');
		}
		else if($('#customer').val() != 0){
			$('input.new_customer_input').closest('div').removeClass('has-error');
			$('input.new_customer_input').nextAll().remove();
			$('li.product_tab').html('<a href="#products" data-toggle="tab">Products</a>');
			$('li.product_tab a').trigger("click");
		}
		else{
			var on = 0;
			$('input.new_customer_input').closest('div').removeClass('has-error');
			$('input.new_customer_input').nextAll().remove();
							
			var _this = $('form#order_form');
			var formData = _this.serialize();
			$.ajax({
				type: "POST",
				url: '{{url("admin/order/formvalidate")}}',
				cache: false,
				async: false,
				dataType:'json',
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				data: formData,
				success: function (data){
					if(data.flag == 0){
						$.each( data.res, function( index, value ){
							$('input#'+index).closest('div').addClass('has-error');
							$("<span class='help-block'>"+value+"</span>").insertAfter("input#"+index);
						});
						
						$( "input.new_customer_input" ).focus();
						on = 0;
					}
					else{
						on = 1;
					}
				} 
			});
			if(on){
				$('li.product_tab').html('<a href="#products" data-toggle="tab">Products</a>');
				$('li.product_tab a').trigger("click");
			}
		}
	}
	else if($(this).attr('data') == 'products'){
		$('li.address_tab').html('<a href="#addresses" data-toggle="tab">Address</a>');
		$('li.address_tab a').trigger("click");
	}
	else if($(this).attr('data') == 'skip_address'){
		$('input#skip_address').val(1);
		$('li.payment_tab').html('<a href="#payment" data-toggle="tab">Payment</a>');
		$('li.payment_tab a').trigger("click");
		$('.payment_tab').removeClass("hide");
	}
	else if($(this).attr('data') == 'addresses'){
		var on = 1;
		$('#shipping_option').closest('div.form-group').removeClass('has-error');
		$('#shipping_option').nextAll().remove();
		
		if($('#shipping_option').val() == ''){
			on = 0;
			$('#shipping_option').closest('div.form-group').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter('#shipping_option');
		}
		
		if($('#customer').val() == 0){
			$('input[type="text"].new_customer_input').each( function (index, data) {
				$(this).closest('div').removeClass('has-error');
				$($(this)).nextAll().remove();
				if($(this).val() == ''){
					on = 0;
					$(this).closest('div').addClass('has-error');
					$("<span class='help-block'>This field is required</span>").insertAfter($(this));
					$('li.customer_tab a').trigger("click");
				}
			});
			
			if($('[name="multiple_shipping"]').is(':checked')){
				$('.multi_shipping input[type="text"] , .new_billing_add input[type="text"]').each( function (index, data) {
					$(this).closest('div').removeClass('has-error');
					$($(this)).nextAll().remove();
					if($(this).val() == ''){
						on = 0;
						$(this).closest('div').addClass('has-error');
						$("<span class='help-block'>This field is required</span>").insertAfter($(this));
					}
				});
			}
			else{
				$('.shipping_add_box input ,.shipping_add_box select , .new_billing_add input , .new_billing_add select').each( function (index, data) {
					if(!$(this).hasClass('opt')){
						$(this).closest('div').removeClass('has-error');
						$($(this)).nextAll().remove();
						if($(this).val() == ''){
							on = 0;
							$(this).closest('div').addClass('has-error');
							$("<span class='help-block'>This field is required</span>").insertAfter($(this));
						}
					}
				});
			}
			
		}else{
			if ($('[name="billing_add_option"]').is(":checked")) {
				if($('[name="billing_add_option"]:checked').val() == 0){
					$('.new_billing_add input,.new_billing_add select').each( function (index, data) {
						if(!$(this).hasClass('opt')){
							$(this).closest('div').removeClass('has-error');
							$($(this)).nextAll().remove();
							if($(this).val() == ''){
								on = 0;
								$(this).closest('div').addClass('has-error');
								$("<span class='help-block'>This field is required</span>").insertAfter($(this));
							}
						}
					});
				}
			}else{
				$('.billing_main_div').addClass('has-error');
				on = 0;
			}
			
			if($('[name="multiple_shipping"]').is(':checked')){
				
				$('input[type="radio"].multi_shipping_add_option:checked').each(function(){ 
					var value = $(this).val();
					var data = $(this).attr('data');
					if(value == 0){
						$('#multi_shipping_add_'+data+' input.shipping_input,#multi_shipping_add_'+data+' select.shipping_input').each( function (index, data) {
							if(!$(this).hasClass('opt')){
								$(this).closest('div').removeClass('has-error');
								$($(this)).nextAll().remove();
								if($(this).val() == ''){
									on = 0;
									$(this).closest('div').addClass('has-error');
									$("<span class='help-block'>This field is required</span>").insertAfter($(this));
								}
							}
						});
					}
					else{
						$('#multi_shipping_add_'+data+' input[type="text"].shipping_input').each( function (index, data) {
							$(this).closest('div').removeClass('has-error');
							$($(this)).nextAll().remove();
						});
					}
				});
			}
			else{
				$('.shipping_main_div').removeClass('has-error');
				if ($('[name="shipping_add_option"]').is(":checked")) {
					if($('[name="shipping_add_option"]:checked').val() == 0){
						$('.shipping_add_box input,.shipping_add_box select').each( function (index, data) {
							if(!$(this).hasClass('opt')){
								$(this).closest('div').removeClass('has-error');
								$($(this)).nextAll().remove();
								if($(this).val() == ''){
									on = 0;
									$(this).closest('div').addClass('has-error');
									$("<span class='help-block'>This field is required</span>").insertAfter($(this));
								}
							}
						});
					}
				}
				else{
					$('.shipping_main_div').addClass('has-error');
					on = 0;
				}
			}
		}
		//alert(on);
		if(on){
			$.ajax({
				url:'{{url("admin/order/shipping")}}',
				type:'post',
				dataType:'json',
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				data:$('#order_form').serialize(),
				success:function(data){
					if(data.status){
						if(data.ship){
							$('#shipping_amount').val(data.rate);
							$('#shipping').html('$'+data.rate);
							$('#shipping').closest('li').removeClass('hide');
						}
						$('input#skip_address').val(0);
						$('li.payment_tab').html('<a href="#payment" data-toggle="tab">Payment</a>');
						$('li.payment_tab a').trigger("click");
						$('.payment_tab').removeClass("hide");
					}else{
						alert(data.msg);
					}
				}
			});
		}
	}
});

$('#customer').on('change',function(){
	$(this).closest('div.form-group').removeClass('has-error');
	$($(this)).nextAll().remove();
	if($(this).val() == ''){
		$(this).closest('div.form-group').addClass('has-error');
		$("<span class='help-block'>This field is required</span>").insertAfter($(this));
		$('.billing_add_div').removeClass('hide');
		$('.new_billing_add').addClass('hide');
		$('.shipping_add_div').removeClass('hide');
		$('.shipping_add_box').addClass('hide');
	}
	else if($(this).val() == 0){
		$('#new_customer_div').removeClass('hide');
		$('#customer_div .next_div').removeClass('hide');
		$('input[name="multiple_shipping"]').iCheck('uncheck');
		$('.billing_add_div').addClass('hide');
		$('.new_billing_add').removeClass('hide');
		$(".new_billing_add input").prop('required', false);
		$('.shipping_add_div').addClass('hide');
		$('.shipping_add_box').removeClass('hide');
		$(".shipping_add_box input").prop('required', false);
	}
	else{
		$('.billing_add_div').removeClass('hide');
		$('.new_billing_add').addClass('hide');
		$(".new_billing_add input").prop('required', false);
		$('.shipping_add_div').removeClass('hide');
		$('.shipping_add_box').addClass('hide');
		$(".shipping_add_box input").prop('required', false);
		
		$('#new_customer_div').addClass('hide');
		$('#customer_div .next_div').removeClass('hide');
		
		var formData = $('form#order_form').serialize();
		$.ajax({
			type: "POST",
			url: '{{url("admin/order/useraddress")}}',
			cache: false,
			async: false, //blocks window close
			dataType:'json',
			data: formData,
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success: function (data){
				if(data.status == 1){
					$( ".billing_add_div").html(data.res.billing);
					$( ".shipping_add_div").html(data.res.shipping);
					$('input[name="multiple_shipping"]').iCheck('uncheck');
					$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
					  checkboxClass: 'icheckbox_flat-green',
					  radioClass   : 'iradio_flat-green'
					})
				}
			} 
		});
	}
});

$('#category').on('change',function(){
	$('#product_option_div div').html('');
	$('#product_option_div').addClass('hide');

	if($(this).val()=='custom')
	{
		$('#custom_product_div').show();
		$('#product_option_div div').html('');
	}	
	else
		$('#custom_product_div').hide();
	

	if($(this).val() != ''){
		$.ajax({
			url:'{{url("admin/order/get_product_by_category")}}/'+$(this).val(),
			type:'get',
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == "success"){
					$('#product').html(data.html);
				}
			}
		});
	}
});

$('#product').on('change',function(){
	if($(this).val() != ''){
		$.ajax({
			url:'{{url("admin/order/get_product_options")}}/'+$(this).val(),
			type:'get',
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == "success"){
					combinations = data.combination;
					discount_arr = data.discount;
					$('#cart_amount').val(data.session_total);
					//$('#amount').html('$'+(data.sub_total+data.session_total));
					$('#product_option_div div').html(data.html);
					$('#product_option_div').removeClass('hide');
					
					// Object Setup for calculation //
					
					orderObj.quantity = 1;
					orderObj.price_default=Number($('input[name="price_default"]').val());
					orderObj.price_sqft_area = Number(data.price_sqft_area);
					orderObj.variant = Number(data.variant_check);
					orderObj.show_width_height = Number(data.show_width_height);
					if(orderObj.show_width_height == 1){
						orderObj.width = Number($('input#width').val());
						orderObj.height = Number($('input#height').val());
					}else{
						orderObj.width = 1;
						orderObj.height = 1;
					}
					
					if($("input#width").length > 0 && $("input#height").length > 0){
						orderObj.width = Number($('input#width').val());
						orderObj.height = Number($('input#height').val());
					}
					
					orderObj.variant_price=Number($('input[name="price_default"]').val());
					orderObj.variant_option_price=Number($('input[name="price_default"]').val());
					
					orderObj.shipping_type = data.shipping_type;
					orderObj.shipping_min_value = data.shipping_min_value;
					orderObj.shipping_weight = data.shipping_weight;
					orderObj.shipping_price = data.shipping_price;
					
					var comb_str = '';
					$('.variants_option').each(function(index,val){
						comb_str += $('option:selected', this).val()+'-';
					});
					comb_str = comb_str.substring(0, comb_str.length - 1);
					
					orderObj.comb_str = comb_str;
					var sqft_area = orderObj.width * orderObj.height;
					
					orderObj.custom_option= new Object();
					orderObj.gross_price=0;
					orderObj.product_total_price=0;
					orderObj.discount_per=0;
					orderObj.discount=0;
					orderObj.total_price=0;	
					orderObj.product_weight=Number($('input[name="product_weight"]').val());	
					
					min_width = Number($('input[name="min_width"]').val());
					max_width = Number($('input[name="max_width"]').val());

					min_height = Number($('input[name="min_height"]').val());
					max_height = Number($('input[name="max_height"]').val());

					orderObj.custom_option= new Object();

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
					
					//console.log(orderObj);
					calculate_price(comb_str,sqft_area);
					calculation();
				}
			}
		});
		$('li.payment_tab').html('<a href="javascript:void(0)">Payment</a>');
		$('#shipping_amount').val('');
		$('#shipping').html('');
		$('#shipping').closest('li').addClass('hide');			
		
		$('.multiple_shipping_add_div').html('');
		$('input[name="billing_add_option"]').each(function(index,val){
			$(this).iCheck('uncheck');
		});
		$('input[name="shipping_add_option"]').each(function(index,val){
			$(this).iCheck('uncheck');
		});
		$('input[name="multiple_shipping"]').iCheck('uncheck');
		$('.shipping_main_div').removeClass('hide');
		$('.shipping_main_div').removeClass('has-error');
	}
	else{
		$('#product_option_div div').html('');
		$('#product_option_div').addClass('hide');
	}
});



function calculation(){	

	//console.log(orderObj);
	
	var gross_price = orderObj.variant_price * (orderObj.width * orderObj.height);	
		
	$.each(orderObj.custom_option, function (cid, coption) {				
		if(coption.price_type == "area")
		{
			gross_price = gross_price + ( (orderObj.width * orderObj.height) * coption.price );
		}
		else if(coption.price_type == "parimeter")
		{
			gross_price = gross_price + ( ((orderObj.width + orderObj.height) * 2 ) * coption.price );
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
	
	orderObj.gross_price = gross_price.toFixed(2);
	orderObj.gross_total = (gross_price*orderObj.quantity).toFixed(2);	
	
	orderObj.qty_discount = (orderObj.gross_total *  orderObj.discount_per)/100;
	orderObj.total =  orderObj.gross_total - orderObj.qty_discount;
	orderObj.total =  orderObj.total .toFixed(2);

	$("#product_amount").html("$"+orderObj.total);
	$(".sub_total").val(orderObj.total);
	
	//console.log(orderObj);
	return true;		
}

$(document).on('blur','input[name="quantity"],#width,#height',function(e) {
	
	if($(this).val() < 1)
		$(this).val('1');
	
	$(this).closest('.form-group').removeClass('has-error');
	$($(this)).nextAll().remove();
	
	var eid = $(this).attr("id");
	var on = 1;
	if(eid=='width'){	
		var width = Number($(this).val());
		if(width < min_width){
			on = 0;
			$(this).closest('.form-group').addClass('has-error');
			$("<span class='help-block'>Minimum width "+min_width+" is required.</span>").insertAfter($(this));
		}
		if(max_width != 0){
			if(width > max_width){
				on = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>Maximum width "+max_width+" is required.</span>").insertAfter($(this));
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
			$(this).closest('.form-group').addClass('has-error');
			$("<span class='help-block'>Minimum height "+min_height+" is required.</span>").insertAfter($(this));
		}
		if(max_height != 0){
			if(height > max_height){
				on = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>Maximum height "+max_height+" is required.</span>").insertAfter($(this));
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

$(document).on('change','.option_custom',function(){

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

$(document).on('change','.variants_option',function(){

	var comb_str = '';
	$('.variants_option').each(function(index,val){
		comb_str += $('option:selected', this).val()+'_';
	});
	comb_str = comb_str.substring(0, comb_str.length - 1);
	
	orderObj.comb_str = comb_str;
	
	var sqft_area = orderObj.width * orderObj.height;
	sqft_area = Number(sqft_area);
	calculate_price(comb_str,sqft_area);
	
	//console.log(orderObj);
	calculation();
});

function calculate_price(comb_str,area){
	//alert(area)
	$.each(combinations, function (index, value) {
		if(index == comb_str){
			if(value.variable_price == 1){
				var first_key = 0;
				var j = 0;
				var change = 0;
				$.each(value.price, function (i, v) {
					if(j == 0){
						first_key = i;
						j++;
					}
					//alert(v.min_area+"  "+v.max_area+'  '+v.price)
					if(area > Number(v.min_area) && area <= Number(v.max_area)){
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
	//console.log(orderObj);
}

function check_price(){
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
}

function add_to_cart(){
	var on = 1;
	var product_div = ($('#category').val()=='custom')?'custom_product_div':'product_option_div';

	$('#'+product_div+' .option_fields').each( function (index, data) {
		$(this).closest('div').removeClass('has-error');
		$($(this)).nextAll().remove();
		if($(this).val() == ''){
			on = 0;
			$(this).closest('div').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($(this));
		}
	});
	if(on){
		var formData = $('form#order_form').serialize();
		$.ajax({
			type: "POST",
			url: '{{url("admin/order/add_to_cart")}}',
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			data: {'form':formData,'object':orderObj},
			success: function (data){
				if(data.status == 1){
					$('#cart_amount').val(data.session_total);
					$('#amount').html('$'+data.session_total);
					$('.cart_products table tbody').html(data.res);
					$('.cart_products').removeClass('hide');
					$('#product_option_div div').html('');
					$('#custom_product_div').hide();
					$('.next_div').removeClass('hide');
					$('.add_more_item').removeClass('hide');
				}
			} 
		});
	}
}

$('form').on( "submit", function( event ) {
	 if($('#customer').val() != 0){
		return true;
	}else{
		var on = 0;
		$('input.new_customer_input').closest('div').removeClass('has-error');
		$('input.new_customer_input').nextAll().remove();
						
		var formData = $('form#order_form').serialize();
		$.ajax({
			type: "POST",
			url: '{{url("admin/order/formvalidate")}}',
			cache: false,
			async: false, //blocks window close
			dataType:'json',
			data: formData,
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success: function (data){
				if(data.flag == 0){
					$.each( data.res, function( index, value ){
						$('input#'+index).closest('div').addClass('has-error');
						$("<span class='help-block'>"+value+"</span>").insertAfter("input#"+index);
					});
					$('li.customer_tab a').trigger("click");
					$( "input.new_customer_input" ).focus();
					on = 0;
				}
				else{
					on = 1;
				}
			} 
		});
		if(on){
			return true;
		}
		else{
			return false;
		}
	}
});
</script>