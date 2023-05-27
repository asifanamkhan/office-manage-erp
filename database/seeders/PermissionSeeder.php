<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('permissions')->delete();
        // $permission = array(
        //     array('name' => "create_client",'guard_name' => 'web'),
        //     array('name' => "edit_client",'guard_name' => 'web'),
        //     array('name' => "delete_client",'guard_name' => 'web'),
        //     array('name' => "create_employee",'guard_name' => 'web'),
        //     array('name' => "edit_employee",'guard_name' => 'web'),
        //     array('name' => "delete_employee",'guard_name' => 'web'),
        // );
        // DB::table('permissions')->insert($permission);
        DB::table('permissions')->insert([
            [
                'id' => 1,
                'name' => 'access_role_permission',
                'guard_name' => 'web',
            ],
            [
                'id' => 2,
                'name' => 'create_client',
                'guard_name' => 'web',
            ],
            [
                'id' => 3,
                'name' => 'edit_client',
                'guard_name' => 'web',
            ],
            [
                'id' => 4,
                'name' => 'delete_client',
                'guard_name' => 'web',
            ],
            [
                'id' => 5,
                'name' => 'create_employee',
                'guard_name' => 'web',
            ],
            [
                'id' => 6,
                'name' => 'edit_employee',
                'guard_name' => 'web',
            ],
            [
                'id' => 7,
                'name' => 'delete_employee',
                'guard_name' => 'web',
            ],
            [
                'id' => 8,
                'name' => 'access_account',
                'guard_name' => 'web',
            ],

        ]);

    }
}
