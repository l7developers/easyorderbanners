@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Edit {{($data->type == 1)?'Billing':'Shipping'}} Address</h2>
	</div>
</section>

<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-sm-12 col-md-12">	{{Form::model('edit_address',['role'=>'form','class'=>'contactpage'])}}
				<div class="row">					
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('company_name') ? ' has-error' : '' }}">
						{{Form::label('company_name','Company Name',array('class'=>''))}} {{Form::text('company_name',(array_key_exists('company_name',old())) ? old('company_name'):$data->company_name,['class'=>'form-control','placeholder'=>'Company Name'])}}
						@if ($errors->has('company_name'))
							<span class="help-block">{{ $errors->first('company_name') }}</span>
						@endif
					</div>
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('phone_number') ? ' has-error' : '' }}">
						{{Form::label('phone_number','Phone Number',array('class'=>''))}} {{Form::text('phone_number',(array_key_exists('phone_number',old())) ? old('phone_number'):$data->phone_number,['class'=>'form-control','placeholder'=>'Phone Number'])}}
						@if ($errors->has('phone_number'))
							<span class="help-block">{{ $errors->first('phone_number') }}</span>
						@endif
					</div>					
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('fname') ? ' has-error' : '' }}">
						{{Form::label('fname','First Name',array('class'=>''))}} {{Form::text('fname',(array_key_exists('fname',old())) ? old('fname'):$data->fname,['class'=>'form-control','placeholder'=>'First Name'])}}
						@if ($errors->has('fname'))
							<span class="help-block">{{ $errors->first('fname') }}</span>
						@endif
					</div>
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('lname') ? ' has-error' : '' }}">
						{{Form::label('lname','Last Name',array('class'=>''))}} {{Form::text('lname',(array_key_exists('lname',old())) ? old('lname'):$data->lname,['class'=>'form-control','placeholder'=>'Last Name'])}}
						@if ($errors->has('lname'))
							<span class="help-block">{{ $errors->first('lname') }}</span>
						@endif
					</div>
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('add1') ? ' has-error' : '' }}">
						{{Form::label('add1','Address Line 1',array('class'=>''))}} {{Form::text('add1',(array_key_exists('add1',old())) ? old('add1'):$data->add1,['class'=>'form-control','placeholder'=>'Address Line 1'])}}
						@if ($errors->has('add1'))
							<span class="help-block">{{ $errors->first('add1') }}</span>
						@endif
					</div>
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('add2') ? ' has-error' : '' }}">
						{{Form::label('add2','Address Line 2',array('class'=>'form-control-label'))}} {{Form::text('add2',(array_key_exists('add2',old())) ? old('add2'):$data->add2,['class'=>'form-control','placeholder'=>'Address Line 2'])}}
						@if ($errors->has('add2'))
							<span class="help-block">{{ $errors->first('add2') }}</span>
						@endif
					</div>
					@if($data->type == 2)
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('company_name') ? ' has-error' : '' }}">
						{{Form::label('ship_in_care','Ship in care of',array('class'=>''))}} {{Form::text('ship_in_care',(array_key_exists('ship_in_care',old())) ? old('ship_in_care'):$data->ship_in_care,['class'=>'form-control','placeholder'=>'Ship in care of'])}}
						@if ($errors->has('ship_in_care'))
							<span class="help-block">{{ $errors->first('ship_in_care') }}</span>
						@endif
					</div>					
					@endif
				</div>
				<div class="row">
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('zipcode') ? ' has-error' : '' }}">											{{Form::label('zipcode','Zipcode',array('class'=>'form-control-label'))}}{{Form::number('zipcode',(array_key_exists('zipcode',old())) ? old('zipcode'):$data->zipcode,['class'=>'form-control','placeholder'=>'Zipcode'])}}
						@if ($errors->has('zipcode'))
							<span class="help-block">{{ $errors->first('zipcode') }}</span>
						@endif
					</div>
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('city') ? ' has-error' : '' }}">									{{Form::label('city','City',array('class'=>'form-control-label'))}} {{Form::text('city',(array_key_exists('city',old())) ? old('city'):$data->city,['class'=>'form-control','placeholder'=>'City'])}}
						@if ($errors->has('city'))
							<span class="help-block">{{ $errors->first('city') }}</span>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('state') ? ' has-error' : '' }}">	{{Form::label('state','State',array('class'=>'form-control-label'))}} {{Form::select('state',$states,(array_key_exists('state',old())) ? old('state'):$data->state,['class'=>'form-control','placeholder'=>'Select State'])}}
						@if ($errors->has('state'))
							<span class="help-block">{{ $errors->first('state') }}</span>
						@endif
					</div>
					<div class="form-group col-sm-6 col-xs-12{{ $errors->has('country') ? ' has-error' : '' }}">	{{Form::label('country','Country',array('class'=>'form-control-label'))}} {{Form::text('country','US',['class'=>'form-control','placeholder'=>'Country','readonly'])}}
						@if ($errors->has('country'))
							<span class="help-block">{{ $errors->first('country') }}</span>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-12{{ $errors->has('address_name') ? ' has-error' : '' }}">
						{{Form::label('address_name','Name This Address',array('class'=>''))}} {{Form::text('address_name',(array_key_exists('address_name',old())) ? old('address_name'):$data->address_name,['class'=>'form-control','placeholder'=>'Provide this address with a name for quick access'])}}
						@if ($errors->has('address_name'))
							<span class="help-block">{{ $errors->first('address_name') }}</span>
						@endif
					</div>
				</div>
				
				<button type="submit" class="btn btn-default">Update</button>
				<button style="background-color:#d6d6d6;" type="reset" class="btn btn-default">Cancel</button>
				<a href="{{url('addresses/')}}" class="btn btn-success">Back</a>
			{{Form::close()}}
			</div>
	 	</div>
	 </div>
</section>
@endsection