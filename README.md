# 禁煙支援アプリケーション

禁煙を志す人々をサポートするコミュニティアプリケーションです。禁煙の進捗管理、コミュニティ機能、バッジシステムを通じて、ユーザーの禁煙成功を支援します。

## 🚀 機能

### ユーザー機能
- **ユーザー認証**: 登録・ログイン・ログアウト
- **プロフィール管理**: 禁煙開始日、1日の喫煙本数、タバコ代の設定
- **禁煙進捗管理**: 禁煙期間の自動計算
- **バッジシステム**: 禁煙期間に応じたバッジ授与（1週間、1ヶ月、半年、1年）

### コミュニティ機能
- **投稿機能**: 禁煙の進捗や感想を投稿
- **コメント機能**: 他のユーザーの投稿にコメント
- **いいね機能**: 投稿へのいいね
- **ユーザープロフィール閲覧**: 他のユーザーの禁煙進捗を確認

### 管理者機能
- **ユーザー管理**: ユーザーの一覧・詳細・削除
- **投稿管理**: 投稿の一覧・詳細・削除
- **コメント管理**: コメントの一覧・詳細・削除

## 🛠 技術スタック

### バックエンド
- **PHP 8.2**
- **Laravel 12**
- **MySQL 8.0**
- **Redis** (キャッシュ・セッション管理)
- **Laravel Sanctum** (API認証)

### 開発・テスト
- **PHPUnit** (テストフレームワーク)
- **Laravel Sail** (開発環境)
- **Docker** (コンテナ化)

## 📁 プロジェクト構造

```
Quit_Smoking/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/ # APIコントローラー
│   │   │   ├── Requests/    # バリデーション
│   │   │   └── Middleware/  # ミドルウェア
│   │   ├── Models/          # Eloquentモデル
│   │   └── UseCases/        # ビジネスロジック
│   ├── tests/               # テストファイル
│   │   ├── Feature/         # 統合テスト
│   │   └── Unit/            # 単体テスト
│   └── routes/api.php       # APIルート
├── frontend/                # フロントエンド（将来実装予定）
└── docker/                  # Docker設定
```

## 🚀 セットアップ

### 前提条件
- PHP 8.2以上
- Composer
- MySQL 8.0
- Redis
- Docker (推奨)

### 1. リポジトリのクローン
```bash
git clone https://github.com/your-username/quit-smoking.git
cd quit-smoking
```

### 2. バックエンドのセットアップ
```bash
cd backend

# 依存関係のインストール
composer install

# 環境変数ファイルの作成
cp .env.example .env

# アプリケーションキーの生成
php artisan key:generate

# データベースの設定
# .envファイルでデータベース接続情報を設定

# マイグレーションの実行
php artisan migrate

# シーダーの実行（オプション）
php artisan db:seed

# Redisの起動
redis-server

# 開発サーバーの起動
php artisan serve
```

### 3. テストの実行
```bash
# 全テストの実行
php artisan test

# テストカバレッジの確認
php artisan test --coverage-text
```

## 📚 API ドキュメント

詳細なAPI仕様書は以下のファイルを参照してください：

- [API仕様書](./backend/API_DOCUMENTATION.md)
- [OpenAPI仕様](./backend/openapi.yaml)
- [API README](./backend/API_README.md)

### 主要なエンドポイント

#### 認証
- `POST /api/register` - ユーザー登録
- `POST /api/login` - ログイン
- `POST /api/logout` - ログアウト

#### ユーザープロフィール
- `GET /api/profile` - 自分のプロフィール取得
- `PUT /api/profile` - プロフィール更新
- `POST /api/profile/check-badges` - バッジチェック

#### コミュニティ
- `GET /api/posts` - 投稿一覧取得
- `POST /api/posts` - 投稿作成
- `GET /api/posts/{id}` - 投稿詳細取得
- `PUT /api/posts/{id}` - 投稿更新
- `DELETE /api/posts/{id}` - 投稿削除

## 🧪 テスト

プロジェクトには包括的なテストスイートが含まれています：

- **Feature Tests**: APIエンドポイントの統合テスト
- **Unit Tests**: ビジネスロジックの単体テスト
- **Request Tests**: バリデーションのテスト

### テストの実行
```bash
# 全テストの実行
php artisan test

# 特定のテストファイルの実行
php artisan test tests/Feature/Http/Controllers/AuthControllerTest.php

# テストカバレッジの確認
php artisan test --coverage-text
```

## 🔧 開発環境

### Docker環境の使用（推奨）
```bash
# Docker環境の起動
docker-compose up -d

# コンテナ内でコマンド実行
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan test
```

### キャッシュ管理
```bash
# キャッシュの状態確認
php artisan cache:status

# 全キャッシュクリア
php artisan cache:clear-all
```

## 📊 プロジェクトの特徴

### アーキテクチャ
- **クリーンアーキテクチャ**: ビジネスロジックとプレゼンテーション層の分離
- **UseCase層**: 複雑なビジネスロジックの集約
- **Repository Pattern**: データアクセス層の抽象化

### セキュリティ
- **Laravel Sanctum**: 安全なAPI認証
- **バリデーション**: 包括的な入力検証
- **SQLインジェクション対策**: Eloquent ORMの使用

### パフォーマンス
- **Redisキャッシュ**: 投稿一覧とユーザープロフィールのキャッシュ
- **データベース最適化**: 適切なインデックスとリレーション
- **N+1問題対策**: Eager Loadingの活用

## 🤝 コントリビューション

1. このリポジトリをフォーク
2. 機能ブランチを作成 (`git checkout -b feature/amazing-feature`)
3. 変更をコミット (`git commit -m 'Add some amazing feature'`)
4. ブランチにプッシュ (`git push origin feature/amazing-feature`)
5. プルリクエストを作成

## 📝 ライセンス

このプロジェクトはMITライセンスの下で公開されています。詳細は[LICENSE](LICENSE)ファイルを参照してください。

## 👨‍💻 作者

- **名前**: [あなたの名前]
- **GitHub**: [@your-username](https://github.com/your-username)

## 🙏 謝辞

- [Laravel](https://laravel.com/) - 素晴らしいPHPフレームワーク
- [Laravel Sanctum](https://laravel.com/docs/sanctum) - API認証
- [PHPUnit](https://phpunit.de/) - テストフレームワーク
