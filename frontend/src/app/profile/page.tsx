'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { 
  Heart, 
  User, 
  Calendar, 
  Cigarette, 
  DollarSign, 
  Trophy, 
  Target,
  Clock,
  TrendingUp,
  Edit3,
  Save,
  X
} from 'lucide-react';
import { apiClient } from '@/lib/api-client';
import { UserProfile } from '@/types';
import toast from 'react-hot-toast';

export default function ProfilePage() {
  const { user, userProfile, isLoading, logout, refreshUserProfile } = useAuth();
  const [isEditing, setIsEditing] = useState(false);
  const [editData, setEditData] = useState({
    display_name: '',
    daily_cigarettes: 0,
    pack_cost: 0,
  });

  useEffect(() => {
    if (userProfile) {
      setEditData({
        display_name: userProfile.display_name || '',
        daily_cigarettes: userProfile.daily_cigarettes || 0,
        pack_cost: userProfile.pack_cost || 0,
      });
    }
  }, [userProfile]);

  const handleEdit = () => {
    setIsEditing(true);
  };

  const handleCancel = () => {
    setIsEditing(false);
    if (userProfile) {
      setEditData({
        display_name: userProfile.display_name || '',
        daily_cigarettes: userProfile.daily_cigarettes || 0,
        pack_cost: userProfile.pack_cost || 0,
      });
    }
  };

  const handleSave = async () => {
    try {
      await apiClient.updateUserProfile(editData);
      await refreshUserProfile();
      setIsEditing(false);
      toast.success('プロフィールを更新しました');
    } catch (error) {
      console.error('プロフィール更新エラー:', error);
      toast.error('プロフィールの更新に失敗しました');
    }
  };

  const handleResetQuitInfo = async () => {
    if (confirm('禁煙情報をリセットしますか？この操作は取り消せません。')) {
      try {
        await apiClient.resetQuitInfo();
        await refreshUserProfile();
        toast.success('禁煙情報をリセットしました');
      } catch (error) {
        console.error('禁煙情報リセットエラー:', error);
        toast.error('禁煙情報のリセットに失敗しました');
      }
    }
  };

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

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
      <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* ページヘッダー */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">プロフィール</h1>
            <p className="text-gray-600 mt-2">あなたの禁煙の進捗と設定を管理しましょう</p>
          </div>
          {!isEditing && (
            <Button onClick={handleEdit}>
              <Edit3 className="h-5 w-5 mr-2" />
              編集
            </Button>
          )}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* プロフィール情報 */}
          <div className="lg:col-span-2 space-y-6">
            {/* 基本情報 */}
            <div className="bg-white rounded-xl p-6 shadow-soft">
              <h2 className="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <User className="h-5 w-5 mr-2" />
                基本情報
              </h2>
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    表示名
                  </label>
                  {isEditing ? (
                    <Input
                      value={editData.display_name}
                      onChange={(e) => setEditData({ ...editData, display_name: e.target.value })}
                      placeholder="表示名を入力"
                    />
                  ) : (
                    <p className="text-gray-900">{userProfile.display_name || '未設定'}</p>
                  )}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    メールアドレス
                  </label>
                  <p className="text-gray-900">{user.email}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    登録日
                  </label>
                  <p className="text-gray-900">
                    {new Date(user.created_at).toLocaleDateString('ja-JP')}
                  </p>
                </div>
              </div>
            </div>

            {/* 禁煙設定 */}
            <div className="bg-white rounded-xl p-6 shadow-soft">
              <h2 className="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <Cigarette className="h-5 w-5 mr-2" />
                禁煙設定
              </h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    1日の喫煙本数
                  </label>
                  {isEditing ? (
                    <Input
                      type="number"
                      min="1"
                      value={editData.daily_cigarettes}
                      onChange={(e) => setEditData({ ...editData, daily_cigarettes: parseInt(e.target.value) || 0 })}
                      placeholder="例: 20"
                    />
                  ) : (
                    <p className="text-gray-900">{userProfile.daily_cigarettes || 0}本</p>
                  )}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    1箱の価格
                  </label>
                  {isEditing ? (
                    <Input
                      type="number"
                      min="300"
                      max="3000"
                      value={editData.pack_cost}
                      onChange={(e) => setEditData({ ...editData, pack_cost: parseInt(e.target.value) || 0 })}
                      placeholder="例: 500"
                    />
                  ) : (
                    <p className="text-gray-900">¥{userProfile.pack_cost || 0}</p>
                  )}
                </div>
              </div>
            </div>

            {/* 編集ボタン */}
            {isEditing && (
              <div className="flex justify-end space-x-3">
                <Button variant="outline" onClick={handleCancel}>
                  <X className="h-4 w-4 mr-2" />
                  キャンセル
                </Button>
                <Button onClick={handleSave}>
                  <Save className="h-4 w-4 mr-2" />
                  保存
                </Button>
              </div>
            )}
          </div>

          {/* サイドバー */}
          <div className="space-y-6">
            {/* 禁煙進捗 */}
            <div className="bg-white rounded-xl p-6 shadow-soft">
              <h2 className="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <Target className="h-5 w-5 mr-2" />
                禁煙進捗
              </h2>
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">禁煙日数</span>
                  <span className="text-2xl font-bold text-blue-600">
                    {Math.floor(Number(userProfile.quit_days_count ?? 0))}日
                  </span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">節約した本数</span>
                  <span className="text-lg font-semibold text-green-600">
                    {userProfile.quit_cigarettes || 0}本
                  </span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">節約した金額</span>
                  <span className="text-lg font-semibold text-green-600">
                    ¥{userProfile.saved_money || 0}
                  </span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-gray-600">延長した寿命</span>
                  <span className="text-lg font-semibold text-purple-600">
                    {userProfile.extended_life || 0}時間
                  </span>
                </div>
              </div>
            </div>

            {/* バッジ */}
            <div className="bg-white rounded-xl p-6 shadow-soft">
              <h2 className="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                <Trophy className="h-5 w-5 mr-2" />
                獲得バッジ
              </h2>
              <div className="text-center">
                {userProfile.badges && userProfile.badges.length > 0 ? (
                  <div className="space-y-2">
                    {userProfile.badges.map((badge, index) => (
                      <div key={index} className="flex items-center justify-center space-x-2">
                        <span className="text-2xl">{badge.icon}</span>
                        <span className="text-sm text-gray-600">{badge.name}</span>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="text-gray-500">まだバッジを獲得していません</p>
                )}
              </div>
            </div>

            {/* アクション */}
            <div className="bg-white rounded-xl p-6 shadow-soft">
              <h2 className="text-xl font-semibold text-gray-900 mb-4">アクション</h2>
              <div className="space-y-3">
                <Button 
                  variant="outline" 
                  fullWidth 
                  onClick={handleResetQuitInfo}
                  className="text-red-600 border-red-300 hover:bg-red-50"
                >
                  禁煙情報をリセット
                </Button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}

