/**
 * OTP Manager Component - Extended OTP functionality with email management
 * Used by: otp-manager.blade.php
 */

document.addEventListener("livewire:initialized", () => {
    // Focus management for email field
    Livewire.on("focus-email", () => {
        setTimeout(() => document.getElementById("email")?.focus(), 100);
    });

    // Focus management for OTP field
    Livewire.on("focus-otp", () => {
        setTimeout(() => document.getElementById("otp")?.focus(), 100);
    });

    // Countdown timer
    let countdownInterval;

    Livewire.on("start-countdown", () => {
        if (countdownInterval) clearInterval(countdownInterval);

        countdownInterval = setInterval(() => {
            // Find the Livewire component and call decrementCountdown
            const livewireElement = document.querySelector("[wire\\:id]");
            if (livewireElement) {
                window.Livewire.find(
                    livewireElement.getAttribute("wire:id")
                ).call("decrementCountdown");
            }
        }, 1000);
    });

    // Auto-format OTP input - only allow digits
    document.addEventListener("input", (e) => {
        if (e.target.id === "otp") {
            e.target.value = e.target.value.replace(/\D/g, "");
        }
    });
});
