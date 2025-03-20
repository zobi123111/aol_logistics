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
           
             <h1></h1> 

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
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".changeLang").forEach(item => {
                item.addEventListener("click", function(event) {
                    event.preventDefault();
                    let lang = this.getAttribute("data-lang");

                    // Redirect to change language route
                    window.location.href = "{{ route('changeLang') }}?lang=" + lang;
                });
            });

            let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            console.log(timezone);

            fetch("{{ route('set.timezone') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ timezone: timezone })
            });
        });
    </script>
</body>

</html>