
@section('title', 'Client Customer Service Executve')
{{-- @section('sub-title', 'Client Customer Service Executve') --}}
@section('sub-title', __('messages.Client Customer Service Executive'))
@extends('layout.app')
@section('content')
<div class="main_cont_outer">
    <div class="create_btn">
    <a href="{{ route('client.index') }}" class="btn btn-primary create-button btn_primary_color"
    id="createClient"><i class="bi bi-arrow-left-circle-fill"></i> 
    <!-- {{ __('messages.Back') }} -->
    {{ __('messages.Back') ?: __('messages.Back', [], 'en') }}

 </a>
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

    <div class="chat-box mt-4">
    @livewire('chat', ['number' => $number, 'name' => $name])
    </div>

</div>
@endsection
@section('js_scripts')
<script>
    function scrollToBottom() {
                    chatBody.scrollTo({
            top: chatBody.scrollHeight,
            behavior: 'smooth'
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        const chatBody = document.getElementById('chatBody');
        scrollToBottom();
    });

    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', () => {
            // setTimeout(() => {
                const chatBody = document.getElementById('chatBody');
                if (chatBody) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            // }, 50); // slight delay
        });
    });
    
</script>

@livewireScripts
@endsection