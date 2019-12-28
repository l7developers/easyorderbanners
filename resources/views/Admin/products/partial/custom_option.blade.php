<!-- product Custom Option info form -->
<form role="form" class="productForm" id="productCustomOptionInfo" method="POST" action="{{ url('admin/products/edit/'.$id) }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<h3 class="formheading">Product Custom Option Info:</h3>

	<div class="form-group row{{ $errors->has('custom_option_id') ? ' has-error' : '' }}">
		<label class="col-sm-3 form-control-label">Custom option</label>
		<div class="box-body col-md-9">
			<div class="panel panel-info product_panel">
				<div class="panel-heading">
					<h5>Custom Options</h5>
				</div>
				<div class="panel-body custom_option_panel" id="">
					<div class="col-md-12 option_show">
						<div class="box-body">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th style="width: 10px">Option Name</th>
										<th style="width: 10px">Action</th>
									</tr>
								</thead>
								<tbody>
									@php 
										$options_array = array();
										foreach($product->Options as $option){
											//pr($option);
											$id = $option->id;
											$options_array[] = $option->option_id;
									@endphp
									<tr>
										<td>{{$option->CustomOption->label}}</td> 
										<td>
											<button class="btn remove-delete btn-danger " data-id="{{$id}}" type="button"><i class="fa fa-trash"></i></button>
										</td> 
									</tr>
									@php } @endphp
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-12">
						<div class="panel-heading">
							<h5>Add New Option</h5>
						</div>
						<div class="col-xs-8">
						<select name="custom_option_id" id="custom_option_id" class="form-control">
							<option value="">Select Option</option>
							@php 
								$selected = '';
								foreach($options as $option){ 
									$selected = '';
									$id = $option->id;
									if(in_array($id,$options_array)){
											$selected = 'disabled="disabled"';
									}
							@endphp
								<option {{$selected}} value="{{$id}}">{{$option->label}}</option>
							@php } @endphp
						</select>
						</div>
						<div class="col-xs-4">
							<button type="button" class="btn btn-success add-option">Add More</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
$(document).ready(function() {
	$(document).on('click', ".add-option", function (event) {
		//alert($('#custom_option_id :selected').val());
		var on = true;
		var id = $('#custom_option_id :selected').val();
		var product_id = '{{$product->id}}';
		//alert(product_id);
		if(id == ''){
			alert("Please Select One option");
			on = false;
		}
		
		if(on){
			$.ajax({
			   url: "<?php echo url('admin/products/addOption/'); ?>",
				type: "post",
				data: {id: id,product_id:product_id},
				dataType:'json',
				success: function (data) {
					//alert(data.html);
					if(data.flag == 1){
						$('select#custom_option_id').html(data.html);
						$('div.option_show table tbody').append(data.tr_html);
					}
				}
			});
		}
	});
	
	$(document).on('click', ".remove-delete", function (event) {
		if(confirm('Are you sure want to delete it.')){
			var ids = $(this).attr('data-id');
			var product_id = '{{$product->id}}';
			var _this = $(this);			
			  $.ajax({
			   url: "<?php echo url('admin/products/deleteData/'); ?>",
				type: "post",
				data: {id: ids,table:'product_options',product_id:product_id},
				dataType:'json',
				success: function (data) {
					//alert(data);
					if(data.flag == 1){
						_this.closest('tr').find('td').fadeOut(1000,function(){ 
							_this.parents('tr:first').remove();   
							$('select#custom_option_id').html(data.html);
							var count = $('.option_show table tbody tr').length;
							//alert(count);
							if(count == 0){
								$('.option_show').addClass('hide');
							}
						});
					}
				}
			});   
		}
	});
});
</script>          