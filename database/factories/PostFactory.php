<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'content' => $faker->text( 400),
        'date_written' => now(), //current date and time
        'featured_image' => $faker->imageUrl(),
        'votes_up' => $faker->numberBetween(1,100),
        'votes_down' => $faker->numberBetween(1,100),
        'user_id' => $faker->numberBetween(1,50), //we have 50 users in total as we seeded
        'category_id' => $faker->numberBetween(1,15), //we have only 15 category


    ];
});
