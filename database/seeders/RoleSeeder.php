<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new Role();
        $data->id = 1;
        $data->name = 'Super Admin';
        $data->guard_name = 'web';
        $data->save();

        $client = new Role();
        $client->id = 2;
        $client->name = 'Client';
        $client->guard_name = 'web';
        $client->save();

        $employee = new Role();
        $employee->id = 3;
        $employee->name = 'Employee';
        $employee->guard_name = 'web';
        $employee->save();

    }
}
