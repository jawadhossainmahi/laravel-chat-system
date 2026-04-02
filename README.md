# Reverb Chat App

A real-time private chat application built with **Laravel 12**, **Livewire 3**, and **Laravel Reverb**. Users can chat privately with each other, see messages update instantly on both sides, and see live typing indicators.

---

## Features

- Real-time private messaging via WebSockets
- Live typing indicator using Reverb broadcasting
- Auto-scroll to latest message
- Private channel authentication per user
- Built with Livewire 3 and Flux UI components
- Dark mode support

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 |
| Frontend reactivity | Livewire 3 |
| WebSocket server | Laravel Reverb |
| UI components | Flux (livewire/flux) |
| Styling | Tailwind CSS |
| JS WebSocket client | Laravel Echo + Pusher JS |

---

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL
- Laravel Reverb (included)

---

## Installation

### 1. Clone the repository
```bash
git clone https://github.com/your-username/reverb-chat-app.git
cd reverb-chat-app
```

### 2. Install dependencies
```bash
composer install
npm install
```

### 3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials and Reverb config:
```env
DB_DATABASE=chat-app
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

Generate Reverb credentials if you haven't already:
```bash
php artisan reverb:install
```

### 4. Run migrations
```bash
php artisan migrate
```

### 5. Build assets
```bash
npm run build
```

---

## Running the App

You need **three terminals** running simultaneously:
```bash
# Terminal 1 — Laravel dev server
php artisan serve

# Terminal 2 — Reverb WebSocket server
php artisan reverb:start

# Terminal 3 — Vite asset bundler
npm run dev
```

Then visit [http://localhost:8000](http://localhost:8000).

---

## How It Works
```
User A types a message
       ↓
Livewire submit() → ChatMessage saved to DB
       ↓
MessageSent event broadcast via Reverb
       ↓
Broadcasts to private-chat.{receiverId} AND private-chat.{senderId}
       ↓
Both users' Livewire components receive the event
       ↓
Message appended to UI on both sides instantly
```

Typing indicator works similarly — `UserTyping` event is broadcast to the receiver's private channel on every keypress, and cleared after 2 seconds of inactivity.

---

## Project Structure
```
app/
├── Events/
│   ├── MessageSent.php       # Broadcasts new messages to both users
│   └── UserTyping.php        # Broadcasts typing state to receiver
├── Livewire/
│   └── Chat.php              # Main chat component
├── Models/
│   └── ChatMessage.php

resources/
├── js/
│   └── app.js                # Laravel Echo + Reverb config
└── views/
    └── livewire/
        └── chat.blade.php    # Chat UI with real-time script

routes/
├── channels.php              # Private channel auth
└── web.php                   # Broadcast::routes + app routes
```

---
