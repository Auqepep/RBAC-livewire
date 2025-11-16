/**
 * Login Page - Livewire interactions
 * Handles OTP input, countdown, and focus management
 */

document.addEventListener("livewire:initialized", () => {
    // Focus management
    Livewire.on("focus-email", () => {
        setTimeout(() => document.getElementById("email")?.focus(), 100);
    });

    Livewire.on("focus-otp", () => {
        setTimeout(() => document.getElementById("otp")?.focus(), 100);
    });

    // Countdown timer
    let countdownInterval;

    Livewire.on("start-countdown", () => {
        if (countdownInterval) clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            window.Livewire.find(
                document.querySelector("[wire\\:id]").getAttribute("wire:id")
            ).call("decrementCountdown");
        }, 1000);
    });

    // Auto-format OTP input
    document.addEventListener("input", (e) => {
        if (e.target.id === "otp") {
            // Only allow digits
            e.target.value = e.target.value.replace(/\D/g, "");

            // Auto-submit when 6 digits are entered
            if (e.target.value.length === 6) {
                // Small delay to ensure Livewire model is synced
                setTimeout(() => {
                    const form = e.target.closest("form");
                    if (form) {
                        form.requestSubmit();
                    }
                }, 100);
            }
        }
    });
});
