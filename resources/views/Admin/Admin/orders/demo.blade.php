@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Order Detail</h1></div>
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
				
			</div>
		</div>
	</div>
</section>	
@endsection		  