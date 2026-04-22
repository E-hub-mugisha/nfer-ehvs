<?php
// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──────────────────────────────────────────
        $permissions = [
            // Employee
            'view own profile', 'edit own profile',
            'view own employment history', 'confirm employment record',
            'raise dispute',

            // Employer
            'search employees', 'view employee history',
            'create employment record', 'update employment record',
            'create feedback', 'update feedback',

            // Admin
            'manage users', 'manage employers', 'manage employees',
            'verify employer', 'verify employee',
            'view all records', 'manage disputes',
            'generate reports', 'manage skills',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Roles ─────────────────────────────────────────────────
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->syncPermissions([
            'view own profile', 'edit own profile',
            'view own employment history', 'confirm employment record',
            'raise dispute',
        ]);

        $employer = Role::firstOrCreate(['name' => 'employer']);
        $employer->syncPermissions([
            'search employees', 'view employee history',
            'create employment record', 'update employment record',
            'create feedback', 'update feedback',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());
    }
}