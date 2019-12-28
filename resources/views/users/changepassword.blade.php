@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Change Password</h2>
	</div>
</section>
<?php //pr(old());die; ?>
<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-sm-6 col-xs-12">	{{Form::model('change_password',['role'=>'form','class'=>'contactpage'])}}
				<div class="form-group{{ $errors->has('OldPassword') ? ' has-error' : '' }}">
					{{Form::label('OldPassword','Old Password',array('class'=>''))}} {{Form::password('OldPassword',['class'=>'form-control','placeholder'=>'Old Password','value'=>''])}}
					@if ($errors->has('OldPassword'))
						<span class="help-block">{{ $errors->first('OldPassword') }}</span>
					@endif
				</div>
				<div class="form-group{{ $errors->has('NewPassword') ? ' has-error' : '' }}">
					{{Form::label('NewPassword','New Password',array('class'=>'form-control-label'))}} {{Form::password('NewPassword',['class'=>'form-control','placeholder'=>'New Password'])}}
					@if ($errors->has('NewPassword'))
						<span class="help-block">{{ $errors->first('NewPassword') }}</span>
					@endif
				</div>
				<div class="form-group{{ $errors->has('NewPassword_confirmation') ? ' has-error' : '' }}">
					{{Form::label('NewPassword_confirmation','New Password Confirm',array('class'=>'form-control-label'))}} {{Form::password('NewPassword_confirmation',['class'=>'form-control','placeholder'=>'New Password Confirm'])}}
					@if ($errors->has('NewPassword_confirmation'))
						<span class="help-block">{{ $errors->first('NewPassword_confirmation') }}</span>
					@endif
				</div>
				<button type="submit" class="btn btn-default">Update</button>
			{{Form::close()}}
			</div>
	 	</div>
	 </div>
</section>
@endsection