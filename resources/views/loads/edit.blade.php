@section('title', 'Loads')
{{-- @section('sub-title', 'Loads') --}}
@section('sub-title', GoogleTranslate::trans('Load', app()->getLocale()))
@extends('layout.app')
@section('content')

<div class="main_cont_outer">

    <div class="create_btn">
        <a href="{{ route('loads.index') }}" class="btn btn-primary create-button btn_primary_color" id="createUser">
            <i class="bi bi-arrow-left-circle-fill"></i> {{ GoogleTranslate::trans('Back', app()->getLocale()) }}
        </a>
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

    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('loads.update', $load->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-3 mt-3">
                    <label for="service_type" class="form-label"> {{ GoogleTranslate::trans('Service Type', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <select name="service_type" id="service_type" class="form-control">
                        <option value="">Select Service Type</option>
                        <option value="Express" {{ old('service_type', $load->service_type) == 'Express' ? 'selected' : '' }}>Express</option>
                        <option value="Standard" {{ old('service_type', $load->service_type) == 'Standard' ? 'selected' : '' }}>Standard</option>
                        <option value="Overnight" {{ old('service_type', $load->service_type) == 'Overnight' ? 'selected' : '' }}>Overnight</option>
                    </select>
                    @error('service_type')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>
                
                <div class="form-group form-group mb-3 mt-3">
                    <label for="supplier_id" class="form-label"> {{ GoogleTranslate::trans('Supplier', app()->getLocale()) }} </label>
                    <select name="supplier_id" id="supplier_id" class="form-control select2">
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ old('supplier_id', $load->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="origin" class="form-label"> {{ GoogleTranslate::trans('Origin', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <select name="origin" id="origin" class="form-control">
                        <option value="">Select Origin</option>
                        @foreach($origins as $origin)
                            <option value="{{ $origin->id }}" {{ old('origin', $load->origindata->id) == $origin->id ? 'selected' : '' }}>
                                {{ $origin->street }}, {{ $origin->city }}, {{ $origin->state }}, {{ $origin->country }}
                            </option>
                        @endforeach
                        </select>
                    </select>
                    @error('origin')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="destination" class="form-label"> {{ GoogleTranslate::trans('Destination', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <select name="destination" id="destination" class="form-control">
                        <option value="">Select Destination</option>
                        @foreach($destinations as $destination)
                            <option value="{{ $destination->id }}" {{ old('destination', $load->destinationdata->id) == $destination->id ? 'selected' : '' }}>
                                {{ $destination->street }}, {{ $destination->city }}, {{ $destination->state }}, {{ $destination->country }}
                            </option>
                        @endforeach
                    </select>
                    @error('destination')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="payer" class="form-label"> {{ GoogleTranslate::trans('Who Pays Load', app()->getLocale()) }}  <span class="text-danger">*</span></label>
                    <select name="payer" id="payer" class="form-control">
                        <option value="">Select Payer</option>
                        <option value="client" {{ $load->payer == 'client' ? 'selected' : '' }}>Client directly</option>
                        <option value="another_party" {{ $load->payer == 'another_party' ? 'selected' : '' }}>Another party will pay for the load</option>
                    </select>
                    @error('payer')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="equipment_type" class="form-label"> {{ GoogleTranslate::trans('Equipment Type', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <select name="equipment_type" id="equipment_type" class="form-control">
                        <option value="">Select Equipment</option>
                        <option value="53' Truck" {{ $load->equipment_type == "53' Truck" ? 'selected' : '' }}>53' Truck</option>
                        <option value="48' Truck" {{ $load->equipment_type == "48' Truck" ? 'selected' : '' }}>48' Truck</option>
                        <option value="48' Flatbed" {{ $load->equipment_type == "48' Flatbed" ? 'selected' : '' }}>48' Flatbed</option>
                        <option value="53' R" {{ $load->equipment_type == "53' R" ? 'selected' : '' }}>53' R</option>
                    </select>
                    @error('equipment_type')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="weight" class="form-label"> {{ GoogleTranslate::trans('Weight', app()->getLocale()) }} </label>
                    <input type="text" name="weight" id="weight" class="form-control" value="{{ $load->weight }}">
                    @error('weight')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="delivery_deadline" class="form-label"> {{ GoogleTranslate::trans('Delivery Deadline', app()->getLocale()) }} <span class="text-danger">*</span></label>
                    <input type="date" id="delivery_deadline" name="delivery_deadline" class="form-control" value="{{ $load->delivery_deadline ? $load->delivery_deadline->format('Y-m-d') : '' }}">
                    @error('delivery_deadline')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="customer_po" class="form-label"> {{ GoogleTranslate::trans('Customer PO / Reference Number', app()->getLocale()) }} </label>
                    <input type="text" id="customer_po" name="customer_po" class="form-control" value="{{ $load->customer_po }}">
                    @error('customer_po')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_hazmat" id="is_hazmat" class="form-check-input" value="1" {{ $load->is_hazmat ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_hazmat"> {{ GoogleTranslate::trans('HazMat (Hazardous)', app()->getLocale()) }} </label>
                    @error('is_hazmat')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="is_inbond" id="is_inbond" class="form-check-input" value="1" {{ $load->is_inbond ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_inbond"> {{ GoogleTranslate::trans('Inbond Load', app()->getLocale()) }} </label>
                    @error('is_inbond')
                        <div class="text-danger">
                            {{ GoogleTranslate::trans($message, app()->getLocale()) }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn_primary_color"> {{ GoogleTranslate::trans('Update Load', app()->getLocale()) }} </button>
            </form>
        </div>
    </div>

    <div class="form-group mb-3">
            <label for="trailer_number" class="form-label">Trailer Number</label>
            <input type="text" id="trailer_number" name="trailer_number" class="form-control" value="{{ old('trailer_number', $load->trailer_number) }}">
            @error('trailer_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>
        <div class="form-group mb-3">
            <label for="port_of_entry" class="form-label">Port of Entry</label>
            <input type="text" id="port_of_entry" name="port_of_entry" class="form-control" value="{{ old('port_of_entry', $load->port_of_entry) }}">
            @error('port_of_entry')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
        </div>

    <div class="form-group mb-3">
        <label for="weight" class="form-label">Weight</label>
        <input type="text" name="weight" id="weight" class="form-control" value="{{ $load->weight }}">
        @error('weight')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="delivery_deadline" class="form-label">Delivery Deadline<span class="text-danger">*</span></label>
        <input type="date" id="delivery_deadline" name="delivery_deadline" class="form-control" value="{{ $load->delivery_deadline ? $load->delivery_deadline->format('Y-m-d') : '' }}">
        @error('delivery_deadline')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group mb-3">
        <label for="customer_po" class="form-label">Customer PO / Reference Number</label>
        <input type="text" id="customer_po" name="customer_po" class="form-control" value="{{ $load->customer_po }}">
        @error('customer_po')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_hazmat" id="is_hazmat" class="form-check-input" value="1" {{ $load->is_hazmat ? 'checked' : '' }}>
        <label class="form-check-label" for="is_hazmat">HazMat (Hazardous)</label>
        @error('is_hazmat')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input type="checkbox" name="is_inbond" id="is_inbond" class="form-check-input" value="1" {{ $load->is_inbond ? 'checked' : '' }}>
        <label class="form-check-label" for="is_inbond">Inbond Load</label>
        @error('is_inbond')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary btn_primary_color">Update Load</button>
</form>

</div>
</div>
</div>

@endsection

@section('js_scripts')

<script>

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select Supplier",
            allowClear: true
        });
    });

</script>

@endsection