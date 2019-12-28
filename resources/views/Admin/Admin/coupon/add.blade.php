@extends('layouts.admin_layout')
@section('content')
<?php
//pr($errors);
//pr(old());
?>
<section class="content-header">
	<h1>Add Coupon</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/coupon/add') }}">
					{{ csrf_field() }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('title') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Title<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('title', old('title') ,['class'=>'form-control','placeholder'=>'Enter title'])}}
									@if ($errors->has('title'))
										<span class="help-block">{{ $errors->first('title') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('code') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Coupon Code<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<div class="input-group input-group-sm">
										<input class="form-control" placeholder="Enter Coupon Code" name="code" id="code" value="{{old('code')}}"/>
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
									{{Form::select('type',[''=>'Select Discount Type','1'=>'Fix Amount','2'=>'Percentage' ,'3'=>'Free Shipping'],old('type') ,['class'=>'form-control','id'=>'type'])}}
									@if ($errors->has('type'))
										<span class="help-block">{{ $errors->first('type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('amount') ? ' has-error' : '' }} amount_div {{(old('type') == 1)?'':'hide'}}">
								<label class="col-sm-3 form-control-label">Amount<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{{Form::text('amount', old('amount') ,['class'=>'form-control','placeholder'=>'Enter amount'])}}
									</div>
									@if ($errors->has('amount'))
										<span class="help-block">{{ $errors->first('amount') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('percent') ? ' has-error' : '' }} percent_div {{(old('type') == 2)?'':'hide'}}">
								<label class="col-sm-3 form-control-label">Percent<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<div class="input-group">
										<span class="input-group-addon">%</span>
										{{Form::text('percent', old('percent') ,['class'=>'form-control','placeholder'=>'Enter percent'])}}
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
										{{Form::text('min_cart', old('min_cart') ,['class'=>'form-control','placeholder'=>'Enter minimum cart amount'])}}
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
										{{Form::text('max_discount', old('max_discount') ,['class'=>'form-control','placeholder'=>'Enter Maximum Discount Amount'])}}
									</div>
									@if ($errors->has('max_discount'))
										<span class="help-block">{{ $errors->first('max_discount') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('expiry_date') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Expiry Date<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::text('expiry_date', old('expiry_date') ,['class'=>'form-control date-picker','placeholder'=>'Enter Expiry Date'])}}
									@if ($errors->has('expiry_date'))
										<span class="help-block">{{ $errors->first('expiry_date') }}</span>
									@endif
									
								</div>
							</div>
							<div class="form-group row{{ $errors->has('single_time') ? ' has-error' : '' }} width_height_div">
								<label class="col-sm-3 form-control-label">Single time use per user</label>
								<div class="col-sm-4" id="single_time">
									{{Form::checkbox('single_time','1',(array_key_exists('single_time',old()) and old('single_time') == 1)?true:false,['class'=>"flat-red"])}}
								</div>
							</div>
							<div class="form-group row{{ $errors->has('users_type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Coupon Users<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									{{Form::select('users_type',[''=>'Select User','1'=>'All','2'=>'Selected User'] ,old('users_type') ,['class'=>'form-control','id'=>'users_type'])}}
									@if ($errors->has('users_type'))
										<span class="help-block">{{ $errors->first('users_type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('users') ? ' has-error' : '' }} users_div {{(old('users_type') == 2)?'':'hide'}}">
								{{ Form::label('users[]', '&nbsp;',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">	{{Form::select('users[]',$users_list,old('users'),['class'=>'form-control select2','id'=>'users','multiple'])}}
									@if ($errors->has('users'))
										<span class="help-block">{{ $errors->first('users') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('product_type') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Coupon Products<span class="text-danger">*</span></label>
								<div class="col-sm-6">	{{Form::select('product_type',['1'=>'All','2'=>'Selected Product'] ,old('product_type') ,['class'=>'form-control','placeholder'=>'Select Products','id'=>'product_type'])}}
									@if ($errors->has('product_type'))
										<span class="help-block">{{ $errors->first('product_type') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('products') ? ' has-error' : '' }} product_div {{(old('product_type') == 2)?'':'hide'}}">
								{{ Form::label('products[]', '&nbsp;',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-6">	{{Form::select('products[]',$products_list,old('products'),['class'=>'form-control select2','id'=>'products','multiple'])}}
									@if ($errors->has('products'))
										<span class="help-block">{{ $errors->first('products') }}</span>
									@endif
								</div>
							</div>
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-6 offset-sm-2">
								<button type="submit" class="btn btn-primary">Save</button>
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