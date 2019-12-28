@extends('layouts.app')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@section('content')
<?php //pr($errors);pr(old());die; ?> 
<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
				<div class="col-xs-12">
					<h3>Oversized Banner form</h3>
					<p>Not finding what you’re looking for? Easy Order Banners deals with hundreds of products. Please take a moment and tell us a little bit about what you’re looking for and we’ll get back to you on the same business day. Or give us a call at 800-920-9527.</p>
					<div class="row">
						<div class="col-sm-12 col-md-12">	
						<p class="bluetitle_big"><b>Your Information!</b></p>
						{{Form::model('oversize_request',['url'=>url('oversized-banner'),'role'=>'form','class'=>'contactpage'])}}
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.fname') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[fname]','First Name')}}<span>*</span>	{{Form::text('oversize[fname]',old('oversize.fname'),['class'=>'form-control','placeholder'=>'Enter First Name'])}}
								@if ($errors->has('oversize.fname'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.fname') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.lname') ? ' has-error' : '' }}">
								{{Form::label('oversize[lname]','Last Name')}}<span>*</span>	{{Form::text('oversize[lname]',old('oversize.lname'),['class'=>'form-control','placeholder'=>'Enter Last Name'])}}
								@if ($errors->has('oversize.lname'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.lname') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.company') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[company]','Company Name')}}	{{Form::text('oversize[company]',old('oversize.company'),['class'=>'form-control','placeholder'=>'Enter Company Name'])}}
								@if ($errors->has('oversize.company'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.company') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.address') ? ' has-error' : '' }}">
								{{Form::label('oversize[address]','Address')}}	{{Form::text('oversize[address]',old('oversize.address'),['class'=>'form-control','placeholder'=>'Enter Address'])}}
								@if ($errors->has('oversize.address'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.address') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.city') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[city]','City')}}	{{Form::text('oversize[city]',old('oversize.city'),['class'=>'form-control','placeholder'=>'Enter City'])}}
								@if ($errors->has('oversize.city'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.city') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.state') ? ' has-error' : '' }}">
								{{Form::label('oversize[state]','State')}}	{{Form::text('oversize[state]',old('oversize.state'),['class'=>'form-control','placeholder'=>'Enter State'])}}
								@if ($errors->has('oversize.state'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.state') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.zipcode') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[zipcode]','ZipCode')}}	{{Form::number('oversize[zipcode]',old('oversize.zipcode'),['class'=>'form-control','placeholder'=>'Enter Zipcode'])}}
								@if ($errors->has('oversize.zipcode'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.zipcode') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.country') ? ' has-error' : '' }}">
								{{Form::label('oversize[country]','Country')}}	{{Form::text('oversize[country]',old('oversize.country'),['class'=>'form-control','placeholder'=>'Enter Country'])}}
								@if ($errors->has('oversize.country'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.country') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.phone') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[phone]','Phone')}}	{{Form::text('oversize[phone]',old('oversize.phone'),['class'=>'form-control','placeholder'=>'Enter phone'])}}
								@if ($errors->has('oversize.phone'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.phone') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.fax') ? ' has-error' : '' }}">
								{{Form::label('oversize[fax]','Fax')}}	{{Form::text('oversize[fax]',old('oversize.fax'),['class'=>'form-control','placeholder'=>'Enter Fax'])}}
								@if ($errors->has('oversize.fax'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.fax') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.email') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[email]','E-mail')}}<span>*</span>	{{Form::text('oversize[email]',old('oversize.email'),['class'=>'form-control','placeholder'=>'Enter E-mail'])}}
								@if ($errors->has('oversize.email'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.email') }}</strong>
									</span>
								@endif
							</div>
							<div class="clearfix"></div>
							
							<p class="bluetitle_big"><b>Project Information!</b></p>
							
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.quantity') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[quantity]','Quantity')}}<span>*</span>	{{Form::text('oversize[quantity]',old('oversize.quantity'),['class'=>'form-control','placeholder'=>'Enter Quantity'])}}
								@if ($errors->has('oversize.quantity'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.quantity') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.material_type') ? ' has-error' : '' }}">
								{{Form::label('oversize[material_type]','Material Type')}}<span>*</span>	{{Form::text('oversize[material_type]',old('oversize.material_type'),['class'=>'form-control','placeholder'=>'Enter Material Type'])}}
								@if ($errors->has('oversize.material_type'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.material_type') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.size') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[size]',"Size (w' x h'):")}}<span>*</span>	{{Form::text('oversize[size]',old('oversize.size'),['class'=>'form-control','placeholder'=>"Enter Size (w' x h')"])}}
								@if ($errors->has('oversize.size'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.size') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('oversize.due_date') ? ' has-error' : '' }}">
								{{Form::label('oversize[due_date]','Project Due Date')}}<span>*</span>	{{Form::text('oversize[due_date]',old('oversize.due_date'),['class'=>'form-control date-picker','placeholder'=>'Enter Project Due Date'])}}
								@if ($errors->has('oversize.due_date'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.due_date') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-xs-12 {{ $errors->has('oversize.detail') ? ' has-error' : '' }} no-padding">
								{{Form::label('oversize[detail]','Project Detail')}}<span>*</span>	{{Form::textarea('oversize[detail]',old('oversize.detail'),['class'=>'form-control','placeholder'=>'Enter Project Detail','rows'=>'3'])}}
								@if ($errors->has('oversize.detail'))
									<span class="help-block">
										<strong>{{ $errors->first('oversize.detail') }}</strong>
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
			</div>
			
			@include('partials.front.side_bar')
		</div>
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
@endsection
