<?php
/**
 * Plugin Name:     LyTalk LG
 * Plugin URI:      https://lazygenius.dev/plugins/lytalk-lg
 * Description:     WordPressに軽量チャットを追加できるミニプラグイン。ショートコード [lytalk] で使用可能。
 * Author:          Leon.C
 * Author URI:      https://lazygenius.dev
 * Version:         0.1.0
 * Text Domain:     lytalk-lg
 * Domain Path:     /languages
 *
 * @package         LyTalk_LG
 */

// 直アクセス防止
if (!defined('ABSPATH')) {
    exit;
}

// ==============================
// 🔧 各種ファイル読み込み
// ==============================

require_once plugin_dir_path(__FILE__) . 'inc/db.php';
require_once plugin_dir_path(__FILE__) . 'inc/ajax.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions.php';

// ==============================
// 🪛 プラグイン有効化時：テーブル作成
// ==============================

register_activation_hook(__FILE__, 'lytalk_create_tables');

// ==============================
// 💬 チャット画面のショートコード
// ==============================

add_shortcode('lytalk', 'lytalk_render_chat');
function lytalk_render_chat() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/chat.php';
    return ob_get_clean();
}

// ==============================
// 🎨 JS/CSS の読み込み
// ==============================

function lytalk_enqueue_assets() {
    // [lightalk] が使われているページだけ読み込みたければ条件分岐も可
    wp_enqueue_style('lytalk-style', plugin_dir_url(__FILE__) . 'assets/css/lytalk.css', [], '0.1.0');
    wp_enqueue_script('lytalk-script', plugin_dir_url(__FILE__) . 'assets/js/lytalk.js', ['jquery'], '0.1.0', true);

    // PHPの値をJSへ渡す（例：ログイン中のユーザー判定など）
    // wp_localize_script('lytalk-script', 'lytalk_data', [
    //     'ajax_url' => admin_url('admin-ajax.php'),
    //     'is_admin' => current_user_can('manage_options') ? 1 : 0,
    //     'nonce'    => wp_create_nonce('lytalk_nonce')
    // ]);
}
add_action('wp_enqueue_scripts', 'lytalk_enqueue_assets');


// ==============================
// メッセージの送信
// ==============================
function lightalk_process_post() {
    if (!isset($_POST['lightalk_submit'])) return;

    global $wpdb;
    $table = $wpdb->prefix . 'ly_messages';

    $user_name  = sanitize_text_field($_POST['user_name'] ?? '');
    $user_color = sanitize_hex_color($_POST['user_color'] ?? '#000000');
    $message    = sanitize_textarea_field($_POST['chat_message'] ?? '');
    $is_admin   = current_user_can('manage_options') ? 1 : 0;

    if ($user_name && $message) {
        $wpdb->insert($table, [
            'user_name'  => $user_name,
            'user_color' => $user_color,
            'message'    => $message,
            'is_admin'   => $is_admin,
            'created_at' => current_time('mysql'),
        ]);
        // リダイレクトしてPOSTリロード防止
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }
}
add_action('init', 'lightalk_process_post');
