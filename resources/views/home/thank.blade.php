@php
$pageTitle = 'Registration Successful';
@endphp
@extends('layouts.app')

@section('content')
<section class="pagestitles">
	<div class="container">
		<h2>Registration Successful</h2>
	</div>
</section>

<section class="innerpages">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="clt_review">
					<em> <b><i>Thank you for registering an account on Easy Order Banners. Your Account has been created.<br/><br/><a href="{{url('login')}}" style="font-size: 18px;margin:30px 0px 50px 0px;padding: 5px 20px;background: #1e73be;border: none;" class="btn btn-sm btn-info">Click Here to Login!</a></i></b></em>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
