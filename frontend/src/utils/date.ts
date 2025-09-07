/**
 * 日付をフォーマットする
 * @param date - 日付文字列またはDateオブジェクト
 * @param format - フォーマット（'short', 'long', 'relative'）
 * @returns フォーマットされた日付文字列
 */
export function formatDate(
  date: string | Date,
  format: 'short' | 'long' | 'relative' = 'short'
): string {
  const dateObj = typeof date === 'string' ? new Date(date) : date;
  
  if (format === 'relative') {
    return getRelativeTime(dateObj);
  }
  
  const options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: format === 'long' ? 'long' : 'short',
    day: 'numeric',
  };
  
  if (format === 'long') {
    options.weekday = 'long';
  }
  
  return new Intl.DateTimeFormat('ja-JP', options).format(dateObj);
}

/**
 * 相対時間を取得する
 * @param date - 日付
 * @returns 相対時間文字列
 */
function getRelativeTime(date: Date): string {
  const now = new Date();
  const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);
  
  if (diffInSeconds < 60) {
    return '今';
  }
  
  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) {
    return `${diffInMinutes}分前`;
  }
  
  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) {
    return `${diffInHours}時間前`;
  }
  
  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 7) {
    return `${diffInDays}日前`;
  }
  
  const diffInWeeks = Math.floor(diffInDays / 7);
  if (diffInWeeks < 4) {
    return `${diffInWeeks}週間前`;
  }
  
  const diffInMonths = Math.floor(diffInDays / 30);
  if (diffInMonths < 12) {
    return `${diffInMonths}ヶ月前`;
  }
  
  const diffInYears = Math.floor(diffInDays / 365);
  return `${diffInYears}年前`;
}

/**
 * 禁煙開始日からの経過日数を計算する
 * @param quitDate - 禁煙開始日
 * @returns 経過日数
 */
export function calculateQuitDays(quitDate: string | Date): number {
  const quit = typeof quitDate === 'string' ? new Date(quitDate) : quitDate;
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - quit.getTime());
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

/**
 * 禁煙による節約金額を計算する
 * @param dailyCigarettes - 1日の喫煙本数
 * @param packCost - 1箱の価格
 * @param quitDate - 禁煙開始日
 * @returns 節約金額
 */
export function calculateSavedMoney(
  dailyCigarettes: number,
  packCost: number,
  quitDate: string | Date
): number {
  const quitDays = calculateQuitDays(quitDate);
  const cigarettesPerPack = 20; // 1箱20本と仮定
  const dailyCost = (dailyCigarettes / cigarettesPerPack) * packCost;
  return Math.round(dailyCost * quitDays);
}

/**
 * 禁煙による健康改善を計算する
 * @param quitDate - 禁煙開始日
 * @returns 健康改善情報
 */
export function calculateHealthImprovements(quitDate: string | Date): {
  milestone: string;
  description: string;
  achieved: boolean;
}[] {
  const quitDays = calculateQuitDays(quitDate);
  
  return [
    {
      milestone: '20分',
      description: '血圧と脈拍が正常値に戻る',
      achieved: quitDays >= 1,
    },
    {
      milestone: '8時間',
      description: '血液中の一酸化炭素レベルが正常値に戻る',
      achieved: quitDays >= 1,
    },
    {
      milestone: '24時間',
      description: '心臓発作のリスクが低下し始める',
      achieved: quitDays >= 1,
    },
    {
      milestone: '48時間',
      description: '神経終末が再生し始め、味覚と嗅覚が改善',
      achieved: quitDays >= 2,
    },
    {
      milestone: '72時間',
      description: '気管支が弛緩し、肺活量が増加',
      achieved: quitDays >= 3,
    },
    {
      milestone: '2週間',
      description: '循環機能が改善し、運動能力が向上',
      achieved: quitDays >= 14,
    },
    {
      milestone: '1ヶ月',
      description: '肌の色艶が改善し、しわが減少',
      achieved: quitDays >= 30,
    },
    {
      milestone: '3ヶ月',
      description: '肺機能が大幅に改善',
      achieved: quitDays >= 90,
    },
    {
      milestone: '1年',
      description: '冠動脈疾患のリスクが半減',
      achieved: quitDays >= 365,
    },
  ];
}

/**
 * 日付が有効かチェックする
 * @param date - 日付文字列
 * @returns 有効な日付かどうか
 */
export function isValidDate(date: string): boolean {
  const dateObj = new Date(date);
  return dateObj instanceof Date && !isNaN(dateObj.getTime());
}

/**
 * 日付範囲を取得する
 * @param startDate - 開始日
 * @param endDate - 終了日
 * @returns 日付範囲の配列
 */
export function getDateRange(startDate: Date, endDate: Date): Date[] {
  const dates: Date[] = [];
  const current = new Date(startDate);
  
  while (current <= endDate) {
    dates.push(new Date(current));
    current.setDate(current.getDate() + 1);
  }
  
  return dates;
}
