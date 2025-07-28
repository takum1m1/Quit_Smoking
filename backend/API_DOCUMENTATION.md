# 禁煙支援アプリケーション API仕様書

## 概要

このAPIは禁煙支援コミュニティアプリケーションのバックエンドAPIです。ユーザーの禁煙進捗管理、コミュニティ機能、バッジシステムを提供します。

- **ベースURL**: `http://localhost:8000/api`
- **認証方式**: Bearer Token (Laravel Sanctum)
- **データ形式**: JSON

## 認証

### トークン認証
APIの大部分のエンドポイントでは認証が必要です。認証には以下の手順で取得したトークンを使用してください：

1. `/register` または `/login` エンドポイントでトークンを取得
2. リクエストヘッダーに `Authorization: Bearer {token}` を設定

## エンドポイント一覧

### 認証系

#### ユーザー登録
```
POST /register
```

**リクエスト例:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "display_name": "禁煙チャレンジャー",
  "daily_cigarettes": 20,
  "pack_cost": 500
}
```

**レスポンス例:**
```json
{
  "message": "ユーザー登録が成功しました。",
  "token": "1|abcdef123456..."
}
```

**バリデーションルール:**
- `email`: 必須、メール形式、重複不可
- `password`: 必須、8文字以上、文字を含む
- `password_confirmation`: 必須、passwordと一致
- `display_name`: 必須、20文字以下
- `daily_cigarettes`: 必須、1以上の整数
- `pack_cost`: 必須、300-3000の整数

---

#### ログイン
```
POST /login
```

**リクエスト例:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**レスポンス例:**
```json
{
  "message": "ログインが成功しました。",
  "token": "1|abcdef123456..."
}
```

**エラーレスポンス例:**
```json
{
  "message": "ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。"
}
```

---

#### ログアウト
```
POST /logout
```
**認証必要**

**レスポンス例:**
```json
{
  "message": "ログアウトが成功しました"
}
```

---

#### パスワードリセット要求
```
POST /forgot-password
```

**リクエスト例:**
```json
{
  "email": "user@example.com"
}
```

**レスポンス例:**
```json
{
  "message": "パスワードリセットリンクが送信されました。"
}
```

---

#### パスワードリセット
```
POST /reset-password/{token}
```

**リクエスト例:**
```json
{
  "email": "user@example.com",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**レスポンス例:**
```json
{
  "message": "パスワードがリセットされました。"
}
```

---

#### アカウント削除
```
DELETE /user
```
**認証必要**

**レスポンス例:**
```json
{
  "message": "アカウントが削除されました。"
}
```

### プロフィール系

#### 自分のプロフィール取得
```
GET /profile
```
**認証必要**

**レスポンス例:**
```json
{
  "display_name": "禁煙チャレンジャー",
  "daily_cigarettes": 20,
  "pack_cost": 500,
  "quit_date": "2024-01-01",
  "quit_days_count": 30,
  "quit_cigarettes": 600,
  "saved_money": 15000,
  "extended_life": 6000,
  "badges": [
    {
      "code": "one_week",
      "name": "1週間達成",
      "description": "禁煙を1週間続けました！素晴らしいスタートです。"
    },
    {
      "code": "one_month",
      "name": "1ヶ月達成",
      "description": "禁煙を1ヶ月続けました！体調の変化を感じ始めているはずです。"
    }
  ]
}
```

---

#### プロフィール更新
```
PATCH /profile
```
**認証必要**

**リクエスト例:**
```json
{
  "display_name": "新しい名前",
  "daily_cigarettes": 15,
  "pack_cost": 450
}
```

**レスポンス例:**
```json
{
  "message": "プロフィールが更新されました。"
}
```

**バリデーションルール:**
- `display_name`: 必須、20文字以下
- `daily_cigarettes`: 必須、1以上の整数
- `pack_cost`: 必須、300-3000の整数

---

#### 禁煙情報リセット
```
POST /profile/reset
```
**認証必要**

**レスポンス例:**
```json
{
  "message": "禁煙情報がリセットされました。"
}
```

---

#### 他のユーザーのプロフィール取得
```
GET /profile/{id}
```
**認証必要**

**レスポンス例:**
```json
{
  "display_name": "他のユーザー",
  "daily_cigarettes": 20,
  "pack_cost": 500,
  "quit_date": "2024-01-01",
  "quit_days_count": 30,
  "quit_cigarettes": 600,
  "saved_money": 15000,
  "extended_life": 6000,
  "badges": [
    {
      "code": "one_week",
      "name": "1週間達成",
      "description": "禁煙を1週間続けました！素晴らしいスタートです。"
    }
  ]
}
```

---

#### バッジチェック
```
POST /profile/check-badges
```
**認証必要**

**レスポンス例:**
```json
{
  "message": "バッジチェックが完了しました。",
  "awarded_badges": [
    {
      "code": "one_month",
      "name": "1ヶ月達成",
      "description": "禁煙を1ヶ月続けました！体調の変化を感じ始めているはずです。",
      "days_required": 30
    }
  ]
}
```

### コミュニティ系

#### 投稿一覧取得
```
GET /posts
```
**認証必要**

**レスポンス例:**
```json
[
  {
    "id": 1,
    "content": "禁煙を始めて1週間が経ちました！",
    "created_at": "2024-01-01T10:00:00.000000Z",
    "updated_at": "2024-01-01T10:00:00.000000Z",
    "user": {
      "id": 1,
      "email": "user@example.com",
      "profile": {
        "display_name": "禁煙チャレンジャー"
      }
    },
    "comments": [],
    "likes": []
  }
]
```

---

#### 投稿詳細取得
```
GET /posts/{id}
```
**認証必要**

**レスポンス例:**
```json
{
  "id": 1,
  "content": "禁煙を始めて1週間が経ちました！",
  "created_at": "2024-01-01T10:00:00.000000Z",
  "updated_at": "2024-01-01T10:00:00.000000Z",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "profile": {
      "display_name": "禁煙チャレンジャー"
    }
  },
  "comments": [
    {
      "id": 1,
      "content": "お疲れ様です！頑張りましょう！",
      "created_at": "2024-01-01T11:00:00.000000Z",
      "user": {
        "id": 2,
        "email": "user2@example.com",
        "profile": {
          "display_name": "応援者"
        }
      }
    }
  ],
  "likes": [
    {
      "id": 1,
      "user_id": 2,
      "created_at": "2024-01-01T12:00:00.000000Z"
    }
  ]
}
```

---

#### 投稿作成
```
POST /posts
```
**認証必要**

**リクエスト例:**
```json
{
  "content": "禁煙を始めて1週間が経ちました！"
}
```

**レスポンス例:**
```json
{
  "id": 1,
  "content": "禁煙を始めて1週間が経ちました！",
  "created_at": "2024-01-01T10:00:00.000000Z",
  "updated_at": "2024-01-01T10:00:00.000000Z",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "profile": {
      "display_name": "禁煙チャレンジャー"
    }
  },
  "comments": [],
  "likes": []
}
```

**バリデーションルール:**
- `content`: 必須、1-1000文字

---

#### 投稿更新
```
PUT /posts/{id}
```
**認証必要（投稿者のみ）**

**リクエスト例:**
```json
{
  "content": "更新された投稿内容です。"
}
```

**レスポンス例:**
```json
{
  "id": 1,
  "content": "更新された投稿内容です。",
  "created_at": "2024-01-01T10:00:00.000000Z",
  "updated_at": "2024-01-01T13:00:00.000000Z",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "profile": {
      "display_name": "禁煙チャレンジャー"
    }
  },
  "comments": [],
  "likes": []
}
```

---

#### 投稿削除
```
DELETE /posts/{id}
```
**認証必要（投稿者のみ）**

**レスポンス例:**
```json
{
  "message": "投稿は正常に削除されました。"
}
```

---

#### コメント作成
```
POST /posts/{postId}/comments
```
**認証必要**

**リクエスト例:**
```json
{
  "content": "お疲れ様です！頑張りましょう！"
}
```

**レスポンス例:**
```json
{
  "id": 1,
  "content": "お疲れ様です！頑張りましょう！",
  "created_at": "2024-01-01T11:00:00.000000Z",
  "user": {
    "id": 2,
    "email": "user2@example.com",
    "profile": {
      "display_name": "応援者"
    }
  }
}
```

**バリデーションルール:**
- `content`: 必須、1-1000文字

---

#### コメント削除
```
DELETE /posts/{postId}/comments/{commentId}
```
**認証必要（コメント投稿者のみ）**

**レスポンス例:**
```json
{
  "message": "コメントは正常に削除されました。"
}
```

---

#### いいね
```
POST /posts/{postId}/like
```
**認証必要**

**レスポンス例:**
```json
{
  "message": "投稿にいいねを押しました。"
}
```

**エラーレスポンス例:**
```json
{
  "message": "あなたはすでにこの投稿に「いいね！」を押しています。"
}
```

---

#### いいね解除
```
POST /posts/{postId}/unlike
```
**認証必要**

**レスポンス例:**
```json
{
  "message": "投稿のいいねを取り消しました。"
}
```

**エラーレスポンス例:**
```json
{
  "message": "あなたはこの投稿に「いいね！」を押していません。"
}
```

### 管理者系

#### ユーザー一覧取得
```
GET /admin/users
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
[
  {
    "id": 1,
    "email": "user@example.com",
    "role": "user",
    "created_at": "2024-01-01T10:00:00.000000Z",
    "profile": {
      "display_name": "禁煙チャレンジャー",
      "quit_date": "2024-01-01"
    }
  }
]
```

---

#### ユーザー詳細取得
```
GET /admin/users/{id}
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
{
  "id": 1,
  "email": "user@example.com",
  "role": "user",
  "created_at": "2024-01-01T10:00:00.000000Z",
  "profile": {
    "display_name": "禁煙チャレンジャー",
    "quit_date": "2024-01-01",
    "earned_badges": ["one_week", "one_month"]
  }
}
```

---

#### ユーザー削除（BAN）
```
DELETE /admin/users/{id}
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
{
  "message": "ユーザーは正常に削除されました。"
}
```

---

#### 投稿一覧取得（管理者）
```
GET /admin/posts
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
[
  {
    "id": 1,
    "content": "禁煙を始めて1週間が経ちました！",
    "created_at": "2024-01-01T10:00:00.000000Z",
    "user": {
      "id": 1,
      "email": "user@example.com",
      "profile": {
        "display_name": "禁煙チャレンジャー"
      }
    },
    "comments": [],
    "likes": []
  }
]
```

---

#### 投稿詳細取得（管理者）
```
GET /admin/posts/{id}
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
{
  "id": 1,
  "content": "禁煙を始めて1週間が経ちました！",
  "created_at": "2024-01-01T10:00:00.000000Z",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "profile": {
      "display_name": "禁煙チャレンジャー"
    }
  },
  "comments": [],
  "likes": []
}
```

---

#### 投稿削除（管理者）
```
DELETE /admin/posts/{id}
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
{
  "message": "投稿は正常に削除されました。"
}
```

---

#### コメント一覧取得（管理者）
```
GET /admin/comments
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
[
  {
    "id": 1,
    "content": "お疲れ様です！頑張りましょう！",
    "created_at": "2024-01-01T11:00:00.000000Z",
    "user": {
      "id": 2,
      "email": "user2@example.com",
      "profile": {
        "display_name": "応援者"
      }
    },
    "post": {
      "id": 1,
      "content": "禁煙を始めて1週間が経ちました！"
    }
  }
]
```

---

#### コメント詳細取得（管理者）
```
GET /admin/comments/{id}
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
{
  "id": 1,
  "content": "お疲れ様です！頑張りましょう！",
  "created_at": "2024-01-01T11:00:00.000000Z",
  "user": {
    "id": 2,
    "email": "user2@example.com",
    "profile": {
      "display_name": "応援者"
    }
  },
  "post": {
    "id": 1,
    "content": "禁煙を始めて1週間が経ちました！"
  }
}
```

---

#### コメント削除（管理者）
```
DELETE /admin/comments/{id}
```
**認証必要（管理者のみ）**

**レスポンス例:**
```json
{
  "message": "コメントは正常に削除されました。"
}
```

## エラーレスポンス

### 認証エラー
```json
{
  "message": "Unauthenticated."
}
```
**ステータスコード: 401**

### 権限エラー
```json
{
  "message": "管理者権限が必要です"
}
```
**ステータスコード: 403**

### バリデーションエラー
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```
**ステータスコード: 422**

### リソースが見つからない
```json
{
  "message": "No query results for model [App\\Models\\Post] 1"
}
```
**ステータスコード: 404**

## バッジシステム

### 利用可能なバッジ

| バッジコード | バッジ名 | 説明 | 獲得条件 |
|-------------|---------|------|----------|
| `one_week` | 1週間達成 | 禁煙を1週間続けました！素晴らしいスタートです。 | 禁煙開始から7日 |
| `one_month` | 1ヶ月達成 | 禁煙を1ヶ月続けました！体調の変化を感じ始めているはずです。 | 禁煙開始から30日 |
| `six_months` | 半年達成 | 禁煙を半年続けました！健康状態が大幅に改善されています。 | 禁煙開始から180日 |
| `one_year` | 1年間達成 | 禁煙を1年間続けました！心臓病のリスクが半減しています。 | 禁煙開始から365日 |

### バッジ獲得の仕組み

1. **自動授与**: プロフィール更新時に自動的にバッジチェックが実行されます
2. **手動チェック**: `/api/profile/check-badges` エンドポイントで手動でバッジチェックを実行できます
3. **プロフィール表示**: プロフィール取得時に獲得済みバッジが表示されます

## 使用例

### 1. ユーザー登録から投稿までの流れ

```bash
# 1. ユーザー登録
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "display_name": "禁煙チャレンジャー",
    "daily_cigarettes": 20,
    "pack_cost": 500
  }'

# レスポンスからトークンを取得
# {"token": "1|abcdef123456..."}

# 2. 投稿作成
curl -X POST http://localhost:8000/api/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|abcdef123456..." \
  -d '{
    "content": "禁煙を始めて1週間が経ちました！"
  }'

# 3. バッジチェック
curl -X POST http://localhost:8000/api/profile/check-badges \
  -H "Authorization: Bearer 1|abcdef123456..."
```

### 2. プロフィール取得

```bash
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer 1|abcdef123456..."
```

## 注意事項

1. **認証トークン**: 大部分のエンドポイントでは認証が必要です
2. **権限**: 管理者機能は管理者権限を持つユーザーのみアクセス可能です
3. **データ形式**: すべてのリクエスト・レスポンスはJSON形式です
4. **エラーハンドリング**: 適切なエラーレスポンスを確認してください
5. **バッジシステム**: 禁煙期間に応じて自動的にバッジが授与されます

## 更新履歴

- **2024-01-01**: 初版作成
- **2024-01-15**: バッジシステム追加
- **2024-02-01**: 管理者機能追加 
