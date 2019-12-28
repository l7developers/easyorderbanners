<div class="form-group row {{($checked == 'checked')?'':'hide'}} variant_div">
	<label class="col-sm-3 form-control-label">&nbsp;</label>
	<div class="col-sm-9">	
		<div class="panel panel-info">
			<div class="panel-heading"><h5>Add Variants</h5></div>
			<div class="panel-body">
				<div class="variant_option_panel">
				@if((array_key_exists('variant',old()) and old('variant') == 1))
					@foreach(old('options') as $key=>$option)
						<div class="row varient_option">
							<div class="col-xs-4 col-sm-3 value">
								<div class="form-group">
									<label for="">Option Name</label>
									<input placeholder="Option Name" name="options[{{$key}}][name]" class="form-control" value="{{$option['name']}}" required />
								</div>
							</div>
							<div class="col-xs-6 col-sm-5 value">
								<div class="form-group">
									<label for="">Option Values</label>
									<input name="options[{{$key}}][value]" class="form-control variant_option_values option_value_{{$i}}" placeholder="Separate options with a comma"  value="{{$option['value']}}" required />
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
				@elseif(count($product->variants) > 0)
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
				<div class="row add_more_varient {{($i>2)?'hide':''}}">
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
					$j = 1;
				@endphp
				@if((array_key_exists('variant',old()) and old('variant') == 1))
					@foreach(old('variant_option_price') as $key1=>$price_option)
					@if($j == 1)
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-body table-responsive">
								<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
								<thead>
									<tr>
										<th>Varient</th>
						@if(array_key_exists('price_sqft_area',old()) and old('price_sqft_area') == 1)
											<th>Price (Area 0-300 sqft)</th>
											<th>Price (Area 301-500 sqft)</th>
											<th>Price (Area 501-1000 sqft)</th>
											<th>Price (Area above 1001+ sqft)</th>
									</tr>
								</thead>
							<tbody>
						@else
										<th>Price</th>
									</tr>
								</thead>
							<tbody>
						@endif
					@endif
					<?php //pr($price_option);die; ?>
					@if(is_array($price_option) and !array_key_exists('price',$price_option))
						
						@foreach($price_option as $key2=>$price)
						<tr>
							<td>
								<span class="text-success">{{(array_key_exists($key1,$variant_values))?$variant_values[$key1]:$key1}}</span>.
								<span clas="text-denger">{{(array_key_exists($key2,$variant_values))?$variant_values[$key2]:$key2}}</span>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][{{$key2}}][price]" value="{{$price['price']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@if(count($price) > 1)
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][{{$key2}}][price_300]" value="{{$price['price_300']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][{{$key2}}][price_500]" value="{{$price['price_500']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][{{$key2}}][price_1000]" value="{{$price['price_1000']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@endif
						</tr>
						@endforeach
					@else
						<tr>
							<td><span class="text-success">{{(array_key_exists($key1,$variant_values))?$variant_values[$key1]:$key1}}</span></td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][price]" value="{{$price_option['price']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@if(array_key_exists('price_sqft_area',old()) and old('price_sqft_area') == 1)
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][price_300]" value="{{$price_option['price_300']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][price_500]" value="{{$price_option['price_500']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$key1}}][price_1000]" value="{{$price_option['price_1000']}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@endif
						</tr>
					@endif
						
					@php
					$j++;
					@endphp
					@endforeach
					</tbody></table></div></div></div>
				@elseif(count($product->variantCombinantion) > 0)
				<?php //pr($product->variantCombinantion->toArray());die; ?>
					@foreach($product->variantCombinantion as $key1=>$price_option)
					
					@if($j == 1)
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-body table-responsive">
								<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
								<thead>
									<tr>
										<th>Varient</th>
						@if($product->price_sqft_area == 1)
											<th>Price (Area 0-300 sqft)</th>
											<th>Price (Area 301-500 sqft)</th>
											<th>Price (Area 501-1000 sqft)</th>
											<th>Price (Area above 1001+ sqft)</th>
									</tr>
								</thead>
							<tbody>
						@else
										<th>Price</th>
									</tr>
								</thead>
							<tbody>
						@endif
					@endif
					@if(!empty($price_option->varient_id2))
						<tr>
							<td>
								<span class="text-success">{{$price_option->variant1}}</span>.<span clas="text-denger">{{$price_option->variant2}}</span>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][{{$price_option->varient_id2}}][price]" value="{{$price_option->price}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@if($product->price_sqft_area == 1)
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][{{$price_option->varient_id2}}][price_300]" value="{{$price_option->price_300}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][{{$price_option->varient_id2}}][price_500]" value="{{$price_option->price_500}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][{{$price_option->varient_id2}}][price_1000]" value="{{$price_option->price_1000}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@endif
						</tr>
					@else
						<tr>
							<td><span class="text-success">{{$variant_values[$price_option->varient_id1]}}</span></td>
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][price]" value="{{$price_option->price}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@if(!empty($price_option->price_300))
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][price_300]" value="{{$price_option->price_300}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@endif
							@if(!empty($price_option->price_500))
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][price_500]" value="{{$price_option->price_500}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@endif
							@if(!empty($price_option->price_1000))
							<td>
								<div class="form-group">
									<input type="text" name="variant_option_price[{{$price_option->varient_id1}}][price_1000]" value="{{$price_option->price_1000}}" class="form-control" placeholder="Enter Price" required />
								</div>
							</td>
							@endif
						</tr>
					@endif
					@php
					$j++;
					@endphp
					@endforeach
					</tbody></table></div></div></div>
				@endif
				</div>
			</div>
		</div>
	</div>
</div>