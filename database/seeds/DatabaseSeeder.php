<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Stop Laravel from freaking out during seeding with FK constraints.

        $this->call([
            PropertiesTableSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
