<header class="main-header">
    <!-- Logo -->
    <a href="{{url('admin/dashboard')}}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>EOB</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><?php echo config('constants.SITE_NAME');?></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{asset('public/img/admin/dami.png')}}" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo \Auth::user()->fname.' '.\Auth::user()->lname;?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{asset('public/img/admin/dami.png')}}" class="img-circle" alt="User Image">

                <p>
					<?php echo \Auth::user()->fname.' '.\Auth::user()->lname;?>
					<small> Member since <?php echo date('F Y',strtotime(\Auth::user()->created_at));?></small>
                </p>
              </li>
			  <!-- Menu Body -->
              <li class="profile">
                <div class="row">
                  <div class="col-xs-7 pull-right text-right">
						<a href="{{ url('/admin/ChangePassword') }}" class="btn btn-primary btn-flat">Change Password</a>
                  </div>
                  <div class="col-xs-5 pull-right text-center">
                    <a href="{{ url('/admin/profile') }}" class="btn btn-default btn-warning">Profile</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer signout_btn">
                <!--<div class="pull-left">
                  <a href="{{ url('/admin/profile') }}" class="btn btn-default btn-flat">Profile</a>
                </div>-->
				  <div class="row">
                <div class="col-xs-6 pull-right text-right">
				
                  <a href="{{ url('/admin/logout') }}" class="btn btn-success btn-flat">Sign out</a>
                </div>
				  </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>