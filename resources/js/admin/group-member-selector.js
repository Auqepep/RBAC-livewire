/**
 * Group Member Selector - Universal member checkbox and role selector management
 * Used by: admin group edit, user group edit pages
 *
 * Handles:
 * - Checkbox toggle to show/hide role selectors
 * - Role selection for members
 * - Moving members between current/available sections
 * - Search/filter users by name or email
 * - Form validation
 */

(function () {
    "use strict";

    console.log(
        "[GroupMemberSelector] Script loading... ReadyState:",
        document.readyState
    );

    let initialized = false;

    // Reset initialization on Livewire navigation
    document.addEventListener("livewire:navigating", function () {
        console.log(
            "[GroupMemberSelector] Livewire navigating, cleaning up..."
        );
        cleanup();
        initialized = false;
    });

    // Re-initialize after Livewire navigation completes
    document.addEventListener("livewire:navigated", function () {
        console.log(
            "[GroupMemberSelector] Livewire navigated, re-initializing..."
        );
        initialized = false;
        safeInit();
    });

    // Cleanup function to remove event listeners
    function cleanup() {
        document.body.removeEventListener("change", handleAnyChange, true);
        document.body.removeEventListener("click", handleAnyClick, true);
    }

    // Multiple initialization strategies for compatibility
    function safeInit() {
        if (initialized) {
            console.log(
                "[GroupMemberSelector] Already initialized, skipping..."
            );
            return;
        }

        // Check if required elements exist
        const memberRows = document.querySelectorAll(".member-row");
        const hasMemberRows = memberRows.length > 0;

        console.log(
            "[GroupMemberSelector] Found",
            memberRows.length,
            "member rows"
        );

        if (!hasMemberRows && document.readyState !== "complete") {
            console.log(
                "[GroupMemberSelector] No member rows yet, will retry after load..."
            );
            return;
        }

        initialized = true;
        init();
    }

    function init() {
        console.log("[GroupMemberSelector] Initializing event listeners...");

        // Use event delegation from document root for maximum compatibility
        document.body.addEventListener("change", handleAnyChange, true);
        document.body.addEventListener("click", handleAnyClick, true);

        // Setup search functionality
        setupSearch();

        // Setup form validation
        setupFormValidation();

        console.log("[GroupMemberSelector] ✅ Initialization complete");
    }

    // Strategy 1: Try immediately if DOM is ready
    if (document.readyState === "loading") {
        console.log(
            "[GroupMemberSelector] DOM still loading, waiting for DOMContentLoaded..."
        );
        document.addEventListener("DOMContentLoaded", safeInit);
    } else {
        console.log(
            "[GroupMemberSelector] DOM already loaded, initializing now..."
        );
        safeInit();
    }

    // Strategy 2: Fallback for window.load (ensures everything is loaded)
    window.addEventListener("load", safeInit);

    // Strategy 3: Retry after short delays (for dynamic content)
    setTimeout(safeInit, 50);
    setTimeout(safeInit, 100);
    setTimeout(safeInit, 250);
    setTimeout(safeInit, 500);
    setTimeout(safeInit, 1000);
    setTimeout(safeInit, 2000);

    // Strategy 4: Watch for DOM changes (for SPA-like navigation)
    const observer = new MutationObserver(function (mutations) {
        const hasMemberRows =
            document.querySelectorAll(".member-row").length > 0;
        if (hasMemberRows && !initialized) {
            console.log(
                "[GroupMemberSelector] Member rows detected via MutationObserver"
            );
            observer.disconnect();
            safeInit();
        }
    });

    // Start observing the document body for changes
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    // Stop observing after 10 seconds to prevent memory leaks
    setTimeout(() => observer.disconnect(), 10000);

    // Strategy 5: Watch for page visibility changes (tab switching)
    document.addEventListener("visibilitychange", function () {
        if (!document.hidden && !initialized) {
            console.log(
                "[GroupMemberSelector] Page became visible, trying to initialize..."
            );
            setTimeout(safeInit, 100);
        }
    });

    // Strategy 6: Try on first user interaction
    let interactionInitialized = false;
    const tryOnInteraction = function () {
        if (!interactionInitialized && !initialized) {
            interactionInitialized = true;
            console.log(
                "[GroupMemberSelector] User interaction detected, trying to initialize..."
            );
            safeInit();
        }
    };
    window.addEventListener("scroll", tryOnInteraction, { once: true });
    window.addEventListener("mousemove", tryOnInteraction, { once: true });
    window.addEventListener("click", tryOnInteraction, { once: true });

    /**
     * Handle any change event (event delegation)
     */
    function handleAnyChange(event) {
        const target = event.target;

        // Handle checkbox changes
        if (
            target.type === "checkbox" &&
            target.classList.contains("member-checkbox")
        ) {
            console.log(
                "[Checkbox] Changed:",
                target.checked,
                "User ID:",
                target.dataset.userId
            );
            handleCheckboxChange(target);
            return;
        }

        // Handle role select changes
        if (target.tagName === "SELECT" && target.closest(".role-selector")) {
            console.log("[RoleSelect] Changed:", target.value);
            handleRoleChange(target);
            return;
        }
    }

    /**
     * Handle any click event (backup for checkboxes)
     */
    function handleAnyClick(event) {
        const target = event.target;

        // Backup handler for checkbox clicks
        if (
            target.type === "checkbox" &&
            target.classList.contains("member-checkbox")
        ) {
            // Small delay to let change event fire first
            setTimeout(() => handleCheckboxChange(target), 50);
        }
    }

    /**
     * Handle checkbox change
     */
    function handleCheckboxChange(checkbox) {
        if (!checkbox) {
            console.warn("[Checkbox] No checkbox provided");
            return;
        }

        const memberRow = checkbox.closest(".member-row");
        if (!memberRow) {
            console.warn("[Checkbox] No member-row found");
            return;
        }

        const roleSelector = memberRow.querySelector(".role-selector");
        if (!roleSelector) {
            console.warn("[Checkbox] No role-selector found");
            return;
        }

        const selectElement = roleSelector.querySelector("select");
        const isChecked = checkbox.checked;

        console.log(
            "[Checkbox] Processing - Checked:",
            isChecked,
            "Has select:",
            !!selectElement
        );

        if (isChecked) {
            // Show role selector
            roleSelector.style.display = "block";
            if (selectElement) {
                selectElement.disabled = false;
                // Ensure a role is selected
                if (!selectElement.value) {
                    const firstOption = selectElement.querySelector(
                        'option:not([value=""])'
                    );
                    if (firstOption) {
                        selectElement.value = firstOption.value;
                    }
                }
            }
            moveToCurrentMembers(memberRow);
        } else {
            // Hide role selector
            roleSelector.style.display = "none";
            if (selectElement) {
                selectElement.disabled = true;
            }
            moveToAvailableMembers(memberRow);
        }
    }

    /**
     * Handle role select change
     */
    function handleRoleChange(select) {
        if (!select) return;

        const memberRow = select.closest(".member-row");
        if (!memberRow) return;

        console.log("[RoleSelect] Role changed to:", select.value);

        // Update any hidden inputs if present
        const hiddenInput = memberRow.querySelector(".role-value");
        if (hiddenInput) {
            hiddenInput.value = select.value;
        }
    }

    /**
     * Setup search functionality
     */
    function setupSearch() {
        const searchInput = document.getElementById("user-search");
        if (!searchInput) {
            console.log(
                "[Search] Search input not found - skipping search setup"
            );
            return;
        }

        console.log("[Search] Setting up search functionality");

        searchInput.addEventListener("input", function () {
            const searchTerm = this.value.toLowerCase().trim();
            filterUsers(searchTerm);
        });
    }

    /**
     * Filter users based on search term
     */
    function filterUsers(searchTerm) {
        // Search both .member-row (incoming/available) and .member-card (current members)
        const allRows = document.querySelectorAll(".member-row, .member-card");
        let visibleCount = 0;

        console.log(
            "[Search] Filtering",
            allRows.length,
            "rows with term:",
            searchTerm
        );

        allRows.forEach((row) => {
            // Get all text content from the row to search through
            const allText = row.textContent.toLowerCase();

            // Also try specific selectors for better debugging
            const userName =
                row
                    .querySelector(".font-semibold")
                    ?.textContent.toLowerCase()
                    .trim() || "";
            const userEmail =
                row
                    .querySelector(".text-gray-500")
                    ?.textContent.toLowerCase()
                    .trim() || "";

            console.log("[Search] Row text:", {
                userName,
                userEmail,
                searchTerm,
            });

            // Search in all text content (includes name, email, and other text)
            const matches = allText.includes(searchTerm);

            if (matches || searchTerm === "") {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        console.log("[Search] Visible rows:", visibleCount);

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
        if (!currentList) {
            console.log(
                "[Move] Current members list not found - skipping move"
            );
            return; // Not on edit page or single list mode
        }

        console.log("[Move] Moving to current members");

        // Update styling for current member
        row.classList.remove(
            "border-base-300",
            "border-dashed",
            "hover:border-primary"
        );
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
        if (!availableList) {
            console.log(
                "[Move] Available members list not found - skipping move"
            );
            return; // Not on edit page or single list mode
        }

        console.log("[Move] Moving to available members");

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

            // Find the badge in the header above this list
            const currentHeader =
                currentList.parentElement.previousElementSibling;
            const currentBadge = currentHeader?.querySelector(".badge");

            if (currentBadge) {
                currentBadge.textContent = `${currentCount} member(s)`;
                console.log("[Counter] Updated current members:", currentCount);
            }
        }

        if (availableList) {
            const availableCount =
                availableList.querySelectorAll(".member-row").length;

            // Find the badge in the header above this list
            const availableHeader =
                availableList.parentElement.previousElementSibling;
            const availableBadge = availableHeader?.querySelector(".badge");

            if (availableBadge) {
                availableBadge.textContent = `${availableCount} available`;
                console.log(
                    "[Counter] Updated available users:",
                    availableCount
                );
            }
        }
    }

    /**
     * Setup form validation
     */
    function setupFormValidation() {
        const form = document.querySelector('form[action*="groups"]');
        if (!form) {
            console.log(
                "[Validation] Form not found - skipping validation setup"
            );
            return;
        }

        console.log("[Validation] Setting up form validation");

        form.addEventListener("submit", function (event) {
            const checkedBoxes = form.querySelectorAll(
                ".member-checkbox:checked"
            );
            console.log("[Validation] Checked members:", checkedBoxes.length);

            // Validate that all checked members have a role selected
            let allValid = true;
            checkedBoxes.forEach((checkbox) => {
                const memberRow = checkbox.closest(".member-row");
                const roleSelect = memberRow?.querySelector(
                    ".role-selector select"
                );

                if (roleSelect && !roleSelect.value) {
                    allValid = false;
                    roleSelect.classList.add("select-error");
                    console.warn(
                        "[Validation] Missing role for user:",
                        checkbox.dataset.userId
                    );
                }
            });

            if (!allValid) {
                event.preventDefault();
                alert("Please select a role for all members.");
                return false;
            }

            console.log("[Validation] Form validation passed");
        });
    }
})();
