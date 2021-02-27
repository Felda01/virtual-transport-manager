<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshMessageQuery implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userFrom;
    public $userTo;

    /**
     * Create a new event instance.
     *
     * @param string $userFrom
     * @param string $userTo
     */
    public function __construct(string $userFrom, string $userTo)
    {
        $this->userFrom = $userFrom;
        $this->userTo = $userTo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('message-' . $this->userTo);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'modelType' => 'Message',
            'userFrom' => $this->userFrom
        ];
    }
}
