# Xdebug ハンズオン教材

PHP / Laravel で Xdebug を学ぶための、対面ハンズオン用ドキュメントです。
上から順に進めてください。

## 進め方

1. **[① 環境設定の解説](./01-environment.md)**
   Docker / Xdebug の設定ファイルを読み解き、「なぜ動くのか」を理解する。
2. **[② 動かし方](./02-run.md)**
   実際に環境を起動し、ブラウザでアプリを開き、Xdebug が動いていることを確認する。
3. **③ IDE を繋いでデバッグする**（お使いの IDE を選択）
   - VSCode → **[VSCode セットアップ](./vscode-setup.md)**（手順＋つまずきポイント付き）
   - PhpStorm / 両IDE概要 → [xdebug-setup.md](./xdebug-setup.md)
4. **[③-2 基本操作を体で覚える](./03-basic-operations.md)**
   実際のログイン処理で、ステップ実行・変数確認・コールスタック等を1つずつ体験する。

## 題材アプリ

MVCS 構成のシンプルなログイン機能（ログイン / 新規登録 / ダッシュボード）。
ログインの流れ **Controller → Service → Model** をステップ実行で追うのが
Xdebug 体験の入り口としておすすめです。

デモアカウント： `demo@example.com` / `password`
