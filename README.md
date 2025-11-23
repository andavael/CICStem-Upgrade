# 📘CICStem-Upgrade

 A structured web-based platform designed to streamline scheduling, tutor management, academic assistance, and communication for the CICSTEM community. The system provides distinct interfaces for administrators, tutors, and students while ensuring secure authentication, controlled tutor approval, and organized academic session workflows.
# ✨ Features

- 🔐 Secure multi-role authentication (Admin, Tutor, Student)
- 🧑‍🏫 Tutor application, approval, and monitoring
- 🎓 Student enrollment in academic sessions
- 🗓️ Session scheduling with conflict prevention
- 📢 Announcement management
- 📝 Feedback system for continuous improvement
- 📨 In-app notifications
- 📄 Resume upload for tutor applicants (PDF only)
- ⚙️ Simple and maintainable Laravel-based architecture

  ## 📁 Project Structure
 ```txt
project-root/
├── app/
│   ├── Models/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Middleware/
├── resources/
│   └── views/
│       ├── admin/
│       ├── tutor/
│       ├── student/
│       └── layouts/
├── public/
│   └── css/
├── routes/
│   └── web.php
└── database/
    ├── migrations/
    ├── seeders/
    └── sql/
```

#1️⃣ Prerequisites

Please ensure the following are installed:

- 🐘 PHP 8.1+
-📦 Composer
- 🐬 MySQL or MariaDB
- 🌐 Node.js & NPM (optional for asset management)
- 🔧 Laravel 10 or later

