<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;

class Chat extends Component
{
    public $users = [];
    public $selectedUser = null;
    public $messages = [];
    public $newMessage = '';
    public $page = 1;
    public $perPage = 15;
    public $name;

    public function mount($number = null, $name = null)
    {
        $this->name = $name;
        $this->users = Message::select('user_number')->groupBy('user_number')->pluck('user_number');

        if ($number) {
            $this->selectUser($number);
        }
    }

    public function selectUser($number)
    {
        $this->selectedUser = $number;
        $this->page = 1; // reset pagination when selecting new user
        $this->loadMessages();
    }

    public function loadMore()
    {
        $this->page++;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if (!$this->selectedUser) return;

        $this->messages = Message::where('user_number', $this->selectedUser)
            ->latest()
            ->take($this->perPage * $this->page)
            ->get()
            ->reverse()
            ->values(); // oldest to newest
    }

    public function sendMessage()
    {
        if (!$this->newMessage || !$this->selectedUser) return;

        $whatsapp = new WhatsAppService();
        $whatsapp->sendMessage($this->selectedUser, $this->newMessage);

        Message::create([
            'user_number' => $this->selectedUser,
            'message' => $this->newMessage,
            'direction' => 'outgoing',
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function fetchMessages()
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}