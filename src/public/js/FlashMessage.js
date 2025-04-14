class FlashMessage {
  constructor() {
    this.$container = $('<div class="flash-message-container"></div>').appendTo(
      "body",
    );
    this.injectStyles();
  }

  injectStyles() {
    if ($("#flash-message-styles").length) return;

    const styles = `
      .flash-message-container {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
      }
      .flash-message {
        border-radius: 3px;
        font-size: 16px;
        color: #fff;
        padding: 12px 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        transition: opacity 0.3s ease-in-out;
      }
      .flash-message.success {
        background-color: #4CAF50;
      }
      .flash-message.error {
        background-color: #F44336;
      }
    `;

    $("<style>", {
      id: "flash-message-styles",
      text: styles,
    }).appendTo("head");
  }

  show(message, type = "success", duration = 3000) {
    const $msg = $('<div class="flash-message"></div>')
      .addClass(type)
      .text(message)
      .css({ opacity: 0 });

    this.$container.append($msg);

    $msg.animate({ opacity: 1 }, 10);

    setTimeout(() => {
      $msg.animate({ opacity: 0 }, 300, () => $msg.remove());
    }, duration);
  }
}
