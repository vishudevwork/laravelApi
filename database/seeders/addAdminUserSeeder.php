<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class addAdminUserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userAdmin = User::updateOrCreate(
                        [
                            'name' => 'Admin',
                            'user_name' => 'admin',
                            'email' => 'admin@admin.com',
                            'user_role' => 'admin',
                        ],
                        [
                            'password' => bcrypt('admin123')
        ]);
        
    }

}
