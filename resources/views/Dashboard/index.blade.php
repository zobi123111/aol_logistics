@section('title', 'Dashboard')
@section('sub-title', env('PROJECT_NAME'))
@extends('layout.app')
@section('content')
<section class="section dashboard">
    <div class="">
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{-- {{ session()->get('message') }} --}}
        {{ GoogleTranslate::trans(session('message'), app()->getLocale()) }}
    </div>
    @endif
        {{-- <p>Welcome to {{ env('PROJECT_NAME')}} dashboard </p> --}}
        <p>{{ __('messages.Welcome to') . ' ' . env('PROJECT_NAME') . ' ' . __('messages.Dashboard') }}</p>

    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h5 class="card-title"> {{ __('messages.User Statistics (Active vs Total)') }}</h5>
                    <div id="userChart"></div>
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