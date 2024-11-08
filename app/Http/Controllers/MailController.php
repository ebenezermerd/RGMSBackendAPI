<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class MailController extends Controller
{
    public function index()
    {
        Mail::to('homelandcarry2@gmail.com')->send(new TestMail([
            'title' => 'The Title',
            'body' => 'The Body',
        ]));
    }
}