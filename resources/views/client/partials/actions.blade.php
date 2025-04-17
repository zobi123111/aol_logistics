@if(checkAllowedModule('client', 'client.edit')->isNotEmpty() || checkAllowedModule('client', 'client.show')->isNotEmpty()|| checkAllowedModule('client', 'client.destroy')->isNotEmpty())
<td>
@if(checkAllowedModule('client', 'client.edit')->isNotEmpty())

<a href="{{ route('client.edit', encode_id($client->id)) }}" class=""><i
        class="fa fa-edit edit-user-icon table_icon_style blue_icon_color"></i></a>

        @endif
        @if(checkAllowedModule('client', 'client.destroy')->isNotEmpty() )

                        <i class="fa-solid fa-trash delete-icon table_icon_style blue_icon_color"
    data-clientdata-id="{{ encode_id($client->id) }}"></i>
    @endif


    @if($client->country_code && $client->mobile_number)
                        <a href="{{ route('chat.here', ['number' => $client->country_code . $client->mobile_number, 'name' => $client->business_name ??  $client->email]) }}">
                            <i class="fa-solid fa-comments table_icon_style blue_icon_color"></i>
                        </a>
                    @endif
</td>
@endif