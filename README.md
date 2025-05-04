# IT Project 2024

## Overview
This web application is developed for **IT Project 2024**, focusing on mental health and well-being. The project offers features such as user authentication, 
journaling, task tracking, music recommendations, weekly reflections, breathing exercises, emergency contacts, and journal customization.

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP, Python (for music recommendations)
- **Database:** MySQL (XAMPP)
- **Server:** Apache (XAMPP)

## Installation & Setup

### Prerequisites
Ensure you have the following installed:
- [XAMPP](https://www.apachefriends.org/index.html) (includes Apache, PHP, and MySQL)
- A web browser
- Git (optional)
- Python (for music recommendation system)

### Steps
1. **Clone the Repository:**
   ```sh
   git clone https://github.com/Shannonsahdeo/IT-Project---Mental-health-website.git
   ```
2. **Move to the XAMPP `htdocs` Directory:**
   ```sh
   mv IT_Project-2024 /xampp/htdocs/
   ```
3. **Start Apache and MySQL in XAMPP.**
4. **Import the Database:**
   - Open [phpMyAdmin](http://localhost/phpmyadmin/).
   - Create a database (e.g., `mentalhealthapp`).
   - Import the provided SQL file (`database.sql`) from the project folder.


## Features
- **User Authentication:** Secure login and registration system.
- **Journaling:** Write, edit, and view personal journal entries.
- **Journal Customization:** Users can personalize their journal appearance.
- **Task Tracking:**Daily , weekly and monthly motivational tasks 
- **Music Recommendations:** Suggests songs based on user mood (Handled by `app.py`).
- **Weekly Reflection:** Prompts users to reflect on their week.
- **Breathing Exercises:** Guided exercises to help reduce stress.
- **Emergency Contacts:** Quick access to important contact information.
- **Paygate System:** Subscription-based access implemented.

## Future Developments
- **Chatroom:** Planned for future implementation.
- **Badges & Level-Up System:** Planned for future implementation.

## File Structure
```
IT_Project-2024/
├── index.php               # Main entry point
├── login.php               # User authentication
├── register.php            # User sign-up
├── dashboard.php           # Main user dashboard
├── journal.php             # Journal feature
├── customize_journal.php   # Journal customization feature
├── tasks.php               # Task tracking system
├── app.py                  # Music recommendation system
├── breathe.php             # Breathing exercises
├── reflection/
│   └── reflection.php      # Weekly reflection feature
├── contacts/
│   └── emergency.php       # Emergency contacts feature
├── subscription.php        # Paygate system
├── assets/                 # CSS, JS, images
├── database.sql            # MySQL database schema
└── README.md               # Project documentation
```

## Contribution
This project is **not open source**, but we welcome improvements and concerns. If you have suggestions, please raise an issue or contact the project maintainers.

## License
This project is private and intended for educational purposes. Unauthorized distribution or modification is not allowed.

---
