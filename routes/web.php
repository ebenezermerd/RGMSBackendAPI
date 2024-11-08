<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ReviewRequestController;

Route::get('/send-mail', [MailController::class, 'index']);
Route::get('/', function () {
    return view('welcome');
});


Route::get('/review-request/{id}/response', [ReviewRequestController::class, 'showResponsePage']);
Route::post('/review-request/{id}/response', [ReviewRequestController::class, 'handleResponse']);


