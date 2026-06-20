# learn-xdebug

PHP / Laravel で **Xdebug の使い方を学ぶ** ための教材プロジェクトです。
Docker でビルド済みの環境に、MVCS 構成のシンプルなログイン機能が入っています。
ブレークポイントを置いて、リクエストがコードを流れる様子をステップ実行で追いかけてみましょう。

## 技術スタック

- PHP 8.4 + Apache（mod_php）
- Laravel 13
- MySQL 8.0
- Xdebug 3
- Docker / Docker Compose

## クイックスタート

```bash
# 初回セットアップ（イメージビルド〜DB準備まで一括）
make init
```

完了したら http://localhost:8080 へアクセス。

デモアカウント：

| メールアドレス | パスワード |
|----------------|-----------|
| demo@example.com | password |

新規登録から自分でアカウントを作ることもできます。

## ハンズオン教材

対面で学ぶための資料を [docs/](docs/) にまとめています。上から順にどうぞ：

1. [① 環境設定の解説](docs/01-environment.md) — Docker / Xdebug の中身を理解する
2. [② 動かし方](docs/02-run.md) — 起動して動作確認する
3. [③ IDE でデバッグ](docs/xdebug-setup.md) — VSCode / PhpStorm を繋ぐ

ログインの流れ（Controller → Service → Model）をステップ実行で追うのがおすすめです。

## ディレクトリ構成（MVCS）

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/Auth/   … C: 入り口。薄く保つ
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   └── Requests/           … 入力バリデーション
│   ├── Services/               … S: ビジネスロジック（認証処理の本体）
│   │   └── AuthService.php
│   └── Models/                 … M: データ（Eloquent）
│       └── User.php
├── resources/views/            … V: 画面（Blade）
│   ├── auth/{login,register}.blade.php
│   └── dashboard.blade.php
└── routes/web.php              … URL とコントローラの対応
```

**MVCS** は MVC に **Service 層** を足した構成です。
Controller は「受け取って Service に渡して結果を返す」だけにし、
判定や永続化などの実処理は Service に集約します。

## よく使うコマンド

```bash
make help        # コマンド一覧
make up          # 起動
make down        # 停止
make fresh       # DBを作り直してデモユーザーを再投入
make routes      # ルート一覧
make shell       # コンテナに入る
make xdebug-log  # Xdebug の接続ログを監視
```
