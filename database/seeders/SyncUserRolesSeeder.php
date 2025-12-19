<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SyncUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $anggotaRole = Role::where('name', 'anggota')->first();

        if (!$adminRole || !$anggotaRole) {
            $this->command->error('Roles not found. Please run RolePermissionSeeder first.');
            return;
        }

        $users = User::all();
        $adminCount = 0;
        $anggotaCount = 0;

        foreach ($users as $user) {
            // Check legacy role column if it exists or use heuristic
            // role = 1 is admin, role = 0 is anggota
            if (isset($user->role)) {
                if ($user->role == 1) {
                    $user->roles()->syncWithoutDetaching([$adminRole->id]);
                    $adminCount++;
                } else {
                    $user->roles()->syncWithoutDetaching([$anggotaRole->id]);
                    $anggotaCount++;
                }
            } else {
                // If the column doesn't exist, check if they have an associated Anggota record
                if ($user->anggota) {
                    $user->roles()->syncWithoutDetaching([$anggotaRole->id]);
                    $anggotaCount++;
                }
            }
        }

        $this->command->info("Synchronization complete!");
        $this->command->info("Admin roles assigned: {$adminCount}");
        $this->command->info("Anggota roles assigned: {$anggotaCount}");
    }
}
