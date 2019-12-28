@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Content List</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/staticpages/add')}}" class="btn btn-primary btn-md">Add Content</a>
				</div>
				<div class="panel-body">
					{{Form::model('filter')}}
						<div class="col-lg-4 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('slug','Slug Name',['class'=>'form-control-label'])}}	{{Form::text('slug',\session()->get('pages.slug'),['class'=>'form-control','placeholder'=>'Search by slug name'])}}
							</div>
						</div>
						<div class="col-lg-4 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('title','Title',['class'=>'form-control-label'])}}	{{Form::text('title',\session()->get('pages.title'),['class'=>'form-control','placeholder'=>'Search By Title'])}}
							</div>
						</div>
						@php
							$status = '2';
							if(\session()->has('pages.status')){
								$status = \session()->get('pages.status');
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
									<a href="{{url('/admin/staticpages/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
				<!-- /.box-header -->
				<div class="box-body table-responsive">
					<table class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>S.No.</th>
								<th>#id</th>
								<th>
									<a href="{{url('admin/staticpages/lists/slug/'.$sort)}}" class="sort_link">Slug Name</a>
									@if($field == 'slug')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/staticpages/lists/title/'.$sort)}}" class="sort_link">Title</a>
									@if($field == 'title')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($pages)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($pages as $page) 
							  <tr class="tr_{{$page->id}}">
								<th>{{++$i}}</th>
								<th scope="row">{{$page->id}}</th>
								<td>{{$page->slug}}</td>
								<td>{{$page->title}}</td>
								<td>
									@if($page->status==1)
										<div class="badge badge-primary">Activate</div>
									@else
										<div class="badge badge-danger">Deactivate</div>
									@endif
								</td>
								<td>								
									@if($page->status==1)
										<a href="{{url('/admin/staticpages/action/'.$page->id.'/0')}}"><button type="submit" class="btn btn-sm btn-danger">Deactivate</button></a>
									@else
										<a href="{{url('/admin/staticpages/action/'.$page->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
									@endif
									<a href="{{url('/admin/staticpages/view/'.$page->id)}}" class="btn btn-sm btn-warning">View</a>
									<a href="{{url('/admin/staticpages/edit/'.$page->id)}}" class="btn btn-sm btn-info">Edit</a>
									
									<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$page->id}}" data="{{$page->title}}">Delete</a>
											
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
					<div class="pull-left">  {{ $pages->links() }} </div>
				</div>
			</div>
		</div>
	</div>
</section>	
<script type="text/javascript">
$(document).ready(function(){
	$('.delete').click(function(index,value){
		var id = $(this).attr('data-id');
		var str = $(this).attr('data');
		str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			return letter.toUpperCase();
		});
		if(confirm("You are about to delete "+str+". Are you sure?")){
			$.ajax({
				url:'{{url("admin/actions/delete")}}',
				type:'post',
				dataType:'json',
				data:{'table':'static_pages','id':id},
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