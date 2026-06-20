# VSCode で Xdebug をセットアップする（手順 + つまずきポイント）

このページは **VSCode 専用**の手順書です。
PhpStorm の人は [xdebug-setup.md](./xdebug-setup.md) を参照してください。

> 前提：[② 動かし方](./02-run.md) まで終えて、http://localhost:8080 でアプリが開ける状態。

---

## 全体像（最初に頭に入れる）

止めるには **2つ別々の操作**が必要です。ここが最大のつまずきポイントです。

| 操作 | 役割 | これだけでは止まらない |
|------|------|------------------------|
| ブレークポイントを置く | 「この行で止めて」という**印** | ❌ 印を置くだけでは止まらない |
| ▶ で待ち受け開始 | VSCode が Xdebug の接続を**待ち受ける** | ❌ 待ち受けるだけでも止まらない |

**両方そろって初めて止まります。** 「ブレークポイントを置いたのに止まらない」の大半は
▶（待ち受け開始）のし忘れです。

---

## ステップ 1. 拡張機能「PHP Debug」をインストール

1. 拡張機能パネルを開く（`Cmd+Shift+X`）
2. **「PHP Debug」** で検索（発行元: Xdebug、ID: `xdebug.php-debug`）
3. インストール

---

## ステップ 2. launch.json を作る

手で作るより、**メニューから自動生成**するのが簡単です。

1. 左サイドバーの **「実行とデバッグ」**（虫つき▷アイコン / `Cmd+Shift+D`）を開く
2. **「launch.json ファイルを作成します」** をクリック
3. 環境の選択で **「PHP」** を選ぶ
4. → `.vscode/launch.json` が自動生成される

自動生成された中の **「Listen for Xdebug」** 設定に、**パスマッピングを1ブロック追記**します。
最終的に、この設定が含まれていれば OK：

```json
{
    "name": "Listen for Xdebug",
    "type": "php",
    "request": "launch",
    "port": 9003,
    "pathMappings": {
        "/var/www/html": "${workspaceFolder}/src"
    }
}
```

`pathMappings` を足したら **`Cmd+S` で保存**。

### パスマッピングの意味（重要）

```
コンテナの中:  /var/www/html/app/Http/Controllers/Auth/LoginController.php
あなたのPC:    ${workspaceFolder}/src/app/Http/Controllers/Auth/LoginController.php
               (= このリポジトリの) src/ が /var/www/html に対応
```

この対応を教えないと、VSCode は「コンテナ側のどの行が手元のどのファイルか」が分からず、
**接続できてもブレークポイントが効きません**。`port` の `9003` はコンテナ側
（[docker/php/conf.d/xdebug.ini](../docker/php/conf.d/xdebug.ini)）と一致させます。

---

## ステップ 3. ★ 待ち受けを開始する（▶）

ここが一番忘れやすい操作です。

1. 「実行とデバッグ」パネル上部のドロップダウンで **「Listen for Xdebug」** を選ぶ
2. 左の **緑の ▶（再生ボタン）を押す**（または `F5`）
3. 画面下のバーが **オレンジ色**になり、上部に **デバッグツールバー**（⏸ ⤼ ⤽ ↻ ⏹）が出れば待ち受け開始 🎧

> 止めたいときはツールバーの ⏹（赤い四角）か `Shift+F5`。

---

## ステップ 4. ブレークポイントを置く

止めたい行の **行番号の左**をクリック → 赤い ● が付きます。

おすすめの最初の一歩（ログインの流れを追う）:

- [src/app/Http/Controllers/Auth/LoginController.php](../src/app/Http/Controllers/Auth/LoginController.php) の `store()` 内
- [src/app/Services/AuthService.php](../src/app/Services/AuthService.php) の `login()` 内

---

## ステップ 5. ブラウザでアクセスして止める

1. http://localhost:8080/login を開く
2. デモアカウントでログイン： `demo@example.com` / `password`
3. ブレークポイントの行で **実行が止まる** 🎉（その行が黄色くハイライトされる）

止まったら左パネルで：
- **変数**：`$request` や `$credentials` の中身を展開して確認
- **コールスタック**：今どの呼び出し経路で来たか（Controller → Service …）
- **ウォッチ式**：任意の式を登録して値を監視

---

## デバッグ中の操作（ツールバー / ショートカット）

| 操作 | ショートカット | 意味 |
|------|----------------|------|
| 続行 | `F5` | 次のブレークポイントまで走る |
| ステップオーバー | `F10` | 現在行を実行（関数の中には入らない） |
| ステップイン | `F11` | 関数の**中へ**入る（Controller→Service を追える） |
| ステップアウト | `Shift+F11` | 今の関数を抜けるまで実行 |
| 停止 | `Shift+F5` | デバッグセッション終了 |

`store()` で止めたら `F11` で `AuthService::login()` の中へ入ってみましょう。

---

## うまく止まらないときの切り分け

順番にチェックすると原因が一発で分かります。

### ① VSCode が待ち受けているか（ホスト側）
```bash
lsof -nP -iTCP:9003 -sTCP:LISTEN
```
- 何も出ない → **▶ を押し忘れ**（ステップ3）。これが一番多い。
- VSCode/Code Helper が出る → 待ち受けOK。②へ。

### ② Xdebug が IDE に繋がっているか（コンテナ側ログ）
```bash
make xdebug-log
```
この状態でブラウザを再読み込みしてログを見る:

| ログの内容 | 意味 | 対処 |
|------------|------|------|
| `ERR: Could not connect to debugging client` | IDE が待ち受けていない | ▶ を押す（ステップ3） |
| `Connected to debugging client` | 接続OK | 繋がっている。③へ |
| （何も出ない） | リクエストがPHPに届いていない | URL・`make up` を確認 |

### ③ 繋がるのに止まらない → パスマッピング
`Connected` は出るのに止まらない場合、ほぼ **pathMappings のズレ**です。
- `"/var/www/html": "${workspaceFolder}/src"` になっているか
- VSCode で開いているフォルダがこのリポジトリのルートか
  （`${workspaceFolder}` がルート → `/src` が付いて `…/learn-xdebug/src` になる）

### ④ そもそも Xdebug が有効か
```bash
make xdebug-status
```
`xdebug.mode => debug,develop` / `start_with_request => yes` / `client_port => 9003` を確認。

---

## 補足：今回の構成のポイント

- コンテナの Xdebug は `start_with_request=yes`。**毎リクエストで自動的に IDE を呼びに行く**ので、
  VSCode 側で待ち受けてさえいれば、特別な操作なしで止まります。
- `.vscode/` はルートの [.gitignore](../.gitignore) で除外しています（各自のローカル設定扱い）。
- 接続の向きは「**PHP → IDE**」。だから IDE を**先に**待ち受けにする必要があります。
  （詳しくは [① 環境設定の解説](./01-environment.md)）
