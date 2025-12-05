/**
 * OTP Common - Reusable OTP functionality
 * Used by: login, otp-verification, otp-manager, and other OTP components
 *
 * Features:
 * - Focus management (email, otp fields)
 * - Countdown timer
 * - Auto-format (digits only)
 * - Auto-submit on 6 digits (optional, based on data attribute)
 */

document.addEventListener("livewire:initialized", () => {
    // Focus management - email field
    Livewire.on("focus-email", () => {
        setTimeout(() => document.getElementById("email")?.focus(), 100);
    });

    // Focus management - OTP field
    Livewire.on("focus-otp", () => {
        setTimeout(() => document.getElementById("otp")?.focus(), 100);
    });

    // Focus management - name field (for register)
    Livewire.on("focus-name", () => {
        setTimeout(() => document.getElementById("name")?.focus(), 100);
    });

    // Countdown timer
    let countdownInterval;

    Livewire.on("start-countdown", () => {
        if (countdownInterval) clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            const livewireElement = document.querySelector("[wire\\:id]");
            if (livewireElement) {
                window.Livewire.find(
                    livewireElement.getAttribute("wire:id")
                ).call("decrementCountdown");
            }
        }, 1000);
    });

    // Auto-format OTP input and optional auto-submit
    document.addEventListener("input", (e) => {
        if (e.target.id === "otp") {
            // Only allow digits
            e.target.value = e.target.value.replace(/\D/g, "");

            // Auto-submit when 6 digits are entered (only if form has data-auto-submit attribute)
            if (e.target.value.length === 6) {
                const form = e.target.closest("form");
                if (form && form.hasAttribute("data-auto-submit")) {
                    // Small delay to ensure Livewire model is synced
                    setTimeout(() => {
                        form.requestSubmit();
                    }, 100);
                }
            }
        }
    });
});
