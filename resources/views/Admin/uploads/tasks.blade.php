@extends('layouts.admin_layout')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>Upload Errors</h1>
</section>

<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body table-responsive">
					<table id="example2" class="table table-bordered table-hover table-striped addedfeature" border="1" style="width:100%;border-collapse:collapse;">
						<thead>
							<tr>
								<th>#</th>
								<th>Order Id</th>
								<th>Status</th>
								<th>Error</th>
								<th>Last Updated</th>
							</tr>
						</thead>
						<tbody>
						@if(count($tasks)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($tasks as $task)
								<tr>
									<td>{{ ++$i }}</td>
									<td scope="row"><a href="/admin/order/edit/7375/{{$task->order_id}}">{{$task->order_id}}</a></td>
									<td scope="row">{{$task->status}}</td>
									<td scope="row">{{$task->error}}</td>
									<td scope="row">{{$task->updated_at}}</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="4"><center><b>There are no upload tasks.</b></center></td>
							</tr>
						@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>  

@endsection		  