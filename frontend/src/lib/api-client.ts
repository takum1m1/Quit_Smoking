import {
  type UserProfile,
  type Post,
  type LoginRequest,
  type RegisterRequest,
  type ForgotPasswordRequest,
  type ResetPasswordRequest,
  type ApiResponse,
  type AuthResponse,
  type UserResponse,
  type PostsResponse,
  type CommentsResponse,
} from '@/types';

/**
 * APIクライアントクラス
 * バックエンドAPIとの通信を管理
 */
class ApiClient {
  private baseURL: string;
  private token: string | null;

  constructor() {
    // 本番環境では環境変数から、開発環境ではデフォルト値を使用
    this.baseURL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';
    this.token = null; // 初期化時はトークンを設定しない
    
    // クライアントサイドでのみトークンを初期化
    if (typeof window !== 'undefined') {
      const savedToken = localStorage.getItem('auth_token');
      if (savedToken) {
        console.log('APIクライアント初期化: localStorageからトークンを復元中...', `${savedToken.substring(0, 20)}...`);
        this.token = savedToken;
        console.log('APIクライアント初期化: トークンが復元されました');
      } else {
        console.log('APIクライアント初期化: 保存されたトークンはありません');
      }
      console.log('APIクライアント初期化: ベースURL', this.baseURL);
    }
  }

  /**
   * 現在のトークンを取得
   */
  get currentToken(): string | null {
    return this.token;
  }

  /**
   * 認証トークンを設定
   */
  setToken(token: string): void {
    console.log('APIクライアント: トークンを設定中...', token ? `${token.substring(0, 20)}...` : 'なし');
    this.token = token;
    if (typeof window !== 'undefined') {
      localStorage.setItem('auth_token', token);
      console.log('APIクライアント: localStorageにトークンを保存しました');
    }
  }

  /**
   * 認証トークンを削除
   */
  removeToken(): void {
    console.log('APIクライアント: トークンを削除中...');
    this.token = null;
    if (typeof window !== 'undefined') {
      localStorage.removeItem('auth_token');
      console.log('APIクライアント: localStorageからトークンを削除しました');
    }
  }

  /**
   * HTTPリクエストを実行
   */
  private async request<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<T> {
    const url = `${this.baseURL}${endpoint}`;
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> || {}),
    };

    if (this.token) {
      headers['Authorization'] = `Bearer ${this.token}`;
      console.log('APIリクエスト: Authorizationヘッダーを設定しました', `Bearer ${this.token.substring(0, 20)}...`);
    } else {
      console.log('APIリクエスト: トークンがありません。Authorizationヘッダーは設定されません');
    }

    const config: RequestInit = {
      ...options,
      headers,
    };

    try {
      console.log(`API リクエスト: ${options.method || 'GET'} ${url}`);
      console.log('リクエストヘッダー:', headers);
      console.log('リクエストボディ:', options.body);
      
      const response = await fetch(url, config);
      console.log(`API レスポンス: ${response.status} ${response.statusText}`);
      console.log('レスポンスヘッダー:', Object.fromEntries(response.headers.entries()));
      
      // レスポンスボディを一度だけ読み取る
      let responseData: unknown;
      try {
        const responseText = await response.text();
        console.log('レスポンステキスト:', responseText);
        
        if (responseText.trim() === '') {
          console.warn('レスポンスボディが空です');
          responseData = {};
        } else {
          responseData = JSON.parse(responseText);
          console.log('API レスポンスデータ:', responseData);
        }
      } catch (parseError) {
        console.error('レスポンスのパースに失敗:', parseError);
        console.error('パースエラーの詳細:', parseError);
        throw new Error('サーバーからの応答を解析できませんでした');
      }
      
      if (!response.ok) {
        // 401 の際に即トークンを破棄しない（初期化レース等で誤って消さないため）
        // トークンの無効化は上位（AuthContext）で判断する
        let errorMessage = `HTTP error! status: ${response.status}`;
        
        if (responseData && typeof responseData === 'object' && 'message' in responseData) {
          errorMessage = (responseData as { message: string }).message;
        } else if (responseData && typeof responseData === 'object' && 'error' in responseData) {
          errorMessage = (responseData as { error: string }).error;
        } else if (responseData && typeof responseData === 'object' && 'errors' in responseData) {
          // バリデーションエラーの場合
          const errors = (responseData as { errors: Record<string, string[]> }).errors;
          if (typeof errors === 'object') {
            const validationErrors = Object.entries(errors)
              .map(([field, messages]) => `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
              .join('; ');
            errorMessage = `バリデーションエラー: ${validationErrors}`;
          }
        }
        
        throw new Error(errorMessage);
      }

      return responseData as T;
    } catch (error) {
      console.error('API request failed:', error);
      throw error;
    }
  }

  // ==================== 認証関連 ====================

  /**
   * ユーザーログイン
   */
  async login(data: LoginRequest): Promise<AuthResponse> {
    return this.request<AuthResponse>('/login', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  /**
   * ユーザー登録
   */
  async register(data: RegisterRequest): Promise<AuthResponse> {
    return this.request<AuthResponse>('/register', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  /**
   * ユーザーログアウト
   */
  async logout(): Promise<ApiResponse> {
    return this.request<ApiResponse>('/logout', {
      method: 'POST',
    });
  }

  /**
   * パスワードリセット要求
   */
  async forgotPassword(data: ForgotPasswordRequest): Promise<ApiResponse> {
    return this.request<ApiResponse>('/forgot-password', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  /**
   * パスワードリセット
   */
  async resetPassword(data: ResetPasswordRequest): Promise<ApiResponse> {
    return this.request<ApiResponse>('/reset-password', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  /**
   * 現在のユーザー情報を取得
   */
  async getCurrentUser(): Promise<UserResponse> {
    return this.request<UserResponse>('/user');
  }

  // ==================== ユーザープロフィール関連 ====================

  /**
   * ユーザープロフィールを取得
   */
  async getUserProfile(userId: number): Promise<UserProfile> {
    return this.request<UserProfile>(`/user-profiles/${userId}`);
  }

  /**
   * ユーザープロフィールを更新
   */
  async updateUserProfile(data: Partial<UserProfile>): Promise<ApiResponse> {
    return this.request<ApiResponse>('/profile', {
      method: 'PATCH',
      body: JSON.stringify(data),
    });
  }

  /**
   * 禁煙情報をリセット
   */
  async resetQuitInfo(): Promise<ApiResponse> {
    return this.request<ApiResponse>('/profile/reset', {
      method: 'POST',
    });
  }

  // ==================== 投稿関連 ====================

  /**
   * 投稿一覧を取得
   */
  async getPosts(page: number = 1, perPage: number = 10): Promise<Post[]> {
    const response = await this.request<Post[]>(`/posts?page=${page}&per_page=${perPage}`);
    // レスポンスが配列でない場合は空配列を返す
    return Array.isArray(response) ? response : [];
  }

  /**
   * 投稿詳細を取得
   */
  async getPost(postId: number): Promise<Post> {
    return this.request<Post>(`/posts/${postId}`);
  }

  /**
   * 新規投稿を作成
   */
  async createPost(data: { content: string }): Promise<Post> {
    return this.request<Post>('/posts', {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  /**
   * 投稿を更新
   */
  async updatePost(postId: number, data: { content: string }): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/posts/${postId}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  /**
   * 投稿を削除
   */
  async deletePost(postId: number): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/posts/${postId}`, {
      method: 'DELETE',
    });
  }

  // ==================== コメント関連 ====================

  /**
   * 投稿のコメント一覧を取得
   */
  async getComments(postId: number): Promise<CommentsResponse> {
    return this.request<CommentsResponse>(`/posts/${postId}/comments`);
  }

  /**
   * コメントを作成
   */
  async createComment(postId: number, data: { content: string }): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/posts/${postId}/comments`, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  /**
   * コメントを更新
   */
  async updateComment(commentId: number, data: { content: string }): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/comments/${commentId}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  /**
   * コメントを削除
   */
  async deleteComment(postId: number, commentId: number): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/posts/${postId}/comments/${commentId}`, {
      method: 'DELETE',
    });
  }

  // ==================== いいね関連 ====================

  /**
   * 投稿にいいねを追加
   */
  async likePost(postId: number): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/posts/${postId}/like`, {
      method: 'POST',
    });
  }

  /**
   * 投稿のいいねを削除
   */
  async unlikePost(postId: number): Promise<ApiResponse> {
    return this.request<ApiResponse>(`/posts/${postId}/unlike`, {
      method: 'DELETE',
    });
  }

  // バックエンドに未実装のエンドポイントは削除
}

// シングルトンインスタンスをエクスポート
export const apiClient = new ApiClient();

// 型付きエクスポート
export type { ApiClient };
