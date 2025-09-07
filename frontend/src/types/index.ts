// ユーザー関連の型定義
export interface User {
  id: number;
  email: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  profile?: UserProfile;
}

export interface UserProfile {
  user_id: number;
  display_name?: string;
  daily_cigarettes?: number;
  pack_cost?: number;
  quit_date: string | null;
  quit_days_count?: number;
  quit_cigarettes?: number;
  saved_money?: number;
  extended_life?: number;
  badges: Badge[] | null;
  created_at: string;
  updated_at: string;
}

export interface Badge {
  id: number;
  name: string;
  description: string;
  icon: string;
  unlocked_at: string;
}

// 認証関連の型定義
export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  email: string;
  password: string;
  password_confirmation: string;
  display_name: string;
  daily_cigarettes: number;
  pack_cost: number;
}

export interface AuthResponse {
  message: string;
  token: string;
  user?: User;
}

export interface UserResponse {
  user: User;
}

export interface ForgotPasswordRequest {
  email: string;
}

export interface ResetPasswordRequest {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
}

// 投稿関連の型定義
export interface Post {
  id: number;
  content: string;
  user_id: number;
  user?: User;
  comments?: Comment[];
  likes?: Like[];
  created_at: string;
  updated_at: string;
  deleted_at?: string;
}

export interface CreatePostData {
  content: string;
}

export interface Comment {
  id: number;
  content: string;
  user_id: number;
  post_id: number;
  user?: User;
  created_at: string;
  updated_at: string;
}

export interface Like {
  id: number;
  user_id: number;
  post_id: number;
  created_at: string;
}

// APIレスポンスの型定義
export interface ApiResponse<T = unknown> {
  data?: T;
  message?: string;
  errors?: Record<string, string[]>;
}

// 認証関連のレスポンス型（重複を削除）

export type PostsResponse = Post[];
export type CommentsResponse = Comment[];

// フォーム関連の型定義
export interface FormField {
  name: string;
  label: string;
  type: 'text' | 'email' | 'password' | 'number' | 'date';
  placeholder?: string;
  required?: boolean;
  validation?: unknown;
}

// UI関連の型定義
export interface Toast {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message?: string;
  duration?: number;
}

export interface ModalState {
  id: string;
  isOpen: boolean;
  data?: unknown;
}

export interface Notification {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message: string;
  read: boolean;
  created_at: string;
}

// ナビゲーション関連の型定義
export interface NavItem {
  id: string;
  label: string;
  href: string;
  icon: string;
  active?: boolean;
  children?: NavItem[];
}

// 統計関連の型定義
export interface QuitStats {
  quit_date: string;
  quit_days: number;
  quit_cigarettes: number;
  saved_money: number;
  extended_life: number;
  health_improvements: HealthImprovement[];
}

export interface HealthImprovement {
  milestone: string;
  description: string;
  achieved: boolean;
  achieved_at?: string;
}

// 設定関連の型定義
export interface UserSettings {
  theme: 'light' | 'dark' | 'system';
  notifications: {
    email: boolean;
    push: boolean;
    sms: boolean;
  };
  privacy: {
    profile_public: boolean;
    show_stats: boolean;
    allow_messages: boolean;
  };
}

// エラー関連の型定義
export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
  status?: number;
}

// ページネーション関連の型定義
export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: PaginationMeta;
}

// 検索・フィルタ関連の型定義
export interface SearchFilters {
  query?: string;
  category?: string;
  date_from?: string;
  date_to?: string;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
}

// ファイル関連の型定義
export interface FileUpload {
  id: string;
  name: string;
  size: number;
  type: string;
  url: string;
  uploaded_at: string;
}

// アクティビティ関連の型定義
export interface Activity {
  id: number;
  type: 'post_created' | 'comment_added' | 'like_received' | 'badge_earned' | 'milestone_reached';
  user_id: number;
  user?: User;
  data: unknown;
  created_at: string;
}
