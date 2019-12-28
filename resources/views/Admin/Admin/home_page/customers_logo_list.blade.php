@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Customer Logo List</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/home/customers-logo-add')}}" class="btn btn-primary btn-md">Add Customer Logo</a>
				</div>
			</div>
		</div>
		
		<div class="col-xs-12">
			<div class="box box-primary">
				<!-- /.box-header -->
				<div class="box-body table-responsive">
					<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>S.No.</th>
								<th>#id</th>
								<th>Title</th>
								<th>Image</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($logos)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($logos as $logo) 
							  <tr class="tr_{{$logo->id}}">
								<th>{{++$i}}</th>
								<th scope="row">{{$logo->id}}</th>
								<td>{{$logo->title}}</td>
								<td><img src="{{URL::to('public/uploads/home/customers/'.$logo->image)}}" title="{{$logo->title}}"/></td>
								<td>
									@if($logo->status==1)
										<div class="badge badge-primary">Activate</div>
									@else
										<div class="badge badge-danger">Deactivate</div>
									@endif
								</td>
								<td>								
									@if($logo->status==1)
										<a href="{{url('/admin/home/logo-action/'.$logo->id.'/0')}}"><button type="submit" class="btn btn-sm btn-danger">Deactivate</button></a>
									@else
										<a href="{{url('/admin/home/logo-action/'.$logo->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
									@endif
									
									<a href="{{url('/admin/home/customers-logo-edit/'.$logo->id)}}" class="btn btn-sm btn-info">Edit</a>
									
									<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$logo->id}}" data="{{$logo->title}}" data-image="public/uploads/home/customers/{{$logo->image}}">Delete</a>
											
								</td>
							  </tr>
							@endforeach 
						@else
							<tr>
								<td colspan="6"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
					<div class="pull-left">  {{ $logos->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>	
<script type="text/javascript">
$(document).ready(function(){
	$('.delete').click(function(index,value){
		var id = $(this).attr('data-id');
		var image = $(this).attr('data-image');
		var str = $(this).attr('data');
		str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			return letter.toUpperCase();
		});
		if(confirm("You are about to delete "+str+". Are you sure?")){
			$.ajax({
				url:'{{url("admin/actions/delete")}}',
				type:'post',
				dataType:'json',
				data:{'table':'customer_logos','id':id,'image_unlink':'true','image':image},
				beforeSend: function () {
				  $.blockUI();
				},
				complete: function () {
				  $.unblockUI();
				},
				success:function(data){
					if(data.status == 'success'){
						$('.tr_'+id).remove();
					}
				}
			});
		}
	});
});
</script>	  
@endsection		  