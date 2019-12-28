@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>My Account</h2>
	</div>
</section>

<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-xs-12">	{{Form::model('my_acccount',['role'=>'form','class'=>'contactpage'])}}
				<div class="row">
					<div class="form-group col-xs-12 col-sm-6{{ $errors->has('fname') ? ' has-error' : '' }}">
						{{Form::label('fname','First Name',array('class'=>''))}} {{Form::text('fname',(array_key_exists('fname',old()))?old('fname'):$user->fname,['class'=>'form-control','placeholder'=>'First Name'])}}
						@if ($errors->has('fname'))
							<span class="help-block">{{ $errors->first('fname') }}</span>
						@endif
					</div>
					<div class="form-group col-xs-12 col-sm-6{{ $errors->has('lname') ? ' has-error' : '' }}">
						{{Form::label('lname','Last Name',array('class'=>''))}} {{Form::text('lname',(array_key_exists('lname',old()))?old('lname'):$user->lname,['class'=>'form-control','placeholder'=>'Last Name'])}}
						@if ($errors->has('lname'))
							<span class="help-block">{{ $errors->first('lname') }}</span>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-12 col-sm-6{{ $errors->has('email') ? ' has-error' : '' }}">
						{{Form::label('email','Email',array('class'=>''))}} {{Form::text('email',(array_key_exists('email',old()))?old('email'):$user->email,['class'=>'form-control','placeholder'=>'Email'])}}
						@if ($errors->has('email'))
							<span class="help-block">{{ $errors->first('email') }}</span>
						@endif
					</div>
					<div class="form-group col-xs-12 col-sm-6{{ $errors->has('phone_number') ? ' has-error' : '' }}">
						{{Form::label('phone_number','Phone Number',array('class'=>''))}} {{Form::text('phone_number',(array_key_exists('phone_number',old()))?old('phone_number'):$user->phone_number,['class'=>'form-control','placeholder'=>'Phone Number'])}}
						@if ($errors->has('phone_number'))
							<span class="help-block">{{ $errors->first('phone_number') }}</span>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="form-group col-xs-12 col-sm-6{{ $errors->has('company_name') ? ' has-error' : '' }}">
						{{Form::label('company_name','Company Name',array('class'=>''))}} {{Form::text('company_name',(array_key_exists('company_name',old()))?old('company_name'):$user->company_name,['class'=>'form-control','placeholder'=>'Company Name'])}}
						@if ($errors->has('company_name'))
							<span class="help-block">{{ $errors->first('company_name') }}</span>
						@endif
					</div>
					<div class="clearfix"></div>
					<!--<div class="form-group col-xs-12 col-sm-6{{ $errors->has('pay_by_invoice') ? ' has-error' : '' }}">
						<label class="form-control-label">	{{Form::checkbox('pay_by_invoice',1,((array_key_exists('pay_by_invoice',old()) and old('pay_by_invoice') == 1) or  ($user->pay_by_invoice == 1))?true:false,['class'=>'flat-red'])}}
						Pay by Invoice</label>
						@if ($errors->has('pay_by_invoice'))
							<span class="help-block">{{ $errors->first('pay_by_invoice') }}</span>
						@endif
					</div>-->
				</div>
				<div class="row">
					<div class="col-xs-12">
						<button type="submit" class="btn btn-primary">Update</button>
					</div>
				</div>
			{{Form::close()}}
		 	</div>
	 	</div>
	 </div>
</section>
@endsection