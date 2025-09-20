(() => {
  const onReady = () => {
    const root = document.getElementById('transaction-root');
    const form = document.getElementById('transaction-form');
    const input = document.getElementById('message-input');
    const modal = document.getElementById('complete-modal');

    if (!root) return;

    // ====== Blade から渡された値 ======
    const draftUrl = root.dataset.draftUrl || '';
    const clearUrl = root.dataset.clearUrl || '';
    const csrf     = root.dataset.csrf || '';
    const autoOpen = root.dataset.autoOpenRating === '1';

    // ====== 1) 取引完了モーダル 自動オープン ======
    if (autoOpen && location.hash !== '#complete-modal') {
      location.hash = '#complete-modal';
    }

    // ====== 2) モーダル外クリックで閉じる（:target解除） ======
    if (modal) {
      modal.addEventListener('click', (e) => {
        const dialog = modal.querySelector('.modal__dialog');
        if (!dialog || !dialog.contains(e.target)) {
          if (location.hash) {
            location.hash = '';
            // URLの # を消してきれいに
            setTimeout(() => {
              history.replaceState(null, document.title, location.pathname + location.search);
            }, 0);
          }
        }
      });
    }

    // ====== 3) 入力オートセーブ（サーバーセッション） ======
    if (input && draftUrl) {
      let timer = null;

      const saveDraft = () => {
        fetch(draftUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ body: input.value || '' }),
        }).catch(() => {});
      };

      // 入力のたびデバウンス保存
      input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(saveDraft, 500);
      });

      // タブを離れる直前も最終状態を保存（信頼できる経路）
      const beacon = () => {
        try {
          const blob = new Blob(
            [JSON.stringify({ body: input.value || '' })],
            { type: 'application/json' }
          );
          navigator.sendBeacon(draftUrl, blob);
        } catch (_) {}
      };
      document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') beacon();
      });
      window.addEventListener('pagehide', beacon);

      // Enterで送信（Shift+Enterで改行）※テキストエリアの場合は調整
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          form?.requestSubmit();
        }
      });
    }

    // ====== 4) 送信時：下書きをサーバ側で即時クリア ======
    if (form && clearUrl) {
      form.addEventListener('submit', () => {
        fetch(clearUrl, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrf },
        }).catch(() => {});
      });
    }
  };

  // 重複登録を避けるため一括で1回だけ
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onReady);
  } else {
    onReady();
  }
})();





// // ===== 入力オートセーブ　（ローカルストレージ版） =====
// (() => {
//   // 取引画面のルート要素からIDなど取得
//   const root  = document.getElementById("transaction-root");
//   const form  = document.getElementById("transaction-form");
//   const input = document.getElementById("message-input");
//   if (!root || !form || !input) return;

//   // 取引ID × ユーザーID ごとにキーを分ける
//   const txId   = root.getAttribute("data-transaction-id") || "unknownTx";
//   const userId = root.getAttribute("data-user-id") || "guest";
//   const KEY    = `txDraft:${txId}:${userId}`;

//   // ===== 復元 =====
//   // サーバー側 old('body') などが既に入っていればそれを優先。
//   // 何も入っていない場合のみ localStorage から復元。
//   if (!input.value?.trim()) {
//     const draft = localStorage.getItem(KEY);
//     if (draft !== null) input.value = draft;
//   }

//   // ===== 保存（デバウンス） =====
//   let timer = null;
//   const saveDraft = () => {
//     try { localStorage.setItem(KEY, input.value || ""); } catch (_) {}
//   };
//   const onInput = () => {
//     if (timer) clearTimeout(timer);
//     timer = setTimeout(saveDraft, 300); // 0.3s デバウンス
//   };
//   input.addEventListener("input", onInput);

//   // ===== Enterで送信（Shift+Enterで改行）※textareaにしたら適宜調整
//   input.addEventListener("keydown", (e) => {
//     if (e.key === "Enter" && !e.shiftKey) {
//       e.preventDefault();
//       form.requestSubmit();
//     }
//   });

//   // ===== 送信時は下書きを削除 =====
//   form.addEventListener("submit", () => {
//     try { localStorage.removeItem(KEY); } catch (_) {}
//   });

//   // ===== ページ離脱時も最後の状態を保存（保険） =====
//   const flush = () => saveDraft();
//   window.addEventListener("beforeunload", flush);
//   document.addEventListener("visibilitychange", () => {
//     if (document.visibilityState === "hidden") flush();
//   });
// })();