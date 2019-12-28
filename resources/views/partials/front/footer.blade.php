<section class="our_customers_block" role="OurCustomers">
	<div class="container">
		
		<div class="our_customers_title" style="background-image: url({{url('public/img/front/our_customers_title_bg.png')}});">We <i class="fa fa-heart"></i> Our Customers!</div>

		<div class="row">
			<div class="col-sm-4">
				<a href="tel:18009209527">
					<div class="ourcustomers">
						<i class="fa fa-phone"></i>
						<h4>(800) 920-9527 </h4>
						<p>Not sure which option is best for you? Send us a chat!</p>
					</div>
				</a>	
			</div>

			<div class="col-sm-4">

			</div>
			<div class="col-sm-4">
				<a href="{{url('contactus')}}">
					<div class="ourcustomers">
						<i class="fa fa-envelope"></i>
						<h4>Send Us an Email</h4>
						<p>Not sure which option is best for you? Send us a chat!</p>
					</div>
				</a>
			</div>

		</div>
	</div>


</section>
<footer>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
				<h4>Join Our Mailing List</h4>
				<p>Never miss a deal! Sign up and be the first to know of Easy Order Banners specials!</p>
				<div class="newsletter">
					<input type="text" class="form-control subscriber" placeholder="Enter Email">
					<button type="submit" class="btn btn-primary btn_subscriber">SUBSCRIBE</button>
					<div class="clearfix"></div>
				</div>
				<h4>Payment Types:</h4>
				<br>
				<img src="{{url('public/img/front/payment.png')}}" alt="" />
			</div>
			<div class="col-xs-12 col-sm-8 col-md-6 col-lg-6">
				<h4>Quick Links</h4>
				<div class="row">
				<?php
					$categories = \App\Category::where('status',1)->limit(20)->get();
					$count = count($categories)/2;
					$i = 1 ;
				?>
					<div class="col-sm-6">
						<ul>
						@foreach($categories as $cat)
							<li><i class="fa fa-angle-right"></i> <a href="{{url($cat->slug)}}">{{$cat->name}}</a> </li>
							<?php
							if($i >= $count){
								$i = 1;
								echo "</ul></div><div class='col-sm-6'><ul>";
							}
							$i++;
							?>
						@endforeach
						</ul>
					</div>
				</div>
			</div>
			<div class="col-sm-12 col-lg-3">
				<h4>Helpful Resources</h4>
				<ul>
					<li><i class="fa fa-angle-right"></i> <a href="{{url('templates_for_artwork')}}">Templates For Artwork</a> </li>
					<li><i class="fa fa-angle-right"></i> <a href="{{url('art_file_preparation')}}">Art File Preparation</a> </li>
					<li><i class="fa fa-angle-right"></i> <a href="{{url('tools_&_utilities')}}">Tools & Utilities</a> </li>
					<li><i class="fa fa-angle-right"></i> <a href="{{url('downloadable-files')}}">Downloads</a> </li>
					<li><i class="fa fa-angle-right"></i> <a href="{{url('testimonials')}}">Testimonials</a> </li>
				</ul>
				<h4>File Types</h4>
				<img src="{{url('public/img/front/file_type.png')}}" alt="" />
			</div>
		</div>
	</div>
	<div class="footercopy">
		<div class="container">
			© 2019 Easy Order Banners <span>|</span> <a href="{{url('privacy_policy')}}"> Privacy Policy</a> <span>|</span> <a href="{{url('conditions_of_sale')}}">Conditions of sale</a> <span>|</span> <a href="{{url('returns_policy')}}">Returns Policy</a> <span>|</span> <a href="{{url('login')}}">Login</a>
		</div>
	</div>
</footer>

<script>
$(document).on('click','.btn_subscriber',function(){
	var email = $('.subscriber').val();
	if(email != ''){
		$(this).closest('.newsletter').removeClass('has-error');
		$('.subscriber').next('.help-block').remove();
		$.ajax({
			url:'{{url("subscriber")}}',
			type:'post',
			data:{'email':email},
			dataType:'json',
			success:function(data){
				if(data.status == 'success'){
					$('.subscriber').val('');
					window.location.reload();
				}else{
					$.each( data.res, function( index, value ){
						$('input.subscriber').closest('.newsletter').addClass('has-error');
						$("<span class='help-block'>"+value+"</span>").insertAfter("input.subscriber");
					});
				}
			}
		});
	}else{
		$(this).closest('.newsletter').addClass('has-error');
		$("<span class='help-block'>Please enter this</span>").insertAfter("input.subscriber");
	}
});
</script>