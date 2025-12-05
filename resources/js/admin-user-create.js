/**
 * Admin User Creation - Dynamic Group/Role Assignment
 */

document.addEventListener("DOMContentLoaded", function () {
    let assignmentIndex = 0;
    let groupRoles = {};

    // Get group roles data from the page
    const groupRolesElement = document.getElementById("group-roles-data");
    if (groupRolesElement) {
        try {
            groupRoles = JSON.parse(groupRolesElement.textContent);
        } catch (e) {
            console.error("Failed to parse group roles data:", e);
        }
    }

    // Add new assignment row
    const addButton = document.getElementById("add-assignment");
    if (addButton) {
        addButton.addEventListener("click", function () {
            assignmentIndex++;
            const container = document.getElementById("group-assignments");
            const newRow = createAssignmentRow(assignmentIndex);
            container.appendChild(newRow);
            setupRowEventListeners(newRow, assignmentIndex);
        });
    }

    // Setup initial row
    const initialRow = document.querySelector(".group-assignment-row");
    if (initialRow) {
        setupRowEventListeners(initialRow, 0);
    }

    /**
     * Create a new assignment row HTML element
     */
    function createAssignmentRow(index) {
        const row = document.createElement("div");
        row.className =
            "group-assignment-row grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white rounded-lg border";

        // Get groups for the select options
        const groupsElement = document.getElementById("groups-data");
        let groupsHTML = '<option value="">Select a group</option>';

        if (groupsElement) {
            try {
                const groups = JSON.parse(groupsElement.textContent);
                groups.forEach((group) => {
                    groupsHTML += `<option value="${group.id}">${group.name}</option>`;
                });
            } catch (e) {
                console.error("Failed to parse groups data:", e);
            }
        }

        row.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                <select name="group_assignments[${index}][group_id]" class="group-select select select-bordered w-full">
                    ${groupsHTML}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role in Group</label>
                <select name="group_assignments[${index}][role_id]" class="role-select select select-bordered w-full" disabled>
                    <option value="">Select a role</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="button" class="remove-assignment btn btn-error btn-outline w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Remove
                </button>
            </div>
        `;
        return row;
    }

    /**
     * Setup event listeners for a row
     */
    function setupRowEventListeners(row, index) {
        const groupSelect = row.querySelector(".group-select");
        const roleSelect = row.querySelector(".role-select");
        const removeBtn = row.querySelector(".remove-assignment");

        if (!groupSelect || !roleSelect) {
            console.error("Could not find select elements in row");
            return;
        }

        // Group selection changes role options
        groupSelect.addEventListener("change", function () {
            const groupId = this.value;
            console.log("Group selected:", groupId); // Debug log

            roleSelect.innerHTML = '<option value="">Select a role</option>';

            if (groupId && groupRoles[groupId]) {
                roleSelect.disabled = false;
                console.log("Roles for group:", groupRoles[groupId]); // Debug log

                groupRoles[groupId].forEach((role) => {
                    const option = document.createElement("option");
                    option.value = role.id;
                    option.textContent = role.display_name || role.name;
                    roleSelect.appendChild(option);
                });
            } else {
                roleSelect.disabled = true;
                if (!groupId) {
                    console.log("No group selected");
                } else {
                    console.log("No roles found for group:", groupId);
                }
            }
        });

        // Remove row
        if (removeBtn) {
            removeBtn.addEventListener("click", function () {
                const allRows = document.querySelectorAll(
                    ".group-assignment-row"
                );
                if (allRows.length > 1) {
                    row.remove();
                } else {
                    alert("At least one group assignment is required.");
                }
            });
        }
    }
});
