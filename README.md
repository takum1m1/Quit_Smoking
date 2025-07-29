# 禁煙支援アプリケーションAPI

禁煙を志す人々をサポートするコミュニティアプリケーションのAPIです。禁煙の進捗管理、コミュニティ機能、バッジシステムを通じて、ユーザーの禁煙成功を支援します。

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
- **PHP 8.4**
- **Laravel 12**
- **MySQL 8.4**
- **Redis 7** (キャッシュ・セッション管理)
- **Laravel Sanctum** (API認証)

### 開発環境
- **Docker** (コンテナ化)
- **Docker Compose** (マルチコンテナ管理)

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
├── docker/                  # Docker設定
│   └── backend/
│       └── Dockerfile       # PHP環境の設定
├── docker-compose.yml       # コンテナ構成
└── frontend/                # フロントエンド（将来実装予定）
```

## 🐳 Docker環境でのセットアップ

### 前提条件
- **Docker Desktop** がインストールされていること
- **Git** がインストールされていること

### 🚀 簡単セットアップ（推奨）

#### 1. リポジトリのクローン
```bash
git clone https://github.com/your-username/quit-smoking.git
cd quit-smoking
```

#### 2. 環境変数ファイルの作成
```bash
# バックエンドディレクトリに移動
cd backend

# 環境変数ファイルを作成
cp .env.example .env
```

#### 3. Docker環境の起動
```bash
# プロジェクトルートに戻る
cd ..

# Dockerコンテナを起動
docker-compose up -d
```

#### 4. アプリケーションの初期化
```bash
# アプリケーションキーの生成
docker-compose exec backend php artisan key:generate

# データベースのマイグレーション
docker-compose exec backend php artisan migrate

# テストデータの作成（オプション）
docker-compose exec backend php artisan db:seed
```

#### 5. アクセス確認
- **API**: http://localhost:8000/api
- **ヘルスチェック**: http://localhost:8000/api/health

### 🔧 開発用コマンド

#### コンテナの管理
```bash
# コンテナの起動
docker-compose up -d

# コンテナの停止
docker-compose down

# ログの確認
docker-compose logs backend

# コンテナ内でコマンド実行
docker-compose exec backend php artisan list
```

#### テストの実行
```bash
# 全テストの実行
docker-compose exec backend php artisan test

# 特定のテストファイル
docker-compose exec backend php artisan test tests/Feature/Http/Controllers/AuthControllerTest.php

# テストカバレッジ
docker-compose exec backend php artisan test --coverage-text
```

#### データベース操作
```bash
# マイグレーション
docker-compose exec backend php artisan migrate

# ロールバック
docker-compose exec backend php artisan migrate:rollback

# シーダー実行
docker-compose exec backend php artisan db:seed

# データベースリセット
docker-compose exec backend php artisan migrate:fresh --seed
```

#### キャッシュ管理
```bash
# キャッシュの状態確認
docker-compose exec backend php artisan cache:status

# 全キャッシュクリア
docker-compose exec backend php artisan cache:clear-all

# 設定キャッシュクリア
docker-compose exec backend php artisan config:clear
```

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
docker-compose exec backend php artisan test

# 特定のテストファイルの実行
docker-compose exec backend php artisan test tests/Feature/Http/Controllers/AuthControllerTest.php
```

## 📊 プロジェクトの特徴

### アーキテクチャ
- **クリーンアーキテクチャ**: ビジネスロジックとプレゼンテーション層の分離
- **UseCase層**: 複雑なビジネスロジックの集約

### セキュリティ
- **Laravel Sanctum**: 安全なAPI認証
- **バリデーション**: 包括的な入力検証

### パフォーマンス
- **Redisキャッシュ**: 投稿一覧とユーザープロフィールのキャッシュ

## 👨‍💻 作者

- **名前**: [桐木 拓海]
- **GitHub**: [@takum1m1](https://github.com/takum1m1)

## 今後の展望
- フロントエンドの実装
- 外部サービス連携
- デプロイ、リリースの経験
- さらにAIを活用した効率的な開発
- API仕様書等の作成