<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Roles
        $roles = [
            ['name' => 'administrator', 'guard_name' => 'web'],
            ['name' => 'monitoring', 'guard_name' => 'web'],
            ['name' => 'administrator', 'guard_name' => 'user'],
            ['name' => 'monitoring', 'guard_name' => 'user'],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate($r);
        }

        // 2. Super Admin User (ID 13)
        $superadmin = User::find(13);
        if (!$superadmin) {
            $superadmin = User::where('email', 'superadmin@presensigps.com')->first();
        }

        if (!$superadmin) {
            $superadmin = User::create([
                'id' => 13,
                'name' => 'Super Admin',
                'email' => 'superadmin@presensigps.com',
                'password' => '$2y$10$7h31EhLcEjr22YgTy6VBK.T/1uIAgjKIlVjmT.k91pWjoaS.CcFsy', // 12345
                'kode_dept' => 'IT',
                'kode_cabang' => null
            ]);
        }

        // Assign administrator role
        DB::table('model_has_roles')->updateOrInsert(
            ['role_id' => 1, 'model_type' => 'App\\Models\\User', 'model_id' => $superadmin->id]
        );
        DB::table('model_has_roles')->updateOrInsert(
            ['role_id' => 3, 'model_type' => 'App\\Models\\User', 'model_id' => $superadmin->id]
        );

        // 3. Akun Monitoring Default
        $monitoringUser = User::firstOrCreate(
            ['email' => 'monitoring@samsat.id'],
            [
                'name' => 'Petugas Monitoring',
                'password' => bcrypt('monitoring123'),
                'kode_cabang' => null
            ]
        );
        if (!$monitoringUser->hasRole('monitoring')) {
            $monitoringUser->assignRole('monitoring');
        }
    }
}

