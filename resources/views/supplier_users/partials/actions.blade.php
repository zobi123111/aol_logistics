<a href="{{ route('supplier_users.edit', [encode_id($supplier->id), encode_id($user->id)]) }}" class=""><i
    class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>
<i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
data-supplier-id="{{ encode_id($supplier->id) }}" data-user-id="{{ encode_id($user->id) }}"></i>