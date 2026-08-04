# ③-2 基本操作を体で覚える（ハンズオン）

IDE が繋がってブレークポイントで止まるようになったら、次はこのページです。
まず **合計金額を計算するだけの練習ページ**（`/playground`）で、ステップ実行など各操作の
意味を体で覚えます。すべて自作コードだけを通るので、操作の違いがくっきり分かります。
最後に、実際のログイン処理（Controller → Service）を追う**応用編**に進みます。

> 前提：[VSCode セットアップ](./vscode-setup.md) を終え、▶ で待ち受け中（下バーがオレンジ）であること。

各レッスンの見方：
- 📍 **打つ場所**（ブレークポイント）
- ▶ **操作**
- ✅ **確認できること**

ショートカットは VSCode 既定（Mac）です。

---

## 操作の意味（まず全体像）

デバッグ中は「**いま止まっている1行**」があり、各操作は「次にどこへ進むか」を選ぶものです。
4つだけ覚えれば十分。次の小さな例で意味をつかみましょう。

```php
function checkout() {
    $price = 100;             // ★ いまここで停止中
    $tax   = calcTax($price); // ← この行には calcTax() の呼び出しがある
    echo $price + $tax;
}

function calcTax($p) {
    return $p * 0.1;          // ← calcTax の中身
}
```

★ の行（`$price = 100;`）で止まっているとして、各操作を押すと：

| 操作 | キー | 何が起きる | ひとことで |
|------|------|------------|-----------|
| **ステップオーバー** | `F10` | 次の行 `$tax = calcTax(...)` を実行するが、**`calcTax()` の中には入らず**結果だけ受け取って `echo` の行へ進む | 「1行ずつ進む。関数の中身は見ない」 |
| **ステップイン** | `F11` | `$tax = calcTax(...)` の行で押すと、**`calcTax()` の中**（`return $p * 0.1;`）へ入る | 「呼んでいる関数の中を覗く」 |
| **ステップアウト** | `Shift+F11` | `calcTax()` の中にいるとき押すと、**残りを実行して呼び出し元へ戻る** | 「この関数はもういい、呼び出し元に戻る」 |
| **続行** | `F5` | 次のブレークポイントまで（無ければ最後まで）**一気に走る** | 「次の●までワープ」 |

### イメージで言うと

- **F10（オーバー）**：本を1行ずつ読む。引用元の本（関数の中）までは開かない。
- **F11（イン）**：気になる引用が出てきたら、その引用元の本を**開いて中を読む**。
- **Shift+F11（アウト）**：引用元を読み終えた（or 興味が失せた）ので、**元の本に戻る**。
- **F5（続行）**：しおり（次のブレークポイント）まで**一気にページを飛ばす**。

> このあと、この4つを練習ページで実際に試します。

---

# 練習ページで体験する（/playground）

題材：http://localhost:8080/playground を開き、**各商品の個数を入力して「計算する」を押す**と
[PlaygroundController@calculate](../src/app/Http/Controllers/PlaygroundController.php#L29) →
[OrderCalculator::calculate()](../src/app/Services/OrderCalculator.php#L33) が動き、
合計金額を計算します。**ログイン不要**です。

> 「入力した値がコードの中をどう流れるか」を追えるのがポイント。
> このあとのレッスンは、**ブレークポイントを置いてから「計算する」を押す**と止まります。
> 初期値（ノート3・ボールペン10・デスクライト2）のまま計算すると、下記レッスンの数値と一致します。

計算の流れ：
```
calculate()
  ├─ 各商品について lineTotal()   … 単価 × 個数
  ├─ calcDiscount()              … 小計が1万円以上なら10%割引
  └─ calcTax()                   … 消費税10%
```

---

## レッスン 0. まず「止まる」を確認する

📍 [OrderCalculator.php:38](../src/app/Services/OrderCalculator.php#L38)（`foreach ($items as $item) {` の行）に ● を置く。

▶ ブラウザで http://localhost:8080/playground を開き、個数はそのまま **「計算する」を押す**。

✅ 38 行目が**黄色くハイライト**されて実行が一時停止する。これが「ブレークした」状態。
   ブラウザは「読み込み中…」のまま待っている（PHP が止まっているため）。

> ここから先の多くのレッスンは、この「止まった状態」から始めます。

---

## レッスン 1. 変数の中身を見る（デバッグの主目的）

📍 レッスン0 の状態（38 行目で停止中）。

✅ 左サイドの **「変数」パネル** → `Locals` を展開すると：
- `$items` … 計算対象の3商品（配列）。展開すると各商品の `name` / `unitPrice` / `quantity` が見える。
  この `quantity` が**さっきフォームに入力した個数**になっているのを確認！
- `$subtotal` … まだ `0`（これから足し込む）

✅ **エディタ上で変数にマウスを乗せる**（ホバー）と、その場で値がポップアップする。

> 「`var_dump()` を書いて確認」していた作業が、コードを汚さずにできる、というのがキモ。

### おまけ：入力値が「届いた瞬間」を見る

📍 [PlaygroundController.php:31](../src/app/Http/Controllers/PlaygroundController.php#L31)（`$catalog = ...` の直前）に ● を置いて「計算する」を押す。

✅ 「変数」パネルで `$request` を展開すると、フォームで入力した `quantities`（個数）が
   そのまま入っている。ここが「画面の入力がコードに渡ってくる入口」。
   このあと 37 行目で各商品の `quantity` に合体され、`calculate()` に渡っていく。

---

## レッスン 2. ステップイン F11 — 自作関数の“中”へ入る

📍 [OrderCalculator.php:39](../src/app/Services/OrderCalculator.php#L39)（`$lineTotal = $this->lineTotal($item);`）に ● を置いて playground を開く。
   → 1件目の商品（ノート）で止まる。

▶ **`F11`（ステップイン）** を押す。

✅ 実行が [OrderCalculator.php:54](../src/app/Services/OrderCalculator.php#L54)（`lineTotal()` の中）へ**飛び込む**。
   「変数」パネルの `$item` は `name = "ノート", unitPrice = 300, quantity = 3`。
   この行で `300 * 3 = 900` が計算される。

> フレームワークに潜らず、**自分の関数の中に素直に入れる**のがポイント。

---

## レッスン 3. ステップオーバー F10 — 中に入らず1行ずつ

📍 レッスン2 と同じく [OrderCalculator.php:39](../src/app/Services/OrderCalculator.php#L39) で止まった状態から（ページを開き直す）。

▶ **`F10`（ステップオーバー）** を押す。

✅ `lineTotal()` の**中には入らず**、計算結果だけ受け取って次の行
   [OrderCalculator.php:40](../src/app/Services/OrderCalculator.php#L40) へ進む。
   `$lineTotal` に `900` が入っているのを確認。

> **F11 と F10 の違いがこれで体感できる**：
> - F11 … `lineTotal()` の中（54 行）へ入る
> - F10 … `lineTotal()` は実行するが中には入らず、次の自分の行へ

---

## レッスン 4. ステップアウト Shift+F11 — 呼び出し元へ戻る

📍 レッスン2 の手順で `lineTotal()` の中（[54 行](../src/app/Services/OrderCalculator.php#L54)）にいる状態。

▶ **`Shift+F11`（ステップアウト）** を押す。

✅ `lineTotal()` を最後まで実行し、呼び出し元
   [OrderCalculator.php:39](../src/app/Services/OrderCalculator.php#L39) の直後（40 行）に戻る。
   「この関数はもう十分、呼び出し元に戻りたい」ときに使う。

---

## レッスン 5. ループで変数が変わる様子を見る（ウォッチ式）

📍 [OrderCalculator.php:40](../src/app/Services/OrderCalculator.php#L40)（`$subtotal += $lineTotal;`）に ● を置いて playground を開く。

▶ 左サイドの **「ウォッチ」パネル**の `+` で式を登録：
- `$subtotal`
- `$item['name']`

▶ **`F5`（続行）** を押すたびに、ループの次の周回で再び 40 行に止まる。

✅ 周回ごとに値が変化するのを追える：

| 周回 | `$item['name']` | `$subtotal`（実行後） |
|------|-----------------|------------------------|
| 1 | ノート | 900 |
| 2 | ボールペン | 2,400 |
| 3 | デスクライト | 12,000 |

> ループ内に ● を置くと**毎周回で止まる**。変数がどう積み上がるかを目で追える。

---

## レッスン 6. 分岐（if）を追う

📍 [OrderCalculator.php:60](../src/app/Services/OrderCalculator.php#L60)（`if ($subtotal >= 10000) {`）に ● を置いて playground を開く。

✅ 止まったら `$subtotal` を確認 → `12000`。

▶ **`F11`（または `F10`）** で1行進める。

✅ `12000 >= 10000` は **true** なので、`if` の中
   [OrderCalculator.php:61](../src/app/Services/OrderCalculator.php#L61)（割引 10% を計算）へ進む。
   もし小計が1万円未満だったら、この中には入らず `return 0;` 側へ進む
   ——という**分岐の意味**を、実行の流れで理解できる。

---

## レッスン 7. コールスタックで「今どこから来たか」を見る

📍 レッスン2 の手順で `lineTotal()` の中（[54 行](../src/app/Services/OrderCalculator.php#L54)）で止まっている状態。

✅ 左サイドの **「コールスタック」パネル**に、呼び出しの積み重なりが見える：

```
OrderCalculator->lineTotal()        ← 今ここ
OrderCalculator->calculate()        ← ここから呼ばれた
PlaygroundController->calculate()   ← さらにその親（フォーム送信の受け口）
```

▶ スタックの **`OrderCalculator->calculate()` をクリック**すると、その階層の変数（`$subtotal` など）を確認できる。
   「どの経路でこの関数に入ったか」を遡れる。

---

## レッスン 8. 条件付きブレークポイント — 特定の時だけ止める

📍 [OrderCalculator.php:39](../src/app/Services/OrderCalculator.php#L39) の ● を**右クリック → 「ブレークポイントの編集」**。
   「式」を選び、次を入力：

```php
$item['name'] === 'デスクライト'
```

▶ playground を開く。

✅ ノート・ボールペンの周回では止まらず、**デスクライトの周回だけ**で止まる。
   ループや大量データの中から「この条件のときだけ調べたい」場面で強力。

---

## レッスン 9. デバッグコンソール — その場で式を評価

📍 `calculate()` の中で止まっている状態（どのレッスンでも可）。

▶ 下部の **「デバッグコンソール」**に式を打ち込んで `Enter`。例：
- `$subtotal`
- `$item`
- `$subtotal * 0.1`

✅ 停止中のコンテキストで式が評価され、結果が表示される。
   「この値ってどうなる？」をその場で試せる。

---

## レッスン 10. 続行 F5 — 最後まで走らせる

▶ ブレークポイントを全部外し（または残したまま）、**`F5`（続行）** を押す。

✅ 次の ● まで（無ければ最後まで）一気に実行され、
   ブラウザに合計 **11,880 円** の表が表示される。

> 「止める → 見る → F5 で続行」が基本のリズム。

---

# 応用編：ログイン処理を追う（Controller → Service）

操作に慣れたら、実際のアプリの流れも追ってみましょう。
題材：http://localhost:8080/login でデモアカウント `demo@example.com` / `password` を送信。

## A. Service の中に入って引数を見る

📍 [AuthService.php:48](../src/app/Services/AuthService.php#L48)（`if (! Auth::attempt(...))`）に ● を置く。

▶ ブラウザでログインする → 48 行で止まる。

✅ 「変数」パネルで、Controller から渡ってきた引数を確認：
- `$credentials` … `['email' => 'demo@example.com', 'password' => 'password']`
- `$remember` … `false`

> **メモ：特定のメソッドの中を見たいときは、そこに ● を置いて `F5` が確実。**
> [LoginController.php:34](../src/app/Http/Controllers/Auth/LoginController.php#L34) の
> `login(...)` の行で `F11` を押すと、引数で呼んでいる `$request->only(...)`（フレームワーク内部）に
> 先に入ってしまいます。これは「F11 はその行で次に実行される関数に入る」ため。
> 狙ったメソッドは ● + F5 で直接止めるのが定石です。

## B. 成功と失敗で分岐が変わるのを見る

📍 同じ [AuthService.php:48](../src/app/Services/AuthService.php#L48) で止まった状態から `F10` で進める。

✅ 分岐を観察：
- **正しいパスワード** → `Auth::attempt()` が `true` を返し、`if` を抜けて
  [AuthService.php:55](../src/app/Services/AuthService.php#L55)（セッション再生成）へ。
- **わざと間違ったパスワード** → `false` になり、`if` の中
  [AuthService.php:49](../src/app/Services/AuthService.php#L49)（例外を投げる）へ進む。
  → ログイン画面にエラーが表示される流れを追える。

---

## まとめ：操作チートシート

| 操作 | キー | ひとことで |
|------|------|-----------|
| 続行 | `F5` | 次のブレークまで走る |
| ステップオーバー | `F10` | 1行実行（中に入らない） |
| ステップイン | `F11` | 関数の中へ入る |
| ステップアウト | `Shift+F11` | 今の関数を抜ける |
| 停止 | `Shift+F5` | デバッグ終了 |
| 変数を見る | — | 変数パネル / ホバー |
| 式を評価 | — | デバッグコンソール |
| 値を監視 | — | ウォッチパネル |
| 条件付き停止 | — | ●を右クリック→編集 |

ここまでできれば、Xdebug の基本操作は一通り身についています。次は実戦（演習問題）へ。
