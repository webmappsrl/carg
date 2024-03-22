<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  \App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Webmapp',
            'email' => 'admin@webmapp.it',
            'password' => bcrypt('webmapp123'),
        ]);
        $this->call([
            ConfFeatureCollectionSeeder::class
        ]);
    }
}
