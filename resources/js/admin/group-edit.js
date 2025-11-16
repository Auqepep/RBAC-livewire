/**
 * Admin Group Edit - Member checkbox and role selector management
 */

document.addEventListener("DOMContentLoaded", function () {
    // Handle checkbox changes to show/hide role selectors
    document
        .querySelectorAll('.member-checkbox input[type="checkbox"]')
        .forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                const memberRow = this.closest(".member-row");
                const roleSelector = memberRow.querySelector(".role-selector");
                const selectElement = roleSelector.querySelector("select");

                if (this.checked) {
                    roleSelector.style.display = "block";
                    selectElement.disabled = false;
                } else {
                    roleSelector.style.display = "none";
                    selectElement.disabled = true;
                }
            });
        });
});
