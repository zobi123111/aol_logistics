@section('title', 'Login')
@include('layout.includes.head')
<main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                        <div class="d-flex justify-content-center py-4">
                            <a href="index.html" class="logo d-flex align-items-center w-auto">
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


                                <!-- OTP Form (Initially Hidden) -->
                                <form class="row g-3 needs-validation" novalidate id="otp_form">
                                    <input type="hidden" id="otp_email" value="{{ session('otp_email') }}">
                                    @csrf
                                    <div class="col-12">
                                        <label for="otp" class="form-label">Enter OTP</label>
                                        <input type="text" name="otp" class="form-control" id="otp" required>
                                        <div class="invalid-feedback">Please enter the OTP sent to your email.</div>
                                    </div>

                                    <span class="text-danger otp_error"></span>

                                    <!-- Timer on Left | Resend OTP Link on Right -->
                                    <div class="d-flex align-items-center" style="justify-content: space-between;">
                                        <span id="timer" class="text-danger me-2"></span> <!-- Timer (Left) -->
                                        <button id="resend_otp_btn" class="small-text btn resend_otp_btn disabled"
                                            type="button">
                                            Resend OTP <span id="timer"></span>
                                        </button>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 btn_primary_color" type="submit">Submit
                                            OTP</button>
                                    </div>

                                    <div class="col-12">
                                        <a href="{{ route('login') }}"
                                            class="btn btn-primary w-100 btn_primary_color">Back to Login</a>
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
@include('layout.sections.footer')
@include('layout.includes.js')

<script>
$(document).ready(function() {
    let timerDuration = 120; // 2 minutes in seconds
    let timer = timerDuration;
    let interval = null;

    function startTimer() {
        $('#resend_otp_btn').addClass('disabled'); // Disable Resend Button
        interval = setInterval(function() {
            if (timer > 0) {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;
                $('#timer').text(`(${minutes}:${seconds < 10 ? '0' : ''}${seconds})`);
                timer--;
            } else {
                clearInterval(interval);
                $('#resend_otp_btn').removeClass('disabled').text(
                    "Resend OTP"); // Enable Resend OTP Button
                $('#timer').text(''); // Hide timer when finished
            }
        }, 1000);
    }

    // Check if timer should start based on session time
    let lastOtpSentTime = "{{ session('last_otp_sent_time') }}";
    if (lastOtpSentTime) {
        let elapsedTime = Math.floor((new Date().getTime() / 1000) - lastOtpSentTime);
        if (elapsedTime < timerDuration) {
            timer = timerDuration - elapsedTime;
            startTimer();
        } else {
            $('#resend_otp_btn').removeClass('disabled').text("Resend OTP");
        }
    } else {
        startTimer();
    }

    // Resend OTP AJAX Request
    $('#resend_otp_btn').click(function() {
        let userEmail = $('#otp_email').val(); // Get email from hidden input

        if (!userEmail) {
            alert("User email is missing!");
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('resendotp') }}",
            data: {
                _token: "{{ csrf_token() }}",
                email: userEmail // Send email in request
            },
            success: function(response) {
                alert(response.success);
                timer = response.remaining_time;
                startTimer(); // Restart the timer if needed
            },
            error: function(xhr) {
                if (xhr.status === 429) {
                    let errorResponse = JSON.parse(xhr.responseText);
                    alert(errorResponse.error);
                    timer = errorResponse.remaining_time;
                    startTimer();
                }
            }
        });
    });

    // OTP Form Submission
    $('#otp_form').submit(function(event) {
        event.preventDefault();
        $('.otp_error').html('');

        var otpData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{ route('verifyotp') }}",
            data: otpData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    $('.otp_error').text(response.error);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errorResponse = JSON.parse(xhr.responseText);

                    if (errorResponse.errors && errorResponse.errors.otp) {
                        var otpErrors = errorResponse.errors.otp.join('<br>');
                        $('.otp_error').html(otpErrors);
                    } else if (errorResponse.error) {
                        $('.otp_error').html(errorResponse.error);
                    }
                }
            }
        });
    });
});
</script>