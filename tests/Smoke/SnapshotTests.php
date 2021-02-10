<?php

namespace Tests\Smoke;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class SnapshotTests extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function can_get_list_of_posts() {
        $response = $this->getJson('/api/posts');

        $this->assertMatchesJsonSnapshot($response->content());
    }

    /** @test */
    public function can_get_comments_for_post() {
        $response = $this->getJson('/api/posts/1/comments');
        $this->assertMatchesJsonSnapshot($response->content());
    }
}
