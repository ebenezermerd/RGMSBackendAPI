<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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

        // Create additional reviewer users
        $reviewerUsers = [
            [
                'username' => 'reviewer1',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@gmail.com',
                'phone_number' => '1111111111',
                'password' => Hash::make('password1'),
                'city' => 'City1',
                'present_address' => 'Address1',
                'permanent_address' => 'Permanent Address1',
                'date_of_birth' => now()->subYears(30),
                'bio' => 'Reviewer user 1.',
            ],
            [
                'username' => 'reviewer2',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@gmail.com',
                'phone_number' => '2222222222',
                'password' => Hash::make('password2'),
                'city' => 'City2',
                'present_address' => 'Address2',
                'permanent_address' => 'Permanent Address2',
                'date_of_birth' => now()->subYears(28),
                'bio' => 'Reviewer user 2.',
            ],
            [
                'username' => 'reviewer3',
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alice.johnson@gmail.com',
                'phone_number' => '3333333333',
                'password' => Hash::make('password3'),
                'city' => 'City3',
                'present_address' => 'Address3',
                'permanent_address' => 'Permanent Address3',
                'date_of_birth' => now()->subYears(32),
                'bio' => 'Reviewer user 3.',
            ],
            [
                'username' => 'reviewer4',
                'first_name' => 'Bob',
                'last_name' => 'Brown',
                'email' => 'bob.brown@gmail.com',
                'phone_number' => '4444444444',
                'password' => Hash::make('password4'),
                'city' => 'City4',
                'present_address' => 'Address4',
                'permanent_address' => 'Permanent Address4',
                'date_of_birth' => now()->subYears(29),
                'bio' => 'Reviewer user 4.',
            ],
            [
                'username' => 'reviewer5',
                'first_name' => 'Charlie',
                'last_name' => 'Davis',
                'email' => 'charlie.davis@gmail.com',
                'phone_number' => '5555555555',
                'password' => Hash::make('password5'),
                'city' => 'City5',
                'present_address' => 'Address5',
                'permanent_address' => 'Permanent Address5',
                'date_of_birth' => now()->subYears(31),
                'bio' => 'Reviewer user 5.',
            ],
            [
                'username' => 'reviewer6',
                'first_name' => 'Eve',
                'last_name' => 'Miller',
                'email' => 'eve.miller@gmail.com',
                'phone_number' => '6666666666',
                'password' => Hash::make('password6'),
                'city' => 'City6',
                'present_address' => 'Address6',
                'permanent_address' => 'Permanent Address6',
                'date_of_birth' => now()->subYears(27),
                'bio' => 'Reviewer user 6.',
            ],
        ];

        foreach ($reviewerUsers as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, ['role_id' => $reviewerRole->id, 'profile_image' => null])
            );
        }

        $this->command->info('Default users created or updated successfully.');
    }
}
