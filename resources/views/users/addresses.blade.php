@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>My Addresses</h2>
	</div>
</section>
<?php
//pr(old('billing'));
?>
<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-xs-12 billingdetails">
				<h4>Billing Addresses</h4>
				<div class="clearfix"></div>
				<div class="row">
				@if(count($data['billing']) > 0)
					@foreach($data['billing'] as $billing)
						<div class="col-md-4 col-sm-6 col-xs-12 add_box">
							<div class="add_box1">
								<div class="radio col-xs-10">
									<address>
										@if(!empty($billing->company_name))
											{{$billing->company_name}}<br/>
										@endif	
										
										@if(!empty($billing->fname))
											{{$billing->fname}}
										@endif
										@if(!empty($billing->lname))
											&nbsp;{{$billing->lname}}
										@endif
										<br/>

										@if(!empty($billing->add1) and !empty($billing->add2))
											{{$billing->add1}}<br/>{{$billing->add2}}<br/>
										@else
											{{$billing->add1}}<br/>
										@endif
										<br/>
										
										{{$billing->city.', '.$billing->state.' '.$billing->zipcode . ' ' . $billing_country}}
									</address>
								</div>
								<div class="col-xs-2 no-padding">
									<a href="{{url('edit/address/'.$billing->id)}}" class="cus_btn cursor-pointer pull-left"><i class="fa fa-pencil-alt"></i></a>
									<a href="{{url('delete/address/'.$billing->id)}}" class="cus_btn cursor-pointer pull-right" onclick="if(confirm('Are you sure for delete this.')){return true;}else{return false;}"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</div>
					@endforeach
				@else
					<div class="col-xs-12">No Addresses</div>
				@endif
					<div class="clearfix"></div>
					<div class="space"></div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<button class="btn btn-success add_billing">Add New Address</button>
					</div>
					<div class="clearfix"></div>
					<div class="space"></div>
					<div class="col-sm-12 col-md-12 add_billing_form" style="display:none;">	{{Form::model('add_address',['role'=>'form','class'=>'contactpage'])}}
						{{Form::hidden('billing[type]','billing')}}
						<div class="row">
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.company_name') ? ' has-error' : '' }}">
								{{Form::label('billing[company_name]','Company Name',array('class'=>''))}} {{Form::text('billing[company_name]',(array_key_exists('billing',old())) ? old()['billing']['company_name']:'',['class'=>'form-control','placeholder'=>'Company Name'])}}
								@if ($errors->has('billing.company_name'))
									<span class="help-block">{{ $errors->first('billing.company_name') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.phone_number') ? ' has-error' : '' }}">
								{{Form::label('billing[phone_number]','Phone Number',array('class'=>''))}} {{Form::text('billing[phone_number]',(array_key_exists('billing',old())) ? old()['billing']['phone_number']:'',['class'=>'form-control','placeholder'=>'Phone Number'])}}
								@if ($errors->has('billing.phone_number'))
									<span class="help-block">{{ $errors->first('billing.phone_number') }}</span>
								@endif
							</div>

							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.fname') ? ' has-error' : '' }}">
								{{Form::label('billing[fname]','First Name',array('class'=>''))}} {{Form::text('billing[fname]',(array_key_exists('billing',old())) ? old()['billing']['fname']:'',['class'=>'form-control','placeholder'=>'First Name'])}}
								@if ($errors->has('billing.fname'))
									<span class="help-block">{{ $errors->first('billing.fname') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.lname') ? ' has-error' : '' }}">
								{{Form::label('billing[lname]','Last Name',array('class'=>''))}} {{Form::text('billing[lname]',(array_key_exists('billing',old())) ? old()['billing']['lname']:'',['class'=>'form-control','placeholder'=>'Last Name'])}}
								@if ($errors->has('billing.lname'))
									<span class="help-block">{{ $errors->first('billing.lname') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.add1') ? ' has-error' : '' }}">
								{{Form::label('billing[add1]','Address Line 1',array('class'=>''))}} {{Form::text('billing[add1]',(array_key_exists('billing',old())) ? old()['billing']['add1']:'',['class'=>'form-control','placeholder'=>'Address Line 1'])}}
								@if ($errors->has('billing.add1'))
									<span class="help-block">{{ $errors->first('billing.add1') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.add2') ? ' has-error' : '' }}">
								{{Form::label('billing[add2]','Address Line 2',array('class'=>'form-control-label'))}} {{Form::text('billing[add2]',(array_key_exists('add2',old())) ? old()['billing']['add2']:'',['class'=>'form-control','placeholder'=>'Address Line 2'])}}
								@if ($errors->has('billing.add2'))
									<span class="help-block">{{ $errors->first('billing.add2') }}</span>
								@endif
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.zipcode') ? ' has-error' : '' }}">											{{Form::label('billing[zipcode]','Zipcode',array('class'=>'form-control-label'))}}{{Form::number('billing[zipcode]',(array_key_exists('zipcode',old())) ? old('zipcode'):'',['class'=>'form-control','placeholder'=>'Zipcode'])}}
								@if ($errors->has('billing.zipcode'))
									<span class="help-block">{{ $errors->first('billing.zipcode') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.city') ? ' has-error' : '' }}">									{{Form::label('billing[city]','City',array('class'=>'form-control-label'))}} {{Form::text('billing[city]',(array_key_exists('city',old())) ? old('city'):'',['class'=>'form-control','placeholder'=>'City'])}}
								@if ($errors->has('billing.city'))
									<span class="help-block">{{ $errors->first('billing.city') }}</span>
								@endif
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.state') ? ' has-error' : '' }}">	{{Form::label('billing[state]','State',array('class'=>'form-control-label'))}} {{Form::select('billing[state]',$states,(array_key_exists('state',old())) ? old('state'):'',['class'=>'form-control','placeholder'=>'State'])}}
								@if ($errors->has('billing.state'))
									<span class="help-block">{{ $errors->first('billing.state') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.country') ? ' has-error' : '' }}">	{{Form::label('billing[country]','Country',array('class'=>'form-control-label'))}} {{Form::text('billing[country]','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
								@if ($errors->has('billing.country'))
									<span class="help-block">{{ $errors->first('billing.country') }}</span>
								@endif
							</div>
						</div>
						<button type="submit" class="btn btn-default">Add</button>
					{{Form::close()}}
					</div>
					<div class="clearfix"></div>
					<div class="space"></div>
				</div>
			</div>
			<div class="col-sm-12 billingdetails">	
				<h4>Shipping Addresses</h4>
				<div class="form-group col-sm-12 shipping_error has-error hide"></div>
				<div class="clearfix"></div>
				<div class="row">
				@if(count($data['shipping']) > 0)
					@foreach($data['shipping'] as $shipping)
						<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 add_box">
							<div class="add_box1">
								<div class="radio col-xs-10">
									<address>		
										@if(!empty($shipping->address_name))
											<b>{{$shipping->address_name}}</b><br/>
										@endif	
										@if(!empty($shipping->company_name))
											{{$shipping->company_name}}<br/>
										@endif	
																	
										{{$shipping->fname.'&nbsp;'.$shipping->lname}}<br/>									
										{{$shipping->add1.', '}}
										@if(!empty($shipping->add2))
											{{$shipping->add2}}
										@endif
										<br/>
										{{$shipping->city.', '.$shipping->state.', '.$shipping->zipcode}}<br>
										{{$shipping->country}}
									</address>
								</div>
								<div class="col-xs-2 no-padding">
									<a href="{{url('edit/address/'.$shipping->id)}}" class="cus_btn cursor-pointer pull-left"><i class="fa fa-pencil-alt"></i></a>
									<a href="{{url('delete/address/'.$shipping->id)}}" class="cus_btn cursor-pointer pull-right" onclick="if(confirm('Are you sure for delete this.')){return true;}else{return false;}"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</div>
					@endforeach
				@else
					<div class="col-xs-12">No Addresses</div>
				@endif
					<div class="clearfix"></div>
					<div class="space"></div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<button class="btn btn-success add_shipping">Add New Address</button>
					</div>
					<div class="clearfix"></div>
					<div class="space"></div>
					<div class="col-sm-12 col-md-12 add_shipping_form" style="display:none;">	{{Form::model('add_address',['role'=>'form','class'=>'contactpage'])}}
						{{Form::hidden('shipping[type]','shipping')}}
						<div class="row">							
							<div class="clearfix"></div>							
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.company_name') ? ' has-error' : '' }}">
								{{Form::label('shipping[company_name]','Company Name',array('class'=>''))}} {{Form::text('shipping[company_name]',(array_key_exists('shipping',old())) ? old()['shipping']['company_name']:'',['class'=>'form-control','placeholder'=>'Company Name'])}}
								@if ($errors->has('shipping.company_name'))
									<span class="help-block">{{ $errors->first('shipping.company_name') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('billing.phone_number') ? ' has-error' : '' }}">
								{{Form::label('shipping[phone_number]','Phone Number',array('class'=>''))}} {{Form::text('shipping[phone_number]',(array_key_exists('shipping',old())) ? old()['shipping']['phone_number']:'',['class'=>'form-control','placeholder'=>'Phone Number'])}}
								@if ($errors->has('shipping.phone_number'))
									<span class="help-block">{{ $errors->first('shipping.phone_number') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.fname') ? ' has-error' : '' }}">
								{{Form::label('shipping[fname]','First Name',array('class'=>''))}} {{Form::text('shipping[fname]',(array_key_exists('shipping',old())) ? old()['shipping']['fname']:'',['class'=>'form-control','placeholder'=>'First Name'])}}
								@if ($errors->has('shipping.fname'))
									<span class="help-block">{{ $errors->first('shipping.fname') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.lname') ? ' has-error' : '' }}">
								{{Form::label('shipping[lname]','Last Name',array('class'=>''))}} {{Form::text('shipping[lname]',(array_key_exists('shipping',old())) ? old()['shipping']['lname']:'',['class'=>'form-control','placeholder'=>'Last Name'])}}
								@if ($errors->has('shipping.lname'))
									<span class="help-block">{{ $errors->first('shipping.lname') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.add1') ? ' has-error' : '' }}">
								{{Form::label('shipping[add1]','Address Line 1',array('class'=>''))}} {{Form::text('shipping[add1]',(array_key_exists('shipping',old())) ? old()['shipping']['add1']:'',['class'=>'form-control','placeholder'=>'Address Line 1'])}}
								@if ($errors->has('shipping.add1'))
									<span class="help-block">{{ $errors->first('shipping.add1') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.add2') ? ' has-error' : '' }}">
								{{Form::label('shipping[add2]','Address Line 2',array('class'=>'form-control-label'))}} {{Form::text('shipping[add2]',(array_key_exists('shipping',old())) ? old()['shipping']['add2']:'',['class'=>'form-control','placeholder'=>'Address Line 2'])}}
								@if ($errors->has('shipping.add2'))
									<span class="help-block">{{ $errors->first('shipping.add2') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.ship_in_care') ? ' has-error' : '' }}">
								{{Form::label('shipping[ship_in_care]','Ship in care of',array('class'=>'form-control-label'))}} {{Form::text('shipping[ship_in_care]',(array_key_exists('shipping',old())) ? old()['shipping']['ship_in_care']:'',['class'=>'form-control','placeholder'=>'Ship in care of'])}}
								@if ($errors->has('shipping.ship_in_care'))
									<span class="help-block">{{ $errors->first('shipping.ship_in_care') }}</span>
								@endif
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.zipcode') ? ' has-error' : '' }}">											{{Form::label('shipping[zipcode]','Zipcode',array('class'=>'form-control-label'))}}{{Form::number('shipping[zipcode]',(array_key_exists('shipping',old())) ? old()['shipping']['zipcode']:'',['class'=>'form-control','placeholder'=>'Zipcode'])}}
								@if ($errors->has('shipping.zipcode'))
									<span class="help-block">{{ $errors->first('shipping.zipcode') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.city') ? ' has-error' : '' }}">									{{Form::label('shipping[city]','City',array('class'=>'form-control-label'))}} {{Form::text('shipping[city]',(array_key_exists('shipping',old())) ? old()['shipping']['city']:'',['class'=>'form-control','placeholder'=>'City'])}}
								@if ($errors->has('shipping.city'))
									<span class="help-block">{{ $errors->first('shipping.city') }}</span>
								@endif
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.state') ? ' has-error' : '' }}">	{{Form::label('shipping[state]','State',array('class'=>'form-control-label'))}} {{Form::select('shipping[state]',$states,(array_key_exists('shipping',old())) ? old()['shipping']['state']:'',['class'=>'form-control','placeholder'=>'State'])}}
								@if ($errors->has('shipping.state'))
									<span class="help-block">{{ $errors->first('shipping.state') }}</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12{{ $errors->has('shipping.country') ? ' has-error' : '' }}">	{{Form::label('shipping[country]','Country',array('class'=>'form-control-label'))}} {{Form::text('shipping[country]','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
								@if ($errors->has('shipping.country'))
									<span class="help-block">{{ $errors->first('shipping.country') }}</span>
								@endif
							</div>
						</div>
						<div class="row">
							<div class="form-group col-xs-12{{ $errors->has('shipping.address_name') ? ' has-error' : '' }}">
								{{Form::label('shipping[address_name]','Name This Address',array('class'=>''))}} {{Form::text('shipping[address_name]',(array_key_exists('shipping',old())) ? old()['shipping']['address_name']:'',['class'=>'form-control','placeholder'=>'Provide this address with a name for quick access'])}}
								@if ($errors->has('shipping.address_name'))
									<span class="help-block">{{ $errors->first('shipping.address_name') }}</span>
								@endif
							</div>
						</div>
						<button type="submit" class="btn btn-default">Add</button>
						<div class="space"></div>
					{{Form::close()}}
					</div>
				</div>
			</div>
	 	</div>
	 </div>
</section>

<script>
<?Php
if(old('billing.type') == 'billing'){
?>
	$(".add_billing_form").slideDown();
<?php
}

if(old('shipping.type') == 'shipping'){
?>
	$(".add_shipping_form").slideDown();
<?php
}
?>

$(".add_billing").click(function(){
	$(".add_billing_form").slideToggle();
});
$(".add_shipping").click(function(){
	$(".add_shipping_form").slideToggle();
});
</script>
@endsection