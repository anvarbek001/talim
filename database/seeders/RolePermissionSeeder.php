<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'add lesson']);
        Permission::firstOrCreate(['name' => 'add test']);
        Permission::firstOrCreate(['name' => 'student statistics']);
        Permission::firstOrCreate(['name' => 'teacher statistics']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $inspector = Role::firstOrCreate(['name' => 'inspector']);  // nazoratchi
        $student = Role::firstOrCreate(['name' => 'student']);
    
        $admin->givePermissionTo([
            'add lesson',
            'add test',
            'student statistics',
            'teacher statistics',
        ]);

        $teacher->givePermissionTo([
            'add lesson',
            'add test',
            'student statistics',
        ]);

        $inspector->givePermissionTo([
            'add lesson',
            'add test',
            'student statistics',
        ]);
        $student->givePermissionTo([
            'student statistics',
        ]);
    }
}
