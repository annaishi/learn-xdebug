<?php

namespace App\Http\Controllers;

use App\Services\ShippingCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Xdebug 演習用ページ（ログイン不要）。
 * 送料込みの合計を計算する。※このどこかにバグがある。
 */
class ExerciseController extends Controller
{
    public function __construct(private readonly ShippingCalculator $calculator) {}

    /** 入力フォームを表示（GET） */
    public function index(): View
    {
        return view('exercise', [
            'catalog'  => $this->calculator->catalog(),
            'defaults' => [3, 10, 2],
            'items'    => null,
            'result'   => null,
        ]);
    }

    /** フォーム送信 → 合計を計算して表示（POST） */
    public function calculate(Request $request): View
    {
        $catalog = $this->calculator->catalog();

        $items = [];
        foreach ($catalog as $index => $product) {
            $quantity = (int) $request->input("quantities.$index", 0);
            $items[] = [...$product, 'quantity' => $quantity];
        }

        $result = $this->calculator->calculate($items);

        return view('exercise', [
            'catalog'  => $catalog,
            'defaults' => array_column($items, 'quantity'),
            'items'    => $items,
            'result'   => $result,
        ]);
    }
}
