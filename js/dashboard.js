function loadContent(page) {
    fetch(`pages/${page}.php`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Page not found");
            }
            return response.text();
        })
        .then(data => {
            document.getElementById("dashboardContent").innerHTML = data;
        })
        .catch(error => {
            document.getElementById("dashboardContent").innerHTML = `<p style="color:red;">Error loading page: ${error.message}</p>`;
        });
}

function logout() {
    alert("Logging out...");
    window.location.href = "logout.php"; // make sure this file exists
}
