<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RusunawaSeeder;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdvancedRoomFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (
            !app()->environment('testing') ||
            config('database.default') !== 'sqlite' ||
            config('database.connections.sqlite.database') !== ':memory:' ||
            !empty(config('database.connections.sqlite.url'))
        ) {
            throw new \RuntimeException(
                'Testing wajib menggunakan SQLite in-memory.'
            );
        }

        Artisan::call('migrate:fresh');
        $this->seed(RusunawaSeeder::class);

        Sanctum::actingAs(
            User::where('email', 'student1@example.test')->firstOrFail()
        );
    }

    public function test_default_room_list(): void
    {
        $this->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonCount(6, 'data')
            ->assertJsonPath('data.0.id', 'A101');
    }

    public function test_filter_by_building(): void
    {
        $this->getJson('/api/rooms?building=Gedung%20A')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', 'A101')
            ->assertJsonPath('data.1.id', 'A102');
    }

    public function test_filter_by_minimum_capacity(): void
    {
        $response = $this->getJson('/api/rooms?min_capacity=2')
            ->assertOk()
            ->assertJsonCount(4, 'data');

        foreach ($response->json('data') as $room) {
            $this->assertGreaterThanOrEqual(2, $room['capacity']);
        }
    }

    public function test_sort_capacity_ascending(): void
    {
        $response = $this->getJson('/api/rooms?sort=capacity_asc')
            ->assertOk();

        $this->assertSame(
            ['C103', 'D14', 'A101', 'A102', 'B201', 'D202'],
            array_column($response->json('data'), 'id')
        );
    }

    public function test_sort_capacity_descending(): void
    {
        $response = $this->getJson('/api/rooms?sort=capacity_desc')
            ->assertOk();

        $this->assertSame(
            ['A101', 'A102', 'B201', 'D202', 'C103', 'D14'],
            array_column($response->json('data'), 'id')
        );
    }

    public function test_sort_available_date_ascending(): void
    {
        $response = $this->getJson('/api/rooms?sort=available_from_asc')
            ->assertOk();

        $this->assertSame(
            ['A101', 'A102', 'C103', 'D14', 'B201', 'D202'],
            array_column($response->json('data'), 'id')
        );
    }

    public function test_sort_available_date_descending(): void
    {
        $response = $this->getJson('/api/rooms?sort=available_from_desc')
            ->assertOk();

        $this->assertSame(
            ['D202', 'B201', 'A101', 'A102', 'C103', 'D14'],
            array_column($response->json('data'), 'id')
        );
    }

    public function test_combined_filters(): void
    {
        $this->getJson(
            '/api/rooms?building=Gedung%20A'
            .'&min_capacity=2&gender=Putra'
            .'&available=1&sort=capacity_desc'
        )
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', 'A101')
            ->assertJsonPath('data.1.id', 'A102');
    }

    public function test_empty_filter_result(): void
    {
        $this->getJson('/api/rooms?min_capacity=999')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    }

    public function test_invalid_capacity(): void
    {
        $this->getJson('/api/rooms?min_capacity=-1')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('min_capacity');
    }

    public function test_invalid_sort(): void
    {
        $this->getJson('/api/rooms?sort=invalid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort');
    }

    public function test_filtered_pagination(): void
    {
        $this->getJson(
            '/api/rooms?min_capacity=2'
            .'&sort=capacity_desc&per_page=2&page=2'
        )
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 4)
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('data.0.id', 'B201')
            ->assertJsonPath('data.1.id', 'D202');
    }

    public function test_favorite_status_preserved(): void
    {
        $this->getJson('/api/rooms?building=Gedung%20A')
            ->assertOk()
            ->assertJsonPath('data.0.id', 'A101')
            ->assertJsonPath('data.0.is_favorite', true)
            ->assertJsonPath('data.1.is_favorite', false);
    }

    public function test_filter_reset(): void
    {
        $this->getJson('/api/rooms?building=Gedung%20A')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/rooms')
            ->assertOk()
            ->assertJsonCount(6, 'data');
    }
}