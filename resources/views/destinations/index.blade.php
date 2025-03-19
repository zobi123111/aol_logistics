@section('title', 'Destinations')
{{-- @section('sub-title', 'Destinations') --}}
@section('sub-title', __('messages.Destinations'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('destinations.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Add Destination') }} </a>
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
        <table class="table mt-3 respo_table" id="destinations">
            <thead>
            <tr>

                <th> {{ __('messages.Name') }} </th>
                <th> {{ __('messages.Street') }} </th>
                <th> {{ __('messages.City') }} </th>
                <th> {{ __('messages.State') }} </th>
                <th> {{ __('messages.Zip Code') }} </th>
                <th> {{ __('messages.Country') }} </th>
                <th> {{ __('messages.Actions') }} </th>

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
    $('#destinations').DataTable(
        {
    language: {
                sSearch: "{{ __('messages.Search') }}",
                sLengthMenu: "{{ __('messages.Show') }} _MENU_ {{ __('messages.entries') }}",
                sInfo: "{{ __('messages.Showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_ {{ __('messages.entries') }}",
                oPaginate: {
                    sPrevious: "{{ __('messages.Previous') }}",
                    sNext: "{{ __('messages.Next') }}"
                }
            },
    }
    );
    });
    function confirmDelete(element) {
        if (confirm("{{ __('messages.Are you sure you want to delete this destination?') }}")) {
            element.closest('.delete-form').submit();
        }
    }
</script>
@endsection