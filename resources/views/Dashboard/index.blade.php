@section('title', 'Dashboard')
@section('sub-title', env('PROJECT_NAME'))
@extends('layout.app')
@section('content')
<section class="section dashboard">
    <div class="">
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
      {{ session()->get('message') }} 
    </div>
    @endif
        <!-- {{-- <p>Welcome to {{ env('PROJECT_NAME')}} dashboard </p> --}} -->
        <!-- <p>{{ __('messages.Welcome to') . ' ' . env('PROJECT_NAME') . ' ' . __('messages.Dashboard') }}</p> -->

    </div>
   

<div class="container">
    <!-- Welcome Section -->
    <div class="text-center py-4 mb-4 border-bottom">
        <h3 class="fw-bold">🎉 Welcome to {{ env('PROJECT_NAME') }}!</h3>
        <p class="text-muted lead">Hey <strong>{{ Auth::user()->fname }} {{ Auth::user()->lname }}</strong>, we're excited to have you on board! 😊</p>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-12">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                    <h5 class="card-title">🔑 Your Account Details</h5>
                    <ul class="list-unstyled mb-0">
                        <li>📧 <strong>Email:</strong> {{ Auth::user()->email }}</li>
                        <li>🏷 <strong>User Type:</strong> 
                            <span class="badge bg-primary">{{ Auth::user()->roledata->userType->name ?? 'Not Assigned' }}</span>
                        </li>
                        <li>🔰 <strong>Role:</strong> 
                            <span class="badge bg-info">{{ Auth::user()->roledata->role_name ?? 'Not Assigned' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users List -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                    <h5 class="card-title">🚀 {{ __('messages.Active Users')}} <span class="badge bg-success" style="color:white">{{ $activeUsers->count() }}</span></h5>
                    <ul class="list-group mt-3">
                        @forelse($activeUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    👤 <strong>{{ $user->fname }} {{ $user->lname }}</strong> 
                                    <small class="text-muted">({{ $user->email }})</small> <br>
                                    🏷 <strong>User Type:</strong> 
                                    <span class="badge bg-primary">{{ $user->roledata->userType->name ?? 'Not Assigned' }}</span> 
                                    🔰 <strong>Role:</strong> 
                                    <span class="badge bg-info">{{ $user->roledata->role_name ?? 'Not Assigned' }}</span>
                                </div>
                                <span class="badge bg-success">🟢 Online</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-danger">😔 No active users found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- User Statistics Chart -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm dashboard-design">
                <div class="card-body">
                    <h5 class="card-title">📊 User Statistics (Active vs Total)</h5>
                    <div id="userChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



</section>



@endsection

@section('js_scripts')

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var options = {
            chart: {
                type: "bar",
                height: 350
            },
            series: [
                {
                    name: "{{ __('messages.Active Users') }}",
                    className: "users_class",
                    data: [ {{ $activeTotalAol }}, {{ $activeSuppliers }} , {{ $activeClients }}]
                },
                {
                    name: "{{ __('messages.Total Users') }}",
                    className: "users_class",
                    data: [{{ $totalAol }}, {{ $totalSuppliers }}, {{ $totalClients }}]
                }
            ],
            xaxis: {
                categories: [
                    // "AOL Users", "Suppliers", "Clients"
                    "{{ __('messages.AOL Users') }}", 
                    "{{ __('messages.Suppliers') }}", 
                    "{{ __('messages.Client Users') }}"
                ]
            },
            colors: ["#28a745", "#007bff"], // Green for active, Blue for total
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "45%"
                }
            },
            dataLabels: {
                enabled: false
            }
        };

        var chart = new ApexCharts(document.querySelector("#userChart"), options);
        chart.render();
    });
</script>

@endsection