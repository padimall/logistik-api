<?php

use Illuminate\Database\Seeder;
use App\Tracking;

class TrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Tracking::class,1)->create();
    }
}
