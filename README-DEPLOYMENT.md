# QuitSmoking デプロイメントガイド

## 環境要件

- Docker 20.10+
- Docker Compose 2.0+
- 最低2GB RAM
- 最低10GB ディスク容量

## 開発環境の起動

```bash
# リポジトリのクローン
git clone <repository-url>
cd Quit_Smoking

# 環境変数の設定
cp .env.example .env
# .envファイルを編集して必要な値を設定

# 開発環境の起動
docker-compose up -d

# データベースマイグレーション
docker-compose exec backend php artisan migrate

# フロントエンドの依存関係インストール
docker-compose exec frontend npm install
```

## 本番環境のデプロイ

### 1. 環境変数の設定

```bash
# 本番用環境変数ファイルを作成
cp .env.example .env.prod

# 以下の値を設定:
# - APP_KEY: php artisan key:generate で生成
# - MYSQL_ROOT_PASSWORD: 強力なパスワード
# - MYSQL_PASSWORD: 強力なパスワード
# - APP_URL: 実際のドメイン
# - SANCTUM_STATEFUL_DOMAINS: 実際のドメイン
# - SESSION_DOMAIN: 実際のドメイン
```

### 2. 本番環境の起動

```bash
# 本番環境の起動
docker-compose -f docker-compose.prod.yml up -d

# データベースマイグレーション
docker-compose -f docker-compose.prod.yml exec backend php artisan migrate

# アプリケーションキーの生成
docker-compose -f docker-compose.prod.yml exec backend php artisan key:generate
```

### 3. SSL証明書の設定（推奨）

```bash
# Let's Encryptを使用したSSL証明書の取得
sudo apt install certbot
sudo certbot certonly --standalone -d yourdomain.com

# 証明書をnginxディレクトリにコピー
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem ./nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem ./nginx/ssl/key.pem
```

## バージョン情報

### フロントエンド
- **Next.js**: 15.2.4 (最新安定版)
- **React**: 18.3.1
- **TypeScript**: 5.x
- **Tailwind CSS**: 3.4.17

### バックエンド
- **Laravel**: 12.0 (最新版)
- **PHP**: 8.4
- **MySQL**: 8.4
- **Redis**: 7-alpine

### インフラ
- **Docker**: 20.10+
- **Nginx**: Alpine (リバースプロキシ)
- **Node.js**: 20-alpine

## セキュリティ設定

### 1. ファイアウォール設定
```bash
# 必要なポートのみ開放
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
sudo ufw enable
```

### 2. データベースセキュリティ
- 強力なパスワードを使用
- 外部からの直接アクセスを制限
- 定期的なバックアップを設定

### 3. アプリケーションセキュリティ
- APP_DEBUG=false に設定
- 強力なAPP_KEYを使用
- 定期的なセキュリティアップデート

## 監視とログ

### ログの確認
```bash
# アプリケーションログ
docker-compose logs -f backend

# フロントエンドログ
docker-compose logs -f frontend

# Nginxログ
docker-compose logs -f nginx
```

### ヘルスチェック
```bash
# アプリケーションの状態確認
curl http://localhost/health

# データベース接続確認
docker-compose exec backend php artisan tinker
```

## バックアップ

### データベースバックアップ
```bash
# バックアップの作成
docker-compose exec mysql mysqldump -u root -p quit_smoking > backup_$(date +%Y%m%d_%H%M%S).sql

# バックアップの復元
docker-compose exec -T mysql mysql -u root -p quit_smoking < backup_file.sql
```

### ファイルバックアップ
```bash
# ストレージファイルのバックアップ
docker-compose exec backend tar -czf storage_backup_$(date +%Y%m%d_%H%M%S).tar.gz storage/
```

## トラブルシューティング

### よくある問題

1. **データベース接続エラー**
   - 環境変数の確認
   - データベースサービスの起動確認

2. **フロントエンドビルドエラー**
   - Node.jsバージョンの確認
   - 依存関係の再インストール

3. **認証エラー**
   - Sanctum設定の確認
   - CORS設定の確認

### ログの確認方法
```bash
# 詳細なログを確認
docker-compose logs --tail=100 -f [service_name]

# エラーログのみ確認
docker-compose logs --tail=100 -f [service_name] | grep ERROR
```

## パフォーマンス最適化

### 1. キャッシュの設定
```bash
# 設定キャッシュ
docker-compose exec backend php artisan config:cache

# ルートキャッシュ
docker-compose exec backend php artisan route:cache

# ビューキャッシュ
docker-compose exec backend php artisan view:cache
```

### 2. データベース最適化
```bash
# インデックスの最適化
docker-compose exec backend php artisan migrate:status
```

## 更新手順

### アプリケーションの更新
```bash
# 最新コードの取得
git pull origin main

# 依存関係の更新
docker-compose exec frontend npm install
docker-compose exec backend composer install

# データベースマイグレーション
docker-compose exec backend php artisan migrate

# アプリケーションの再起動
docker-compose restart
```

### 本番環境の更新
```bash
# 本番環境の更新
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d --build
```
