# API仕様書の使用方法

このプロジェクトには2種類のAPI仕様書が含まれています：

## 1. Markdown形式のAPI仕様書

**ファイル**: `API_DOCUMENTATION.md`

### 特徴
- 人間が読みやすい形式
- 詳細な説明と例文
- 初心者にも理解しやすい
- コピー&ペーストで使用可能なcurlコマンド例

### 使用方法
```bash
# ファイルを開いて読む
cat API_DOCUMENTATION.md

# または、お好みのテキストエディタで開く
code API_DOCUMENTATION.md
```

### 内容
- 全エンドポイントの詳細説明
- リクエスト・レスポンス例
- エラーハンドリング
- バッジシステムの説明
- 実際の使用例（curlコマンド）

## 2. OpenAPI 3.0形式のAPI仕様書

**ファイル**: `openapi.yaml`

### 特徴
- 機械可読な形式
- 自動生成ツールとの連携
- インタラクティブなドキュメント生成
- コード生成に使用可能

### 使用方法

#### A. Swagger UIで表示する

1. **Swagger Editor（オンライン）**
   - https://editor.swagger.io/ にアクセス
   - `openapi.yaml` の内容をコピー&ペースト
   - リアルタイムでAPIドキュメントを確認

2. **Swagger UI（ローカル）**
   ```bash
   # Dockerを使用
   docker run -p 8080:8080 -e SWAGGER_JSON=/openapi.yaml -v $(pwd):/swagger swaggerapi/swagger-ui
   # ブラウザで http://localhost:8080 にアクセス
   ```

#### B. コード生成に使用する

1. **TypeScript型定義の生成**
   ```bash
   # OpenAPI Generatorを使用
   npx @openapitools/openapi-generator-cli generate \
     -i openapi.yaml \
     -g typescript-fetch \
     -o ./generated-typescript
   ```

2. **React Hooksの生成**
   ```bash
   # OpenAPI Generatorを使用
   npx @openapitools/openapi-generator-cli generate \
     -i openapi.yaml \
     -g typescript-react-query \
     -o ./generated-react-query
   ```

## 3. API仕様書の活用方法

### フロントエンド開発での活用

#### A. 型定義の自動生成
```bash
# TypeScript型定義を生成
npx @openapitools/openapi-generator-cli generate \
  -i openapi.yaml \
  -g typescript-fetch \
  -o ./frontend/src/types/api
```

#### B. APIクライアントの自動生成
```bash
# Axiosベースのクライアントを生成
npx @openapitools/openapi-generator-cli generate \
  -i openapi.yaml \
  -g typescript-axios \
  -o ./frontend/src/api
```

### テストでの活用

#### A. APIテストの自動生成
```bash
# Postmanコレクションを生成
npx @openapitools/openapi-generator-cli generate \
  -i openapi.yaml \
  -g postman \
  -o ./tests/postman
```

#### B. モックサーバーの生成
```bash
# Prismを使用してモックサーバーを起動
npx @stoplight/prism mock openapi.yaml
```

## 4. 開発ワークフロー

### 1. API仕様書の更新
```bash
# 新しいエンドポイントを追加した場合
# 1. openapi.yamlを更新
# 2. API_DOCUMENTATION.mdを更新
# 3. 型定義を再生成
npx @openapitools/openapi-generator-cli generate \
  -i openapi.yaml \
  -g typescript-fetch \
  -o ./frontend/src/types/api
```

### 2. フロントエンド開発
```bash
# 1. 型定義を使用してAPIクライアントを作成
# 2. 生成された型を使用してコンポーネントを作成
# 3. APIレスポンスの型安全性を確保
```

### 3. テスト
```bash
# 1. 生成されたPostmanコレクションでAPIテスト
# 2. 型定義を使用したユニットテスト
# 3. モックサーバーを使用した統合テスト
```

## 5. 推奨ツール

### API仕様書の表示・編集
- **Swagger Editor**: オンラインでOpenAPI仕様書を編集・表示
- **Swagger UI**: インタラクティブなAPIドキュメント
- **Redoc**: 美しいAPIドキュメント

### コード生成
- **OpenAPI Generator**: 多言語対応のコード生成
- **Swagger Codegen**: レガシーなコード生成ツール

### テスト
- **Postman**: APIテスト
- **Prism**: モックサーバー
- **Newman**: Postmanコレクションの自動実行

## 6. ベストプラクティス

### API仕様書の管理
1. **バージョン管理**: GitでAPI仕様書を管理
2. **自動生成**: CI/CDで型定義を自動生成
3. **同期**: 実装と仕様書を常に同期

### 開発フロー
1. **API First**: まずAPI仕様書を作成
2. **型安全性**: 生成された型を活用
3. **テスト駆動**: API仕様書からテストを生成

### ドキュメント
1. **例文**: 実際の使用例を含める
2. **エラーハンドリング**: エラーケースを明記
3. **認証**: 認証方法を詳細に説明

## 7. トラブルシューティング

### よくある問題

#### A. 型定義が古い
```bash
# 型定義を再生成
npx @openapitools/openapi-generator-cli generate \
  -i openapi.yaml \
  -g typescript-fetch \
  -o ./frontend/src/types/api \
  --clear-output-dir
```

#### B. Swagger UIが表示されない
```bash
# ファイルパスを確認
# 相対パスではなく絶対パスを使用
docker run -p 8080:8080 -e SWAGGER_JSON=/swagger/openapi.yaml -v $(pwd):/swagger swaggerapi/swagger-ui
```

#### C. 生成されたコードにエラーがある
```bash
# OpenAPI仕様書の構文をチェック
npx @stoplight/spectral lint openapi.yaml
```

## 8. 次のステップ

1. **フロントエンド開発**: 生成された型定義を使用してReactアプリを開発
2. **テスト自動化**: APIテストをCI/CDに組み込み
3. **ドキュメント自動化**: API仕様書の自動更新を設定
4. **モニタリング**: APIの使用状況を監視

## 参考リンク

- [OpenAPI Specification](https://swagger.io/specification/)
- [OpenAPI Generator](https://openapi-generator.tech/)
- [Swagger UI](https://swagger.io/tools/swagger-ui/)
- [Prism](https://meta.stoplight.io/docs/prism/)
- [Postman](https://www.postman.com/) 
