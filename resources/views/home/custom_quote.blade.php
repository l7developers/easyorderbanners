@extends('layouts.app')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@section('content')
<?php //pr($errors);pr(old());die; ?> 
<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
				<div class="col-xs-12">
					<h3>Custom Quotes</h3>
					<p>Not finding what you’re looking for? Easy Order Banners deals with hundreds of products. Please take a moment and tell us a little bit about what you’re looking for and we’ll get back to you on the same business day. Or give us a call at 800-920-9527.</p>
					<div class="row">
						<div class="col-sm-12 col-md-12">	
						<p class="bluetitle_big"><b>Your Information!</b></p>	{{Form::model('custom_request',['url'=>url('custom-quotes'),'role'=>'form','class'=>'contactpage'])}}
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.fname') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[fname]','First Name')}}<span>*</span>	{{Form::text('custom[fname]',old('custom.fname'),['class'=>'form-control','placeholder'=>'Enter First Name'])}}
								@if ($errors->has('custom.fname'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.fname') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.lname') ? ' has-error' : '' }}">
								{{Form::label('custom[lname]','Last Name')}}<span>*</span>	{{Form::text('custom[lname]',old('custom.lname'),['class'=>'form-control','placeholder'=>'Enter Last Name'])}}
								@if ($errors->has('custom.lname'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.lname') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.company') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[company]','Company Name')}}	{{Form::text('custom[company]',old('custom.company'),['class'=>'form-control','placeholder'=>'Enter Company Name'])}}
								@if ($errors->has('custom.company'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.company') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.address') ? ' has-error' : '' }}">
								{{Form::label('custom[address]','Address')}}	{{Form::text('custom[address]',old('custom.address'),['class'=>'form-control','placeholder'=>'Enter Address'])}}
								@if ($errors->has('custom.address'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.address') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.city') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[city]','City')}}	{{Form::text('custom[city]',old('custom.city'),['class'=>'form-control','placeholder'=>'Enter City'])}}
								@if ($errors->has('custom.city'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.city') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.state') ? ' has-error' : '' }}">
								{{Form::label('custom[state]','State')}}	{{Form::text('custom[state]',old('custom.state'),['class'=>'form-control','placeholder'=>'Enter State'])}}
								@if ($errors->has('custom.state'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.state') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.zipcode') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[zipcode]','ZipCode')}}	{{Form::number('custom[zipcode]',old('custom.zipcode'),['class'=>'form-control','placeholder'=>'Enter Zipcode'])}}
								@if ($errors->has('custom.zipcode'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.zipcode') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.country') ? ' has-error' : '' }}">
								{{Form::label('custom[country]','Country')}}	{{Form::text('custom[country]',old('custom.country'),['class'=>'form-control','placeholder'=>'Enter Country'])}}
								@if ($errors->has('custom.country'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.country') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.phone') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[phone]','Phone')}}	{{Form::text('custom[phone]',old('custom.phone'),['class'=>'form-control','placeholder'=>'Enter phone'])}}
								@if ($errors->has('custom.phone'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.phone') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.fax') ? ' has-error' : '' }}">
								{{Form::label('custom[fax]','Fax')}}	{{Form::text('custom[fax]',old('custom.fax'),['class'=>'form-control','placeholder'=>'Enter Fax'])}}
								@if ($errors->has('custom.fax'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.fax') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.email') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[email]','E-mail')}}<span>*</span>	{{Form::text('custom[email]',old('custom.email'),['class'=>'form-control','placeholder'=>'Enter E-mail'])}}
								@if ($errors->has('custom.email'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.email') }}</strong>
									</span>
								@endif
							</div>
							<div class="clearfix"></div>
							
							<p class="bluetitle_big"><b>Project Information!</b></p>
							
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.quantity') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[quantity]','Quantity')}}<span>*</span>	{{Form::text('custom[quantity]',old('custom.quantity'),['class'=>'form-control','placeholder'=>'Enter Quantity'])}}
								@if ($errors->has('custom.quantity'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.quantity') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.material_type') ? ' has-error' : '' }}">
								{{Form::label('custom[material_type]','Material Type')}}<span>*</span>	{{Form::text('custom[material_type]',old('custom.material_type'),['class'=>'form-control','placeholder'=>'Enter Material Type'])}}
								@if ($errors->has('custom.material_type'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.material_type') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.size') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[size]',"Size (w' x h'):")}}<span>*</span>	{{Form::text('custom[size]',old('custom.size'),['class'=>'form-control','placeholder'=>"Enter Size (w' x h')"])}}
								@if ($errors->has('custom.size'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.size') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('custom.due_date') ? ' has-error' : '' }}">
								{{Form::label('custom[due_date]','Project Due Date')}}<span>*</span>	{{Form::text('custom[due_date]',old('custom.due_date'),['class'=>'form-control date-picker','placeholder'=>'Enter Project Due Date'])}}
								@if ($errors->has('custom.due_date'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.due_date') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-xs-12 {{ $errors->has('custom.detail') ? ' has-error' : '' }} no-padding">
								{{Form::label('custom[detail]','Project Detail')}}<span>*</span>	{{Form::textarea('custom[detail]',old('custom.detail'),['class'=>'form-control','placeholder'=>'Enter Project Detail','rows'=>'3'])}}
								@if ($errors->has('custom.detail'))
									<span class="help-block">
										<strong>{{ $errors->first('custom.detail') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-xs-12 no-padding">
								<div class="g-recaptcha" data-sitekey="{{config('constants.google_captch_site_key')}}" data-callback="recaptcha_callback"></div>
							</div>
							<div class="clearfix"></div>
							<button type="submit" class="btn btn-default formsubmit" disabled="true">Send Message</button>
						{{Form::close()}}
						</div>
					</div>
					<div class="space"></div>
				</div>
				<div class="col-xs-12">
					For Oversized Banner query click <a href="{{url('oversized-banner')}}">Here</a>
				</div>
			</div>
			@include('partials.front.side_bar')
		</div>
		<div class="space"></div>
	</div>
</section>
<script type="text/javascript">
	$( ".date-picker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		minDate: 0,
	});
	var recaptcha_callback = function(response) {		
		$('.formsubmit').prop('disabled',false);
	};
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
@endsection
