<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">Chat</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            Manage your profile and account settings
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div
        class="flex h-[550px] text-sm border rounded-xl shadow overflow-hidden
                bg-white border-zinc-200
                dark:bg-zinc-800 dark:border-zinc-700">

        <!-- Left: User List -->
        <div class="w-1/4 border-r bg-zinc-50 border-zinc-200
                    dark:bg-zinc-800 dark:border-zinc-700">
            <div
                class="p-4 font-bold text-zinc-700 border-b border-zinc-200
                        dark:text-zinc-200 dark:border-zinc-700">
                Users
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($users as $item)
                    <div wire:click="selectUser({{ $item->id }})"
                        class="p-3 cursor-pointer hover:bg-blue-100 dark:hover:bg-zinc-700 transition
                               {{ $item->id === $selectedUser->id ? 'bg-blue-200 dark:bg-zinc-700' : '' }}">
                        <div class="text-zinc-800 dark:text-zinc-100">{{ $item->name }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->email }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Chat Section -->
        <div class="w-3/4 flex flex-col">

            <!-- Header -->
            <div
                class="p-4 border-b bg-zinc-50 border-zinc-200
                        dark:bg-zinc-800 dark:border-zinc-700">
                <div class="text-lg font-semibold text-zinc-800 dark:text-white">
                    {{ $selectedUser->name }}
                </div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $selectedUser->email }}
                </div>
            </div>

            <!-- Messages -->
            <div id="chat-box" class="flex-1 p-4 overflow-y-auto space-y-2 bg-zinc-50 dark:bg-zinc-800">
                @foreach ($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="max-w-xs px-4 py-2 rounded-2xl shadow
                                    {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                            {{ $message->message }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Typing Indicator -->
            <div id="typing-indicator" class="px-4 pb-1 text-xs text-zinc-500 italic min-h-[20px]">
            </div>

            <!-- Input -->
            <form wire:submit="submit"
                class="p-4 border-t bg-white flex items-center gap-2
                        border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                <input wire:model.live="newMessage" wire:keydown="userTyping" type="text"
                    class="flex-1 border rounded-full px-4 py-2 text-sm
           border-zinc-300 bg-white text-zinc-900
           focus:outline-none focus:ring focus:ring-blue-300
           dark:border-zinc-600 dark:bg-zinc-700
           dark:text-white dark:placeholder-zinc-400"
                    placeholder="Type your message..." />
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function scrollToBottom() {
        const box = document.getElementById('chat-box');
        if (box) box.scrollTop = box.scrollHeight;
    }

    let typingTimeout;

    document.addEventListener('livewire:initialized', () => {
        scrollToBottom();

        Livewire.on('scroll-to-bottom', () => {
            setTimeout(scrollToBottom, 50);
        });

        // ✅ Show typing indicator when event received
        Livewire.on('showTyping', (event) => {
            const indicator = document.getElementById('typing-indicator');
            indicator.innerText = `${event.userName} is typing...`;

            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                indicator.innerText = '';
            }, 2000);
        });
    });
</script>
