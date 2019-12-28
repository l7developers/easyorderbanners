@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Custom Options List</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<a href="{{url('admin/products/custom/option/add')}}" class="btn btn-primary btn-md">Add Custom Option</a>
				</div>
				<div class="panel-body">
					{{Form::model('filter')}}
						<div class="col-lg-3 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('name','Name',['class'=>'form-control-label'])}}	{{Form::text('name',\session()->get('coupon.name'),['class'=>'form-control','placeholder'=>'Search by name'])}}
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('label','Label',['class'=>'form-control-label'])}}	{{Form::text('label',\session()->get('coupon.label'),['class'=>'form-control','placeholder'=>'Search by label'])}}
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('field_group','Group Field',['class'=>'form-control-label'])}}
								{{Form::select('field_group',['printing'=>'Printing','finishing'=>'Finishing','production'=>'Design Services Options'],\session()->get('coupon.field_group'),['class'=>'form-control','id'=>'field_group','placeholder'=>'--All--'])}}	
							</div>
						</div>
						<div class="col-lg-3 col-md-3 col-sm-6">
							<div class="form-group">
								{{Form::label('price_formate','Price Format',['class'=>'form-control-label'])}}	{{Form::select('price_formate',['area'=>'Area','item'=>'Item','parimeter'=>'Parimeter'],\session()->get('coupon.price_formate'),['class'=>'form-control','id'=>'price_formate','placeholder'=>'--All--'])}}
							</div>
						</div>
						<div class="clearfix"></div>
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
									<a href="{{url('/admin/products/custom/option/lists?rs=1')}}" class="btn btn-sm btn-info">Reset</a>
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
								<th>Sr.No.</th>
								<th>#Id</th>
								<th>
									<a href="{{url('admin/products/custom/option/lists/name/'.$sort)}}" class="sort_link">Name</a>
									@if($field == 'name')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/products/custom/option/lists/label/'.$sort)}}" class="sort_link">Label</a>
									@if($field == 'label')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/products/custom/option/lists/group/'.$sort)}}" class="sort_link">Group Field</a>
									@if($field == 'field_group')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>
									<a href="{{url('admin/products/custom/option/lists/format/'.$sort)}}" class="sort_link">Price Calculation Format</a>
									@if($field == 'price_formate')
										{!!$arrow!!}
									@else
										<i class="fa fa-arrows-v"></i>
									@endif
								</th>
								<th>Type</th>
								<th>status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(count($options) > 0)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($options as $option) 
							<tr class="tr_{{$option->id}}">
								<th>{{++$i}}</th>
								<th scope="row">{{$option['id']}}</th>
								<td>{{$option['name']}}</td>
								<td>{{$option['label']}}</td>
								<td>
									{{ucwords(
										($option['field_group'] == 'production')?'Design Services Options':$option['field_group']
									)}}
								</td>
								<td>{{ucwords($option['price_formate'])}}</td>
								<td>{{$option['option_type'] == 1 ? 'Select box' : 'Input'}}</td>
								<td>
									@if($option->status==1)
										<div class="badge badge-primary">Activate</div>
									@else
										<div class="badge badge-danger">Deactivate</div>
									@endif
								</td>
								<td>								
									@if($option->status==1)
										<a href="{{url('/admin/products/custom/option/action/'.$option->id.'/0')}}"><button type="submit" class="btn btn-sm btn-danger">Deactivate</button></a>
									@else
										<a href="{{url('/admin/products/custom/option/action/'.$option->id.'/1')}}"><button type="submit" class="btn btn-primary btn-sm">Activate</button></a>
									@endif
									<a href="{{url('/admin/products/custom/option/view/'.$option->id)}}" class="btn btn-sm btn-warning">View</a>
									<a href="{{url('/admin/products/custom/option/edit/'.$option->id)}}" class="btn btn-sm btn-info">Edit</a>
									<a href="javascript:void(0)" class="btn btn-sm bg-olive delete" data-id="{{$option->id}}" data="{{$option->name}}">Delete</a>
								</td>
							</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="9"><center><b>No Data Found here</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
					<div class="pull-left">  {{ $options->links() }} </div>
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
				data:{'table':'custom_options','id':id,'related_tables':{'name':'product_options','field_name':'option_id'}},
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