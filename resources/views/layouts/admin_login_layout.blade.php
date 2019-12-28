<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{(isset($pageTitle))?$pageTitle:"Easy Order Banners"}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
	
	@include('partials.admin.css')
	
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	
	<link rel="icon" href="{{url('public/img/admin/logo.png')}}" type="image/png"/>
	
  </head>
<body class="hold-transition login-page">
	<!-- /////////////////////////////////////////Content -->
	<div class="login-box">
		@if(Session::has('success'))
			<div class="col-lg-12">
				<div class="alert alert-info alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<i class="fa fa-info-circle"></i>  <strong>{!! session('success') !!}</strong> 
				</div>
			</div>
		@endif
		
		@if(Session::has('error'))
			<div class="col-lg-12">
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<i class="fa fa-info-circle"></i>  <strong>{!! session('error') !!}</strong> 
				</div>
			</div>
		@endif
		@yield('content')		
	</div>
</body>
@include('partials.admin.javascripts')
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '10%' // optional
    });
  });
</script>
</html>
