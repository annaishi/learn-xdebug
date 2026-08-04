<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xdebug 練習ページ</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f3f4f6; color: #111827;
            max-width: 640px; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.3rem; }
        h2 { font-size: 1.05rem; margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.06); margin: 1rem 0; }
        th, td { padding: .6rem .8rem; text-align: right; border-bottom: 1px solid #eee; }
        th:first-child, td:first-child { text-align: left; }
        thead th { background: #2563eb; color: #fff; }
        input[type=number] { width: 5rem; padding: .35rem; border: 1px solid #d1d5db; border-radius: 6px;
            text-align: right; }
        button { margin-top: .5rem; padding: .6rem 1.4rem; border: 0; border-radius: 8px;
            background: #2563eb; color: #fff; font-size: .95rem; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .summary td { border: 0; }
        .summary tr:last-child td { font-weight: bold; font-size: 1.1rem; border-top: 2px solid #111827; }
        .note { font-size: .85rem; color: #6b7280; }
        code { background: #eef2ff; padding: .1rem .3rem; border-radius: 4px; }
        .rules { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;
            padding: .6rem 1rem .6rem 2rem; font-size: .9rem; color: #92400e; }
        .rules li { margin: .2rem 0; }
    </style>
</head>
<body>
    <h1>🛒 合計金額の計算（Xdebug 練習ページ）</h1>
    <p class="note">
        個数を入力して「計算する」を押すと <code>PlaygroundController@calculate</code> →
        <code>OrderCalculator::calculate()</code> が動きます。
        入力した値がコードの中をどう流れるか、ブレークポイントで追ってみましょう。
    </p>

    <ul class="rules">
        <li>小計が <strong>10,000 円以上</strong> で <strong>10% 割引</strong></li>
        <li>割引後の金額に <strong>消費税 10%</strong> を加算</li>
    </ul>

    <form method="POST" action="{{ route('playground.calculate') }}">
        @csrf
        <table>
            <thead>
                <tr><th>商品</th><th>単価</th><th>個数</th></tr>
            </thead>
            <tbody>
                @foreach ($catalog as $i => $product)
                    <tr>
                        <td>{{ $product['name'] }}</td>
                        <td>{{ number_format($product['unitPrice']) }} 円</td>
                        <td>
                            <input type="number" min="0"
                                   name="quantities[{{ $i }}]"
                                   value="{{ old("quantities.$i", $defaults[$i] ?? 0) }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit">計算する</button>
    </form>

    @if ($result)
        <h2>計算結果</h2>
        <table class="summary">
            <tr><td>小計</td><td>{{ number_format($result['subtotal']) }} 円</td></tr>
            <tr><td>割引</td><td>- {{ number_format($result['discount']) }} 円</td></tr>
            <tr><td>税抜</td><td>{{ number_format($result['taxable']) }} 円</td></tr>
            <tr><td>消費税(10%)</td><td>{{ number_format($result['tax']) }} 円</td></tr>
            <tr><td>合計</td><td>{{ number_format($result['total']) }} 円</td></tr>
        </table>
        <p class="note">ブレークポイントを置いてから「計算する」を押すと、この計算の途中で止まります。</p>
    @endif
</body>
</html>
