'use client';

import React, { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import Link from 'next/link';
import { Mail, Lock, Eye, EyeOff } from 'lucide-react';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { useAuth } from '@/contexts/AuthContext';

// ログインフォームのスキーマ
const loginSchema = z.object({
  email: z
    .string()
    .min(1, 'メールアドレスを入力してください')
    .email('有効なメールアドレスを入力してください'),
  password: z
    .string()
    .min(1, 'パスワードを入力してください')
    .min(8, 'パスワードは8文字以上で入力してください'),
});

type LoginFormData = z.infer<typeof loginSchema>;

/**
 * ログインフォームコンポーネント
 */
export function LoginForm() {
  const [showPassword, setShowPassword] = useState(false);
  const { login, isLoading } = useAuth();

  const {
    register,
    handleSubmit,
    formState: { errors },
    setError,
  } = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
  });

  /**
   * フォーム送信処理
   */
  const onSubmit = async (data: LoginFormData) => {
    try {
      const success = await login(data);
      if (!success) {
        setError('root', {
          message: 'ログインに失敗しました。メールアドレスとパスワードを確認してください。',
        });
      }
    } catch (error) {
      console.error('ログインエラー:', error);
      setError('root', {
        message: '予期しないエラーが発生しました。しばらく時間をおいて再度お試しください。',
      });
    }
  };

  return (
    <div className="w-full max-w-md mx-auto">
      <div className="text-center mb-8">
        <h1 className="text-3xl font-bold text-foreground mb-2">
          ログイン
        </h1>
        <p className="text-muted-foreground">
          アカウントにログインして禁煙の旅を続けましょう
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
        <div className="space-y-2">
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
            fullWidth
            {...register('password')}
          />
        </div>

        {/* エラーメッセージ */}
        {errors.root && (
          <div className="alert alert-error">
            <p className="text-sm">{errors.root.message}</p>
          </div>
        )}

        {/* ログインボタン */}
        <Button
          type="submit"
          loading={isLoading}
          className="w-full"
          size="lg"
        >
          ログイン
        </Button>

        {/* リンク */}
        <div className="text-center space-y-2">
          <Link
            href="/auth/forgot-password"
            className="text-sm text-primary hover:underline transition-colors"
          >
            パスワードを忘れた方はこちら
          </Link>
          
          <div className="text-sm text-muted-foreground">
            アカウントをお持ちでない方は{' '}
            <Link
              href="/auth/register"
              className="text-primary hover:underline transition-colors"
            >
              新規登録
            </Link>
          </div>
        </div>
      </form>
    </div>
  );
}
