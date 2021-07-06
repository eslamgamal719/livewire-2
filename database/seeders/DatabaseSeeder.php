<?php

namespace Database\Seeders;

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
        $images = glob(public_path('images/*.*'));  //to get all images from directory images to delete them before doing seed

        foreach($images as $image) {
            unlink($image);
        }


         \App\Models\User::factory(10)->create();
         \App\Models\Post::factory(50)->create();
    }
}