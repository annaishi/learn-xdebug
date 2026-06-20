# ===================================================================
# learn-xdebug  Makefile
#   よく使う操作をまとめたショートカット集。
#   `make` または `make help` で一覧を表示します。
# ===================================================================

# app コンテナで artisan / composer などを叩くための共通プレフィックス
DC      := docker compose
APP     := $(DC) exec app

.DEFAULT_GOAL := help

# ---- セットアップ系 ------------------------------------------------

.PHONY: init
init: build up composer-install env migrate seed ## 初回セットアップ（ビルド〜DB準備まで一括）
	@echo "✅ セットアップ完了！ http://localhost:8080 にアクセスしてください"
	@echo "   デモアカウント: demo@example.com / password"

.PHONY: build
build: ## Docker イメージをビルド
	$(DC) build

.PHONY: up
up: ## コンテナを起動（バックグラウンド）
	$(DC) up -d

.PHONY: down
down: ## コンテナを停止・削除
	$(DC) down

.PHONY: restart
restart: down up ## コンテナを再起動

.PHONY: destroy
destroy: ## コンテナとDBボリュームを完全に削除（データも消える）
	$(DC) down -v

# ---- アプリ操作系 --------------------------------------------------

.PHONY: composer-install
composer-install: ## composer install を実行
	$(APP) composer install

.PHONY: env
env: ## .env が無ければ .env.example からコピー & アプリキー生成
	$(APP) sh -c '[ -f .env ] || cp .env.example .env'
	$(APP) php artisan key:generate

.PHONY: migrate
migrate: ## マイグレーション実行
	$(APP) php artisan migrate

.PHONY: fresh
fresh: ## DBを作り直してシードまで実行
	$(APP) php artisan migrate:fresh --seed

.PHONY: seed
seed: ## シーダー実行（デモユーザー作成）
	$(APP) php artisan db:seed

.PHONY: routes
routes: ## ルート一覧を表示
	$(APP) php artisan route:list

# ---- 開発補助系 ----------------------------------------------------

.PHONY: shell
shell: ## app コンテナに bash で入る
	$(APP) bash

.PHONY: tinker
tinker: ## artisan tinker（対話シェル）を起動
	$(APP) php artisan tinker

.PHONY: logs
logs: ## コンテナのログを表示（Ctrl-Cで終了）
	$(DC) logs -f

.PHONY: test
test: ## PHPUnit / Pest テストを実行
	$(APP) php artisan test

# ---- Xdebug 系 -----------------------------------------------------

.PHONY: xdebug-log
xdebug-log: ## Xdebug のログ（接続状況）を表示
	$(APP) tail -f /tmp/xdebug.log

.PHONY: xdebug-status
xdebug-status: ## Xdebug が有効か確認
	$(APP) php -v
	@echo "---"
	$(APP) php -i | grep -iE 'xdebug.mode|client_host|client_port|start_with_request'

# ---- ヘルプ --------------------------------------------------------

.PHONY: help
help: ## このヘルプを表示
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}'
