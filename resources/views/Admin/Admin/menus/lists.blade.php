@extends('layouts.admin_layout')
@section('content')

<link href="{{ asset('public/css/admin/jquery.nestable.css') }}" rel="stylesheet">
<script src="{{ asset('public/js/admin/jquery.nestable.js') }}"></script>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Menu List</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel-heading">
				<a href="{{url('admin/menu/add')}}" class="btn btn-primary btn-md">Add Menu</a>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
				@if(count($menulist) > 0)
					<div class="box-body dd" id="menulist">										
						<ol class="dd-list">
						@foreach($menulist as $menu)							
							<li class='dd-item' data-id='{{$menu->id}}'>
								<div class="dd-handle">{{$menu->name}}</div>	
								<a href="{{url('/admin/menu/edit/'.$menu->id)}}"><i class="fa fa-edit"></i></a>
								<a href="{{url('/admin/menu/delete/'.$menu->id)}}"><i class="fa fa-remove"></i></a>						
								@if(count($menu->menu)>=1)
								<ol class="dd-list">							
									@foreach((object)@$menu->menu as $submenu)
									<li class='dd-item' data-id='{{$submenu->id}}'>
										<div class="dd-handle">{{$submenu->name}}</div>	
										<a href="{{url('/admin/menu/edit/'.$submenu->id)}}"><i class="fa fa-edit"></i></a>
										<a href="{{url('/admin/menu/delete/'.$submenu->id)}}"><i class="fa fa-remove"></i></a>
								
									</li>
									@endforeach								
								</ol>	
								@endif													
							</li>
						@endforeach
						</ol>					
					</div>    
				@else
					<div class="box-body">	
						Here no data found.
					</div>
				@endif
				</div>        
			</div>
		</div>
	</div>
</section>  
<script>
$(document).ready(function(){

	 $('#nestable').nestable({
        group: 1
    })
    .on('change', updateOutput);

$('#menulist').nestable({
        group: 1,
        maxDepth :2
    }).on('change', updateOutput);

function updateOutput()
{
	var data = $('#menulist').nestable('serialize');
	//console.log(data);

	$.ajax({
		url:'{{url("admin/menu/sorting")}}',
		type:'post',
		data:{'data':data},
		dataType:'json',
		success:function(data){
			if(data.status == 'success'){
				
			}
		}
	});
}


});
</script>
@endsection		  