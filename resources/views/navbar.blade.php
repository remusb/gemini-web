<header class="main-header">
  <nav class="navbar navbar-static-top">
    <div class="container">
      <div class="navbar-header">
        <a href="/dashboard" class="navbar-brand"><b>Gemini</b> Portal</a>
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
          <i class="fa fa-bars"></i>
        </button>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
        <ul class="nav navbar-nav">
          <li class="{{ \App\Helper::set_active('/') }}"><a href="/">publish </a></li>
          <li class="{{ \App\Helper::set_active('schedule') }}"><a href="/schedule">schedule </a></li>
          <li class="{{ \App\Helper::set_active('profiles') }}"><a href="/profiles">profiles </a></li>
          <li class="{{ \App\Helper::set_active('analytics') }}"><a href="/analytics">analytics </a></li>
        </ul>
      </div><!-- /.navbar-collapse -->
      <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs">{{ Auth::user()->name }}</span>
              </a>
            </li>

          </ul>
        </div><!-- /.navbar-custom-menu -->
    </div><!-- /.container-fluid -->
  </nav>
</header>
