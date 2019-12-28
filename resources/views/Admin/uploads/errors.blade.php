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
								<th>Error</th>
								<th>Last Updated</th>
							</tr>
						</thead>
						<tbody>
						@if(count($errors)>=1)
							@php 
								if(isset($_GET['page'])){
									$i=($limit*$_GET['page'])-$limit;
								}
								else{
									$i=0;
								}
							@endphp
							@foreach($errors as $error)
								<tr>
									<td>{{ ++$i }}</td>
									<td scope="row"><a href="/admin/order/edit/7375/{{$error->order_id}}">{{$error->order_id}}</a></td>
									<td scope="row">{{$error->error}}</td>
									<td scope="row">{{$error->updated_at}}</td>
								</tr>
							@endforeach 
						@else
							<tr>
								<td colspan="4"><center><b>There are no upload errors. Nice!</b></center></td>
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