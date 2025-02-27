@section('title', 'Destinations')
{{-- @section('sub-title', 'Destinations') --}}
@section('sub-title', GoogleTranslate::trans('destinations', app()->getLocale()))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('destinations.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ GoogleTranslate::trans('Add Destination', app()->getLocale()) }} </a>
    </div>
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{-- {{ session()->get('message') }} --}}
        {{ GoogleTranslate::trans(session('message'), app()->getLocale()) }}

    </div>
    @endif
        <table class="table mt-3" id="destinations">
            <thead>
            <tr>

                <th> {{ GoogleTranslate::trans('Street', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('City', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('State', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Zip Code', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Country', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Actions', app()->getLocale()) }} </th>

            </tr>
            </thead>
            <tbody>
                @foreach($destinations as $destination)
                    <tr>
                    <td>{{ $destination->name ? $destination->name: '---' }}</td>
                    <td>{{ $destination->street }}</td>
                    <td>{{ $destination->city }}</td>
                    <td>{{ $destination->state }}</td>
                    <td>{{ $destination->zip }}</td>
                    <td>{{ $destination->country }}</td>
                        <td class="icon-design">
                            <a href="{{ route('destinations.edit', $destination) }}" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                        <form action="{{ route('destinations.destroy', $destination) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="delete-icon blue_icon_color" onclick="confirmDelete(this)">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('js_scripts')
<script>
    $(document).ready(function() {
    $('#destinations').DataTable();
    });
    function confirmDelete(element) {
        if (confirm("{{ GoogleTranslate::trans('Are you sure you want to delete this destination?', app()->getLocale()) }}")) {
            element.closest('.delete-form').submit();
        }
    }
</script>
@endsection