<?php

namespace Tests\Unit\UseCases\UserAccount;

use App\Models\User;
use App\Models\UserProfile;
use App\UseCases\UserAccount\DestroyAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DestroyActionTest extends TestCase
{
    use RefreshDatabase;

    private DestroyAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DestroyAction();
    }

    /**
     * 正常にアカウントを削除できることをテスト
     */
    public function test_destroy_account_successfully(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $user->id]);
        Auth::login($user);

        $this->action->__invoke($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        // UserProfileは削除されない（外部キー制約で削除される可能性はあるが、Actionでは直接削除しない）
    }

    /**
     * 未認証の場合のテスト
     */
    public function test_destroy_account_without_authentication(): void
    {
        $user = User::factory()->create();

        $this->action->__invoke($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * ユーザープロフィールが存在しない場合でも削除できることをテスト
     */
    public function test_destroy_account_without_profile(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->action->__invoke($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * 投稿やコメント、いいねがある場合でも削除できることをテスト
     */
    public function test_destroy_account_with_related_data(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $user->id]);

        // 関連データを作成（実際のモデルを使用）
        $post = \App\Models\Post::factory()->create(['user_id' => $user->id]);
        $comment = \App\Models\Comment::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);
        $like = \App\Models\Like::factory()->create(['user_id' => $user->id, 'post_id' => $post->id]);

        Auth::login($user);

        $this->action->__invoke($user);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        // 関連データは外部キー制約で削除される可能性があるが、Actionでは直接削除しない
    }

    /**
     * 他のユーザーに影響がないことをテスト
     */
    public function test_destroy_account_does_not_affect_other_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $user1->id]);
        UserProfile::factory()->create(['user_id' => $user2->id]);

        Auth::login($user1);

        $this->action->__invoke($user1);

        // user1は削除される
        $this->assertSoftDeleted('users', ['id' => $user1->id]);

        // user2は影響を受けない
        $this->assertDatabaseHas('users', ['id' => $user2->id]);
        $this->assertDatabaseHas('user_profiles', ['user_id' => $user2->id]);
    }
}
