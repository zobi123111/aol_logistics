  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

      <ul class="sidebar-nav" id="sidebar-nav">

          <li class="nav-item">
              <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                  <i class="bi bi-grid"></i>
                  <span>Dashboard</span>
              </a>
          </li><!-- End Dashboard Nav -->

          <li class="nav-heading">Pages</li>

          <li class="nav-item">
              <a class="nav-link {{ Request::is('users') ? 'active' : '' }}" href="{{ url('users') }}">
                  <i class="bi bi-person"></i>
                  <span>Users</span>
              </a>
          </li>
      </ul>

  </aside><!-- End Sidebar-->