@extends('layouts.admin_layout')

@section('content')
<link href="{{ asset('public/css/admin/bootstrap-tokenfield.min.css') }}" rel="stylesheet">
<link href="{{ asset('public/css/admin/tokenfield-typeahead.min.css') }}" rel="stylesheet">
<script src="{{asset('public/js/admin/bootstrap-tokenfield.min.js')}}"> </script>
<section class="content-header">
	<div class="row">
		<div class="col-xs-6 full_w"><h1>Edit Product</h1></div>
		<div class="col-xs-6 full_w">
			<div class="top_btns">
				<a href="{{url('admin/products/lists')}}" class="btn btn-warning btn-sm" style="float: right;">Back to list</a>
			</div>
		</div>
	</div>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">				
				<div class="box-body">
					<!-- product basic info form -->
						@include('/Admin/products/partial/basic')
					
					<!-- product price info form -->
						@include('/Admin/products/partial/price')
					
					<!-- product variant info form -->
						@include('/Admin/products/partial/variant')
					
					<!-- product Custom Option info form -->
						@include('/Admin/products/partial/custom_option')
						
                </div>
			</div>
		</div>
	</div>
</section>

@endsection		  