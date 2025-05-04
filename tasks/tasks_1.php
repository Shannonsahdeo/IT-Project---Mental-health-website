<?php

session_start();

if (!isset($_SESSION['username'])) {
    if (isset($_COOKIE['username'])) {
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['ID'] = $_COOKIE['ID']; // Assuming ID is stored in the cookie
    } else {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
}



// Check if the user is logged out, then destroy session and redirect
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
  session_unset();
  session_destroy();
  setcookie("username", "", time() - 3600, "/"); // Optional: Delete the cookie
  header("Location: loginform.php"); // Redirect to login page
  exit();
}




$user_id = $_SESSION['ID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <style>
        /* General body and background */
        body {
            font-family: 'Verdana', sans-serif;
            background: linear-gradient(to bottom, #fceabb, #f8b195);
            color: #4d4d4d;
            padding: 20px;
            margin: 0;
            overflow: auto;
        }

        header {
            background: url('header_2.jpg') no-repeat center/cover;
            width: 100%;
            height: 300px;
            border-radius: 15px;
            margin-bottom: 0;
            position: relative;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin: auto;
            margin-top: -50px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s;
        }

        h2 {
            text-align: center;
            color: #e29578;
            margin-bottom: 20px;
        }

        .level-display {
            background: #ffddd2;
            color: #4d4d4d;
            padding: 15px;
            border-radius: 10px;
            font-size: 24px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .xp-progress {
            background: #ffe5d9;
            border-radius: 10px;
            height: 20px;
            width: 100%;
            margin: 30px 0;
            position: relative;
        }

        .xp-progress-bar {
            background: orangered;
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s;
        }

        /* Tab navigation */
        .tab {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            background: #faf3dd;
            border-radius: 10px;
            margin-right: 5px;
            color: #4d4d4d;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: background 0.3s, transform 0.2s;
        }

        .tab:hover {
            background: #ffddd2;
            transform: scale(1.05);
        }

        .active-tab {
            background: #ffcab1;
        }

        /* Enhanced Task Styling */
        .task-list {
            margin-top: 20px;
        }

        .task-item {
            background: #fff5e6;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .task-item::after {
            content: "▼";
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .task-item.expanded::after {
            transform: translateY(-50%) rotate(180deg);
        }

        .task-item:hover {
            background: #ffddd2;
            transform: scale(1.02);
        }

        .task-item.completed {
            background: #d4edda;
        }

        .task-title {
            font-weight: bold;
            margin-bottom: 10px;
            padding-right: 30px;
        }

        .task-xp {
            color: #e29578;
            font-weight: bold;
        }

        .task-description {
            color: #666;
            margin: 15px 0;
            padding: 15px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            font-size: 14px;
            display: none;
            border-left: 3px solid #e29578;
        }

        /* Button styling */
        button {
            background-color: #e29578;
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 25px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #d47f67;
            transform: scale(1.05);
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* Toast message */
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #f8b195;
            color: #4d4d4d;
            padding: 20px;
            border-radius: 10px;
            display: none;
            z-index: 1000;
            transition: opacity 0.5s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 18px;
        }

        /* Confetti Animation */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background-color: #f00;
            pointer-events: none;
            opacity: 0;
            animation: confettiFall 3s ease-out forwards;
            z-index: 9999;
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #4d4d4d;
        }
    </style>
</head>
<body>
    <header></header>

    <div class="form-container">
        <h2>Select a Task</h2>
        <div class="level-display">Current Level: <span id="currentLevel">1</span></div>
        <div>XP to Next Level: <strong id="xpToNextLevel">50</strong></div>
        <div class="xp-progress">
            <div class="xp-progress-bar" id="xpProgressBar" style="width: 0%;"></div>
        </div>
        
        <div>
            <div class="tab active-tab" onclick="showTasks('daily')">Daily Tasks</div>
            <div class="tab" onclick="showTasks('weekly')">Weekly Tasks</div>
            <div class="tab" onclick="showTasks('monthly')">Monthly Tasks</div>
        </div>

        <div class="task-list" id="taskList"></div>
    </div>

    <div class="toast" id="toastMessage"></div>

    <footer>
        <p>&copy; 2024 Moodify</p>
    </footer>

    <audio id="levelUpSound" src="level-up-sound.mp3" preload="auto"></audio>

    <script>
        let currentXP = 0;
        let currentLevel = 1;
        const baseXPToNextLevel = 50;
        const currentLevelElement = document.getElementById('currentLevel');
        const xpToNextLevelElement = document.getElementById('xpToNextLevel');
        const xpProgressBar = document.getElementById('xpProgressBar');
        const toastMessage = document.getElementById('toastMessage');
        const levelUpSound = document.getElementById('levelUpSound');

        function calculateXPToNextLevel(level) {
            return baseXPToNextLevel * Math.pow(level, 2);
        }

        function createConfetti() {
            const colors = ['#e29578', '#ffddd2', '#83c5be', '#006d77', '#edf6f9'];
            const confettiCount = 100;

            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.animationDelay = Math.random() * 3 + 's';
                document.body.appendChild(confetti);

                setTimeout(() => {
                    confetti.remove();
                }, 3000);
            }
        }

        function showTasks(taskType) {
            fetchTasksFromDB(taskType);
            
            // Update active tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active-tab');
            });
            event.target.classList.add('active-tab');
        }

        async function fetchTasksFromDB(taskType) {
            try {
                const response = await fetch('fetch_tasks.php?taskType=' + taskType);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const tasks = await response.json();

                const taskList = document.getElementById('taskList');
                taskList.innerHTML = '';

                if (tasks.length === 0) {
                    taskList.innerHTML = `<p>No ${taskType} tasks found.</p>`;
                    return;
                }

                tasks.forEach(task => {
                    const taskItem = document.createElement('div');
                    taskItem.classList.add('task-item');
                    taskItem.setAttribute('data-task-id', task.id);
                    taskItem.setAttribute('data-xp', task.xp);

                    taskItem.innerHTML = `
                        <div class="task-title">${task.name} <span class="task-xp">(${task.xp} XP)</span></div>
                        <div class="task-description">${task.description}</div>
                        <div class="progress-bar" style="width: 0%;"></div>
                        <button onclick="completeTask(this, ${task.id})">Complete Task</button>
                    `;

                    taskItem.addEventListener('click', function(e) {
                        if (e.target.tagName !== 'BUTTON') {
                            this.classList.toggle('expanded');
                            const description = this.querySelector('.task-description');
                            description.style.display = description.style.display === 'none' ? 'block' : 'none';
                        }
                    });

                    taskList.appendChild(taskItem);
                });
            } catch (error) {
                console.error('Error fetching tasks:', error);
                document.getElementById('taskList').innerHTML = `<p>Error loading tasks.</p>`;
            }
        }

        function completeTask(button, taskId) {
        const taskItem = button.parentElement;
        const xpEarned = parseInt(taskItem.getAttribute('data-xp'));
        const progressBar = taskItem.querySelector('.progress-bar');
        progressBar.style.width = '100%';
        taskItem.classList.add('completed');
        button.innerText = 'Completed';
        button.disabled = true;

        fetch('update_tasks_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `task_id=${taskId}`
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response from server:', data);
            if (data.status !== 'success') {
                console.error('Error updating task:', data.message);
                showToast(data.message || 'Error updating task. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating task. Please try again.');
        });

        // Update XP and level
        currentXP += xpEarned;
        if (currentXP >= calculateXPToNextLevel(currentLevel)) {
            currentLevel++;
            currentXP = 0;
            playLevelUpSound();
            showToast('Level Up! You are now level ' + currentLevel);
            createConfetti();
        }

        updateLevelDisplay();
    }

        function updateLevelDisplay() {
            currentLevelElement.innerText = currentLevel;
            xpToNextLevelElement.innerText = calculateXPToNextLevel(currentLevel) - currentXP;
            xpProgressBar.style.width = (currentXP / calculateXPToNextLevel(currentLevel)) * 100 + '%';
        }

        function showToast(message) {
            toastMessage.innerText = message;
            toastMessage.style.display = 'block';
            setTimeout(() => {
                toastMessage.style.display = 'none';
            }, 3000);
        }

        function playLevelUpSound() {
            levelUpSound.play();
        }

        // Initialize with daily tasks
        showTasks('daily');
    </script>
</body>
</html>
