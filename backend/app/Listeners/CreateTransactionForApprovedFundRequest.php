<?php
// backend-laravel-server/app/Listeners/CreateTransactionForApprovedFundRequest.php

namespace App\Listeners;

use App\Events\FundRequestApproved;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTransactionForApprovedFundRequest
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\FundRequestApproved  $event
     * @return void
     */
    public function handle(FundRequestApproved $event)
    {
        $fundRequest = $event->fundRequest;

        // Create a new transaction
        Transaction::create([
            'transaction_date' => now(),
            'transaction_amount' => $fundRequest->request_amount,
            'transaction_type' => 'fund_request_approval',
            'transaction_description' => 'Transaction for approved fund request',
            'fund_request_id' => $fundRequest->id,
            'user_id' => $fundRequest->user_id,
        ]);

        // Optionally, you can broadcast the transaction creation event here
        event(new \App\Events\FundRequestApproved(Transaction::latest()->first()));
    }
}