<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Call;

class CallSeeder extends Seeder
{
    public function run()
    {
        Call::create([
            'title' => 'CALL FOR PROPOSALS',
            'subtitle' => 'Submit your innovative research proposals and get funded to bring your ideas to life.',
            'whyApplyTitle' => 'Why Apply?',
            'whyApplyContent' => 'By submitting your proposal, you unlock opportunities to gain essential funding and collaborate with leading professionals in your field.',
            'bulletPoints' => [
                'Secure financial support for groundbreaking research',
                'Network and collaborate with industry experts and peers',
                'Enhance your academic and professional profile by publishing your findings',
            ],
            'buttonText' => 'Apply Now',
            'isActive' => true,
            'startDate' => now(),
            'endDate' => now()->addMonth(),
            'proposalType' => 'research',
            'isResubmissionAllowed' => true, // Set this flag as needed
        ]);
    }
}