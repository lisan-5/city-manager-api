<?php

namespace App\Models;

class City
{
    /**
     * @OA\Schema(
     *     schema="City",
     *     title="City Resource",
     *
     *     @OA\Property(property="id", type="string", example="676a91b..."),
     *     @OA\Property(property="name", type="string", example="Tokyo"),
     *     @OA\Property(property="country", type="string", example="Japan"),
     *     @OA\Property(property="population", type="integer", example=14000000),
     *     @OA\Property(property="founded_at", type="string", format="date", example="1457-01-01")
     * )
     */
    public string $id;

    public string $name;

    public string $country;

    public int $population;

    public string $founded_at;

    public function __construct(string $id, string $name, string $country, int $population, string $founded_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->country = $country;
        $this->population = $population;
        $this->founded_at = $founded_at;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? uniqid(),
            $data['name'],
            $data['country'],
            $data['population'],
            $data['founded_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'population' => $this->population,
            'founded_at' => $this->founded_at,
        ];
    }
}
