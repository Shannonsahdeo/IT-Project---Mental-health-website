<?php

session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use session if available
} elseif (isset($_COOKIE['username'])) {
    $username = $_COOKIE['username']; // Use cookie if session doesn't exist
} else {
    $username = "Guest"; // Fallback for anonymous access
}



// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_unset();
  session_destroy();
  setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
  header("Location: loginform.php"); // Redirect to login page
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge Locker - Mental Health Journey</title>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #9ef3b9, #6ae992, #6ae9b8, #58d5b1);
            color: #333;
            text-align: center;
            overflow: hidden; /* Prevent scrollbars */
			height: 100vh;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .filter-container {
            margin: 20px 0;
            animation: slideIn 1s ease-in-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        select {
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #fff;
            color: #333;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        select:hover {
            background-color: #f0f0f0;
        }
        .badge-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            animation: bounceIn 1s ease-in-out;
        }
        @keyframes bounceIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        .badge {
            width: 120px;
            height: 120px;
            background-color: rgba(255, 255, 255, 0.7);
            border: 2px solid #ff6f61;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.5s ease-in-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .badge:hover {
            transform: scale(1.1);
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }
        .badge.locked {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .badge img {
            width: 70%;
            height: auto;
            margin-bottom: 5px;
            transition: transform 0.3s;
        }
        .badge img:hover {
            transform: rotate(15deg) scale(1.1);
        }
        .badge-title {
            font-weight: bold;
            font-size: 1.1em;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        .trophy-wall {
            margin-top: 40px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            border: 2px dashed #ff6f61;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }
        .selected-badge {
            display: inline-block;
            margin: 10px;
            padding: 5px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            transition: transform 0.3s, background-color 0.3s;
            animation: bounce 0.5s ease-in-out;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        .selected-badge:hover {
            transform: scale(1.05);
            background-color: rgba(255, 255, 255, 0.7);
        }
        
        .tooltip {
            position: absolute;
            bottom: 130%; /* Position above the badge */
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ff6f61;
            border-radius: 5px;
            padding: 10px;
            display: none;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .progress-container {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-top: 5px;
        }
        .progress-bar {
            height: 10px;
            background-color: #ff6f61;
            border-radius: 5px;
            width: 0; /* Will be set dynamically */
            transition: width 0.3s;
        }
		
		/* Menu styles */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar {
            padding: 10px;
        }

        .menu-toggle {
            color: black;
            border: none;
            cursor: pointer;
            font-size: 20px;
            border-radius: 7px;
			margin-right: 1470px;
        }

        .fancy-menu {
            display: none;
            background-color: #6CB4EE;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 220px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 15px 0 0 15px;
            padding-top: 20px;
            transition: transform 0.3s ease;
            transform: translateX(-220px);
        }

        .fancy-menu.show {
            display: block;
            transform: translateX(0);
        }

        .content {
            transition: margin-left 0.3s ease;
        }

        .menu-open .content {
            margin-left: 220px;
        }

        .fancy-menu h1 {
            margin: 0;
            padding: 10px;
            color: white;
            font-size: 1.5em;
            text-align: center;
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
        }

        .fancy-menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .fancy-menu li {
            padding: 10px 20px;
        }

        .fancy-menu a {
            color: white;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .fancy-menu a:hover {
            color: grey;
            transform: translateX(5px);
        }

        .close-menu {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 20px;
            position: absolute;
            top: 7px;
            right: 180px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-menu:hover {
            color: grey;
        }
    </style>
</head>
<body>
<header>
        <nav class="navbar">
            <button class="menu-toggle">☰</button>
            <div class="fancy-menu">
                <h1>Dashboard</h1>
                <button class="close-menu">✖</button>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../userprofile.php">Profile</a></li>
                    <li><a href="../tasks/tasks_1.php">Tasks</a></li>
                    <li><a href="../journal_final/journal.php">Journal</a></li>
                    <li><a href="../breathe.php">Zen Zone</a></li>
                    <li><a href="../subscriptions/doctor.php">Subscription</a></li>
                    
                    <li><a href="../contacts/contacts_index.php">Emergency Contacts</a></li>
                </ul>
            </div>
        </nav>
    </header>
	<div class = "content">

    <h1 style = "color: #0e5066;">Badge Locker - Your Mental Health Journey</h1>

    <div class="filter-container">
        <label for="filter">Filter Badges: </label>
        <select id="filter" onchange="filterBadges()">
            <option value="all">All Badges</option>
            <option value="unlocked">Unlocked Badges</option>
            <option value="locked">Locked Badges</option>
        </select>
    </div>

    <div class="badge-container" id="badgeContainer">
        <!-- Badges will be dynamically generated here -->
    </div>

    <div class="trophy-wall" id="trophyWall">
        <h2 style = "color: #0e5066;">Your Trophy Wall</h2>
        <div id="selectedBadges"></div>
    </div>

    
	</div>
	
	<script>
	// Get the button and the menu
        const menuToggle = document.querySelector('.menu-toggle');
        const fancyMenu = document.querySelector('.fancy-menu');
        const closeMenuButton = document.querySelector('.close-menu');
        const body = document.querySelector('body');

        // Toggle the menu display when the button is clicked
        menuToggle.onclick = function() {
            fancyMenu.classList.toggle('show');
            body.classList.toggle('menu-open');
        };

        // Close the menu when the close button is clicked
        closeMenuButton.onclick = function() {
            fancyMenu.classList.remove('show');
            body.classList.remove('menu-open');
        };
	</script>

    <!-- Inline JavaScript -->
    <script>
       let badges = [
  { 
    id: 1, 
    title: 'Novice', 
    unlocked: true, 
    criteria: 'Login 1 day', 
    dateUnlocked: '2024-01-01', 
    image: "level1.png", 
    progress: 100 
  },
  { 
    id: 2, 
    title: 'Intermediate', 
    unlocked: false, 
    criteria: 'Login 7 days', 
    dateUnlocked: null, 
    image: "level2.png", 
    progress: 3 
  },
  { 
    id: 3, 
    title: 'Expert', 
    unlocked: false, 
    criteria: 'Reach Level 10', 
    dateUnlocked: null, 
    image: "level3.png", 
    progress: 5 
  },
  { 
    id: 4, 
    title: 'Master', 
    unlocked: false, 
    criteria: 'Complete 10 tasks', 
    dateUnlocked: null, 
    image: "level4.png", 
    progress: 2 
  }
];


        const badgeContainer = document.getElementById('badgeContainer');
        const trophyWall = document.getElementById('selectedBadges');

        // Function to render badges
        function renderBadges(filter = 'all') {
            badgeContainer.innerHTML = ''; // Clear existing badges
            badges.forEach(badge => {
                if ((filter === 'unlocked' && !badge.unlocked) || (filter === 'locked' && badge.unlocked)) {
                    return; // Skip badges based on filter
                }

                const badgeElement = document.createElement('div');
                badgeElement.className = `badge ${badge.unlocked ? '' : 'locked'}`;
                badgeElement.innerHTML = `
                    <img src="${badge.image}" alt="${badge.title}">
                    <div class="badge-title">${badge.title}</div>
                    <div class="tooltip" id="tooltip-${badge.id}">
                        <div>Criteria: ${badge.criteria}</div>
                        <div class="progress-container">
                            <div class="progress-bar" id="progressBar-${badge.id}" style="width: ${badge.progress / 7 * 100}%;"></div>
                        </div>
                    </div>
                `;
                badgeElement.onmouseover = () => showTooltip(badge.id);
                badgeElement.onmouseout = () => hideTooltip(badge.id);
                badgeElement.onclick = () => toggleBadgeSelection(badge);
                badgeContainer.appendChild(badgeElement);
            });
        }

        // Function to show tooltip
        function showTooltip(id) {
            const tooltip = document.getElementById(`tooltip-${id}`);
            tooltip.style.display = 'block';
        }

        // Function to hide tooltip
        function hideTooltip(id) {
            const tooltip = document.getElementById(`tooltip-${id}`);
            tooltip.style.display = 'none';
        }

        // Function to filter badges
        function filterBadges() {
            const filterValue = document.getElementById('filter').value;
            renderBadges(filterValue);
        }

        // Function to toggle badge selection
        function toggleBadgeSelection(badge) {
            if (badge.unlocked) {
                const selectedBadge = document.createElement('div');
                selectedBadge.className = 'selected-badge';
                selectedBadge.innerText = badge.title;
                trophyWall.appendChild(selectedBadge);
                // Animate the badge selection
                selectedBadge.animate([
                    { transform: 'scale(0)' },
                    { transform: 'scale(1.2)' },
                    { transform: 'scale(1)' }
                ], {
                    duration: 300,
                    easing: 'ease-in-out'
                });
            } else {
                alert(`This badge is locked! Criteria: ${badge.criteria}`);
            }
        }

        // Initial render
        renderBadges();

        class BadgeManager {
    constructor() {
        this.badges = [];
        this.init();
    }

    async init() {
        await this.loadBadges();
        this.startProgressUpdates();
    }

    async loadBadges() {
        try {
            const response = await fetch('badge_api.php?action=get_progress');
            const data = await response.json();
            this.badges = data;
            this.renderBadges();
        } catch (error) {
            console.error('Error loading badges:', error);
        }
    }

    renderBadges(filter = 'all') {
        const container = document.getElementById('badgeContainer');
        container.innerHTML = '';

        this.badges.forEach(badge => {
            if (this.shouldShowBadge(badge, filter)) {
                const badgeElement = this.createBadgeElement(badge);
                container.appendChild(badgeElement);
            }
        });
    }

    createBadgeElement(badge) {
        const div = document.createElement('div');
        div.className = `badge ${badge.is_unlocked ? '' : 'locked'}`;
        
        div.innerHTML = `
            <img src="${badge.image}" alt="${badge.title}">
            <div class="badge-title">${badge.title}</div>
            <div class="tooltip" id="tooltip-${badge.id}">
                <div>Criteria: ${badge.criteria}</div>
                <div>Progress: ${Math.round(badge.progress)}%</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: ${Math.min(badge.progress, 100)}%"></div>
                </div>
                ${badge.unlock_date ? `<div>Unlocked: ${new Date(badge.unlock_date).toLocaleDateString()}</div>` : ''}
            </div>
        `;

        div.onmouseover = () => this.showTooltip(badge.id);
        div.onmouseout = () => this.hideTooltip(badge.id);
        div.onclick = () => this.handleBadgeClick(badge);

        return div;
    }

    shouldShowBadge(badge, filter) {
        switch (filter) {
            case 'unlocked':
                return badge.is_unlocked;
            case 'locked':
                return !badge.is_unlocked;
            default:
                return true;
        }
    }

    showTooltip(badgeId) {
        const tooltip = document.getElementById(`tooltip-${badgeId}`);
        if (tooltip) tooltip.style.display = 'block';
    }

    hideTooltip(badgeId) {
        const tooltip = document.getElementById(`tooltip-${badgeId}`);
        if (tooltip) tooltip.style.display = 'none';
    }

    handleBadgeClick(badge) {
        if (badge.is_unlocked) {
            this.addToTrophyWall(badge);
        } else {
            alert(`Badge locked! ${badge.criteria}`);
        }
    }

    addToTrophyWall(badge) {
        const trophyWall = document.getElementById('selectedBadges');
        const selectedBadge = document.createElement('div');
        selectedBadge.className = 'selected-badge';
        selectedBadge.innerText = badge.title;
        trophyWall.appendChild(selectedBadge);
    }

    startProgressUpdates() {
        setInterval(() => this.loadBadges(), 30000); // Update every 30 seconds
    }
}

// Initialize badge manager when document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.badgeManager = new BadgeManager();
});

// Update filter function
function filterBadges() {
    const filterValue = document.getElementById('filter').value;
    window.badgeManager.renderBadges(filterValue);
}
    </script>

</body>
</html>