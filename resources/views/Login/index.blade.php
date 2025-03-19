@section('title', 'Login')
@include('layout.includes.head')
<main>

@if(session('logout'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.remove('dark-mode');
            
            const modeIcon = document.getElementById('mode-icon');
            if (modeIcon) {
                modeIcon.classList.remove('bi-sun');
                modeIcon.classList.add('bi-moon');
            }

            localStorage.removeItem('darkMode');
            
            document.cookie = "alpha_omega_logistcs_session=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
        });
    </script>
@endif

    <div class="container">

        <section class="section register  d-flex flex-column align-items-center justify-content-center ">
     
            <div class="container">
         
                <div class="row justify-content-center">
                <button class="toggle-btn toogle-btn-login" onclick="toggleMode()">
                    <i class="bi bi-moon" id="mode-icon"></i>
                </button>
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                  
                        <div class="d-flex justify-content-center py-4">
                            <a href="#" class="logo d-flex align-items-center w-auto">
                                <img src="{{env('PROJECT_LOGO')}}" alt="">
                                <!-- <span class="d-none d-lg-block">{{ env('PROJECT_NAME') }}</span> -->
                            </a>
                        </div><!-- End Logo -->

                        <div class="card mb-3">

                            <div class="card-body">
                       

                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                    <p class="text-center small">Enter your username & password to login</p>
                                </div>
                                @if(session()->has('message'))
                                <div id="successMessage" class="alert alert-success fade show" role="alert">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ session()->get('message') }}
                                </div>
                                @endif
                                <form class="row g-3 needs-validation" novalidate id="login_form">
                                    @csrf
                                    <div class="col-12">
                                        <label for="yourUsername" class="form-label">Username</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                            <input type="text" class="form-control" id="yourUsername" name="email"
                                                required>
                                            <div class="invalid-feedback">Please enter your username.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="yourPassword" class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" id="yourPassword"
                                            required>
                                        <div class="invalid-feedback">Please enter your password!</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" value="true"
                                                id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                    </div>

                                    <span
                                        class="text-danger credential_error">{{ $errors->first('credentials_error') }}</span>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 btn_primary_color login-btn"
                                            type="submit"><span>Login</span></button>
                                    </div>
                                    <div class="col-12">
                                    <div class="social-media">        
                                        <p class="small mb-0"><a href="{{ url('forgot-password') }} ">Forgot Password ?
                                            </a></p>
                                            <div class="social-links">

                                            <a href="https://www.instagram.com/alphaomega_log" target="_blank">
                                                <img src="/assets/img/instagram.png" alt="Instagram">
                                            </a>

                                            <a href="https://x.com/alphaomega_log" target="_blank">
                                                <img src="/assets/img/twitter.png" alt="Twitter">
                                            </a>
                                        </div>
                                    </div>
                                    </div>

                                </form>

                                <!-- OTP Form (Initially Hidden) -->
                                <form class="row g-3 needs-validation" novalidate id="otp_form" style="display:none;">
                                    @csrf
                                    <div class="col-12">
                                        <label for="otp" class="form-label">Enter OTP</label>
                                        <input type="text" name="otp" class="form-control" id="otp" required>
                                        <div class="invalid-feedback">Please enter the OTP sent to your email.</div>
                                    </div>

                                    <span class="text-danger otp_error"></span>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 btn_primary_color" type="submit">Submit
                                            OTP</button>
                                    </div>
                                </form>

                            </div>
                        </div>



                    </div>
                </div>
            </div>

        </section>
    </div>
</main><!-- End #main -->
@include('layout.includes.js')

<script>
$(document).ready(function() {
    $("#login_form").submit(function(event) {
        event.preventDefault();

        let button = $(this).find(".login-btn"); // Find the button inside the form

        // Show spinner & disable button
        // $(".show_loader").removeClass("noactive");
        button.prop("disabled", true).find("span:last").text("Logging in...");

        $('.error_e').html('');
        var formData = new FormData(this);
          // Refresh CSRF token before submitting OTP form
    $.get('/sanctum/csrf-cookie').then(() => {
        $.get('/csrf-token').then(response => {
            $('meta[name="csrf-token"]').attr('content', response.csrfToken);
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': response.csrfToken }
            });
        $.ajax({
            type: 'POST',
            url: "{{ route('login') }}",
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(data) {
                // $(".show_loader").addClass("noactive");
                button.prop("disabled", false).find("span:last").text("Login");
                if (data.otp_required) {
                    window.location.href = "{{ route('otp.verify') }}";
                } else if (data.credentials_error) {
                    $('.error_ee').html('');
                    $('.credential_error').html(data.credentials_error);
                } else {
                    window.location.href = "{{ route('dashboard')}}";
                }

            },
            error: function(xhr, textStatus, errorThrown) {
                if (xhr.status === 422) {
                    var errorResponse = xhr.responseText;
                    var errorResponse = JSON.parse(errorResponse);
                    if (errorResponse) {
                        $('#otp_msg').hide();
                        $.each(errorResponse.errors, function(key, value) {
                            var html1 = '<p>' + value + '</p>';
                            $('#' + key + '_error').html(html1);
                        });
                    }
                }
                // $(".show_loader").addClass("noactive");
                button.prop("disabled", false).find("span:last").text("Login");
            }
        });
    });
    }).catch(error => {
        console.error("CSRF token refresh failed", error);
        button.prop("disabled", false).text("Submit OTP");
    });

    });

    setTimeout(function() {
        $('#successMessage').fadeOut('fast');
    }, 2000);

});
</script>