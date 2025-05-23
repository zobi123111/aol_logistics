  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center">
                <img src="{{env('PROJECT_LOGO')}}" alt="">
                <!-- <span class="d-none d-lg-block">{{env('PROJECT_NAME_SHORT')}}</span> -->
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <nav class="header-nav ms-auto d-flex align-items-center">
            @php
                $currentLang = session()->get('locale', 'en'); // Default to English if not set
            @endphp

            <div class="col-md-6">
                <div class="dropdown">
                    <button class="dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        @if($currentLang == 'en')
                            <img src="{{ asset('assets/img/usa-flag.jpg') }}" alt="English Flag" style="width: 30px; height: 20px; vertical-align: middle;">
                        @else
                            <img src="{{ asset('assets/img/mexican-flag.png') }}" alt="Spanish Flag" style="width: 30px; height: 20px; vertical-align: middle;">
                        @endif
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item changeLang" href="#" data-lang="en">
                                <img src="{{ asset('assets/img/usa-flag.jpg') }}" alt="English Flag" style="width: 20%; height: 10%; vertical-align: middle;"> English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item changeLang" href="#" data-lang="es">
                                <img src="{{ asset('assets/img/mexican-flag.png') }}" alt="Spanish Flag" style="width: 20%; height: 10%; vertical-align: middle;"> Spanish
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


        
            
            <ul class="d-flex align-items-center ms-3">

                <button class="toggle-btn" onclick="toggleMode()">
                    <i class="bi bi-moon" id="mode-icon"></i>
                </button>
                </li><!-- End Search Icon-->
                
                <li class="nav-item dropdown pe-5">
                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('assets/img/dummy.png') }}"
                            alt="Profile" class="rounded-circle">
                    @php
                        $fullName = Auth::user()->fname;
                        $maxLength = 8;
                        if (strlen($fullName) > $maxLength) {
                            $truncated = substr($fullName, 0, $maxLength) . '...';
                        } else {
                            $truncated = $fullName;
                        }
                    @endphp

                    <span 
                    class="d-none d-md-block dropdown-toggle ps-2" 
                    style="max-width: 150px; display: inline-block; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" 
                    data-bs-toggle="tooltip" 
                    title="{{ $fullName }}">
                    {{ $truncated }}
                    </span>    
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