# 禁煙支援アプリケーション（API + フロントエンド）

禁煙を志す人々をサポートするコミュニティアプリです。禁煙の進捗管理、コミュニティ機能、バッジシステムを通じてユーザーの禁煙成功を支援します。API は Laravel、フロントは Next.js で構成し、Docker でローカル/本番を統一運用します。

## 🚀 機能

### ユーザー
- **認証**: 登録 / ログイン / ログアウト / 現在ユーザー取得
- **プロフィール管理**: 禁煙開始日・喫煙本数・タバコ代の設定/更新、禁煙進捗の自動計算
- **バッジ**: 禁煙期間に応じたバッジ授与（1週間・1ヶ月・半年・1年）

### コミュニティ
- **投稿**: 作成・閲覧・更新・削除
- **コメント**: 作成・削除
- **いいね**: 付与・解除
- **ユーザープロフィール閲覧**: 他ユーザーの禁煙進捗の確認

### 管理者
- **ユーザー/投稿/コメント**: 一覧・詳細・削除（要管理者権限）

## 🛠 技術スタック

### バックエンド
- PHP 8.4（Composer 要件は ^8.2）
- Laravel 12
- MySQL 8.4 / Redis 7（キャッシュ・セッション）
- Laravel Sanctum（Bearer トークン認証）

### フロントエンド
- Next.js 15（App Router）/ React 19 / TypeScript
- Tailwind CSS / @tanstack/react-query

### 開発/運用
- Docker / docker compose（開発: `docker-compose.yml` / 本番: `docker-compose.prod.yml`, `deploy.sh`）

## 📁 プロジェクト構造

```
Quit_Smoking/
├── backend/                 # Laravel API
│   ├── app/
│   │   ├── Http/Controllers # 薄いコントローラ（業務ロジックは UseCases）
│   │   ├── Http/Requests    # バリデーション
│   │   ├── Http/Middleware
│   │   ├── Models           # Eloquent モデル
│   │   └── UseCases         # 業務ロジック（ユースケース層）
│   ├── routes/api.php       # API ルート定義
│   └── tests/{Feature,Unit}
├── frontend/                # Next.js フロント
│   └── src/{app,components,contexts,utils}
├── docker/
│   ├── backend/Dockerfile
│   └── frontend/{Dockerfile,Dockerfile.dev}
├── docker-compose.yml       # 開発用 compose
├── docker-compose.prod.yml  # 本番用 compose
└── deploy.sh                # デプロイスクリプト
```

## 🐳 セットアップ（Docker）

### 前提条件
- Docker Desktop / Git がインストール済み

### 1) リポジトリ取得
```bash
git clone <your-repo-url>
cd Quit_Smoking
```

### 2) 環境変数ファイル作成（バックエンド）
```bash
cd backend
cp .env.example .env
cd ..
```

### 3) 起動
```bash
docker compose up -d
```

### 4) 初期化
```bash
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate
# オプション: シーディング
docker compose exec backend php artisan db:seed
```

### 5) アクセス
- フロント: http://localhost:3000
- API: http://localhost:8000/api
- ヘルスチェック: http://localhost:8000/up

## 🔧 よく使うコマンド

### コンテナ管理
```bash
docker compose up -d              # 起動
docker compose down               # 停止
docker compose logs -f backend    # ログ
docker compose exec backend php artisan list
```

### テスト
```bash
# Backend
docker compose exec backend php artisan test

# Frontend
docker compose exec frontend npm test
```

### データベース
```bash
docker compose exec backend php artisan migrate
docker compose exec backend php artisan migrate:rollback
docker compose exec backend php artisan migrate:fresh --seed
```

### キャッシュ/運用
```bash
# カスタム: キャッシュ状態
docker compose exec backend php artisan cache:status

# カスタム: キャッシュ全消去（または posts/profiles を指定）
docker compose exec backend php artisan cache:clear-all
docker compose exec backend php artisan cache:clear-all --type=posts
docker compose exec backend php artisan cache:clear-all --type=profiles

# 設定キャッシュクリア
docker compose exec backend php artisan config:clear
```

## 🌐 主要エンドポイント（抜粋）

### 公開
- POST `/api/register`（登録）
- POST `/api/login`（ログイン）
- POST `/api/forgot-password`（再設定リンク送信）
- POST `/api/reset-password/{token}`（パスワード更新）

### 要認証（Sanctum）
- GET `/api/user`（現在ユーザー）
- GET `/api/profile`（自分のプロフィール）
- PATCH `/api/profile`（プロフィール更新）
- POST `/api/profile/check-badges`（バッジチェック）
- POST `/api/profile/reset`（禁煙情報リセット）
- GET `/api/user-profiles/{id}`（他ユーザーのプロフィール）

### 投稿/コメント/いいね（要認証）
- GET `/api/posts` / GET `/api/posts/{id}`
- POST `/api/posts` / PUT `/api/posts/{id}` / DELETE `/api/posts/{id}`
- POST `/api/posts/{postId}/comments` / DELETE `/api/posts/{postId}/comments/{commentId}`
- POST `/api/posts/{postId}/like` / POST `/api/posts/{postId}/unlike`

### 管理者（auth:sanctum, admin）
- GET `/api/admin/users` / GET `/api/admin/users/{id}` / DELETE `/api/admin/users/{id}`
- GET `/api/admin/posts` / GET `/api/admin/posts/{id}` / DELETE `/api/admin/posts/{id}`
- GET `/api/admin/comments` / GET `/api/admin/comments/{id}` / DELETE `/api/admin/comments/{id}`

## 📊 プロジェクトの特徴

### アーキテクチャ
- 薄いコントローラ + UseCase 層でビジネスロジックを集約
- フロント/バック分離（SPA + API）

### セキュリティ/運用
- Sanctum によるトークン認証
- CORS/SameSite/Stateful ドメイン設定

## 🔧 環境変数の要点（開発）
- フロント: `NEXT_PUBLIC_API_URL=http://localhost:8000/api`
- バック: `SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000`、`SESSION_DOMAIN=localhost`

## 📚 参考
- `PROJECT_OVERVIEW.md`（全体像と詳細）
- `QUICKSTART.md`（最速起動ガイド）
- `README-DEPLOYMENT.md`（デプロイ手順）

## 👨‍💻 作者
- 名前: 桐木 拓海
- GitHub: [@takum1m1](https://github.com/takum1m1)

## 今後の展望
- フロント機能拡張 / API 仕様の明文化
- 外部サービス連携
- 本番運用の強化（監視・ログ・セキュリティ）