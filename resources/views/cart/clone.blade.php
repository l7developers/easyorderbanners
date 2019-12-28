@php
$products = \session()->get('cart');
@endphp
<div class="yourcart">
	<h4>Your Cart</h4>
	{{Form::model('cart_form',['id'=>'cart_form'])}}
	@php
	$session_total = 0;
	if(count($products) > 0 ){
		foreach($products as $key=>$product){
			$session_total += $product['total'];
			$product_detail = \App\Products::where('id',$product['product_id'])->with('Catgory','product_image')->first();
	@endphp
	<div class="yourcartlist">
		<div class="row">
			<div class="col-sm-6 col-md-6 col-lg-4">
				<div class="cart_product">
					<a href="{{url('/'.$product_detail->slug.'?cartkey='.$key)}}" class="edit_btns tooltipAdd" title="Edit Item"><i class="fa fa-edit fa-xs"></i>
					</a>
					<a href="javascript:void(0)" class="delete_btns tooltipAdd" data="{{$key}}" title="Remove Item from Cart"><i class="fa fa-trash fa-xs"></i>
					<i class="fa fa-spinner fa-spin hide delete_spin spinner_{{$key}}" style="font-size:24px"></i></a>
					<h5>Product</h5>
					<div class="row" id="asd">
						<div class="col-xs-6 col-sm-4">
							<div class="product_item">	
							@php
							$img_url = url('/public/img/front/img.jpg');
							if(@getimagesize(url('public/uploads/product/'.$product_detail->image))){
								$img_url = url('/public/uploads/product/'.$product_detail->image);
							}
							@endphp
								<a href="{{url('/'.$product_detail->slug)}}"><img src="{{$img_url}}" alt="" /></a>
								<br/>
								<a href="javascript:void(0)" class="project_comment_btn" data="{{@$product['project_name']}}" data-id="{{$key}}" data-type="project_name" id="project_{{$key}}">Project Name</a>
								<a href="javascript:void(0)" class="project_comment_btn" data="{{@$product['comments']}}" data-id="{{$key}}" data-type="comments" id="comment_{{$key}}">comments</a>
							</div>
						</div>
						<div class="col-xs-6 col-sm-8">
							<span> {{$product_detail->Catgory->name}}</span>
							<p> {{$product_detail->name}}</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-6 col-lg-4">
				<div class="cart_options detail_page">
					<h5>Options</h5>
					<ul>
						@php
						if(array_key_exists('variants',$product)){
							foreach($product['variants'] as $key1=>$variant){
								$detail = \App\ProductVariant::where('id',$key1)->with('variantValues')->first();
								//pr($detail);
								foreach($detail->variantValues as $value){
									if($value->id == $variant){
										echo "<li>".ucwords($detail->name).": ".ucwords($value->value)."</li>";
									}
								}
							}
						}
						@endphp
						@foreach((array)$product['options'] as $options)
							@foreach($options as $k1=>$v1)
								@foreach($v1 as $k2=>$v2)
									@if($k2 == 'width' or $k2 == 'height')
										<li>{{ucwords(str_replace('_',' ',$k2)).'(ft)'}}: {{$v2}}</li>
									@else
										@php
											$val = explode('__',$v2);
											echo '<li>'.ucwords(str_replace('_',' ',$k2)).': '.$val[0].'</li>';
										@endphp
									@endif	
								@endforeach						
							@endforeach						
						@endforeach
						<li>{{@$product['object_data']['custom_option']['turnaround_time']['value']}}</li>
					</ul>
				</div>
			</div>
			<div class="col-sm-12 col-md-12 col-lg-4">
				<div class="row">
					<div class="col-xs-3">
						<div class="cart_price">
							<h5>Price</h5>
							<div class="amount">${{priceFormat($product['price'])}}</div>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="cart_price">
							<h5>Quantity</h5>
							<input type="number" class="restict_zero" step="1" name="qty[{{$key}}]" value="{{$product['quantity']}}"/>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="cart_price">
							<h5>Total</h5>
							<div class="total_amount_{{$key}}">${{priceFormat($product['total'])}}</div>
						</div>
					</div>
					<div class="col-xs-3">
						<div class="cart_price">
							<h5>&nbsp;</h5>
							<button type="button" class="btn btn-xs btn-success cloneProduct tooltipAdd" title="Add this itme clone in cart" data-key="{{$key}}">
								<i class="fa fa-clone"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
	@php
	}
	$session_total = priceFormat($session_total);
	} else{
	@endphp
		<div class="yourcartlist">
			<div class="row">
				<div class="col-sm-6 col-md-6 col-lg-4">
					<h3>Cart is empty</h3>
				</div>
			</div>
		</div>
	@php
	}
	@endphp
	{{Form::close()}}
	<div class="space"></div>

	<div class="row">
		<div class="col-sm-6 col-md-6 col-lg-4 comment">
			<div class="form-group coupon">
				@php
					//pr(\session()->get('carttotal'));
					$code = \session()->get('carttotal.discount_code');
				@endphp
				<input type="text" class="form-control" id="coupon_code" placeholder="Enter Coupon" value="{{($code != 0 or $code != '0')?$code:''}}">
				<a href="javascript:void(0)" class="coupon_code_btn">apply coupon</a>
				@if($code != '')
					@if(\session()->get('carttotal.free_shipping'))
						<span class="text-success">Free shipping applied on your order.</span>
					@else
						<span class="text-success">This coupon code apply on your order.</span>
					@endif
				@endif
			</div>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-8">
			<div class="right_text">
				<i class="fa fa-spinner fa-spin hide update_spin" style="font-size:24px"></i>
				<a href="{{url('/shop')}}" class="update">Continue Shopping</a>
				<a href="javascript:void(0)" class="update updateCart">Update</a>
				<a href="{{url('cart/checkout')}}" class="proceed">Proceed to Checkout</a>
			</div>
			<br/>
			<div class="col-sm-12 col-md-8 col-lg-8 pull-right">
				<table class="table totlaamount_table">
					<thead>
						<tr class="title_total">
							<th colspan="2">Cart Totals</th>
						</tr>
					</thead>
					<tbody>
						<tr class="sub_total_tr">
							<td>Subtotal</td>
							<td><span class="cart_total">${{priceFormat(\session()->get('carttotal.gross'))}}</span></td>
						</tr>
						@php
						$class="hide";
						if(\session()->get('carttotal.discount') != 0 or \session()->get('carttotal.discount') != '0'){
							$class = '';
						}								
						@endphp
						<tr class="discount_tr {{$class}}">
							<td>Discount</td>
							<td><span> ${{priceFormat(\session()->get('carttotal.discount'))}}</span> </td>
						</tr>
						@php
						$class="hide";
						if(\session()->get('carttotal.shipping') != 0 or \session()->get('carttotal.shipping') != '0'){
							$class = '';
						}								
						@endphp
						<tr class="{{$class}}">
							<td>Shipping</td>
							<td><span>${{priceFormat(\session()->get('carttotal.shipping'))}}</span> </td>
						</tr>
						<tr>
							<td>Total</td>
							<td><span class="total"> ${{ priceFormat(\session()->get('carttotal.total')) }} </span></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>