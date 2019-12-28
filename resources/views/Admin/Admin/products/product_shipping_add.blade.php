@extends('layouts.admin_layout')
@section('content')
<?php
//pr($errors);
//pr(old());
?>
<section class="content-header">
	<h1>Set Product Shipping</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					{{Form::model('add_shipping',['url'=>url('admin/products/shipping/'.$id)])}}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('type') ? ' has-error' : '' }}">
								{{ Form::label('type', 'Shipping Type',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									@php
										$value = '';
										if(array_key_exists('type',old())){
											$value = old('type');
										}else{
											if(count($shipping) > 0){
												$value = $shipping->type;
											}
										}
									@endphp
									{{Form::select('type',['free'=>'Free Shipping','free_value'=>'Free Shipping Based On Order Value','paid'=>'Paid Shipping','flat'=> 'Flat Rate Shipping'], $value ,['class'=>'form-control','placeholder'=>'Select Type','id'=>'type'])}}
									@if ($errors->has('type'))
										<span class="help-block">{{ $errors->first('type') }}</span>
									@endif
								</div>
							</div>
							@php
								$str = 'display:none';
								$value = '';
								if(array_key_exists('type',old())){
									if(old('type') == 'free_value'){
										$str = '';
									}
									
									if(array_key_exists('min_value',old()) and old('min_value') != ''){
										$value = old('min_value');
									}
								}else{
									if(count($shipping) > 0){
										if($shipping->type == 'free_value'){
											$str = '';
										}
										$value = $shipping->min_value;
									}
								}
							@endphp
							<div class="form-group row value_div {{ $errors->has('min_value') ? ' has-error' : '' }}" style="{{$str}}">
								{{ Form::label('min_value', 'Min. Order Value',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('min_value', $value ,['class'=>'form-control','placeholder'=>'Enter Minimum Order Value'])}}
									</div>
									@if ($errors->has('min_value'))
										<span class="help-block">{{ $errors->first('min_value') }}</span>
									@endif
								</div>
							</div>
							@php
								$str = 'display:none';
								$weight = 'display:block';
								$value = '';
								if(array_key_exists('type',old())){
									if(old('type') == 'flat'){
										$str = '';
										$weight = 'display:none';
									}
									
									if(array_key_exists('price',old()) and old('price') != ''){
										$value = old('price');
									}
								}else{
									if(count($shipping) > 0){
										if($shipping->type == 'flat'){
											$str = '';
											$weight = 'display:none';
										}
										$value = $shipping->price;
									}
								}
							@endphp
							<div class="form-group row price_div {{ $errors->has('price') ? ' has-error' : '' }}" style="{{$str}}">
								{{ Form::label('price', 'Price',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('price', $value ,['class'=>'form-control','placeholder'=>'Enter Flat Price'])}}
									</div>
									@if ($errors->has('price'))
										<span class="help-block">{{ $errors->first('price') }}</span>
									@endif
								</div>
							</div>
							@php
								$check = '';	if(array_key_exists('reduce_price',old()) and old('reduce_price') == 1){
									$check = 'checked';
								}else{
									if(count($shipping) > 0){
										if($shipping->reduce_price == 1){
											$check = 'checked';
										}
									}
								}
								
								$value = '';	if(array_key_exists('additional_qty_price',old())){	
									if(array_key_exists('additional_qty_price',old()) and old('additional_qty_price') != ''){
										$value = old('additional_qty_price');
									}
								}else{
									if(count($shipping) > 0){
										if($shipping->type == 'flat'){
											$value = $shipping->additional_qty_price;
										}
									}
								}
							@endphp
							<div class="form-group row reduce_div {{ $errors->has('reduce_price') ? ' has-error' : '' }}" style="{{$str}}">
								{{ Form::label('reduce_price', 'Reduced price for additional Quantity?',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									{{Form::checkbox('reduce_price', '1', $check ,['class'=>'flat-red'])}}
									@if ($errors->has('reduce_price'))
										<span class="help-block">{{ $errors->first('reduce_price') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row reduce_price_div {{ $errors->has('additional_qty_price') ? ' has-error' : '' }}" style="{{$str}}">
								{{ Form::label('additional_qty_price', 'Additional Quantity Price',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('additional_qty_price', $value ,['class'=>'form-control','placeholder'=>'Enter Additional Price'])}}
									</div>
									@if ($errors->has('additional_qty_price'))
										<span class="help-block">{{ $errors->first('additional_qty_price') }}</span>
									@endif
								</div>
							</div>
							<?php /* ?>
							<div class="form-group row weight_div{{ $errors->has('weight') ? ' has-error' : '' }}" style="{{$weight}}">
								@php
									$value = '';
									if(array_key_exists('weight',old())){
										$value = old('weight');
									}else{
										if(count($shipping) > 0){
											//$value = $shipping->weight;
										}
										$value = $product->shipping_weight;
									}
								@endphp
								<label class="col-sm-3 form-control-label">Product Weight
									@if($product->show_width_height == 1)
										<br/><span class="size_msg">(Per SQFT In LBS)</span>
									@else
										<br/><span class="size_msg">(In LBS)</span>
									@endif
								</label>
								<div class="col-sm-6">
									{{Form::text('weight', $value ,['class'=>'form-control','placeholder'=>'Enter Weight'])}}
									@if ($errors->has('weight'))
										<span class="help-block">{{ $errors->first('weight') }}</span>
									@endif
								</div>
							</div><?php  */?>
							<div class="line"></div>
							<div class="form-group">
								<div class="form-group row">
									<label class="col-sm-3 form-control-label">&nbsp;</label>
									<div class="col-sm-4 offset-sm-2">
										<button type="submit" class="btn btn-primary">Set Shipping</button>
									</div>
								</div>
							</div>
						</div>
					{{Form::close()}}
				</div>
			</div>
		</div>
	</div>
</section>
<script>
//$('.users_div').slideUp();

$(document).on('change','#type',function(){
	var value = $(this).val();
	if(value == 'free_value'){
		$('.value_div').slideDown();
		//$('.weight_div').slideDown();
		$('.price_div').slideUp();
		$('.reduce_div').slideUp();
		$('.reduce_price_div').slideUp();
		$('input[type="checkbox"]').iCheck('uncheck'); 
	}else if(value == 'flat'){
		$('.price_div').slideDown();
		$('.reduce_div').slideDown();
		$('.reduce_price_div').slideDown();
		$('.value_div').slideUp();
		//$('.weight_div').slideUp();
	}
	else{
		$('.price_div').slideUp();
		$('.reduce_div').slideUp();
		$('.reduce_price_div').slideUp();
		$('.value_div').slideUp();
		//$('.weight_div').slideDown();
		$('input[type="checkbox"]').iCheck('uncheck');
	}
});
</script>	  
@endsection		  