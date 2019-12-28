@extends('layouts.admin_login_layout')
@section('content')
@php
$email = ''; 
$password = ''; 
$remember_me = ''; 
if(!empty($remember)){
	$email = $remember['Username'];
	$password = $remember['Password'];
	$remember_me = 'checked'; 
}
@endphp
<div class="login-logo">
	<a href="{{url('admin')}}">
		<img src="{{URL::to('public/img/admin/logo.png')}}" alt="Logo"/>
	</a>
</div>
<div class="login-box-body">
    <form id="login_form" role="form" method="POST">
		{{ csrf_field() }}
		<div class="form-group has-feedback">
			<label for="username" class="label-custom">Email</label>
			<input class="form-control" id="username" type="text" name="Username" required="" placeholder="Enter Email" value="{{(old('email')) ? old('email') : $email}}">
			<i class="fa fa-envelope form-control-feedback" aria-hidden="true"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="password" class="label-custom">Password</label>
			<input class="form-control" id="password" type="password" name="Password" required="" placeholder="Enter Password" value="{{$password}}">
			<i class="fa fa-lock form-control-feedback" aria-hidden="true"></i>
		</div>
		<div class="row">
			<div class="col-xs-8">
				<div class="checkbox icheck">
					<label>
						<input type="checkbox" name="remember_me" value="1" {{ $remember_me }}> Remember Me
					</label>
				</div>
			</div>
			<div class="col-xs-4">
			  <button type="submit" id="login" class="btn btn-primary btn-block btn-flat">Sign In</button>
			</div>
		</div>
	</form> 
</div>

@endsection		  