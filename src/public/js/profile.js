$(document).ready(function () {
  const passwordManager = new PasswordVisibilityManager();
  passwordManager.init();

  const modal = new OverlayModal();
  modal.init();

  // Handle upload avatar
  $("div.uploadable img").on("click", function () {
    $("div.uploadable form input").click();
  });
  $("div.uploadable form input").on("change", function () {
    $("div.uploadable form").submit();
  });

  // Handle logout
  $(".btn.logout").on("click", () => {
    document.cookie =
      "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.location.href = "/login";
  });
});
