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

## 1️⃣ Prerequisites

Please ensure the following are installed:

- 🐘 PHP 8.1+
-📦 Composer
- 🐬 PostgreSQL
- 🔧 Laravel 10 or later
-  Clone the repository
  ```
    git clone https://github.com/andavael/CICStem-Upgrade.git
   cd CICStem-Upgrade
 ```

## 2️⃣ Install dependencies
```bash
composer install
npm install && npm run build  # if using front-end assets
```

## 3️⃣ Copy .env.example to .env and configure your database credentials
```bash
cp .env.example .env

```
## 4️⃣ Generate application key
```bash
php artisan key:generate
```

## 5️⃣ Run migrations (and seeders, if any)
```bash
php artisan migrate
```
## 6️⃣ Start the application (local dev)
```bash
php artisan serve
```
# 🎯 Usage
- Visit the app via your browser (e.g. http://localhost:8000)
- Register as a Student or Tutor (or admin if you have credentials)
- Tutors can apply and upload their resume (PDF), admins can approve or reject
- Students can browse available tutoring sessions and enroll
- Admins can manage schedules, approve tutors, send announcements, and review feedback

# ⚙️ Configuration
- Database settings — configure in .env (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- Email / Notification settings — configure mail or notification driver in .env (if implemented)
- Storage / uploads — configure storage settings as needed (for resume uploads, etc.)

# 🧪 Testing
If there are tests defined under the tests/ directory, you can run them using PHPUnit:
```bash 
./vendor/bin/phpunit
```
Adjust configurations (e.g. test database) in your .env.testing (or equivalent) before running tests.

# 📝 Changelog
See the included CHANGELOG.md for a history of changes and updates.

# 🤝 Contributing
Feel free to open issues or submit pull requests. Please ensure your code follows PSR-12 standards (or your coding style preferences), includes meaningful commit messages, and — if adding new features — updates any relevant documentation, migrations, or views.


