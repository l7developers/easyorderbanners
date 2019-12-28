@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Give Review</h2>
	</div>
</section>

<section class="innerpages">
	 <div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-right">
				<nav class="my_account_nav">
					<ul>
						<li><a href="{{url('orders')}}">Back</a></li>
					</ul>
				</nav>
			</div>
		</div>
		<!--<div class="space"></div>-->
	 	<div class="row">
		 	<div class="col-sm-6 col-xs-12">	{{Form::model('order-review',['role'=>'form','class'=>'contactpage','id'=>'order_review'])}}
				{{Form::hidden('order_id',$orderId)}}
				<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					{{Form::label('name','Name',array('class'=>''))}} {{Form::text('name','',['class'=>'form-control','placeholder'=>'Name'])}}
					@if ($errors->has('name'))
						<span class="help-block">{{ $errors->first('name') }}</span>
					@endif
				</div>
				<div class="clearfix"></div>
				<div class="form-group{{ $errors->has('designation_company') ? ' has-error' : '' }}">
					{{Form::label('designation_company','Designation/Company',array('class'=>''))}} {{Form::text('designation_company','',['class'=>'form-control','placeholder'=>'Designation/Company'])}}
					@if ($errors->has('designation_company'))
						<span class="help-block">{{ $errors->first('designation_company') }}</span>
					@endif
				</div>
				<div class="clearfix"></div>
				<div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
					{{Form::label('content','Content',array('class'=>''))}} {{Form::textarea('content','',['class'=>'form-control','placeholder'=>'Content'])}}
					@if ($errors->has('content'))
						<span class="help-block">{{ $errors->first('content') }}</span>
					@endif
				</div>
				<button type="submit" class="btn btn-default">Save</button>
			{{Form::close()}}
			</div>
	 	</div>
	 </div>
</section>
@endsection