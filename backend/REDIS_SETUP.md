# Redis設定の修正と活用

## 修正内容

### 1. Docker環境変数の修正

**修正前:**
```yaml
CACHE_DRIVER: redis
QUEUE_CONNECTION: sync
SESSION_DRIVER: file
```

**修正後:**
```yaml
CACHE_STORE: redis
QUEUE_CONNECTION: redis
SESSION_DRIVER: redis
```

### 2. キャッシュ機能の実装

#### A. 投稿一覧キャッシュ
- **ファイル**: `app/UseCases/Community/ListPostsAction.php`
- **キャッシュ時間**: 15分（900秒）
- **キー**: `posts.all`

```php
$posts = Cache::remember('posts.all', 900, function () {
    return Post::with(['user.profile', 'comments', 'likes'])->get();
});
```

#### B. ユーザープロフィールキャッシュ
- **ファイル**: `app/UseCases/UserProfile/GetMyProfileAction.php`
- **キャッシュ時間**: 30分（1800秒）
- **キー**: `user.profile.{user_id}`

```php
$profileData = Cache::remember("user.profile.{$user->id}", 1800, function () use ($user) {
    // プロフィール計算処理
});
```

### 3. キャッシュ自動クリア機能

#### A. 投稿関連
- **投稿作成時**: `CreatePostAction.php`
- **投稿更新時**: `UpdatePostAction.php`
- **投稿削除時**: `DeletePostAction.php`

```php
Cache::forget('posts.all');
```

#### B. プロフィール関連
- **プロフィール更新時**: `UpdateAction.php`
- **バッジ授与時**: `CheckAndAwardBadgesAction.php`

```php
Cache::forget("user.profile.{$user->id}");
```

### 4. キャッシュ管理コマンド

#### A. キャッシュ状態確認
```bash
php artisan cache:status
```

**出力例:**
```
=== Cache Status ===
✅ Redis connection: OK
📊 Redis keys: 0
💾 Memory used: 1.02 MB

=== Application Cache ===
❌ Posts cache: Not available
🔧 Cache driver: redis

=== Cache Configuration ===
Default store: redis
Redis host: redis
Redis port: 6379
Cache prefix: quit_smoking_cache_
```

#### B. キャッシュクリア
```bash
# 全キャッシュクリア
php artisan cache:clear-all

# 投稿キャッシュのみクリア
php artisan cache:clear-all --type=posts

# プロフィールキャッシュのみクリア
php artisan cache:clear-all --type=profiles
```

## 使用方法

### 1. 開発環境での実行

```bash
# Docker環境を起動
docker compose up -d

# キャッシュ状態確認
docker exec quit_smoking-api php artisan cache:status

# テスト実行
docker exec quit_smoking-api php artisan test
```

### 2. 本番環境での設定

#### A. 環境変数
```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=your-redis-host
REDIS_PORT=6379
REDIS_PASSWORD=your-redis-password
```

#### B. Redis設定
```php
// config/database.php
'redis' => [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
],
```

## パフォーマンス向上

### 1. 期待される効果

- **投稿一覧取得**: データベースクエリ削減により高速化
- **プロフィール取得**: 計算処理のキャッシュにより高速化
- **セッション管理**: Redisによる高速なセッション処理

### 2. キャッシュ戦略

#### A. 読み取り頻度の高いデータ
- 投稿一覧（15分キャッシュ）
- ユーザープロフィール（30分キャッシュ）

#### B. 自動更新
- データ変更時に自動的にキャッシュをクリア
- 常に最新のデータを提供

#### C. 段階的キャッシュ
- 短時間キャッシュで頻繁にアクセスされるデータ
- 長時間キャッシュで計算コストの高いデータ

## 監視とメンテナンス

### 1. キャッシュヒット率の監視

```bash
# Redis統計情報の確認
docker exec quit_smoking-redis redis-cli info stats
```

### 2. メモリ使用量の監視

```bash
# メモリ使用量の確認
docker exec quit_smoking-redis redis-cli info memory
```

### 3. 定期的なキャッシュクリア

```bash
# 毎日午前2時に全キャッシュクリア
0 2 * * * docker exec quit_smoking-api php artisan cache:clear-all
```

## トラブルシューティング

### 1. Redis接続エラー

**エラー**: `Class "Redis" not found`
**解決策**: Docker環境で実行するか、PHP Redis拡張機能をインストール

### 2. キャッシュが更新されない

**原因**: キャッシュクリア処理が実行されていない
**解決策**: 手動でキャッシュをクリア
```bash
php artisan cache:clear-all
```

### 3. メモリ不足

**原因**: キャッシュデータが多すぎる
**解決策**: キャッシュ時間を短縮するか、定期的にクリア

## 今後の拡張

### 1. 追加キャッシュ対象

- **バッジ計算結果**: 禁煙日数計算のキャッシュ
- **統計情報**: ユーザー統計のキャッシュ
- **検索結果**: 投稿検索結果のキャッシュ

### 2. キャッシュ戦略の最適化

- **TTL（Time To Live）の調整**: アクセスパターンに応じた最適化
- **キャッシュ階層**: 複数レベルのキャッシュ戦略
- **キャッシュ予熱**: 重要なデータの事前キャッシュ

### 3. 監視とアラート

- **キャッシュヒット率の監視**
- **メモリ使用量のアラート**
- **キャッシュパフォーマンスの分析** 
