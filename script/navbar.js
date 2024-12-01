// Function to load CSS
function loadCSS(filename) {
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = filename;
    document.head.appendChild(link);
}

// Function to load the navbar
function loadNavbar() {
    fetch("navbar.html")
        .then((response) => response.text())
        .then((data) => {
        document.getElementById("navbar-container").innerHTML = data;
        // Initialize your navbar functionality here if needed
        new Navbar();
    })
    .catch((error) => console.error("Error loading the navbar:", error));
}

// Load the navbar when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
    loadCSS("../css/navbar.css"); // Load your navbar CSS file
    loadNavbar(); // Load the navbar
}); 

class Navbar {
    constructor() {
        this.hamburger = document.getElementById("hamburger");
        this.closeBtn = document.getElementById("close-btn");
        this.navbarLinks = document.getElementById("navbar-links");
        this.profileLink = document.getElementById("profile-link");
        this.dropdownContent = document.getElementById("dropdown-content");

        this.init();
    }

    init() {
        this.hamburger.addEventListener("click", () => this.toggleNavbar());
        this.closeBtn.addEventListener("click", () => this.closeNavbar());
        this.profileLink.addEventListener("click", (event) => this.toggleDropdown(event));
        window.addEventListener("click", (event) => this.closeDropdown(event));
    }

    toggleNavbar() {
        this.navbarLinks.classList.toggle("active");
    }

    closeNavbar() {
        this.navbarLinks.classList.remove("active");
    }

    toggleDropdown(event) {
        event.preventDefault(); // Prevent default anchor click behavior
        this.dropdownContent.classList.toggle("show"); // Toggle dropdown visibility
    }

    closeDropdown(event) {
        if (!event.target.matches("#profile-link")) {
        this.dropdownContent.classList.remove("show"); // Hide dropdown
        }
    }
}
