<div class="modal fade" id="assign_vendor_model">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign Vendor</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="form-group assign_vendor_div">
							{{Form::hidden('vendor_order_id','',['id'=>'vendor_order_id'])}}
							{{Form::hidden('vendor_order_po','',['id'=>'vendor_order_po'])}}
							{{Form::hidden('vendor_order_product_id','',['id'=>'vendor_order_product_id'])}}
							{{Form::hidden('vendor_order_product','',['id'=>'vendor_order_product'])}}
							{{ Form::label('select_vendor', 'Vendor',array('class'=>'form-control-label'))}}	
							{{Form::select('select_vendor', [''=>'Select Vendor']+$vendors, '',array('class'=>'form-control','id'=>'select_vendor'))}}
							<span class="help-block"></span>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-xs-6">
						<div class="form-group">
							<button type="button" class="btn btn-success assign_vendor">Assign Vendor</button>
							<img id="vendor_loader_img" class="loader_img" src="{{url('public/img/loader/Spinner.gif')}}">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click','.vendor_btn',function(e){
		var id = $(this).attr('data');
		var order_product_id = $(this).attr('order-product-id');
		var order_product = $(this).attr('order-product');
		var order_id = $(this).attr('order-id');
		var order_po = $(this).attr('order-po');
		$('#vendor_order_product_id').val(order_product_id);
		$('#vendor_order_product').val(order_product);
		$('#vendor_order_id').val(order_id);
		$('#vendor_order_po').val(order_po);
		if(id != 0){
			$("#select_vendor option[value='" + id + "']").attr("selected","selected");
		}
		else{
			$('#select_vendor option:eq(0)').prop('selected', 'selected');
		}
	});
	$(document).on('click','.assign_vendor',function(e){
		
		var vendor_id = $('#select_vendor').val();
		var order_product_id = $('#vendor_order_product_id').val();
		var order_product = $('#vendor_order_product').val();
		var order_id = $('#vendor_order_id').val();
		var order_po = $('#vendor_order_po').val();
		if(vendor_id != ''){
			$('div.assign_vendor_div').removeClass('has-error');
			$('div.assign_vendor_div span').html('');
			$.ajax({
				url:'{{url("admin/order/assign_vendor")}}',
				type:'post',
				data:{'vendor_id':vendor_id,'order_product_id':order_product_id,'order_product':order_product,'order_id':order_id,'vendor_name':$("#select_vendor option:selected").text()},
				dataType:'json',
				beforeSend: function() {
					$('#vendor_loader_img').fadeIn();
					$('.assign_vendor').prop('disabled', true);
				},
				success:function(data){
					$('#vendor_loader_img').fadeOut();
					$('.assign_vendor').prop('disabled', false);
					if(data.status == 'success'){
						$('#vendor_'+order_product_id).html(data.html);
						
						var url = "{{url('/admin/order/po')}}/"+order_id+'/'+vendor_id;
						var str = '<a href="'+url+'" class="btn btn-xs bg-purple margin">'+order_po+'</a>';
						
						//$('.po_td_'+order_product_id).html(str);
						
						if(data.po_btn != ''){
							var btns = data.po_btn;
							$.each(btns,function(k,v){
								$('.po_td_'+k).html(v);
							});
						}
						
						$('#assign_vendor_model').modal('hide');
					}
				}
			});
		}else{
			$('div.assign_vendor_div').addClass('has-error');
			$('div.assign_vendor_div span').html('Select Vendor');
		}
	});
</script>