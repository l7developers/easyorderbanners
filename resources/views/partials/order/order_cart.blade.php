<div class="modal fade" id="order_cart" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="fa fa-shopping-cart"></i> Cart</h4>
			</div>
			<div class="modal-body">
				<div class="row cart_div">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-body table-responsive">
								<table id="cart_table" class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
									<thead>
										<tr>
											<th>S.No.</th>
											<th>Product</th>
											<th>Options</th>
											<th>Quantity</th>
											<th>Price</th>
											<th>Total</th>
											<th>Remove</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
/*
$(document).on('click','.cart',function(e){
	$.ajax({
		url:'{{url("admin/order/cart_products")}}',
		type:'post',
		data:{'type':'list'},
		dataType:'json',
		beforeSend: function () {
		  $.blockUI();
		},
		complete: function () {
		  $.unblockUI();
		},
		success:function(data){
			if(data.status == 'success'){
				$('#cart_table tbody').html(data.res);
				$('#order_cart').modal('show');
			}
		}
	});
});
*/
$(document).on('click','.remove-product',function(e){
	var key = $(this).attr('data');
	$.ajax({
		url:'{{url("admin/order/cart_products")}}',
		type:'post',
		data:{'type':'delete','key':key},
		dataType:'json',
		beforeSend: function () {
		  $.blockUI();
		},
		complete: function () {
		  $.unblockUI();
		},
		success:function(data){
			if(data.status == 'success'){
				$('#cart_table tbody').html(data.res);
				if(data.res1 != ''){
					$('.cart_products table tbody').html(data.res1);
					$('.cart_products').removeClass('hide');
				}else{
					$('.cart_products table tbody').html('');
					$('.cart_products').addClass('hide');
				}
				$('#cart_amount').val(data.session_total);
				$('#amount').html('$'+data.session_total);
				
				paymentTabReset();
				addressTabReset();
				$('li.product_tab a').trigger("click");
			}
		}
	});
});

$(document).on('click','.edit-product',function(e){
	var key = $(this).attr('data');
	$.ajax({
		url:'{{url("admin/order/cart/products-edit")}}',
		type:'post',
		data:{'_token':"{{ csrf_token() }}",'type':'form','key':key},
		dataType:'json',
		beforeSend: function () {
		  $.blockUI();
		},
		complete: function () {
		  $.unblockUI();
		  $('#order_cart').modal('hide');
		},
		success:function(data){
			if(data.status == 'success'){
				setObject(data,'cart');
				setPicers();
			}
		}
	});
});
</script>