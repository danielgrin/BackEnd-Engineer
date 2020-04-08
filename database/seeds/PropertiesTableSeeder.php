<?php

use bfinlay\SpreadsheetSeeder\SpreadsheetSeeder;

class PropertiesTableSeeder extends SpreadsheetSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->defaults = ['guid' => Str::uuid()];
        parent::run();
    }
}
