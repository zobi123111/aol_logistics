  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    @foreach(getAllowedPages() as $page)
    <!-- {{$page->modules}} -->
        <li class="nav-item">
            <a class="nav-link {{ Request::is($page->route_name) ? 'active' : '' }}" href="{{ url($page->route_name) }}">
            <i class="{{ $page->icon }}"></i> 
                <span>{{ ucfirst($page->name) }}</span>
            </a>
        </li>
    @endforeach
 

@if(Auth::check() && Auth::user()->supplier_id !== null)
<li class="nav-item">
            <a class="nav-link" href="{{ route('supplier_users.index', encode_id(Auth::user()->supplier_id)) }}">
            <i class="{{ $page->icon }}"></i> 
                <span>Supplier Users</span>
            </a>
        </li>
@endif
</ul>
      <!-- <ul class="sidebar-nav" id="sidebar-nav">

      
          <li class="nav-item">
              <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                  <i class="bi bi-grid"></i>
                  <span>Dashboard</span>
              </a>
          </li>


          <li class="nav-item">
              <a class="nav-link {{ Request::is('users') ? 'active' : '' }}" href="{{ url('users') }}">
                  <i class="bi bi-person"></i>
                  <span>Users</span>
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link {{ Request::is('roles') ? 'active' : '' }}" href="{{ url('roles') }}">
                  <i class="bi bi-person"></i>
                  <span>Roles</span>
              </a>
          </li>
      </ul> -->

  </aside><!-- End Sidebar-->