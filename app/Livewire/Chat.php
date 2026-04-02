<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Events\UserTyping;
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
    public $userId;

    public function mount()
    {
        $this->userId = Auth::id();
        $this->users = User::where('id', '!=', $this->userId)->get();
        $this->selectedUser = $this->users->first();
        $this->loadMessages();
    }

    private function loadMessages()
    {
        $this->messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', $this->userId)
                ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                ->where('receiver_id', $this->userId);
        })->oldest()->get();
    }

    public function selectUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->loadMessages();
        $this->dispatch('scroll-to-bottom');
    }

    public function submit()
    {
        if (!trim($this->newMessage)) return;

        $message = ChatMessage::create([
            'sender_id' => $this->userId,
            'receiver_id' => $this->selectedUser->id,
            'message' => trim($this->newMessage),
        ]);

        $this->newMessage = '';
        broadcast(new MessageSent($message));
        $this->dispatch('scroll-to-bottom');
    }

    public function userTyping()
    {
        broadcast(new UserTyping(
            $this->userId,
            Auth::user()->name,
            $this->selectedUser->id
        ));
    }

    // ✅ updatedNewMessage() removed — was conflicting with userTyping()

    public function getListeners()
    {
        return [
            "echo-private:chat.{$this->userId},MessageSent" => 'newChatMessageNotification',
            "echo-private:chat.{$this->userId},UserTyping" => 'userIsTyping', // ✅ Added
        ];
    }

    public function newChatMessageNotification($event)
    {
        $isCurrentConversation =
            ($event['sender_id'] == $this->selectedUser->id && $event['receiver_id'] == $this->userId) ||
            ($event['sender_id'] == $this->userId && $event['receiver_id'] == $this->selectedUser->id);

        if ($isCurrentConversation) {
            $message = ChatMessage::find($event['id']);
            if ($message && !$this->messages->contains('id', $message->id)) {
                $this->messages->push($message);
                $this->dispatch('scroll-to-bottom');
            }
        }
    }

    // ✅ Added — handles incoming typing event
    public function userIsTyping($event)
    {
        $this->dispatch('showTyping', userName: $event['user_name']);
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
