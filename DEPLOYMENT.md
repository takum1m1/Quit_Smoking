# デプロイメントガイド

## 本番環境へのデプロイ

### 前提条件

- Docker と Docker Compose がインストールされていること
- 本番環境用の環境変数ファイル（`.env.production`）が準備されていること

### デプロイ手順

1. **環境変数ファイルの準備**
   ```bash
   cp .env.production.example .env.production
   # .env.production を編集して本番環境の設定を記入
   ```

2. **デプロイスクリプトの実行**
   ```bash
   ./deploy.sh
   ```

3. **手動デプロイ（スクリプトを使用しない場合）**
   ```bash
   # 本番環境用のDockerイメージをビルド
   docker compose -f docker-compose.prod.yml build
   
   # 既存のコンテナを停止
   docker compose -f docker-compose.prod.yml down
   
   # 本番環境を起動
   docker compose -f docker-compose.prod.yml up -d
   
   # データベースマイグレーション
   docker compose -f docker-compose.prod.yml exec backend php artisan migrate --force
   
   # アプリケーションキーを生成
   docker compose -f docker-compose.prod.yml exec backend php artisan key:generate --force
   
   # ストレージリンクを作成
   docker compose -f docker-compose.prod.yml exec backend php artisan storage:link
   
   # キャッシュをクリア
   docker compose -f docker-compose.prod.yml exec backend php artisan config:clear
   docker compose -f docker-compose.prod.yml exec backend php artisan cache:clear
   ```

### 環境変数の設定

本番環境では以下の環境変数を適切に設定してください：

#### アプリケーション設定
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `APP_KEY=base64:your-generated-key`

#### データベース設定
- `DB_CONNECTION=mysql`
- `DB_HOST=mysql`
- `DB_DATABASE=quit_smoking`
- `DB_USERNAME=your-username`
- `DB_PASSWORD=your-secure-password`

#### 認証設定
- `SANCTUM_STATEFUL_DOMAINS=your-domain.com,www.your-domain.com`
- `SESSION_DOMAIN=your-domain.com`
- `SESSION_SECURE_COOKIE=true` (HTTPS使用時)

#### フロントエンド設定
- `FRONTEND_URL=https://your-domain.com`
- `APP_FRONTEND_URL=https://your-domain.com`
- `NEXT_PUBLIC_API_URL=https://your-domain.com/api`

### ログの確認

```bash
# 全サービスのログを確認
docker compose -f docker-compose.prod.yml logs -f

# 特定のサービスのログを確認
docker compose -f docker-compose.prod.yml logs -f backend
docker compose -f docker-compose.prod.yml logs -f frontend
```

### バックアップ

```bash
# データベースのバックアップ
docker compose -f docker-compose.prod.yml exec mysql mysqldump -u root -p quit_smoking > backup.sql

# ストレージのバックアップ
docker compose -f docker-compose.prod.yml exec backend tar -czf /tmp/storage-backup.tar.gz /var/www/html/storage
```

### トラブルシューティング

#### 認証問題
- `SANCTUM_STATEFUL_DOMAINS` に正しいドメインが設定されているか確認
- `SESSION_DOMAIN` が正しく設定されているか確認
- CORS設定が正しいか確認

#### データベース接続問題
- データベースの認証情報が正しいか確認
- データベースコンテナが正常に起動しているか確認

#### フロントエンド問題
- `NEXT_PUBLIC_API_URL` が正しく設定されているか確認
- フロントエンドコンテナが正常に起動しているか確認

### セキュリティ考慮事項

1. **HTTPSの使用**: 本番環境では必ずHTTPSを使用してください
2. **強力なパスワード**: データベースパスワードは強力なものを使用してください
3. **環境変数の保護**: `.env.production` ファイルは適切に保護してください
4. **定期的な更新**: Dockerイメージと依存関係を定期的に更新してください
