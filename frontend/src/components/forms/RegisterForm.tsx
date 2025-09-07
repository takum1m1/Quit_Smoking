'use client';

import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import Link from 'next/link';
import { 
  Mail, 
  Lock, 
  Eye, 
  EyeOff, 
  User, 
  Cigarette, 
  DollarSign 
} from 'lucide-react';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { useAuth } from '@/contexts/AuthContext';

// 登録フォームのスキーマ（バックエンドのバリデーションルールに準拠）
const registerSchema = z.object({
  email: z
    .string()
    .min(1, 'メールアドレスを入力してください')
    .email('有効なメールアドレスを入力してください')
    .max(255, 'メールアドレスは255文字以下で入力してください'),
  password: z
    .string()
    .min(1, 'パスワードを入力してください')
    .min(8, 'パスワードは8文字以上で入力してください')
    .regex(
      /^(?=.*[a-zA-Z])(?=.*\d).+$/,
      'パスワードはアルファベット1文字と数字1文字を含む必要があります'
    ),
  password_confirmation: z
    .string()
    .min(1, 'パスワード確認を入力してください')
    .min(8, 'パスワード確認は8文字以上で入力してください'),
  display_name: z
    .string()
    .min(1, '表示名を入力してください')
    .max(20, '表示名は20文字以下で入力してください'),
  daily_cigarettes: z
    .number()
    .min(1, '1日の喫煙本数を入力してください')
    .int('1日の喫煙本数は整数で入力してください'),
  pack_cost: z
    .number()
    .min(300, '1箱の価格は300円以上で入力してください')
    .max(3000, '1箱の価格は3,000円以下で入力してください')
    .int('1箱の価格は整数で入力してください'),
}).refine((data) => data.password === data.password_confirmation, {
  message: 'パスワードが一致しません',
  path: ['password_confirmation'],
});

type RegisterFormData = z.infer<typeof registerSchema>;

/**
 * 登録フォームコンポーネント
 */
export function RegisterForm() {
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const { register: registerUser, isLoading } = useAuth();

  const {
    register,
    handleSubmit,
    formState: { errors },
    setError,
  } = useForm<RegisterFormData>({
    resolver: zodResolver(registerSchema),
  });

  /**
   * フォーム送信処理
   */
  const onSubmit = async (data: RegisterFormData) => {
    try {
      console.log('=== 登録フォーム送信開始 ===');
      console.log('登録フォームデータ:', data);
      console.log('データ型確認:', {
        email: typeof data.email,
        password: typeof data.password,
        password_confirmation: typeof data.password_confirmation,
        display_name: typeof data.display_name,
        daily_cigarettes: typeof data.daily_cigarettes,
        pack_cost: typeof data.pack_cost
      });
      
      console.log('registerUser関数を呼び出し中...');
      const success = await registerUser(data);
      console.log('registerUser結果:', success);
      
      if (!success) {
        console.log('登録失敗: エラーを設定');
        setError('root', {
          message: 'アカウント登録に失敗しました。入力内容を確認してください。',
        });
      } else {
        console.log('登録成功');
      }
    } catch (error) {
      console.error('=== 登録エラー詳細 ===');
      console.error('エラーオブジェクト:', error);
      console.error('エラーメッセージ:', error instanceof Error ? error.message : 'Unknown error');
      console.error('エラースタック:', error instanceof Error ? error.stack : 'No stack trace');
      setError('root', {
        message: '予期しないエラーが発生しました。しばらく時間をおいて再度お試しください。',
      });
    }
  };

  return (
    <div className="w-full max-w-md mx-auto">
      <div className="text-center mb-8">
        <h1 className="text-3xl font-bold text-foreground mb-2">
          アカウント登録
        </h1>
        <p className="text-muted-foreground">
          禁煙の旅を始めるためのアカウントを作成しましょう
        </p>
      </div>

      <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
        {/* メールアドレス */}
        <Input
          label="メールアドレス"
          type="email"
          placeholder="example@email.com"
          leftIcon={<Mail className="h-4 w-4" />}
          error={errors.email?.message}
          fullWidth
          {...register('email')}
        />

        {/* パスワード */}
        <Input
          label="パスワード"
          type={showPassword ? 'text' : 'password'}
          placeholder="パスワードを入力"
          leftIcon={<Lock className="h-4 w-4" />}
          rightIcon={
            <button
              type="button"
              onClick={() => setShowPassword(!showPassword)}
              className="text-muted-foreground hover:text-foreground transition-colors"
            >
              {showPassword ? (
                <EyeOff className="h-4 w-4" />
              ) : (
                <Eye className="h-4 w-4" />
              )}
            </button>
          }
          error={errors.password?.message}
          helperText="アルファベット1文字と数字1文字を含む8文字以上"
          fullWidth
          {...register('password')}
        />

        {/* パスワード確認 */}
        <Input
          label="パスワード確認"
          type={showConfirmPassword ? 'text' : 'password'}
          placeholder="パスワードを再入力"
          leftIcon={<Lock className="h-4 w-4" />}
          rightIcon={
            <button
              type="button"
              onClick={() => setShowConfirmPassword(!showConfirmPassword)}
              className="text-muted-foreground hover:text-foreground transition-colors"
            >
              {showConfirmPassword ? (
                <EyeOff className="h-4 w-4" />
              ) : (
                <Eye className="h-4 w-4" />
              )}
            </button>
          }
          error={errors.password_confirmation?.message}
          fullWidth
          {...register('password_confirmation')}
        />

        {/* 表示名 */}
        <Input
          label="表示名"
          type="text"
          placeholder="あなたの表示名"
          leftIcon={<User className="h-4 w-4" />}
          error={errors.display_name?.message}
          fullWidth
          {...register('display_name')}
        />

        {/* 1日の喫煙本数 */}
        <Input
          label="1日の喫煙本数"
          type="number"
          min="1"
          step="1"
          placeholder="例: 20"
          leftIcon={<Cigarette className="h-4 w-4" />}
          error={errors.daily_cigarettes?.message}
          helperText="禁煙前の1日の喫煙本数を入力してください（1本以上）"
          fullWidth
          {...register('daily_cigarettes', { valueAsNumber: true })}
        />

        {/* 1箱の価格 */}
        <Input
          label="1箱の価格（円）"
          type="number"
          min="300"
          max="3000"
          step="1"
          placeholder="例: 500"
          leftIcon={<DollarSign className="h-4 w-4" />}
          error={errors.pack_cost?.message}
          helperText="禁煙前の1箱の価格を入力してください（300円〜3,000円）"
          fullWidth
          {...register('pack_cost', { valueAsNumber: true })}
        />

        {/* エラーメッセージ */}
        {errors.root && (
          <div className="alert alert-error">
            <p className="text-sm">{errors.root.message}</p>
          </div>
        )}

        {/* 登録ボタン */}
        <Button
          type="submit"
          loading={isLoading}
          fullWidth
          size="lg"
        >
          アカウント登録
        </Button>

        {/* リンク */}
        <div className="text-center">
          <div className="text-sm text-muted-foreground">
            すでにアカウントをお持ちの方は{' '}
            <Link
              href="/auth/login"
              className="text-primary hover:underline transition-colors"
            >
              ログイン
            </Link>
          </div>
        </div>
      </form>
    </div>
  );
}
