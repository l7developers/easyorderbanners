@extends('layouts.app')

@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Sign Up Form</h2>
	</div>
</section>

<section class="innerpages form">
	 <div class="container">
	 	<div class="row">
	 		<div class="col-sm-12 col-md-12  col-lg-5">		{{Form::model('register',['url'=>route('register'),'role'=>'form','class'=>'contactpage'])}}
				
				<div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
					{{Form::label('company_name','Company Name')}}	{{Form::text('company_name',old('company_name'),['class'=>'form-control','id'=>'company_name','placeholder'=>'Enter Company Name'])}}
					@if ($errors->has('company_name'))
						<span class="help-block">
							<strong>{{ $errors->first('company_name') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group{{ $errors->has('fname') ? ' has-error' : '' }}">
					{{Form::label('fname','First Name')}}<span>*</span>	{{Form::text('fname',old('fname'),['class'=>'form-control','id'=>'fname','placeholder'=>'Enter First Name'])}}
					@if ($errors->has('fname'))
						<span class="help-block">
							<strong>{{ $errors->first('fname') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group{{ $errors->has('lname') ? ' has-error' : '' }}">
					{{Form::label('lname','Last Name')}}<span>*</span>					{{Form::text('lname',old('lname'),['class'=>'form-control','id'=>'lname','placeholder'=>'Enter Last Name'])}}
					@if ($errors->has('lname'))
						<span class="help-block">
							<strong>{{ $errors->first('lname') }}</strong>
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
				<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
					{{Form::label('password','Password')}}<span>*</span>	{{Form::password('password',['class'=>'form-control','id'=>'password','placeholder'=>'Enter Password'])}}
					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group">
					{{Form::label('password_confirmation','Confirm Password')}}<span>*</span>	{{Form::password('password_confirmation',['class'=>'form-control','id'=>'password_confirmation','placeholder'=>'Enter Confirm Password'])}}
				</div>

				<div class="form-group{{ $errors->has('phone_number') ? ' has-error' : '' }}">
					{{Form::label('phone_number','Phone Number')}}<span>*</span>				{{Form::text('phone_number',old('phone_number'),['class'=>'form-control','id'=>'phone_number','placeholder'=>'Enter Phone Number'])}}
					@if ($errors->has('phone_number'))
						<span class="help-block">
							<strong>{{ $errors->first('phone_number') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-theme">Register</button>
				</div>
			{{Form::close()}}
			</div>
	 	</div>
	 </div>
</section>
@endsection
