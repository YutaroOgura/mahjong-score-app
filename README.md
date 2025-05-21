# 麻雀収支計算アプリケーション

## 概要
このアプリケーションは、麻雀の対局結果を記録し、収支を管理するためのウェブアプリケーションです。

## 機能
- 対局結果の記録
- プレイヤー管理
- 収支計算
- 統計情報の表示
- レポート出力

## 技術スタック
- PHP 8.1
- Laravel 10
- MySQL 8.0
- Apache
- Docker

## 開発環境のセットアップ

### 必要条件
- Docker
- Docker Compose

### インストール手順

1. リポジトリのクローン
```bash
git clone [repository-url]
cd mahjong-score-app
```

2. 環境変数の設定
```bash
cp src/.env.example src/.env
```

3. Dockerコンテナの起動
```bash
docker-compose up -d
```

4. Composerパッケージのインストール
```bash
docker-compose exec web composer install
```

5. アプリケーションキーの生成
```bash
docker-compose exec web php artisan key:generate
```

6. データベースのマイグレーション
```bash
docker-compose exec web php artisan migrate
```

### アクセス方法
- ウェブアプリケーション: http://localhost:8080
- phpMyAdmin: http://localhost:8081

## 開発ガイドライン
- コーディング規約: PSR-12に準拠
- Gitブランチ戦略: GitHub Flow
- テスト: PHPUnit

## ライセンス
MIT License 