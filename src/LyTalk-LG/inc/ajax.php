<?php

// --------------------
// 🔼 メッセージ送信
// --------------------
add_action('wp_ajax_lytalk_send_message', 'lytalk_send_message');
add_action('wp_ajax_nopriv_lytalk_send_message', 'lytalk_send_message');

function lytalk_send_message() {
    check_ajax_referer('lytalk_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'ly_messages';

    $user_name  = sanitize_text_field($_POST['user_name'] ?? '');
    $user_color = sanitize_hex_color($_POST['user_color'] ?? '');
    $message    = sanitize_textarea_field($_POST['message'] ?? '');
    $is_admin   = current_user_can('manage_options') ? 1 : 0;

    if (empty($user_name) || empty($message)) {
        wp_send_json_error('名前とメッセージは必須です');
    }

    $wpdb->insert($table, [
        'user_name'  => $user_name,
        'user_color' => $user_color ?: '#000000',
        'message'    => $message,
        'is_admin'   => $is_admin,
        'created_at' => current_time('mysql'),
    ]);

    $msg_id = $wpdb->insert_id;

    wp_send_json_success([
        'id'         => $msg_id,
        'user_name'  => $user_name,
        'user_color' => $user_color,
        'message'    => $message,
        'is_admin'   => $is_admin,
        'can_delete' => true
    ]);
}

// --------------------
// 🔁 メッセージ取得
// --------------------
add_action('wp_ajax_lytalk_get_messages', 'lytalk_get_messages');
add_action('wp_ajax_nopriv_lytalk_get_messages', 'lytalk_get_messages');

function lytalk_get_messages() {
    check_ajax_referer('lytalk_nonce', 'nonce');

    global $wpdb;
    $table = $wpdb->prefix . 'ly_messages';

    $rows = $wpdb->get_results("
        SELECT id, user_name, user_color, message, is_admin, created_at
        FROM $table
        ORDER BY created_at DESC
        LIMIT 10
    ", ARRAY_A);

    $user_is_admin = current_user_can('manage_options');

    $data = array_map(function ($row) use ($user_is_admin) {
        $row['can_delete'] = $user_is_admin; // 管理者のみ削除可能（今は仮）
        $row['id']         = (int) $row['id'];
        $row['is_admin']   = (int) $row['is_admin'];
        
        return $row;
    }, array_reverse($rows));

    wp_send_json_success($data);
}

// --------------------
// 🗑️ メッセージ削除
// --------------------
add_action('wp_ajax_lytalk_delete_message', 'lytalk_delete_message');

function lytalk_delete_message() {
    check_ajax_referer('lytalk_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('削除権限がありません');
    }

    global $wpdb;
    $id = intval($_POST['message_id'] ?? 0);
    $table = $wpdb->prefix . 'ly_messages';

    $deleted = $wpdb->delete($table, ['id' => $id]);

    if ($deleted) {
        wp_send_json_success(['deleted_id' => $id]);
    } else {
        wp_send_json_error('削除失敗');
    }
}
