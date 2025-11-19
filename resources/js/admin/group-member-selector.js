/**
 * Admin Group Member Selector - Reusable member checkbox and role selector management
 * Used by: group create and group edit pages
 *
 * Handles:
 * - Checkbox toggle to show/hide role selectors
 * - Role selection for members
 * - Moving members between current/available sections (edit page)
 * - Search/filter users by name or email
 */

document.addEventListener("DOMContentLoaded", function () {
    // Handle checkbox changes to show/hide role selectors
    // Support both wrapped checkboxes (.member-checkbox input) and direct checkboxes (.member-checkbox)
    const checkboxes = document.querySelectorAll(
        '.member-checkbox input[type="checkbox"], input.member-checkbox[type="checkbox"]'
    );

    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            const memberRow = this.closest(".member-row");
            if (!memberRow) return;

            const roleSelector = memberRow.querySelector(".role-selector");
            if (!roleSelector) return;

            // Support both .role-select and generic select elements
            const selectElement =
                roleSelector.querySelector(".role-select") ||
                roleSelector.querySelector("select");

            if (this.checked) {
                roleSelector.style.display = "block";
                if (selectElement) selectElement.disabled = false;

                // Move to current members section if it exists (edit page)
                moveToCurrentMembers(memberRow);
            } else {
                roleSelector.style.display = "none";
                if (selectElement) selectElement.disabled = true;

                // Move to available members section if it exists (edit page)
                moveToAvailableMembers(memberRow);
            }
        });
    });

    // Update hidden input when role changes (if hidden input exists)
    const roleSelects = document.querySelectorAll(
        ".role-select, .role-selector select"
    );
    roleSelects.forEach((select) => {
        select.addEventListener("change", function () {
            const memberRow = this.closest(".member-row");
            if (!memberRow) return;

            const hiddenInput = memberRow.querySelector(".role-value");
            if (hiddenInput) {
                hiddenInput.value = this.value;
            }
        });
    });

    // User search functionality
    const searchInput = document.getElementById("user-search");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const searchTerm = this.value.toLowerCase().trim();
            filterUsers(searchTerm);
        });
    }

    /**
     * Filter users based on search term
     */
    function filterUsers(searchTerm) {
        const allRows = document.querySelectorAll(".member-row");
        let visibleCount = 0;

        allRows.forEach((row) => {
            const label = row.querySelector("label");
            if (!label) return;

            const userName =
                label
                    .querySelector(".font-semibold")
                    ?.textContent.toLowerCase() || "";
            const userEmail =
                label
                    .querySelector(".text-gray-500")
                    ?.textContent.toLowerCase() || "";

            const matches =
                userName.includes(searchTerm) || userEmail.includes(searchTerm);

            if (matches || searchTerm === "") {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        // Show "no results" message if needed
        showNoResultsMessage(visibleCount === 0 && searchTerm !== "");
    }

    /**
     * Show/hide no results message
     */
    function showNoResultsMessage(show) {
        let noResultsMsg = document.getElementById("no-results-message");

        if (show && !noResultsMsg) {
            // Create message
            noResultsMsg = document.createElement("div");
            noResultsMsg.id = "no-results-message";
            noResultsMsg.className = "text-center py-8 text-gray-500";
            noResultsMsg.innerHTML = `
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-lg font-medium">No users found</p>
                <p class="text-sm mt-1">Try a different search term</p>
            `;

            // Insert into appropriate list
            const currentList = document.getElementById("current-members-list");
            const availableList = document.getElementById(
                "available-members-list"
            );
            const membersList = document.getElementById("group-members-list");

            if (availableList) {
                availableList.appendChild(noResultsMsg);
            } else if (currentList) {
                currentList.appendChild(noResultsMsg);
            } else if (membersList) {
                membersList.appendChild(noResultsMsg);
            }
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    /**
     * Move member row to current members section
     */
    function moveToCurrentMembers(row) {
        const currentList = document.getElementById("current-members-list");
        if (!currentList) return; // Not on edit page

        // Update styling for current member
        row.classList.remove("border-base-300", "hover:border-primary");
        row.classList.add("border-success", "hover:shadow-lg");

        const checkbox = row.querySelector(".member-checkbox");
        if (checkbox) {
            checkbox.classList.remove("checkbox-primary");
            checkbox.classList.add("checkbox-success");
        }

        // Move to current members list
        currentList.appendChild(row);
        updateCounters();
    }

    /**
     * Move member row to available members section
     */
    function moveToAvailableMembers(row) {
        const availableList = document.getElementById("available-members-list");
        if (!availableList) return; // Not on edit page

        // Update styling for available member
        row.classList.remove("border-success", "hover:shadow-lg");
        row.classList.add("border-base-300", "hover:border-primary");

        const checkbox = row.querySelector(".member-checkbox");
        if (checkbox) {
            checkbox.classList.remove("checkbox-success");
            checkbox.classList.add("checkbox-primary");
        }

        // Move to available members list
        availableList.appendChild(row);
        updateCounters();
    }

    /**
     * Update member counters
     */
    function updateCounters() {
        const currentList = document.getElementById("current-members-list");
        const availableList = document.getElementById("available-members-list");

        if (currentList) {
            const currentCount =
                currentList.querySelectorAll(".member-row").length;
            const currentBadge = document
                .querySelector("#current-members-list")
                ?.closest("div")
                ?.previousElementSibling?.querySelector(".badge");
            if (currentBadge) {
                currentBadge.textContent = `${currentCount} member(s)`;
            }
        }

        if (availableList) {
            const availableCount =
                availableList.querySelectorAll(".member-row").length;
            const availableBadge = document
                .querySelector("#available-members-list")
                ?.closest("div")
                ?.previousElementSibling?.querySelector(".badge");
            if (availableBadge) {
                availableBadge.textContent = `${availableCount} available`;
            }
        }
    }
});
