/**
 * Register Form - Focus Management
 */

document.addEventListener("livewire:initialized", () => {
    // Focus management
    Livewire.on("focus-name", () => {
        setTimeout(() => document.getElementById("name")?.focus(), 100);
    });
});
