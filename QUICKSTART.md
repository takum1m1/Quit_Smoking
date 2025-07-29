# 🚀 クイックスタートガイド

このガイドでは、Docker環境を使って禁煙支援アプリケーションを素早く起動する方法を説明します。

## 📋 前提条件

- **Docker Desktop** がインストールされていること
- **Git** がインストールされていること

## ⚡ 5分で起動！

### 1. リポジトリのクローン
```bash
git clone https://github.com/your-username/quit-smoking.git
cd quit-smoking
```

### 2. 環境変数ファイルの作成
```bash
cd backend
cp .env.example .env
cd ..
```

### 3. Docker環境の起動
```bash
docker-compose up -d
```

### 4. アプリケーションの初期化
```bash
# アプリケーションキーの生成
docker-compose exec backend php artisan key:generate

# データベースのセットアップ
docker-compose exec backend php artisan migrate

# テストデータの作成（オプション）
docker-compose exec backend php artisan db:seed
```

### 5. 動作確認
```bash
# テストの実行
docker-compose exec backend php artisan test

# APIの動作確認
curl http://localhost:8000/api
```

## 🎯 アクセスURL

- **API**: http://localhost:8000/api

## 🔧 よく使うコマンド

### コンテナの管理
```bash
# 起動
docker-compose up -d

# 停止
docker-compose down

# ログ確認
docker-compose logs backend
```

### 開発コマンド
```bash
# テスト実行
docker-compose exec backend php artisan test

# マイグレーション
docker-compose exec backend php artisan migrate

# キャッシュクリア
docker-compose exec backend php artisan cache:clear-all
```


## 📚 次のステップ

- [README.md](README.md) - 詳細なドキュメント

## 🎉 お疲れ様でした！

これで禁煙支援アプリケーションが起動しました！開発を始める準備が整いました。