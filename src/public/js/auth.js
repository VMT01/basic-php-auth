$(document).ready(function () {
  const passwordManager = new PasswordVisibilityManager();
  passwordManager.init();

  const modal = new OverlayModal();
  modal.init();
});
