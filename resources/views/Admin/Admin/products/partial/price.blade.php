<!-- product price info form -->
<form role="form" class="productForm" id="productPriceInfo" method="POST" action="{{ url('admin/products/edit_price/'.$id) }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<h3 class="formheading">Product Price Info:</h3>	

	<div class="form-group row{{ $errors->has('price_sqft_area') ? ' has-error' : '' }}">
		<label class="col-sm-3 form-control-label">Variable price based on sqft area</label>
		<div class="col-sm-9" id="price_sqft_area_div">
			@php 
			$checked = '';
			if(array_key_exists('price_sqft_area',old())){
				if(old('price_sqft_area') == 1){	$checked = 'checked'; }
			}else{	if($product->price_sqft_area == 1){ $checked = 'checked';} }
			@endphp
			<input type="checkbox" name="price_sqft_area" id="price_sqft_area" value="1" class="flat-red" {{ $checked }} />
		</div>
	</div>
	<div class="form-group row price_div{{ $errors->has('price') ? ' has-error' : '' }}" style="{{($checked == 'checked')?'display:none;':''}}" >
		<label class="col-sm-3 form-control-label label_price">Price<span class="text-danger">*</span></label>
		<div class="col-sm-6">
			<div class="input-group">
				<span class="input-group-addon">$</span>
				<input id="price" type="text" class="form-control price" name="price" value="{{array_key_exists('price',old())?old('price'):$product->price}}" placeholder="Enter Price" />
			</div>
			@if ($errors->has('price'))
				<span class="help-block">{{ $errors->first('price') }}</span>
			@endif
		</div>
	</div>
	<div class="form-group row multiple_price_div" style="{{($checked == 'checked')?'display:block;':'display:none;'}}" >
		<label class="col-sm-3 form-control-label">&nbsp;</label>
		<div class="col-md-9">
			<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Price</th>
						<th>Min Area</th>
						<th>Max Area</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@php
						$i = 1;
						if(count($product->product_prices) > 0){
							foreach($product->product_prices as $val){
					@endphp
								<tr>
									<td>Price {{$i}}</td>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" step="any" placeholder="Enter Price" name="old_prices[{{$val->id}}][price]" class="form-control" value="{{$val->price}}" required/>
										</div>
									</td>
									<td>
										<input type="number" step="any" name="old_prices[{{$val->id}}][min_area]" class="form-control" placeholder="Enter Min Area" value="{{$val->min_area}}" min="1" required/>
									</td>
									<td>
										<input type="number" step="any" name="old_prices[{{$val->id}}][max_area]" class="form-control" placeholder="Enter Max Area" value="{{$val->max_area}}" min="1" required/>
									</td>
									<td>
										<button class="btn btn-danger remove-price-option" type="button"><i class="fa fa-trash"></i></button>
									</td>
								</tr>
					@php
							$i++;
							}
						}
					@endphp
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group row add_more_price" style="{{($checked == 'checked')?'display:block;':'display:none;'}}">
		<label class="col-sm-3 form-control-label">&nbsp;</label>
		<div class="col-md-9">
			<button class="btn btn-success add-price-option" type="button">Add More</button>
		</div>
	</div>
	<div class="line"></div>

	<div class="form-group row">
		<label class="col-sm-3 form-control-label">Minimum Price</label>
		<div class="col-sm-6" id="price_sqft_area_div">			
			<input type="text" name="min_price" id="min_price" value="{{$product->min_price}}" class="form-control" />
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 form-control-label">&nbsp;</label>
		<div class="col-sm-4 offset-sm-2">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>
<script>
$(document).ready(function(){
	var price_option_count = Number('{{$i}}');
	$(document).on('ifChecked','[name="price_sqft_area"]', function(event){
		$('.price_div').slideUp();
		
		var str = '<tr><td>Price '+price_option_count+'</td>';
		str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="number" step="any" placeholder="Enter Price" name="prices['+price_option_count+'][price]" class="form-control" required/></div></td>';
		
		str += '<td><input type="number" step="any" name="prices['+price_option_count+'][min_area]" class="form-control" placeholder="Enter Min Area" min="1" required/></td>';
		
		str += '<td><input type="number" step="any" name="prices['+price_option_count+'][max_area]" class="form-control" placeholder="Enter Max Area" min="1" required/></td>';
		
		str += '<td><button class="btn btn-danger remove-price-option" type="button"><i class="fa fa-trash"></i></button></td>';
		
		str += '</tr>';
			
		$('.multiple_price_div table tbody').append(str);
		$('.add_more_price').slideDown();
		$('.multiple_price_div').slideDown();
		//price_option_count++;
	});
	
	$(document).on('ifUnchecked','[name="price_sqft_area"]', function(event){
		$('.price_div').slideDown(500);
		$('.multiple_price_div table tbody').html('');
		$('.add_more_price').slideUp();
		$('.multiple_price_div').slideUp();
		price_option_count = 1;
	});
	
	$(document).on('click','.add-price-option', function(event){
		price_option_count++;
		
		var str = '<tr><td>Price '+price_option_count+'</td>';
		str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="number" step="any" placeholder="Enter Price" name="prices['+price_option_count+'][price]" class="form-control" required/></div></td>';
		
		str += '<td><input type="number" step="any" name="prices['+price_option_count+'][min_area]" class="form-control" placeholder="Enter Min Area" min="1" required/></td>';
		
		str += '<td><input type="number" step="any" name="prices['+price_option_count+'][max_area]" class="form-control" placeholder="Enter Max Area" min="1" required/></td>';
		
		str += '<td><button class="btn btn-danger remove-price-option" type="button"><i class="fa fa-trash"></i></button></td>';
		
		str += '</tr>';
			
		$('.multiple_price_div table tbody').append(str);
	});
	
	$(document).on('click','.remove-price-option', function(event){
		if(confirm('Are you sure want to delete this ?')){
			$(this).closest('tr').remove();
		}
	});
});
</script>
<!-- product basic info form -->
