@section('title', 'Loads')
{{-- @section('sub-title', 'Loads') --}}
@section('sub-title', __('messages.Loads'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ __('messages.Add Load') }} </a>
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
    
    <table class="table mt-3" id="loads">
        <thead>
            <tr>

                <th> {{ __('messages.AOL Number') }} </th>
                <th> {{ __('messages.Service Type') }} </th>
                <th> {{ __('messages.Origin') }} </th>
                <th> {{ __('messages.Destination') }} </th>
                <th> {{ __('messages.Payer') }} </th>
                <th> {{ __('messages.Equipment Type') }} </th>
                <th> {{ __('messages.Weight') }} </th>
                <th>Schedule Date</th>
                <th> {{ __('messages.Delivery Deadline') }} </th>
                <th> {{ __('messages.Customer PO') }} </th>
                <th> {{ __('messages.HazMat') }} </th>
                <th> {{ __('messages.Inbond') }} </th>
                <th> {{ __('messages.Status') }} </th>
                <th> {{ __('messages.Supplier Company') }} </th>
                <th> {{ __('messages.Created By') }} </th>
                <th width="160px">{{ __('messages.Actions') }} </th>
                <th> {{ __('messages.Assign') }} </th>
            </tr>
        </thead>

    </table>
    <form method="POST" id="deleteloadFormId">
        @csrf
        @method('DELETE')
        <div class="modal fade" id="deleteloadForm" tabindex="-1" aria-labelledby="exampleModalLabel"
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
                        <button type="submit" class="btn btn-primary load_delete btn_primary_color"> {{ __('messages.Delete') }} </button>
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
    $('#loads').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('loads.index') }}", 
        order: [],
        columns: [
            { data: 'aol_number', name: 'aol_number' },
            { data: 'service_type', name: 'service_type' },
            { data: 'originval', name: 'origin' },
            { data: 'destinationval', name: 'destination' },
            { data: 'payer', name: 'payer'},
            { data: 'equipment_type', name: 'equipment_type' },
            { data: 'weight', name: 'weight' },
            { data: 'schedule', name: 'schedule',  render: function (data, type, row) {
                if (data) {
                    return moment(data).format('YYYY-MM-DD hh:mm A');
                }
                return data; 
            } },
            { data: 'delivery_deadline', name: 'delivery_deadline',  render: function (data, type, row) {
                if (data) {
                    return moment(data).format('YYYY-MM-DD');
                }
                return data; 
            } },
            { data: 'customer_po', name: 'customer_po' },
            { data: 'is_hazmat', name: 'is_hazmat', orderable: false, searchable: false },
            { data: 'is_inbond', name: 'is_inbond', orderable: false, searchable: false },
            { data: 'status', name: 'status'},
            {
            data: 'supplier_company_name',
            name: 'supplier_company_name',
            render: function(data) {
                    if (!data || data === '---') {
                        return '---';
                    }

                    let suppliers = data.split(', ');
                    return suppliers.map(supplier => `<span class="badge bg-primary me-1">${supplier}</span>`).join('');
                }
        },
        { data: 'created_by', name: 'created_by' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'assign', name: 'assign', orderable: false, searchable: false },
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
        columnDefs: [
            {
            targets: 4, 
            className: 'text-center',
            render: function(data, type, row, meta) {
               return data=='another_party'? 'Another party will pay for the load':'Client'
            }
        },
            {
            targets: 9, 
            className: 'text-center',
            render: function(data, type, row) {
                return '<input type="checkbox" ' + (row.is_hazmat ? 'checked' : '') + ' disabled>';
            }
        },
        {
            targets: 10, 
            className: 'text-center',
            render: function(data, type, row) {
                return '<input type="checkbox" ' + (row.is_inbond ? 'checked' : '') + ' disabled>';
            }
        },
        {
            targets: 13, 
            className: 'icon-design',
           
        }
        ]
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var loadid = $(this).data('load-id');
        var modal_text =
            `{{ __('messages.Are you sure you want to delete ?') }}`;
        $('.delete_content').html(modal_text);
        $('#deleteloadFormId').attr('action', `/loads/${loadid}`);

        $('#deleteloadForm').modal('show');
    });
});
</script>

@endsection