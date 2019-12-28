@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Coupons List</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/coupon/add')}}" class="btn btn-primary btn-md">Add coupon</a>
				</div>
				<div class="panel-body">
					{{Form::model('filter')}}
						<div class="col-lg-4 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('title','Title',['class'=>'form-control-label'])}}	{{Form::text('title',\session()->get('coupon.title'),['class'=>'form-control','placeholder'=>'Search By Title'])}}
							</div>
						</div>
						<div class="col-lg-4 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('code','Code',['class'=>'form-control-label'])}}	{{Form::text('code',\session()->get('coupon.code'),['class'=>'form-control','placeholder'=>'Search By Code'])}}
							</div>
						</div>
						@php
							$status = '2';
							if(\session()->has('coupon.status')){
								$status = \session()->get('coupon.status');
							}
							//echo $status;
						@endphp
						<div class="col-lg-2 col-md-3 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="Filter_Search">Status</label>				{{Form::select('status',[''=>'--All--','1'=>'Active','0'=>'Deactive'],$status,['class'=>'form-control','id'=>'status'])}}
							</div>
						</div>
						<div class="col-lg-2 col-md-3 col-sm-6">
							<div class="form-group">
								<label class="control-label" for="Filter_Search">&nbsp;</label>
								<div class="col-sm-12">
									<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
									<a href="{{url('/admin/coupon/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
								</div>
							</div>
						</div>
					{{Form::close()}}
				</div>
			</div>
		</div>
		@php
			if($sort == 'ASC'){
				$sort = 'DESC';
				$arrow = '<i class="fa fa-arrow-up"></i>';
			}else{
				$sort = 'ASC';
				$arrow = '<i class="fa fa-arrow-down"></i>';
			}
		@endphp
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body table-responsive">
					<table id="example2" class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th class="nowrap">S.No.</th>
								<th class="nowrap">ID#</th>
								<th class="nowrap">
									<a href="{{url('admin/coupon/lists/title/'.$sort)}}" class="sort_link">Title</a>
									@if($field == 'title')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th class="nowrap">
									<a href="{{url('admin/coupon/lists/code/'.$sort)}}" class="sort_link">Code</a>
									@if($field == 'code')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th class="nowrap">
									<a href="{{url('admin/coupon/lists/value/'.$sort)}}" class="sort_link">Code Value</a>
									@if($field == 'type_value')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th class="nowrap">Min. Cart Value</th>
								<th class="nowrap">Max. Discount Value</th>
								<th class="nowrap">Expiry Date</th>
								<th class="nowrap">Email Send</th>
								<th class="nowrap">Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($coupons)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($coupons as $coupon)
								<tr>
									<td class="nowrap">{{ ++$i }}</td>
									<th class="nowrap">{{$coupon->id}}</th>
									<td class="nowrap">{{$coupon->title}}</td>
									<td class="nowrap">{{$coupon->code}}</td>
									<td class="nowrap">
										@if($coupon->type == 'amount')
											Fix Amount : {{'$'.$coupon->type_value}}
										@elseif($coupon->type == 'percent')
											Percentage : {{$coupon->type_value.'%'}}
										@endif
									</td>
									<td class="nowrap">
										@if(!empty($coupon->min_cart))
											{{'$'.priceFormat($coupon->min_cart)}}
										@else
											Not Define
										@endif
									</td>
									<td class="nowrap">
										@if(!empty($coupon->max_discount))
											{{'$'.priceFormat($coupon->max_discount)}}
										@else
											Not Define
										@endif
									</td>
									<td class="nowrap">{{date('d M Y',strtotime($coupon->expire_date))}}</td>
									<td class="nowrap">
										@if($coupon->mail_status == 0)
											<span id="span_{{$coupon->id}}"></span><br/>
											<button class="btn btn-sm btn-warning send_mail" data-id="{{$coupon->id}}">Mail Send To Users</button>
										@else
											<span id="span_{{$coupon->id}}"><b>Mail Sent to Users</b></span><br/>
											<button class="btn btn-sm btn-warning send_mail" data-id="{{$coupon->id}}">Send Mail Again</button>
											
										@endif	
									</td>
									<td class="nowrap">								
										@if($coupon->status==1)
											<a href="{{url('/admin/coupon/action/'.$coupon->id.'/0')}}" class="btn btn-sm btn-danger">Deactivate</a>
										@else
											<a href="{{url('/admin/coupon/action/'.$coupon->id.'/1')}}" class="btn btn-primary btn-sm">Activate</a>
										@endif
											<a href="{{url('/admin/coupon/edit/'.$coupon->id)}}" class="btn btn-sm btn-info">Edit</a>
											<button class="btn btn-sm bg-olive" onclick="var msg = confirm('Are you sure to delete this ?'); if(msg){ location.href = '{{url('/admin/coupon/action/'.$coupon->id.'/delete')}}';}">Delete</button>
									</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="10"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				<div class="pull-left">  {{ $coupons->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>  
<script>
$('.send_mail').click(function(){
	var _this = $(this);
	var id = _this.attr('data-id');
	$('#span_'+id).html('');
	$.ajax({
		url:'{{url("admin/coupon/mail")}}',
		type:'post',
		dataType:'json',
		data:{'id':id},
		beforeSend: function () {
		  $.blockUI();
		},
		complete: function () {
		  $.unblockUI();
		},
		success:function(data){
			if(data.status == 'success'){
				$('#span_'+id).html( "<b>Mail Sent To Users</b>" );
				_this.html('Send Mail Again');
			}
		}
	});
});
</script>
@endsection		  