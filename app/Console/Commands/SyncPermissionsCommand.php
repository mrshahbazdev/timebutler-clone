<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing permissions and sync them to default roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            'manage_employees',
            'manage_holidays',
            'manage_absence_types',
            'manage_overtime',
            'view_all_absences',
            'view_team_absences',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $employee = Role::firstOrCreate(['name' => 'employee']);

        // Admin gets all permissions
        $admin->syncPermissions($permissions);

        // Manager gets specific permissions
        $manager->syncPermissions([
            'view_team_absences',
        ]);

        // Employee gets no special management permissions for now

        $this->info('Permissions have been created and synced to roles successfully.');
    }
}
