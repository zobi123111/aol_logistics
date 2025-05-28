@section('title', __('messages.Truck Number'))
@section('sub-title', __('messages.Truck Number'). ' | Company: ' . $supplier->company_name)

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }} </a>
        <a href="{{ route('supplier_trailers.create', encode_id($supplier->id)) }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Add truck Number') }} </a>
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
    @php
    $supplierId = request()->route('supplierId'); 
 
@endphp

<input type="hidden" id="supplierId" value="{{ $supplierId }}">
      <table class="table mt-3 respo_table" id="trailers-table">
        <thead>
            <tr>
               <th>{{ __('messages.Truck Number') }}</th>
                <th>{{ __('messages.Actions') }}</th>
            </tr>
        </thead>
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
        $('#trailers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("supplier_trailers.index", encode_id($supplier->id)) }}',
        columns: [
            { data: 'trailer_num', name: 'trailer_num' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
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
            var trailer = $(this).data('trailer-id');
            var sup = $(this).data('sup-id');
            var username = $(this).closest('tr').find('.username').text();
            var modal_text =
                ` {{ __('messages.Are you sure you want to delete') }}?`;
            $('.delete_content').html(modal_text);
            $('#deleteRoleFormId').attr('action', `/suppliers/${sup}/trailers/${trailer}`);

            $('#deleteRoleForm').modal('show');
        });
    });

    $(document).on('change', '.status-toggle', function() {
        const toggleSwitch = $(this);
        var userId = $(this).data('id');
        var isActive = $(this).prop('checked') ? 1 : 0;

        $.ajax({
            url: '{{ route("users.toggleStatus") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: userId,
                is_active: isActive
            },
            success: function(response) {
                if (response.success) {
                    const label = toggleSwitch.siblings("label");
                    label.text(isActive ? "Active" : "Inactive");
                    $('#successMessagea').text(response.message).fadeIn().delay(3000).fadeOut();

                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while updating the user status.');
            }
        });
    });
</script>

@endsection