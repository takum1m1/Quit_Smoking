'use client';

import React from 'react';
import Link from 'next/link';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/Button';
import { Heart, TrendingUp, DollarSign, Calendar, Trophy } from 'lucide-react';

export default function DashboardPage() {
  const { user, userProfile, isLoading, logout } = useAuth();

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">読み込み中...</p>
        </div>
      </div>
    );
  }

  if (!user || !userProfile) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold text-gray-900 mb-4">アクセスできません</h1>
          <p className="text-gray-600 mb-6">ログインが必要です</p>
          <Link href="/auth/login">
            <Button>ログイン</Button>
          </Link>
        </div>
      </div>
    );
  }

  const quitDays = userProfile.quit_date 
    ? Math.floor((new Date().getTime() - new Date(userProfile.quit_date).getTime()) / (1000 * 60 * 60 * 24))
    : 0;

  const savedMoney = quitDays * (userProfile.daily_cigarettes || 0) * ((userProfile.pack_cost || 0) / 20);
  const extendedLife = quitDays * 0.1; // 1日0.1時間延長

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
      {/* ヘッダー */}
      <header className="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Heart className="h-8 w-8 text-red-500 mr-3" />
              <h1 className="text-2xl font-bold text-gray-900">QuitSmoking</h1>
            </div>
            <nav className="hidden md:flex space-x-8">
              <Link href="/dashboard" className="text-blue-600 font-medium">ダッシュボード</Link>
              <Link href="/posts" className="text-gray-600 hover:text-gray-900 transition-colors">コミュニティ</Link>
              <Link href="/profile" className="text-gray-600 hover:text-gray-900 transition-colors">プロフィール</Link>
            </nav>
            <div className="flex items-center space-x-4">
              <span className="text-gray-700">こんにちは、{userProfile.display_name || 'ユーザー'}さん</span>
              <Button variant="outline" size="sm" onClick={logout}>
                ログアウト
              </Button>
            </div>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* ウェルカムセクション */}
        <div className="text-center mb-12">
          <h1 className="text-4xl font-bold text-gray-900 mb-4">
            お疲れ様です、{userProfile.display_name || 'ユーザー'}さん！
          </h1>
          <p className="text-xl text-gray-600">
            禁煙の旅を続けているあなたを応援しています
          </p>
        </div>

        {/* 統計カード */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
          <div className="bg-white rounded-xl p-6 shadow-soft">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">禁煙日数</p>
                <p className="text-3xl font-bold text-blue-600">{quitDays}日</p>
              </div>
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <Calendar className="h-6 w-6 text-blue-600" />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl p-6 shadow-soft">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">節約金額</p>
                <p className="text-3xl font-bold text-green-600">¥{savedMoney.toLocaleString()}</p>
              </div>
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <DollarSign className="h-6 w-6 text-green-600" />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl p-6 shadow-soft">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">延長された寿命</p>
                <p className="text-3xl font-bold text-purple-600">{extendedLife.toFixed(1)}時間</p>
              </div>
              <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <TrendingUp className="h-6 w-6 text-purple-600" />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl p-6 shadow-soft">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-gray-600">獲得バッジ</p>
                <p className="text-3xl font-bold text-yellow-600">{userProfile.badges?.length || 0}個</p>
              </div>
              <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <Trophy className="h-6 w-6 text-yellow-600" />
              </div>
            </div>
          </div>
        </div>

        {/* アクションカード */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div className="bg-white rounded-xl p-8 shadow-soft">
            <h3 className="text-2xl font-bold text-gray-900 mb-4">今日の目標</h3>
            <div className="space-y-4">
              <div className="flex items-center">
                <div className="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                <span className="text-gray-700">禁煙を継続する</span>
              </div>
              <div className="flex items-center">
                <div className="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                <span className="text-gray-700">水分を十分に摂る</span>
              </div>
              <div className="flex items-center">
                <div className="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                <span className="text-gray-700">軽い運動をする</span>
              </div>
            </div>
            <div className="mt-6">
              <Button className="w-full">
                目標を更新
              </Button>
            </div>
          </div>

          <div className="bg-white rounded-xl p-8 shadow-soft">
            <h3 className="text-2xl font-bold text-gray-900 mb-4">コミュニティ</h3>
            <p className="text-gray-600 mb-6">
              同じ目標を持つ仲間たちと励まし合いましょう
            </p>
            <div className="space-y-4">
              <Link href="/posts" className="block">
                <Button className="w-full">投稿を見る</Button>
              </Link>
              <Link href="/posts/create" className="block">
                <Button variant="outline" className="w-full">新規投稿</Button>
              </Link>
            </div>
          </div>
        </div>

        {/* 最近の活動 */}
        <div className="mt-12">
          <h3 className="text-2xl font-bold text-gray-900 mb-6">最近の活動</h3>
          <div className="bg-white rounded-xl p-6 shadow-soft">
            <div className="text-center text-gray-500 py-8">
              <p>まだ活動がありません</p>
              <p className="text-sm mt-2">コミュニティに参加して、最初の投稿をしてみましょう！</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
