@extends('layouts.app')

@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Login Form</h2>
	</div>
</section>
@php
$email = ''; 
$password = ''; 
$remember_me = false; 
if(!empty($remember)){
	$email = $remember['email'];
	$password = $remember['password'];
	$remember_me = true; 
}
@endphp
<section class="innerpages form">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12  col-lg-5">	{{Form::model('login',['url'=>route('login'),'role'=>'form','class'=>'contactpage'])}}
				<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					{{Form::label('email','Email Address')}}<span>*</span>	{{Form::email('email',(old('email')) ? old('email') : $email,['class'=>'form-control','id'=>'email','placeholder'=>'Enter Email'])}}
					@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
					{{Form::label('password','Password')}}<span>*</span>	{{Form::password('password',['class'=>'form-control','id'=>'password','placeholder'=>'Enter Password','value'=>$password])}}
					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group">
					{{Form::checkbox('remember',1,$remember_me)}} Remember me
				</div>
				<button type="submit" class="btn btn-default">Login</button>
				
			{{Form::close()}}
			</div>
			<div class="clearfix"></div><br/>
			<div class="col-sm-12 col-md-12 col-lg-5">	
				<a href="{{url('register')}}" class=""><i class="fa fa-user"></i> Sign Up</a>
				&nbsp;&nbsp;&nbsp;
				<a href="{{url('password/reset')}}" class=""><i class="fa fa-lock"></i> Forgot Password</a>
			</div>
			<div class="space"></div>
		</div>
	</div>
</section>
@endsection
