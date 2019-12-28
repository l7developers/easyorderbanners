@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<h1>Discount Edit</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/coupon/edit/'.$id) }}">
					{{ csrf_field() }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('title') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Title<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('title',array_key_exists('title',old())?old('title'):$coupon->title ,['class'=>'form-control','placeholder'=>'Enter title'])}}
									@if ($errors->has('title'))
										<span class="help-block">{{ $errors->first('title') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('code') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Coupon Code<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<div class="input-group input-group-sm">
										<input class="form-control" placeholder="Enter Coupon Code" name="code" id="code" value="{{array_key_exists('code',old())?old('code'):$coupon->code}}"/>
										<span class="input-group-btn">
											<button onclick="regeneratecode('discount_code')" type="button" class="btn btn-primary btn-lrg ajax" title="Ajax Request"><i class="fa fa-refresh"></i>&nbsp; Get Code</button>
										</span>
									</div>
									@if ($errors->has('code'))
										<span class="help-block">{{ $errors->first('code') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Discount Type<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									@php
									$type = '';
									if($coupon->type == 'amount')
										$type = 1;
									else if($coupon->type == 'percent')
										$type = 2;
									else if($coupon->type == 'free_shipping')
										$type = 3;
									@endphp
									{{Form::select('type',[''=>'Select Discount Type','1'=>'Fix Amount','2'=>'Percentage',3=>'Free Shipping'] ,array_key_exists('type',old())?old('type'):$type  ,['class'=>'form-control','id'=>'type'])}}
									@if ($errors->has('type'))
										<span class="help-block">{{ $errors->first('type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('amount') ? ' has-error' : '' }} amount_div {{($type == 1)?'':'hide'}}">
								<label class="col-sm-3 form-control-label">Amount<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									@php
									$val = '';
									if(old('type') == 1 and !empty(old('amount'))){
										$val = old('amount');
									}else{
										if($type == 1)
											$val = $coupon->type_value;
									}
									@endphp
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('amount', $val ,['class'=>'form-control','placeholder'=>'Enter amount'])}}
									</div>
									@if ($errors->has('amount'))
										<span class="help-block">{{ $errors->first('amount') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('percent') ? ' has-error' : '' }} percent_div {{($type == 2)?'':'hide'}}">
								<label class="col-sm-3 form-control-label">Percent<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									@php
									$val = '';
									if(old('type') == 2 and !empty(old('percent'))){
										$val = old('percent');
									}else{
										if($type == 2)
											$val = $coupon->type_value;
									}
									@endphp
									<div class="input-group">
										<span class="input-group-addon">%</span>
										{{Form::text('percent', $val ,['class'=>'form-control','placeholder'=>'Enter percent'])}}
									</div>
									@if ($errors->has('percent'))
										<span class="help-block">{{ $errors->first('percent') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('min_cart') ? ' has-error' : '' }}">
								{{ Form::label('min_cart', 'Mini. Cart Amount',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('min_cart', array_key_exists('min_cart',old())?old('min_cart'):$coupon->min_cart ,['class'=>'form-control','placeholder'=>'Enter minimum cart amount'])}}
									</div>
									@if ($errors->has('min_cart'))
										<span class="help-block">{{ $errors->first('min_cart') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('max_discount') ? ' has-error' : '' }}">
								{{ Form::label('max_discount', 'Max. Discount Amount',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('max_discount', array_key_exists('max_discount',old())?old('max_discount'):$coupon->max_discount,['class'=>'form-control','placeholder'=>'Enter Maximum Discount Amount'])}}
									</div>
									@if ($errors->has('max_discount'))
										<span class="help-block">{{ $errors->first('max_discount') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('expiry_date') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Expiry Date<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('expiry_date', array_key_exists('expiry_date',old())?old('expiry_date'):date('Y-m-d',strtotime($coupon->expire_date)) ,['class'=>'form-control date-picker','placeholder'=>'Enter Expiry Date'])}}
									@if ($errors->has('expiry_date'))
										<span class="help-block">{{ $errors->first('expiry_date') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('single_time') ? ' has-error' : '' }} width_height_div">
								<label class="col-sm-3 form-control-label">Single Time Use</label>
								<div class="col-sm-4" id="single_time">
									@php 
									$checked = '';
									if(array_key_exists('single_time',old())){
										if(old('single_time') == 1){	$checked = 'checked'; }
									}else{	if($coupon->single_time == 1){ $checked = 'checked';} }
									@endphp
									{{Form::checkbox('single_time','1',$checked,['class'=>"flat-red"])}}
								</div>
							</div>
							<div class="form-group row{{ $errors->has('users_type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Coupon Users<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									@php
									$user_type = '';
									if(empty($coupon->users))
										$user_type = 1;
									else
										$user_type = 2;
									@endphp
									
									{{Form::select('users_type',['1'=>'All','2'=>'Selected User'] ,array_key_exists('users_type',old())?old('users_type'): $user_type,['class'=>'form-control','id'=>'users_type'])}}
									@if ($errors->has('users_type'))
										<span class="help-block">{{ $errors->first('users_type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('users') ? ' has-error' : '' }} users_div {{($user_type == 2)?'':'hide'}}">
								{{ Form::label('users[]', '&nbsp;',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">	{{Form::select('users[]',$users_list,array_key_exists('users',old())?old('users'):(explode(',',$coupon->users)),['class'=>'form-control select2','id'=>'users','multiple'])}}
									@if ($errors->has('users'))
										<span class="help-block">{{ $errors->first('users') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('product_type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Coupon Products<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									@php
									$product_type = '';
									if(empty($coupon->products))
										$product_type = 1;
									else
										$product_type = 2;
									@endphp
									
									{{Form::select('product_type',['1'=>'All','2'=>'Selected Products'] ,array_key_exists('product_type',old())?old('product_type'): $product_type,['class'=>'form-control','id'=>'product_type'])}}
									@if ($errors->has('product_type'))
										<span class="help-block">{{ $errors->first('product_type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('products') ? ' has-error' : '' }} product_div {{($product_type == 2)?'':'hide'}}">
								{{ Form::label('products[]', '&nbsp;',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">	{{Form::select('products[]',$products_list,array_key_exists('products',old())?old('products'):(explode(',',$coupon->products)),['class'=>'form-control select2','id'=>'products','multiple'])}}
									@if ($errors->has('products'))
										<span class="help-block">{{ $errors->first('products') }}</span>
									@endif
								</div>
							</div>
							
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-9 offset-sm-2">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{url('/admin/coupon/lists')}}" class="btn btn-warning">Back</a>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
//$('.users_div').slideUp();

$(document).on('change','#type',function(){
	var value = $(this).val();
	if(value == 1){
		$('.amount_div').removeClass('hide');
		$('.amount_div').slideDown();
		$('.percent_div').slideUp();
	}
	else if(value == 2){
		$('.percent_div').removeClass('hide');
		$('.percent_div').slideDown();
		$('.amount_div').slideUp();
	}else{
		$('.percent_div').slideUp();
		$('.amount_div').slideUp();
	}
});
$(document).on('change','#users_type',function(){
	var value = $(this).val();
	if(value == 2){
		$('.users_div').removeClass('hide');
		$('.users_div').slideDown();
		//$('#users').attr('required', true);
		$(".select2").select2({placeholder: "Select Values",});
	}else{
		$('.users_div').slideUp();
		//$('#users').attr('required', false);
		$("#users").val("");
	}
});

$(document).on('change','#product_type',function(){
	var value = $(this).val();
	if(value == 2){
		$('.product_div').removeClass('hide');
		$('.product_div').slideDown();
		$(".select2").select2({placeholder: "Select Values",});
	}else{
		$('.product_div').slideUp();
		$("#products").val("");
	}
});

function regeneratecode(){
	$('input#code').val(randomString(6));
}
function randomString(len, charSet) {
    charSet = charSet || '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var randomString = '';
    for (var i = 0; i < len; i++) {
        var randomPoz = Math.floor(Math.random() * charSet.length);
        randomString += charSet.substring(randomPoz,randomPoz+1);
    }
    return randomString;
}
</script>	  				  
@endsection		  