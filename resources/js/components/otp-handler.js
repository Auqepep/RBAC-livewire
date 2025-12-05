/**
 * OTP Component - Reusable OTP verification logic
 * Used by: otp-verification.blade.php, otp-manager.blade.php, and other OTP components
 */

document.addEventListener("livewire:initialized", () => {
    // Focus management
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

    // Auto-format OTP input - only digits
    document.addEventListener("input", (e) => {
        if (e.target.id === "otp") {
            e.target.value = e.target.value.replace(/\D/g, "");
        }
    });
});
