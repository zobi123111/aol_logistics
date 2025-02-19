<div class="form-check form-switch">
    <input class="form-check-input status-toggle" type="checkbox"
        id="flexSwitchCheckChecked{{ encode_id($user->id) }}" data-id="{{ encode_id($user->id) }}"
        {{ $user->is_active ? 'checked' : '' }}>
    <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($user->id) }}">
        {{ $user->is_active ? 'Active' : 'Inactive' }}
    </label>
</div>