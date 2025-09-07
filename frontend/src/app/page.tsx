import React from 'react';
import Link from 'next/link';
import { 
  Heart, 
  DollarSign, 
  Users, 
  Trophy, 
  Calendar, 
  TrendingUp,
  ArrowRight,
  CheckCircle
} from 'lucide-react';
import { Button } from '@/components/ui/Button';

/**
 * ランディングページ
 * 禁煙アプリの魅力を伝えるメインページ
 */
export default function HomePage() {
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
              <Link href="#features" className="text-gray-600 hover:text-gray-900 transition-colors">
                機能
              </Link>
              <Link href="#about" className="text-gray-600 hover:text-gray-900 transition-colors">
                について
              </Link>
              <Link href="#contact" className="text-gray-600 hover:text-gray-900 transition-colors">
                お問い合わせ
              </Link>
            </nav>
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
          </div>
        </div>
      </header>

      {/* メインコンテンツ */}
      <main>
        {/* ヒーローセクション */}
        <section className="py-20 px-4 sm:px-6 lg:px-8">
          <div className="max-w-7xl mx-auto text-center">
            <h1 className="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
              禁煙の旅を
              <span className="text-blue-600 block">一緒に歩もう</span>
            </h1>
            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              科学的根拠に基づいた禁煙サポートと、同じ目標を持つ仲間たちとの交流で、
              あなたの禁煙成功をサポートします。
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/auth/register">
                <Button size="lg" className="text-lg px-8 py-4">
                  今すぐ始める
                  <ArrowRight className="ml-2 h-5 w-5" />
                </Button>
              </Link>
              <Link href="#features">
                <Button variant="outline" size="lg" className="text-lg px-8 py-4">
                  詳しく見る
                </Button>
              </Link>
            </div>
          </div>
        </section>

        {/* 統計セクション */}
        <section className="py-16 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div className="text-center">
                <div className="text-4xl font-bold text-blue-600 mb-2">10,000+</div>
                <div className="text-gray-600">禁煙成功者</div>
              </div>
              <div className="text-center">
                <div className="text-4xl font-bold text-green-600 mb-2">¥50M+</div>
                <div className="text-gray-600">節約金額</div>
              </div>
              <div className="text-center">
                <div className="text-4xl font-bold text-purple-600 mb-2">98%</div>
                <div className="text-gray-600">満足度</div>
              </div>
            </div>
          </div>
        </section>

        {/* 機能セクション */}
        <section id="features" className="py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-4xl font-bold text-gray-900 mb-4">
                あなたの禁煙をサポートする機能
              </h2>
              <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                科学的根拠に基づいた禁煙サポートと、コミュニティ機能で
                禁煙の成功を確実にします。
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {/* 進捗管理 */}
              <div className="bg-white rounded-xl p-6 shadow-soft hover:shadow-medium transition-shadow">
                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                  <TrendingUp className="h-6 w-6 text-blue-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-2">進捗管理</h3>
                <p className="text-gray-600">
                  禁煙開始日からの経過日数、節約金額、健康改善を可視化して
                  モチベーションを維持します。
                </p>
              </div>

              {/* コミュニティ */}
              <div className="bg-white rounded-xl p-6 shadow-soft hover:shadow-medium transition-shadow">
                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                  <Users className="h-6 w-6 text-green-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-2">コミュニティ</h3>
                <p className="text-gray-600">
                  同じ目標を持つ仲間たちと励まし合い、経験を共有して
                  禁煙の成功を加速させます。
                </p>
              </div>

              {/* バッジシステム */}
              <div className="bg-white rounded-xl p-6 shadow-soft hover:shadow-medium transition-shadow">
                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                  <Trophy className="h-6 w-6 text-purple-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-2">バッジシステム</h3>
                <p className="text-gray-600">
                  禁煙のマイルストーンを達成すると、特別なバッジを獲得。
                  ゲーミフィケーションで楽しく禁煙を続けられます。
                </p>
              </div>

              {/* 健康改善 */}
              <div className="bg-white rounded-xl p-6 shadow-soft hover:shadow-medium transition-shadow">
                <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                  <Heart className="h-6 w-6 text-red-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-2">健康改善</h3>
                <p className="text-gray-600">
                  禁煙による健康改善をリアルタイムで確認。
                  血圧、肺機能、心臓病リスクの改善を実感できます。
                </p>
              </div>

              {/* 節約計算 */}
              <div className="bg-white rounded-xl p-6 shadow-soft hover:shadow-medium transition-shadow">
                <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                  <DollarSign className="h-6 w-6 text-yellow-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-2">節約計算</h3>
                <p className="text-gray-600">
                  禁煙による節約金額を自動計算。
                  将来の夢や目標に使えるお金が増えていくのを実感できます。
                </p>
              </div>

              {/* カスタマイズ */}
              <div className="bg-white rounded-xl p-6 shadow-soft hover:shadow-medium transition-shadow">
                <div className="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                  <Calendar className="h-6 w-6 text-indigo-600" />
                </div>
                <h3 className="text-xl font-semibold text-gray-900 mb-2">カスタマイズ</h3>
                <p className="text-gray-600">
                  あなたの禁煙スタイルに合わせてカスタマイズ。
                  個別の目標設定とリマインダーで確実に禁煙を成功させます。
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* メリットセクション */}
        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-4xl font-bold text-gray-900 mb-4">
                禁煙のメリット
              </h2>
              <p className="text-xl text-gray-600">
                禁煙することで得られる数多くのメリットをご紹介します
              </p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
              <div>
                <h3 className="text-2xl font-semibold text-gray-900 mb-6">
                  健康面での改善
                </h3>
                <div className="space-y-4">
                  {[
                    '血圧と脈拍が正常値に戻る（20分後）',
                    '血液中の一酸化炭素レベルが正常値に戻る（8時間後）',
                    '心臓発作のリスクが低下し始める（24時間後）',
                    '神経終末が再生し始め、味覚と嗅覚が改善（48時間後）',
                    '気管支が弛緩し、肺活量が増加（72時間後）',
                    '循環機能が改善し、運動能力が向上（2週間後）',
                    '肌の色艶が改善し、しわが減少（1ヶ月後）',
                    '肺機能が大幅に改善（3ヶ月後）',
                    '冠動脈疾患のリスクが半減（1年後）',
                  ].map((benefit, index) => (
                    <div key={index} className="flex items-start">
                      <CheckCircle className="h-5 w-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" />
                      <span className="text-gray-700">{benefit}</span>
                    </div>
                  ))}
                </div>
              </div>

              <div className="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-8 text-white">
                <h3 className="text-2xl font-semibold mb-6">経済面でのメリット</h3>
                <div className="space-y-4">
                  <div className="text-center p-4 bg-white/20 rounded-lg">
                    <div className="text-3xl font-bold">¥150,000</div>
                    <div className="text-sm opacity-90">年間の節約金額（1日20本、1箱500円の場合）</div>
                  </div>
                  <div className="text-center p-4 bg-white/20 rounded-lg">
                    <div className="text-3xl font-bold">¥1,500,000</div>
                    <div className="text-sm opacity-90">10年間の節約金額</div>
                  </div>
                  <div className="text-center p-4 bg-white/20 rounded-lg">
                    <div className="text-3xl font-bold">¥3,000,000</div>
                    <div className="text-sm opacity-90">20年間の節約金額</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* CTAセクション */}
        <section className="py-20 bg-blue-600">
          <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 className="text-4xl font-bold text-white mb-4">
              今すぐ禁煙の旅を始めませんか？
            </h2>
            <p className="text-xl text-blue-100 mb-8">
              科学的根拠に基づいたサポートと、仲間たちとの交流で
              あなたの禁煙成功を確実にします。
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/auth/register">
                <Button size="lg" variant="secondary" className="text-lg px-8 py-4">
                  無料で始める
                  <ArrowRight className="ml-2 h-5 w-5" />
                </Button>
              </Link>
              <Link href="/auth/login">
                <Button size="lg" variant="outline" className="text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-blue-600">
                  ログイン
                </Button>
              </Link>
            </div>
          </div>
        </section>
      </main>

      {/* フッター */}
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
    </div>
  );
}
