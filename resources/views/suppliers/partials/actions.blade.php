@if(checkAllowedModule('suppliers', 'suppliers.edit')->isNotEmpty() || checkAllowedModule('suppliers', 'suppliers.show')->isNotEmpty() || checkAllowedModule('suppliers', 'suppliers.destroy')->isNotEmpty())
    @if(checkAllowedModule('suppliers', 'suppliers.edit')->isNotEmpty())
        <a href="{{ route('suppliers.edit', encode_id($supplier->id)) }}"><i class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
    @endif
    @if(checkAllowedModule('suppliers', 'suppliers.destroy')->isNotEmpty())
        <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color" data-supplier-id="{{ encode_id($supplier->id) }}"></i>
    @endif
    @if(checkAllowedModule('suppliers', 'suppliers.show')->isNotEmpty())
        <a href="{{ route('suppliers.show', encode_id($supplier->id)) }}"><i class="fa-solid fa-eye view-icon table_icon_style blue_icon_color"></i></a>
    @endif
@endif