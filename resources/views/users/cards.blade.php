@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>My Cards</h2>
	</div>
</section>

<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-xs-12 billingdetails">
				<h4>Cards</h4>
				<div class="clearfix"></div>
				<div class="row">
				@if(count($data) > 0)
					@foreach($data as $card)
						@php
							$number =  $card->card_number;
							$card_number =  str_pad(substr($number, -4), strlen($number), '*', STR_PAD_LEFT);
							$date = explode('-',$card->expire_date);
							$expire_month = $date[0];
							$expire_year = $date[1];
						@endphp
						<div class="col-md-4 col-sm-6 col-xs-12">
							<div class="add_box">
								<div class="radio col-xs-10">
									<address>
										<b>Cards Number: </b> {{$card_number}}<br>
										<b>Expire Month: </b> {{$months[$expire_month]}}<br>
										<b>Expire Year: </b>{{$expire_year}}<br>
									</address>
								</div>
								<div class="col-xs-2 no-padding">
									<a href="{{url('card/edit/'.$card->id)}}" class="cus_btn cursor-pointer pull-left"><i class="fa fa-pencil-alt"></i></a>
									<a href="{{url('card/delete/'.$card->id)}}" class="cus_btn cursor-pointer pull-right" onclick="if(confirm('Are you sure for delete this.')){return true;}else{return false;}"><i class="fa fa-trash"></i></a>
								</div>
							</div>
						</div>
					@endforeach
				@else
					<div class="col-xs-12">No Payment Method on File</div>
				@endif
				</div>
			</div>
	 	</div>
	 </div>
</section>
@endsection