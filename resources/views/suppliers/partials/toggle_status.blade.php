
@if(checkAllowedModule('suppliers', 'suppliers.toggleStatus')->isNotEmpty())
<div class="form-check form-switch">
    <input class="form-check-input status-toggle" type="checkbox"
        id="flexSwitchCheckChecked{{ encode_id($supplier->id) }}" data-id="{{ encode_id($supplier->id) }}"
        {{ $supplier->is_active ? 'checked' : '' }}>
    <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($supplier->id) }}">
        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
    </label>
</div>
@endif 