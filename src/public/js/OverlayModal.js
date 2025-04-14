class OverlayModal {
  constructor() {
    this.$overlay = $(".modal-overlay");
  }

  init() {
    // Using event delegation for closing modal
    $(document).on(
      "click",
      ".modal-overlay .close-modal, .modal-overlay .btn-cancel",
      () => this.closeModal(),
    );

    // Close if clicking outside content
    $(document).on("click", ".modal-overlay", (e) => {
      if ($(e.target).hasClass("modal-overlay")) {
        this.closeModal();
      }
    });

    // Open profile form
    $(document).on("click", '[data-action="openProfile"]', () => {
      this.openModal("profile");
    });

    // Open password form
    $(document).on("click", '[data-action="openPassword"]', () => {
      this.openModal("password");
    });

    // Submit handlers within modal
    $(document).on("click", ".profile-update-form .btn-confirm", () => {
      $(".profile-update-form form").submit();
      this.closeModal();
    });

    $(document).on("click", ".password-update-form .btn-confirm", () => {
      $(".password-update-form form").submit();
      this.closeModal();
    });
  }

  openModal(formType) {
    this.$overlay.addClass("show");
    if (formType === "profile") {
      $(".profile-update-form").addClass("show").siblings().removeClass("show");
    } else if (formType === "password") {
      $(".password-update-form")
        .addClass("show")
        .siblings()
        .removeClass("show");
    }
  }

  closeModal() {
    this.$overlay.removeClass("show");
  }
}
