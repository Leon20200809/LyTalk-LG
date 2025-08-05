<div id="lightalk-chat">
    <div id="chat-log" style="height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 1rem;">
        <h3>投稿一覧（簡易）</h3>
        <?php
        global $wpdb;
        $table = $wpdb->prefix . 'ly_messages';

        $messages = $wpdb->get_results("
            SELECT * FROM $table
            ORDER BY created_at DESC
            LIMIT 10
        ");

        if ($messages) {
            foreach (array_reverse($messages) as $msg) {
                $color = esc_attr($msg->user_color ?: '#000');
                $name  = esc_html($msg->user_name);
                $text  = esc_html($msg->message);
                $label = $msg->is_admin ? '<strong>[管理者]</strong>' : '';

                echo "<div style='margin-bottom: 6px;'>";
                echo "<span style='color: $color;'>$label $name:</span> $text";
                echo "</div>";
            }
        } else {
            echo '<p>まだメッセージはありません。</p>';
        }
        ?>
    </div>

    <form method="POST">
        <input type="text" name="user_name" placeholder="名前" required>
        <input type="color" name="user_color" value="#2196f3">
        <input type="text" name="chat_message" placeholder="メッセージ" required>
        <button type="submit" name="lightalk_submit">送信</button>
    </form>
</div>