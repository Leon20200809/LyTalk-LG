<div id="lightalk-chat">
    <div id="chat-log" class="chat-log mb-2">
        
    </div>

    <form id="chat-form" class="chat-form">
        <div class="d-flex mb-2">
            <label>識別色：<input type="color" id="user-color" name="user_color" value="#2196f3"></label>
            <input type="text" id="user-name" name="user_name" placeholder="お名前" required maxlength="20">
        </div>

        <div class=".d-flex-center mb-2">
            <input type="text" id="chat-message" name="chat_message" placeholder="知りたい事を送ってください" required maxlength="100">
        </div>
        <div class="d-flex-center">
            <p class="warning-text">※100文字まで送信できます。個人情報は入力しないようお願いします。</p>
            <button type="submit" name="lytalk_submit">送信</button>
        </div>
        
    </form>
</div>

<!-- チャットウインドウ開閉ボタン -->
<div id="visible-btn">▲ チャットを開く</div>

<!-- 音鳴らす用 -->
<audio id="chat-sound" src="<?php echo esc_url( plugin_dir_url( dirname(__FILE__) ) . 'assets/audio/notification-sound.mp3' ); ?>" preload="auto"></audio>

