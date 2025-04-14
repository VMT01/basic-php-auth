// TODO: REFACTOR THIS

$(document).ready(main);

function main() {
  handleLogoutButton();
  handleUploadAvatar();
  handleOverlayModal();
  handleUpdateProfile();
  handleUpdatePassword();
  toggleHidden();
}

function handleLogoutButton() {
  $(".btn.logout").on("click", () => {
    document.cookie =
      "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.location.href = "/login";
  });
}

function handleUploadAvatar() {
  $("div.uploadable img").on("click", () => {
    $("div.uploadable form input").click();
  });
  $("div.uploadable form input").on("change", function () {
    $("div.uploadable form").submit();
  });
}

function handleOverlayModal() {
  // Đóng modal khi /* nhấn */ vào nút đóng
  $(".close-modal").click(function () {
    $(".modal-overlay").css("display", "none");
  });

  // Đóng modal khi nhấn vào nút Hủy
  $(".btn-cancel").click(function () {
    $(".modal-overlay").css("display", "none");
  });

  // Đóng modal khi nhấn bên ngoài modal
  $(".modal-overlay").click(function (e) {
    if ($(e.target).hasClass("modal-overlay")) {
      $(this).css("display", "none");
    }
  });
}

function handleUpdateProfile() {
  function toggle() {
    $(".modal-overlay").css("display", "flex");
    $(".modal-body .profile-update-form").css("display", "inline");
    $(".modal-body .password-update-form").css("display", "none");
  }

  $(".header button").click(toggle);
  $(".side-navbar .box li").eq(1).click(toggle);
  $(".profile-update-form .btn-confirm").click(function () {
    $(".modal-overlay .modal-body .profile-update-form form").submit();
    $(".modal-overlay").css("display", "none");
  });
}

function handleUpdatePassword() {
  function toggle() {
    $(".modal-overlay").css("display", "flex");
    $(".modal-body .profile-update-form").css("display", "none");
    $(".modal-body .password-update-form").css("display", "inline");
  }

  $(".side-navbar .box li").eq(2).click(toggle);
  $(".password-update-form .btn-confirm").click(function () {
    $(".modal-overlay .modal-body .password-update-form form").submit();
    $(".modal-overlay").css("display", "none");
  });
}

function toggleHidden() {
  $(".toggle-hidden").on("click", function () {
    if ($(this).hasClass("fa-eye-slash")) {
      $(this).removeClass("fa-eye-slash").addClass("fa-eye");
      $(this).siblings("input").attr("type", "text");
    } else {
      $(this).removeClass("fa-eye").addClass("fa-eye-slash");
      $(this).siblings("input").attr("type", "password");
    }
  });
}
