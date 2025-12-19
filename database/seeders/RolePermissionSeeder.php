<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions
        $permissions = [
            'manage_anggota',
            'manage_simpanan',
            'manage_pinjaman',
            'manage_transaksi',
            'manage_laporan',
            'manage_roles',
            'manage_closing',
            'manage_simulasi',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $anggotaRole = Role::firstOrCreate(['name' => 'anggota']);
        
        // Assign all permissions to Admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions);

        // Anggota permissions (limited access but enough to see menus)
        $anggotaPermissions = $allPermissions->filter(function($permission) {
            return in_array($permission->name, [
                'manage_anggota',
                'manage_simpanan',
                'manage_pinjaman',
                'manage_laporan',
                'manage_simulasi'
            ]);
        });
        $anggotaRole->permissions()->sync($anggotaPermissions);

        // Assign 'admin' role to the first user if exists
        $user = User::first();
        if ($user) {
            $user->roles()->sync([$adminRole->id]);
        }
    }
}
