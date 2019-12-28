@extends('layouts.app')
@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Orders Tracking</h2>
	</div>
</section>
<?php
	//pr($track);die;
?>
<section class="innerpages">
	 <div class="container">
		@include('partials.front.account_nav_bar')
		<div class="space"></div>
		<table class="table table-striped table-bordered">
			<tr>
				<th>Order Id :</th>
				<td>{{$order_product->order_id}}</td>
			</tr>
			<tr>
				<th>Tracking ID :</th>
				<td>{{$order_product->tracking_id}}</td>
			</tr>
			@if($errorMessage != '')
				<tr>
				<th>Message :</th>
				<td>{{$errorMessage}}</td>
			</tr>
			@endif
		</table>
		<div class="space"></div>
	 	<div class="row">
		 	<div class="col-lg-12">
			 	<table class="table table-striped table-bordered table-hover">
					@if(count($track)>=1)
						<tr>
							<th>Sr.No</th>
							<th>Location</th>
							<th>Date</th>
							<th>Time</th>
							<th>Activity</th>
						</tr>
						@foreach($track as $key=>$val)
							<tr>
								<td class="nowrap">{{$key}}</td>
								<td class="nowrap">
									@php
										if(!empty($val['address'])){
											$str = '';
											if(array_key_exists('City',$val['address']))
												$str .= $val['address']['City'].',';
											if(array_key_exists('StateProvinceCode',$val['address']))
												$str .= $val['address']['StateProvinceCode'].',';
											if(array_key_exists('CountryCode',$val['address']))
												$str .= $val['address']['CountryCode'].',';
											
											echo trim($str,',');
										}
									@endphp
								</td>
								<td class="nowrap">{{$val['date']}}</td>
								<td class="nowrap">{{$val['time']}}</td>
								<td class="nowrap">{{(array_key_exists('description',$val))?$val['description']:''}}</td>
							</tr>
						@endforeach
					@endif
				</table>
		 	</div>
	 	</div>
	 </div>
</section>
@endsection