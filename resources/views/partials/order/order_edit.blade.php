<div class="modal fade" id="order_edit">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Order</h4>
			</div>
			<div class="modal-body">
				<div class="row"></div>
			</div>
		</div>
	</div>
</div>

<script>
var combinations = null;
var discount_arr = null;

var min_width = null;
var max_width = null;
var min_height = null;
var max_height = null;

var orderObj={};

orderObj.price_default=null;
orderObj.productMinPrice = null;
orderObj.productMinSQFT = null;
orderObj.quantity = 1;

orderObj.price_sqft_area = null;
orderObj.variant = null;
orderObj.show_width_height = null;
orderObj.variant_price=null;
orderObj.variant_option_price=null;
orderObj.shipping_price = '';
var comb_str = '';


	$(document).on('click','.fa-pencil-square-o.address',function(e){
		$('#order_edit .modal-header h4').html('Edit Order Address');
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var order_product_table_id = $(this).attr('order-product-table-id');
		var order_multiple = 0;
		var order_product_id = 0;
		if($(this).is('[order-product-id]')){
			order_product_id = $(this).attr('order-product-id');
		}
		if($(this).is('[order-multiple]')){
			order_multiple = $(this).attr('order-multiple');
		}
		
		$.ajax({
			url:'{{url("admin/order/order_edit")}}',
			type:'post',
			data:{'type':type,'get':'form','order_id':order_id,'order_product_id':order_product_id,'order_multiple':order_multiple,'order_product_table_id':order_product_table_id},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('#order_edit .modal-body div.row').html(data.html);
					$('#order_edit').modal('show');
				}
			}
		});
	});

	$(document).on('change','select#saved_address',function(e){		
		var order_id = $(this).attr('order-id');
		var order_multiple = $(this).attr('order-multiple');
		var order_product_table_id = $(this).attr('order-product-table-id');
		var order_product_id = 0;
		if(order_multiple != 0){
			order_product_id = $(this).attr('order-product-id');
		}
		var address_id = $(this).val();
		
		$.ajax({
			url:'{{url("admin/order/order_edit")}}',
			type:'post',
			data:{'get':'form','type':'shipping_add','order_id':order_id,'order_product_id':order_product_id,'order_multiple':order_multiple,'order_product_table_id':order_product_table_id,'address_id':address_id},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('div#order_edit div.modal-body div.row').html(data.html);
					$('#order_edit').modal('show');
				}
			}
		});
	});
	
	$(document).on('click','.edit_address',function(e){
		var on = 1;
		
		$('.add_option').each(function(index,value){
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
		
		if(on){
			var form_data = $('form#edit_add').serialize();
			$.ajax({
				url:'{{url("admin/order/order_edit")}}',
				type:'post',
				data:{'get':'form_save','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#edit_add_loader_img').fadeIn();
					$('.edit_address').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#edit_add_loader_img').fadeOut();
						$('.edit_address').prop('disabled', false);
						$('.'+data.class+' address').html(data.html);
						$('#order_edit').modal('hide');
						$('#order_edit .modal-body div.row').html('');
						
						if(data.type == 'shipping_add'){
							$('.sub_total').html('$'+data.sub_total);
							if(Number(data.sales_tax) > 0)
								$('.tax').html('$'+data.sales_tax).closest('tr.sales_tax').show();
							else
								$('.tax').closest('tr.sales_tax').hide();
							$('.discount').html('$'+data.discount);
							$('.shipping input').val(data.shipping_fee);
							$('.main_total').html('$'+data.main_total);
						}
					}
				}
			});
		}
	});
	
	$(document).on('click','.fa-pencil-square-o.options',function(e){
		$('#order_edit .modal-header h4').html('Edit Order Options');
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var order_product_id = $(this).attr('order-product-id');
		var order_qty = $(this).attr('order-product-qty');
		var order_item = $(this).attr('order-item');
		
		$.ajax({
			url:'{{url("admin/order/order_option_edit")}}',
			type:'post',
			data:{'type':type,'get':'form','order_id':order_id,'order_product_id':order_product_id,'order_item':order_item,'order_qty':order_qty},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('#order_edit .modal-body div.row').html(data.html);
					$('#order_edit').modal('show');
					
					combinations = data.combination;
					discount_arr = data.discount;
					
					min_width = Number($('input[name="min_width"]').val());
					max_width = Number($('input[name="max_width"]').val());

					min_height = Number($('input[name="min_height"]').val());
					max_height = Number($('input[name="max_height"]').val());
					
					orderObj.product_id = data.product_id;
					orderObj.order_product_id = data.order_product_id;
					orderObj.quantity=Number($('input[name="quantity"]').val());
					orderObj.price_default=Number($('input[name="price_default"]').val());
					orderObj.price_sqft_area = Number(data.price_sqft_area);
					orderObj.variant = Number(data.variant_check);
					orderObj.show_width_height = Number(data.show_width_height);
					orderObj.productMinPrice = Number(data.min_price);
					if(orderObj.show_width_height == 1){
						orderObj.width = Number($('input#width').val());
						orderObj.height = Number($('input#height').val());
						orderObj.productMinSQFT = Number(data.min_sqft);
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
						comb_str += $('option:selected', this).val()+'_';
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
					orderObj.product_weight = data.product_weight;
					
					orderObj.shipping_type = data.shipping_type;
					orderObj.shipping_min_value = data.shipping_min_value;
					orderObj.shipping_weight = data.shipping_weight;
					orderObj.shipping_price = data.shipping_price;

					$.each(discount_arr,function(index,val){
						if(index <= orderObj.quantity)
							orderObj.discount_per=Number(val);
					});
					
					$('.option_custom').each(function () {
						var sid = $(this).attr('id');
						
						custom_option = new Object();

						custom_option.id = $(this).attr('data-id');
						custom_option.name = $(this).attr('data-name');
						
						if($(this).attr('data-type') == 1){
							custom_option.value = $('#'+sid+' :selected').attr('value');
							custom_option.price = parseFloat($('#'+sid+' :selected').attr('data-price'));
							custom_option.weight = parseFloat($('#'+sid+' :selected').attr('data-weight'));
							custom_option.flat_rate_additional_price = parseFloat($('#'+sid+' :selected').attr('data-flat_rate_additional_price'));
							custom_option.price_type = $('#'+sid+' :selected').attr('data-price-type');
						}else{
							custom_option.value = $('#'+sid).attr('value');
							custom_option.price = parseFloat($('#'+sid).attr('data-price'));
							custom_option.weight = parseFloat($('#'+sid).attr('data-weight'));
							custom_option.price_type = $('#'+sid).attr('data-price-type');
						}
						orderObj.custom_option[custom_option.id]= custom_option;

					});

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
				}
			}
		});
	});
	
	$(document).on('click','.edit_option',function(e){
		var on = 1;
		$('form#edit_product_option .validate').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				on = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		});
		
		// check if product total is less them then min. sale price
		
		if((orderObj.width*orderObj.height) < orderObj.productMinSQFT){
			alert("The area of your order does not meet our minimum area which is "+orderObj.productMinSQFT+" sqft. Please adjust your order width and height.");
			on = 0;
		}else if(Number(orderObj.total) < Number(orderObj.productMinPrice))
		{
			alert("The size for your order does not meet our minimum requirements. Please adjust your order or call us at {{config('constants.store_phone_number')}} to discuss your order.");
			on = 0;
		}
		// end if product total check with min sale price
		
		if(on){

			var form_data = $('form#edit_product_option').serialize();
			$.ajax({
				url:'{{url("admin/order/order_option_edit")}}',
				type:'post',
				data:{'get':'form_save','data':form_data,'object':orderObj},
				dataType:'json',
				beforeSend: function() {
					$('#edit_option_loader_img').fadeIn();
					$('.edit_option').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#order_edit').modal('hide');
						$('#order_edit .modal-body div.row').html('');
						$('#edit_option_loader_img').fadeOut();
						$('.edit_option').prop('disabled', false);
						$('.product_options_'+data.class).html(data.html);
						if(data.order_product_id == "" || data.order_product_id == '{{config('constants.CUSTOM_PRODUCT_ID')}}')
						{
							return true;
						}
						$('.div_rate_'+data.class).html('$'+data.default_price);
						$('.div_gross_price_'+data.class).html('$'+data.gross_price);
						$('.div_gross_total_'+data.class).html('$'+data.gross_total);
						$('.div_qty_discount_'+data.class).html('$'+data.qty_discount);
						$('.div_total_'+data.class).html('$'+data.product_total);
						
						if(Number(data.sales_tax) > 0 && Number(data.sales_tax) != ''){
							$('.tax').html('$'+data.sales_tax);
							$('.tax').closest('tr').fadeIn(500);
						}else{
							$('.tax').closest('tr').fadeOut(500);
						}

                        $('.discount').html('$'+data.discount);
                        // reapply coupon code if it exists
                        if ($('#coupon_code').val() != '') {
                            applyCoupon();
                        }

						$('.sub_total').html('$'+data.sub_total);
						$('.shipping input').val(data.shipping_fee);
						$('.main_total').html('$'+data.main_total);
					}
				}
			});
		}
	});
	
	var counter = 1;
	$(document).on('click','.add_option',function(){
		var str = '<div id="main_div_'+counter+'" class="col-xs-12 main_option_div">';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Name<span class="text-danger">*</span></label><input class="form-control validate" name="new_option['+counter+'][name]" value="" placeholder="Enter Option Name" required/></div>';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Value<span class="text-danger">*</span></label><input class="form-control validate" name="new_option['+counter+'][value]" value="" placeholder="Enter Option Value" required/></div><div class="clearfix"></div>';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Price<span class="text-danger">*</span></label><input class="form-control validate" name="new_option['+counter+'][price]" value="" placeholder="Enter Option Price" required/></div>';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Group Field<span class="text-danger">*</span></label><select class="form-control validate" name="new_option['+counter+'][field_group]" required>';
		
		str += '<option value="">Select Group Field</option>';
		str += '<option value="printing">Printing Options</option>';
		str += '<option value="finishing">Finishing Options</option>';
		str += '<option value="production">Design Service Options</option>';
		
		str += '</select></div><div class="clearfix"></div>';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Calculation format<span class="text-danger">*</span></label><select class="form-control validate" name="new_option['+counter+'][price_formate]" required>';
		
		str += '<option value="">Select price calculation format</option>';
		str += '<option value="area">Price by sqft area</option>';
		str += '<option value="parimeter">Price by sqft parimeter</option>';
		str += '<option value="item">Price by item</option>';
		str += '<option value="line_item">Price by line item</option>';
		
		str += '</select></div>';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Weight</label><input class="form-control" name="new_option['+counter+'][weight]" value="" placeholder="Enter Option Weigth"/></div><div class="clearfix"></div>';
		
		str += '<div class="col-xs-6 form-group"><label class="form-control-label">Flat Rate Additional Price</label><input class="form-control" name="new_option['+counter+'][flat_rate_additional_price]" value="" placeholder="Flat Rate Additional Price"/></div>';
		
		str += '<div class="col-xs-6 form-group"><label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-new-option pull-right" rel="'+counter+'" type="button"><i class="fa fa-minus"></i></button></div>';
		
		str += '</div>';
		
		$('div.extra_option_div').append(str);
		$('div.extra_option_div').removeClass('hide');
		
		counter++;
	});
	
	$(document).on('click','.remove-new-option',function(){
		var new_option_id = $(this).attr('rel');
		$('#main_div_'+new_option_id).remove();	

		if($('.extra_option_div div').length == 0)
		{
			$('div.extra_option_div').addClass('hide');
		}
	});
	
	$(document).on('click','.project_comment_btn',function(e){
		var key = $(this).attr('data-id');
		var type = $(this).attr('data-type');
		var data_btn = $(this).attr('data-btn');
		var data = $(this).attr('data-value');
		var str = '<form name="project_comment_form" id="project_comment_form">';
		
		str += '<input type="hidden" name="id" value="'+key+'"/>';
		str += '<input type="hidden" name="data_btn" value="'+data_btn+'"/>';
		str += '<input type="hidden" name="field" value="'+type+'"/>';
		
		if(type == 'project_name'){
			$('#order_edit .modal-header h4').html('Product Project Name');
			str += '<input type="hidden" name="key_id" value="project_'+key+'"/>';
			str += '<div class="col-xs-6"><div class="form-group"><label>Enter Project Name</label><input type="text" class="form-control" name="field_value" id="field_value" value="'+data+'" placeholder="Enter Project Name"/></div></div>';
		}else{
			var label = 'Enter Comment';
			$('#order_edit .modal-header h4').html('Product Comment');
			str += '<input type="hidden" name="key_id" value="comment_'+key+'"/>';
			str += '<div class="col-xs-12"><div class="form-group"><label>Enter Comment</label><textarea class="form-control" name="field_value" id="field_value" placeholder="Enter Comment">'+data+'</textarea></div></div>';
		}
		
		str += '<div class="col-xs-12"><div class="form-group">{{ Form::button("Set",["class"=>"btn btn-success project_comment_submit"])}}';
		
		str += '<img id="project_comment_loader" class="loader_img" src="'+"{{url('public/img/loader/Spinner.gif')}}"+'" style="width:50px;display:none;" /></div></div></form>';
		
		$('#order_edit .modal-body div.row').html(str);
		$('#order_edit').modal('show');
	});
	
	$(document).on('click','.project_comment_submit',function(){
		if($('#field_value').val() == ''){
			$('#field_value').closest('div').addClass('has-error');
			$("<span class='help-block'>This field is required</span>").insertAfter($('#field_value'));
		}else{
			$('#field_value').closest('div').removeClass('has-error');
			$('#field_value').nextAll().remove();
			
			$.ajax({
				url:'{{url("admin/order/savecomment")}}',
				type:'post',
				dataType:'json',
				data:$('form#project_comment_form').serialize(),
				beforeSend: function() {
					$('#project_comment_loader').fadeIn();
					$('.project_comment_submit').prop('disabled',true);
				},
				success:function(data){
					$('#project_comment_loader').fadeOut();
					$('.project_comment_submit').prop('disabled',false);
					if(data.status == 'success'){
						if(data.data_btn == 'add'){
							if(data.field == 'project_name'){
								var str =  '<div class="col-sm-2 no-padding"><strong>Project Name:</strong></div>';
								str += '<div class="col-sm-10" style="overflow-x: auto;max-height: 300px;overflow-y: auto;"><div class="no-padding"><i class="fa fa-pencil-square-o project_comment_btn pull-left" data-value="'+data.res+'" data-id="'+data.id+'" data-type="project_name" data-btn="edit" id="project_'+data.id+'"></i></div><div class="val_div_project_'+data.id+'">'+data.res+'</div></div>';
							}else{
								var str =  '<div class="col-sm-2 no-padding"><strong>Comment:</strong></div>';
								str += '<div class="col-sm-10" style="overflow-x: auto;max-height: 300px;overflow-y: auto;"><div class="no-padding"><i class="fa fa-pencil-square-o project_comment_btn pull-left" data-value="'+data.res+'" data-id="'+data.id+'" data-type="comments" data-btn="edit" id="comment_'+data.id+'"></i></div><div class="val_div_comment_'+data.id+'">'+data.res+'</div></div>';
							}
							$('.div_'+data.key_id).html(str);
							$('#order_edit').modal('hide');
						}else{
							$('#'+data.key_id).attr('data-value',data.res);
							$('.val_div_'+data.key_id).html(data.res);
							$('#order_edit').modal('hide');
						}
					}else{
						alert("Value not save successfully,Please try again.");
					}
				}
			});
		}
	});
	
	$(document).on('click','.fa-pencil-square-o.product_values',function(e){
		$('#order_edit .modal-header h4').html('Edit Order Values');
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var order_product_id = $(this).attr('order-product-id');
		var order_value = $(this).attr('order-product-value');
		var order_item = $(this).attr('order-item');
		
		$.ajax({
			url:'{{url("admin/order/order_values_edit")}}',
			type:'post',
			data:{'type':type,'get':'form','order_id':order_id,'order_product_id':order_product_id,'order_item':order_item,'order_value':order_value},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('#order_edit .modal-body div.row').html(data.html);
					$('#order_edit').modal('show');
				}
			}
		});
	});
	
	$(document).on('click','.edit_order_product_values',function(e){
		var form_data = $('form#edit_order_values').serialize();
		$.ajax({
			url:'{{url("admin/order/order_values_edit")}}',
			type:'post',
			data:{'get':'form_save','data':form_data},
			dataType:'json',
			beforeSend: function() {
				$('#edit_product_values_loader_img').fadeIn();
				$('.edit_order_product_values').prop('disabled', true);
			},
			success:function(data){
				if(data.status == 'success'){
					$('#edit_product_values_loader_img').fadeOut();
					$('.edit_order_product_values').prop('disabled', false);
					
					$('.div_qty_'+data.class).html(data.qty);
					$('.div_qty_edit_'+data.class+' i').attr('order-product-value',data.qty);
					//$('.div_rate_'+data.class).html('$'+data.default_price);
					$('.div_gross_price_'+data.class).html('$'+data.gross_price);
					$('.div_gross_price_edit_'+data.class+' i').attr('order-product-value',data.gross_price);
					$('.div_gross_total_'+data.class).html('$'+data.gross_total);
					$('.div_qty_discount_'+data.class).html('$'+data.qty_discount);
					$('.div_qty_discount_edit_'+data.class+' i').attr('order-product-value',data.qty_discount);
					$('.div_total_'+data.class).html('$'+data.product_total);
					
					$('.sub_total').html('$'+data.sub_total);
					$('.discount').html('$'+data.discount);
					$('.shipping input').val(data.shipping_fee);
					$('.main_total').html('$'+data.main_total);
					$('#shipping_amount').attr('data-subtotal',data.sub_total);
					
					if(Number(data.sales_tax) > 0 && Number(data.sales_tax) != ''){
						$('.tax').html('$'+data.sales_tax);
						$('.tax').closest('tr').fadeIn(500);
					}else{
						$('.tax').closest('tr').fadeOut(500);
					}

					// reapply coupon code if it exists
                    if ($('#coupon_code').val() != '') {
                        applyCoupon();
                    }
					
					$('#order_edit').modal('hide');
					$('#order_edit .modal-body div.row').html('');
				}
			}
		});
		
	});
	
	$(document).on('keyup input change','input[name="quantity"],#width,#height',function(e) {
		if($(this).val() < 1)
			$(this).val('1');
		
		$(this).closest('.form-group').removeClass('has-error');
		$($(this)).nextAll().remove();
		//alert($(this).val())
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
			if(on != 0){
				orderObj.width = width;
				var sqft_area = orderObj.width * orderObj.height;
				sqft_area = Number(sqft_area);
				calculate_price(orderObj.comb_str,sqft_area);
				calculation();
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
			if(on != 0){
				orderObj.height = height;
				var sqft_area = orderObj.width * orderObj.height;
				sqft_area = Number(sqft_area);
				calculate_price(orderObj.comb_str,sqft_area);
				//check_price();
				calculation();
			}
		}
		
		else if(eid=='quantity'){
			
		}
		
		if(on == 0){
			$('.edit_option').prop('disabled',true);
		}else{
			$('.edit_option').prop('disabled',false);			
		}
		
	});
	
	function calculate_price(comb_str, area){
		//alert(area)

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
						//alert(v.min_area+"  "+v.max_area+'  '+v.price)
						if(area > Number(v.min_area) && area <= Number(v.max_area)){
							orderObj.variant_price = Number(v.price);
                            orderObj.variant_option_price = Number(v.price);
							$('input[name="price_default"]').val(Number(v.price));
							change = 1;
						}
					});
					if(change == 0){
						$.each(value.price, function (i, v) {
							if(i == first_key){
								orderObj.variant_price = Number(v.price);
                                orderObj.variant_option_price = Number(v.price);
								$('input[name="price_default"]').val(Number(v.price));
							}
						});
					}
				}else{
					orderObj.variant_price = Number(value.price);
					orderObj.variant_option_price = Number(value.price);
					$('input[name="price_default"]').val(Number(value.price));
				}
			}
		});
	}
	
	function calculation(){	
		var gross_price = orderObj.variant_price * (orderObj.width * orderObj.height);

		$.each(orderObj.custom_option, function (cid, coption) {
			if(coption.price_type == "gross")
			{
				gross_price = gross_price + ( coption.price * orderObj.quantity ) ;
			}
			else if(coption.price_type == "area")
			{
				gross_price = gross_price + ( (orderObj.width * orderObj.height) * coption.price );
			}
			else if(coption.price_type == "parimeter")
			{
				gross_price = gross_price + ( ((orderObj.width + orderObj.height) * 2 ) * coption.price );
			}
			else if(coption.price_type == "item" || coption.price_type == "line_item")
			{
				gross_price = (gross_price + coption.price );
			}
		});
			
		gross_price = gross_price.toFixed(2);

		if(orderObj.productMinPrice != 0 && gross_price < orderObj.productMinPrice){
			orderObj.gross_price = orderObj.productMinPrice.toFixed(2);	
		}else{
			orderObj.gross_price = gross_price;	
		}

		orderObj.gross_total = (orderObj.gross_price * orderObj.quantity).toFixed(2);	

		orderObj.qty_discount = (orderObj.gross_total *  orderObj.discount_per)/100;
		orderObj.total =  orderObj.gross_total - orderObj.qty_discount;
		orderObj.total =  orderObj.total .toFixed(2);
		
		if(orderObj.productMinPrice != 0 && orderObj.total < orderObj.productMinPrice){
			orderObj.total =  orderObj.productMinPrice.toFixed(2);
		}

		return true;		
	}
	
	$(document).on('change','.option_custom',function(){

		var sid=$(this).attr('id');

		custom_option = new Object();

		custom_option.id = $(this).attr('data-id');
		custom_option.name = $(this).attr('data-name');		
		
		if($(this).attr('data-type') == 1){
			custom_option.value = $('#'+sid+' :selected').attr('value');
			custom_option.price = parseFloat($('#'+sid+' :selected').attr('data-price'));
			custom_option.weight = parseFloat($('#'+sid+' :selected').attr('data-weight'));
			custom_option.price_type = $('#'+sid+' :selected').attr('data-price-type');
			custom_option.flat_rate_additional_price = parseFloat($('#'+sid+' :selected').attr('data-flat_rate_additional_price'));
		}else{
			custom_option.value = $('#'+sid).attr('value');
			custom_option.price = parseFloat($('#'+sid).attr('data-price'));
			custom_option.weight = parseFloat($('#'+sid).attr('data-weight'));
			custom_option.price_type = $('#'+sid).attr('data-price-type');
		}
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
        calculation();
	});

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

	function applyCoupon(){
		var code = $('#coupon_code').val();
		if(code != ''){
			$('#coupon_code').closest('.form-group').removeClass('has-error');
			$('#coupon_code').closest('.input-group').nextAll().remove();
			$.ajax({
					url:'{{url("/admin/order/edit/applycoupon")}}',
					type:'post',
					dataType:'json',
					data:{"_token": "{{ csrf_token() }}",'code':code,'order_id':'{{$order->id}}'},
					success:function(data){
						if(data.status == 'success'){
							if(data.code_apply == 1){
								$("<span class='text-success'>"+data.msg+"</span>").insertAfter($('#coupon_code').closest('.input-group'));
								
								if(Number(data.sales_tax) > 0 && Number(data.sales_tax) != ''){
									$('.tax').html('$'+data.sales_tax);
									$('.tax').closest('tr').fadeIn(500);
								}else{
									$('.tax').closest('tr').fadeOut(500);
								}
													
								$('.sub_total').html('$'+data.sub_total);
								$('.discount').html('$'+data.discount_amount);
								$('.shipping input').val(data.shipping_fee);
								$('.main_total').html('$'+data.total);
								
							}else{
								$('#coupon_code').closest('.form-group').addClass('has-error');
								$("<span class='help-block'>"+data.msg+"</span>").insertAfter($('#coupon_code').closest('.input-group'));
							}
						}
					}
				});
		}else{
			$('#coupon_code').closest('.form-group').addClass('has-error');
			$('#coupon_code').closest('.input-group').nextAll().remove();
			$("<span class='help-block'>This field is required</span>").insertAfter($('#coupon_code').closest('.input-group'));
		}
	}

	$('select[name="shipping_option"]').change(function(event){
		if($(this).val() != '' ){
		    var order_id = $('.select_save_all').attr('order-id');
		    $('input[name="shipping_type"]').val(this.value);
			$.ajax({
				url:'{{url("admin/order/calculate_shipping")}}/' + order_id + '/' + $(this).val(),
				dataType:'json',
				beforeSend: function() {

				},
				success:function(data){
                    $('#shipping_amount').val(data.MonetaryValue);
                    total();
				}
			});
		}
	});
</script>