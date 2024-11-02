<?php
// backend-laravel-server/app/Events/FundRequestApproved.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\FundRequest;

class FundRequestApproved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fundRequest;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FundRequest $fundRequest)
    {
        $this->fundRequest = $fundRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('fund-request');
    }

    public function broadcastWith()
    {
        return ['fundRequest' => $this->fundRequest];
    }
}