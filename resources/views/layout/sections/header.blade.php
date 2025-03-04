  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

      <div class="d-flex align-items-center justify-content-between">
          <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
              <img src="{{env('PROJECT_LOGO')}}" alt="">
              <!-- <span class="d-none d-lg-block">{{env('PROJECT_NAME_SHORT')}}</span> -->
          </a>
          <i class="bi bi-list toggle-sidebar-btn"></i>
      </div><!-- End Logo -->

      {{-- <nav class="header-nav ms-auto">

          <div class="col-md-6">
              <select class="form-select changeLang">
                  <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>English</option>
                  <option value="fr" {{ session()->get('locale') == 'fr' ? 'selected' : '' }}>France</option>
                  <option value="es" {{ session()->get('locale') == 'es' ? 'selected' : '' }}>Spanish</option>
              </select>
          </div>
          <ul class="d-flex align-items-center">

            

              <li class="nav-item d-block d-lg-none">
                  <a class="nav-link nav-icon search-bar-toggle " href="#">
                      <i class="bi bi-search"></i>
                  </a>
              </li><!-- End Search Icon-->
              <li class="nav-item dropdown pe-3">

                  <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                      <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('assets/img/dummy.png') }}"
                          alt="Profile" class="rounded-circle">
                      <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->fname }}


                      </span>
                  </a>
                  <!-- End Profile Iamge Icon -->

                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                      <li class="dropdown-header">
                          <h6>{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h6>
                          <span>{{ Auth::user()->roledata->role_name }}</span>
                      </li>
                      <!-- <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
            </li>
             -->
                      <li>
                          <hr class="dropdown-divider">
                      </li>

                      <li>
                          <a class="dropdown-item d-flex align-items-center" href="{{ url('logout') }}">
                              <i class="bi bi-box-arrow-right"></i>
                              <span>Sign Out</span>
                          </a>
                      </li>

                  </ul><!-- End Profile Dropdown Items -->
              </li><!-- End Profile Nav -->

          </ul>
      </nav> --}}

      <nav class="header-nav ms-auto d-flex align-items-center">

        <div class="col-md-6">
            <select class="form-select changeLang">
                <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>English</option>
                <option value="fr" {{ session()->get('locale') == 'fr' ? 'selected' : '' }}>French</option>
                <option value="es" {{ session()->get('locale') == 'es' ? 'selected' : '' }}>Spanish</option>
            </select>
        </div>
        
        <ul class="d-flex align-items-center ms-3">

            <button class="toggle-btn" onclick="toggleMode()">
                <i class="bi bi-moon" id="mode-icon"></i>
            </button>
            
            <li class="nav-item d-block d-lg-none">
                <a class="nav-link nav-icon search-bar-toggle" href="#">
                    <i class="bi bi-search"></i>
                </a>
            </li><!-- End Search Icon-->
            
            <li class="nav-item dropdown pe-5">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('assets/img/dummy.png') }}"
                        alt="Profile" class="rounded-circle">
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->fname }}</span>
                </a>
                <!-- End Profile Image Icon -->
    
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ Auth::user()->fname }} {{ Auth::user()->lname }}</h6>
                        <span>{{ Auth::user()->roledata->role_name }}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{ url('logout') }}">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>{{ __('messages.Sign Out') }}</span>
                            
                        </a>
                    </li>
                </ul><!-- End Profile Dropdown Items -->
            </li><!-- End Profile Nav -->
        </ul>
    </nav>
    
      <!-- End Icons Navigation -->

  </header><!-- End Header -->