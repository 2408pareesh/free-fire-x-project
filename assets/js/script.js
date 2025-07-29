function togglePassword() {
    const passwordField = document.getElementById("password");
    const icon = document.querySelector(".toggle-password i");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        passwordField.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

    window.addEventListener("load", function () {
        const preloader = document.getElementById("preloader");
        setTimeout(() => {
            preloader.style.opacity = "0";
            preloader.style.transition = "opacity 0.5s ease";
            setTimeout(() => {
                preloader.style.display = "none";
            }, 500); // Matches the fade duration
        }, 1500); // 1.5 second delay after page is fully loaded
    });

