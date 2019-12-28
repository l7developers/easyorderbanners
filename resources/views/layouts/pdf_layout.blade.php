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
	
	<!-- Bootstrap Core CSS -->
	<link href="{{ asset('public/css/admin/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/admin/AdminLTE.min.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/admin/_all-skins.min.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/admin/blue.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/admin/select2.min.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/admin/developer.css') }}" rel="stylesheet">

	<link rel="icon" href="{{url('public/img/admin/logo.png')}}" type="image/png"/>
	
	@include('partials.admin.javascripts')
	
  </head>
<body class="">
	<div class="wrapper">
		@yield('content')		
		<div class="control-sidebar-bg"></div>
	</div>
	
</body>
</html>
