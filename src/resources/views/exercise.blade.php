<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Xdebug 演習ページ</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f3f4f6; color: #111827;
            max-width: 640px; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.3rem; }
        h2 { font-size: 1.05rem; margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.06); margin: 1rem 0; }
        th, td { padding: .6rem .8rem; text-align: right; border-bottom: 1px solid #eee; }
        th:first-child, td:first-child { text-align: left; }
        thead th { background: #7c3aed; color: #fff; }
        input[type=number] { width: 5rem; padding: .35rem; border: 1px solid #d1d5db; border-radius: 6px;
            text-align: right; }
        button { margin-top: .5rem; padding: .6rem 1.4rem; border: 0; border-radius: 8px;
            background: #7c3aed; color: #fff; font-size: .95rem; cursor: pointer; }
        button:hover { background: #6d28d9; }
        .summary td { border: 0; }
        .summary tr:last-child td { font-weight: bold; font-size: 1.1rem; border-top: 2px solid #111827; }
        .note { font-size: .85rem; color: #6b7280; }
        code { background: #f3e8ff; padding: .1rem .3rem; border-radius: 4px; }
        .rules { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;
            padding: .6rem 1rem .6rem 2rem; font-size: .9rem; color: #92400e; }
        .rules li { margin: .2rem 0; }
        .task { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;
            padding: .8rem 1rem; font-size: .9rem; color: #1e40af; }
    </style>
</head>
<body>
    <h1>🐞 演習：送料計算のバグを直せ（Xdebug）</h1>

    <div class="task">
        このページには <strong>バグ</strong> が仕込まれています。<br>
        下記の仕様どおりに動いていません。Xdebug で原因の行を突き止めて直しましょう。
    </div>

    <ul class="rules">
        <li>小計 = 単価 × 個数 の合計</li>
        <li>送料：小計が <strong>3,000 円以上で無料</strong>、3,000 円未満なら <strong>500 円</strong></li>
        <li>消費税：小計の <strong>10%</strong></li>
        <li>合計 = 小計 + 送料 + 消費税</li>
        <li>👉 初期値（3 / 10 / 2）の場合の正解 → <strong>合計 13,200 円</strong></li>
    </ul>

    <form method="POST" action="{{ route('exercise.calculate') }}">
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
            <tr><td>送料</td><td>{{ number_format($result['shipping']) }} 円</td></tr>
            <tr><td>消費税(10%)</td><td>{{ number_format($result['tax']) }} 円</td></tr>
            <tr><td>合計</td><td>{{ number_format($result['total']) }} 円</td></tr>
        </table>
        <p class="note">
            仕様と見比べてみてください。この送料、合っていますか？
            手順は <code>docs/04-exercise.md</code> を参照。
        </p>
    @endif
</body>
</html>
