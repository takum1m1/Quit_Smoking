'use client';

import { useState, useEffect } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { useAuth } from '@/contexts/AuthContext';
import { apiClient } from '@/lib/api-client';
import { Post, Comment } from '@/types';
import { Button } from '@/components/ui/Button';
import { Heart, MessageCircle, ArrowLeft, User, Calendar, Edit, Trash2 } from 'lucide-react';
import toast from 'react-hot-toast';
import Link from 'next/link';

export default function PostDetailPage() {
  const { user, userProfile } = useAuth();
  const params = useParams();
  const router = useRouter();
  const postId = params.id as string;

  const [post, setPost] = useState<Post | null>(null);
  const [loading, setLoading] = useState(true);
  const [showCommentForm, setShowCommentForm] = useState(false);
  const [newCommentContent, setNewCommentContent] = useState('');
  const [isLiking, setIsLiking] = useState(false);
  const [isCommenting, setIsCommenting] = useState(false);

  useEffect(() => {
    if (postId && user) {
      fetchPost();
    }
  }, [postId, user]);

  const fetchPost = async () => {
    try {
      setLoading(true);
      const response = await apiClient.getPost(parseInt(postId));
      setPost(response);
    } catch (error) {
      console.error('投稿の取得に失敗しました:', error);
      toast.error('投稿の取得に失敗しました');
      router.push('/posts');
    } finally {
      setLoading(false);
    }
  };

  const handleLike = async () => {
    if (!post || isLiking) return;

    try {
      setIsLiking(true);
      await apiClient.likePost(post.id);
      await fetchPost(); // 投稿を再取得
      toast.success('いいねしました');
    } catch (error) {
      console.error('いいねの処理に失敗しました:', error);
      toast.error('いいねの処理に失敗しました');
    } finally {
      setIsLiking(false);
    }
  };

  const handleCreateComment = async () => {
    if (!post || !newCommentContent.trim() || isCommenting) return;

    try {
      setIsCommenting(true);
      await apiClient.createComment(post.id, { content: newCommentContent });
      setNewCommentContent('');
      setShowCommentForm(false);
      await fetchPost(); // 投稿を再取得
      toast.success('コメントを投稿しました');
    } catch (error) {
      console.error('コメントの投稿に失敗しました:', error);
      toast.error('コメントの投稿に失敗しました');
    } finally {
      setIsCommenting(false);
    }
  };

  const handleDeletePost = async () => {
    if (!post || !confirm('この投稿を削除しますか？')) return;

    try {
      await apiClient.deletePost(post.id);
      toast.success('投稿を削除しました');
      router.push('/posts');
    } catch (error) {
      console.error('投稿の削除に失敗しました:', error);
      toast.error('投稿の削除に失敗しました');
    }
  };

  const handleDeleteComment = async (commentId: number) => {
    if (!post || !confirm('このコメントを削除しますか？')) return;

    try {
      await apiClient.deleteComment(post.id, commentId);
      await fetchPost(); // 投稿を再取得
      toast.success('コメントを削除しました');
    } catch (error) {
      console.error('コメントの削除に失敗しました:', error);
      toast.error('コメントの削除に失敗しました');
    }
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

  const isLiked = post.likes?.some(like => like.user_id === user?.id);
  const isPostOwner = post.user_id === user?.id;

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
          <h1 className="text-3xl font-bold text-gray-900">投稿詳細</h1>
        </div>

        {/* 投稿カード */}
        <div className="bg-white rounded-xl shadow-lg p-6 mb-6">
          {/* 投稿者情報 */}
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center space-x-3">
              <div className="w-12 h-12 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center">
                <span className="text-white font-bold text-lg">
                  {post.user?.profile?.display_name?.charAt(0) || 'U'}
                </span>
              </div>
              <div>
                <Link 
                  href={`/users/${post.user_id}`}
                  className="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors"
                >
                  {post.user?.profile?.display_name || '匿名ユーザー'}
                </Link>
                <p className="text-sm text-gray-500 flex items-center">
                  <Calendar className="h-4 w-4 mr-1" />
                  {new Date(post.created_at).toLocaleDateString('ja-JP', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                  })}
                </p>
              </div>
            </div>
            
            {/* 投稿者のアクション */}
            {isPostOwner && (
              <div className="flex space-x-2">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => router.push(`/posts/${post.id}/edit`)}
                >
                  <Edit className="h-4 w-4 mr-1" />
                  編集
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={handleDeletePost}
                  className="text-red-600 hover:text-red-700"
                >
                  <Trash2 className="h-4 w-4 mr-1" />
                  削除
                </Button>
              </div>
            )}
          </div>

          {/* 投稿内容 */}
          <div className="mb-6">
            <p className="text-gray-800 leading-relaxed text-lg whitespace-pre-wrap">
              {post.content}
            </p>
          </div>

          {/* 投稿アクション */}
          <div className="flex items-center justify-between pt-4 border-t border-gray-200">
            <div className="flex items-center space-x-6">
              <button
                onClick={handleLike}
                disabled={isLiking}
                className={`flex items-center space-x-2 transition-colors ${
                  isLiked
                    ? 'text-red-500'
                    : 'text-gray-500 hover:text-red-500'
                } ${isLiking ? 'opacity-50 cursor-not-allowed' : ''}`}
              >
                <Heart 
                  className={`h-6 w-6 ${isLiked ? 'fill-current' : ''}`} 
                />
                <span className="font-semibold">{post.likes?.length || 0}</span>
              </button>
              
              <button 
                onClick={() => setShowCommentForm(!showCommentForm)}
                className="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition-colors"
              >
                <MessageCircle className="h-6 w-6" />
                <span className="font-semibold">{post.comments?.length || 0}</span>
              </button>
            </div>
          </div>
        </div>

        {/* コメントフォーム */}
        {showCommentForm && (
          <div className="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">コメントを投稿</h3>
            <div className="space-y-4">
              <textarea
                value={newCommentContent}
                onChange={(e) => setNewCommentContent(e.target.value)}
                placeholder="コメントを入力..."
                className="w-full p-4 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                rows={4}
              />
              <div className="flex justify-end space-x-3">
                <Button
                  variant="outline"
                  onClick={() => {
                    setShowCommentForm(false);
                    setNewCommentContent('');
                  }}
                >
                  キャンセル
                </Button>
                <Button
                  onClick={handleCreateComment}
                  disabled={!newCommentContent.trim() || isCommenting}
                >
                  {isCommenting ? '投稿中...' : 'コメントを投稿'}
                </Button>
              </div>
            </div>
          </div>
        )}

        {/* コメント一覧 */}
        <div className="bg-white rounded-xl shadow-lg p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">
            コメント ({post.comments?.length || 0})
          </h3>
          
          {post.comments && post.comments.length > 0 ? (
            <div className="space-y-4">
              {post.comments.map((comment) => (
                <div key={comment.id} className="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                  <div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span className="text-white font-semibold">
                      {comment.user?.profile?.display_name?.charAt(0) || 'U'}
                    </span>
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center justify-between mb-2">
                      <Link 
                        href={`/users/${comment.user_id}`}
                        className="font-semibold text-gray-900 hover:text-blue-600 transition-colors"
                      >
                        {comment.user?.profile?.display_name || '匿名ユーザー'}
                      </Link>
                      <div className="flex items-center space-x-2">
                        <span className="text-sm text-gray-500">
                          {new Date(comment.created_at).toLocaleDateString('ja-JP', {
                            month: 'short',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                          })}
                        </span>
                        {comment.user_id === user?.id && (
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleDeleteComment(comment.id)}
                            className="text-red-600 hover:text-red-700"
                          >
                            <Trash2 className="h-3 w-3" />
                          </Button>
                        )}
                      </div>
                    </div>
                    <p className="text-gray-800 whitespace-pre-wrap">{comment.content}</p>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-8 text-gray-500">
              <MessageCircle className="h-12 w-12 mx-auto mb-3 opacity-50" />
              <p>まだコメントがありません</p>
              <p className="text-sm">最初のコメントを投稿してみませんか？</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
