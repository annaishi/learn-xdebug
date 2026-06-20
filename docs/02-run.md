# ② 動かし方 — 環境を起動して動作確認する

[① 環境設定の解説](./01-environment.md) で中身を理解したら、実際に動かします。
このページの最後まで進めば、ブラウザでアプリが見える状態になります。

---

## 事前準備（最初の1回だけ）

| 必要なもの | 確認コマンド |
|------------|--------------|
| Docker Desktop が起動している | `docker info` がエラーなく返る |
| `make` が使える | `make --version` |

> Docker Desktop は **アプリを起動**しておくのを忘れずに（デーモンが動いていないと何も始まりません）。

---

## ステップ 1. 一括セットアップ

プロジェクト直下で：

```bash
make init
```

これ1つで以下が順番に実行されます（[Makefile](../Makefile) 参照）:

1. `build` … Docker イメージをビルド（Xdebug のコンパイルで初回数分）
2. `up` … コンテナ起動（app / db）
3. `composer-install` … PHP ライブラリ導入
4. `env` … `.env` 準備 & アプリキー生成
5. `migrate` … DB テーブル作成
6. `seed` … デモユーザー投入

完了すると次が表示されます：

```
✅ セットアップ完了！ http://localhost:8080 にアクセスしてください
   デモアカウント: demo@example.com / password
```

---

## ステップ 2. ブラウザで開く

http://localhost:8080 へアクセス → ログイン画面が出ます。

デモアカウントでログイン：

| メールアドレス | パスワード |
|----------------|-----------|
| `demo@example.com` | `password` |

ログインに成功すると「ようこそ、デモ ユーザー さん」のダッシュボードが表示されます。
（新規登録から自分でアカウントを作ることもできます）

---

## ステップ 3. Xdebug が有効か確認

```bash
make xdebug-status
```

次のような行が出ていれば OK：

```
with Xdebug v3.5.3 ...
xdebug.mode => debug,develop => debug,develop
xdebug.client_host => host.docker.internal
xdebug.client_port => 9003
xdebug.start_with_request => yes
```

---

## ステップ 4. Xdebug が「IDE を探しに来ている」ことを確認

ここがハンズオンの面白いポイントです。**まだ IDE は繋いでいない**状態で、
ブラウザを再読み込み（または下記）してからログを見ます：

```bash
# 1) リクエストを1回送る
curl -s -o /dev/null http://localhost:8080/login

# 2) Xdebug のログを見る
make xdebug-log
```

すると、こんなログが出ます：

```
[Step Debug] INFO: Connecting to configured address/port: host.docker.internal:9003.
[Step Debug] ERR: Could not connect to debugging client.
```

これは **失敗ではなく正常**です。
「Xdebug は毎リクエストで IDE(9003) を探しに行っているが、まだ誰も待ち受けていない」
という状態。次の段階で IDE を待ち受けにすれば、この接続が成立してブレークします。

> `[01. 環境設定]` で説明した「接続の向き」を、ログで実感できる瞬間です。
> （`make xdebug-log` は `Ctrl-C` で抜けます）

---

## ステップ 5. IDE を繋いでブレークさせる

ここから先は IDE の設定が必要です 👉 **[③ IDE で実際にデバッグする](./xdebug-setup.md)**

ざっくりの流れ：
1. IDE（VSCode / PhpStorm）を「待ち受け」状態にする
2. コードにブレークポイントを置く
3. ブラウザでアクセス → その行で実行が止まる 🎉

---

## 日常の操作チートシート

```bash
make up          # 起動
make down        # 停止
make restart     # 再起動
make fresh       # DBを作り直してデモユーザーを再投入（データをリセットしたい時）
make routes      # URL とコントローラの対応一覧
make shell       # コンテナの中に入る（php artisan などを直接叩きたい時）
make logs        # コンテナのログを監視
make xdebug-log  # Xdebug の接続ログを監視
make help        # コマンド一覧
```

---

## 困ったとき（トラブルシュート）

| 症状 | 確認すること |
|------|--------------|
| `make init` が途中で失敗する | Docker Desktop が起動しているか（`docker info`） |
| http://localhost:8080 が開けない | `make up` 済みか／`docker compose ps` で app が Up か |
| DB エラーが出る | `make fresh` でマイグレーション＋シードをやり直す |
| ポート 8080 が使用中 | 他のアプリが 8080 を使っていないか。compose の `ports` を変更 |
| ブレークしない | → [③ IDE デバッグ](./xdebug-setup.md) の「うまく繋がらないときは」へ |

---

前へ 👈 [① 環境設定の解説](./01-environment.md) ／ 次へ 👉 [③ IDE でデバッグ](./xdebug-setup.md)
