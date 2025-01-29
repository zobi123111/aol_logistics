@section('title', 'Dashboard')
@section('sub-title', 'Dashboard')
@extends('layout.app')
@section('content')
<section class="section dashboard">
    <div class="">
        <p>Welcome to {{ env('PROJECT_NAME')}} dashboard </p>
    </div>
</section>



@endsection

@section('js_scripts')


@endsection