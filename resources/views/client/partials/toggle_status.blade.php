@if(checkAllowedModule('client', 'client.toggleStatus')->isNotEmpty() )
<td>
    <div class="form-check form-switch">
        <input class="form-check-input status-toggle" type="checkbox"
            id="flexSwitchCheckChecked{{ encode_id($client->id) }}" data-id="{{ encode_id($client->id) }}"
            {{ $client->is_active ? 'checked' : '' }}>
        <label class="form-check-label" for="flexSwitchCheckChecked{{ encode_id($client->id) }}">
            {{ $client->is_active ? 'Active' : 'Inactive' }}
        </label>
    </div>
</td>   
@endif   