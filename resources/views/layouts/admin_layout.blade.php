<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{(isset($pageTitle))?$pageTitle:"Easy Order Banners"}} </title>
    <!--<title>Easy Order Banner</title>-->
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
	
	@include('partials.admin.css')
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	
	<link rel="icon" href="{{url('public/img/admin/logo.png')}}" type="image/png"/>
	
	@include('partials.admin.javascripts')
	<script>
		var SITE_NAME='<?php echo config('constants.SITE_NAME');?>';
		var SITE_URL='<?php echo config('constants.SITE_URL');?>';
	</script>
  </head>
<body class="hold-transition skin-blue sidebar-mini">
	<div id="fade" class="black_overlay"></div>
	<img id="loader_img" class="white_content" src="{{url('public/img/loader/Spinner.gif')}}"/>
	<div class="wrapper">
		@include('partials.admin.header')
		@if(\Auth::user()->role_id == 1)
			@include('partials.admin.nav_admin')
		@else
			@include('partials.admin.nav_agent')
		@endif
	
		<!-- /////////////////////////////////////////Content -->
		<div class="content-wrapper">
				
			@if(Session::has('success'))
				<div class="row">
					<div class="col-lg-12">
						<div class="alert alert-info alert-dismissable" data-dismiss="alert" aria-hidden="true">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<i class="fa fa-info-circle"></i>  <strong>{!! session('success') !!}</strong> 
						</div>
					</div>
				</div>
			@endif
			
			@if(Session::has('error'))
				<div class="row">
					<div class="col-lg-12">
						<div class="alert alert-danger alert-dismissable" data-dismiss="alert" aria-hidden="true">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<i class="fa fa-info-circle"></i>  <strong>{!! session('error') !!}</strong> 
						</div>
					</div>
				</div>
			@endif
			
			@yield('content')		
			
		</div>
		
		<div class="control-sidebar-bg"></div>
	</div>
	
</body>
<script>
	$(document).ready(function () {
		$('.sidebar-menu').tree();
		function Select2(){
			var placeholder = "Select Values";
			if($(this).attr('placeholder')){
				placeholder = $(this).attr('placeholder');
			}
			
			$('.select2').select2({
				placeholder: placeholder,
				dropdownCssClass : $(this).closest('div').attr('class'),
				dropdownAutoWidth : true,
				allowClear: true
			});
		}
		
		Select2();
	});
	
	function setPicers(){
		$( "#from_date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'mm-dd-yy',
			onClose: function( selectedDate ) {
				$( "#end_date" ).datepicker( "option", "minDate", selectedDate );
			  }
		});
		$( "#end_date" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'mm-dd-yy',
			onClose: function( selectedDate ) {
				$( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
			  }
		});
		
		$( ".date-picker" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'mm-dd-yy',
		}).attr('readonly', 'true');
	}
	
	setPicers();
	
	//Flat red color scheme for iCheck
	$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
	  checkboxClass: 'icheckbox_flat-green',
	  radioClass   : 'iradio_flat-green'
	});
</script>
</html>
