<div class="modal fade" id="customer_status">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-snowflake-o"></i> Customer Status</h4>
			</div>
			<div class="modal-body">
				<div class="row customer_status_div">
					<div class="col-md-12">
						{{ Form::model('customer_status_change', ['url' => ['admin/order/list'],'files'=>true,'id'=>'customer_status_change']) }}
							{{ Form::hidden('customer_order_id','',['id'=>'customer_order_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_customer_status', 'Select Status',array('class'=>'form-control-label'))}}	
									{{ Form::select('select_customer_status',['Select Status']+config('constants.customer_status'),'',['class'=>'form-control','id'=>'select_customer_status'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set Status',['class'=>'btn btn-success set_customer_status'])}}
									<img id="customer_status_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.customer_status',function(e){
		var id = $(this).attr('data');
		var order_id = $(this).attr('order-id');
		$('#customer_order_id').val(order_id);
		if(id != 0){
			$("#select_customer_status option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_customer_status option:eq(0)').prop('selected', 'selected');
		}
		$('#customer_status').modal('show');
	});
	
	$(document).on('click','.set_customer_status',function(e){
		if($('form#customer_status_change select').val() != 0){
			$('form#customer_status_change select').closest('div').removeClass('has-error');
			$('form#customer_status_change select').nextAll().remove();
			var order_id = $('#customer_order_id').val();
			var form_data = $('form#customer_status_change').serialize();
			$.ajax({
				url:'{{url("admin/order/status")}}',
				type:'post',
				data:{'type':'customer','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#customer_status_loader_img').fadeIn();
					$('.set_customer_status').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#customer_status_loader_img').fadeOut();
						$('.set_customer_status').prop('disabled', false);
						
						$('.customer_status_'+order_id).html('<button type="button" style="background:'+data.status_color+' !important;border-color:'+data.status_color+' !important" class="btn btn-xs bg-olive margin customer_status" data="'+data.status_key+'" order-id="'+order_id+'" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">'+data.status_value+'</button>');
						document.getElementById("customer_status_change").reset();
						$('#customer_status').modal('hide');
					}
				}
			});
		}else{
			$('form#customer_status_change select').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#customer_status_change select');
		}		
	});
</script>

<div class="modal fade" id="payment_status">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-snowflake-o"></i> Payment Status</h4>
			</div>
			<div class="modal-body">
				<div class="row customer_status_div">
					<div class="col-md-12">
						{{ Form::model('payment_status_change', ['url' => ['admin/order/list'],'files'=>true,'id'=>'payment_status_change']) }}
							{{ Form::hidden('payment_order_id','',['id'=>'payment_order_id']) }}
							{{ Form::hidden('payment_product_item_id','',['id'=>'payment_product_item_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_payment_status', 'Select Status',array('class'=>'form-control-label'))}}	
									{{ Form::select('select_payment_status',['Select Status']+config('constants.payment_status'),'',['class'=>'form-control','id'=>'select_payment_status'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set Status',['class'=>'btn btn-success set_payment_status'])}}
									<img id="payment_status_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.payment_status',function(e){
		var id = $(this).attr('data');
		var order_id = $(this).attr('order-id');
		var product_item_id = $(this).attr('product_item_id');
		$('#payment_order_id').val(order_id);
		$('#payment_product_item_id').val(product_item_id);
		if(id != 0){
			$("#select_payment_status option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_payment_status option:eq(0)').prop('selected', 'selected');
		}
		$('#payment_status').modal('show');
	});
	
	$(document).on('click','.set_payment_status',function(e){
		if($('form#payment_status_change select').val() != 0){
			$('form#payment_status_change select').closest('div').removeClass('has-error');
			$('form#payment_status_change select').nextAll().remove();
			var order_id = $('#payment_order_id').val();
			var product_item_id = $('#payment_product_item_id').val();
			var form_data = $('form#payment_status_change').serialize();
			$.ajax({
				url:'{{url("admin/order/status")}}',
				type:'post',
				data:{'type':'payment','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#payment_status_loader_img').fadeIn();
					$('.set_payment_status').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#payment_status_loader_img').fadeOut();
						$('.set_payment_status').prop('disabled', false);
						$('.payment_status_'+product_item_id+' button').replaceWith('<button style="background:'+data.status_color+' !important;border-color:'+data.status_color+' !important"  type="button" class="btn btn-xs bg-navy margin payment_status" data="'+data.status_key+'" order-id="'+order_id+'" product_item_id="'+product_item_id+'" data-toggle="modal" data-target="#payment_status" title="Set Payment Status">'+data.status_value+'</button>');
						document.getElementById("payment_status_change").reset();
						$('#payment_status').modal('hide');
					}
				}
			});
		}else{
			$('form#payment_status_change select').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#payment_status_change select');
		}		
	});
</script>

<div class="modal fade" id="vendor_status">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-snowflake-o"></i> Vendor Status</h4>
			</div>
			<div class="modal-body">
				<div class="row vendor_status_div">
					<div class="col-md-12">
						{{ Form::model('vendor_status_change', ['url' => ['admin/order/list'],'files'=>true,'id'=>'vendor_status_change']) }}
							{{ Form::hidden('order_product_id','',['id'=>'order_product_id']) }}
							{{ Form::hidden('product_item_id','',['id'=>'product_item_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_vendor_status', 'Select Status',array('class'=>'form-control-label'))}}	
									{{ Form::select('select_vendor_status',['Select Status']+config('constants.vendor_status'),'',['class'=>'form-control','id'=>'select_vendor_status'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set Status',['class'=>'btn btn-success set_status'])}}
									<img id="vendor_status_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.vendor_status',function(e){
		var id = $(this).attr('data');
		var order_product_id = $(this).attr('order-product-id');
		var product_item_id = $(this).attr('product_item_id');
		$('#order_product_id').val(order_product_id);
		$('#product_item_id').val(product_item_id);
		if(id != 0){
			$("#select_vendor_status option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_vendor_status option:eq(0)').prop('selected', 'selected');
		}
		$('#vendor_status').modal('show');
	});
	
	$(document).on('click','.set_status',function(e){
		if($('form#vendor_status_change select').val() != 0){
			$('form#vendor_status_change select').closest('div').removeClass('has-error');
			$('form#vendor_status_change select').nextAll().remove();
			var order_product_id = $('#order_product_id').val();
			var product_item_id = $('#product_item_id').val();
			var form_data = $('form#vendor_status_change').serialize();
			$.ajax({
				url:'{{url("admin/order/status")}}',
				type:'post',
				data:{'type':'vendor','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#vendor_status_loader_img').fadeIn();
					$('.set_status').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#vendor_status_loader_img').fadeOut();
						$('.set_status').prop('disabled', false);
						$('#vendor_status_'+product_item_id).html('<button style="background:'+data.status_color+' !important;border-color:'+data.status_color+' !important" type="button" class="btn btn-xs btn-info margin vendor_status" data="'+data.status_key+'" order-product-id="'+order_product_id+'" product_item_id="'+product_item_id+'" data-toggle="modal" data-target="#vendor_status" title="Set Vendor Status">'+data.status_value+'</button>');
						document.getElementById("vendor_status_change").reset();
						$('#vendor_status').modal('hide');
					}
				}
			});
		}else{
			$('form#vendor_status_change select').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#vendor_status_change select');
		}		
	});
</script>

<div class="modal fade" id="art_work_status">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-snowflake-o"></i> Art Work Status</h4>
			</div>
			<div class="modal-body">
				<div class="row vendor_status_div">
					<div class="col-md-12">
						{{ Form::model('art_work_status_change', ['url' => ['admin/order/list'],'files'=>true,'id'=>'art_work_status_change']) }}
							{{ Form::hidden('art_work_order_id','',['id'=>'art_work_order_id']) }}
							{{ Form::hidden('art_work_product_id','',['id'=>'art_work_product_id']) }}
							{{ Form::hidden('art_work_product_item_id','',['id'=>'art_work_product_item_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_art_work_status', 'Select Status',array('class'=>'form-control-label'))}}	
									{{ Form::select('select_art_work_status',['Select Status']+config('constants.art_work_status'),'',['class'=>'form-control','id'=>'select_art_work_status'])}}
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_art_work_date', 'Select Date',array('class'=>'form-control-label'))}}	
									{{ Form::text('select_art_work_date','',['class'=>'form-control date','id'=>'select_art_work_date','placeholder'=>'Select Date'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set Status',['class'=>'btn btn-success set_status_art'])}}
									<img id="art_status_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.art_work_status',function(e){
		var id = $(this).attr('data');
		var date = $(this).attr('date');
		var order_id = $(this).attr('order-id');
		var order_product_id = $(this).attr('order-product-id');
		var product_item_id = $(this).attr('product_item_id');
		$('#art_work_order_id').val(order_id);
		$('#art_work_product_id').val(order_product_id);
		$('#art_work_product_item_id').val(product_item_id);
		if(id != 0){
			$("#select_art_work_status option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_art_work_status option:eq(0)').prop('selected', 'selected');
		}
		$('#select_art_work_date').val(date);
		$('#art_work_status').modal('show');
	});
	
	$(document).on('click','.set_status_art',function(e){
		if($('form#art_work_status_change select').val() != 0){
			$('form#art_work_status_change select').closest('div').removeClass('has-error');
			$('form#art_work_status_change select').nextAll().remove();
			var order_id = $('#art_work_order_id').val();
			var order_product_id = $('#art_work_product_id').val();
			var product_item_id = $('#art_work_product_item_id').val();
			var form_data = $('form#art_work_status_change').serialize();
			$.ajax({
				url:'{{url("admin/order/status")}}',
				type:'post',
				data:{'type':'art_work','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#art_status_loader_img').fadeIn();
					$('.set_status_art').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#art_status_loader_img').fadeOut();
						$('.set_status_art').prop('disabled', false);
						if(data.customer_status_btn != ''){
							$('.customer_status_'+order_id).html(data.customer_status_btn);
						}
						
						$('#art_work_status_'+product_item_id).html('<button type="button" style="background:'+data.status_color+' !important;border-color:'+data.status_color+' !important" class="btn btn-xs btn-danger margin art_work_status" date="'+data.art_work_date+'" data="'+data.status_key+'" order-product-id="'+order_product_id+'" order-id="'+order_id+'" product_item_id="'+product_item_id+'" data-toggle="modal" data-target="#art_work_status" title="Set Art Work Status">'+data.status_value+'<br/>'+data.art_work_date+'</button>');
						document.getElementById("art_work_status_change").reset();
						$('#art_work_status').modal('hide');
					}
				}
			});
		}else{
			$('form#art_work_status_change select').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#art_work_status_change select');
		}		
	});	
</script>

<div class="modal fade" id="due_date">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-calendar"></i> Set Due Date</h4>
			</div>
			<div class="modal-body">
				<div class="row due_date_div">
					<div class="col-md-12">
						{{ Form::model('due_date_form', ['url' => ['admin/order/list'],'files'=>true,'id'=>'due_date_form']) }}
							{{ Form::hidden('due_date_product_id','',['id'=>'due_date_product_id']) }}
							{{ Form::hidden('due_date_product_item_id','',['id'=>'due_date_product_item_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
								{{ Form::label('select_type', 'Select Due Date Type',array('class'=>'form-control-label'))}}	
								{{ Form::select('select_type',['soft_date'=>'Soft Date','hard_date'=>'Hard Date'],'',['class'=>'form-control date','id'=>'select_type'])}}
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_due_date', 'Select Due Date',array('class'=>'form-control-label'))}}	
									{{ Form::text('select_due_date','',['class'=>'form-control date','id'=>'select_due_date','placeholder'=>'Select Due Date'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set Date',['class'=>'btn btn-success set_due_date'])}}
									<img id="due_date_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.due_date',function(e){
		var date = $(this).attr('data');
		var type = $(this).attr('data-type');
		var order_product_id = $(this).attr('order-product-id');
		var product_item_id = $(this).attr('product_item_id');
		$('#due_date_product_id').val(order_product_id);
		$('#due_date_product_item_id').val(product_item_id);
		if(date != 0){
			$("#select_due_date").val(date);
		}
		if(type != ''){
			$("#select_type option[value='" + type + "']").attr("selected","selected");
		}
		$('#due_date').modal('show');
	});
	
	$(document).on('click','.set_due_date',function(e){
		if($('form#due_date_form input#select_due_date').val() != ''){
			$('form#due_date_form input#select_due_date').closest('div').removeClass('has-error');
			$('form#due_date_form input#select_due_date').nextAll().remove();
			var due_date_product_id = $('#due_date_product_id').val();
			var due_date_product_item_id = $('#due_date_product_item_id').val();
			var form_data = $('form#due_date_form').serialize();
			$.ajax({
				url:'{{url("admin/order/set_value")}}',
				type:'post',
				data:{'type':'due_date','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#due_date_loader_img').fadeIn();
					$('.set_due_date').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#due_date_loader_img').fadeOut();
						$('.set_due_date').prop('disabled', false);
						
						$('#due_date_'+due_date_product_item_id).html('<button type="button" class="btn btn-xs '+data.class_name+' margin due_date" data="'+data.date+'" data-type="'+data.type+'"  order-product-id="'+due_date_product_id+'" product_item_id="'+due_date_product_item_id+'" data-toggle="modal" data-target="#due_date" title="Set Due Date">'+data.res+'</button>');
						document.getElementById("due_date_form").reset();
						$('#due_date').modal('hide');
					}
				}
			});
		}else{
			$('form#due_date_form input#select_due_date').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#due_date_form input#select_due_date');
		}		
	});
	
	$( ".date" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy',
		//minDate: 0,
	});
</script>

<div class="modal fade" id="tracking_id">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-truck"></i> Set Shipping Number</h4>
			</div>
			<div class="modal-body">
				<div class="row tracking_id_div">
					<div class="col-md-12">
						{{ Form::model('tracking_id_form', ['url' => ['admin/order/list'],'files'=>true,'id'=>'tracking_id_form']) }}
							{{ Form::hidden('tracking_id_product_id','',['id'=>'tracking_id_product_id']) }}
							{{ Form::hidden('tracking_id_product_item_id','',['id'=>'tracking_id_product_item_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('shipping_type', 'Select Shipping Type',array('class'=>'form-control-label'))}}	
									{{ Form::select('shipping_type',['1'=>'UPS Shipping','2'=>'FedEx Shipping','3'=>'Other'],'',['class'=>'form-control','id'=>'shipping_type'])}}
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-xs-6 shipping_career_div" style="display: none">
								<div class="form-group">
									{{ Form::label('shipping_career', 'Carrier',array('class'=>'form-control-label'))}}	
									{{ Form::text('shipping_career','',['class'=>'form-control','id'=>'shipping_career','placeholder'=>'Enter Carrier Name'])}}
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_tracking_id', 'Tracking Number',array('class'=>'form-control-label'))}}	
									{{ Form::text('select_tracking_id','',['class'=>'form-control','id'=>'select_tracking_id','placeholder'=>'Enter Tracking Number'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set Shipping',['class'=>'btn btn-success set_tracking_id'])}}
									<img id="tracking_id_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).on('click','.tracking_id',function(e){
		var data = $(this).attr('data');
		var shipping_type = $(this).attr('data-type');
		var shipping_career = $(this).attr('data-care');
		var order_product_id = $(this).attr('order-product-id');
		var product_item_id = $(this).attr('product_item_id');
		$('#tracking_id_product_id').val(order_product_id);
		$('#tracking_id_product_item_id').val(product_item_id);
		if(data != 0){
			$("#select_tracking_id").val(data);
			$("#shipping_type").val(shipping_type);
			if(shipping_type == 3){
				$('.shipping_career_div').show();
				$("#shipping_career").val(shipping_career);
			}else{
				$('.shipping_career_div').hide();
				$("#shipping_career").val('');
			}
		}
		$('#tracking_id').modal('show');
	});

	$(document).on('change','#shipping_type',function(e){
		var type = $(this).val();
		if(type == 3)
			$('.shipping_career_div').show();
		else
			$('.shipping_career_div').hide();
		
	});
	
	$(document).on('click','.set_tracking_id',function(e){
		if($('form#tracking_id_form input#select_tracking_id').val() != ''){
			$('form#tracking_id_form input#select_tracking_id').closest('div').removeClass('has-error');
			$('form#tracking_id_form input#select_tracking_id').nextAll().remove();
			var tracking_id_product_id = $('#tracking_id_product_id').val();
			var tracking_id_product_item_id = $('#tracking_id_product_item_id').val();
			var form_data = $('form#tracking_id_form').serialize();
			$.ajax({
				url:'{{url("admin/order/set_value")}}',
				type:'post',
				data:{'type':'tracking_id','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#tracking_id_loader_img').fadeIn();
					$('.set_tracking_id').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#tracking_id_loader_img').fadeOut();
						$('.set_tracking_id').prop('disabled', false);
						
						var str = '<b>'+data.shipping_type_txt+' :</b><br/>'+data.shipping_via+'<br/>';
						str += '<b>Tracking Number :</b><br/>'+data.res+'<br/><br/>';
						str += '<button style="background:#868686 !important" type="button" class="btn btn-xs bg-maroon margin tracking_id" data="'+data.res+'" data-type="'+data.shipping_type+'" data-care="'+data.shipping_via+'" order-product-id="'+tracking_id_product_id+'" product_item_id="'+tracking_id_product_item_id+'" data-toggle="modal" data-target="#tracking_id" title="Change Tracking">Change Tracking</button>';
						
						if(data.link != '')
							str += '<br/><a style="background:#868686 !important" href="'+data.link+'" target="_blank" class="btn btn-xs bg-olive margin">Track Order</a>';
						
						var btnStr = '';
						if(data.customer_status != 0)
							btnStr += '<button style="background:'+data.customer_status_color+' !important;border-color:'+data.customer_status_color+' !important" type="button" class="btn btn-xs bg-olive margin customer_status" data="'+data.customer_status+'" order-id="'+data.order_id+'" data-toggle="modal" data-target="#customer_status" title="Set Customer Status">'+data.customer_status_name+'</button>';
						else
							btnStr += '<button style="background:'+data.customer_status_color+' !important;border-color:'+data.customer_status_color+' !important" type="button" class="btn btn-xs bg-orange margin customer_status" data="0" order-id="'+data.order_id+'" data-toggle="modal" data-target="#customer_status" title="Set customer Status">Set Status</button>';
						
						$('.customer_status_'+data.order_id).html(btnStr);
						
						$('#tracking_id_'+tracking_id_product_item_id).html(str);
						document.getElementById("tracking_id_form").reset();
						$('#tracking_id').modal('hide');
					}
				}
			});
		}else{
			$('form#tracking_id_form input#select_tracking_id').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#tracking_id_form input#select_tracking_id');
		}		
	});
</script>

<div class="modal fade" id="po_id">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-snowflake-o"></i> Set PO Id</h4>
			</div>
			<div class="modal-body">
				<div class="row tracking_id_div">
					<div class="col-md-12">
						{{ Form::model('po_id_form', ['url' => ['admin/order/list'],'files'=>true,'id'=>'po_id_form']) }}
							{{ Form::hidden('po_id_product_id','',['id'=>'po_id_product_id']) }}
							{{ Form::hidden('po_id_product_item_id','',['id'=>'po_id_product_item_id']) }}
							<div class="col-xs-6">
								<div class="form-group">
									{{ Form::label('select_po_id', 'PO Id',array('class'=>'form-control-label'))}}	
									{{ Form::text('select_po_id','',['class'=>'form-control','id'=>'select_po_id','placeholder'=>'Enter PO Id'])}}
								</div>
							</div>
							<div class="col-xs-12">
								<div class="form-group">
									{{ Form::button('Set PO ID',['class'=>'btn btn-success set_po_id'])}}
									<img id="po_id_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
								</div>
							</div>
						{{ Form::close() }}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){
	$(document).on('click','.po_id',function(e){
		var po = $(this).attr('data');
		var order_product_id = $(this).attr('order-product-id');
		var product_item_id = $(this).attr('product_item_id');
		$('#po_id_product_id').val(order_product_id);
		$('#po_id_product_item_id').val(product_item_id);
		
		$("#select_po_id").val(po);
		$('#po_id').modal('show');
	});
	
	$(document).on('click','.set_po_id',function(e){
		if($('form#po_id_form input#select_po_id').val() != ''){
			$('form#po_id_form input#select_po_id').closest('div').removeClass('has-error');
			$('form#po_id_form input#select_po_id').nextAll().remove();
			var po_id_product_id = $('#po_id_product_id').val();
			var po_id_product_item_id = $('#po_id_product_item_id').val();
			var form_data = $('form#po_id_form').serialize();
			$.ajax({
				url:'{{url("admin/order/set_value")}}',
				type:'post',
				data:{'type':'po_id','data':form_data},
				dataType:'json',
				beforeSend: function() {
					$('#po_id_loader_img').fadeIn();
					$('.set_po_id').prop('disabled', true);
				},
				success:function(data){
					if(data.status == 'success'){
						$('#po_id_loader_img').fadeOut();
						$('.set_po_id').prop('disabled', false);
						$('#po_id_'+po_id_product_item_id).html('<button type="button" class="btn btn-xs bg-purple margin po_id" data="'+data.res+'" order-product-id="'+po_id_product_id+'" product_item_id="'+po_id_product_item_id+'" data-toggle="modal" data-target="#po_id" title="Set PO Id">'+data.res+'</button>');
						document.getElementById("tracking_id_form").reset();
						$('#po_id').modal('hide');
					}
				}
			});
		}else{
			$('form#po_id_form input#select_po_id').closest('div').addClass('has-error');
			$("<span class='help-block'>this field is required</span>").insertAfter('form#po_id_form input#select_po_id');
		}		
	});
	
	// Book Shipping //
	
	$(document).on('click','.book_shipping',function(e){
		var order_id = $(this).attr('order-id');
		var product_id = $(this).attr('product_id');
		var product_item_id = $(this).attr('product_item_id');
		var data_type = $(this).attr('data-type');
		if(data_type == 'shipping'){
			$.ajax({
					url:'{{url("admin/order/bookshipping")}}',
					type:'post',
					data:{'order_id':order_id,'product_id':product_id,'item_id':product_item_id},
					dataType:'json',
					beforeSend: function() {
						//$('#fade').fadeIn();
						$('.spinner_'+product_item_id).removeClass('hide');
					},
					success:function(data){
						$('#fade').fadeOut();
						$('.spinner_'+product_item_id).addClass('hide');
						if(data.status == 'success'){
							var str = '<b>Tracking Number :</b> '+data.tracking_number+'<br/>';
							str += '<b>Shipping Option :</b> '+data.shipping_option+'<br/>';
							str += '<a href="'+"{{url('public/uploads/orders/ShippingLabels/')}}"+'/'+data.image_name+'" target="_blank" class="btn btn-xs bg-navy margin">View Label</a>';
							str += '<a href="'+"{{url('admin/order/tracking/')}}"+'/'+data.tracking_number+'" class="btn btn-xs bg-olive margin">Track Order</a>';
							if(data.multiple){
								$('td.ship_td_'+product_item_id).html(str);
							}else{
								$('td.same_ship_'+order_id).html(str);
							}
						}else{
							$('.error_span_'+product_item_id).html(data.msg);
						}
					}
				});
		}else{
			alert('label');
		}
	});
	
	// Order Product Delete //
	
	$(document).on('click','.deleteItem',function(){
		var order_id = $(this).attr('data-order');
		var id = $(this).attr('data-id');
		var po = $(this).attr('data-po');
		var _this = $(this);
		$.ajax({
			url:'{{url("/admin/order/product-delete")}}',
			type:'post',
			dataType:'json',
			data:{'_token':"{{ csrf_token() }}",'order_id':order_id,'id':id,'po':po},
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 'success'){
					alert("Item Deleted Successfully");
					_this.closest('tr.child_row_'+order_id).remove();
				}
			}
		});
	});
	
	// Order Product Clone //
	
	$(document).on('click','.productClone',function(){
		var id = $(this).attr('data-id');
		var type = $(this).attr('data-type');
		$.ajax({
			url:'{{url("/admin/order/product-clone")}}',
			type:'post',
			dataType:'json',
			data:{'_token':"{{ csrf_token() }}",'id':id,'type':type},
			beforeSend: function () {
			  $.blockUI();
			},
			complete: function () {
			  $.unblockUI();
			},
			success:function(data){
				if(data.status == 1){
					$(data.html).insertAfter($('tr.child_row_'+data.order_id).last());
				}else{
					alert(data.msg);
				}
			}
		});
	});
	
	$('.order_row').click(function(){
		var id = $(this).attr('data-id');
		var rel = $(this).attr('rel');
		
		$('.child_row_'+id).fadeToggle(300);
		
		if(rel == 0){
			$(this).attr('rel',1);
			$(this).removeClass('btn-success');
			$(this).addClass('btn-danger');
			$(this).html('<i class="fa fa-minus"></i>');
		}else{
			$(this).attr('rel',0);
			$(this).addClass('btn-success');
			$(this).removeClass('btn-danger');
			$(this).html('<i class="fa fa-plus"></i>');
		}
	});
	
	$('#container2').doubleScroll();

	$('#check_all').click(function(){
		if($(this).is(":checked"))
		{
			$('.ids').prop('checked', true);
		}
		else
		{
			$('.ids').prop('checked', false); 				
		}
	});	

	$('.exporttoqb').click(function(){

		var allVals = [];
		$('.ids:checked').each(function() {
		   allVals.push($(this).val());
		});

		if(allVals.length >= 1)
		{
			window.open("{{url('/admin/quickbook/exporttoqb')}}/"+allVals,'qbwindow','height=600,width=800,top=10,left=100');		    	
		}
		else
		{
			 alert("No order selected");
			 return false;
		}
	});
});	
</script>