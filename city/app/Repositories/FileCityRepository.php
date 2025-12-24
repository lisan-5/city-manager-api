<?php

namespace App\Repositories;

use App\Models\City;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class FileCityRepository implements CityRepositoryInterface
{
    private string $filePath = 'cities.json';
    private string $cacheKey = 'cities_data';

    /** @var Collection<int, City> */
    private Collection $cities;

    public function __construct()
    {
        $this->load();
    }

    private function load(): void
    {
        /** @var Collection<int, City> $data */
        $data = Cache::rememberForever($this->cacheKey, function () {
            if (! Storage::disk('local')->exists($this->filePath)) {
                return collect([]);
            }

            $json = Storage::disk('local')->get($this->filePath);
            $decoded = json_decode((string)$json, true);
            $data = is_array($decoded) ? $decoded : [];

            return collect($data)->map(function ($item) {
                if (!is_array($item)) return null;
                return City::fromArray($item);
            })->filter(fn($item) => $item !== null);
        });

        $this->cities = $data;
    }

    private function save(): void
    {
        $data = $this->cities->map(fn (City $city) => $city->toArray())->values()->all();
        
        // Write to file
        Storage::disk('local')->put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
        
        // Update Cache
        Cache::put($this->cacheKey, $this->cities);
    }

    public function all(): Collection
    {
        return $this->cities;
    }

    public function find(string $id): ?City
    {
        return $this->cities->first(fn (City $city) => $city->id === $id);
    }

    public function create(array $data): City
    {
        $data['id'] = uniqid();
        $city = City::fromArray($data);
        $this->cities->push($city);
        $this->save();

        return $city;
    }

    public function update(string $id, array $data): ?City
    {
        $key = $this->cities->search(fn (City $city) => $city->id === $id);

        if ($key === false) {
            return null;
        }

        $existingCity = $this->cities->get($key);
        $updatedData = array_merge($existingCity->toArray(), $data);
        $updatedCity = City::fromArray($updatedData);

        $this->cities->put($key, $updatedCity);
        $this->save();

        return $updatedCity;
    }

    public function delete(string $id): bool
    {
        $key = $this->cities->search(fn (City $city) => $city->id === $id);

        if ($key === false) {
            return false;
        }

        $this->cities->forget($key);
        $this->save();

        return true;
    }

    /**
     * @return array{data: Collection<int, City>, meta: array<string, int|float>}
     */
    public function paginate(int $perPage = 15, int $page = 1, ?string $search = null, string $sortBy = 'created_at', string $sortOrder = 'desc'): array
    {
        $query = $this->cities;

        // Search
        if ($search) {
            $query = $query->filter(function (City $city) use ($search) {
                return str_contains(strtolower($city->name), strtolower($search)) ||
                       str_contains(strtolower($city->country), strtolower($search));
            });
        }

        // Sort
        $query = $query->sortBy(function (City $city) use ($sortBy) {
            return $city->{$sortBy} ?? $city->founded_at;
        }, SORT_REGULAR, $sortOrder === 'desc');

        $total = $query->count();
        $items = $query->slice(($page - 1) * $perPage, $perPage)->values();

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ];
    }
}
