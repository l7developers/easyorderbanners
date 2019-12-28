@extends('layouts.admin_layout')
@section('content')
<section class="content-header top_deshboard">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Customer Detail</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/users/lists')}}" class="btn btn-success btn-sm" style="float: right;">Back to list</a>
				<a href="{{url('/admin/users/edit/'.$user->id)}}" class="btn btn-sm btn-info" style="float: right;">Edit</a>
			</div>
		</div>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body admin-view">
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Company Name</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->company_name}}</div> <div class="clearfix"></div>
					</div>						
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>First Name</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->fname}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Last Name</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->lname}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Email</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->email}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Phone Number</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->phone_number}}</div> <div class="clearfix"></div>
					</div>					
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Pay by Invoice</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->pay_by_invoice == 1?'yes':'no'}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Status</b></div>
						<div class="col-lg-10 col-xs-8">
							@if($user->status ==0)
								<span class="badge">Deactive</span>
							@else
								<span class="badge">Active</span>
							@endif
						</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Created</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->created_at}}</div> <div class="clearfix"></div>
					</div>
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>Modified</b></div>
						<div class="col-lg-10 col-xs-8">{{$user->updated_at}}</div> <div class="clearfix"></div>
					</div>
					@php
					if(count($user->user_add)>=1){
						foreach($user->user_add as $val){
							if($val->type == 1){ $name = 'Billing Address'; }else{ $name = 'Shipping Address'; }
					@endphp
					<div class="col-lg-12">    
						<div class="col-lg-2 col-xs-4"><b>{{$name}}</b></div>
						<div class="col-lg-10 col-xs-8">
							<div class="table-responsive">
								<table class="table">
									@if($val->type == 2)
									<tr>
										<th>Address Name</th>
										<td>
											{{trim($val->address_name)}}
										</td>
									</tr>
									@endif
									<tr>
										<th>Company Name</th>
										<td>
											{{trim($val->company_name)}}
										</td>
									</tr>
									<tr>
										<th>Phone Number</th>
										<td>
											{{trim($val->phone_number)}}
										</td>
									</tr>
									<tr>
										<th>First Name</th>
										<td>
											{{trim($val->fname)}}
										</td>
									</tr>
									<tr>
										<th>Last Name</th>
										<td>
											{{trim($val->lname)}}
										</td>
									</tr>
									<tr>
										<th>Address</th>
										<td>
											{{trim($val->add1)}}<br/>
											{{$val->add2}}
										</td>
									</tr>
									@if($val->type == 2)
									<tr>
										<th>Ship in care of</th>
										<td>
											{{trim($val->ship_in_care)}}
										</td>
									</tr>
									@endif
									<tr>
										<th>Zipcode</th>
										<td>
											{{trim($val->zipcode)}}
										</td>
									</tr>
									<tr>
										<th>City</th>
										<td>
											{{trim($val->city)}}
										</td>
									</tr>
									<tr>
										<th>State</th>
										<td>
											{{trim($val->state)}}
										</td>
									</tr>
									<tr>
										<th>Country</th>
										<td>
											{{trim($val->country)}}
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					@php
						}
					}
					@endphp
				</div>
			</div>
		</div>
	</div>
</section>	  
@endsection		  