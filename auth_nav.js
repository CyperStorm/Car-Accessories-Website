document.addEventListener("DOMContentLoaded", function () {
    checkAuthStatus();
});

function checkAuthStatus() {
    fetch("session_status.php")
        .then(response => response.json())
        .then(data => {
            const loginBtn = document.getElementById("loginButton");
            const profileBtn = document.getElementById("profileButton");
            const logoutBtn = document.getElementById("logoutButton");

            // Changed data.logged_in to data.loggedIn
            if (data.loggedIn) {
                // User is logged in: Hide Login, Show Profile & Logout
                if (loginBtn) loginBtn.style.display = "none";
                if (profileBtn) profileBtn.style.display = "inline-flex";
                if (logoutBtn) logoutBtn.style.display = "inline-flex";
            } else {
                // User is logged out: Show Login, Hide Profile & Logout
                if (loginBtn) loginBtn.style.display = "inline-block";
                if (profileBtn) profileBtn.style.display = "none";
                if (logoutBtn) logoutBtn.style.display = "none";
            }
        })
        .catch(error => {
            console.error("Error verifying session status:", error);
        });
}

function logoutUser() {
    fetch("logout.php", { method: "POST" })
        .then(() => {
            window.location.href = "login.html";
        })
        .catch(error => {
            console.error("Error logging out:", error);
        });
}