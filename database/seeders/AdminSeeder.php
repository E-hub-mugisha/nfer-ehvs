<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Employer;
use App\Models\Skill;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ───────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@nfer.gov'],
            [
                'name'         => 'System Administrator',
                'phone'        => '+250788000001',
                'password'     => Hash::make('Admin@12345'),
                'account_type' => 'admin',
                'is_verified'  => true,
            ]
        );
        $admin->assignRole('admin');

        // ── Demo Employer ─────────────────────────────────────────
        $empUser = User::firstOrCreate(
            ['email' => 'hr@techcorp.rw'],
            [
                'name'         => 'TechCorp Rwanda HR',
                'phone'        => '+250788100001',
                'password'     => Hash::make('Employer@123'),
                'account_type' => 'employer',
                'is_verified'  => true,
            ]
        );
        $empUser->assignRole('employer');
        Employer::firstOrCreate(
            ['user_id' => $empUser->id],
            [
                'company_name'        => 'TechCorp Rwanda Ltd',
                'registration_number' => 'RDB/2019/00123',
                'tin_number'          => '103456789',
                'company_type'        => 'private_company',
                'industry'            => 'Information Technology',
                'district'            => 'Kigali',
                'city'                => 'Kigali',
                'address'             => 'KG 9 Ave, Kacyiru',
                'website'             => 'https://techcorp.rw',
                'contact_person'      => 'Jane Uwimana',
                'contact_email'       => 'jane@techcorp.rw',
                'contact_phone'       => '+250788100002',
                'verification_status' => 'verified',
                'verified_at'         => now(),
            ]
        );

        // ── Demo Employee ─────────────────────────────────────────
        $empUser2 = User::firstOrCreate(
            ['email' => 'employee@demo.rw'],
            [
                'name'         => 'Jean Baptiste Nkurunziza',
                'phone'        => '+250788200001',
                'password'     => Hash::make('Employee@123'),
                'account_type' => 'employee',
                'is_verified'  => true,
            ]
        );
        $empUser2->assignRole('employee');
        Employee::firstOrCreate(
            ['user_id' => $empUser2->id],
            [
                'national_id'       => '1199880012345678',
                'first_name'        => 'Jean Baptiste',
                'last_name'         => 'Nkurunziza',
                'date_of_birth'     => '1988-06-15',
                'gender'            => 'male',
                'nationality'       => 'Rwandan',
                'district'          => 'Gasabo',
                'current_title'     => 'Senior Software Engineer',
                'employment_status' => 'employed',
                'is_verified'       => true,
                'verified_at'       => now(),
            ]
        );

        // ── Skills ────────────────────────────────────────────────
        $skills = [
            ['name' => 'Project Management',    'category' => 'Management'],
            ['name' => 'Financial Analysis',    'category' => 'Finance'],
            ['name' => 'Software Development',  'category' => 'Technology'],
            ['name' => 'Data Analysis',         'category' => 'Technology'],
            ['name' => 'Human Resources',       'category' => 'Management'],
            ['name' => 'Customer Service',      'category' => 'Operations'],
            ['name' => 'Accounting',            'category' => 'Finance'],
            ['name' => 'Marketing',             'category' => 'Marketing'],
            ['name' => 'Public Relations',      'category' => 'Marketing'],
            ['name' => 'Legal Affairs',         'category' => 'Legal'],
            ['name' => 'Procurement',           'category' => 'Operations'],
            ['name' => 'Research & Development','category' => 'Research'],
        ];
        foreach ($skills as $skill) {
            Skill::firstOrCreate(['name' => $skill['name']], $skill);
        }
    }
}