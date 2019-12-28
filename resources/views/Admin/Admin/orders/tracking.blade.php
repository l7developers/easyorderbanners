@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Order Tracking</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/order/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>

<section class="invoice">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="row">
					<div class="col-xs-12">
						<h2 class="page-header">
							<i class="fa fa-globe"></i> {{config('constants.SITE_NAME')}}
						</h2>
					</div>
				</div>
				<div class="row">
					<div id="order_view" class="col-xs-12 table-responsive">
						<table class="table table-striped table-bordered">
							<tr>
								<th>Order Id :</th>
								<td>{{$order_product->order_id}}</td>
							</tr>
							<tr>
								<th>Tracking ID :</th>
								<td>{{$order_product->tracking_id}}</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="row">
					<div id="order_view" class="col-xs-12 table-responsive" style="width:100%;overflow:scroll;">
						<table class="table table-striped">
							<thead>
								<tr>
									<th class="nowrap">Sr.No</th>
									<th class="nowrap">Location</th>
									<th class="nowrap">Date</th>
									<th class="nowrap">Time</th>
									<th class="nowrap">Activity</th>
								</tr>
							</thead>
							<tbody>
							@if(count($track) > 0)
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
									<td class="nowrap">{{$val['description']}}</td>
								</tr>
							@endforeach
							@else
								<tr>
									<td colspan="5">No Data Found</td>
								</tr>
							@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection		  