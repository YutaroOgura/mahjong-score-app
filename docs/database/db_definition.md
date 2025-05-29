# 麻雀収支計算アプリケーション - データベース定義書

## 1. 概要

このドキュメントは麻雀収支計算アプリケーションで使用されるデータベースの構造を定義します。本アプリケーションは、麻雀の対局結果を記録し、プレイヤーごとの収支や統計情報を管理するためのシステムです。

## 2. テーブル一覧

| テーブル名 | 説明 |
|------------|------|
| players | プレイヤー情報を管理するテーブル |
| games | 対局情報を管理するテーブル |
| game_results | 対局結果を管理するテーブル |

## 3. テーブル定義

### 3.1 players テーブル

プレイヤー情報を管理するテーブルです。

#### 3.1.1 カラム定義

| カラム名 | データ型 | NULL許可 | 主キー | デフォルト値 | 説明 |
|----------|----------|----------|--------|--------------|------|
| id | BIGINT UNSIGNED | NO | YES | AUTO_INCREMENT | プレイヤーID |
| name | VARCHAR(255) | NO | NO | なし | プレイヤー名 |
| nickname | VARCHAR(255) | YES | NO | NULL | ニックネーム |
| total_games | INTEGER | NO | NO | 0 | 総対局数 |
| total_points | INTEGER | NO | NO | 0 | 総得点 |
| average_rank | DECIMAL(3,2) | NO | NO | 0 | 平均順位 |
| created_at | TIMESTAMP | YES | NO | NULL | 作成日時 |
| updated_at | TIMESTAMP | YES | NO | NULL | 更新日時 |
| deleted_at | TIMESTAMP | YES | NO | NULL | 削除日時（ソフトデリート用） |

#### 3.1.2 インデックス

| インデックス名 | カラム | 種類 | 説明 |
|----------------|--------|------|------|
| PRIMARY | id | PRIMARY KEY | 主キー |
| players_name_index | name | INDEX | プレイヤー名による検索用 |

### 3.2 games テーブル

対局情報を管理するテーブルです。

#### 3.2.1 カラム定義

| カラム名 | データ型 | NULL許可 | 主キー | デフォルト値 | 説明 |
|----------|----------|----------|--------|--------------|------|
| id | BIGINT UNSIGNED | NO | YES | AUTO_INCREMENT | 対局ID |
| played_at | DATETIME | NO | NO | なし | 対局日時 |
| location | VARCHAR(255) | YES | NO | NULL | 対局場所 |
| game_type | ENUM('east_only', 'east_south') | NO | NO | 'east_south' | ゲームタイプ（東風戦/東南戦） |
| uma_1st | INTEGER | NO | NO | 30 | 1位のウマ |
| uma_2nd | INTEGER | NO | NO | 10 | 2位のウマ |
| uma_3rd | INTEGER | NO | NO | -10 | 3位のウマ |
| uma_4th | INTEGER | NO | NO | -30 | 4位のウマ |
| starting_points | INTEGER | NO | NO | 25000 | 配給原点 |
| note | TEXT | YES | NO | NULL | メモ |
| created_at | TIMESTAMP | YES | NO | NULL | 作成日時 |
| updated_at | TIMESTAMP | YES | NO | NULL | 更新日時 |
| deleted_at | TIMESTAMP | YES | NO | NULL | 削除日時（ソフトデリート用） |

#### 3.2.2 インデックス

| インデックス名 | カラム | 種類 | 説明 |
|----------------|--------|------|------|
| PRIMARY | id | PRIMARY KEY | 主キー |
| games_played_at_index | played_at | INDEX | 対局日時による検索用 |

### 3.3 game_results テーブル

対局結果を管理するテーブルです。

#### 3.3.1 カラム定義

| カラム名 | データ型 | NULL許可 | 主キー | デフォルト値 | 説明 |
|----------|----------|----------|--------|--------------|------|
| id | BIGINT UNSIGNED | NO | YES | AUTO_INCREMENT | 結果ID |
| game_id | BIGINT UNSIGNED | NO | NO | なし | 対局ID（外部キー） |
| player_id | BIGINT UNSIGNED | NO | NO | なし | プレイヤーID（外部キー） |
| seat | INTEGER | NO | NO | なし | 席順（0:東, 1:南, 2:西, 3:北） |
| rank | INTEGER | NO | NO | なし | 順位（1-4） |
| points | INTEGER | NO | NO | なし | 得点 |
| score | INTEGER | NO | NO | なし | 最終スコア（ウマ込み） |
| created_at | TIMESTAMP | YES | NO | NULL | 作成日時 |
| updated_at | TIMESTAMP | YES | NO | NULL | 更新日時 |

#### 3.3.2 インデックス

| インデックス名 | カラム | 種類 | 説明 |
|----------------|--------|------|------|
| PRIMARY | id | PRIMARY KEY | 主キー |
| game_results_game_id_foreign | game_id | FOREIGN KEY | 外部キー（games.id） |
| game_results_player_id_foreign | player_id | FOREIGN KEY | 外部キー（players.id） |
| game_results_game_id_player_id_unique | game_id, player_id | UNIQUE | 同一対局での同一プレイヤーの重複防止 |
| game_results_game_id_seat_unique | game_id, seat | UNIQUE | 同一対局での同一席順の重複防止 |
| game_results_game_id_rank_unique | game_id, rank | UNIQUE | 同一対局での同一順位の重複防止 |

## 4. リレーションシップ

### 4.1 players と game_results の関係

- 関係: 1対多（One-to-Many）
- 説明: 1人のプレイヤーは複数の対局結果を持つことができる
- 外部キー制約: `game_results.player_id` → `players.id` (CASCADE DELETE)

### 4.2 games と game_results の関係

- 関係: 1対多（One-to-Many）
- 説明: 1つの対局は複数の対局結果を持つ（通常は4つ）
- 外部キー制約: `game_results.game_id` → `games.id` (CASCADE DELETE)

### 4.3 players と games の関係

- 関係: 多対多（Many-to-Many）
- 説明: 1人のプレイヤーは複数の対局に参加でき、1つの対局には複数のプレイヤーが参加する
- 中間テーブル: `game_results`

## 5. データ整合性制約

### 5.1 一意性制約

- `game_results` テーブルでは、同一の対局（`game_id`）において以下の制約が適用されます:
  - 同一プレイヤー（`player_id`）の重複は許可されない
  - 同一席順（`seat`）の重複は許可されない
  - 同一順位（`rank`）の重複は許可されない

### 5.2 参照整合性制約

- `game_results.game_id` は必ず `games.id` に存在する値を参照する
- `game_results.player_id` は必ず `players.id` に存在する値を参照する
- 親レコードが削除された場合、関連する子レコードも削除される（CASCADE DELETE）

### 5.3 ドメイン制約

- `players.total_games` は0以上の整数
- `players.total_points` は任意の整数
- `players.average_rank` は1.00から4.00の範囲の小数
- `games.game_type` は 'east_only' または 'east_south' のいずれか
- `game_results.seat` は0から3の整数
- `game_results.rank` は1から4の整数

## 6. インデックス戦略

### 6.1 検索パフォーマンス向上のためのインデックス

- `players.name` に対するインデックス: プレイヤー名による検索を高速化
- `games.played_at` に対するインデックス: 日付範囲による対局検索を高速化

### 6.2 外部キーに対するインデックス

- `game_results.game_id` に対するインデックス: 特定の対局に関連する結果の検索を高速化
- `game_results.player_id` に対するインデックス: 特定のプレイヤーに関連する結果の検索を高速化

## 7. ソフトデリート

以下のテーブルではソフトデリート（論理削除）が実装されています:

- `players`: `deleted_at` カラムが NULL でない場合、そのレコードは削除されたとみなされる
- `games`: `deleted_at` カラムが NULL でない場合、そのレコードは削除されたとみなされる

`game_results` テーブルではソフトデリートは実装されていません。親テーブル（`games` または `players`）のレコードが削除された場合、関連する `game_results` のレコードは物理的に削除されます。

## 8. 統計データの更新

`players` テーブルの統計データ（`total_games`, `total_points`, `average_rank`）は、新しい対局結果が登録されるたびに更新されます。これらの値は以下のように計算されます:

- `total_games`: プレイヤーが参加した対局の総数
- `total_points`: プレイヤーが獲得した総得点（すべての対局のスコアの合計）
- `average_rank`: プレイヤーの平均順位（すべての対局の順位の平均）

## 9. データマイグレーション

データベーススキーマの変更は Laravel のマイグレーション機能を使用して管理されます。マイグレーションファイルは以下の場所に保存されています:

- `database/migrations/2024_03_19_000001_create_players_table.php`
- `database/migrations/2024_03_19_000002_create_games_table.php`
- `database/migrations/2024_03_19_000003_create_game_results_table.php`

## 10. バックアップ戦略

データベースのバックアップは以下の方針で実施されます:

- 日次完全バックアップ: 毎日深夜にデータベース全体のバックアップを作成
- 差分バックアップ: 6時間ごとに前回のバックアップからの変更部分のみをバックアップ
- バックアップの保持期間: 完全バックアップは30日間、差分バックアップは7日間保持
- バックアップの保存場所: プライマリストレージとは別の物理的な場所に保存
