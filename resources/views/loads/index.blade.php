@section('title', 'Loads')
@section('sub-title', 'Loads')
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole">Add Load</a>
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
            <th>AOL Number</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Payer</th>
                <!-- <th>Equipment Type</th> -->
                <th>Weight</th>
                <th>Delivery Deadline</th>
                <th>Customer PO</th>
                <th>HazMat</th>
                <th>Inbond</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Assign</th>

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
                        <h5 class="modal-title" id="exampleModalLabel">Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary load_delete btn_primary_color">Delete</button>
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
        columns: [
            { data: 'aol_number', name: 'aol_number' },
            { data: 'origin', name: 'origin' },
            { data: 'destination', name: 'destination' },
            { data: 'payer', name: 'payer'},
            // { data: 'equipment_type', name: 'equipment_type' },
            { data: 'weight', name: 'weight' },
            { data: 'delivery_deadline', name: 'delivery_deadline',  render: function (data, type, row) {
                if (data) {
                    return moment(data).format('YYYY-MM-DD hh:mm A');
                }
                return data; 
            } },
            { data: 'customer_po', name: 'customer_po' },
            { data: 'is_hazmat', name: 'is_hazmat', orderable: false, searchable: false },
            { data: 'is_inbond', name: 'is_inbond', orderable: false, searchable: false },
            { data: 'status', name: 'status'},
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'assign', name: 'assign', orderable: false, searchable: false },
        ],
        columnDefs: [
            {
            targets: 3, 
            className: 'text-center',
            render: function(data, type, row, meta) {
               return data=='another_party'? 'Another party will pay for the load':'Client'
            }
        },
            {
            targets: 7, 
            className: 'text-center',
            render: function(data, type, row) {
                return '<input type="checkbox" ' + (row.is_hazmat ? 'checked' : '') + ' disabled>';
            }
        },
        {
            targets: 8, 
            className: 'text-center',
            render: function(data, type, row) {
                return '<input type="checkbox" ' + (row.is_inbond ? 'checked' : '') + ' disabled>';
            }
        }
        ]
    });
    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        var loadid = $(this).data('load-id');
        var modal_text =
            `Are you sure you want to delete ?`;
        $('.delete_content').html(modal_text);
        $('#deleteloadFormId').attr('action', `/loads/${loadid}`);

        $('#deleteloadForm').modal('show');
    });
});
</script>

@endsection