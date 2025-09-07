import React from 'react';
import Link from 'next/link';
import { Heart } from 'lucide-react';

export function Footer() {
  return (
    <footer className="bg-gray-900 text-white py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <div className="flex items-center mb-4">
              <Heart className="h-8 w-8 text-red-500 mr-3" />
              <h3 className="text-xl font-bold">QuitSmoking</h3>
            </div>
            <p className="text-gray-400">
              科学的根拠に基づいた禁煙サポートで、
              あなたの禁煙成功をサポートします。
            </p>
          </div>
          
          <div>
            <h4 className="text-lg font-semibold mb-4">機能</h4>
            <ul className="space-y-2 text-gray-400">
              <li><Link href="#features" className="hover:text-white transition-colors">進捗管理</Link></li>
              <li><Link href="#features" className="hover:text-white transition-colors">コミュニティ</Link></li>
              <li><Link href="#features" className="hover:text-white transition-colors">バッジシステム</Link></li>
              <li><Link href="#features" className="hover:text-white transition-colors">健康改善</Link></li>
            </ul>
          </div>
          
          <div>
            <h4 className="text-lg font-semibold mb-4">サポート</h4>
            <ul className="space-y-2 text-gray-400">
              <li><Link href="#contact" className="hover:text-white transition-colors">お問い合わせ</Link></li>
              <li><Link href="#about" className="hover:text-white transition-colors">ヘルプ</Link></li>
              <li><Link href="#about" className="hover:text-white transition-colors">FAQ</Link></li>
              <li><Link href="#about" className="hover:text-white transition-colors">プライバシーポリシー</Link></li>
            </ul>
          </div>
          
          <div>
            <h4 className="text-lg font-semibold mb-4">コミュニティ</h4>
            <ul className="space-y-2 text-gray-400">
              <li><Link href="/posts" className="hover:text-white transition-colors">投稿一覧</Link></li>
              <li><Link href="/posts/create" className="hover:text-white transition-colors">新規投稿</Link></li>
              <li><Link href="#about" className="hover:text-white transition-colors">ガイドライン</Link></li>
              <li><Link href="#about" className="hover:text-white transition-colors">コミュニティルール</Link></li>
            </ul>
          </div>
        </div>
        
        <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2024 QuitSmoking. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}
