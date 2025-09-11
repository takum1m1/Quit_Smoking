'use client';

import { useState, useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { useAuth } from '@/contexts/AuthContext';
import { apiClient } from '@/lib/api-client';
import { UserProfile, Post } from '@/types';
import { Button } from '@/components/ui/Button';
import { ArrowLeft, User, Calendar, Award, TrendingUp, Heart, MessageCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import Link from 'next/link';

export default function UserProfilePage() {
  const { user: currentUser } = useAuth();
  const params = useParams();
  const router = useRouter();
  const userId = parseInt(params.id as string);

  const [userProfile, setUserProfile] = useState<UserProfile | null>(null);
  const [userPosts, setUserPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [postsLoading, setPostsLoading] = useState(false);

  useEffect(() => {
    if (userId && currentUser) {
      fetchUserProfile();
      fetchUserPosts();
    }
  }, [userId, currentUser]);

  const fetchUserProfile = async () => {
    try {
      setLoading(true);
      const response = await apiClient.getUserProfile(userId);
      setUserProfile(response);
    } catch (error) {
      console.error('ユーザープロフィールの取得に失敗しました:', error);
      toast.error('ユーザープロフィールの取得に失敗しました');
      router.push('/posts');
    } finally {
      setLoading(false);
    }
  };

  const fetchUserPosts = async () => {
    try {
      setPostsLoading(true);
      const posts = await apiClient.getPosts(1, 20);
      // このユーザーの投稿のみをフィルタリングし新着順に
      const filteredPosts = posts
        .filter(post => post.user_id === userId)
        .sort(
          (a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
        );
      setUserPosts(filteredPosts);
    } catch (error) {
      console.error('ユーザーの投稿取得に失敗しました:', error);
      toast.error('ユーザーの投稿取得に失敗しました');
    } finally {
      setPostsLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">プロフィールを読み込み中...</p>
        </div>
      </div>
    );
  }

  if (!userProfile) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold text-gray-900 mb-4">ユーザーが見つかりません</h1>
          <Button onClick={() => router.push('/posts')}>
            コミュニティに戻る
          </Button>
        </div>
      </div>
    );
  }

  const isOwnProfile = currentUser?.id === userId;
  const quitDays = userProfile.quit_date 
    ? Math.floor((new Date().getTime() - new Date(userProfile.quit_date).getTime()) / (1000 * 60 * 60 * 24))
    : 0;

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
      <div className="container mx-auto px-4 py-8">
        {/* ヘッダー */}
        <div className="mb-6">
          <Button
            variant="outline"
            onClick={() => router.back()}
            className="mb-4"
          >
            <ArrowLeft className="h-4 w-4 mr-2" />
            戻る
          </Button>
          <h1 className="text-3xl font-bold text-gray-900">ユーザープロフィール</h1>
        </div>

        {/* プロフィールカード */}
        <div className="bg-white rounded-xl shadow-lg p-6 mb-6">
          <div className="flex items-start space-x-6">
            {/* アバター */}
            <div className="w-24 h-24 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center flex-shrink-0">
              <span className="text-white font-bold text-3xl">
                {userProfile.display_name?.charAt(0) || 'U'}
              </span>
            </div>

            {/* プロフィール情報 */}
            <div className="flex-1">
              <h2 className="text-2xl font-bold text-gray-900 mb-2">
                {userProfile.display_name || '匿名ユーザー'}
              </h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {/* 禁煙統計 */}
                <div className="space-y-3">
                  <h3 className="text-lg font-semibold text-gray-900 flex items-center">
                    <TrendingUp className="h-5 w-5 mr-2 text-green-600" />
                    禁煙統計
                  </h3>
                  
                  <div className="grid grid-cols-2 gap-3">
                    <div className="bg-green-50 p-3 rounded-lg">
                      <p className="text-sm text-green-600 font-medium">禁煙日数</p>
                      <p className="text-2xl font-bold text-green-700">{quitDays}日</p>
                    </div>
                    
                    <div className="bg-blue-50 p-3 rounded-lg">
                      <p className="text-sm text-blue-600 font-medium">節約金額</p>
                      <p className="text-2xl font-bold text-blue-700">
                        ¥{Math.floor(userProfile.saved_money || 0).toLocaleString()}
                      </p>
                    </div>
                    
                    <div className="bg-purple-50 p-3 rounded-lg">
                      <p className="text-sm text-purple-600 font-medium">吸わなかった本数</p>
                      <p className="text-2xl font-bold text-purple-700">
                        {Math.floor(userProfile.quit_cigarettes || 0)}本
                      </p>
                    </div>
                    
                    <div className="bg-orange-50 p-3 rounded-lg">
                      <p className="text-sm text-orange-600 font-medium">延命時間</p>
                      <p className="text-2xl font-bold text-orange-700">
                        {Math.floor(userProfile.extended_life || 0)}時間
                      </p>
                    </div>
                  </div>
                </div>

                {/* ユーザー情報 */}
                <div className="space-y-3">
                  <h3 className="text-lg font-semibold text-gray-900 flex items-center">
                    <User className="h-5 w-5 mr-2 text-blue-600" />
                    ユーザー情報
                  </h3>
                  
                  <div className="space-y-2">
                    <div className="flex items-center text-gray-600">
                      <Calendar className="h-4 w-4 mr-2" />
                      <span className="text-sm">登録日: {new Date(userProfile.created_at).toLocaleDateString('ja-JP')}</span>
                    </div>
                    
                    {userProfile.quit_date && (
                      <div className="flex items-center text-gray-600">
                        <Calendar className="h-4 w-4 mr-2" />
                        <span className="text-sm">禁煙開始日: {new Date(userProfile.quit_date).toLocaleDateString('ja-JP')}</span>
                      </div>
                    )}
                    
                    <div className="flex items-center text-gray-600">
                      <span className="text-sm">1日の本数: {userProfile.daily_cigarettes || 0}本</span>
                    </div>
                    
                    <div className="flex items-center text-gray-600">
                      <span className="text-sm">1箱の価格: ¥{userProfile.pack_cost || 0}</span>
                    </div>
                  </div>
                </div>
              </div>

              {/* バッジ */}
              {userProfile.badges && userProfile.badges.length > 0 && (
                <div className="mb-6">
                  <h3 className="text-lg font-semibold text-gray-900 flex items-center mb-3">
                    <Award className="h-5 w-5 mr-2 text-yellow-600" />
                    獲得バッジ
                  </h3>
                  <div className="flex flex-wrap gap-2">
                    {userProfile.badges.map((badge, index) => (
                      <span
                        key={index}
                        className="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium"
                      >
                        {badge.name}
                      </span>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* 投稿一覧 */}
        <div className="bg-white rounded-xl shadow-lg p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">
            {isOwnProfile ? 'あなたの投稿' : `${userProfile.display_name}の投稿`} ({userPosts.length})
          </h3>
          
          {postsLoading ? (
            <div className="text-center py-8">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p className="mt-2 text-gray-600">投稿を読み込み中...</p>
            </div>
          ) : userPosts.length > 0 ? (
            <div className="space-y-4">
              {userPosts.map((post) => (
                <div key={post.id} className="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                  <div className="flex items-center justify-between mb-3">
                    <div className="flex items-center space-x-3">
                      <div className="w-8 h-8 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center">
                        <span className="text-white font-semibold text-sm">
                          {userProfile.display_name?.charAt(0) || 'U'}
                        </span>
                      </div>
                      <div>
                        <p className="font-semibold text-gray-900">
                          {userProfile.display_name || '匿名ユーザー'}
                        </p>
                        <p className="text-sm text-gray-500">
                          {new Date(post.created_at).toLocaleDateString('ja-JP', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                          })}
                        </p>
                      </div>
                    </div>
                  </div>
                  
                  <div className="mb-3">
                    <Link 
                      href={`/posts/${post.id}`}
                      className="text-gray-800 hover:text-blue-600 transition-colors"
                    >
                      <p className="line-clamp-3">{post.content}</p>
                    </Link>
                  </div>
                  
                  <div className="flex items-center space-x-4 text-sm text-gray-500">
                    <div className="flex items-center space-x-1">
                      <Heart className="h-4 w-4" />
                      <span>{post.likes?.length || 0}</span>
                    </div>
                    <div className="flex items-center space-x-1">
                      <MessageCircle className="h-4 w-4" />
                      <span>{post.comments?.length || 0}</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-8 text-gray-500">
              <User className="h-12 w-12 mx-auto mb-3 opacity-50" />
              <p>まだ投稿がありません</p>
              {isOwnProfile && (
                <p className="text-sm">最初の投稿を作成してみませんか？</p>
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
