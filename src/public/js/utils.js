function showFlashMessage(message, type, duration = 3000) {
  const flashMessage = document.createElement("div");
  flashMessage.textContent = message;
  flashMessage.style.position = "absolute";
  flashMessage.style.top = "20px";
  flashMessage.style.left = "50%";
  flashMessage.style.transform = "translateX(-50%)";
  flashMessage.style.borderRadius = "3px";
  flashMessage.style.fontSize = "16px";
  flashMessage.style.color = "#fff";
  flashMessage.style.padding = "12px 24px";
  flashMessage.style.zIndex = "9999";
  flashMessage.style.transition = "opacity 0.3s ease-in-out";
  flashMessage.style.boxShadow = "0 4px 6px rgba(0, 0, 0, 0.1)";
  flashMessage.style.backgroundColor =
    type === "success" ? "#4CAF50" : "#F44336";
  document.body.appendChild(flashMessage);

  setTimeout(() => {
    flashMessage.style.opacity = "1";
  }, 10);

  setTimeout(() => {
    flashMessage.style.opacity = "0";
    setTimeout(() => document.body.removeChild(flashMessage), 300);
  }, duration);
}
