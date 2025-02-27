@include('layout.includes.head')

<body>

    <style>

        .loader {
            opacity: 0.7;
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url(assets/img/double_ring.svg) 50% 50% no-repeat #000;
        }
      
      
      
        /* .dropdown-menu li a::after {
            margin-right: 4px;
            position: absolute;
            right: 15px;
            content: "";
            font-family: fcicons !important;
        } */
      
      
        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
      
      </style>

    @include('layout.sections.header')
    @include('layout.sections.sidebar')

    <main id="main" class="main">
        <div class="new_loader">
            <img src="{{env('LOADER_IMG')}}" alt="">
        </div>
        <div class="pagetitle">
            <h1>@yield('sub-title')</h1>
           
            {{-- <h1>{{ GoogleTranslate::trans(trim(@yield('sub-title')), app()->getLocale()) }}</h1> --}}

        </div><!-- End Page Title -->
        <!--begin::Main-->
        @yield('content')
        <!--end::Main-->

    </main>

    @include('layout.sections.footer')

    @include('layout.includes.js')
    <script type="text/javascript">
    $(document).ready(function() {});
    </script>
    @yield('js_scripts')


    <div class="loader" id="loader" style="display: none;"></div>


    <script>
        var url = "{{ route('changeLang') }}";
    
        $(".changeLang").change(function() {
            // Show loader
            $("#loader").show();
    
            window.location.href = url + "?lang=" + $(this).val();
        });
    
        // $(window).on('load', function() {
        //     // $("#loader").hide();
        // });
    </script>
    

</body>

</html>