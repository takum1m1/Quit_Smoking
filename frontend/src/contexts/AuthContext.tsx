'use client';

import React, { createContext, useContext, useEffect, useState, ReactNode, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import { apiClient } from '@/lib/api-client';
import { type User, type UserProfile, type LoginRequest, type RegisterRequest } from '@/types';
import toast from 'react-hot-toast';

// 認証コンテキストの型定義
interface AuthContextType {
  user: User | null;
  userProfile: UserProfile | null;
  isLoading: boolean;
  isAuthenticated: boolean;
  login: (data: LoginRequest) => Promise<boolean>;
  register: (data: RegisterRequest) => Promise<boolean>;
  logout: () => Promise<void>;
  refreshUser: () => Promise<void>;
  refreshUserProfile: () => Promise<void>;
  updateProfile: (data: Partial<UserProfile>) => Promise<boolean>;
  checkAuthStatus: () => Promise<boolean>;
}

// 認証コンテキストの作成
const AuthContext = createContext<AuthContextType | undefined>(undefined);

// 認証プロバイダーの型定義
interface AuthProviderProps {
  children: ReactNode;
}

/**
 * 認証プロバイダーコンポーネント
 * 認証状態の管理とAPI通信を提供
 */
export function AuthProvider({ children }: AuthProviderProps) {
  const [user, setUser] = useState<User | null>(null);
  const [userProfile, setUserProfile] = useState<UserProfile | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isInitialized, setIsInitialized] = useState(false);
  const router = useRouter();

  // 認証状態の確認
  const isAuthenticated = !!user && !!userProfile && isInitialized;

  /**
   * プロフィール情報を取得
   */
  const fetchUserProfile = useCallback(async (userId: number): Promise<UserProfile | null> => {
    try {
      const response = await apiClient.getUserProfile(userId);
      // APIクライアントはUserProfileを直接返す
      return response as UserProfile;
    } catch (error) {
      console.error('プロフィール取得エラー:', error);
      return null;
    }
  }, []);

  /**
   * ユーザー情報を更新
   */
  const refreshUser = useCallback(async (): Promise<void> => {
    if (!user) return;

    try {
      const [userResponse, profileResponse] = await Promise.all([
        apiClient.getCurrentUser(),
        fetchUserProfile(user.id),
      ]);

      if (userResponse.user) {
        setUser(userResponse.user);
      }

      if (profileResponse) {
        setUserProfile(profileResponse);
      }
    } catch (error) {
      console.error('ユーザー情報更新エラー:', error);
      // エラーが発生した場合はログアウト
      await logout();
    }
  }, [user, fetchUserProfile]); // eslint-disable-line react-hooks/exhaustive-deps

  /**
   * ユーザープロフィールを更新
   */
  const refreshUserProfile = useCallback(async (): Promise<void> => {
    if (!user) return;

    try {
      const profileResponse = await fetchUserProfile(user.id);
      if (profileResponse) {
        setUserProfile(profileResponse);
      }
    } catch (error) {
      console.error('プロフィール更新エラー:', error);
    }
  }, [user, fetchUserProfile]);

  /**
   * 認証の初期化
   */
  const initializeAuth = useCallback(async () => {
    console.log('=== 認証初期化開始 ===');
    
    // クライアントサイドでのみ実行
    if (typeof window === 'undefined') {
      console.log('サーバーサイドレンダリング中 - 認証初期化をスキップ');
      setIsLoading(false);
      setIsInitialized(true);
      return;
    }

    try {
      const token = localStorage.getItem('auth_token');
      console.log('localStorageから取得したトークン:', token ? `${token.substring(0, 20)}...` : 'なし');
      
      if (!token) {
        console.log('トークンが存在しません。未認証状態に設定します。');
        setUser(null);
        setUserProfile(null);
        setIsLoading(false);
        setIsInitialized(true);
        return;
      }

      console.log('トークンが存在します。APIクライアントの状態を確認中...');
      // APIクライアントが既にトークンを持っているかチェック
      if (!apiClient.currentToken) {
        console.log('APIクライアントにトークンを設定中...');
        apiClient.setToken(token);
      } else {
        console.log('APIクライアントは既にトークンを持っています');
      }
      
      console.log('ユーザー情報を取得中...');
      // ユーザー情報を取得
      const userResponse = await apiClient.getCurrentUser();
      console.log('ユーザー情報取得結果:', userResponse);
      
      if (!userResponse.user) {
        console.log('ユーザー情報が取得できませんでした。トークンを削除します。');
        localStorage.removeItem('auth_token');
        apiClient.removeToken();
        setUser(null);
        setUserProfile(null);
        setIsLoading(false);
        setIsInitialized(true);
        return;
      }

      console.log('ユーザー情報取得成功。ユーザー状態を設定中...');
      setUser(userResponse.user);
      
      console.log('プロフィール情報を取得中...');
      // 正しいユーザーIDでプロフィールを取得
      const profile = await fetchUserProfile(userResponse.user.id);
      if (profile) {
        console.log('プロフィール情報取得成功:', profile);
        setUserProfile(profile);
      } else {
        console.log('プロフィール情報の取得に失敗しました');
        setUserProfile(null);
      }
      
    } catch (error) {
      console.error('認証初期化エラー:', error);
      console.error('エラーの詳細:', error);
      
      // 認証に失敗した場合はトークンを削除
      localStorage.removeItem('auth_token');
      apiClient.removeToken();
      setUser(null);
      setUserProfile(null);
      
    } finally {
      console.log('認証初期化完了。ローディング状態を解除します。');
      setIsLoading(false);
      setIsInitialized(true);
    }
  }, [fetchUserProfile]);

  /**
   * 初期化時にユーザー情報を取得
   */
  useEffect(() => {
    if (!isInitialized) {
      console.log('AuthContext: useEffect実行 - 認証初期化を開始します');
      initializeAuth();
    }
  }, [initializeAuth, isInitialized]);

  /**
   * ストレージの変更を監視
   */
  useEffect(() => {
    if (typeof window === 'undefined') return;

    const handleStorageChange = (e: StorageEvent) => {
      if (e.key === 'auth_token') {
        console.log('localStorageのauth_tokenが変更されました:', e.newValue ? '設定' : '削除');
        if (!e.newValue && user) {
          // トークンが削除された場合はログアウト
          console.log('トークンが削除されました。ログアウト処理を実行します。');
          setUser(null);
          setUserProfile(null);
        }
      }
    };

    window.addEventListener('storage', handleStorageChange);
    return () => window.removeEventListener('storage', handleStorageChange);
  }, [user]);

  /**
   * 認証状態をチェック
   */
  const checkAuthStatus = useCallback(async (): Promise<boolean> => {
    try {
      const token = localStorage.getItem('auth_token');
      if (!token) {
        return false;
      }

      apiClient.setToken(token);
      const userResponse = await apiClient.getCurrentUser();
      return !!userResponse.user;
    } catch (error) {
      console.error('認証状態チェックエラー:', error);
      return false;
    }
  }, []);

  /**
   * ユーザーログイン
   */
  const login = async (data: LoginRequest): Promise<boolean> => {
    try {
      console.log('=== ログイン処理開始 ===');
      setIsLoading(true);
      
      // ログイン処理
      console.log('ログインAPIを呼び出し中...');
      const response = await apiClient.login(data);
      console.log('ログインAPIレスポンス:', response);
      
      if (!response.token) {
        console.log('ログイン失敗: トークンが取得できませんでした');
        toast.error(response.message || 'ログインに失敗しました');
        return false;
      }

      // トークンを設定
      console.log('トークンを設定中...', `${response.token.substring(0, 20)}...`);
      apiClient.setToken(response.token);
      localStorage.setItem('auth_token', response.token);
      console.log('トークンの設定が完了しました');
      
      // ユーザー情報を取得
      if (response.user) {
        setUser(response.user);
        
        // プロフィール情報を取得
        if (response.user.profile) {
          setUserProfile(response.user.profile);
        } else {
          const profile = await fetchUserProfile(response.user.id);
          if (profile) {
            setUserProfile(profile);
          }
        }
      } else {
        toast.error('ユーザー情報の取得に失敗しました');
        return false;
      }

      // 成功通知とリダイレクト
      toast.success('ログインに成功しました！ダッシュボードに移動しています...');

      setTimeout(() => {
        router.push('/dashboard');
      }, 1000);

      return true;

    } catch (error: unknown) {
      console.error('ログインエラー:', error);
      const errorMessage = error instanceof Error ? error.message : 'ログインに失敗しました';
      toast.error(errorMessage);
      return false;
    } finally {
      setIsLoading(false);
    }
  };

  /**
   * ユーザー登録
   */
  const register = async (data: RegisterRequest): Promise<boolean> => {
    try {
      console.log('=== AuthContext register開始 ===');
      console.log('受信データ:', data);
      setIsLoading(true);

      console.log('apiClient.registerを呼び出し中...');
      // 登録処理
      const response = await apiClient.register(data);
      console.log('apiClient.registerレスポンス:', response);

      if (!response.token) {
        console.log('トークンが取得できませんでした:', response);
        toast.error(response.message || 'アカウント登録に失敗しました');
        return false;
      }

      console.log('トークンを設定中...');
      // トークンを設定
      apiClient.setToken(response.token);
      localStorage.setItem('auth_token', response.token);
      
      // ユーザー情報を取得
      if (response.user) {
        console.log('ユーザー情報を設定中:', response.user);
        setUser(response.user);
        
        // プロフィール情報を取得
        if (response.user.profile) {
          console.log('プロフィール情報を設定中:', response.user.profile);
          setUserProfile(response.user.profile);
        } else {
          console.log('プロフィール情報を取得中...');
          const profile = await fetchUserProfile(response.user.id);
          if (profile) {
            console.log('取得したプロフィール:', profile);
            setUserProfile(profile);
          }
        }
      } else {
        console.log('ユーザー情報が取得できませんでした');
        toast.error('ユーザー情報の取得に失敗しました');
        return false;
      }

      console.log('登録成功: リダイレクト準備中...');
      // 成功通知とリダイレクト
      toast.success('アカウント登録が完了しました！自動的にログインしています...');
      
      setTimeout(() => {
        console.log('ダッシュボードにリダイレクト中...');
        router.push('/dashboard');
      }, 1500);
      
      return true;
      
    } catch (error: unknown) {
      console.error('=== AuthContext登録エラー詳細 ===');
      console.error('エラーオブジェクト:', error);
      console.error('エラーメッセージ:', error instanceof Error ? error.message : 'Unknown error');
      console.error('エラースタック:', error instanceof Error ? error.stack : 'No stack trace');
      
      let errorMessage = 'アカウント登録に失敗しました';
      
      if (error instanceof Error) {
        // バリデーションエラーの場合、より詳細なメッセージを表示
        if (error.message.includes('email') && error.message.includes('already been taken')) {
          errorMessage = 'このメールアドレスは既に登録されています。別のメールアドレスをお試しください。';
        } else if (error.message.includes('バリデーションエラー')) {
          errorMessage = error.message;
        } else {
          errorMessage = error.message;
        }
      }
      
      toast.error(errorMessage);
      return false;
    } finally {
      setIsLoading(false);
      console.log('=== AuthContext register終了 ===');
    }
  };

  /**
   * ユーザーログアウト
   */
  const logout = useCallback(async (): Promise<void> => {
    try {
      if (isAuthenticated) {
        await apiClient.logout();
      }
    } catch (error) {
      console.error('ログアウトエラー:', error);
    } finally {
      // ローカル状態をクリア
      apiClient.removeToken();
      localStorage.removeItem('auth_token');
      setUser(null);
      setUserProfile(null);
      router.push('/');
      toast.success('ログアウトしました');
    }
  }, [isAuthenticated, router]);

  /**
   * プロフィール更新
   */
  const updateProfile = async (data: Partial<UserProfile>): Promise<boolean> => {
    if (!user) return false;

    try {
      const response = await apiClient.updateUserProfile(data);
      
      if (response.data) {
        setUserProfile(response.data as UserProfile);
        toast.success('プロフィールを更新しました');
        return true;
      } else {
        toast.error(response.message || 'プロフィール更新に失敗しました');
        return false;
      }
    } catch (error: unknown) {
      console.error('プロフィール更新エラー:', error);
      const errorMessage = error instanceof Error ? error.message : 'プロフィール更新に失敗しました';
      toast.error(errorMessage);
      return false;
    }
  };

  // コンテキストの値
  const value: AuthContextType = {
    user,
    userProfile,
    isLoading,
    isAuthenticated,
    login,
    register,
    logout,
    refreshUser,
    refreshUserProfile,
    updateProfile,
    checkAuthStatus,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
}

/**
 * 認証コンテキストを使用するフック
 */
export function useAuth(): AuthContextType {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}

/**
 * 認証が必要なページで使用するフック
 */
export function useRequireAuth(): AuthContextType {
  const auth = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!auth.isLoading && !auth.isAuthenticated) {
      router.push('/auth/login');
    }
  }, [auth.isLoading, auth.isAuthenticated, router]);

  return auth;
}
