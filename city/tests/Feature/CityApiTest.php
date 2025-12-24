<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CityApiTest extends TestCase
{
    private string $apiKey = 'secret-api-key';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::disk('local')->delete('cities.json');
        Cache::forget('cities_data');
    }

    public function test_can_list_cities()
    {
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson('/api/cities');

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'message', 'data', 'meta']);
    }

    public function test_can_create_city()
    {
        $data = [
            'name' => 'Tokyo',
            'country' => 'Japan',
            'population' => 14000000,
            'founded_at' => '1457-01-01',
        ];

        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/api/cities', $data);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Tokyo');
    }

    public function test_can_show_city()
    {
        $data = [
            'name' => 'London',
            'country' => 'UK',
            'population' => 9000000,
            'founded_at' => '0047-01-01',
        ];
        $createResponse = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/api/cities', $data);
        $id = $createResponse->json('data.id');

        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson("/api/cities/{$id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $id);
    }

    public function test_can_update_city()
    {
        $data = [
            'name' => 'Paris',
            'country' => 'France',
            'population' => 2000000,
            'founded_at' => '0250-01-01',
        ];
        $createResponse = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/api/cities', $data);
        $id = $createResponse->json('data.id');

        $updateData = ['population' => 2148000];
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->putJson("/api/cities/{$id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('data.population', 2148000);
    }

    public function test_can_delete_city()
    {
        $data = [
            'name' => 'Berlin',
            'country' => 'Germany',
            'population' => 3600000,
            'founded_at' => '1237-01-01',
        ];
        $createResponse = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/api/cities', $data);
        $id = $createResponse->json('data.id');

        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->deleteJson("/api/cities/{$id}");

        $response->assertStatus(200);

        $getIds = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson("/api/cities/{$id}");
        // Global exception handler should catch 404
        $getIds->assertStatus(404)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'City not found.');
    }

    public function test_can_search_cities()
    {
        $this->withHeaders(['X-API-KEY' => $this->apiKey])->postJson('/api/cities', ['name' => 'New York', 'country' => 'USA', 'population' => 8000000, 'founded_at' => '1624-01-01']);
        $this->withHeaders(['X-API-KEY' => $this->apiKey])->postJson('/api/cities', ['name' => 'York', 'country' => 'UK', 'population' => 200000, 'founded_at' => '0071-01-01']);
        $this->withHeaders(['X-API-KEY' => $this->apiKey])->postJson('/api/cities', ['name' => 'Madrid', 'country' => 'Spain', 'population' => 3000000, 'founded_at' => '0865-01-01']);

        // Search for 'York' should return 2 results
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson('/api/cities?search=york');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_sort_cities()
    {
        $this->withHeaders(['X-API-KEY' => $this->apiKey])->postJson('/api/cities', ['name' => 'A City', 'country' => 'A', 'population' => 100, 'founded_at' => '2000-01-01']);
        $this->withHeaders(['X-API-KEY' => $this->apiKey])->postJson('/api/cities', ['name' => 'B City', 'country' => 'B', 'population' => 200, 'founded_at' => '2001-01-01']);

        // Sort desc by population
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson('/api/cities?sort_by=population&sort_order=desc');

        $this->assertEquals('B City', $response->json('data.0.name'));

        // Sort asc by population
        $responseAsc = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson('/api/cities?sort_by=population&sort_order=asc');

        $this->assertEquals('A City', $responseAsc->json('data.0.name'));
        $this->assertEquals('A City', $responseAsc->json('data.0.name'));
    }

    public function test_auth_fails_without_key()
    {
        $response = $this->getJson('/api/cities');
        $response->assertStatus(401)
                 ->assertJson(['status' => 'error', 'message' => 'Unauthorized']);
    }
}
