<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;
use App\Models\User;

class ProjectsSynced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user){}

    /**
     * TODO: implement Pusher|Echo
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'projects.synced';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('seller-'.$this->user->id),
        ];
    }
}
