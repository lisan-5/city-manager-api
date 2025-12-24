<?php

namespace App\Console\Commands;

use App\Repositories\CityRepositoryInterface;
use Illuminate\Console\Command;

class CityStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'city:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display statistics about the stored cities';

    /**
     * Execute the console command.
     */
    public function handle(CityRepositoryInterface $repository)
    {
        $cities = $repository->all();
        $count = $cities->count();

        if ($count === 0) {
            $this->info('No cities found.');
            return;
        }

        $totalPop = $cities->sum('population');
        $avgPop = $cities->avg('population');
        $oldest = $cities->sortBy('founded_at')->first();
        $newest = $cities->sortByDesc('founded_at')->first();

        $this->info("City Database Statistics");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Cities', $count],
                ['Total Population', number_format($totalPop)],
                ['Average Population', number_format($avgPop)],
                ['Oldest City', "{$oldest->name} ({$oldest->founded_at})"],
                ['Newest City', "{$newest->name} ({$newest->founded_at})"],
            ]
        );
    }
}
