<?php

namespace App\Events;

use App\Models\WhatsappMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class WhatsappMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(WhatsappMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->message->lead_id);
    }

    public function broadcastWith()
    {
        return [
            'text' => $this->message->message,
            'from' => $this->message->direction === 'outgoing' ? 'me' : 'them',
            'time' => $this->message->created_at->format('h:i A'),
        ];
    }

    public function broadcastAs()
    {
        return 'WhatsappMessageReceived';
    }
}
