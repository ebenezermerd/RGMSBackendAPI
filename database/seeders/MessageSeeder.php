<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message; // Make sure to import your Message model

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample messages for researcher with user_id 6 from coe@gmail.com
        $messages = [
            [
            'sender_name' => 'Coe User',
            'receiver_email' => 'coe@gmail.com',
            'message_subject' => 'Research Proposal Feedback',
            'message_content' => 'Thank you for your submission. We have reviewed your proposal and have some feedback to share.',
            'message_date' => now(),
            'user_id' => 6,
            'profile_image' => null,
            ],
     
        ];

        // Insert the messages into the database
        foreach ($messages as $message) {
            Message::create($message);
        }
    }
}
