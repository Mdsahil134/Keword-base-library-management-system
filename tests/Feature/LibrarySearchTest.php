<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibrarySearchTest extends TestCase
{
    use RefreshDatabase;

    private function seedCatalogue(): void
    {
        Category::query()->create(['name' => 'Computer Science']);
        Category::query()->create(['name' => 'Fiction']);

        Book::query()->create([
            'title' => 'Deep Learning Foundations',
            'author' => 'A. Researcher',
            'description' => 'Neural networks and optimization methods for practitioners.',
            'category' => 'Computer Science',
            'year' => 2023,
            'keywords' => 'machine learning, neural networks',
        ]);

        Book::query()->create([
            'title' => 'The Quiet Garden',
            'author' => 'B. Poet',
            'description' => 'A literary novel about memory and seasons.',
            'category' => 'Fiction',
            'year' => 2019,
            'keywords' => 'novel, literary, seasons',
        ]);
    }

    public function test_home_page_loads(): void
    {
        $this->seedCatalogue();

        $this->get(route('home'))->assertOk()->assertSee('Library intelligence dashboard', false);
    }

    public function test_empty_search_lists_all_books_alphabetically(): void
    {
        $this->seedCatalogue();

        $response = $this->get(route('search', ['q' => '']));
        $response->assertOk();
        $response->assertSee('Deep Learning Foundations', false);
        $response->assertSee('The Quiet Garden', false);
    }

    public function test_single_keyword_filters_results(): void
    {
        $this->seedCatalogue();

        $response = $this->get(route('search', ['q' => 'learning']));
        $response->assertOk();
        $plain = strtolower(strip_tags($response->getContent()));
        $this->assertStringContainsString('deep learning foundations', $plain);
        $this->assertStringNotContainsString('the quiet garden', $plain);
    }

    public function test_multiple_keywords_require_all_terms(): void
    {
        $this->seedCatalogue();

        $response = $this->get(route('search', ['q' => 'machine literary']));
        $response->assertOk();
        $response->assertSee('No results found', false);
    }

    public function test_results_sorted_by_relevance_score(): void
    {
        $this->seedCatalogue();

        Book::query()->create([
            'title' => 'Learning to Relax',
            'author' => 'C. Coach',
            'description' => 'Wellness and mindfulness exercises.',
            'category' => 'Fiction',
            'year' => 2021,
            'keywords' => 'relaxation, wellness',
        ]);

        $response = $this->get(route('search', ['q' => 'learning']));
        $response->assertOk();
        $plain = strtolower(strip_tags($response->getContent()));
        $posDeep = strpos($plain, 'deep learning foundations');
        $posRelax = strpos($plain, 'learning to relax');
        $this->assertNotFalse($posDeep);
        $this->assertNotFalse($posRelax);
        $this->assertLessThan($posRelax, $posDeep, 'Higher relevance should appear first');
    }

    public function test_filter_by_category(): void
    {
        $this->seedCatalogue();

        $response = $this->get(route('search', [
            'q' => 'novel',
            'category' => 'Fiction',
        ]));
        $response->assertOk();
        $response->assertSee('The Quiet Garden', false);
        $response->assertDontSee('Deep Learning Foundations', false);
    }

    public function test_suggest_returns_titles(): void
    {
        $this->seedCatalogue();

        $this->getJson(route('suggest', ['q' => 'Deep']))
            ->assertOk()
            ->assertJsonPath('suggestions.0', 'Deep Learning Foundations');
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_user_can_log_in_and_reach_admin(): void
    {
        $user = User::factory()->create([
            'email' => 'librarian@example.com',
            'password' => 'secret123',
            'is_admin' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'librarian@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_non_admin_user_cannot_reach_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.dashboard'))->assertForbidden();
    }
}
