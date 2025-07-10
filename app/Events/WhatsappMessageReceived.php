<?php

namespace App\Events;

use App\Events\WhatsappMessageReceived;
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

    /**
     * Create a new event instance.
     */
    public function __construct(WhatsappMessage $message)
    {
        $this->message = $message;
    }

    /**
     * The name of the channel on which the event is broadcast.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->message->lead_id);
    }

    /**
     * The data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'text' => $this->message->message,
            'from' => $this->message->direction === 'outgoing' ? 'me' : 'them',
            'time' => $this->message->created_at->format('h:i A'),
        ];
    }

    /**
     * Optional: Name the event on frontend
     */
    public function broadcastAs()
    {
        return 'WhatsappMessageReceived';
    }

    public function send(Request $request)
    {
        // validation...

        $message = WhatsappMessage::create([
            'user_id' => auth()->id(),
            'lead_id' => $lead->id,
            'phone' => $request->recipients[0],
            'direction' => 'outgoing',
            'message' => $request->message,
            'status' => 'sent',
        ]);

        // Dispatch the event after saving
        broadcast(new WhatsappMessageReceived($message))->toOthers();

        return response()->json(['success' => true]);
    }
    
}
