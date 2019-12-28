<div class="modal fade" id="po_edit">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Edit Order</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="new_item">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add New Line Item</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-4 form-group">
						<label class="form-control-label">Item Name<span class="text-danger">*</span></label>
						<input class="form-control validate" name="new_item_name" id="new_item_name" value="" placeholder="Enter Item Name"/>
					</div>
					<div class="col-xs-4 form-group">
						<label class="form-control-label">Item Qty<span class="text-danger">*</span></label>
						<input class="form-control validate" type="number" name="new_item_qty" id="new_item_qty" value="" placeholder="Enter Item Quantity"/>
					</div>
					<div class="col-xs-4 form-group">
						<label class="form-control-label">Item Rate<span class="text-danger">*</span></label>
						<input class="form-control validate" type="number" name="new_item_rate" id="new_item_rate" value="" placeholder="Enter Item Rate"/>
					</div>
					<div class="col-xs-12 form-group">
						<label class="form-control-label">Item Description<span class="text-danger">*</span></label>
						<textarea class="form-control validate" name="new_item_description" id="new_item_description" value="" placeholder="Enter Item Description"></textarea>
					</div>
					<div class="clearfix"></div>
					<div class="col-xs-2 form-group">
						<label>&nbsp;</label>
						<div class="clearfix"></div>
						<button class="btn btn-success add_item" type="button">Add Item</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var vendors = <?php echo json_encode($vendors->keyBy('id')->toArray(),JSON_PRETTY_PRINT);?>;
	
	$(document).on('click','.fa-trash.product_delete',function(e){		
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var order_product_id = $(this).attr('order-product-id');
		var order_product_name = $(this).attr('order-product-name');
		var custom_line_item = $(this).hasClass('custom_line_item')?1:0;

		var line_item_id = $(this).closest('tr.po_line_item').attr('rel');
				
		$.ajax({
			url:'{{url("admin/order/po/product_delete")}}',
			type:'post',
			data:{'_token': '{{csrf_token()}}','order_id':order_id,'order_product_id':order_product_id,'order_product_name':order_product_name,'vendor':$('#vendor').val(),'custom_line_item':custom_line_item},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('#subtotal_amount').html('$'+data.subtotal);
					$('#total_amount').html('$'+data.total);
					$('tr.po_line_item_'+line_item_id).remove();					
				}
			}
		});
	});

	$(document).on('click','.fa-pencil-square-o.options',function(e){
		$('#po_edit .modal-header h4').html('Edit Options');
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var order_product_id = $(this).attr('order-product-id');
		var order_product_name = $(this).attr('order-product-name');

		var custom_line_item = $(this).hasClass('custom_line_item')?1:0;
				
		$.ajax({
			url:'{{url("admin/order/po/option_edit")}}',
			type:'post',
			data:{'order_id':order_id,'order_product_id':order_product_id,'order_product_name':order_product_name,'vendor':$('#vendor').val(),'custom_line_item':custom_line_item},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('div#po_edit div.modal-body div.row').html(data.html);
					$('#po_edit').modal('show');
				}
			}
		});
	});
	
	$(document).on('click','.edit_option',function(e){
		var check = 1;
		
		$('.option_custom').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				check = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		});
		
		if(check){
			var form_data = $('form#edit_po_option').serialize();
			$.ajax({
				url:'{{url("admin/order/po/option/save")}}',
				type:'post',
				data:form_data,
				dataType:'json',
				beforeSend: function() {
					$('#edit_option_loader_img').fadeIn();
					$('.edit_option').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('div strong.poProductName'+data.product_id).html(data.product_name);
						$('div.product_options_'+data.product_id).html(data.html);
						$('#po_edit').modal('hide');
					}
				}
			});
		}
	});
	
	$(document).on('click','.fa-pencil-square-o.address',function(e){
		$('#po_edit .modal-header h4').html('Edit Shipping Address');
		var order_id = $(this).attr('order-id');
		var order_multiple = $(this).attr('order-multiple');
		var order_product_id = 0;
		if(order_multiple != 0){
			order_product_id = $(this).attr('order-product-id');
		}
		
		$.ajax({
			url:'{{url("admin/order/po/address_edit")}}',
			type:'post',
			data:{'order_id':order_id,'order_product_id':order_product_id,'order_multiple':order_multiple},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('div#po_edit div.modal-body div.row').html(data.html);
					$('#po_edit').modal('show');
				}
			}
		});
	});

	$(document).on('change','select#saved_address',function(e){		
		var order_id = $(this).attr('order-id');
		var order_multiple = $(this).attr('order-multiple');
		var order_product_id = 0;
		if(order_multiple != 0){
			order_product_id = $(this).attr('order-product-id');
		}
		var address_id = $(this).val();
        var vendor_id = $('select[name="vendor"]').val()
		
		$.ajax({
			url:'{{url("admin/order/po/address_edit")}}',
			type:'post',
			data:{'order_id':order_id,'order_product_id':order_product_id,'order_multiple':order_multiple,'address_id':address_id, 'vendor_id':vendor_id},
			dataType:'json',
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					$('div#po_edit div.modal-body div.row').html(data.html);
					$('#po_edit').modal('show');
				}
			}
		});
	});
	
	$(document).on('click','.edit_address',function(e){
		var check = 1;
		
		$('.validate_add').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				check = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		});
		
		if(check){
			var form_data = $('form#edit_add').serialize();
			$.ajax({
				url:'{{url("admin/order/po/address/save")}}',
				type:'post',
				data:form_data,
				dataType:'json',
				beforeSend: function() {
					$('#edit_add_loader_img').fadeIn();
					//$('.edit_address').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						if(data.order_multiple != 0 || data.order_multiple != '0'){
							$('#shipp_add_'+data.order_product_id).html(data.str);
						}else{
							$('#shipp_add').html(data.str);
						}
						$('#po_edit').modal('hide');
					}
				}
			});
		}
	});
	
	
	var counter = 1;
	$(document).on('click','.add_option',function(){
		var str = '<div id="main_div_'+counter+'"><div class="col-xs-5 form-group"><label class="form-control-label">Option Name</label><input class="form-control option_custom" name="new_option['+counter+'][name]" value="" placeholder="Enter Option Name"/></div><div class="col-xs-5 form-group"><label class="form-control-label">Option Value</label><input class="form-control option_custom" name="new_option['+counter+'][value]" value="" placeholder="Enter Option Value"/></div><div class="col-xs-2 form-group"><label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-new-option" rel="'+counter+'" type="button"><i class="fa fa-minus"></i></button></div></div>';
		
		$('div.extra_option_div').append(str);
		$('div.extra_option_div').removeClass('hide');
		
		counter++;
	});
	
	var new_item = 1;
	$(document).on('click','.add_line_item',function(){
		$('#new_item').modal('show');
	});
	
	$(document).on('click','.add_item',function(){
		var on = 1;
		
		$('#new_item .validate').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				on = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		});
		
		if(on){
			var new_item_name = $('#new_item_name').val();
			var new_item_description = $('#new_item_description').val();
			var new_item_qty = $('#new_item_qty').val();
			var new_item_rate = $('#new_item_rate').val();
			var new_item_amount = new_item_qty*new_item_rate;
			var str = '<tr class="additional_row" data-no="'+new_item+'">';
			str += '<td><div class="col-xs-12 no-padding"><input name="new_item['+new_item+'][name]" class="form-control" placeholder="New Item Name" value="'+new_item_name+'"/></div><div class="col-xs-12 no-padding" style="margin-top:10px;"><textarea name="new_item['+new_item+'][description]" class="form-control" placeholder="New Item Description">'+new_item_description+'</textarea></div></td>';
			str += '<td><div class="col-xs-12 no-padding"><input type="number" name="new_item['+new_item+'][qty]" class="form-control new_item_change" min="1" data-type="qty" data-product-id="'+new_item+'" placeholder="Quantity" value="'+new_item_qty+'"/></div></td>';
			str += '<td><div class="col-xs-12 no-padding"><input type="number" name="new_item['+new_item+'][rate]" class="form-control new_item_change" min="1" data-type="rate" data-product-id="'+new_item+'" placeholder="Vendor Rate" value="'+new_item_rate+'"/></div></td>';
			str += '<td><input type="hidden" class="new_item_amount" name="new_item['+new_item+'][amount]" value="'+new_item_amount+'" data-no="'+new_item+'"/><div class="col-xs-12 no-padding new_item_amount_'+new_item+'">$'+formatMoney(new_item_amount)+'</div></td>';
			@if($order->multiple_shipping != 0)
				str += '<td colspan="3"></td>';
			@else
				str += '<td colspan="2"></td>';
			@endif
			
			str += '</tr>';
			
			$('tr.additional_line_item').before(str);
			
			$('#new_item_name').val('');
			$('#new_item_qty').val('');
			$('#new_item_rate').val('');
			$('#new_item').modal('hide');
			
			total();
			
			new_item++;
		}
	});
	
	$( ".due_date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'M-d-yy'
	});
	
	var total_amount = 0;
	
	$(document).on('blur','.val_change',function(){
		$(this).closest('.form-group').removeClass('has-error');
		$(this).nextAll().remove();
		
		var product_id = $(this).attr('data-product-id');
		var type = $(this).attr('data-type');
		var qty = 0;
		var rate = 0;
		if(type == 'qty'){
			qty = Number($(this).val());
			rate = Number($('input[name="rate['+product_id+']"]').val());
		}else{
			rate = Number($(this).val());
			qty = Number($('input[name="qty['+product_id+']"]').val());
		}
		
		var amount = Number(rate*qty).toFixed(2);
		$('input[name="amount['+product_id+']"]').val(amount);
		$('.amount_div_'+product_id).html('$'+formatMoney(amount));
		total();
	});
	
	$(document).on('blur','.new_item_change',function(){
		$(this).closest('.form-group').removeClass('has-error');
		$(this).nextAll().remove();
		
		var product_id = $(this).attr('data-product-id');
		var type = $(this).attr('data-type');
		var qty = 0;
		var rate = 0;
		if(type == 'qty'){
			qty = Number($(this).val());
			rate = Number($('input[name="new_item['+product_id+']"[rate]]').val());
		}else{
			rate = Number($(this).val());
			qty = Number($('input[name="new_item['+product_id+'][qty]"]').val());
		}
		
		var amount = Number(rate*qty).toFixed(2);
		$('input[name="new_item['+product_id+'][amount]"]').val(amount);
		$('.new_item_amount_'+product_id).html('$'+formatMoney(amount));
		total();
	});
	
	$(document).on('blur','#shipping_amount',function(){
		total();
	});
	
	function total(){
		total_amount = 0;
		$('input.amount').each(function(){
			total_amount += Number($(this).val());
		});
		if($('.additional_row').length){
			$('input.new_item_amount').each(function(){
				total_amount += Number($(this).val());
			});
		}
		
		total_amount = Number(total_amount).toFixed(2);
		$('#po_sub_total').val(total_amount);
		$('#subtotal_amount').html('$'+formatMoney(total_amount));
		total_amount = Number(total_amount)+Number($('#shipping_amount').val());
		
		total_amount = Number(total_amount).toFixed(2);
		$('#total_amount').html('$'+formatMoney(total_amount));
	}
	
	//total();
	
	//function po_mail(){
	$('.save_po').click(function(){
		var check = 1;
		$('.val_change').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				check = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		});
		
		// Below code for validate due date //

		/* $('.due_date').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				check = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		}); */

		$('.shipping_option').each(function(){
			$(this).closest('.form-group').removeClass('has-error');
			$(this).nextAll().remove();
			if($(this).val() == ''){
				check = 0;
				$(this).closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter($(this));
			}
		});
		
		if(check == 0){
			return false;
		}else{
			$.ajax({
				url:'{{url("admin/order/po/save_po")}}',
				type:'post',
				dataType:'json',
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				data:$('form#po_form').serialize(),
				success:function(data){
					if(data.status == 'success'){
						$('div.top_btns').removeClass('hide');
					}
				}
			});
		}
		return false;
	});
	
	/* $(document).on('change','#terms',function(){
		if($(this).val() == 3){
			$('#new_terms').closest('.form-group').removeClass('hide');
		}else{
			$('#new_terms').closest('.form-group').addClass('hide');
		}
	}); */

	$(document).on('change','#vendor',function(){
		/* var companyName = $(this).attr('data-company');
		var name = $(this).attr('data-name');
		var companyAddress = $(this).attr('data-address'); */
		var selectedVendor = $(this).val();
		var str = null;
		$.each(vendors,function(k,v){
			if(k == selectedVendor){
				if(v.company_name != null)
					str = '<strong>Company : </strong>'+v.company_name+'<br/>';
				else
					str = '<strong>Company : </strong> <br/>';
				if(v.name != null)
					str += '<strong>Name : </strong>'+v.name+'<br/>';
				else
					str += '<strong>Name : </strong> <br/>';
				if(v.company_address != null)
					str += '<strong>Address : </strong>'+v.company_address;
				else
					str += '<strong>Address : </strong> ';
			}				
		});
		
		if(str != null)
			$('.vendor_detail').html(str);
	})
	
	$(document).on('click','.vendor_change',function(){
		var order_id = $(this).attr('order-id');
		var po_id = '{{$id}}';
		
		if($('select[name="vendor"]').val() == ''){
			$('select[name="vendor"]').closest('div').addClass('has-error');
			$("<span class='help-block'>this is required</span>").insertAfter('select[name="vendor"]');
		}else{
			$('select[name="vendor"]').closest('div').removeClass('has-error');
			$('select[name="vendor"]').nextAll().remove();
			var vendor_id = $('select[name="vendor"]').val();
			
			$.ajax({
				url:'{{url("admin/order/po/change_vendor")}}',
				type:'post',
				dataType:'json',
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				data:{'vendor_id':vendor_id,'order_id':order_id,'po_id':po_id},
				success:function(data){
					if(data.status == 'success'){
						//$('.vendor_detail').html(data.detail);
						window.location.href = '{{url("admin/order/po/")}}/'+po_id;
					}
				}
			});
		}
		
	});
	
	$(document).on('click','.select_save',function(){
		var type = $(this).attr('data-type');
		var order_id = $(this).attr('order-id');
		var on = 1;
		if(type == 'terms'){
			$('#terms').closest('.form-group').removeClass('has-error');
			$('#terms').nextAll().remove();
			$('#new_terms').closest('.form-group').removeClass('has-error');
			$('#new_terms').nextAll().remove();
			var term_value = $('#terms').val();
			var new_term_value = 0;
			if(term_value != ''){
				if(term_value == 3){
					new_term_value = $('#new_terms').val();
					if(new_term_value == ''){
						$('#new_terms').closest('.form-group').addClass('has-error');
						$("<span class='help-block'>this is required</span>").insertAfter("#new_terms");
						on = 0;
					}
				}
			}else{
				$('#terms').closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter("#terms");
				on = 0;
			}
			if(on){
				$.ajax({
					url:'{{url("admin/order/order_changes")}}',
					type:'post',
					dataType:'json',
					beforeSend: function () {
					  $.blockUI();
					},
					complete: function () {
					  $.unblockUI();
					},
					data:{'type':'terms','order_id':order_id,'terms':term_value,'new_terms':new_term_value},
					success:function(data){
						
					}
				});
			}
		}
		else{
			$('#representative').closest('.form-group').removeClass('has-error');
			$('#representative').nextAll().remove();
			var agent = $('#representative').val();
			if(agent == ''){
				$('#representative').closest('.form-group').addClass('has-error');
				$("<span class='help-block'>this is required</span>").insertAfter("#representative");
				on = 0;
			}
			if(on){
				$.ajax({
					url:'{{url("admin/order/order_changes")}}',
					type:'post',
					dataType:'json',
					beforeSend: function () {
					  $.blockUI();
					},
					complete: function () {
					  $.unblockUI();
					},
					data:{'type':'agent','order_id':order_id,'agent':agent},
					success:function(data){
						
					}
				});
			}
		}
	});
	
	$(document).on('click','.delete_option',function(){
		var product_id = $(this).attr('data-product-id');
		var option_id = $(this).attr('data-id');
		//alert($(this).closest('div.main_option_div').html());
		if(confirm("Are you sure to delete this options ?")){
			$.ajax({
				url:'{{url("admin/order/po/delete_option")}}',
				type:'post',
				dataType:'json',
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				data:{'order_id':$('#order_id').val(),'order_product_id':product_id,'option_id':option_id},
				success:function(data){
					if(data.status == 'success'){
						$('div.product_options_'+data.product_id).html(data.html);
						$('#option_div_'+option_id).remove();
						
						$('.main_option_div').each(function(){
							if($(this).html() == ''){
								$(this).closest('div.form-group').addClass('hide');
							}
						});
					}
				}
			});
		}
	});

	$(document).on('click','.remove-new-option',function(){
		var new_option_id = $(this).attr('rel');
		$('#main_div_'+new_option_id).remove();	

		if($('.extra_option_div div').length == 0)
		{
			$('div.extra_option_div').addClass('hide');
		}
	});
</script>	