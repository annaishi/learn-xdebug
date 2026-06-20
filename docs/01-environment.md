# ① 環境設定の解説 — Docker と Xdebug の中身を理解する

このページでは「なぜこの設定で Xdebug が動くのか」を、ファイルごとに読み解きます。
ハンズオンの最初に全員で目を通すパートです。

---

## 0. そもそも Xdebug とは？

Xdebug は PHP の **デバッグ拡張**です。`var_dump()` や `dd()` を仕込んで動かす代わりに、

- 任意の行で実行を**一時停止**（ブレークポイント）
- そのときの**変数の中身**をすべて確認
- **1行ずつ**実行を進める（ステップ実行）
- 関数の**呼び出し履歴**（スタックトレース）を確認

ができるようになります。「コードがどう動いているか」を目で追える、という体験が一番の価値です。

### 接続の向きが肝心

初心者がつまずく最大のポイントがこれです。

```
ブラウザ ──HTTPリクエスト──▶ PHP(コンテナ)
                                │ Xdebug が
                                │「IDE に繋ぎに行く」← この向き！
                                ▼
                      IDE(VSCode / PhpStorm) ◀ ポート 9003 で待ち受け
```

**PHP 側から IDE へ接続しに来ます。** だから IDE を先に「待ち受け状態」にしておく必要があります。
（よくある勘違い：「IDE が PHP を見に行く」と思ってしまう → 順番を間違えて繋がらない）

---

## 1. 全体構成

```
learn-xdebug/
├── docker-compose.yml         … コンテナ2つ(app/db)の定義
├── docker/php/
│   ├── Dockerfile             … PHP+Apache+Xdebug のイメージ定義
│   └── conf.d/
│       ├── xdebug.ini         … Xdebug の設定（最重要）
│       └── php.ini            … PHP の開発用設定
└── src/                       … Laravel アプリ本体（コンテナの /var/www/html にマウント）
```

コンテナは2つ：

| コンテナ | 役割 | ポート |
|----------|------|--------|
| `app` | PHP 8.4 + Apache + Xdebug | ホスト 8080 → コンテナ 80 |
| `db`  | MySQL 8.0 | ホスト 3306 → コンテナ 3306 |

---

## 2. Dockerfile — Xdebug 入り PHP イメージを作る

[docker/php/Dockerfile](../docker/php/Dockerfile)

ブロックごとに見ていきます。

```dockerfile
FROM php:8.4-apache
```
PHP 8.4 と Apache（mod_php）が同梱された公式イメージが土台。
これ単体では Xdebug も MySQL ドライバも入っていないので、以降で足していきます。

```dockerfile
RUN docker-php-ext-install pdo_mysql mbstring zip bcmath
```
Laravel と MySQL に必要な PHP 拡張を追加。`pdo_mysql` が無いと DB に繋がりません。

```dockerfile
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
```
**ここが主役。** PECL から Xdebug をインストールして有効化します。
（ビルド時にコンパイルが走るので、`make build` は初回少し時間がかかります）

```dockerfile
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
```
Composer（PHP のパッケージ管理ツール）を別イメージからコピーして使えるようにします。

```dockerfile
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' ...
```
Laravel は `public/` がドキュメントルート。Apache の公開ディレクトリを
`/var/www/html/public` に向け替え、`mod_rewrite`（きれいな URL のため）を有効化します。

```dockerfile
COPY conf.d/php.ini    /usr/local/etc/php/conf.d/zzz-custom-php.ini
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/zzz-xdebug.ini
```
自前の設定ファイルを PHP の設定ディレクトリに置きます。
`zzz-` を付けているのは、**他の設定より後に読ませて確実に上書き**するためです。

---

## 3. xdebug.ini — 最重要の設定ファイル

[docker/php/conf.d/xdebug.ini](../docker/php/conf.d/xdebug.ini)

ここが Xdebug の挙動を決めます。1行ずつ意味を押さえましょう。

```ini
xdebug.mode=debug,develop
```
Xdebug の**動作モード**。Xdebug 3 では「何をするか」をここで選びます。
- `debug` … ステップ実行（ブレークポイント）。今回の主目的。
- `develop` … `var_dump` を見やすくする等の開発支援。
- 他に `coverage`（カバレッジ）, `profile`（性能計測）, `trace` もあります。

> モードは増えるほど少し遅くなります。本番環境では Xdebug を**入れない/無効**が鉄則。

```ini
xdebug.start_with_request=yes
```
**いつデバッグを開始するか。** `yes` は「毎回のリクエストで自動的に IDE へ繋ぎに行く」。
学習中はこれが一番ラク。
（`trigger` にすると、特定の合図があるときだけ繋ぐ。本番に近い環境で使う上級設定）

```ini
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
```
**接続先＝IDE の場所。** コンテナの中から見た「ホスト PC」が `host.docker.internal`。
`9003` は Xdebug 3 の標準ポートで、IDE 側もこの番号で待ち受けます。

```ini
xdebug.idekey=VSCODE
```
複数の IDE/セッションを区別するためのキー。基本そのままでOK。

```ini
xdebug.log=/tmp/xdebug.log
xdebug.log_level=7
```
**トラブル解決の生命線。** Xdebug の接続試行や失敗理由がここに記録されます。
「繋がらない」ときは必ずこのログを見ます（`make xdebug-log`）。

---

## 4. php.ini — 開発用の PHP 設定

[docker/php/conf.d/php.ini](../docker/php/conf.d/php.ini)

```ini
display_errors=On          # エラーを画面に出す（開発用）
error_reporting=E_ALL      # 全種類のエラー/警告を表示
memory_limit=512M          # メモリ上限を緩めに
date.timezone=Asia/Tokyo   # タイムゾーン
```
Xdebug 本体とは別の、PHP を開発しやすくする設定です。本番ではこれらは絞ります。

---

## 5. docker-compose.yml — コンテナの起動定義

[docker-compose.yml](../docker-compose.yml)

Xdebug に関係する重要ポイントだけ抜粋します。

```yaml
  app:
    build: { context: ./docker/php }
    ports:
      - "8080:80"            # ブラウザは http://localhost:8080
    volumes:
      - ./src:/var/www/html  # ローカルのsrc/がコンテナ内に直結
    extra_hosts:
      - "host.docker.internal:host-gateway"  # Linuxでもホストを解決可能に
```

- **`volumes`（マウント）が効く理由**：`src/` を編集すると即コンテナに反映され、
  さらに Xdebug は「コンテナ内 `/var/www/html`」＝「ローカル `src/`」の対応さえ
  IDE に教えれば、ローカルのファイルにブレークポイントを置けます（＝パスマッピング）。
- **`extra_hosts`**：Mac/Windows の Docker Desktop は `host.docker.internal` を自動解決しますが、
  Linux では解決できないことがあるため明示しています。

```yaml
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret
    healthcheck: ...        # DBが「準備完了」になるまでappを待たせる
```
`healthcheck` のおかげで、`app` は DB が応答可能になってから起動します
（起動直後の「DB に繋がらない」エラーを防ぐ）。

---

## 6. パスマッピング（この教材で一番大事な概念）

```
コンテナの中:  /var/www/html/app/Services/AuthService.php
ローカル:      ./src/app/Services/AuthService.php
                ~~~~~              ~~~~~~~~~~~~~~~~~~~~~~~~~
                src/ = /var/www/html  という対応を IDE に教える
```

これを IDE に設定しないと、「コンテナ側のどのファイルが、手元のどのファイルか」が
分からず、ブレークポイントが効きません。
具体的な設定方法は次のページ以降で扱います。

---

次へ 👉 [② 動かし方](./02-run.md)
