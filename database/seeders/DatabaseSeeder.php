<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'view dashboard',
            'manage products',
            'manage inventory',
            'access pos',
            'view sales',
            'manage sales',
            'manage purchases',
            'manage suppliers',
            'manage customers',
            'manage expenses',
            'view reports',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Roles
        $superAdminRole = Role::findOrCreate('Super Admin');
        $adminRole = Role::findOrCreate('Admin');
        $storeManagerRole = Role::findOrCreate('Store Manager');
        $inventoryManagerRole = Role::findOrCreate('Inventory Manager');
        $warehouseStaffRole = Role::findOrCreate('Warehouse Staff');
        $cashierRole = Role::findOrCreate('Cashier');
        $accountantRole = Role::findOrCreate('Accountant');

        $superAdminRole->givePermissionTo(Permission::all());
        $adminRole->givePermissionTo(Permission::all());
        $cashierRole->givePermissionTo(['access pos', 'view sales', 'manage sales', 'manage customers']);
        $inventoryManagerRole->givePermissionTo(['manage products', 'manage inventory', 'view dashboard']);
        $accountantRole->givePermissionTo(['view reports', 'manage expenses', 'view sales']);

        // Create Default Enterprise Users
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('Admin123!@#'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@pos.com'],
            [
                'name' => 'Default Cashier',
                'password' => Hash::make('Cashier123!@#'),
                'email_verified_at' => now(),
            ]
        );
        $cashier->assignRole($cashierRole);

        $this->call(EnterpriseSeeder::class);
    }
}
