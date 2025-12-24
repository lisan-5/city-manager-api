<?php

namespace App\Repositories;

use App\Models\City;
use Illuminate\Support\Collection;

interface CityRepositoryInterface
{
    public function all(): Collection;

    public function find(string $id): ?City;

    public function create(array $data): City;

    public function update(string $id, array $data): ?City;

    public function delete(string $id): bool;

    public function paginate(int $perPage = 15, int $page = 1, ?string $search = null, string $sortBy = 'created_at', string $sortOrder = 'desc'): array;
}
