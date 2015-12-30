<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\UserRepository;

class UserSeeder extends Seeder {

    public function run()
    {
        $config = app()->make('config');

        DB::table("users")->insert([
            'name' => 'Gemini Web',
            'email' => $config->get('secrets.admin_email'),
            'password' => Hash::make($config->get('secrets.admin_password')),
            'owner_id' => ''
        ]);
    }

}

?>
