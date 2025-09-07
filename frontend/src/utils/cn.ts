import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

/**
 * クラス名を結合し、Tailwind CSSの競合を解決する
 * @param inputs - 結合するクラス名
 * @returns 結合されたクラス名
 */
export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/**
 * 条件付きでクラス名を適用する
 * @param condition - 条件
 * @param trueClass - 条件が真の時のクラス名
 * @param falseClass - 条件が偽の時のクラス名
 * @returns 適用されるクラス名
 */
export function conditionalClass(
  condition: boolean,
  trueClass: string,
  falseClass: string = ""
): string {
  return condition ? trueClass : falseClass;
}

/**
 * 複数の条件に基づいてクラス名を適用する
 * @param conditions - 条件とクラス名のオブジェクト
 * @returns 適用されるクラス名
 */
export function multiConditionalClass(
  conditions: Record<string, string>
): string {
  return Object.entries(conditions)
    .filter(([condition]) => Boolean(condition))
    .map(([, className]) => className)
    .join(" ");
}
