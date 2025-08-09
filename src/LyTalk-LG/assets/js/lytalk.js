jQuery(document).ready(function ($) {

	let interacted = false;
	window.addEventListener('click', () => interacted = true, { once:true });

	if (interacted) {
		document.getElementById('chat-sound').play().catch(err => console.warn(err));
	}

	/*** 最新メッセージ確認したら音で通知 ***/
	let lastId = null;

	function checkMessages(res) {
		if (!res.success || res.data.length === 0) return;

		// 最新のメッセージID
		const latestId = res.data[res.data.length - 1].id;

		// 初回ロードは基準セットだけ（音は鳴らさない）
		if (lastId === null) {
			lastId = latestId;
			return;
		}

		console.log("lastId:" + lastId);
		console.log("latestId:" + latestId);
		// 新着があれば一回だけ鳴らす
		if (latestId > lastId) {
			const el = document.getElementById('chat-sound');
			// play() は Promise 返すので失敗しても無視
			el.play().catch(() => {});
		}

		// 受信した中で最大IDに更新（連続新着にも強い）
		lastId = Math.max(lastId, latestId);
		
	}

	/*** 😀 表示/非表示 ***/
	$("#visible-btn").on("click", function(){
		$("#lightalk-chat").slideToggle();

		// 今のテキストで切り替える
		const isInvisible = $(this).text().includes("閉");
		$(this).text(isInvisible ? "▼ チャットを開く　" : "▲ チャットを閉じる");
	})

	/*** 🔼 送信処理 ***/
	$("#chat-form").on("submit", function (e) {
		// 非同期でやるのでページリロードしないように
		console.log("チャット送信ボタンおした");
		e.preventDefault();

		const name = $("#user-name").val();
		const color = $("#user-color").val();
		const message = $("#chat-message").val();

		if (!name || !message) return;

		$.post(
			// ← jQueryのPOST送信（非同期Ajax）を開始
			lytalk_data.ajax_url, // ← 送信先URL（admin-ajax.php）。PHPからwp_localize_scriptで渡された値
			{
				action: "lytalk_send_message", // ← PHPでフックするAjaxアクション名（wp_ajax_●●）
				nonce: lytalk_data.nonce, // ← WordPressのセキュリティトークン（正規リクエストかを確認）
				user_name: name,
				user_color: color,
				message: message,
			},
			function (response) {
				// ← PHPから返ってきた結果（JSON形式）を受け取るコールバック関数
				if (response.success) {
					// ← PHP側で wp_send_json_success() が実行された場合 true
					$("#chat-message").val("");
					appendMessage(response.data);
				} else {
					alert("送信失敗");
				}
			}
		);
	});

	/*** 🔁 メッセージ取得処理（ポーリング） ***/
	function fetchMessages() {
		$.ajax({
			// jQueryのAjax（$.postより柔軟）で非同期通信スタート
			url: lytalk_data.ajax_url, // WordPressのAjax受付URL（admin-ajax.php）
			method: "POST", // POSTメソッドで送信
			data: {
				action: "lytalk_get_messages", // PHP側のアクション名（wp_ajax_lytalk_get_messages）と一致させる
				nonce: lytalk_data.nonce, // セキュリティトークン（正規のJSからのリクエストかを検証）
			},
			success: function (res) {
				// console.table(res);
				// 通信成功時のコールバック関数（PHPが返したJSONが入る）
				if (res.success) {

					checkMessages(res);

					// PHPが wp_send_json_success() で返した場合
					$("#chat-log").html("");
					res.data.forEach(appendMessage); // 受け取ったメッセージ配列を順に描画（appendMessage関数を使って）
				}
			},
			error: function () {
				console.error("取得エラー");
			},
		});
	}
	// 3秒ごと取得
	setInterval(fetchMessages, 3000);

	/*** 🗑️ 削除処理（イベント委任） ***/
	$("#chat-log").on("click", ".delete-btn", function () {
		const messageId = $(this).data("id");

		$.post(
			lytalk_data.ajax_url,
			{
				action: "lytalk_delete_message",
				nonce: lytalk_data.nonce,
				message_id: messageId,
			},
			function (res) {
				if (res.success) {
					$(`#message-${messageId}`).remove();
				} else {
					alert("削除失敗");
				}
			}
		);
	});

	/*** 表示処理 ***/
	function appendMessage(msg) {

		// created_at が存在するか確認する
    	const createdAt = msg.created_at ? escapeHTML(msg.created_at.slice(0, 16)) : '';

		const html = `
            <div id="message-${msg.id}" class="chat-message">
				<span class="ccreated-at" style="color:${escapeHTML(msg.user_color)}">
					${createdAt}
				</span><br>
                <span class="chat-name" style="color:${escapeHTML(msg.user_color)}">
				${msg.is_admin ? '[管理者]' : ''}${escapeHTML(msg.user_name)}:
                </span>
                <span class="chat-text">${escapeAndFormat(msg.message)}</span>
				${msg.can_delete ? `<button class="delete-button" data-id="${msg.id}">削除</button>` : ''}
            </div>
            `;
        $("#chat-log").append(html);
	}

    function escapeHTML(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
    }

    function escapeAndFormat(str) {
    return escapeHTML(str).replace(/\n/g, "<br>");
    }

	
});
