# LyTalk-LG

🎈 WordPress対応の軽量チャットプラグイン  
**管理者が在席中だったら返事が返ってくるかもしれない気軽なチャットUIを実現。**

---

## ✅ 現在の機能

- [x] 匿名でもチャット投稿可能（名前＋色指定）
- [x] WordPress管理者は `[管理者]` として識別される
- [x] 投稿メッセージはDBに保存され、ページ下部に表示される
- [x] ショートコード `[lightalk]` で任意のページにチャットUIを表示

---

## 📦 使い方（導入方法）

1. プラグインディレクトリへアップロード：

    ```
    /wp-content/plugins/LyTalk-LG/
    ```

2. WordPress管理画面から **プラグインを有効化**

3. 任意のページや投稿に以下のショートコードを挿入：

    ```
    [lytalk]
    ```

---

## 🖼️ スクリーンショット

![Lightalk UI Screenshot](screenshots/screenshot.jpg)

---

## 🛠️ 開発メモ

- DBテーブルは有効化時に自動作成（`ly_messages`, `ly_admin_status`）
- フォーム送信は現在 POST + リロード方式（今後 Ajax化予定）
- 管理者判定は `current_user_can('manage_options')` を使用

---

## 🧩 今後の予定

- [ ] Ajax対応でリロードなし送信＆表示
- [ ] 管理者による在席／離席トグル
- [ ] localStorage によるユーザー情報保持
- [ ] メッセージ削除機能（投稿者のみ）
- [ ] UIスタイル改善（CSSバッジ、ログ整形など）

---

## ✨ キャッチコピー

> Lightalk｜気軽に話しかけられるチャット  
> 担当が“いま席にいて手が空いていれば”、そのままお返事できます。

---

## 🧑‍💻 開発者

**Leon.C**  
https://lazygenius.dev  
GitHub: [@Leon20200809](https://github.com/Leon20200809)
