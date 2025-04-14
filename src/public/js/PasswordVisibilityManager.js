class PasswordVisibilityManager {
  /**
   * @param {Object} options - Configuration options
   * @param {string} options.toggleSelector - jQuery selector for toggle buttons
   * @param {string} options.visibleIconClass - Icon class for visible state
   * @param {string} options.hiddenIconClass - Icon class for hidden state
   */
  constructor(options = {}) {
    this.options = $.extend(
      {
        toggleSelector: ".toggle-hidden",
        visibleIconClass: "fa-eye",
        hiddenIconClass: "fa-eye-slash",
      },
      options,
    );
    this.$toggleButtons = $();
    this.initialized = false;
  }

  /**
   * Initialize the password visibility manager
   */
  init() {
    if (this.initialized) return this;

    this.$toggleButtons = $(this.options.toggleSelector);
    this.addEventListeners();
    this.initialized = true;

    return this;
  }

  /**
   * Add event listeners to toggle buttons
   * @private
   */
  addEventListeners() {
    this.$toggleButtons.on("click", this.handleToggleClick.bind(this));
  }

  /**
   * Handle toggle button click event
   * @param {Event} event - Click event
   * @private
   */
  handleToggleClick(event) {
    const $toggleButton = $(event.currentTarget);
    const $inputField = this.findAssociatedInput($toggleButton);

    if (!$inputField.length) {
      console.error("No input field found for toggle button", $toggleButton);
      return;
    }

    this.toggleVisibility($toggleButton, $inputField);
  }

  /**
   * Find the input field associated with a toggle button
   * @param {jQuery} $toggleButton - The toggle button jQuery element
   * @returns {jQuery} - The associated input field
   * @private
   */
  findAssociatedInput($toggleButton) {
    // First try to find a sibling input
    let $input = $toggleButton.siblings("input");

    // If not found, try to find input using data-target attribute
    if (!$input.length && $toggleButton.data("target")) {
      $input = $($toggleButton.data("target"));
    }

    return $input;
  }

  /**
   * Toggle the visibility of a password field
   * @param {jQuery} $toggleButton - The toggle button jQuery element
   * @param {jQuery} $inputField - The input field jQuery element
   * @private
   */
  toggleVisibility($toggleButton, $inputField) {
    const isVisible = $inputField.attr("type") === "text";

    if (isVisible) {
      // Hide password
      $toggleButton
        .removeClass(this.options.visibleIconClass)
        .addClass(this.options.hiddenIconClass)
        .attr("aria-label", "Show password");
      $inputField.attr("type", "password");
    } else {
      // Show password
      $toggleButton
        .removeClass(this.options.hiddenIconClass)
        .addClass(this.options.visibleIconClass)
        .attr("aria-label", "Hide password");
      $inputField.attr("type", "text");
    }

    // Trigger a custom event for any additional listeners
    $(document).trigger("passwordVisibilityChanged", {
      isVisible: !isVisible,
      field: $inputField,
    });
  }

  /**
   * Destroy all event listeners
   */
  destroy() {
    if (this.$toggleButtons.length) {
      this.$toggleButtons.off("click", this.handleToggleClick.bind(this));
    }
    this.initialized = false;
  }
}
