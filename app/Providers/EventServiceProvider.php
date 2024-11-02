<?php
// backend-laravel-server/app/Providers/EventServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\FundRequestApproved;
use App\Listeners\CreateTransactionForApprovedFundRequest;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        FundRequestApproved::class => [
            CreateTransactionForApprovedFundRequest::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}