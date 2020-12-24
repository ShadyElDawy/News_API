<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory( \App\User::class, 50 ) -> create();  //Every one of 50 will use /factories/UserFactory.php
    }
}
