@extends('layouts.admin_layout')
@section('content')

<section class="content-header">
	<h1>Edit Customer</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body">		
					<form role="form" method="POST" action="{{ url('admin/users/edit/'.$id) }}">
					{{ csrf_field() }}
						<div class="col-sm-12">
							<div class="form-group row{{ $errors->has('company_name') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Company Name</label>
								<div class="col-sm-6">
									<input id="company_name" class="form-control" name="company_name" value="{{ (old('company_name')) ? old('company_name'):$user->company_name }}" placeholder="Company Name" >
									@if ($errors->has('company_name'))
										<span class="help-block">{{ $errors->first('company_name') }}</span>
									@endif
								</div>
							</div>
							
							<div class="form-group row{{ $errors->has('fname') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">First Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="fname" type="text" class="form-control" name="fname" value="{{ (old('fname')) ? old('fname') : $user->fname }}" placeholder="First Name">
									@if ($errors->has('fname'))
										<span class="help-block">{{ $errors->first('fname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('lname') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Last Name<span class="text-danger">*</span></label>
								<div class="col-sm-6">
								<input id="lname" type="text" class="form-control" name="lname" value="{{ (old('lname')) ? old('lname') : $user->lname }}" placeholder="Last Name">
									@if ($errors->has('lname'))
										<span class="help-block">{{ $errors->first('lname') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('email') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Email<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="email" type="email" class="form-control" name="email" value="{{ (old('email')) ? old('email') : $user->email }}" placeholder="Email">
									@if ($errors->has('email'))
										<span class="help-block">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row{{ $errors->has('password') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Password<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="password" type="password" class="form-control" name="password" placeholder="Password">
									@if ($errors->has('password'))
										<span class="help-block">{{ $errors->first('password') }}</span>
									@endif
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">Confirm Password<span class="text-danger">*</span></label>
								<div class="col-sm-6">
									<input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
								</div>
							</div>
							<div class="form-group row{{ $errors->has('phone_number') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Phone Number</label>
								<div class="col-sm-6">
									<input id="phone_number" type="text" class="form-control" name="phone_number" value="{{ (old('phone_number')) ? old('phone_number') : $user->phone_number }}" placeholder="Phone Number">
									@if ($errors->has('phone_number'))
										<span class="help-block">{{ $errors->first('phone_number') }}</span>
									@endif
								</div>
							</div>							
							@php
							if(array_key_exists('pay_by_invoice',old())){
								if(old('pay_by_invoice') == 1){
									$checked = true;
								}
								else{
									$checked = false;
								}
							}else{
								if($user->pay_by_invoice == 1){
									$checked = true;
								}
								else{
									$checked = false;
								}
							}
							if(array_key_exists('tax_exempt',old())){
								if(old('tax_exempt') == 1){
									$tax_exempt = true;
								}
								else{
									$tax_exempt = false;
								}
							}else{
								if($user->tax_exempt == 1){
									$tax_exempt = true;
								}
								else{
									$tax_exempt = false;
								}
							}
							@endphp
							<div class="form-group row{{ $errors->has('pay_by_invoice') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Pay by Invoice</label>
								<div class="col-sm-4" id="paid_free_div">	{{Form::checkbox('pay_by_invoice',1,$checked,['class'=>'flat-red'])}}
								</div>
							</div>
							<div class="form-group row{{ $errors->has('tax_exempt') ? ' has-error' : '' }}">
								<label class="col-sm-3 form-control-label">Tax Exempt</label>
								<div class="col-sm-4" id="tax_exempt">	{{Form::checkbox('tax_exempt',1,$tax_exempt,['class'=>'flat-red'])}}
								</div>
							</div>
							<div class="form-group row">
								{{ Form::label('billing_add', 'Billing Address',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-9">
									<div class="row order_option_box">
										{{Form::hidden('billing_address_id',$address['billing']['id'])}}
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_company_name') ? ' has-error' : '' }}">
											{{Form::label('billing_company_name','Company Name',array('class'=>'form-control-label'))}} 
											{{Form::text('billing_company_name',(old('billing_company_name')) ? old('billing_company_name'):$address['billing']['company_name'],['class'=>'form-control','placeholder'=>'Company Name'])}}
											@if ($errors->has('billing_company_name'))
												<span class="help-block">{{ $errors->first('billing_company_name') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_phone_number') ? ' has-error' : '' }}">
											{{Form::label('billing_phone_number','Phone Number',array('class'=>'form-control-label'))}} {{Form::text('billing_phone_number',(old('billing_phone_number')) ?old('billing_phone_number'):$address['billing']['phone_number'],['class'=>'form-control','placeholder'=>'Phone Number'])}}
											@if ($errors->has('billing_phone_number'))
												<span class="help-block">{{ $errors->first('billing_phone_number') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_fname') ? ' has-error' : '' }}">
											{{Form::label('billing_fname','First Name',array('class'=>'form-control-label'))}} 
											{{Form::text('billing_fname',(old('billing_fname')) ? old('billing_fname'):$address['billing']['fname'],['class'=>'form-control','placeholder'=>'First Name'])}}
											@if ($errors->has('billing_fname'))
												<span class="help-block">{{ $errors->first('billing_fname') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_lname') ? ' has-error' : '' }}">
											{{Form::label('billing_lname','Last Name',array('class'=>'form-control-label'))}} {{Form::text('billing_lname',(old('billing_lname')) ?old('billing_lname'):$address['billing']['lname'],['class'=>'form-control','placeholder'=>'Last Name'])}}
											@if ($errors->has('billing_lname'))
												<span class="help-block">{{ $errors->first('billing_lname') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_address1') ? ' has-error' : '' }}">
											{{Form::label('billing_address1','Address Line 1',array('class'=>'form-control-label'))}} {{Form::text('billing_address1',(old('billing_address1')) ? old('billing_address1'):$address['billing']['add1'],['class'=>'form-control','placeholder'=>'Address Line 1'])}}
											@if ($errors->has('billing_address1'))
												<span class="help-block">{{ $errors->first('billing_address1') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_address2') ? ' has-error' : '' }}">
											{{Form::label('billing_address2','Address Line 2',array('class'=>'form-control-label'))}}	{{Form::text('billing_address2',(old('billing_address2')) ? old('billing_address2'):$address['billing']['add2'],['class'=>'form-control add2','placeholder'=>'Address Line 2'])}}
											@if ($errors->has('billing_address2'))
												<span class="help-block">{{ $errors->first('billing_address2') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_zipcode') ? ' has-error' : '' }}">	{{Form::label('billing_zipcode','Zipcode',array('class'=>'form-control-label'))}}
										{{Form::number('billing_zipcode',(old('billing_zipcode')) ? old('billing_zipcode'):$address['billing']['zipcode'],['class'=>'form-control','placeholder'=>'Zipcode'])}}
											@if ($errors->has('billing_zipcode'))
												<span class="help-block">{{ $errors->first('billing_zipcode') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_city') ? ' has-error' : '' }}">									{{Form::label('billing_city','City',array('class'=>'form-control-label'))}}
										{{Form::text('billing_city',(old('billing_city')) ? old('billing_city'):$address['billing']['city'],['class'=>'form-control','placeholder'=>'City'])}}
											@if ($errors->has('billing_city'))
												<span class="help-block">{{ $errors->first('billing_city') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_state') ? ' has-error' : '' }}">	{{Form::label('billing_state','State',array('class'=>'form-control-label'))}} 
										{{Form::select('billing_state',$states,(old('billing_state')) ? old('billing_state'):$address['billing']['state'],['class'=>'form-control','placeholder'=>'Select State'])}}
											@if ($errors->has('billing_state'))
												<span class="help-block">{{ $errors->first('billing_state') }}</span>
											@endif
										</div>
										<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing_country') ? ' has-error' : '' }}">	{{Form::label('billing_country','Country',array('class'=>'form-control-label'))}} 
										{{Form::text('billing_country','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
											@if ($errors->has('billing_country'))
												<span class="help-block">{{ $errors->first('billing_country') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								{{ Form::label('shipping_add', 'Shipping Address',array('class'=>'col-sm-3 form-control-label'))}}
								<div class="col-sm-9 ship_div">
									@php
									$i = 1;
									if(array_key_exists('shipping',$address)){
									foreach($address['shipping'] as $val){
									@endphp
									<div class="row shipping_add_box order_option_box">
										<input type="hidden" name="shipping_address[{{$i}}][id]" value="{{$val['id']}}"/>
										<button class="btn btn-danger pull-right remove-add {{ $i == 1 ? 'hide' : '' }}" type="button"><i class="fa fa-minus"></i></button>
										<div class="clearfix"></div>
										<div class="form-group col-sm-12 col-xs-12">
											<label class="form-control-label">Address Name</label>
											<input class="form-control" name="shipping_address[{{$i}}][address_name]" value="{{$val['address_name']}}" placeholder="Address Name" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Company Name</label>
											<input class="form-control" name="shipping_address[{{$i}}][company_name]" value="{{$val['company_name']}}" placeholder="Company Name" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Phone Number</label>
											<input class="form-control" name="shipping_address[{{$i}}][phone_number]" value="{{$val['phone_number']}}" placeholder="Phone Number"  />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">First Name</label>
											<input class="form-control" name="shipping_address[{{$i}}][fname]" value="{{$val['fname']}}" placeholder="First Name" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Last Name</label>
											<input class="form-control" name="shipping_address[{{$i}}][lname]" value="{{$val['lname']}}" placeholder="Last Name"  />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Address Line 1</label>
											<input class="form-control" name="shipping_address[{{$i}}][add1]" value="{{$val['add1']}}" placeholder="Address Line 1" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Address Line 2</label>
											<input class="form-control" name="shipping_address[{{$i}}][add2]" value="{{$val['add2']}}" placeholder="Address Line 2" />
										</div>
										<div class="form-group col-sm-12 col-xs-12">
											<label class="form-control-label">Ship in care of</label>
											<input class="form-control" name="shipping_address[{{$i}}][ship_in_care]" value="{{$val['ship_in_care']}}" placeholder="Ship in care of" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">	
											<label class="form-control-label">Zipcode</label>
											<input type="number" class="form-control" name="shipping_address[{{$i}}][zipcode]" value="{{$val['zipcode']}}" placeholder="Zipcode" />	
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">City</label>
											<input class="form-control" name="shipping_address[{{$i}}][city]" value="{{$val['city']}}" placeholder="City" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">	
											<label class="form-control-label">State</label>	
											{{Form::select('shipping_address['.$i.'][state]',$states,$val['state'],['class'=>'form-control','placeholder'=>'Select State'])}}
										</div>
										<div class="form-group col-sm-6 col-xs-12">	
											<label class="form-control-label">Country</label>
											<input class="form-control" name="shipping_address[{{$i}}][country]" value="US" placeholder="Country" readonly />	
										</div>
									</div>
									@php
										$i++;
										}
									}
									else{
									@endphp
									<div class="row shipping_add_box order_option_box">
										<button class="btn btn-danger pull-right remove-add hide" type="button"><i class="fa fa-minus"></i></button>
										<div class="clearfix"></div>
										<div class="form-group col-sm-12 col-xs-12">
											<label class="form-control-label">Address Name</label>
											<input class="form-control" name="shipping_address[{{$i}}][address_name]" value="" placeholder="Address Name" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Company Name</label>
											<input class="form-control" name="shipping_address[{{$i}}][company_name]" value="" placeholder="Company Name" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Phone Number</label>
											<input class="form-control" name="shipping_address[{{$i}}][phone_number]" value="" placeholder="Phone Number"  />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">First Name</label>
											<input class="form-control" name="shipping_address[1][fname]" value="" placeholder="First Name"  />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Last Name</label>
											<input class="form-control" name="shipping_address[1][lname]" value="" placeholder="Last Name"  />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Address Line 1</label>
											<input class="form-control" name="shipping_address[1][add1]" value="" placeholder="Address Line 1" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">Address Line 2</label>
											<input class="form-control" name="shipping_address[1][add2]" value="" placeholder="Address Line 2" />
										</div>
										<div class="form-group col-sm-12 col-xs-12">
											<label class="form-control-label">Ship in care of</label>
											<input class="form-control" name="shipping_address[{{$i}}][ship_in_care]" value="" placeholder="Ship in care of" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">	
											<label class="form-control-label">Zipcode</label>
											<input type="number" class="form-control" name="shipping_address[1][zipcode]" value="" placeholder="Zipcode" />	
										</div>
										<div class="form-group col-sm-6 col-xs-12">
											<label class="form-control-label">City</label>
											<input class="form-control" name="shipping_address[1][city]" value="" placeholder="City" />
										</div>
										<div class="form-group col-sm-6 col-xs-12">	
											<label class="form-control-label">State</label>	{{Form::select('shipping_address[1][state]',$states,'',['class'=>'form-control','placeholder'=>'Select State'])}}
										</div>
										<div class="form-group col-sm-6 col-xs-12">	
											<label class="form-control-label">Country</label>
											<input class="form-control" name="shipping_address[1][country]" value="US" placeholder="Country" readonly />	
										</div>
									</div>
									@php
									}
									@endphp
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-6 offset-sm-2">
									<button class="btn btn-success add-more" type="button">Add More</button>
								</div>
							</div>
							<div class="line"></div>
							<div class="form-group row">
								<label class="col-sm-3 form-control-label">&nbsp;</label>
								<div class="col-sm-6 offset-sm-2">
									<button type="submit" class="btn btn-primary">Update</button>
									<a href="{{url('/admin/users/lists')}}" class="btn btn-warning">Back</a>
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
var _key = '{{$i}}';
_key++;
$(document).on('click','.add-more',function(){
	var str = '<div class="row shipping_add_box order_option_box"><button class="btn btn-danger pull-right remove-add" type="button"><i class="fa fa-minus"></i></button><div class="clearfix"></div>';
	str +='<div class="form-group col-sm-12 col-xs-12"><label for="shipping_address['+_key+'][address_name]" class="form-control-label">Address Name</label><input class="form-control" placeholder="Address Name" name="shipping_address['+_key+'][address_name]" type="text" value="" /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][company_name]" class="form-control-label">Company Name</label><input class="form-control" placeholder="Company Name" name="shipping_address['+_key+'][company_name]" type="text" value="" /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][phone_number]" class="form-control-label">Phone Number</label><input class="form-control" placeholder="Phone Number" name="shipping_address['+_key+'][phone_number]" type="text" value="" /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][fname]" class="form-control-label">First Name</label><span class="text-danger">*</span> <input class="form-control" placeholder="First Name" name="shipping_address['+_key+'][fname]" type="text" value="" required /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][lname]" class="form-control-label">Last Name</label><span class="text-danger">*</span> <input class="form-control" placeholder="Last Name" name="shipping_address['+_key+'][lname]" type="text" value="" required /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][add1]" class="form-control-label">Address Line 1</label> <span class="text-danger">*</span><input class="form-control" placeholder="Address Line 1" name="shipping_address['+_key+'][add1]" type="text" value="" required /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][add2]" class="form-control-label">Address Line 2</label><input class="form-control" placeholder="Address Line 2" name="shipping_address['+_key+'][add2]" type="text" value="" /></div><div class="form-group col-sm-12 col-xs-12"><label for="shipping_address['+_key+'][ship_in_care]" class="form-control-label">Ship in care of</label><input class="form-control" placeholder="Ship in care of" name="shipping_address['+_key+'][ship_in_care]" type="text" value="" /></div><div class="form-group col-sm-6 col-xs-12">	<label for="shipping_address['+_key+'][zipcode]" class="form-control-label">Zipcode</label> <span class="text-danger">*</span><input type="number" class="form-control" placeholder="Zipcode" name="shipping_address['+_key+'][zipcode]" type="text" value="" required /></div><div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][city]"  class="form-control-label">City</label> <span class="text-danger">*</span><input class="form-control" placeholder="City" name="shipping_address['+_key+'][city]" type="text" value="" required /></div>';
	
	<?php
	$startstr="<option>Select State</option>";
	foreach($states as $key=>$state)
	{
		$startstr .='<option value="'.$key.'">'.$state.'</option>';
	}		
	?>
	
	str += '<div class="form-group col-sm-6 col-xs-12"><label for="shipping_address['+_key+'][state]" class="form-control-label">State</label><span class="text-danger">*</span><select name="shipping_address['+_key+'][state]" class="form-control" required>{!!$startstr!!}</select></div>';
	
	str += '<div class="form-group col-sm-6 col-xs-12">	<label for="shipping_address['+_key+'][country]"="" class="form-control-label">Country</label><span class="text-danger">*</span><input class="form-control shipping_input" placeholder="Country" name="shipping_address['+_key+'][country]" type="text" value="" required /></div></div>';
	
	$('div.ship_div').append(str);
	_key++;
});

$(document).on('click', '.remove-add', function () {
	$(this).closest('.shipping_add_box').remove();
});
</script>		  
@endsection		  