<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    /**
     * テスト用のユーザーを作成
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * テスト用のユーザープロフィールを作成
     */
    protected function createUserProfile(array $attributes = []): \App\Models\UserProfile
    {
        return \App\Models\UserProfile::factory()->create($attributes);
    }

    /**
     * テスト用の投稿を作成
     */
    protected function createPost(array $attributes = []): \App\Models\Post
    {
        return \App\Models\Post::factory()->create($attributes);
    }

    /**
     * テスト用のコメントを作成
     */
    protected function createComment(array $attributes = []): \App\Models\Comment
    {
        return \App\Models\Comment::factory()->create($attributes);
    }

    /**
     * 認証済みユーザーでAPIリクエストを送信
     */
    protected function authenticatedRequest(string $method, string $uri, array $data = [], array $headers = [])
    {
        $user = $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            ...$headers,
        ])->json($method, $uri, $data);
    }
}
