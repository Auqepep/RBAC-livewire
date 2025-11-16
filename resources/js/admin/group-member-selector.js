/**
 * Admin Group Member Selector - Reusable member checkbox and role selector management
 * Used by: group create and group edit pages
 *
 * Handles:
 * - Checkbox toggle to show/hide role selectors
 * - Role selection for members
 */

document.addEventListener("DOMContentLoaded", function () {
    // Handle checkbox changes to show/hide role selectors
    document
        .querySelectorAll('.member-checkbox input[type="checkbox"]')
        .forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                const memberRow = this.closest(".member-row");
                const roleSelector = memberRow.querySelector(".role-selector");
                // Support both .role-select and generic select elements
                const selectElement =
                    roleSelector.querySelector(".role-select") ||
                    roleSelector.querySelector("select");

                if (this.checked) {
                    roleSelector.style.display = "block";
                    selectElement.disabled = false;
                } else {
                    roleSelector.style.display = "none";
                    selectElement.disabled = true;
                }
            });
        });

    // Update hidden input when role changes (if hidden input exists)
    document.querySelectorAll(".role-select").forEach((select) => {
        select.addEventListener("change", function () {
            const memberRow = this.closest(".member-row");
            const hiddenInput = memberRow.querySelector(".role-value");
            if (hiddenInput) {
                hiddenInput.value = this.value;
            }
        });
    });
});
