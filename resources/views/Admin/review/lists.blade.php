@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Reviews List</h1>
</section>

<section class="content">
	<div class="row">		
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
								<th>S.No.</th>
								<th>ID#</th>
								<th>User Name</th>
								<th>Product</th>
								<th>Rating</th>
								<th width="25%">Comment</th>
								<th>Status</th>
								<th>Created</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($reviews)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($reviews as $review)
								<tr>
									<td>{{ ++$i }}</td>
									<th scope="row">{{$review->id}}</th>
									<td scope="row">
										@if(isset($review->user))
											{{$review->user->fname.' '.$review->user->lname}}
										@endif
									</td>
									<td scope="row"><a href="{{url($review->product->slug)}}" target="_blank">{{$review->product->name}}</a></td>
									<td scope="row">{{$review->rating}}</td>
									<td scope="row">{!! nl2br($review->comment) !!}</td>
									<td>
										@if($review->status==1)
											<div class="badge badge-primary">Active</div>
										@else
											<div class="badge badge-danger">Waiting</div>
										@endif
									</td>
									<td>{{$review->created_at}}</td>
									<td>								
										@if($review->status==1)
											<a href="{{url('/admin/reviews/edit/'.$review->id)}}" class="btn btn-sm btn-info">Edit</a>	
											<button data-value="{{$review->name}}" data-url="{{url('/admin/reviews/delete/'.$review->id)}}" class="btn btn-sm btn-danger delete">Delete</button>										
										@else
											<a href="{{url('/admin/reviews/action/'.$review->id.'/1')}}" class="btn btn-primary btn-sm">Approve</a>
											<a href="{{url('/admin/reviews/action/'.$review->id.'/2')}}" class="btn btn-primary btn-sm">Reject</a>
											<a href="{{url('/admin/reviews/edit/'.$review->id)}}" class="btn btn-sm btn-info">Edit</a>
										@endif																				
											
										<!--<button data-value="{{$review->name}}" data-url="{{url('/admin/reviews/delete/'.$review->id)}}" class="btn bg-olive btn-sm delete">Delete</button>-->
									</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="7"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				<div class="pull-left">  {{ $reviews->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>  
<script> 
$(document).ready(function(){
	$('.delete').click(function(){
		if(confirm('Are you sure to delete #'+$(this).attr('data-value'))){
			window.location.href = $(this).attr('data-url');
		}
	})
});
</script>
@endsection		  