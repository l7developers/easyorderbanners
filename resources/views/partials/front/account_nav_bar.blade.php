@php
$currentAction = \Route::currentRouteAction();		
list($controller, $action) = explode('@', $currentAction);
$controller = preg_replace('/.*\\\/', '', $controller);
//echo $controller .' and ' .$action;
@endphp

<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<nav class="my_account_nav">
			<ul>
				<li class="{{($controller == 'MyAccountController' and  $action == 'myaccount')?'active':''}}"><a href="{{url('myaccount')}}">My Account</a></li>
				<li class="{{($controller == 'MyAccountController' and  $action == 'changePassword')?'active':''}}"><a href="{{url('change-password')}}">Change Password</a></li>
				<li class="{{($controller == 'MyAccountController' and  $action == 'addresses')?'active':''}}"><a href="{{url('addresses')}}">My Addresses</a></li>
				<!--<li class="{{($controller == 'MyAccountController' and  $action == 'cards')?'active':''}}"><a href="{{url('cards')}}">Payment Options</a></li>-->
				<li class="{{($controller == 'MyAccountController' and in_array($action,['myOrders','ViewOrder']))?'active':''}}"><a href="{{url('orders')}}">My Estimates/Orders</a></li>
				<li class="{{($controller == 'MyAccountController' and in_array($action,['myArtWorkFiles']))?'active':''}}"><a href="{{url('my-artwork-files')}}">My Artwork</a></li>
				<li class="pull-right"><a href="{{url('logout')}}" class="contact" style="background:#d43f3a;color:#FFF">Logout</a></li>
			</ul>
		</nav>
	</div>
</div>