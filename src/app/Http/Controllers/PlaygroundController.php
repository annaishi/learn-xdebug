<?php

namespace App\Http\Controllers;

use App\Services\OrderCalculator;
use Illuminate\View\View;

/**
 * Xdebug の操作練習用ページ（ログイン不要）。
 * カートの合計金額を計算して表示するだけのシンプルな機能。
 */
class PlaygroundController extends Controller
{
    public function __construct(private readonly OrderCalculator $calculator) {}

    public function index(): View
    {
        // ここに ● を置いて F11 → calculate() の中へ入れる（自作コードだけを追える）
        $items  = $this->calculator->sampleCart();
        $result = $this->calculator->calculate($items);

        return view('playground', compact('items', 'result'));
    }
}
