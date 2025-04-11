// Initialize Bootstrap tooltips
document.addEventListener("DOMContentLoaded", function () {
  // Enable tooltips
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Auto-focus on first input in forms
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    const firstInput = form.querySelector("input, textarea, select");
    if (firstInput) {
      firstInput.focus();
    }
  });

  // Confirm before delete actions
  const deleteButtons = document.querySelectorAll(".delete-confirm");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      if (!confirm("Are you sure you want to delete this?")) {
        e.preventDefault();
      }
    });
  });

  // Initialize date pickers with current date as default
  const dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach((input) => {
    if (!input.value) {
      input.valueAsDate = new Date();
    }
  });

  // Toggle password visibility
  const togglePasswordButtons = document.querySelectorAll(".toggle-password");
  togglePasswordButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const input = this.previousElementSibling;
      const icon = this.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    });
  });
});
