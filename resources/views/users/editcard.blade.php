@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Edit Card Detail</h2>
	</div>
</section>

<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-sm-6 col-xs-12">	{{Form::model('edit_card',['role'=>'form','class'=>'contactpage'])}}
				@php
					$number =  $data->card_number;
					$card_number =  str_pad(substr($number, -4), strlen($number), '*', STR_PAD_LEFT);
					$date = explode('-',$data->expire_date);
					$expire_month = $date[0];
					$expire_year = $date[1];
				@endphp
				<div class="form-group col-xs-12 no-padding">
					{{Form::label('card_number','Card Number')}}	{{Form::text('card_number',(array_key_exists('card_number',old())) ? old('card_number'):$card_number,['class'=>'form-control authorizePayment','id'=>'account_number','placeholder'=>'Card Number','readonly'])}}
				</div>
				<div class="form-group col-md-7 col-xs-12 no-padding main_box {{( $errors->has('expire_month')or $errors->has('expire_year')) ? ' has-error' : '' }}">
					{{Form::label('expiry_date','Expiry Date')}}<div class="clearfix"></div>
					<div class="payment_select col-xs-5">
					{{Form::select('expire_year',[''=>'Select Year']+$years,(array_key_exists('expire_year',old())) ? old('expire_year'):$expire_year,['class'=>'form-control authorizePayment','id'=>'expire_year'])}}
					</div>
					<div class="payment_select col-xs-5">
					{{Form::select('expire_month',[''=>'Select Month']+$months,(array_key_exists('expire_month',old())) ? old('expire_month'):$expire_month,['class'=>'form-control authorizePayment','id'=>'expire_month'])}}
					</div>
					<div class="clearfix"></div>
					@if ($errors->has('expire_month') or $errors->has('expire_year'))
						<span class="help-block">this field is required</span>
					@endif
				</div>
				<div class="clearfix"></div>
				<button type="submit" class="btn btn-default">Update</button>
			{{Form::close()}}
			</div>
	 	</div>
	 </div>
</section>
@endsection