<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_search_creates_history_and_increments_total(): void
    {
        Category::query()->create(['name' => 'Fiction']);
        Book::query()->create([
            'title' => 'Test Book',
            'author' => 'A',
            'description' => 'About fiction stories.',
            'category' => 'Fiction',
            'year' => 2020,
            'keywords' => 'fiction, stories',
        ]);

        $user = User::factory()->create(['total_searches' => 0]);

        $this->actingAs($user)->get(route('search', ['q' => 'fiction stories']));

        $this->assertDatabaseHas('search_histories', [
            'user_id' => $user->id,
            'query' => 'fiction stories',
        ]);
        $this->assertSame(1, $user->fresh()->total_searches);
    }

    public function test_guest_search_does_not_create_history(): void
    {
        Category::query()->create(['name' => 'Fiction']);
        Book::query()->create([
            'title' => 'Test Book',
            'author' => 'A',
            'description' => 'Desc',
            'category' => 'Fiction',
            'year' => 2020,
            'keywords' => 'x',
        ]);

        $this->get(route('search', ['q' => 'test']));

        $this->assertSame(0, SearchHistory::query()->count());
    }

    public function test_activity_time_endpoint_updates_user(): void
    {
        $user = User::factory()->create(['time_spent' => 10]);

        $this->actingAs($user)->post(route('activity.time'), [
            'seconds' => 25,
        ])->assertOk();

        $this->assertSame(35, $user->fresh()->time_spent);
    }
}
