import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { AuthProvider } from "@/contexts/AuthContext";
import { Toaster } from "react-hot-toast";

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: "QuitSmoking - 禁煙の旅を一緒に歩もう",
  description: "科学的根拠に基づいた禁煙サポートと、同じ目標を持つ仲間たちとの交流で、あなたの禁煙成功をサポートします。",
  keywords: ["禁煙", "健康", "コミュニティ", "サポート", "禁煙アプリ"],
  authors: [{ name: "QuitSmoking Team" }],
  creator: "QuitSmoking",
  publisher: "QuitSmoking",
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  metadataBase: new URL(process.env.NEXT_PUBLIC_APP_URL || 'http://localhost:3000'),
  openGraph: {
    title: "QuitSmoking - 禁煙の旅を一緒に歩もう",
    description: "科学的根拠に基づいた禁煙サポートと、同じ目標を持つ仲間たちとの交流で、あなたの禁煙成功をサポートします。",
    url: "/",
    siteName: "QuitSmoking",
    images: [
      {
        url: "/og-image.jpg",
        width: 1200,
        height: 630,
        alt: "QuitSmoking - 禁煙サポートアプリ",
      },
    ],
    locale: "ja_JP",
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: "QuitSmoking - 禁煙の旅を一緒に歩もう",
    description: "科学的根拠に基づいた禁煙サポートと、同じ目標を持つ仲間たちとの交流で、あなたの禁煙成功をサポートします。",
    images: ["/og-image.jpg"],
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-video-preview": -1,
      "max-image-preview": "large",
      "max-snippet": -1,
    },
  },
  verification: {
    google: process.env.GOOGLE_SITE_VERIFICATION,
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ja" className="h-full">
      <body className={`${inter.className} h-full antialiased`}>
        <AuthProvider>
          {children}
          <Toaster
            position="top-right"
            toastOptions={{
              duration: 4000,
              style: {
                background: '#363636',
                color: '#fff',
              },
              success: {
                duration: 3000,
                iconTheme: {
                  primary: '#10B981',
                  secondary: '#fff',
                },
              },
              error: {
                duration: 5000,
                iconTheme: {
                  primary: '#EF4444',
                  secondary: '#fff',
                },
              },
            }}
          />
        </AuthProvider>
      </body>
    </html>
  );
}
