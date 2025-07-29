# コントリビューションガイドライン

このプロジェクトへのコントリビューションをありがとうございます！このガイドラインに従って、プロジェクトの改善にご協力ください。

## 🚀 開発環境のセットアップ

### 前提条件
- PHP 8.2以上
- Composer
- MySQL 8.0
- Redis
- Git

### セットアップ手順

1. **リポジトリのフォーク**
   ```bash
   # GitHubでリポジトリをフォーク
   # ローカルにクローン
   git clone https://github.com/your-username/quit-smoking.git
   cd quit-smoking
   ```

2. **バックエンドのセットアップ**
   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **データベースの設定**
   ```bash
   # .envファイルでデータベース接続情報を設定
   php artisan migrate
   php artisan db:seed
   ```

4. **Redisの起動**
   ```bash
   redis-server
   ```

5. **開発サーバーの起動**
   ```bash
   php artisan serve
   ```

## 📝 開発の流れ

### 1. ブランチの作成
```bash
# 最新のmainブランチを取得
git checkout main
git pull origin main

# 機能ブランチを作成
git checkout -b feature/your-feature-name
# または
git checkout -b fix/your-bug-fix
```

### 2. 開発・テスト
```bash
# テストの実行
php artisan test

# コードスタイルの確認
./vendor/bin/pint --test

# 静的解析（オプション）
./vendor/bin/phpstan analyse
```

### 3. コミット
```bash
# 変更をステージング
git add .

# コミットメッセージの作成
git commit -m "feat: 新しい機能の追加"
git commit -m "fix: バグ修正"
git commit -m "docs: ドキュメントの更新"
git commit -m "test: テストの追加"
```

### 4. プッシュ・プルリクエスト
```bash
# ブランチをプッシュ
git push origin feature/your-feature-name

# GitHubでプルリクエストを作成
```

## 📋 コミットメッセージの規約

[Conventional Commits](https://www.conventionalcommits.org/)に従ってください：

- `feat:` - 新機能
- `fix:` - バグ修正
- `docs:` - ドキュメントのみの変更
- `style:` - コードの意味に影響しない変更（空白、フォーマット等）
- `refactor:` - バグ修正や機能追加ではないコードの変更
- `test:` - テストの追加や修正
- `chore:` - ビルドプロセスや補助ツールの変更

## 🧪 テスト

### テストの実行
```bash
# 全テストの実行
php artisan test

# 特定のテストファイル
php artisan test tests/Feature/Http/Controllers/AuthControllerTest.php

# テストカバレッジ
php artisan test --coverage-text
```

### テストの作成
- **Feature Tests**: APIエンドポイントの統合テスト
- **Unit Tests**: ビジネスロジックの単体テスト
- **Request Tests**: バリデーションのテスト

### テストのベストプラクティス
- テスト名は日本語で明確に記述
- 正常系と異常系の両方をテスト
- データベースの状態を確認
- モックやスタブを適切に使用

## 📏 コーディング規約

### PHP
- PSR-12に準拠
- Laravel Pintを使用
- 型宣言を適切に使用
- コメントは日本語で記述

### アーキテクチャ
- クリーンアーキテクチャの原則に従う
- UseCase層でビジネスロジックを集約
- コントローラーは薄く保つ
- 依存性注入を活用

### データベース
- マイグレーションファイルでスキーマ変更
- シーダーでテストデータを作成
- インデックスを適切に設定
- 外部キー制約を設定

## 🔍 コードレビュー

### レビューのポイント
- コードの可読性
- セキュリティ
- パフォーマンス
- テストカバレッジ
- ドキュメントの更新

### レビューコメント
- 建設的なフィードバック
- 具体的な改善提案
- コード例の提示

## 🚨 セキュリティ

### セキュリティのベストプラクティス
- 環境変数で機密情報を管理
- SQLインジェクション対策
- XSS対策
- CSRF対策
- 適切な認証・認可

### セキュリティ問題の報告
セキュリティ問題を発見した場合は、直接メールで報告してください：
- メール: security@example.com
- 件名: [SECURITY] 禁煙支援アプリケーション

## 📚 ドキュメント

### ドキュメントの更新
- API仕様書の更新
- READMEの更新
- コメントの追加
- 変更履歴の記録

### ドキュメントのベストプラクティス
- 明確で簡潔な説明
- コード例の提供
- スクリーンショットの使用
- 定期的な更新

## 🎯 プルリクエストのチェックリスト

プルリクエストを作成する前に、以下を確認してください：

- [ ] テストが全て通る
- [ ] コードスタイルが統一されている
- [ ] ドキュメントが更新されている
- [ ] コミットメッセージが適切
- [ ] セキュリティ上の問題がない
- [ ] パフォーマンスに影響がない

## 🤝 コミュニティ

### 行動規範
- 敬意を持って接する
- 建設的な議論を行う
- 多様性を尊重する
- 学習を支援する

### サポート
- GitHub Issuesで質問
- ディスカッションで議論
- ドキュメントを参照

## 📞 連絡先

- **GitHub Issues**: [プロジェクトのIssues](https://github.com/your-username/quit-smoking/issues)
- **メール**: support@example.com
- **Twitter**: [@your-username](https://twitter.com/your-username)

---

このガイドラインに従って、素晴らしいコントリビューションをお願いします！🎉 