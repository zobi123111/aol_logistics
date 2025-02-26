@section('title', 'Origins')
@section('sub-title', 'Origins')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('origins.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Add Origin</a>
    </div>
    <div id="successMessagea" class="alert alert-success" style="display: none;" role="alert">
        <i class="bi bi-check-circle me-1"></i>
    </div>
    @if(session()->has('message'))
    <div id="successMessage" class="alert alert-success fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session()->get('message') }}
    </div>
    @endif
        <table class="table mt-3" id="origins">
            <thead>
            <tr>
                <th>Name</th>
                <th>Street</th>
                <th>City</th>
                <th>State</th>
                <th>Zip Code</th>
                <th>Country</th>
                <th>Actions</th>
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
        if (confirm('Are you sure you want to delete this origin?')) {
            element.closest('.delete-form').submit();
        }
    }
</script>
@endsection