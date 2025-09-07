/** @type {import('next').NextConfig} */
const nextConfig = {
  // 本番環境でのstandaloneモードを有効化（Docker用）
  output: 'standalone',
  
  // 画像最適化の設定
  images: {
    domains: ['localhost'],
    unoptimized: true, // Docker環境での画像最適化を無効化
  },
  
  // 環境変数の設定
  env: {
    NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL,
  },
  
  // 開発環境での設定
  ...(process.env.NODE_ENV === 'development' && {
    // 開発環境でのみ有効な設定
  }),
  
  // 本番環境での設定
  ...(process.env.NODE_ENV === 'production' && {
    // 本番環境でのみ有効な設定
    compress: true,
    poweredByHeader: false,
  }),
};

module.exports = nextConfig;
