<?php
// inc/db.php

function lytalk_create_tables() {
    // WordPressのデータベース操作用グローバル変数
    global $wpdb;

    // WordPressがサイト全体で使っている標準的な設定（文字コード）を適用するため文字列照合
    $charset_collate = $wpdb->get_charset_collate();

    // WordPressの標準テーブルと名前が重複しないように
    $prefix = $wpdb->prefix;
    
    // ABSPATH:WordPressのインストールされたパス名
    // dbDelta() データベースのテーブルを作成・更新するための関数を使うために必要なWordPressのコアファイル
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // SQLファイルディレクトリ
    $sql_dir = plugin_dir_path(__FILE__) . 'schema/';

    // SQLファイルパス
    $files = ['admin_status.sql', 'chat_messages.sql'];

    foreach($files as $file){
        // 文字列で読み込み
        $sql = file_get_contents($sql_dir . $file);

        // プレフィックスを置換
        $sql = str_replace('{prefix}', $prefix, $sql);

        // 末尾に文字セット情報追加（dbDelta用）
        // dbDelta() を使うときは最後に CHARACTER SET や COLLATE 情報を含める必要があるので $charset_collate を付加する
        $sql = rtrim($sql, " \t\n\r\0\x0B;") . " $charset_collate;";

        // データベース更新
        dbDelta($sql);
    }

    // ステータス初期レコード（1行固定）
    $wpdb->replace($prefix . 'admin_status', [
        'id' => 1,
        'is_online' => 0,
        'updated_at' => current_time('mysql'),
    ]);
}
