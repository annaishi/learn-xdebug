# Xdebug セットアップ手順（VSCode / PhpStorm 両対応）

このプロジェクトの Xdebug は、コンテナ側で既に有効化済みです。
あとは **お使いの IDE を「リスナー」として待ち受け状態にする** だけでブレークポイントが効きます。

## 仕組みの全体像

```
ブラウザ ──HTTPリクエスト──▶ Apache + PHP (Docker コンテナ)
                                   │ Xdebug が
                                   │ 「ホストの IDE に繋ぎに行く」
                                   ▼
                       IDE (VSCode / PhpStorm)  ◀── ポート 9003 で待ち受け
```

ポイントは「**PHP 側が IDE に接続しに来る**」という向きです。
だから IDE を先に待ち受け状態にしておく必要があります。

コンテナ側の設定（[docker/php/conf.d/xdebug.ini](../docker/php/conf.d/xdebug.ini)）の要点：

| 設定 | 値 | 意味 |
|------|-----|------|
| `xdebug.mode` | `debug,develop` | ステップ実行を有効化 |
| `xdebug.start_with_request` | `yes` | 毎リクエストで自動的にデバッグ接続を試みる |
| `xdebug.client_host` | `host.docker.internal` | コンテナからホスト（=IDE）を指す |
| `xdebug.client_port` | `9003` | Xdebug 3 の標準ポート |

> パスマッピングが最重要：コンテナ内 `/var/www/html` ＝ ローカル `src/`

---

## VSCode の場合

1. 拡張機能 **「PHP Debug」(`xdebug.php-debug`)** をインストール
2. プロジェクト直下に `.vscode/launch.json` を作成し、以下を貼り付け：

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug (Docker)",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}/src"
            }
        }
    ]
}
```

3. 「実行とデバッグ」パネル（虫アイコン）で **「Listen for Xdebug (Docker)」** を選んで ▶ を押す
4. デバッグしたい行の左をクリックして赤い●（ブレークポイント）を置く
5. ブラウザで http://localhost:8080/login にアクセス → 該当行で止まる 🎉

---

## PhpStorm の場合

1. **Settings → PHP → Debug** で `Debug port` が `9003` になっているか確認
2. メニュー **Run → Start Listening for PHP Debug Connections**（受話器アイコン）をクリック
3. デバッグしたい行の左をクリックしてブレークポイントを置く
4. ブラウザで http://localhost:8080/login にアクセス
5. 初回はパスマッピングを聞かれるので、サーバーを以下のように設定：
   - Host: `localhost` / Port: `8080`
   - **Absolute path on the server**: `/var/www/html` ＝ プロジェクトの `src` ディレクトリ
6. 設定後にもう一度アクセスすると、該当行で止まる 🎉

---

## まず試すおすすめブレークポイント

ログインの流れ（Controller → Service → Model）を追うと MVCS が体感できます。

1. [src/app/Http/Controllers/Auth/LoginController.php](../src/app/Http/Controllers/Auth/LoginController.php) の `store()`
2. そこから **ステップイン（F11 / PhpStorm は F7）** で
   [src/app/Services/AuthService.php](../src/app/Services/AuthService.php) の `login()` へ
3. `Auth::attempt()` の戻り値や `$credentials` の中身を覗いてみる

デモアカウント： `demo@example.com` / `password`

---

## うまく繋がらないときは

```bash
# Xdebug が有効か / 設定値の確認
make xdebug-status

# Xdebug の接続ログをリアルタイム表示（接続失敗の原因が分かる）
make xdebug-log
```

- IDE 側を「待ち受け」にし忘れていないか
- ポート 9003 が他プロセスに使われていないか
- （Linux ホストの場合）`host.docker.internal` が解決できているか
  → docker-compose.yml の `extra_hosts` で対応済み
