@section('title', 'Supplier Services')
{{-- @section('sub-title', 'Supplier Services') --}}
@section('sub-title', __('messages.Supplier Service'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('master-services.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Add Service') }} </a>
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

    @if(session()->has('error'))
    <div class="alert alert-danger fade show" role="alert">
        <i class="bi bi-x-circle me-1"></i>
        {{ session()->get('error') }}
    </div>
    @endif
    <table class="table mt-3 respo_table" id="servicesTable">
        <thead>
            <tr>
                <th> {{ __('messages.Service Type') }}  </th>
                <th> {{ __('messages.Service Name') }}  </th>
                <th> {{ __('messages.Origin') }} </th>
                <th> {{ __('messages.Destination') }} </th>
                <th>{{ __('messages.Location') }}</th>
                <th> {{ __('messages.Actions') }} </th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <form method="POST" id="deleteRoleFormId">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteRoleForm" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{ __('messages.Delete') }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal"> {{ __('messages.Close') }} </button>
                        <button type="submit" class="btn btn-primary role_delete btn_primary_color"> {{ __('messages.Delete') }} </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- End of Delete Model -->
@endsection

@section('js_scripts')

<script>
$(document).ready(function() {
      window.Laravel = {
        translations: @json(__('messages'))
    };
    $('#servicesTable').DataTable({
        processing: true,
    serverSide: true,
    ajax: "{{ route('master-services.data') }}",
    columns: [
       {
            data: 'service_type',
            name: 'service_type',
            render: function (data, type, row) {
        if (typeof data === 'string' && data.length > 0) {
            // Convert snake_case to Title Case
            let label = data.replace(/_/g, ' ')
                            .replace(/\w\S*/g, function(txt) {
                                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                            });

            // Use Laravel translation key
            return Laravel.translations[label] || label;

        }
        return data;
    }
        },
        { data: 'service_name', name: 'service_name' },
        { data: 'origin', name: 'origin' },
        { data: 'destination', name: 'destination' },
        { data: 'warehouse', name: 'warehouse', orderable: false, searchable: false },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ],
    columnDefs: [
                {
                    targets:  5, 
                    className: 'icon-design',
                }
            ],
    language: {
                sSearch: "{{ __('messages.Search') }}",
                sLengthMenu: "{{ __('messages.Show') }} _MENU_ {{ __('messages.entries') }}",
                sInfo: "{{ __('messages.Showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_ {{ __('messages.entries') }}",
                oPaginate: {
                    sPrevious: "{{ __('messages.Previous') }}",
                    sNext: "{{ __('messages.Next') }}"
                }
            },
        });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var serviceId = $(this).data('service-id');
        // var username = $(this).closest('tr').find('.username').text();
        var modal_text =
            `{{ __('messages.Are you sure you want to delete ?') }}`;
        $('.delete_content').html(modal_text);
        $('#deleteRoleFormId').attr('action', `/master-services/${serviceId}`);

        $('#deleteRoleForm').modal('show');
    });
});
</script>

@endsection