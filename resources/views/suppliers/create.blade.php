@section('title', 'Supplier')
{{-- @section('sub-title', 'Supplier') --}}
@section('sub-title', __('messages.Supplier'))

@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
        <a href="{{ route('suppliers.index') }}" class="btn btn-primary create-button btn_primary_color"
            id="createUser"><i class="bi bi-arrow-left-circle-fill"></i> {{ __('messages.Back') }} </a>
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

    <div class="card card-container">
        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Company Name -->
                <div class="mb-3 mt-3">
                    <label class="form-label"> {{ __('messages.Full and Legal Name of the Company') }} </label><span class="text-danger">*</span>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}">
                    @error('company_name')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- DBA -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.DBA (Nickname)') }} </label><span class="text-danger">*</span>
                    <input type="text" name="dba" class="form-control" value="{{ old('dba') }}">
                    @error('dba')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Street Address') }} </label>
                    <input type="text" name="street_address" class="form-control" value="{{ old('street_address') }}">
                    @error('street_address')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"> {{ __('messages.City') }} </label><span class="text-danger">*</span>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                        @error('city')
                            <div class="text-danger">
                               {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"> {{ __('messages.State') }} </label><span class="text-danger">*</span>
                        <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                        @error('state')
                            <div class="text-danger">
                               {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"> {{ __('messages.Zip Code') }} </label><span class="text-danger">*</span>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}">
                        @error('zip_code')
                            <div class="text-danger">
                               {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Country') }} </label><span class="text-danger">*</span>
                    <select name="country" class="form-select">
                        <option value="USA" {{ old('country') == 'USA' ? 'selected' : '' }}>United States</option>
                        <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                        <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                        <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('country')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Office Phone -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Office Phone Number') }} </label>
                    <input type="text" name="office_phone" class="form-control" value="{{ old('office_phone') }}">
                    @error('office_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Primary Contact -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Primary Contact Email') }} </label><span class="text-danger">*</span>
                    <input type="email" name="primary_contact_email" class="form-control"
                        value="{{ old('primary_contact_email') }}">
                    @error('primary_contact_email')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Primary Contact Office Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="primary_contact_office_phone" class="form-control"
                        value="{{ old('primary_contact_office_phone') }}">
                    @error('primary_contact_office_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Primary Contact Mobile Phone') }} </label><span class="text-danger">*</span>
                    <input type="text" name="primary_contact_mobile_phone" class="form-control"
                        value="{{ old('primary_contact_mobile_phone') }}">
                    @error('primary_contact_mobile_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- User Role Selection -->
                <!-- <div class="mb-3">
                    <label class="form-label">User Role</label>
                    <select name="user_role" class="form-select">
                        <option value="master_client" {{ old('user_role') == 'master_client' ? 'selected' : '' }}>Master
                            Client</option>
                        <option value="customer_service_executive"
                            {{ old('user_role') == 'customer_service_executive' ? 'selected' : '' }}>Customer Service
                            Executive</option>
                        <option value="accounting_user" {{ old('user_role') == 'accounting_user' ? 'selected' : '' }}>
                            Accounting User</option>
                    </select>
                    @error('user_role')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- User Email -->
                <!-- <div class="mb-3">
                    <label class="form-label"> {{ __('messages.User Email') }}  <small> {{ __('messages.(Used for login)') }} </small><span class="text-danger">*</span></label>
                    <input type="email" name="user_email" class="form-control" value="{{ old('user_email') }}">
                    @error('user_email')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- User Office Phone -->
                <!-- <div class="mb-3">
                    <label class="form-label"> {{ __('messages.User Office Phone Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="user_office_phone" class="form-control"
                        value="{{ old('user_office_phone') }}">
                    @error('user_office_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- User Mobile Phone -->
                <!-- <div class="mb-3">
                    <label class="form-label"> {{ __('messages.User Mobile Phone Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="user_mobile_phone" class="form-control"
                        value="{{ old('user_mobile_phone') }}">
                    @error('user_mobile_phone')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- User Password -->
                <!-- <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Password') }} </label><span class="text-danger">*</span>
                    <input type="password" name="password" class="form-control" value="{{ old('password') }}">
                    @error('password')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- Confirm Password -->
                <!-- <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Confirm Password') }} </label><span class="text-danger">*</span>
                    <input type="password" name="password_confirmation" class="form-control"
                        value="{{ old('password_confirmation') }}">
                    @error('password_confirmation')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div> -->

                <!-- Service Type (Select) -->
                <div class="mb-3">
                    <label class="form-label" for="service_type"> {{ __('messages.Type of Service Authorized') }} </label><span class="text-danger">*</span>
                    <select name="service_type[]" class="form-select" id="service_type" multiple>
                        @php
                            $oldServices = old('service_type' ?? []);
                            $selectedServices = is_array($oldServices) ? $oldServices : explode(',', $oldServices);
                        @endphp
                        <option value="Land Freight" {{ in_array('Land Freight', $selectedServices) ? 'selected' : '' }}>
                            {{ __('messages.Land Freight') }}
                        </option>
                        <option value="Air Freight" {{ in_array('Air Freight', $selectedServices) ? 'selected' : '' }}>
                            {{ __('messages.Air Freight') }}
                        </option>
                        <option value="Ocean Freight" {{ in_array('Ocean Freight', $selectedServices) ? 'selected' : '' }}>
                            {{ __('messages.Ocean Freight') }}
                        </option>
                    </select>


                    @error('service_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </div>

                <!-- Currency Selection (Radio) -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Will the supplier invoice in') }} </label><span class="text-danger">*</span>
                    <div>
                        <input type="radio" name="currency" value="USD" id="usd"
                            {{ old('currency') == 'USD' ? 'checked' : '' }}> <label for="usd">USD</label>
                        <input type="radio" name="currency" value="MXP" id="mxp"
                            {{ old('currency') == 'MXP' ? 'checked' : '' }}> <label for="mxp">MXP</label>
                        <input type="radio" name="currency" value="Both" id="both"
                            {{ old('currency') == 'Both' ? 'checked' : '' }}> <label for="both">Both</label>
                    </div>
                    @error('currency')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Preferred Language (Select) -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Preferred Language') }} </label><span class="text-danger">*</span>
                    <select name="preferred_language" class="form-select">
                        <option value="English" {{ old('preferred_language') == 'English' ? 'selected' : '' }}>English
                        </option>
                        <option value="Spanish" {{ old('preferred_language') == 'Spanish' ? 'selected' : '' }}>Spanish
                        </option>
                    </select>
                    @error('preferred_language')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- File Uploads -->
                 <div class="upload-design">
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.Upload Documents') }} </label>
                    <input type="file" name="document_path[]" class="form-control" multiple>
                    <small class="text-muted"> {{ __('messages.You can upload multiple documents') }} </small>
                    <!-- Loop to show any validation errors for the document_path array -->
                    @error('document_path')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('document_path.*') as $message)
                        <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>
                </div>

                <div class="upload-design">
                <!-- SCAC Number and File Upload -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.SCAC Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="scac_number" class="form-control" value="{{ old('scac_number') }}">
                    @error('scac_number')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.SCAC Legal Documents') }} </label>
                    <input type="file" name="scac_documents[]" class="form-control" multiple>
                    <small class="text-muted"> {{ __('messages.You can upload multiple legal documents') }} </small>
                    @error('scac_documents')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('scac_documents.*') as $message)
                        <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>
                </div>

                <div class="upload-design">
                <!-- CAAT Number and File Upload -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.CAAT Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="caat_number" class="form-control" value="{{ old('caat_number') }}">
                    @error('caat_number')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.CAAT Legal Documents') }} </label>
                    <input type="file" name="caat_documents[]" class="form-control" multiple>
                    <small class="text-muted"> {{ __('messages.You can upload multiple legal documents') }} </small>
                    @error('caat_documents')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('caat_documents.*') as $message)
                        <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>
                </div>
                <div class="upload-design">
                <!-- CAAT Number and File Upload -->
                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.CTPAT Number') }} </label><span class="text-danger">*</span>
                    <input type="text" name="ctpat_number" class="form-control" value="{{ old('ctpat_number') }}">
                    @error('ctpat_number')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"> {{ __('messages.CTPAT Legal Documents') }} </label>
                    <input type="file" name="ctpat_documents[]" class="form-control" multiple>
                    <small class="text-muted"> {{ __('messages.You can upload multiple legal documents') }} </small>
                    @error('ctpat_documents')
                        <div class="text-danger">
                           {{ $message }}
                        </div>
                    @enderror

                    <!-- Loop to show individual errors for each document -->
                    @foreach ($errors->get('ctpat_documents.*') as $message)
                        <div class="text-danger">{{ $message[0] }}</div>
                    @endforeach
                </div>
                </div>
                @if($errors->has('error'))
                <div class="error-message">{{ $errors->first('error') }}</div>
                @endif

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn_primary_color"> {{ __('messages.Save Supplier') }} </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js_scripts')

<script>
       $('#service_type').select2({ width: '100%' });

</script>

@endsection