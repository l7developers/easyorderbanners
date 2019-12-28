@extends('layouts.app')

@section('content')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
				<div class="col-xs-12">
					<h3>Volume Discounts</h3>
					<p>If you’re a wholesale buyer, distributor, print shop, or reseller looking to do large format vinyl printing or high volume printing, please fill out the form below. Someone from our organization will contact you to discuss ordering and pricing.</p>
					<div class="row">
						<div class="col-sm-12 col-md-12">	{{Form::model('volume_discount',['url'=>url('volume-discounts'),'role'=>'form','class'=>'contactpage'])}}
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('volume.fname') ? ' has-error' : '' }} no-padding">
								{{Form::label('volume[fname]','First Name')}}<span>*</span>	{{Form::text('volume[fname]',old('volume.fname'),['class'=>'form-control','placeholder'=>'Enter First Name'])}}
								@if ($errors->has('volume.fname'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.fname') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('volume.lname') ? ' has-error' : '' }}">
								{{Form::label('volume[lname]','Last Name')}}<span>*</span>	{{Form::text('volume[lname]',old('volume.lname'),['class'=>'form-control','placeholder'=>'Enter Last Name'])}}
								@if ($errors->has('volume.lname'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.lname') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('volume.organization_name') ? ' has-error' : '' }} no-padding">
								{{Form::label('volume[organization_name]','Organization Name')}}	{{Form::text('volume[organization_name]',old('volume.organization_name'),['class'=>'form-control','placeholder'=>'Enter Organization Name'])}}
								@if ($errors->has('volume.organization_name'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.organization_name') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('volume.phone') ? ' has-error' : '' }}">
								{{Form::label('volume[phone]','Phone Number')}}	{{Form::text('volume[phone]',old('volume.phone'),['class'=>'form-control','placeholder'=>'Enter Phone Number'])}}
								@if ($errors->has('volume.phone'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.phone') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('volume.email') ? ' has-error' : '' }} no-padding">
								{{Form::label('volume[email]','Email Address')}}<span>*</span>{{Form::text('volume[email]',old('volume.email'),['class'=>'form-control','placeholder'=>'Enter Email Address'])}}
								@if ($errors->has('volume.email'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.email') }}</strong>
									</span>
								@endif
							</div>
							<div class="clearfix"></div>
							<div class="form-group col-sm-6 col-xs-12 {{ $errors->has('volume.amount_sqft_per_month') ? ' has-error' : '' }} no-padding">
								{{Form::label('volume[amount_sqft_per_month]','Approximate amount of square footage
									per month that you will be purchasing')}}	{{Form::text('volume[amount_sqft_per_month]',old('volume.amount_sqft_per_month'),['class'=>'form-control','placeholder'=>'Enter amount'])}}
								@if ($errors->has('volume.amount_sqft_per_month'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.amount_sqft_per_month') }}</strong>
									</span>
								@endif
							</div>
							<div class="form-group col-xs-12 {{ $errors->has('volume.comment') ? ' has-error' : '' }} no-padding">
								{{Form::label('volume[comment]','Comment')}}	{{Form::textarea('volume[comment]',old('volume.comment'),['class'=>'form-control','placeholder'=>'Enter Comment','rows'=>'3'])}}
								@if ($errors->has('volume.comment'))
									<span class="help-block">
										<strong>{{ $errors->first('volume.comment') }}</strong>
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
