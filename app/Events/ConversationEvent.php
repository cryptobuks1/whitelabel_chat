<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ConversationEvent
 * @package App\Events
 */
class ConversationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Conversation
     */
    public $conversation;
    /**
     * @var
     */
    public $receiverId;

    /**
     * Create a new event instance.
     *
     * @param  Conversation  $conversation
     * @param $receiverId
     */
    public function __construct(Conversation $conversation, $receiverId)
    {
        $this->conversation = $conversation;
        $this->receiverId = $receiverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|Channel[]|PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('conversation.'.$this->receiverId);
    }

    /**
     * @return array
     */
    public function broadcastWith()
    {
        return $this->conversation->toArray();
    }
}
