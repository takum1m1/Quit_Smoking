<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use Illuminate\Support\Facades\Route;
// 一般用
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;

// ユーザー登録
Route::post('/register', [AuthController::class, 'register']);
// ログイン
Route::post('/login', [AuthController::class, 'login']);
// パスワード再設定リンクの送信依頼
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.request');
// パスワードリセット
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])
    ->name('password.reset');

/**********************************************
 * ログインユーザー用エンドポイント
 **********************************************/
Route::middleware('auth:sanctum')->group(function () {
    // ログアウト
    Route::post('/logout', [AuthController::class, 'logout']);
    // アカウント削除
    Route::delete('/user', [AuthController::class, 'destroy']);
    // ユーザープロフィール(自分の情報)
    Route::get('/profile', [UserProfileController::class, 'myProfile']);
    // バッジチェック
    Route::post('/profile/check-badges', [UserProfileController::class, 'checkBadges']);
    // ユーザープロフィール更新
    Route::patch('/profile', [UserProfileController::class, 'update']);
    // 禁煙情報リセット
    Route::post('/profile/reset', [UserProfileController::class, 'resetQuitInfo']);
    // ユーザープロフィール取得(他のユーザーの情報)
    Route::get('/profile/{id}', [UserProfileController::class, 'showById']);
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
    // コメント作成
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    // コメント削除
    Route::delete('/posts/{postId}/comments/{commentId}', [CommentController::class, 'destroy']);
    // いいね機能
    Route::post('/posts/{postId}/like', [LikeController::class, 'likePost']);
    // いいね解除
    Route::post('/posts/{postId}/unlike', [LikeController::class, 'unlikePost']);
});

/**********************************************
 * 管理者用エンドポイント
 **********************************************/
Route::middleware('auth:sanctum', 'admin')->group(function () {
    // ユーザー一覧
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    // ユーザー詳細
    Route::get('/admin/users/{id}', [AdminUserController::class, 'show']);
    // ユーザーBAN
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
    // 投稿一覧
    Route::get('/admin/posts', [AdminPostController::class, 'index']);
    // 投稿詳細
    Route::get('/admin/posts/{id}', [AdminPostController::class, 'show']);
    // 投稿削除
    Route::delete('/admin/posts/{id}', [AdminPostController::class, 'destroy']);
    // コメント一覧
    Route::get('/admin/comments', [AdminCommentController::class, 'index']);
    // コメント詳細
    Route::get('/admin/comments/{id}', [AdminCommentController::class, 'show']);
    // コメント削除
    Route::delete('/admin/comments/{id}', [AdminCommentController::class, 'destroy']);
});
