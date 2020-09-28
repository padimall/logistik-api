<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Package;
use Faker\Generator as Faker;

$factory->define(Package::class, function (Faker $faker) {
    return [
        'user_id' => function(){
            return App\User::inRandomOrder()->pluck('id')->first();
        },
        'type' => 'Makanan',
        'origin' => 'Medan',
        'sender' => $faker->name,
        'sender_contact' => $faker->phoneNumber,
        'receiver' => $faker->name,
        'receiver_contact' => $faker->phoneNumber,
        'receiver_province' => 'Sumatera Utara',
        'receiver_city' => 'Kota Sibolga',
        'receiver_district' => 'Sibolga Utara',
        'receiver_post_code' => '22513',
        'address'=>'Jl. Oswald Siahaan no.18, Panomboman arah laut',
        'weight' => 1,
        'price' => 180000
    ];
});
