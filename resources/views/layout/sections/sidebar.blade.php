  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
  @if(!empty(getAllowedPages()) && is_iterable(getAllowedPages()))
    @foreach(getAllowedPages() as $page)
        @if(isset($page->route_name, $page->icon, $page->name)) 
            <li class="nav-item">
                <a class="nav-link {{ Request::is($page->route_name) ? 'active' : '' }}" href="{{ url($page->route_name) }}">
                    <i class="{{ $page->icon }}"></i> 
                    {{-- <span>{{ ucfirst($page->name) }}</span> --}}
                    <span>{{ ucfirst(__('messages.'.$page->name)) }}</span>
                </a>
            </li>
        @endif
    @endforeach
@endif
 

@if(Auth::check() && Auth::user()->roledata->role_slug == config('constants.roles.MASTERCLIENT'))
<li class="nav-item">
        <a class="nav-link {{ Request::routeIs('supplier_users.index') ? 'active' : '' }}" href="{{ route('supplier_users.index', encode_id(Auth::user()->supplier_id)) }}">
        <i class="bi bi-people"></i> 
            <span>Supplier Users</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('supplier_units.index') ? 'active' : '' }}" href="{{ route('supplier_units.index', encode_id(Auth::user()->supplier_id)) }}">
        <i class="bi bi-truck"></i> 
            <span>  {{ __('messages.Supplier Equipment') }} </span>
        </a>
    </li>   
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('services.index') ? 'active' : '' }}" href="{{ route('services.index', encode_id(Auth::user()->supplier_id)) }}">
        <i class="bi bi-gear"></i> 
            <span>  {{ __('messages.Supplier Services') }}  </span>
        </a>
    </li>
@endif
@if(Auth::check() && Auth::user()->roledata->role_slug == config('constants.roles.CLIENTMASTERCLIENT'))
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('client_users.index') ? 'active' : '' }}" href="{{ route('client_users.index', encode_id(Auth::user()->client_id)) }}">
        <i class="bi bi-people"></i> 
            <span>  {{ __('messages.Client Users') }} </span>
        </a>
    </li>
@endif
@if(isOwnerOrDev())
<li class="nav-item">
        <a class="nav-link {{ Request::routeIs('master-services.index') ? 'active' : '' }}" href="{{ route('master-services.index') }}">
        <i class="bi bi-bell"></i> 
            <span> Master Service </span>
        </a>
    </li>
    @endif
</ul>

  </aside><!-- End Sidebar-->