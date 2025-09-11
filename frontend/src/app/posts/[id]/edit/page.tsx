'use client';

import { useState, useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { useAuth } from '@/contexts/AuthContext';
import { apiClient } from '@/lib/api-client';
import { Post } from '@/types';
import { Button } from '@/components/ui/Button';
import { ArrowLeft, Save, X } from 'lucide-react';
import toast from 'react-hot-toast';

export default function EditPostPage() {
  const { user } = useAuth();
  const params = useParams();
  const router = useRouter();
  const postId = parseInt(params.id as string);

  const [post, setPost] = useState<Post | null>(null);
  const [content, setContent] = useState('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    if (postId && user) {
      fetchPost();
    }
  }, [postId, user]);

  const fetchPost = async () => {
    try {
      setLoading(true);
      const response = await apiClient.getPost(postId);
      
      // 投稿の所有者かチェック
      if (response.user_id !== user?.id) {
        toast.error('この投稿を編集する権限がありません');
        router.push(`/posts/${postId}`);
        return;
      }
      
      setPost(response);
      setContent(response.content);
    } catch (error) {
      console.error('投稿の取得に失敗しました:', error);
      toast.error('投稿の取得に失敗しました');
      router.push('/posts');
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    if (!content.trim()) {
      toast.error('投稿内容を入力してください');
      return;
    }

    try {
      setSaving(true);
      await apiClient.updatePost(postId, { content: content.trim() });
      toast.success('投稿を更新しました');
      router.push(`/posts/${postId}`);
    } catch (error) {
      console.error('投稿の更新に失敗しました:', error);
      toast.error('投稿の更新に失敗しました');
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    router.push(`/posts/${postId}`);
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">投稿を読み込み中...</p>
        </div>
      </div>
    );
  }

  if (!post) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold text-gray-900 mb-4">投稿が見つかりません</h1>
          <Button onClick={() => router.push('/posts')}>
            コミュニティに戻る
          </Button>
        </div>
      </div>
    );
  }

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
          <h1 className="text-3xl font-bold text-gray-900">投稿を編集</h1>
        </div>

        {/* 編集フォーム */}
        <div className="bg-white rounded-xl shadow-lg p-6">
          <div className="space-y-6">
            {/* 投稿者情報 */}
            <div className="flex items-center space-x-3 pb-4 border-b border-gray-200">
              <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center">
                <span className="text-white font-semibold">
                  {post.user?.profile?.display_name?.charAt(0) || 'U'}
                </span>
              </div>
              <div>
                <p className="font-semibold text-gray-900">
                  {post.user?.profile?.display_name || '匿名ユーザー'}
                </p>
                <p className="text-sm text-gray-500">
                  {new Date(post.created_at).toLocaleDateString('ja-JP')}
                </p>
              </div>
            </div>

            {/* 投稿内容編集 */}
            <div>
              <label htmlFor="content" className="block text-sm font-medium text-gray-700 mb-2">
                投稿内容
              </label>
              <textarea
                id="content"
                value={content}
                onChange={(e) => setContent(e.target.value)}
                placeholder="投稿内容を入力..."
                className="w-full p-4 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                rows={8}
              />
              <p className="mt-2 text-sm text-gray-500">
                {content.length} 文字
              </p>
            </div>

            {/* アクションボタン */}
            <div className="flex justify-end space-x-3 pt-4 border-t border-gray-200">
              <Button
                variant="outline"
                onClick={handleCancel}
                disabled={saving}
              >
                <X className="h-4 w-4 mr-2" />
                キャンセル
              </Button>
              <Button
                onClick={handleSave}
                disabled={!content.trim() || saving}
              >
                <Save className="h-4 w-4 mr-2" />
                {saving ? '保存中...' : '保存'}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
