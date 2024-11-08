<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewRequestController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TestMail;
use App\Http\Controllers\MailController;

Route::get('/send-mail', [MailController::class, 'index']);
Route::get('/', function () {
    return view('welcome');
});


Route::get('/review-request/{id}/response', [ReviewRequestController::class, 'showResponsePage']);
Route::post('/review-request/{id}/response', [ReviewRequestController::class, 'handleResponse']);


