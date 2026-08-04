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
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.06); margin: 1rem 0; }
        th, td { padding: .6rem .8rem; text-align: right; border-bottom: 1px solid #eee; }
        th:first-child, td:first-child { text-align: left; }
        thead th { background: #2563eb; color: #fff; }
        .summary td { border: 0; }
        .summary tr:last-child td { font-weight: bold; font-size: 1.1rem; border-top: 2px solid #111827; }
        .note { font-size: .85rem; color: #6b7280; }
        code { background: #eef2ff; padding: .1rem .3rem; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🛒 合計金額（Xdebug 練習ページ）</h1>
    <p class="note">
        このページを開くと <code>PlaygroundController@index</code> →
        <code>OrderCalculator::calculate()</code> が動きます。
        ブレークポイントを置いて、ステップ実行を試してみましょう。
    </p>

    <table>
        <thead>
            <tr><th>商品</th><th>単価</th><th>個数</th><th>小計</th></tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ number_format($item['unitPrice']) }} 円</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unitPrice'] * $item['quantity']) }} 円</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr><td>小計</td><td>{{ number_format($result['subtotal']) }} 円</td></tr>
        <tr><td>割引</td><td>- {{ number_format($result['discount']) }} 円</td></tr>
        <tr><td>税抜</td><td>{{ number_format($result['taxable']) }} 円</td></tr>
        <tr><td>消費税(10%)</td><td>{{ number_format($result['tax']) }} 円</td></tr>
        <tr><td>合計</td><td>{{ number_format($result['total']) }} 円</td></tr>
    </table>

    <p class="note">再読み込みするたびに同じ計算が走ります。ブレークポイントで止めて F10 / F11 / Shift+F11 を試してください。</p>
</body>
</html>
