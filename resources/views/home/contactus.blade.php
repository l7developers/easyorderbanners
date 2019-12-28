@extends('layouts.app')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Contact Us</h2>
		<p>We’re Here to Help!</p>
	</div>
</section>

<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
				<h3>Send Us a Note</h3>
				<div class="row">
					<div class="col-sm-12 col-md-12 col-lg-5">
						<p>
							If you have any questions or comments about our services, please feel free to call or email us!
						</p>
						<div class="address">
							<i class="fas fa-building"></i> Easy Order Banners P.O. Box 432, Hatfield, PA 19440
						</div>
						<div class="address"><i class="fas fa-phone"></i> (800) 920-9527</div>
						<!--<div class="address">
							<i class="fas fa-fax"></i> (267) 244-1056</div>-->
						<div class="address">
							<i class="fas fa-envelope"></i> <a href="mailto:info@easyorderbanners.com"> info@easyorderbanners.com</a>
						</div>
						<div class="space"></div>
					</div>
					<div class="col-sm-12 col-md-12  col-lg-5">	{{Form::model('contactus',['url'=>url('contactus'),'role'=>'form','class'=>'contactpage'])}}
						<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
							{{Form::label('name','Your Name')}}<span>*</span>	{{Form::text('name',old('name'),['class'=>'form-control','id'=>'name','placeholder'=>'Enter Name'])}}
							@if ($errors->has('name'))
								<span class="help-block">
									<strong>{{ $errors->first('name') }}</strong>
								</span>
							@endif
						</div>
						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							{{Form::label('email','Email Address')}}<span>*</span>	{{Form::email('email',old('email'),['class'=>'form-control','id'=>'email','placeholder'=>'Enter Email'])}}
							@if ($errors->has('email'))
								<span class="help-block">
									<strong>{{ $errors->first('email') }}</strong>
								</span>
							@endif
						</div>
						<div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
							{{Form::label('subject','Subject')}}<span>*</span>	{{Form::text('subject',old('subject'),['class'=>'form-control','id'=>'subject','placeholder'=>'Enter Subject'])}}
							@if ($errors->has('subject'))
								<span class="help-block">
									<strong>{{ $errors->first('subject') }}</strong>
								</span>
							@endif
						</div>
						<div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
							{{Form::label('message','Your Message')}}<span>*</span>	{{Form::textarea('message',old('message'),['class'=>'form-control','id'=>'message','rows'=>'3','placeholder'=>'Enter Message'])}}
							@if ($errors->has('message'))
								<span class="help-block">
									<strong>{{ $errors->first('message') }}</strong>
								</span>
							@endif
						</div>
						<div class="form-group">
							<div class="g-recaptcha" data-sitekey="{{config('constants.google_captch_site_key')}}" data-callback="recaptcha_callback"></div>
						</div>
						<button type="submit" class="btn btn-default formsubmit" disabled="true">Send Message</button>
					{{Form::close()}}
					</div>
				</div>
				<div class="space"></div>
			</div>
			@include('partials.front.side_bar')
		</div>
	</div>
</section>
<script type="text/javascript">
	var recaptcha_callback = function(response) {		
		$('.formsubmit').prop('disabled',false);
	};
</script>
@endsection
