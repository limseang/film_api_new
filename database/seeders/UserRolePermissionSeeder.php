<?php

namespace Database\Seeders;

use App\Constant\RolePermissionConstant;
use App\Constant\UserConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->delete();
        Admin::query()->firstOrCreate(
            ['id' => 1],
            ['username' => 'Owner', 'email' => 'owner@gmail.com', 'role_id' => 1, 'password' => bcrypt('12345678')]
        );

        $roles = [
            ['created_at' => now(),'id' => 1, 'name' => 'Owner', 'status' => 'Y', 'is_default' => 'Y'],
            ['created_at' => now(),'id' => 2, 'name' => 'User','status' => 'Y','is_default' => 'N'],
        ];
        DB::table('roles')->insertOrIgnore($roles);

        $menus = [
            ['id' => 1, 'name' => RolePermissionConstant::MENU_DASHBOARD,'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => RolePermissionConstant::MENU_USER, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => RolePermissionConstant::MENU_MISS_PLANET, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => RolePermissionConstant::MENU_SUPPLIER, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => RolePermissionConstant::MENU_SUPPLIER_ORDER, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => RolePermissionConstant::MENU_DEPOSIT, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => RolePermissionConstant::MENU_TRANSACTION, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => RolePermissionConstant::MENU_SETTING, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => RolePermissionConstant::MENU_ROLE, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => RolePermissionConstant::MENU_ADMIN, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => RolePermissionConstant::MENU_SET_ROUND, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => RolePermissionConstant::MENU_VOTE_PRICE, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'name' => RolePermissionConstant::MENU_PACKAGE , 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => RolePermissionConstant::MENU_CURRENCY, 'parent_id' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => RolePermissionConstant::MENU_SYSTEM_USER_LOG, 'parent_id'=> 0, 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('permissions')->insertOrIgnore($menus);

        $permissions =[
            // Dashboard
            ['id' => 16, 'name' => RolePermissionConstant::PERMISSION_DASHBOARD_VIEW, 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            // User
            ['id' => 17, 'name' => RolePermissionConstant::PERMISSION_USER_VIEW, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'name' => RolePermissionConstant::PERMISSION_USER_CREATE, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => RolePermissionConstant::PERMISSION_USER_EDIT, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'name' => RolePermissionConstant::PERMISSION_USER_DELETE, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'name' => RolePermissionConstant::PERMISSION_USER_RESTORE, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Miss Planet
            ['id' => 22, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_VIEW, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_VIEW_ANY, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_CREATE, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_EDIT, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_DELETE, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_RESTORE, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_VIEW_ANY, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_VOTE_HISTORY, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'name' => RolePermissionConstant::PERMISSION_MISS_PLANET_COMMENT_HISTORY, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            // Supplier
            ['id' => 31, 'name' => RolePermissionConstant::PERMISSION_SUPPLIER_VIEW, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'name' => RolePermissionConstant::PERMISSION_SUPPLIER_EDIT, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Supplier Order
            ['id' => 33, 'name' => RolePermissionConstant::PERMISSION_SUPPLIER_ORDER_VIEW, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'name' => RolePermissionConstant::PERMISSION_SUPPLIER_ORDER_VIEW_ANY, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            // Deposit
            ['id' => 35, 'name' => RolePermissionConstant::PERMISSION_DEPOSIT_VIEW, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'name' => RolePermissionConstant::PERMISSION_DEPOSIT_VIEW_ANY, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            //Transaction
            ['id' => 37, 'name' => RolePermissionConstant::PERMISSION_TRANSACTION_VIEW, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'name' => RolePermissionConstant::PERMISSION_TRANSACTION_VIEW_ANY, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            // Setting
            ['id' => 39, 'name' => RolePermissionConstant::PERMISSION_SETTING_VIEW, 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            // Role 
            ['id' => 40, 'name' => RolePermissionConstant::PERMISSION_ROLE_VIEW, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'name' => RolePermissionConstant::PERMISSION_ROLE_CREATE, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 42, 'name' => RolePermissionConstant::PERMISSION_ROLE_EDIT, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 43, 'name' => RolePermissionConstant::PERMISSION_ROLE_DELETE, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 44, 'name' => RolePermissionConstant::PERMISSION_ROLE_RESTORE, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 45, 'name' => RolePermissionConstant::PERMISSION_CHANGE_PERMISSION, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            // Admin
            ['id' => 46, 'name' => RolePermissionConstant::PERMISSION_ADMIN_VIEW, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 47, 'name' => RolePermissionConstant::PERMISSION_ADMIN_CREATE, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 48, 'name' => RolePermissionConstant::PERMISSION_ADMIN_EDIT, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 49, 'name' => RolePermissionConstant::PERMISSION_ADMIN_DELETE, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 50, 'name' => RolePermissionConstant::PERMISSION_ADMIN_RESTORE, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            // Set Round
            ['id' => 51, 'name' => RolePermissionConstant::PERMISSION_SET_ROUND_VIEW, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 52, 'name' => RolePermissionConstant::PERMISSION_SET_ROUND_CREATE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 53, 'name' => RolePermissionConstant::PERMISSION_SET_ROUND_EDIT, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 54, 'name' => RolePermissionConstant::PERMISSION_SET_ROUND_DELETE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 55, 'name' => RolePermissionConstant::PERMISSION_SET_ROUND_RESTORE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            //Vote Price
            ['id' => 56, 'name' => RolePermissionConstant::PERMISSION_VOTE_PRICE_VIEW, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 57, 'name' => RolePermissionConstant::PERMISSION_VOTE_PRICE_EDIT, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 58, 'name' => RolePermissionConstant::PERMISSION_VOTE_PRICE_HISTORY, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            // Package
            ['id' => 59, 'name' => RolePermissionConstant::PERMISSION_PACKAGE_VIEW, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 60, 'name' => RolePermissionConstant::PERMISSION_PACKAGE_CREATE, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 61, 'name' => RolePermissionConstant::PERMISSION_PACKAGE_EDIT, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 62, 'name' => RolePermissionConstant::PERMISSION_PACKAGE_DELETE, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 63, 'name' => RolePermissionConstant::PERMISSION_PACKAGE_RESTORE, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            // Currency
            ['id' => 64, 'name' => RolePermissionConstant::PERMISSION_CURRENCY_VIEW, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            // System User Log
            ['id' => 65, 'name' => RolePermissionConstant::PERMISSION_SYSTEM_USER_LOG_VIEW, 'parent_id' => 15, 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('permissions')->insertOrIgnore($permissions);

        // Assign permission to role
        $permission = Permission::query()->pluck('id')->toArray();
        $roleOwner = Role::query()->where('name', UserConstant::ROLE_OWNER)->first();
        // Assign all permission to owner
        $roleOwner->hashPermission()->sync($permission);

    }
}
