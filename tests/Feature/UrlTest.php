<?php

namespace Tests\Feature;

use App\Models\User;
use AshAllenDesign\ShortURL\Models\ShortURL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_shorten_url_require_authentication()
    {
        $response = $this->postJson(route('urls.store'), [
            'url' => fake()->url,
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_shorten_url()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $url = fake()->url;
        $response = $this->postJson(route('urls.store'), [
            'url' => $url,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount('short_urls', 1);
    }

    public function test_only_admin_can_update_url()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $url = ShortURL::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin);
        $response = $this->patchJson(route('urls.update', $url));
        $response->assertOk();

        $this->actingAs($user);
        $response = $this->patchJson(route('urls.update', $url));
        $response->assertForbidden();
    }

    public function test_user_can_delete_its_own_shorted_url()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $url = ShortURL::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(route('urls.destroy', $url));

        $response->assertNoContent();
        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_user_cannot_delete_other_shorten_url()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $anotherUser = User::factory()->create();
        $url = ShortURL::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->deleteJson(route('urls.destroy', $url));
        $response->assertForbidden();
    }

    public function test_admin_can_delete_user_shorten_url()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $user = User::factory()->create();
        $url = ShortURL::factory()->create([
            'user_id' => $user->id,
        ]);

        $anotherUser = User::factory()->create();
        $anotherUrl = ShortURL::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->deleteJson(route('urls.destroy', $url));
        $response->assertNoContent();

        $anotherResponse = $this->deleteJson(route('urls.destroy', $anotherUrl));
        $anotherResponse->assertNoContent();
    }
}
