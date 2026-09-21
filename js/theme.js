document.addEventListener("DOMContentLoaded", function() {
    var toggleBtn = document.getElementById("theme-toggle");
    var themeIcon = document.getElementById("theme-icon");
    var savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.body.className = "dark-mode";
        themeIcon.src = "../images/sun.png";
    }

    toggleBtn.onclick = function() {
        var isDark = document.body.className === "dark-mode";
        document.body.className = isDark ? "" : "dark-mode";
        themeIcon.src = isDark ? "../images/moon.png" : "../images/sun.png";
        localStorage.setItem("theme", isDark ? "light" : "dark");
    };
});
