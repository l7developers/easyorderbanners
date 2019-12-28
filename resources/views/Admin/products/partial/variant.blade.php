<!-- product price info form -->
<form role="form" class="productForm" id="productVariantInfo" method="POST" action="{{ url('admin/products/edit_variants/'.$id) }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<h3 class="formheading">Product Variant Info:</h3>

	<div class="form-group row{{ $errors->has('custom_option_id') ? ' has-error' : '' }}">
		<label class="col-sm-3 form-control-label">Variants</label>
		<div class="col-sm-6">	
		@php 
			$checked = '';
			if(array_key_exists('variant',old())){
				if(old('variant') == 1){	$checked = 'checked'; }
			}else{	if($product->variant == 1){ $checked = 'checked';} }
		@endphp
		{{Form::checkbox('variant','1',$checked,['class'=>'flat-red','id'=>'variant'])}}
		</div>
	</div>
	@php
		$i = 1;
		$count = 1;
		if(count(old('options')) > 0){
			$count = count(old('options'));
		}elseif(count($product->variants) > 0){
			$count = count($product->variants);
		}
		
		// Make Combinations of Prices //
		
		$combinations = array();
		if(count($product->product_prices) > 0){
			foreach($product->product_prices as $val){
				$combinations[$val->min_area.'--'.$val['max_area']]['price'] = $val['price']; 
				$combinations[$val->min_area.'--'.$val['max_area']]['min_area'] = $val['min_area']; 
				$combinations[$val->min_area.'--'.$val['max_area']]['max_area'] = $val['max_area']; 
			}
		}
		
		// Make a array of old combinations //
		$old_combo = array();
		$weight_array = array();
		$shipping_array = array();
		//pr($product->variantCombinantion);
		if(count($product->variantCombinantion) > 0){
			foreach($product->variantCombinantion as $val){
				$key = $val->variant1;
				if($val->varient_id2 != ''){
					$key .= '--'.$val->variant2;
				}
				if($product->price_sqft_area == 1){
					$old_combo[$key][$val->min_area.'--'.$val['max_area']]['price'] = $val['price'];  
					$old_combo[$key][$val->min_area.'--'.$val['max_area']]['min_area'] = $val['min_area'];  
					$old_combo[$key][$val->min_area.'--'.$val['max_area']]['max_area'] = $val['max_area'];  
				}else{
					$old_combo[$key]['single']['price'] = $val['price'];
				}
				$weight_array[$key] = $val['shipping_weight'];
				$shipping_array[$key] = $val['shipping_price'];
			}
		}
		/* pr($combinations);
		pr($old_combo);
		pr($weight_array);
		pr($shipping_array);die; */
	@endphp
	<div class="form-group row {{($checked == 'checked')?'':'hide'}} variant_div">
		<label class="col-sm-3 form-control-label">&nbsp;</label>
		<div class="col-sm-9">	
			<div class="panel panel-info">
				<div class="panel-heading"><h5>Add Variants</h5></div>
				<div class="panel-body">
					<div class="variant_option_panel">
					@if(count($product->variants) > 0)
						@foreach($product->variants as $key=>$option)
							<div class="row varient_option">
								<div class="col-xs-4 col-sm-3 value">
									<div class="form-group">
										<label for="">Option Name</label>
										<input placeholder="Option Name" name="options[{{$option->id}}][name]" class="form-control" value="{{$option->name}}" required />
									</div>
								</div>
								<div class="col-xs-6 col-sm-5 value">
									<div class="form-group">
										@php
										$str = '';
										foreach($option->variantValues as $variant){
											$str .= $variant->value.',';
										}
										$str = trim($str,',');
										@endphp
										<label for="">Option Values</label>
										<input name="options[{{$option->id}}][value]" class="form-control variant_option_values option_value_{{$i}}" placeholder="Separate options with a comma"  value="{{$str}}" required />
									</div>
								</div>
								<div class="col-xs-2 col-sm-4 remove_div">
									<div class="form-group"> 
										<label>&nbsp;</label>
										<div class="clearfix"></div>
										<button class="btn btn-danger remove-varient-option" type="button"><i class="fa fa-trash"></i></button>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
						@php
						$i++;
						@endphp
						@endforeach
					@endif
					</div>
					<div class="clearfix"></div>
					<div class="row add_more_varient {{($count > 1)?'hide':''}}">
						<div class="col-md-2">
							<div class="form-group"> 
								<button class="btn btn-success add-variant-option" type="button">Add More</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group row {{($checked == 'checked')?'':'hide'}} variant_price_div">
		<label class="col-sm-3 form-control-label">&nbsp;</label>
		<div class="col-sm-9">	
			<div class="panel panel-info">
				<div class="panel-heading"><h5>Variants Prices</h5></div>
				<div class="panel-body">
					<div class="variant_price_panel">
					@php
					if(count($product->variantCombinantion) > 0){
						$j = 1;
						foreach($old_combo as $key1=>$price_option){
						if($j == 1){
					@endphp
						<div class="col-xs-12">
							<div class="box box-primary">
								<div class="box-body table-responsive">
									<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
									<thead>
										<tr><th class="nowrap">Varient</th>
										@if($product->price_sqft_area == 1)
											@foreach($combinations as $k=>$v)
												<th class="nowrap">Price (Area {{$k}} sqft)</th>
											@endforeach
										@else
											<th class="nowrap">Price</th>
										@endif
											<th class="nowrap">Additional Weight<span class="size_msg">(In LBS)</span></th>
											<th class="nowrap">Additional Flat Rate Shipping</th>
										<tr/>
									</thead>
									<tbody>
					@php
						}
						$str = explode('--',$key1);
					@endphp
						@if($product->price_sqft_area == 1)
							<tr>
								<td>
									<span class="text-success">{{$str[0]}}</span>
									@if(array_key_exists(1,$str))
										.<span class="text-danger">{{$str[1]}}</span>
									@endif
								</td>
							@foreach($combinations as $k=>$v)
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											@php
												if(array_key_exists($k,$price_option)){
													$input_key = $price_option[$k]['min_area'].'--'.$price_option[$k]['max_area'];
													$input_value = $price_option[$k]['price'];
												}else{
													$input_key = $k;
													$input_value = $v['price'];
												}
											@endphp
											@if(array_key_exists(1,$str))
											<input type="text" name="variant_option_price[{{$str[0]}}][{{$str[1]}}][{{$input_key}}][price]" value="{{$input_value}}" class="form-control" placeholder="Enter Price" required />
											@else
											<input type="text" name="variant_option_price[{{$str[0]}}][single][{{$input_key}}][price]" value="{{$input_value}}" class="form-control" placeholder="Enter Price" required />
											@endif
										</div>
									</td>
							@endforeach
								@if(array_key_exists(1,$str))
									<td>
									<input type="text" name="variant_option_weight[{{$str[0].'--'.$str[1]}}][weight]" value="{{$weight_array[$key1]}}" class="form-control" placeholder="Enter Weight"/>
									</td>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="variant_option_shipping[{{$str[0].'--'.$str[1]}}][shipping]" value="{{$shipping_array[$key1]}}" class="form-control" placeholder="Enter Rate"/>
										</div>
									</td>
									@else
									<td>
									<input type="text" name="variant_option_weight[{{$str[0]}}][weight]" value="{{$weight_array[$key1]}}" class="form-control" placeholder="Enter Weight"/>
									</td>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="variant_option_shipping[{{$str[0]}}][shipping]" value="{{$shipping_array[$key1]}}" class="form-control" placeholder="Enter Rate"/>
										</div>
									</td>
								@endif
							</tr>
						@else
							<tr>
								<td>
									<span class="text-success">{{$str[0]}}</span>
									@if(array_key_exists(1,$str))
										.<span class="text-danger">{{$str[1]}}</span>
									@endif
								</td>
								<td>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										@if(array_key_exists(1,$str))
										<input type="text" name="variant_option_price[{{$str[0]}}][{{$str[1]}}][default][price]" value="{{$price_option['single']['price']}}" class="form-control" placeholder="Enter Price" required />
										@else
										<input type="text" name="variant_option_price[{{$str[0]}}][single][default][price]" value="{{$price_option['single']['price']}}" class="form-control" placeholder="Enter Price" required />
										@endif
									</div>
								</td>
								@if(array_key_exists(1,$str))
									<td>
									<input type="text" name="variant_option_weight[{{$str[0].'--'.$str[1]}}][weight]" value="{{$weight_array[$key1]}}" class="form-control" placeholder="Enter Weight"/>
									</td>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="variant_option_shipping[{{$str[0].'--'.$str[1]}}][shipping]" value="{{$shipping_array[$key1]}}" class="form-control" placeholder="Enter Rate"/>
										</div>
									</td>
									@else
									<td>
									<input type="text" name="variant_option_weight[{{$str[0]}}][weight]" value="{{$weight_array[$key1]}}" class="form-control" placeholder="Enter Weight"/>
									</td>
									<td>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="text" name="variant_option_shipping[{{$str[0]}}][shipping]" value="{{$shipping_array[$key1]}}" class="form-control" placeholder="Enter Rate"/>
										</div>
									</td>
								@endif
							</tr>
						@endif
					@php
						$j++;
						}
					@endphp
									</tbody>
									</table>
								</div>
							</div>
						</div>
					@php
					}
					@endphp
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="line"></div>
	<div class="form-group row">
		<label class="col-sm-3 form-control-label">&nbsp;</label>
		<div class="col-sm-4 offset-sm-2">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	</div>
</form>
<script>
$(document).ready(function(){
	
	var old_combo = <?php echo json_encode($old_combo,JSON_PRETTY_PRINT);?>;
	var combinations = <?php echo json_encode($combinations,JSON_PRETTY_PRINT);?>;
	var weight_array = <?php echo json_encode($weight_array,JSON_PRETTY_PRINT);?>;
	var shipping_array = <?php echo json_encode($shipping_array,JSON_PRETTY_PRINT);?>;
	//console.log(weight_array);
	
	var variant_count = Number('{{$count}}');
	//alert(variant_count)
	$(document).on('ifChecked','[name="variant"]', function(event){
		$('div.variant_option_panel').html('');
		var str = '<div class="row varient_option">';
		
		str += '<div class="col-xs-4 col-sm-3 value"><div class="form-group"><label for="">Option Name</label><input placeholder="Option Name" name="options['+variant_count+'][name]" class="form-control" required/></div></div>';
		
		str += '<div class="col-xs-6 col-sm-5 value"><div class="form-group"><label for="">Option Values</label><input name="options['+variant_count+'][value]" class="form-control variant_option_values option_value_'+variant_count+'" placeholder="Separate options with a comma" required/></div></div>';
		
		str += '<div class="col-xs-2 col-sm-4 hide remove_div"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-varient-option" type="button"><i class="fa fa-trash"></i></button></div></div><div class="clearfix"></div></div>';
		
		$('div.variant_option_panel').append(str);
		$('.variant_div').removeClass('hide');
		$('.variant_div').slideDown();
		$('.variant_option_values').tokenfield({
		  autocomplete: {
			source: [],
			delay: 100
		  },
		  showAutocompleteOnFocus: true,
		  createTokensOnBlur: true,
		});
	});
	
	$(document).on('ifUnchecked','[name="variant"]', function(event){
		$('div.variant_option_panel').html('');
		$('.variant_price_panel').html('');
		$('.variant_div').slideUp();
		$('.variant_price_div').slideUp();
		variant_count = 1;
	});
	
	$(document).on('click','.add-variant-option', function () {
		variant_count++;
		var str = '<div class="row varient_option">';
		str += '<div class="col-xs-4 col-sm-3 value"><div class="form-group"><label for="">Option Name</label><input placeholder="Option Name" name="options['+variant_count+'][name]" class="form-control" required/></div></div>';
		
		str += '<div class="col-xs-6 col-sm-5 value"><div class="form-group"><label for="">Option Values</label><input name="options['+variant_count+'][value]" class="form-control variant_option_values option_value_'+variant_count+'" placeholder="Separate options with a comma" required/></div></div>';
		
		str += '<div class="col-xs-2 col-sm-4 remove_div"><div class="form-group"> <label>&nbsp;</label><div class="clearfix"></div><button class="btn btn-danger remove-varient-option" type="button"><i class="fa fa-trash"></i></button></div></div><div class="clearfix"></div></div>';
			
		$('.variant_option_panel').append(str);
		$('.remove_div').removeClass('hide');
		
		$('.variant_option_values').tokenfield({
		  autocomplete: {
			source: [],
			delay: 100
		  },
		  showAutocompleteOnFocus: true,
		  createTokensOnBlur: true,
		});
		
		if(variant_count == 2){
			$('.add_more_varient').addClass('hide');
		}
	});
	
	
	$(document).on('click', '.remove-varient-option', function () {
		//alert(variant_count);
		if(confirm('Are You sure want to delete it ?')){
			$(this).closest('.varient_option').remove();
			variant_count--;
			$('.add_more_varient').removeClass('hide');
			if(variant_count < 1){
				$('[name="variant"]').iCheck('uncheck');
				variant_count = 1;
			}else{
				add_option_price();	
			}
		}
	});
	
	setTimeout(function() {
		$(document).on('tokenfield:createtoken','.variant_option_values', function(e) {
			//var value = e.attrs.value;
			setTimeout(function() {
				add_option_price();
			 }, 10);
		});
	 }, 15);
	$(document).on('tokenfield:removedtoken','.variant_option_values', function (event) {
		add_option_price();
	});
	
	
	
	function add_option_price(){
		
		$('.variant_price_panel').html('');
		var price = 0;
		if($('#price').val() != ''){
			price = $('#price').val();
		}
		var str = '<div class="col-xs-12"><div class="box box-primary"><div class="box-body table-responsive"><table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;"><thead>';
		
		str += '<tr class="nowrap"><th>Varient</th>';
		
		if($('#price_sqft_area').is(':checked')){
			$.each(combinations,function(i,v){
				str += '<th>Price (Area '+i+' sqft)</th>';
			});
				
			str += '<th class="nowrap">Weight<span class="size_msg">(In LBS)</span></th>';
			str += '<th class="nowrap">Additional Flat Rate shipping</th>';
			
			str += '</tr></thead><tbody>';
		}else{
			str += '<th class="nowrap">Price</th><th class="nowrap">Weight<span class="size_msg">(In LBS)</span></th><th class="nowrap">Additional Flat Rate shipping</th></tr></thead><tbody>';
		}
		
		var existingTokens = $('input.option_value_1').tokenfield('getTokens');
		$.each(existingTokens, function(index, token) {
			//alert($('input.option_value_2').length)
			//if($('input[name="options[2][value]"]').length && $('input[name="options[2][value]"]').val().length){
			if($('input.option_value_2').length > 0){
				
				var existingTokens1 = $('input.option_value_2').tokenfield('getTokens');
				$.each(existingTokens1, function(index1, token1) {
					str += '<tr>';
					str += '<td><span class="text-success">'+token.value+'</span>.<span clas="text-denger">'+token1.value+'</span></td>';

					var tck1 = escape(token.value);
					var tck2 = escape(token1.value);
					
					if($('#price_sqft_area').is(':checked')){
						$.each(combinations,function(i,v){
							var varient_price = v.price;
							$.each(old_combo,function(k,g){
								if(k == (token.value+'-'+token1.value)){
									var dfg = v.min_area+'--'+v.max_area;
									//alert(dfg);
									$.each(g,function(k1,g1){
										if(k1 == dfg){
											varient_price = g1.price;
										}
									});
								}
							});
							
							str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="variant_option_price['+tck1+']['+tck2+']['+v.min_area+'--'+v.max_area+'][price]" value="'+varient_price+'" class="form-control" placeholder="Enter Price" required /></div></td>';
						});
					}else{
						var varient_price = price;
						$.each(old_combo,function(k,g){
							if(k == (token.value+'--'+token1.value)){
								varient_price = g.single.price;
							}
						});
						str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="variant_option_price['+tck1+']['+tck2+'][default][price]" value="'+varient_price+'" class="form-control" placeholder="Enter Price" required /></div></td>';
					}
					
					var weight = '';
					var shipping = '';
						
					$.each(weight_array,function(index,val){
						if(index == (token.value+'--'+token1.value)){
							if(val != null && val != '')
								weight = val;
						}
					})
						
					$.each(shipping_array,function(index,val){
						if(index == (token.value+'--'+token1.value)){
							if(val != null && val != '')
								shipping = val;
						}
					})
					
					str += '<td><input type="text" name="variant_option_weight['+tck1+'--'+tck2+'][weight]" value="'+weight+'" class="form-control" placeholder="Enter Weight"/></td>';
					
					str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="variant_option_shipping['+tck1+'--'+tck2+'][shipping]" value="'+shipping+'" class="form-control" placeholder="Enter Rate"/></div></td>';
					
					str += '</tr>'; 
				});
			}else{
				str += '<tr>';
				str += '<td><span class="text-success">'+token.value+'</span></td>';
				
				var tck1 = escape(token.value);
				
				if($('#price_sqft_area').is(':checked')){
					$.each(combinations,function(i,v){
						var varient_price = v.price;
						$.each(old_combo,function(k,g){
							if(k == (token.value)){
								var dfg = v.min_area+'--'+v.max_area;
								$.each(g,function(k1,g1){
									if(k1 == dfg){
										varient_price = g1.price;
									}
								});
							}
						});
						str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="variant_option_price['+tck1+'][single]['+v.min_area+'--'+v.max_area+'][price]" value="'+varient_price+'" class="form-control" placeholder="Enter Price" required /></div></td>';
					});
				}else{
					var varient_price = price;
					$.each(old_combo,function(k,g){
						if(k == (token.value)){
							varient_price = g.single.price;
						}
					});
					str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="variant_option_price['+tck1+'][single][default][price]" value="'+varient_price+'" class="form-control" placeholder="Enter Price" required /></div></td>';
				}
				
				var weight = '';
				var shipping = '';
				
				$.each(weight_array,function(index,val){
					if(index == token.value){
						if(val != null && val != '')
							weight = val;
					}
				})
				
				$.each(shipping_array,function(index,val){
					if(index == token.value){
						if(val != null && val != '')
							shipping = val;
					}
				})
				
				str += '<td><input type="text" name="variant_option_weight['+tck1+'][weight]" value="'+weight+'" class="form-control" placeholder="Enter Weight"/></td>';
				
				str += '<td><div class="input-group"><span class="input-group-addon">$</span><input type="text" name="variant_option_shipping['+tck1+'][shipping]" value="'+shipping+'" class="form-control" placeholder="Enter Rate"/></div></td>';
					
				str += '</tr>'; 
			}
		});
		
		str += '</tbody></table></div></div></div>';
		$('.variant_price_panel').append(str);
		$('.variant_price_div').removeClass('hide');
		$('.variant_price_div').slideDown();
	}
	
	$('.variant_option_values').tokenfield({
	  autocomplete: {
		source: [],
		delay: 100
	  },
	  showAutocompleteOnFocus: true,
	  createTokensOnBlur: true,
	});
});
</script>
<!-- product variant info form -->