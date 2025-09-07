'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/Button';
import { Input } from '@/components/ui/Input';
import { Heart, MessageCircle, Share2, Plus, Search, Filter } from 'lucide-react';
import { apiClient } from '@/lib/api-client';
import { Post } from '@/types';
import toast from 'react-hot-toast';

export default function PostsPage() {
  const { user, userProfile, isLoading, logout } = useAuth();
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [newPostContent, setNewPostContent] = useState('');
  const [showCommentForm, setShowCommentForm] = useState<number | null>(null);
  const [newCommentContent, setNewCommentContent] = useState('');

  useEffect(() => {
    fetchPosts();
  }, []);

  const fetchPosts = async () => {
    try {
      setLoading(true);
      const response = await apiClient.getPosts();
      setPosts(response);
    } catch (error) {
      console.error('投稿の取得に失敗しました:', error);
      toast.error('投稿の取得に失敗しました');
      setPosts([]);
    } finally {
      setLoading(false);
    }
  };

  const handleCreatePost = async () => {
    if (!newPostContent.trim()) {
      toast.error('投稿内容を入力してください');
      return;
    }

    try {
      const response = await apiClient.createPost({ content: newPostContent });
      if (response && response.id) {
        toast.success('投稿が作成されました');
        setNewPostContent('');
        setShowCreateForm(false);
        fetchPosts(); // 投稿一覧を再取得
      }
    } catch (error) {
      console.error('投稿の作成に失敗しました:', error);
      toast.error('投稿の作成に失敗しました');
    }
  };

  const handleLike = async (postId: number) => {
    try {
      await apiClient.likePost(postId);
      fetchPosts(); // 投稿一覧を再取得
    } catch (error) {
      console.error('いいねの処理に失敗しました:', error);
      toast.error('いいねの処理に失敗しました');
    }
  };

  const handleCreateComment = async (postId: number) => {
    if (!newCommentContent.trim()) {
      toast.error('コメント内容を入力してください');
      return;
    }

    try {
      await apiClient.createComment(postId, { content: newCommentContent });
      toast.success('コメントを投稿しました');
      setNewCommentContent('');
      setShowCommentForm(null);
      fetchPosts(); // 投稿一覧を再取得
    } catch (error) {
      console.error('コメントの投稿に失敗しました:', error);
      toast.error('コメントの投稿に失敗しました');
    }
  };

  const filteredPosts = posts.filter(post =>
    post.content.toLowerCase().includes(searchQuery.toLowerCase()) ||
    (post.user?.profile?.display_name || '').toLowerCase().includes(searchQuery.toLowerCase())
  );

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
      {/* ヘッダー */}
      <header className="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Heart className="h-8 w-8 text-red-500 mr-3" />
              <h1 className="text-2xl font-bold text-gray-900">QuitSmoking</h1>
            </div>
            <nav className="hidden md:flex space-x-8">
              <Link href="/dashboard" className="text-gray-600 hover:text-gray-900 transition-colors">ダッシュボード</Link>
              <Link href="/posts" className="text-blue-600 font-medium">コミュニティ</Link>
              <Link href="/profile" className="text-gray-600 hover:text-gray-900 transition-colors">プロフィール</Link>
            </nav>
            <div className="flex items-center space-x-4">
              <span className="text-gray-700">こんにちは、{userProfile.display_name}さん</span>
              <Button variant="outline" size="sm" onClick={logout}>
                ログアウト
              </Button>
            </div>
          </div>
        </div>
      </header>

      <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* ページヘッダー */}
        <div className="flex justify-between items-center mb-8">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">コミュニティ</h1>
            <p className="text-gray-600 mt-2">同じ目標を持つ仲間たちと励まし合いましょう</p>
          </div>
          <Button onClick={() => setShowCreateForm(!showCreateForm)}>
            <Plus className="h-5 w-5 mr-2" />
            新規投稿
          </Button>
        </div>

        {/* 検索・フィルター */}
        <div className="bg-white rounded-xl p-6 shadow-soft mb-8">
          <div className="flex gap-4">
            <div className="flex-1">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                <Input
                  type="text"
                  placeholder="投稿やユーザーを検索..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="pl-10"
                />
              </div>
            </div>
            <Button variant="outline">
              <Filter className="h-5 w-5 mr-2" />
              フィルター
            </Button>
          </div>
        </div>

        {/* 新規投稿フォーム */}
        {showCreateForm && (
          <div className="bg-white rounded-xl p-6 shadow-soft mb-8">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">新規投稿</h3>
            <textarea
              value={newPostContent}
              onChange={(e) => setNewPostContent(e.target.value)}
              placeholder="今日の禁煙の様子や、仲間へのメッセージを書いてみましょう..."
              className="w-full p-4 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              rows={4}
            />
            <div className="flex justify-end gap-3 mt-4">
              <Button variant="outline" onClick={() => setShowCreateForm(false)}>
                キャンセル
              </Button>
              <Button onClick={handleCreatePost}>
                投稿する
              </Button>
            </div>
          </div>
        )}

        {/* 投稿一覧 */}
        {loading ? (
          <div className="text-center py-12">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p className="mt-4 text-gray-600">投稿を読み込み中...</p>
          </div>
        ) : filteredPosts.length === 0 ? (
          <div className="bg-white rounded-xl p-12 shadow-soft text-center">
            <Heart className="h-16 w-16 text-gray-300 mx-auto mb-4" />
            <h3 className="text-xl font-semibold text-gray-900 mb-2">
              {searchQuery ? '検索結果がありません' : 'まだ投稿がありません'}
            </h3>
            <p className="text-gray-600 mb-6">
              {searchQuery 
                ? '別のキーワードで検索してみてください'
                : '最初の投稿を作成して、コミュニティを始めましょう！'
              }
            </p>
            {!searchQuery && (
              <Button onClick={() => setShowCreateForm(true)}>
                <Plus className="h-5 w-5 mr-2" />
                最初の投稿を作成
              </Button>
            )}
          </div>
        ) : (
          <div className="space-y-6">
            {filteredPosts.map((post) => (
              <div key={post.id} className="bg-white rounded-xl p-6 shadow-soft">
                {/* 投稿ヘッダー */}
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center space-x-3">
                    <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <span className="text-blue-600 font-semibold">
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
                </div>

                {/* 投稿内容 */}
                <div className="mb-4">
                  <p className="text-gray-800 leading-relaxed">{post.content}</p>
                </div>

                {/* 投稿アクション */}
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-6">
                    <button
                      onClick={() => handleLike(post.id)}
                      className={`flex items-center space-x-2 transition-colors ${
                        post.likes?.some(like => like.user_id === user?.id)
                          ? 'text-red-500'
                          : 'text-gray-500 hover:text-red-500'
                      }`}
                    >
                      <Heart 
                        className={`h-5 w-5 ${
                          post.likes?.some(like => like.user_id === user?.id)
                            ? 'fill-current'
                            : ''
                        }`} 
                      />
                      <span>{post.likes?.length || 0}</span>
                    </button>
                    <button 
                      onClick={() => setShowCommentForm(showCommentForm === post.id ? null : post.id)}
                      className="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition-colors"
                    >
                      <MessageCircle className="h-5 w-5" />
                      <span>{post.comments?.length || 0}</span>
                    </button>
                    <button className="flex items-center space-x-2 text-gray-500 hover:text-green-500 transition-colors">
                      <Share2 className="h-5 w-5" />
                    </button>
                  </div>
                </div>

                {/* コメントフォーム */}
                {showCommentForm === post.id && (
                  <div className="mt-4 pt-4 border-t border-gray-200">
                    <div className="flex space-x-3">
                      <textarea
                        value={newCommentContent}
                        onChange={(e) => setNewCommentContent(e.target.value)}
                        placeholder="コメントを入力..."
                        className="flex-1 p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        rows={2}
                      />
                      <div className="flex flex-col space-y-2">
                        <Button 
                          onClick={() => handleCreateComment(post.id)}
                          size="sm"
                        >
                          投稿
                        </Button>
                        <Button 
                          variant="outline" 
                          size="sm"
                          onClick={() => {
                            setShowCommentForm(null);
                            setNewCommentContent('');
                          }}
                        >
                          キャンセル
                        </Button>
                      </div>
                    </div>
                  </div>
                )}

                {/* コメント一覧 */}
                {post.comments && post.comments.length > 0 && (
                  <div className="mt-4 pt-4 border-t border-gray-200">
                    <div className="space-y-3">
                      {post.comments.map((comment) => (
                        <div key={comment.id} className="flex items-start space-x-3">
                          <div className="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <span className="text-gray-600 text-sm font-semibold">
                              {comment.user?.profile?.display_name?.charAt(0) || 'U'}
                            </span>
                          </div>
                          <div className="flex-1">
                            <div className="bg-gray-50 rounded-lg p-3">
                              <p className="text-sm font-semibold text-gray-900">
                                {comment.user?.profile?.display_name || '匿名ユーザー'}
                              </p>
                              <p className="text-gray-800 mt-1">{comment.content}</p>
                            </div>
                            <p className="text-xs text-gray-500 mt-1">
                              {new Date(comment.created_at).toLocaleDateString('ja-JP')}
                            </p>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </main>
    </div>
  );
}
