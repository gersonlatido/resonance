<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddDefaultAdminUser extends Migration
{
    public function up()
    {
        DB::table('users')->insert([
            'employee_id' => 'EMP001',  // or any format you use for employee ID
            'name' => 'Admin-DEMO',
            'username' => 'admin',
            'email' => 'admin@demo.com',  
            'password' => Hash::make('admin1234'), // Hash the password
            'position' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('users')->where('username', 'admin')->delete();
    }
}
