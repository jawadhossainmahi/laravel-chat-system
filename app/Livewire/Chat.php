<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;


#[Layout('components.layouts.app')]
class Chat extends Component
{

    public $users;
    public $selectedUser;

    public $newMessage;
    public $messages;
    public function mount()
    {
        $this->users = User::where('id', '!=', Auth::id())->get();
        $this->selectedUser = $this->users->first();
        $this->messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                ->where('receiver_id', Auth::id());
        })->oldest()->get();
    }

    public function selectUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                ->where('receiver_id', Auth::id());
        })->oldest()->get();
    }

    public function submit()
    {
        if (!trim($this->newMessage)) {
            return;
        }

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => trim($this->newMessage),
        ]);

        $this->messages->push($message);

        $this->newMessage = '';
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
