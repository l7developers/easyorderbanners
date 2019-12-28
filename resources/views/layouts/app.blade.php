<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{(isset($pageTitle))?$pageTitle:"Custom Banners & Signs | Vinyl & Mesh Banner Design | Easy Order Banners"}} </title>
	@yield('meta')        
	<link rel="icon" href="{{url('public/img/admin/logo.png')}}" type="image/png"/>
    <!-- CSS -->
	@include('partials.front.css')
	
	<!-- JavaScripts -->
	@include('partials.front.javascripts')

	@php
	echo get_setting('header_script');
	@endphp

	<style>
    @media all and (-ms-high-contrast:none)
    {  
    .coustomeselect select {
		width: 100%;
		padding: 5px 5px 5px 8px;
		font-size: 13px;
	}   
     *::-ms-backdrop, .coustomeselect select {
				width: 100%;
				padding: 5px 5px 5px 8px;
				font-size: 13px;
			} /* IE11 */
     }
  </style>
	
</head>
<body>
	<div id="fade" class="black_overlay"></div>
	<!--<img id="loader_img" class="white_content" src="{{url('public/img/loader/Spinner.gif')}}"/>-->
	<img id="loader_img" class="white_content" src="{{url('public/img/loader/ajax-loader.gif')}}"/>
	@include('partials.front.header')
	
	@include('partials.front.nav')

	@include('partials.front.flash')
	
	@yield('content')
	
	@include('partials.front.footer')
</body>
@php
echo get_setting('footer_script');
@endphp
</html>
<script src="{{ asset('public/js/front/custom.js') }}"></script>
