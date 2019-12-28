<?php
$menulist = \App\Menus::with('menu','category','product','page')->where('parent_id',0)->orderBy('weight','ASC')->get();
//pr($menulist->toArray());die;
?>
<div class="wsmenucontainer">
	<div class="overlapblackbg"></div>
	<div class="wsmobileheader">
		<a id="wsnavtoggle" class="animated-arrow" title="Open menu"><div class="mobilemenutxt">MENU</div><span></span></a>
	</div>
	<header class="navigation">
		<div class="container">
			<div class="header-content bigmegamenu clearfix">
				<nav class="wsmenu">
					<ul class="mobile-sub wsmenu-list">
					@foreach($menulist as $menu)
						<li>
							@if($menu->type == 'page')
								<a href="{{url($menu->page->slug)}}" class="" title="{{$menu->name}}">{{$menu->name}}</a>
							@elseif($menu->type == 'category')
								<a href="{{url($menu->category->slug)}}" class="" title="{{$menu->name}}">{{$menu->name}}</a>
							@elseif($menu->type == 'product')
								<a href="{{url($menu->product->slug)}}" class="" title="{{$menu->name}}">{{$menu->name}}</a>
							@else
								<a href="{{url($menu->static)}}" class="" title="Home">{{$menu->name}}</a>
							@endif
							
							@if(count($menu->menu)>=1)
								@if(!empty($menu->image_name))
								<div class="megamenu clearfix" style="background-image: url({{url('public/img/front/welcome_banner.jpg')}});">
									<div class="row">
										<div class="col-md-3">
											<div class="links_data">
											@if($menu->type == 'page')
												<h3><a href="{{url($menu->page->slug)}}">{{ucwords($menu->page->name)}}</a></h3>
											@elseif($menu->type == 'category')
												<h3><a href="{{url($menu->category->slug)}}">{{ucwords($menu->category->name)}}</a></h3>
											@elseif($menu->type == 'product')
												<h3><a href="{{url($menu->product->slug)}}">{{ucwords($menu->product->name)}}</a></h3>
											
											@endif
												<ul>
												@foreach((object)@$menu->menu as $submenu)
													@if($submenu->type == 'page')
														<li><a href="{{url('/'.$submenu->page->slug)}}">{{$submenu->name}}</a></li>
													@elseif($submenu->type == 'category')
														<li><a href="{{url('/'.$submenu->category->slug)}}">{{$submenu->name}}</a></li>
													@elseif($submenu->type == 'product')
														<li><a href="{{url('/'.$submenu->product->slug)}}">{{$submenu->name}}</a></li>
													
													@endif
												@endforeach
												</ul>
											</div>
										</div>
										@if(!empty($menu->image_name))
										<div class="col-md-9">
											<div class="text-center">
												<a href="" class="">
													<img src="{{url('public/uploads/menu/'.$menu->image_name)}}" alt="">
												</a>
											</div>
										</div>
										@endif
									</div>
								</div>
								@else
									<ul class="wsmenu-submenu" style="display: none">
										@foreach((object)@$menu->menu as $submenu)
											@if($submenu->type == 'page')
												<li><a href="{{url($submenu->page->slug)}}">{{$submenu->name}}</a></li>
											@elseif($submenu->type == 'category')
												<li><a href="{{url($submenu->category->slug)}}">{{$submenu->name}}</a></li>
											@elseif($submenu->type == 'product')
												<li><a href="{{url($submenu->product->slug)}}">{{$submenu->name}}</a></li>
											
											@endif
										@endforeach
									</ul>
								@endif
							@endif
						</li>
						@endforeach
						<li class="visible-xs visible-sm"><a href="{{url('cart')}}" class="" title="My Cart">My Cart</a></li>						
						@if(isset(Auth::user()->id))
							<li class="visible-xs visible-sm"><a href="{{url('myaccount')}}" class="" title="My Account">My Account</a></li>
						@else
							<li class="visible-xs visible-sm"><a href="{{url('login')}}" class="" title="Login">Login</a></li>
						@endif

						<li class="last_cart_li"><a href="javascript:void(0)"> <i class="fas fa-shopping-cart"></i><span class="{{(count(session()->get('cart')) > 0)?'':'hide'}}" id="cart_count">{{(count(session()->get('cart')) > 0)?count(session()->get('cart')):''}}</span></a>
							<ul class="wsmenu-submenu cart_menu">
							<?php
							$session_total = 0;
							if(session()->has('cart') and count(session()->get('cart')) > 0){
								foreach(session()->get('cart') as $k1=>$product){
									$session_total += $product['total'];
									$product_detail = \App\Products::where('id',$product['product_id'])->with('product_image')->first();
									
									$img_url = url('/public/img/front/img.jpg');
									if(@getimagesize(url('public/uploads/product/'.$product_detail->image))){
										$img_url = url('/public/uploads/product/'.$product_detail->image);
									}
							?>
								<li>
									<div class="product">
										<a href="javascript:void(0)" class="remove_product" data="{{$k1}}"><i class="fas fa-times"></i></a>
										<span class="img">
											<img src="{{$img_url}}" alt="">
										</span>
										<span class="right">
											<span class="title">{{$product_detail->name}}</span><br/>
											<span class="cart_product_{{$k1}}">Qty : {{$product['quantity']}}</span>
											<div class="clearfix"></div>
											<span class="cart_product_amount_{{$k1}}">{{'$'.priceFormat($product['total'])}}</span>
										</span>
									</div>
									<div class="clearfix"></div>
								</li>
							<?php
								}
								$session_total = priceFormat($session_total);
							?>
								<li class="boot_opction">
									<div class="top_text"><b>Subtotal:<span class="cart_total">${{$session_total}}</span></b></div>
									<div class="bot_btns">
										<ul>
											<li><a href="{{url('cart')}}" class="btn parpal_btn">View Cart</a></li>
											<li><a href="{{url('cart')}}" class="btn blue_btn">Checkout</a></li>
										</ul>
									</div>
								</li>
							<?php
							}else{
							?>
								<li>
									<div class="product">
										<span>Cart is empty</span>
									</div>
								</li>
							<?php }	?>
							</ul>
						</li>
					</ul>
				</nav>
			</div>
		</div>
	</header>
</div>
<script>
$(document).ready(function() {
	$(document).on('click','.remove_product',function(e){
		if(confirm("Are you sure to delete this product from cart.")){
			var key = $(this).attr('data');
			$.ajax({
				url:'{{url("cart/delete")}}',
				type:'post',
				data:{'key':key},
				dataType:'json',
				success:function(data){
					if(data.status == 'success'){
						$('.cart_menu').html(data.res);
						if(data.session_count > 0){
							$('#cart_count').html(data.session_count).removeClass('hide');
						}else{
							$('#cart_count').html('').addClass('hide');
						}
					}
				}
			});
		}
	});
});
</script>