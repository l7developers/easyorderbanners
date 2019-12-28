<div class="top_bar">
	<div class="top_header">
		<div class="row">
			<div class="col-sm-12 col-md-5">
				<p class="left">The Highest Quality Vinyl Banners at the Industry’s <span>Lowest</span> Prices!</p>
			</div>
			<div class="col-sm-12 col-md-7 top_side hidden-xs">
				<p class="right">
					<i class="fa fa-shopping-cart"></i><a href="{{url('how_to_order')}}" class="contact"> How to Order</a> <span>|</span> 
					<i class="fa fa-quote-left"></i><a href="{{url('custom-quotes')}}" class="contact"> Custom Quotes</a><span>|</span> 
					<i class="fa fa-quote-left"></i><a href="{{url('volume-discounts')}}" class="contact">Volume Discounts</a><span>|</span> 
					<!--<i class="fa fa-quote-left"></i><a href="{{url('oversized_banner')}}" class="contact">Oversized Banner form</a><span>|</span>-->
					<i class="fa fa-image"></i> <a href="{{url('templates_for_artwork')}}" class="contact">Art Templates </a> <span>|</span> 
					<i class="fa fa-envelope"></i> <a href="{{url('contactus')}}" class="contact">Contact Us</a><span>|</span> 
					@if(isset(Auth::user()->id))
						<i class="fa fa-user"></i> <a href="{{url('myaccount')}}" class="contact">My Account</a>
					@else
						<i class="fa fa-sign-in-alt"></i> <a href="{{url('login')}}" class="contact">Login</a>
					@endif
				</p>
			</div>
		</div>
	</div>
</div>

<div class="header_logobar">
	<div class="container">
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-7 col-lg-8 header_full1">
				<a href="{{url('')}}" class="logo"><img src="{{url('public/img/front/logo.png')}}" alt="" /></a>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-5 col-lg-4 header_full1 right_div">
				<div class="right_box">
					<p>We <i class="fa fa-heart"></i> Our Customers!</p>
					<div class="call">
						<img src="{{url('public/img/front/phone_icon.png')}}" alt="" /> (800) 920-9527 
						<span>Need Help Picking the Right Product? <span class="givecall" style="padding: 0px"> Give us a Call!</span></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>