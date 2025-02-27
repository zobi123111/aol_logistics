@section('title', 'Loads')
{{-- @section('sub-title', 'Loads') --}}
@section('sub-title', GoogleTranslate::trans('Loads', app()->getLocale()))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('loads.create') }}" class="btn btn-primary create-button btn_primary_color"
            id="createrole"> {{ GoogleTranslate::trans('Add Load', app()->getLocale()) }} </a>
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
    <table class="table mt-3" id="loads">
        <thead>
            <tr>
                <th> {{ GoogleTranslate::trans('AOL Number', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Service Type', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Origin', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Destination', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Payer', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Equipment Type', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Weight', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Delivery Deadline', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Customer PO', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('HazMat', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Inbond', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Status', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Supplier Company', app()->getLocale()) }} </th>
                <th width="160px"> {{ GoogleTranslate::trans('Actions', app()->getLocale()) }} </th>
                <th> {{ GoogleTranslate::trans('Assign', app()->getLocale()) }} </th>

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
                        <h5 class="modal-title" id="exampleModalLabel"> {{ GoogleTranslate::trans('Add Load', app()->getLocale()) }} Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body delete_content">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close_btn" data-bs-dismiss="modal"> {{ GoogleTranslate::trans('Add Load', app()->getLocale()) }} Close</button>
                        <button type="submit" class="btn btn-primary load_delete btn_primary_color"> {{ GoogleTranslate::trans('Add Load', app()->getLocale()) }} Delete</button>
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
            { data: 'suppliercompany', name: 'suppliercompany' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
            { data: 'assign', name: 'assign', orderable: false, searchable: false },
        ],
        language: {
            sSearch: "{{ GoogleTranslate::trans('Search', app()->getLocale()) }}",
            sLengthMenu: "{{ GoogleTranslate::trans('Show', app()->getLocale()) }} _MENU_ {{ GoogleTranslate::trans('entries', app()->getLocale()) }}",
            sInfo: "{{ GoogleTranslate::trans('Showing', app()->getLocale()) }} _START_ {{ GoogleTranslate::trans('to', app()->getLocale()) }} _END_ {{ GoogleTranslate::trans('of', app()->getLocale()) }} _TOTAL_ {{ GoogleTranslate::trans('entries', app()->getLocale()) }}",
            oPaginate: {
                sPrevious: "{{ GoogleTranslate::trans('Previous', app()->getLocale()) }}",
                sNext: "{{ GoogleTranslate::trans('Next', app()->getLocale()) }}"
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
            `{{ GoogleTranslate::trans('Are you sure you want to delete ?', app()->getLocale()) }}`;
        $('.delete_content').html(modal_text);
        $('#deleteloadFormId').attr('action', `/loads/${loadid}`);

        $('#deleteloadForm').modal('show');
    });
});
</script>

@endsection