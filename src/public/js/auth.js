$(document).ready(main);

function main() {
  toggleHidden();
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
