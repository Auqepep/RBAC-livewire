/**
 * Permission Testing - Real-time permission checking and UI updates
 * Used by: test/permissions.blade.php
 */

let autoRefreshInterval = null;
let refreshIntervalSeconds = 10;

// Test a single permission
function testSinglePermission(permissionName) {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;
    const testUrl = document.body.getAttribute("data-test-permission-url");

    if (!testUrl) {
        console.error("Test permission URL not found");
        return;
    }

    fetch(testUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({ permission: permissionName }),
    })
        .then((response) => response.json())
        .then((data) => {
            updatePermissionStatus(permissionName, data.allowed);
            showNotification(
                `Permission "${permissionName}": ${
                    data.allowed ? "ALLOWED" : "DENIED"
                }`,
                data.allowed ? "success" : "error"
            );
        })
        .catch((error) => {
            console.error("Error testing permission:", error);
            showNotification("Error testing permission", "error");
        });
}

// Refresh all permissions
function refreshPermissions() {
    const permissions = document.querySelectorAll("[data-permission]");
    const permissionNames = Array.from(permissions).map(
        (el) => el.dataset.permission
    );
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;
    const testUrl = document.body.getAttribute("data-test-permission-url");

    if (!testUrl) {
        console.error("Test permission URL not found");
        return;
    }

    // Show loading state
    const refreshBtn = document.getElementById("refresh-permissions");
    if (refreshBtn) {
        refreshBtn.innerHTML =
            '<span class="loading loading-spinner loading-sm"></span> Refreshing...';
    }

    // Test each permission
    Promise.all(
        permissionNames.map((permission) =>
            fetch(testUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ permission }),
            }).then((r) => r.json())
        )
    )
        .then((results) => {
            results.forEach((data) => {
                updatePermissionStatus(data.permission, data.allowed);
            });

            // Update last updated time
            const lastUpdated = document.getElementById("last-updated");
            if (lastUpdated) {
                lastUpdated.textContent = new Date().toLocaleTimeString();
            }

            // Reset button
            if (refreshBtn) {
                refreshBtn.innerHTML =
                    '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Refresh';
            }

            showNotification(
                `Refreshed ${results.length} permissions`,
                "success"
            );
        })
        .catch((error) => {
            console.error("Error refreshing permissions:", error);
            showNotification("Error refreshing permissions", "error");
            if (refreshBtn) {
                refreshBtn.innerHTML =
                    '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Refresh';
            }
        });
}

// Update permission status in UI
function updatePermissionStatus(permissionName, allowed) {
    const statusElement = document.getElementById(`status-${permissionName}`);
    const itemElement = document.getElementById(`permission-${permissionName}`);

    if (statusElement && itemElement) {
        if (allowed) {
            statusElement.innerHTML =
                '<span class="badge badge-success">✓ Allowed</span>';
            itemElement.className =
                itemElement.className
                    .replace("border-red-200 bg-red-50", "")
                    .replace("border-gray-200", "") +
                " border-green-200 bg-green-50";
        } else {
            statusElement.innerHTML =
                '<span class="badge badge-error">✗ Denied</span>';
            itemElement.className =
                itemElement.className
                    .replace("border-green-200 bg-green-50", "")
                    .replace("border-gray-200", "") +
                " border-red-200 bg-red-50";
        }
    }
}

// Toggle auto-refresh
function toggleAutoRefresh() {
    const toggle = document.getElementById("auto-refresh-toggle");

    if (toggle && toggle.checked) {
        autoRefreshInterval = setInterval(
            refreshPermissions,
            refreshIntervalSeconds * 1000
        );
        showNotification(
            `Auto-refresh enabled (every ${refreshIntervalSeconds}s)`,
            "success"
        );
    } else {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        showNotification("Auto-refresh disabled", "info");
    }
}

// Show notification
function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement("div");
    notification.className = `alert ${
        type === "success"
            ? "alert-success"
            : type === "error"
            ? "alert-error"
            : "alert-info"
    } fixed top-4 right-4 z-50 w-auto max-w-sm shadow-lg`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-lg">&times;</button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 3000);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
    // Add CSRF token to meta if not present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const csrfTokenMeta = document.querySelector('input[name="_token"]');
        if (csrfTokenMeta) {
            const meta = document.createElement("meta");
            meta.name = "csrf-token";
            meta.content = csrfTokenMeta.value;
            document.head.appendChild(meta);
        }
    }
});

// Make functions globally available
window.testSinglePermission = testSinglePermission;
window.refreshPermissions = refreshPermissions;
window.toggleAutoRefresh = toggleAutoRefresh;
