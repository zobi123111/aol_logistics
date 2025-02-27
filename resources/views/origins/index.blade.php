@section('title', 'Origins')
{{-- @section('sub-title', 'Origins') --}}
@section('sub-title', GoogleTranslate::trans('Origins', app()->getLocale()))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('origins.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ GoogleTranslate::trans('Add Origin', app()->getLocale()) }} </a>
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
        <table class="table mt-3" id="origins">
            <thead>
            <tr>
                <th> {{ GoogleTranslate::trans('Name', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Street', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('City', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('State', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Zip Code', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Country', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Actions', app()->getLocale()) }} </th>

            </tr>
            </thead>
            <tbody>
                @foreach($origins as $origin)
                    <tr>
                    <td>{{ $origin->name ? $origin->name: '---' }}</td>
                    <td>{{ $origin->street }}</td>
                    <td>{{ $origin->city }}</td>
                    <td>{{ $origin->state }}</td>
                    <td>{{ $origin->zip }}</td>
                    <td>{{ $origin->country }}</td>
                        <td class="icon-design">
                            <a href="{{ route('origins.edit', $origin) }}" class=""><i
                            class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
                        <form action="{{ route('origins.destroy', $origin) }}" method="POST" class="d-inline delete-form">
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
    $('#origins').DataTable();
    });
    function confirmDelete(element) {
        if (confirm("{{ GoogleTranslate::trans('Are you sure you want to delete this origin?', app()->getLocale()) }}")) {
            element.closest('.delete-form').submit();
        }
    }
</script>
@endsection