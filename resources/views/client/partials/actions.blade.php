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
                        <a href="#">
                            <!-- <i class="fa-brands fa-square-whatsapp"></i> -->
                              <img src="/assets/img/whatsapp.png" alt="Twitter">
                        </a>
                    @endif
</td>
@endif