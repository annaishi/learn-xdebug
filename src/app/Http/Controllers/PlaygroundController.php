<?php

namespace App\Http\Controllers;

use App\Services\OrderCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Xdebug の操作練習用ページ（ログイン不要）。
 * 個数を入力 → 送信 → 合計金額を計算して表示する。
 */
class PlaygroundController extends Controller
{
    public function __construct(private readonly OrderCalculator $calculator) {}

    /** 入力フォームを表示（GET） */
    public function index(): View
    {
        return view('playground', [
            'catalog'  => $this->calculator->catalog(),
            'defaults' => [3, 10, 2], // フォームの初期値
            'items'    => null,
            'result'   => null,
        ]);
    }

    /** フォーム送信 → 合計を計算して表示（POST） */
    public function calculate(Request $request): View
    {
        // ★ ここに ● を置くと、入力された個数（$request）を確認できる
        $catalog = $this->calculator->catalog();

        // 入力された個数を各商品に合体させて、計算用の $items を組み立てる
        $items = [];
        foreach ($catalog as $index => $product) {
            $quantity = (int) $request->input("quantities.$index", 0);
            $items[] = [...$product, 'quantity' => $quantity];
        }

        // F11 で OrderCalculator::calculate() の中へ入れる
        $result = $this->calculator->calculate($items);

        return view('playground', [
            'catalog'  => $catalog,
            'defaults' => array_column($items, 'quantity'), // 入力値を保持
            'items'    => $items,
            'result'   => $result,
        ]);
    }
}
