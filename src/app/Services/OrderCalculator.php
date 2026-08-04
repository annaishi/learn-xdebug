<?php

namespace App\Services;

/**
 * カートの合計金額を計算する Service（Xdebug 操作の練習用）。
 *
 * すべて自作メソッドの数珠つなぎなので、
 *   - F11（ステップイン）で lineTotal() / calcDiscount() / calcTax() の中へ入る
 *   - F10（ステップオーバー）で中に入らず結果だけ受け取る
 *   - Shift+F11（ステップアウト）で呼び出し元へ戻る
 *   - ループ内で変数が変わる様子 / 条件付きブレークポイント
 * を、フレームワーク内部に潜らずに体験できます。
 */
class OrderCalculator
{
    /** 商品カタログ（個数はフォームから入力される） */
    public function catalog(): array
    {
        return [
            ['name' => 'ノート',       'unitPrice' => 300],
            ['name' => 'ボールペン',   'unitPrice' => 150],
            ['name' => 'デスクライト', 'unitPrice' => 4800],
        ];
    }

    /**
     * 合計金額を計算する（このメソッドが入口）。
     *
     * @param  array<int, array{name:string, unitPrice:int, quantity:int}> $items
     * @return array{subtotal:int, discount:int, taxable:int, tax:int, total:int}
     */
    public function calculate(array $items): array
    {
        $subtotal = 0;

        // ★ ループ：ここに ● を置くと、商品ごとに繰り返し止まる（変数が変わる様子を観察）
        foreach ($items as $item) {
            $lineTotal = $this->lineTotal($item);  // ← F11 で lineTotal() の中へ入れる
            $subtotal += $lineTotal;
        }

        $discount = $this->calcDiscount($subtotal); // ← F11 で中へ / F10 でまたぐ
        $taxable  = $subtotal - $discount;
        $tax      = $this->calcTax($taxable);        // ← F11 で中へ / F10 でまたぐ
        $total    = $taxable + $tax;

        return compact('subtotal', 'discount', 'taxable', 'tax', 'total');
    }

    /** 1商品の小計 = 単価 × 個数 */
    public function lineTotal(array $item): int
    {
        return $item['unitPrice'] * $item['quantity'];
    }

    /** 小計が 10,000 円以上なら 10% 割引（分岐の観察に最適） */
    public function calcDiscount(int $subtotal): int
    {
        if ($subtotal >= 10000) {
            return (int) ($subtotal * 0.1);
        }

        return 0;
    }

    /** 消費税 10% */
    public function calcTax(int $amount): int
    {
        return (int) ($amount * 0.1);
    }
}
