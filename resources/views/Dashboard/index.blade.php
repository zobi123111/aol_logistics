@section('title', 'Dashboard')
@section('sub-title', 'Dashboard')
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
        <p>Welcome to {{ env('PROJECT_NAME')}} dashboard </p>
    </div>
</section>



@endsection

@section('js_scripts')


@endsection