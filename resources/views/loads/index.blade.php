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

    <div class="filter-container">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="aol_number_filter" class="form-control" placeholder="{{ __('messages.Enter AOL Number') }}">
            </div>
            <div class="col-md-3">
                <select id="status_filter" class="form-control">
                    <option value="">{{ __('messages.Filter by Status') }}</option>
                    <option value="assigned">{{ __('messages.Assigned') }}</option>
                    <option value="requested">{{ __('messages.Requested') }}</option>
                </select>
            </div>
            <div class="col-md-3">
            <select id="creator_filter" class="form-control select2" multiple>
                <option value="">{{ __('messages.Filter by Creator') }}</option>
                @foreach($creators as $creator)
                            <option value="{{ $creator->creator->id }}">{{ isset($creator->creator->fname) ? $creator->creator->fname. ' '. $creator->creator->lname : $creator->creator->email }} </option>
                        @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select id="client_filter" class="form-control select2" multiple>
                <option value="">{{ __('messages.filter_by_client') }}</option>
                @foreach($creatorsclients as $client)
                    <option value="{{ $client->creator->id }}"> {{ optional($client->creator->client)->business_name 
                                ?? $client->creator->business_name 
                                ?? $client->creator->fname . ' ' . $client->creator->lname }}</option>
                @endforeach
            </select>
        </div>
       
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mt-3 respo_table" id="loads">
            <thead>
                <tr>

                    <th> {{ __('messages.AOL Number') }} </th>
                    <th> {{ __('messages.Service Type') }} </th>
                    <th> {{ __('messages.Origin') }} </th>
                    <th> {{ __('messages.Destination') }} </th>
                    <th>Created For</th>
                    <th> {{ __('messages.Payer') }} </th>
                    <th> {{ __('messages.Equipment Type') }} </th>
                    <th> {{ __('messages.Weight') }} </th>
                    <th>{{ __('messages.Schedule Date') }}</th>
                    <th> {{ __('messages.Delivery Deadline') }} </th>
                    <th> {{ __('messages.Customer PO') }} </th>
                    <th> {{ __('messages.HazMat') }} </th>
                    <th> {{ __('messages.Inbond') }} </th>
                    <th> {{ __('messages.Status') }} </th>
                    <th> {{ __('messages.Supplier Company') }} </th>
                    <th> {{ __('messages.Created By') }} </th>
                    <th width="160px">{{ __('messages.Actions') }} </th>
                    <th> {{ __('messages.Assign') }} </th>
                    <th>{{ __('messages.shipment_status') }}</th>
                    <th>{{ __('messages.update_truck_details') }}</th>
                    @if (auth()->user()->roledata->user_type_id == 3)
                    <th>{{ __('messages.add_invoice') }}</th>
                    @endif
                    @if (auth()->user()->roledata->user_type_id != 3)
                    <th>{{ __('messages.quickbooks_client_invoice') }}</th>
                    @endif
                    @if (auth()->user()->roledata->user_type_id == 3)
                    <th>{{ __('messages.quickbooks_supplier_invoice') }}</th>
                    @endif
                </tr>
            </thead>

        </table>
        
    </div>
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


    <form method="POST" id="changeStatusForm">
    @csrf
    @method('PUT')
    <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusLabel">{{ __('messages.change_shipment_status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="status">{{ __('messages.select_status') }}</label>
                    <select name="status" id="statusSelect" class="form-select">
                        <option value="pending">{{ __('messages.pending') }}</option>
                        <option value="in_transit">{{ __('messages.in_transit') }}</option>
                        <option value="delivered">{{ __('messages.delivered') }}</option>
                        <option value="cancelled">{{ __('messages.cancelled') }}</option>
                        <option value="ready_to_invoice">Ready to invoice</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="changeStatusBtn btn btn-primary create-button btn_primary_color">
                        {{ __('messages.update_status') }}
                    </button>
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
        var userType = @json(auth()->user()->roledata->user_type_id);

        let userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;    
        var table = $('#loads').DataTable({
            processing: true,
            serverSide: true,
            scrollY: '550px',
            scrollX: true, 
            scrollCollapse: true,
            // paging: false, 
            fixedHeader: false, 
            // footerCallback: function (row, data, start, end, display) {
            //     $(row).css('position', 'relative'); 
            // },
            order: [[0, 'desc']],
            ajax: function(data, callback, settings) {
                $('#loader').show();

                var aolNumber = $('#aol_number_filter').val();
                var status = $('#status_filter').val();
                var creator_filter = $('#creator_filter').val() || [];
                var client_filter = $('#client_filter').val() || [];
                console.log(creator_filter);
                $.ajax({
                    url: "{{ route('loads.index') }}",
                    data: {
                        aol_number: aolNumber,
                        status: status,
                        page: settings.page,
                        length: settings.length,
                        order: data.order, 
                        creator_filter: creator_filter,
                        client_filter: client_filter,
                        columns: data.columns
                    },
                    success: function(response) {
                        $('#loader').hide();

                        callback({
                            draw: settings.draw,
                            recordsTotal: response.recordsTotal,
                            recordsFiltered: response.recordsFiltered,
                            data: response.data
                        });
                    },
                    error: function() {
                        $('#loader').hide();
                    }
                });
            },
           
            columns: [
                { data: 'aol', name: 'aol_number' },
                { data: 'service_type', name: 'service_type' },
                { data: 'originval', name: 'origin' },
                { data: 'destinationval', name: 'destination' },
                { data: 'created_for_user', name: 'created_for_user', orderable: false, searchable: false },
                { data: 'payer', name: 'payer' },
                { data: 'equipment_type', name: 'equipment_type' },
                { data: 'weight', name: 'weight', render: function(data, type, row) {
                    return row.weight ? `${row.weight} ${row.weight_unit}` : 'N/A';
                } },
                { data: 'schedule', name: 'schedule', render: function(data) { 
                    return data ? moment(data).tz(userTimezone).format('MMM. D, YYYY HH:mm') : 'N/A'; 
                }  },
                { data: 'delivery_deadline', name: 'delivery_deadline', render: function(data) { return moment(data).tz(userTimezone).format('MMM. D, YYYY'); } },
                { data: 'customer_po', name: 'customer_po' },
                { data: 'is_hazmat', name: 'is_hazmat', orderable: false, searchable: false },
                { data: 'is_inbond', name: 'is_inbond', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'supplier_company_name', name: 'supplier_company_name' },
                { data: 'created_by_user', name: 'created_by' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
                { data: 'assign', name: 'assign', orderable: false, searchable: false },
                { data: 'shipment_status', name: 'shipment_status', orderable: false, searchable: false },
                { data: 'update_details', name: 'update_details', orderable: false, searchable: false },
                // { data: 'add_invoice', name: 'add_invoice', orderable: false, searchable: false },
                ...(userType === 3 ? [{ data: 'add_invoice', name: 'add_invoice', orderable: false, searchable: false }] : []),
                // { data: 'quickbooks_invoice', name: 'quickbooks_invoice', orderable: false, searchable: false },
                ...(userType != 3 ? [{ data: 'quickbooks_invoice', name: 'quickbooks_invoice', orderable: false, searchable: false }] : []),
                // { data: 'quickbooks_supplier_invoice', name: 'quickbooks_supplier_invoice', orderable: false, searchable: false },
                
                // Conditionally add 'quickbooks_supplier_invoice' column based on user type
                ...(userType === 3 ? [{ data: 'quickbooks_supplier_invoice', name: 'quickbooks_supplier_invoice', orderable: false, searchable: false }] : [])
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
                    targets: 5, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return data == 'another_party' ? 'Another party will pay for the load' : 'Client';
                    }
                },
                {
                    targets: 11, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<input type="checkbox" ' + (row.is_hazmat ? 'checked' : '') + ' disabled>';
                    }
                },
                {
                    targets: 12, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<input type="checkbox" ' + (row.is_inbond ? 'checked' : '') + ' disabled>';
                    }
                },
                {
                    targets: 16, 
                    className: 'icon-design',
                }
            ]
        });

        // Filter change event
        var delayTimer;
        $('#aol_number_filter, #status_filter').on('change keyup', function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(function() {
                table.draw();
            }, 200);
        });

        $('#creator_filter, #client_filter').on('change', function() {

            clearTimeout(delayTimer);
            delayTimer = setTimeout(function() {
                table.draw();
            }, 200);
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
    function changeStatusModal(loadId, shipmentStatus) {
        $('#statusSelect').val(shipmentStatus || 'pending');
        $('#changeStatusForm').attr('action', `/loads/${loadId}/change-status`);
        // Show the modal
        $('#changeStatusModal').modal('show');
    }
    $('#creator_filter').select2({
            placeholder: "{{ __('messages.Filter by Creator') }}",
            allowClear: true
        });

         $('#client_filter').select2({
            placeholder: "{{ __('messages.filter_by_client') }}",
            allowClear: true
        });

</script>

@endsection