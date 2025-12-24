<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $country
 * @property int $population
 * @property string|null $founded_at
 */
class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'population' => $this->population,
            'founded_at' => $this->founded_at,
            'links' => [
                'self' => route('cities.show', $this->id),
            ],
        ];
    }
}
