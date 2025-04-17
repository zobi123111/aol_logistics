
  
<div class="chat-wrapper d-flex flex-column">
    <div class="chat-header">
    Chat with {{ $name }}

    </div>

    <div class="chat-body d-flex flex-column" id="chatBody" wire:poll.5s="fetchMessages" >
        @if($messages->count() >= $perPage * $page)
            <button wire:click="loadMore" class="btn btn-sm btn-secondary mb-2 align-self-center">
                Load More
            </button>
        @endif

        @foreach ($messages as $msg)
            <div class="{{ $msg->direction == 'incoming' ? 'chat-message chat-left' : 'chat-message chat-right' }}">
                <span class="bg-{{ $msg->direction == 'incoming' ? 'gray' : 'green' }}-100 px-3 py-2 rounded my-1 inline-block">
                    {{ $msg->message }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="chat-footer">
        <form wire:submit.prevent="sendMessage">
            <input wire:model="newMessage" class="form-control" placeholder="Type your message...">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
