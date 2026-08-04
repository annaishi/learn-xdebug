<?php

namespace App\Services;

/**
 * 送料込みの合計金額を計算する Service（演習用）。
 *
 * 仕様：
 *   - 小計 = Σ(単価 × 個数)
 *   - 送料：小計が 3,000 円以上なら無料、未満なら一律 500 円
 *   - 消費税：小計の 10%
 *   - 合計 = 小計 + 送料 + 消費税
 */
class ShippingCalculator
{
    /** 送料無料になる小計のしきい値 */
    private const FREE_SHIPPING_THRESHOLD = 3000;

    /** 送料（円） */
    private const SHIPPING_FEE = 500;

    /** 消費税率 */
    private const TAX_RATE = 0.1;

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
     * 送料込みの合計を計算する。
     *
     * @param  array<int, array{name:string, unitPrice:int, quantity:int}> $items
     * @return array{subtotal:int, shipping:int, tax:int, total:int}
     */
    public function calculate(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $lineTotal = $this->lineTotal($item);  // ← F11 で lineTotal() の中へ入れる
            $subtotal += $lineTotal;
        }

        $shipping = $this->shippingFee($subtotal);
        $tax      = (int) ($subtotal * self::TAX_RATE);
        $total    = $subtotal + $shipping + $tax;

        return compact('subtotal', 'shipping', 'tax', 'total');
    }

    /** 1商品の小計 = 単価 × 個数 */
    public function lineTotal(array $item): int
    {
        return $item['unitPrice'] * $item['quantity'];
    }

    /**
     * 送料を計算する。小計が 3,000 円以上なら無料、未満なら 500 円。
     */
    public function shippingFee(int $subtotal): int
    {
        if ($subtotal >= self::FREE_SHIPPING_THRESHOLD) {
            return self::SHIPPING_FEE;
        }

        return 0;
    }
}
