<?php
$currentAction = \Route::currentRouteAction();		
list($controller, $action) = explode('@', $currentAction);
$controller = preg_replace('/.*\\\/', '', $controller);
//echo $action;die;
?>
<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
			<div class="pull-left image">
				<img src="{{asset('public/img/admin/dami.png')}}" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p><?php echo \Auth::user()->fname.' '.\Auth::user()->lname;?></p>
				<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>
		<!--<form action="#" method="get" class="sidebar-form">
			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="Search...">
				<span class="input-group-btn">
					<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
			  </span>
			</div>
		</form>-->
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header">Section 1</li>
			<?php
			$class="";
			if($controller == 'HomeController' and $action=='index')
				$class = 'active';
			?>
			<li class="{{$class}}">
				<a href="{{url('admin/dashboard')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
			</li>
			<?php
			$class="";
			if(in_array($controller,array('OrdersController','OrderPOController')) and in_array($action,array('lists','add','edit','view','archived','estimates','orderMail','po')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-first-order"></i>
					<span>Order Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='OrdersController' && $action=='estimates'){ echo 'active';}?>">
						<a href="{{ url('admin/order/estimates') }}" ><i class="fa fa-calculator" ></i> Estimates</a>
					</li>
					<li class="<?php if($controller=='OrdersController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/order/lists') }}"><i class="fa fa-list"></i>Orders</a>
					</li>					
					<li class="<?php if($controller=='OrdersController' && $action=='archived'){ echo 'active';}?>">
						<a href="{{ url('admin/order/archived') }}"><i class="fa fa-file-archive-o"></i> Archived</a>
					</li>
					<li class="<?php if($controller=='OrdersController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/order/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'UserController' and in_array($action,array('lists','add','view','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-user"></i>
					<span>Customers Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='UserController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/users/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='UserController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/users/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'VendorController' and in_array($action,array('lists','add','view','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-asterisk"></i>
					<span>Vendors Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='VendorController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/vendors/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='VendorController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/vendors/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'AgentController' and in_array($action,array('lists','add','view','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-address-book"></i>
					<span>Agents Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='AgentController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/agents/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='AgentController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/agents/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'DesignerController' and in_array($action,array('lists','add','view','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-paint-brush"></i>
					<span>Designers Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='DesignerController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/designers/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='DesignerController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/designers/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'EmailsController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-envelope-o"></i>
					<span>Emails Managers</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='EmailsController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/emails/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<!--<li class="<?php if($controller=='EmailsController' && $action=='add'){ echo 'active';}?>"><a href="{{ url('admin/emails/add') }}"><i class="fa fa-plus-square-o"></i> Add</a></li>-->
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'DiscountController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-asterisk"></i>
					<span>Qty. Discounts Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='DiscountController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/discount/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='DiscountController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/discount/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'CouponController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-gift"></i>
					<span>Discount Coupon Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='CouponController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/coupon/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='CouponController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/coupon/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'ReportsController' and in_array($action,array('Orders','Sales','Customers','poExports')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-area-chart"></i>
					<span>Reports Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='ReportsController' && $action=='Orders'){ echo 'active';}?>">
						<a href="{{ url('admin/reports/orders') }}"><i class="fa fa-bar-chart"></i> Orders</a>
					</li>
					<li class="<?php if($controller=='ReportsController' && $action=='Sales'){ echo 'active';}?>">
						<a href="{{ url('admin/reports/sales') }}"><i class="fa fa-line-chart"></i>Sales</a>
					</li>
					<li class="<?php if($controller=='ReportsController' && $action=='Customers'){ echo 'active';}?>">
						<a href="{{ url('admin/reports/customers') }}"><i class="fa fa-users"></i>Customers</a>
					</li>
					<li class="<?php if($controller=='ReportsController' && $action=='poExports'){ echo 'active';}?>">
						<a href="{{ url('admin/reports/po-exports') }}"><i class="fa fa-users"></i>PO Export</a>
					</li>
				</ul>
			</li>
			<li class="header">Section 2</li>
			<?php
			$class="";
			if($controller == 'CategoriesController' and in_array($action,array('lists','add','edit','addmenu','editmenu','listsmenu')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-cubes"></i>
					<span>Category Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='CategoriesController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/category/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='CategoriesController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/category/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
					<?php /*<li class="<?php if($controller=='CategoriesController' && $action=='listsmenu'){ echo 'active';}?>">
						<a href="{{ url('admin/category/menucategory/lists') }}"><i class="fa fa-list"></i>Menu List</a>
					</li>
					<li class="<?php if($controller=='CategoriesController' && $action=='addmenu'){ echo 'active';}?>">
						<a href="{{ url('admin/category/menucategory/add') }}"><i class="fa fa-plus-square-o"></i> Menu Add</a>
					</li>*/ ?>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'ProductsController' and in_array($action,array('lists','add','edit','view','custom_lists','custom_add','custom_edit','custom_view')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-product-hunt"></i>
					<span>Product Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='ProductsController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/products/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='ProductsController' && $action=='add'){ echo 'active';}?>">
						<a href="{{ url('admin/products/add') }}"><i class="fa fa-plus-square-o"></i> Add</a>
					</li>
					<li class="<?php if($controller=='ProductsController' && $action=='custom_lists'){ echo 'active';}?>">
						<a href="{{ url('admin/products/custom/option/lists') }}"><i class="fa fa-list"></i>Custom option List</a>
					</li>
					<li class="<?php if($controller=='ProductsController' && $action=='custom_add'){ echo 'active';}?>">
						<a href="{{ url('admin/products/custom/option/add') }}"><i class="fa fa-plus-square-o"></i>Custom Option  Add</a>
					</li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'MenusController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-bars"></i>
					<span>Menus Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='MenusController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/menu/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='MenusController' && $action=='add'){ echo 'active';}?>"><a href="{{ url('admin/menu/add') }}"><i class="fa fa-plus-square-o"></i> Add</a></li>
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'StaticpagesController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-file-text"></i>
					<span>Static Pages</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='StaticpagesController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/staticpages/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<!--<li class="<?php if($controller=='StaticpagesController' && $action=='add'){ echo 'active';}?>"><a href="{{ url('admin/staticpages/add') }}"><i class="fa fa-plus-square-o"></i> Add</a></li>-->
				</ul>
			</li>
			<?php
			$class="";
			if($controller == 'SlidersController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<!--<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-sliders"></i>
					<span>Slider Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='SlidersController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/slider/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='SlidersController' && $action=='add'){ echo 'active';}?>"><a href="{{ url('admin/slider/add') }}"><i class="fa fa-plus-square-o"></i> Add</a></li>
				</ul>
			</li>-->
			<?php
			$class="";
			if($controller == 'TestimonialsController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-quote-left"></i>
					<span>Testimonial Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='TestimonialsController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/testimonial/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>
					<li class="<?php if($controller=='TestimonialsController' && $action=='add'){ echo 'active';}?>"><a href="{{ url('admin/testimonial/add') }}"><i class="fa fa-plus-square-o"></i> Add</a></li>
				</ul>
			</li>

			<?php
			$class="";
			if($controller == 'ReviewsController' and in_array($action,array('lists','add','edit')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-quote-left"></i>
					<span>Reviews Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='TestimonialsController' && $action=='lists'){ echo 'active';}?>">
						<a href="{{ url('admin/reviews/lists') }}"><i class="fa fa-list"></i> List</a>
					</li>					
				</ul>
			</li>

			<?php
			$class="";
			if(($controller == 'HomePageController' or $controller == 'SlidersController') and in_array($action,array('top_blue','customers_logos_add','customers_logos_list','customers_logos_edit','images','carousel1','carousel2','carousel3','carousel4','lists','edit','add','view')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-home"></i>
					<span>Home Page Manager</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='SlidersController' and in_array($action,array('lists','edit','add','view'))){ echo 'active';}?>">
						<a href="{{ url('admin/slider/lists') }}"><i class="fa fa-sliders"></i> Slider</a>
					</li>
					<li class="<?php if($controller=='HomePageController' && in_array($action,array('top_blue'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/top-blue') }}"><i class="fa fa-list"></i>Top Blue Section</a>
					</li>
					<li class="<?php if($controller=='HomePageController' and in_array($action,array('images'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/images') }}">
							<i class="fa fa-file-image-o" aria-hidden="true"></i>
							<span>Top Images</span>
							<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
						</a>
					</li>
					<li class="<?php if($controller=='HomePageController' and in_array($action,array('carousel1'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/carousel1') }}">
							<i class="fa fa-braille"></i>
							<span>Product Carousel-1</span>
							<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
						</a>
					</li>
					<li class="<?php if($controller=='HomePageController' and in_array($action,array('carousel2'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/carousel2') }}">
							<i class="fa fa-braille"></i>
							<span>Product Carousel-2</span>
							<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
						</a>
					</li>
					<li class="<?php if($controller=='HomePageController' and in_array($action,array('carousel3'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/carousel3') }}">
							<i class="fa fa-braille"></i>
							<span>Product Carousel-3</span>
							<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
						</a>
					</li>
					<li class="<?php if($controller=='HomePageController' and in_array($action,array('carousel4'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/carousel4') }}">
							<i class="fa fa-braille"></i>
							<span>Product Carousel-4</span>
							<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
						</a>
					</li>
					<li class="<?php if($controller=='HomePageController' and in_array($action,array('customers_logos_list','customers_logos_add','customers-logo-edit'))){ echo 'active';}?>">
						<a href="{{ url('admin/home/customers-logo-list') }}">
							<i class="fa fa-user"></i>
							<span>Customers Logo</span>
							<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
						</a>
					</li>
				</ul>
			</li>

			<?php
			$class="";
			if($controller == 'SettingsController')
				$class = 'active';
			?>
			<li class="{{$class}}">
				<a href="{{ url('admin/settings') }}"><i class="fa fa-cog"></i> <span>Site Setting</span></a>			
			</li>
			<!--<li class="">
				<a href=""><i class="fa fa-calendar"></i> <span>Calendar</span></a>
			</li>
			<li class="">
				<a href=""><i class="fa fa-comments"></i> <span>Messages</span></a>
			</li>-->
			
		</ul>
	</section>
</aside>