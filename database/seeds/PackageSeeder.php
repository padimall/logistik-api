<?php

use Illuminate\Database\Seeder;
use App\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Package::class,1)->create();
    }
}
