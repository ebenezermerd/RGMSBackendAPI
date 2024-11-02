<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Proposal;
use App\Models\Phase;
use App\Models\Activity;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure the 'admin' role exists
        $adminRole = Role::where('role_name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Please seed roles table first.');
            return;
        }
        // Ensure the 'reviewer' role exists
        $reviewerRole = Role::where('role_name', 'reviewer')->first();

        if (!$reviewerRole) {
            $this->command->error('Reviewer role not found. Please seed roles table first.');
            return;
        }

        // Ensure the 'COE' role exists
        $coeRole = Role::where('role_name', 'coe')->first();

        if (!$coeRole) {
            $this->command->error('COE role not found. Please seed roles table first.');
            return;
        }
        // Ensure the 'Auditor' role exists
        $auditorRole = Role::where('role_name', 'auditor')->first();

        if (!$auditorRole) {
            $this->command->error('Auditor role not found. Please seed roles table first.');
            return;
        }
        // Ensure the 'Directorate' role exists
        $directorateRole = Role::where('role_name', 'directorate')->first();

        if (!$directorateRole) {
            $this->command->error('Directorate role not found. Please seed roles table first.');
            return;
        }

        // Ensure the 'researcher' role exists
        $researcherRole = Role::where('role_name', 'researcher')->first();

        if (!$researcherRole) {
            $this->command->error('Researcher role not found. Please seed roles table first.');
            return;
        }

        // Create or update the reviewer user
        User::updateOrCreate(
            ['username' => 'reviewer'],
            [
                'first_name' => 'Reviewer',
                'last_name' => 'User',
                'email' => 'reviewer@gmail.com',
                'phone_number' => '0987654321',
                'password' => Hash::make('reviewer'), // Hashing the password for security
                'role_id' => $reviewerRole->id, // Assign the reviewer role
                'city' => 'Default City',
                'present_address' => '789 Reviewer Street',
                'permanent_address' => '101 Reviewer Address',
                'date_of_birth' => now()->subYears(25), // Default date of birth
                'bio' => 'Default reviewer user.',
                'profile_image' => null, // Default profile image if applicable
            ]
        );

        // Create or update the COE user
        User::updateOrCreate(
            ['username' => 'coe'],
            [
                'first_name' => 'COE',
                'last_name' => 'User',
                'email' => 'coe@gmail.com',
                'phone_number' => '1122334455',
                'password' => Hash::make('coe'), // Hashing the password for security
                'role_id' => $coeRole->id, // Assign the COE role
                'city' => 'Default City',
                'present_address' => '102 COE Street',
                'permanent_address' => '103 COE Address',
                'date_of_birth' => now()->subYears(35), // Default date of birth
                'bio' => 'Default COE user.',
                'profile_image' => null, // Default profile image if applicable
            ]
        );

        // Create or update the admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@gmail.com',
                'phone_number' => '1234567890',
                'password' => Hash::make('admin'), // Hashing the password for security
                'role_id' => $adminRole->id, // Assign the admin role
                'city' => 'Default City',
                'present_address' => '123 Admin Street',
                'permanent_address' => '456 Permanent Address',
                'date_of_birth' => now()->subYears(30), // Default date of birth
                'bio' => 'Default admin user.',
                'profile_image' => null, // Default profile image if applicable
            ]
        );

        // Create or update the auditor user
        User::updateOrCreate(
            ['username' => 'auditor'],
            [
                'first_name' => 'Auditor',
                'last_name' => 'User',
                'email' => 'auditor@gmail.com',
                'phone_number' => '1234567890',
                'password' => Hash::make('auditor'), // Hashing the password for security
                'role_id' => $auditorRole->id, // Assign the auditor role
                'city' => 'Default City',
                'present_address' => '123 Auditor Street',
                'permanent_address' => '456 Permanent Address',
                'date_of_birth' => now()->subYears(30), // Default date of birth
                'bio' => 'Default auditor user.',
                'profile_image' => null, // Default profile image if applicable
            ]
        );
        // Create or update the directorate user
        User::updateOrCreate(
            ['username' => 'directorate'],
            [
                'first_name' => 'Directorate',
                'last_name' => 'User',
                'email' => 'directorate@gmail.com',
                'phone_number' => '1234567890',
                'password' => Hash::make('directorate'), // Hashing the password for security
                'role_id' => $directorateRole->id, // Assign the directorate role
                'city' => 'Default City',
                'present_address' => '123 Directorate Street',
                'permanent_address' => '456 Permanent Address',
                'date_of_birth' => now()->subYears(30), // Default date of birth
                'bio' => 'Default directorate user.',
                'profile_image' => null, // Default profile image if applicable
            ]
        );

        // Create or update the researcher user
        User::updateOrCreate(
            ['username' => 'researcher'],
            [
                'first_name' => 'Researcher',
                'last_name' => 'User',
                'email' => 'researcher@gmail.com',
                'phone_number' => '2233445566',
                'password' => Hash::make('researcher'), // Hashing the password for security
                'role_id' => $researcherRole->id, // Assign the researcher role
                'city' => 'Default City',
                'present_address' => '104 Researcher Street',
                'permanent_address' => '105 Researcher Address',
                'date_of_birth' => now()->subYears(28), // Default date of birth
                'bio' => 'Default researcher user.',
                'profile_image' => null, // Default profile image if applicable
            ]
        );

        $this->command->info('Default users created or updated successfully.');
    
    
    //  // Create the proposal
    //  $proposal = Proposal::create([
    //     'id' => 12,
    //     'COE' => 'artificial-intelligence-and-robotics',
    //     'proposal_title' => 'Development of AI-Driven Diagnostic Tools for Early Detection of Cardiovascular Diseases',
    //     'proposal_abstract' => 'This project aims to develop and validate AI-driven diagnostic tools that can accurately detect cardiovascular diseases at an early stage, reducing mortality rates. The study will leverage large-scale patient data, including imaging and clinical records, to train machine learning models.',
    //     'proposal_introduction' => 'Cardiovascular diseases remain the leading cause of death globally. Despite advances in medical technology, early detection remains a challenge. This research proposes to address this issue by developing AI-based diagnostic tools using machine learning algorithms and neural networks trained on patient data.',
    //     'proposal_literature' => 'Previous studies have demonstrated the potential of AI in healthcare, particularly in diagnostics. However, most existing models lack generalizability across populations. This proposal aims to build upon prior research by developing more robust and inclusive models.',
    //     'proposal_methodology' => 'The research will adopt a multi-phase approach. Phase 1 will focus on data collection and preprocessing. Phase 2 involves model development and testing, using neural networks and deep learning techniques. Phase 3 will validate the models in clinical settings with the help of medical practitioners.',
    //     'proposal_results' => 'The expected outcome is a validated AI-driven diagnostic tool that can be integrated into healthcare systems for early detection of cardiovascular diseases, improving patient outcomes and reducing healthcare costs.',
    //     'proposal_reference' => 'Smith, J. et al., 2023. AI in Diagnostics: A Review. Journal of Biomedical Engineering, 12(4), pp. 123-145.',
    //     'proposal_submitted_date' => '2024-09-17',
    //     'proposal_end_date' => '2025-12-30',
    //     'proposal_budget' => '500000.00',
    //     'user_id' => 6,
    //     'created_at' => '2024-09-17T19:20:07.000000Z',
    //     'updated_at' => '2024-09-17T19:20:07.000000Z'
    // ]);

    // // Create the phases and activities
    // $phases = [
    //     [
    //         'phase_name' => 'Data Collection',
    //         'phase_startdate' => '2024-09-20',
    //         'phase_enddate' => '2024-12-20',
    //         'phase_objective' => 'Collect and preprocess patient data from multiple healthcare providers, ensuring data diversity and quality for machine learning model training.',
    //         'activities' => [
    //             [
    //                 'activity_name' => 'Data Gathering from Hospitals',
    //                 'activity_budget' => '120000.00'
    //             ],
    //             [
    //                 'activity_name' => 'Data Cleaning and Preprocessing',
    //                 'activity_budget' => '80000.00'
    //             ]
    //         ]
    //     ],
    //     [
    //         'phase_name' => 'Model Development',
    //         'phase_startdate' => '2025-01-10',
    //         'phase_enddate' => '2025-05-30',
    //         'phase_objective' => 'Develop and test machine learning models using neural networks, optimizing for accuracy and efficiency.',
    //         'activities' => [
    //             [
    //                 'activity_name' => 'Model Training',
    //                 'activity_budget' => '150000.00'
    //             ],
    //             [
    //                 'activity_name' => 'Model Validation',
    //                 'activity_budget' => '100000.00'
    //             ]
    //         ]
    //     ],
    //     [
    //         'phase_name' => 'Clinical Testing',
    //         'phase_startdate' => '2025-06-10',
    //         'phase_enddate' => '2025-12-10',
    //         'phase_objective' => 'Validate the developed AI tools in a real-world clinical environment, ensuring they meet regulatory standards and patient safety requirements.',
    //         'activities' => [
    //             [
    //                 'activity_name' => 'Clinical Testing and Feedback',
    //                 'activity_budget' => '50000.00'
    //             ]
    //         ]
    //     ]
    // ];

    // foreach ($phases as $phaseData) {
    //     $phase = Phase::create([
    //         'phase_name' => $phaseData['phase_name'],
    //         'phase_startdate' => $phaseData['phase_startdate'],
    //         'phase_enddate' => $phaseData['phase_enddate'],
    //         'phase_objective' => $phaseData['phase_objective'],
    //         'proposal_id' => $proposal->id,
    //         'created_at' => '2024-09-17T19:20:07.000000Z',
    //         'updated_at' => '2024-09-17T19:20:07.000000Z'
    //     ]);

    //     foreach ($phaseData['activities'] as $activityData) {
    //         Activity::create([
    //             'activity_name' => $activityData['activity_name'],
    //             'activity_budget' => $activityData['activity_budget'],
    //             'phase_id' => $phase->id,
    //             'created_at' => '2024-09-17T19:20:07.000000Z',
    //             'updated_at' => '2024-09-17T19:20:07.000000Z'
    //         ]);
    //     }
    // }
    }
}
