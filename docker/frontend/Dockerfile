# 本番環境用フロントエンドDockerfile
FROM node:20-alpine AS builder

WORKDIR /app

# パッケージファイルをコピー
COPY package.json package-lock.json* ./

# 依存関係をインストール（ビルドにはdevDependenciesも必要）
RUN npm ci

# アプリケーションのソースコードをコピー
COPY . .

# ビルド時にパブリック環境変数を注入できるように
ARG NEXT_PUBLIC_API_URL
ENV NEXT_PUBLIC_API_URL=${NEXT_PUBLIC_API_URL}
ENV NODE_ENV=production

# 本番用ビルドを実行
RUN npm run build

# 本番環境用の軽量なイメージ
FROM node:20-alpine AS runner

WORKDIR /app

# ビルド済みアプリケーションをコピー
COPY --from=builder /app/.next/standalone ./
COPY --from=builder /app/.next/static ./.next/static
COPY --from=builder /app/public ./public

# 非rootユーザーを作成
RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

# 権限を設定
USER nextjs

# ポートを公開
EXPOSE 3000

# 環境変数を設定
ENV NODE_ENV=production
ENV PORT=3000
ENV HOSTNAME="0.0.0.0"
ENV NEXT_PUBLIC_API_URL=${NEXT_PUBLIC_API_URL}

# アプリケーションを起動
CMD ["node", "server.js"]