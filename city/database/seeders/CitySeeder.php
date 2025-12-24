<?php

namespace Database\Seeders;

use App\Repositories\CityRepositoryInterface;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    protected CityRepositoryInterface $repository;

    public function __construct(CityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Clear existing? Maybe not, just append or we can make a clear command.
        // Let's generate 20 random cities.

        $this->command->info('Seeding 20 cities...');

        for ($i = 0; $i < 20; $i++) {
            $this->repository->create([
                'name' => $faker->city,
                'country' => $faker->country,
                'population' => $faker->numberBetween(100000, 10000000),
                'founded_at' => $faker->date('Y-m-d', '2000-01-01'),
            ]);
        }

        $this->command->info('Cities seeded successfully!');
    }
}
