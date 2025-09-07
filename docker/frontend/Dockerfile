# フロントエンド用Dockerfile
# Next.js 15.2.4 + TypeScript + Tailwind CSS

# ベースイメージ
FROM node:20-alpine AS base

# 依存関係のインストール
FROM base AS deps
# パッケージマネージャーのキャッシュを無効化
RUN apk add --no-cache libc6-compat
WORKDIR /app

# パッケージファイルをコピー
COPY package.json package-lock.json* ./
RUN npm ci

# ビルドステージ
FROM base AS builder
WORKDIR /app
COPY --from=deps /app/node_modules ./node_modules
COPY . .

# 環境変数の設定
ENV NEXT_TELEMETRY_DISABLED 1
ENV NODE_ENV production
ENV NEXT_PUBLIC_API_URL http://localhost:8000/api

# ビルドの実行
RUN npm run build

# 本番環境
FROM base AS runner
WORKDIR /app

ENV NODE_ENV production
ENV NEXT_TELEMETRY_DISABLED 1

# 非rootユーザーの作成
RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

# 必要なファイルをコピー
COPY --from=builder /app/public ./public

# standaloneモードでビルドされた場合の処理
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

USER nextjs

EXPOSE 3000

ENV PORT 3000
ENV HOSTNAME "0.0.0.0"

# アプリケーションの起動
CMD ["node", "server.js"]
