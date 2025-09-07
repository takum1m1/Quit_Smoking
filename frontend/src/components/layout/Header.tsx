'use client';

import React from 'react';
import Link from 'next/link';
import { useAuth } from '@/contexts/AuthContext';
import { Button } from '@/components/ui/Button';
import { Heart } from 'lucide-react';

interface HeaderProps {
  currentPath?: string;
}

export function Header({ currentPath = '/' }: HeaderProps) {
  const { user, userProfile, logout } = useAuth();

  return (
    <header className="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          <div className="flex items-center">
            <Link href="/" className="flex items-center">
              <Heart className="h-8 w-8 text-red-500 mr-3" />
              <h1 className="text-2xl font-bold text-gray-900">QuitSmoking</h1>
            </Link>
          </div>
          
          {user && userProfile ? (
            <>
              <nav className="hidden md:flex space-x-8">
                <Link 
                  href="/dashboard" 
                  className={`transition-colors ${
                    currentPath === '/dashboard' 
                      ? 'text-blue-600 font-medium' 
                      : 'text-gray-600 hover:text-gray-900'
                  }`}
                >
                  ダッシュボード
                </Link>
                <Link 
                  href="/posts" 
                  className={`transition-colors ${
                    currentPath === '/posts' 
                      ? 'text-blue-600 font-medium' 
                      : 'text-gray-600 hover:text-gray-900'
                  }`}
                >
                  コミュニティ
                </Link>
                <Link 
                  href="/profile" 
                  className={`transition-colors ${
                    currentPath === '/profile' 
                      ? 'text-blue-600 font-medium' 
                      : 'text-gray-600 hover:text-gray-900'
                  }`}
                >
                  プロフィール
                </Link>
              </nav>
              <div className="flex items-center space-x-4">
                <span className="text-gray-700">こんにちは、{userProfile.display_name}さん</span>
                <Button variant="outline" size="sm" onClick={logout}>
                  ログアウト
                </Button>
              </div>
            </>
          ) : (
            <div className="flex items-center space-x-4">
              <Link href="/auth/login">
                <Button variant="ghost" size="sm">
                  ログイン
                </Button>
              </Link>
              <Link href="/auth/register">
                <Button size="sm">
                  無料で始める
                </Button>
              </Link>
            </div>
          )}
        </div>
      </div>
    </header>
  );
}
