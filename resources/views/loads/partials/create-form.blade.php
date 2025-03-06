<form action="{{ route('assign.service.store', $load->id) }}" method="POST">
    @csrf
    <input type="hidden" name="load_id" value="{{ $load->id }}">

    <!-- Supplier Selection -->
    <div class="form-group">
        <label for="supplier">Select Supplier</label>
        <select name="supplier_id" id="supplier" class="form-control">
            <option value="">Choose Supplier</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Service Type Selection -->
    <div class="form-group">
        <label for="service_type">Service Type</label>
        <select name="service_type" id="service_type" class="form-control">
            <option value="">Choose Service Type</option>
            @foreach($serviceTypes as $type)
                <option value="{{ $type->service_type }}">{{ $type->service_type }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-success">Assign Service</button>
</form>
