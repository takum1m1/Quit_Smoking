#!/bin/bash

# デプロイスクリプト
set -e

echo "🚀 本番環境へのデプロイを開始します..."

# 環境変数の確認
if [ ! -f .env.production ]; then
    echo "❌ .env.production ファイルが見つかりません"
    echo "📝 .env.production.example を参考に .env.production を作成してください"
    exit 1
fi

# 本番環境用のDocker Composeでビルド・起動
echo "🔨 本番環境用のDockerイメージをビルド中..."
docker compose -f docker-compose.prod.yml build

echo "🛑 既存のコンテナを停止中..."
docker compose -f docker-compose.prod.yml down

echo "🚀 本番環境を起動中..."
docker compose -f docker-compose.prod.yml up -d

echo "⏳ データベースの準備を待機中..."
sleep 10

echo "🗄️ データベースマイグレーションを実行中..."
docker compose -f docker-compose.prod.yml exec backend php artisan migrate --force

echo "🔑 アプリケーションキーを生成中..."
docker compose -f docker-compose.prod.yml exec backend php artisan key:generate --force

echo "📦 ストレージリンクを作成中..."
docker compose -f docker-compose.prod.yml exec backend php artisan storage:link

echo "🧹 キャッシュをクリア中..."
docker compose -f docker-compose.prod.yml exec backend php artisan config:clear
docker compose -f docker-compose.prod.yml exec backend php artisan cache:clear
docker compose -f docker-compose.prod.yml exec backend php artisan route:clear
docker compose -f docker-compose.prod.yml exec backend php artisan view:clear

echo "✅ デプロイが完了しました！"
echo "🌐 アプリケーションは以下のURLでアクセスできます："
echo "   - フロントエンド: http://localhost:3000"
echo "   - バックエンド: http://localhost:8000"

echo "📊 ログを確認するには："
echo "   docker compose -f docker-compose.prod.yml logs -f"
