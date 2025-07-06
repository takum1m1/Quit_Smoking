<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserController;

// ユーザー登録
Route::post('/register', [UserController::class, 'register']);
// ログイン
Route::post('/login', [UserController::class, 'login']);

/**********************************************
 * ログインユーザー用エンドポイント
 **********************************************/
Route::middleware('auth:sanctum')->group(function () {
    // ログアウト
    Route::post('/logout', [UserController::class, 'logout']);
    // アカウント削除
    Route::delete('/user', [UserController::class, 'destroy']);
    // ユーザープロフィール(自分の情報)
    Route::get('/profile', [UserProfileController::class, 'show']);
    // バッジ一覧
    Route::get('/profile/badges', [UserProfileController::class, 'getBadges']);
    // ユーザープロフィール更新
    Route::put('/profile', [UserProfileController::class, 'update']);
    // 禁煙情報リセット
    Route::post('/profile/reset', [UserProfileController::class, 'resetSmokingInfo']);
    // ユーザープロフィール取得(他のユーザーの情報)
    Route::get('/profile/{id}', [UserProfileController::class, 'show']);
    // 投稿一覧
    Route::get('/posts', [PostController::class, 'index']);
    // 投稿詳細
    Route::get('/posts/{id}', [PostController::class, 'show']);
    // 投稿作成
    Route::post('/posts', [PostController::class, 'store']);
    // 投稿更新
    Route::put('/posts/{id}', [PostController::class, 'update']);
    // 投稿削除
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    // コメント一覧
    Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);
    // コメント作成
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    // コメント更新
    Route::put('/posts/{postId}/comments/{commentId}', [CommentController::class, 'update']);
    // コメント削除
    Route::delete('/posts/{postId}/comments/{commentId}', [CommentController::class, 'destroy']);
    // いいね機能
    Route::post('/posts/{postId}/like', [LikeController::class, 'likePost']);
    // いいね解除
    Route::post('/posts/{postId}/unlike', [LikeController::class, 'unlikePost']);
    // いいね一覧取得
    Route::get('/posts/{postId}/likes', [LikeController::class, 'getLikes']);
});

/**********************************************
 * 管理者用エンドポイント
 **********************************************/
Route::middleware('auth:sanctum', 'admin')->group(function () {
    // ユーザー一覧
    Route::get('/admin/users', [UserController::class, 'index']);
    // ユーザー詳細
    Route::get('/admin/users/{id}', [UserController::class, 'show']);
    // ユーザーBAN
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
    // 投稿一覧
    Route::get('/admin/posts', [PostController::class, 'index']);
    // 投稿削除
    Route::delete('/admin/posts/{id}', [PostController::class, 'destroy']);
    // コメント一覧
    Route::get('/admin/comments', [CommentController::class, 'index']);
    // コメント削除
    Route::delete('/admin/comments/{id}', [CommentController::class, 'destroy']);
});
