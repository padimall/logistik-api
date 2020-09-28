<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Tracking;
use Faker\Generator as Faker;

$factory->define(Tracking::class, function (Faker $faker) {
    return [
        'package_id' => function(){
            return App\Package::inRandomOrder()->pluck('id')->first();
        },
        'user_id' => function(){
            return App\User::inRandomOrder()->pluck('id')->first();
        },
        'location' => 'Medan',
        'detail' => 'Paket akan dikirimkan ke gateway Sibolga'
    ];
});
