<?php

namespace Database\Seeders;

use App\Constant\RolePermissionConstant;
use App\Constant\UserConstant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->truncate();
        User::query()->firstOrCreate(
            ['id' => 1],
            ['username' => 'Owner', 'email' => 'owner@gmail.com', 'role_id' => 1, 'password' => bcrypt('12345678')]
        );

        $roles = [
            ['created_at' => now(),'name' => 'Owner', 'is_default' => 'Y','description' => 'Owner'],
            ['created_at' => now(),'name' => 'User','is_default' => 'N','description' => 'User'],
        ];
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],  // Matching condition (unique field)
                $role  // Data to insert or update
            );
        }

        $menus = [
            ['id' => 1, 'name' => RolePermissionConstant::MENU_DASHBOARD,'parent_id' => 0, 'icon' => RolePermissionConstant::DASHBOARD_ICON, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => RolePermissionConstant::MENU_USER, 'parent_id' => 0,'icon' => RolePermissionConstant::USER_ICON, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => RolePermissionConstant::MENU_ARTIST, 'parent_id' => 0,'icon' => RolePermissionConstant::ARTIST_ICON, 'created_at' => now(),'updated_at' => now()],
            ['id' => 4, 'name' => RolePermissionConstant::MENU_DIRECTOR, 'parent_id' => 0,'icon' => RolePermissionConstant::DIRECTOR_ICON, 'created_at' => now(),'updated_at' => now()],
            ['id' => 5, 'name' => RolePermissionConstant::MENU_AVAILABLE_IN,'parent_id' => 0,'icon' => RolePermissionConstant::AVAILABLE_IN_ICON,  'create_at' =>now(),'updated_at' => now()],
            ['id' => 6, 'name' => RolePermissionConstant::MENU_CINEMA_BRANCH,'parent_id' => 0,'icon' => RolePermissionConstant::CINEMA_BRANCH_ICON, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => RolePermissionConstant::MENU_GIFT, 'parent_id' => 0,'icon' => RolePermissionConstant::GIFT_ICON, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => RolePermissionConstant::MENU_RANDOM_GIFT, 'parent_id' => 0,'icon' => RolePermissionConstant::RANDOM_GIFT_ICON, 'created_at' => now(),'updated_at' => now()],
            ['id' => 9, 'name' => RolePermissionConstant::MENU_ARTICAL, 'parent_id' => 0,'icon' => RolePermissionConstant::ARTICAL_ICON, 'created_at' => now(),'updated_at' => now()],
            ['id' => 10, 'name' => RolePermissionConstant::MENU_ORIGIN,'parent_id' => 0, 'icon' => '', 'create_at' =>now(),'updated_at' => now()],
            ['id' => 11, 'name' => RolePermissionConstant::MENU_FILM, 'parent_id'=> 0,'icon' => RolePermissionConstant::FILM_ICON, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'name' => RolePermissionConstant::MENU_CAST,'parent_id' => 0,'icon' => '', 'create_at' =>now(),'updated_at' => now()],
            ['id' => 13, 'name' => RolePermissionConstant::MENU_REPORT_INCOME_EXPENSE,'parent_id' => 0, 'icon' => RolePermissionConstant::REPORT_INCOME_EXPENSE_ICON, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'name' => RolePermissionConstant::MENU_ROLE, 'parent_id' => 0,'icon' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'name' => RolePermissionConstant::MENU_TYPE, 'parent_id' => 0,'icon' => '', 'created_at' => now(),'updated_at' => now()],
            ['id' => 16, 'name' => RolePermissionConstant::MENU_TAG, 'parent_id' => 0,'icon' => '', 'created_at' => now(),'updated_at' => now()],
            ['id' => 17, 'name' => RolePermissionConstant::MENU_DISTRIBUTOR,'parent_id' => 0,'icon' => '', 'create_at' =>now(),'updated_at' => now()],
            ['id' => 18, 'name' => RolePermissionConstant::MENU_CATEGORY, 'parent_id'=> 0,'icon' => '', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'name' => RolePermissionConstant::MENU_GENRE, 'parent_id' => 0,'icon' => '', 'created_at' => now(),'updated_at' => now()],
            ['id' => 20, 'name' => RolePermissionConstant::MENU_VERSION,'parent_id' => 0,'icon' => '', 'create_at' =>now(),'updated_at' => now()],
            ['id' => 21, 'name' => RolePermissionConstant::MENU_SYSTEM_USER_LOG, 'parent_id'=> 0,'icon' => '', 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('permissions')->insertOrIgnore($menus);

        $permissions =[
            // Dashboard
            ['id' => 22, 'name' => RolePermissionConstant::PERMISSION_DASHBOARD_VIEW, 'parent_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            // User
            ['id' => 23, 'name' => RolePermissionConstant::PERMISSION_USER_VIEW, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'name' => RolePermissionConstant::PERMISSION_USER_CREATE, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'name' => RolePermissionConstant::PERMISSION_USER_EDIT, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'name' => RolePermissionConstant::PERMISSION_USER_DELETE, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'name' => RolePermissionConstant::PERMISSION_USER_RESTORE, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 135, 'name' => RolePermissionConstant::PERMISSION_USER_CHANGE_STATUS, 'parent_id' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Artist
            ['id' => 28, 'name' => RolePermissionConstant::PERMISSION_ARTIST_VIEW, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'name' => RolePermissionConstant::PERMISSION_ARTIST_CREATE, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'name' => RolePermissionConstant::PERMISSION_ARTIST_EDIT, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'name' => RolePermissionConstant::PERMISSION_ARTIST_DELETE, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'name' => RolePermissionConstant::PERMISSION_ARTIST_RESTORE, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'name' => RolePermissionConstant::PERMISSION_ARTIST_CHANGE_STATUS, 'parent_id' => 3, 'created_at' => now(), 'updated_at' => now()],

             // DIRECTOR
            ['id' => 34, 'name' => RolePermissionConstant::PERMISSION_DIRECTOR_VIEW, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'name' => RolePermissionConstant::PERMISSION_DIRECTOR_CREATE, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'name' => RolePermissionConstant::PERMISSION_DIRECTOR_EDIT, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'name' => RolePermissionConstant::PERMISSION_DIRECTOR_DELETE, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'name' => RolePermissionConstant::PERMISSION_DIRECTOR_CHANGE_STATUS, 'parent_id' => 4, 'created_at' => now(), 'updated_at' => now()],

            // AVAILABLE IN
            ['id' => 39, 'name' => RolePermissionConstant::PERMISSION_AVAILABLE_IN_VIEW, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'name' => RolePermissionConstant::PERMISSION_AVAILABLE_IN_CREATE, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'name' => RolePermissionConstant::PERMISSION_AVAILABLE_IN_EDIT, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 42, 'name' => RolePermissionConstant::PERMISSION_AVAILABLE_IN_DELETE, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 133, 'name' => RolePermissionConstant::PERMISSION_AVAILABLE_IN_RESTORE, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 43, 'name' => RolePermissionConstant::PERMISSION_ASSIGN_AVAILABLE_IN, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 44, 'name' => RolePermissionConstant::PERMISSION_ADD_ASSIGN_AVAILABLE_IN, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 45, 'name' => RolePermissionConstant::PERMISSION_DELETE_ASSIGN_AVAILABLE_IN, 'parent_id' => 5, 'created_at' => now(), 'updated_at' => now()],

            // CINEMA BRANCH
            ['id' => 46, 'name' => RolePermissionConstant::PERMISSION_CINEMA_BRANCH_VIEW, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 47, 'name' => RolePermissionConstant::PERMISSION_CINEMA_BRANCH_VIEW_DETAIL, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 48, 'name' => RolePermissionConstant::PERMISSION_CINEMA_BRANCH_CREATE, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 49, 'name' => RolePermissionConstant::PERMISSION_CINEMA_BRANCH_EDIT, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 50, 'name' => RolePermissionConstant::PERMISSION_CINEMA_BRANCH_DELETE, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 134, 'name' => RolePermissionConstant::PERMISSION_CINEMA_BRANCH_CHANGE_STATUS, 'parent_id' => 6, 'created_at' => now(), 'updated_at' => now()],

            // GIFT 
            ['id' => 51, 'name' => RolePermissionConstant::PERMISSION_GIFT_VIEW, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 52, 'name' => RolePermissionConstant::PERMISSION_GIFT_CREATE, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 53, 'name' => RolePermissionConstant::PERMISSION_GIFT_EDIT, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 54, 'name' => RolePermissionConstant::PERMISSION_GIFT_DELETE, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 55, 'name' => RolePermissionConstant::PERMISSION_GIFT_CHANGE_STATUS, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 56, 'name' => RolePermissionConstant::PERMISSION_GIFT_RESTORE, 'parent_id' => 7, 'created_at' => now(), 'updated_at' => now()],

            // RANDOM GIFT
            ['id' => 57, 'name' => RolePermissionConstant::PERMISSION_RANDOM_GIFT_VIEW, 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 58, 'name' => RolePermissionConstant::PERMISSION_RANDOM_GIFT_DELETE, 'parent_id' => 8, 'created_at' => now(), 'updated_at' => now()],

            // ARTICAL
            ['id' => 59, 'name' => RolePermissionConstant::PERMISSION_ARTICAL_VIEW, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 60, 'name' => RolePermissionConstant::PERMISSION_ARTICAL_CREATE, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 61, 'name' => RolePermissionConstant::PERMISSION_ARTICAL_EDIT, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 62, 'name' => RolePermissionConstant::PERMISSION_ARTICAL_DELETE, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 63, 'name' => RolePermissionConstant::PERMISSION_ARTICAL_RESTORE, 'parent_id' => 9, 'created_at' => now(), 'updated_at' => now()],

            // ORIGIN
            ['id' => 64, 'name' => RolePermissionConstant::PERMISSION_ORIGIN_VIEW, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 65, 'name' => RolePermissionConstant::PERMISSION_ORIGIN_CREATE, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 66, 'name' => RolePermissionConstant::PERMISSION_ORIGIN_EDIT, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 67, 'name' => RolePermissionConstant::PERMISSION_ORIGIN_DELETE, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 68, 'name' => RolePermissionConstant::PERMISSION_ORIGIN_CHANGE_STATUS, 'parent_id' => 10, 'created_at' => now(), 'updated_at' => now()],

            // FILM
            ['id' => 69, 'name' => RolePermissionConstant::PERMISSION_FILM_VIEW, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 70, 'name' => RolePermissionConstant::PERMISSION_FILM_CREATE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 71, 'name' => RolePermissionConstant::PERMISSION_FILM_EDIT, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 72, 'name' => RolePermissionConstant::PERMISSION_FILM_DELETE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 73, 'name' => RolePermissionConstant::PERMISSION_FILM_RESTORE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 74, 'name' => RolePermissionConstant::PERMISSION_FILM_SHOW_EPISODE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 75, 'name' => RolePermissionConstant::PERMISSION_FILM_ASSIGN_AVAILABLE_IN, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 76, 'name' => RolePermissionConstant::PERMISSION_FILM_ADD_AVAILABLE_IN, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 77, 'name' => RolePermissionConstant::PERMISSION_FILM_DELETE_AVAILABLE_IN, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 78, 'name' => RolePermissionConstant::PERMISSION_FILM_ADD_EPISODE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 79, 'name' => RolePermissionConstant::PERMISSION_FILM_EDIT_EPISODE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 80, 'name' => RolePermissionConstant::PERMISSION_FILM_DELETE_EPISODE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 81, 'name' => RolePermissionConstant::PERMISSION_FILM_CHANGE_STATUS_EPISODE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 82, 'name' => RolePermissionConstant::PERMISSION_FILM_RESTORE_EPISODE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 83, 'name' => RolePermissionConstant::PERMISSION_FILM_ADD_EPISODE_SUBTITLE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 84, 'name' => RolePermissionConstant::PERMISSION_FILM_DELETE_EPISODE_SUBTITLE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 136, 'name' => RolePermissionConstant::PERMISSION_FILM_EDIT_EPISODE_SUBTITLE, 'parent_id' => 11, 'created_at' => now(), 'updated_at' => now()],

            // CAST
            ['id' => 85, 'name' => RolePermissionConstant::PERMISSION_CAST_VIEW, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 86, 'name' => RolePermissionConstant::PERMISSION_CAST_CREATE, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 87, 'name' => RolePermissionConstant::PERMISSION_CAST_EDIT, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 88, 'name' => RolePermissionConstant::PERMISSION_CAST_DELETE, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 89, 'name' => RolePermissionConstant::PERMISSION_CAST_RESTORE, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 90, 'name' => RolePermissionConstant::PERMISSION_CAST_CHANGE_STATUS, 'parent_id' => 12, 'created_at' => now(), 'updated_at' => now()],

            // REPORT INCOME EXPENSE
            ['id' => 91, 'name' => RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_VIEW, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 92, 'name' => RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_CREATE, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 93, 'name' => RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_EDIT, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 94, 'name' => RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_DELETE, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 95, 'name' => RolePermissionConstant::PERMISSION_REPORT_INCOME_EXPENSE_RESTORE, 'parent_id' => 13, 'created_at' => now(), 'updated_at' => now()],

            // ROLE
            ['id' => 96, 'name' => RolePermissionConstant::PERMISSION_ROLE_VIEW, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 97, 'name' => RolePermissionConstant::PERMISSION_ROLE_CREATE, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 98, 'name' => RolePermissionConstant::PERMISSION_ROLE_EDIT, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 99, 'name' => RolePermissionConstant::PERMISSION_ROLE_DELETE, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 100, 'name' => RolePermissionConstant::PERMISSION_ROLE_RESTORE, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 101, 'name' => RolePermissionConstant::PERMISSION_CHANGE_PERMISSION, 'parent_id' => 14, 'created_at' => now(), 'updated_at' => now()],

            // TYPE
            ['id' => 102, 'name' => RolePermissionConstant::PERMISSION_TYPE_VIEW, 'parent_id' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 103, 'name' => RolePermissionConstant::PERMISSION_TYPE_CREATE, 'parent_id' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 104, 'name' => RolePermissionConstant::PERMISSION_TYPE_EDIT, 'parent_id' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 105, 'name' => RolePermissionConstant::PERMISSION_TYPE_DELETE, 'parent_id' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 106, 'name' => RolePermissionConstant::PERMISSION_TYPE_CHANGE_STATUS, 'parent_id' => 15, 'created_at' => now(), 'updated_at' => now()],	

            // TAG
            ['id' => 107, 'name' => RolePermissionConstant::PERMISSION_TAG_VIEW, 'parent_id' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 108, 'name' => RolePermissionConstant::PERMISSION_TAG_CREATE, 'parent_id' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 109, 'name' => RolePermissionConstant::PERMISSION_TAG_EDIT, 'parent_id' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 110, 'name' => RolePermissionConstant::PERMISSION_TAG_DELETE, 'parent_id' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 111, 'name' => RolePermissionConstant::PERMISSION_TAG_CHANGE_STATUS, 'parent_id' => 16, 'created_at' => now(), 'updated_at' => now()],

            // DISTRIBUTOR
            ['id' => 112, 'name' => RolePermissionConstant::PERMISSION_DISTRIBUTOR_VIEW, 'parent_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 113, 'name' => RolePermissionConstant::PERMISSION_DISTRIBUTOR_CREATE, 'parent_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 114, 'name' => RolePermissionConstant::PERMISSION_DISTRIBUTOR_EDIT, 'parent_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 115, 'name' => RolePermissionConstant::PERMISSION_DISTRIBUTOR_DELETE, 'parent_id' => 17, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 116, 'name' => RolePermissionConstant::PERMISSION_DISTRIBUTOR_CHANGE_STATUS, 'parent_id' => 17, 'created_at' => now(), 'updated_at' => now()],

            // CATEGORY
            ['id' => 117, 'name' => RolePermissionConstant::PERMISSION_CATEGORY_VIEW, 'parent_id' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 118, 'name' => RolePermissionConstant::PERMISSION_CATEGORY_CREATE, 'parent_id' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 119, 'name' => RolePermissionConstant::PERMISSION_CATEGORY_EDIT, 'parent_id' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 120, 'name' => RolePermissionConstant::PERMISSION_CATEGORY_DELETE, 'parent_id' => 18, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 121, 'name' => RolePermissionConstant::PERMISSION_CATEGORY_CHANGE_STATUS, 'parent_id' => 18, 'created_at' => now(), 'updated_at' => now()],

            // GENRE
            ['id' => 122, 'name' => RolePermissionConstant::PERMISSION_GENRE_VIEW, 'parent_id' => 19, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 123, 'name' => RolePermissionConstant::PERMISSION_GENRE_CREATE, 'parent_id' => 19, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 124, 'name' => RolePermissionConstant::PERMISSION_GENRE_EDIT, 'parent_id' => 19, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 125, 'name' => RolePermissionConstant::PERMISSION_GENRE_DELETE, 'parent_id' => 19, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 126, 'name' => RolePermissionConstant::PERMISSION_GENRE_CHANGE_STATUS, 'parent_id' => 19, 'created_at' => now(), 'updated_at' => now()],

            // VERSION
            ['id' => 127, 'name' => RolePermissionConstant::PERMISSION_VERSION_VIEW, 'parent_id' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 128, 'name' => RolePermissionConstant::PERMISSION_VERSION_CREATE, 'parent_id' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 129, 'name' => RolePermissionConstant::PERMISSION_VERSION_EDIT, 'parent_id' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 130, 'name' => RolePermissionConstant::PERMISSION_VERSION_DELETE, 'parent_id' => 20, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 131, 'name' => RolePermissionConstant::PERMISSION_VERSION_CHANGE_STATUS, 'parent_id' => 20, 'created_at' => now(), 'updated_at' => now()],
            // System User Log
            ['id' => 132, 'name' => RolePermissionConstant::PERMISSION_SYSTEM_USER_LOG_VIEW, 'parent_id' => 21, 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('permissions')->insertOrIgnore($permissions);

        // Assign permission to role
        $permission = Permission::query()->pluck('id')->toArray();
        $roleOwner = Role::query()->where('name', UserConstant::ROLE_OWNER)->first();
        // Assign all permission to owner
        $roleOwner->hashPermission()->sync($permission);

    }
}
