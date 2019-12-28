<?php
$currentAction = \Route::currentRouteAction();		
list($controller, $action) = explode('@', $currentAction);
$controller = preg_replace('/.*\\\/', '', $controller);
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
			if($controller == 'OrdersController' and in_array($action,array('lists','add','edit','view','archived','estimates')))
				$class = 'active menu-open';
			?>
			<li class="treeview {{$class}}">
				<a href="#">
					<i class="fa fa-first-order"></i>
					<span>My Orders</span>
					<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
				</a>
				<ul class="treeview-menu">
					<li class="<?php if($controller=='OrdersController' && $action=='estimates'){ echo 'active';}?>">
						<a href="{{ url('admin/order/estimates') }}"><i class="fa fa-calculator" ></i> Estimates</a>
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
			/* $class="";
			if($controller == 'HomeController' and in_array($action,array('chat_message')))
				$class = 'active menu-open';
			?>
			<li class="{{$class}}">
				<a href="{{url('admin/chat')}}"><i class="fa fa-comments"></i> <span>Messages</span></a>
			</li>
			<?php */
			$class="";
			if($controller == 'HomeController' and in_array($action,array('agent_events')))
				$class = 'active menu-open';
			?>
			<li class="{{$class}}">
				<a href="{{url('admin/events')}}"><i class="fa fa-calendar"></i> <span>Events</span></a>
			</li>
			
		</ul>
	</section>
</aside>