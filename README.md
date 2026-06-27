<div align="center">

<img src="https://readme-typing-svg.herokuapp.com?font=DM+Sans&size=36&duration=3000&pause=1000&color=6366F1&center=true&vCenter=true&width=700&lines=EDUGUARD+🎓;Secure+Online+Exam+Management;Camera+%2B+Fullscreen+Proctoring;Built+with+PHP+%2B+MySQL" alt="Typing SVG" />

<br/>

# 🎓 EDUGUARD
### *Empowering Digital Learning Through Secure Examination*

<br/>

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![HTML5](https://img.shields.io/badge/HTML5-Frontend-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-Styling-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)

<br/>

</div>

---

## 📌 Overview

**EDUGUARD** is a full-featured, web-based Online Exam Management System built with PHP and MySQL. It provides educational institutions with a secure, role-based platform to conduct, manage, and evaluate exams entirely online.

What makes EDUGUARD stand out is its **built-in proctoring** — live camera monitoring and fullscreen enforcement ensure exam integrity without needing any third-party software.

> 🔒 *Once a student submits an exam, resubmission is permanently blocked — ensuring fair evaluation every time.*

---

## ✨ Features

### 👥 Role-Based Access

<table>
<tr>
<td align="center" width="33%">

### 🛡️ Admin
- Add and manage users
- Create and schedule exams
- View all results and reports
- Full system control

</td>
<td align="center" width="33%">

### 🧑‍🏫 Teacher
- Create & edit questions
- Set exam duration and marks
- Manually evaluate subjective answers
- View student submissions

</td>
<td align="center" width="33%">

### 🧑‍🎓 Student
- Attempt assigned exams
- Camera & fullscreen required
- View results after evaluation
- Cannot retake submitted exams

</td>
</tr>
</table>

---

### 🧠 Question Types Supported

| Type | Description | Evaluation |
|---|---|---|
| ✅ Single Choice (MCQ) | One correct answer from options | Auto |
| ☑️ Multiple Choice | Multiple correct answers | Auto |
| 📝 Short Answer | Brief written response | Manual |
| 📄 Long Answer | Detailed written response | Manual |

---

### 🔒 Security & Proctoring

```
Student starts exam
      ↓
📷 Camera access requested via browser
      ↓
🖥️ Fullscreen mode enforced automatically
      ↓
⏱️ Timer synced with server time (server_time.php)
      ↓
✅ On submission → exam locked permanently
      ↓
🚫 No resubmission allowed
```

- **Live camera monitoring** via `getUserMedia` Browser API
- **Fullscreen enforcement** — exits trigger warnings
- **Server-side time sync** — prevents client-side timer manipulation
- **One-time submission** — stored and locked in `results` table

---

### 📊 Grading System

| Question Type | Method | Who Grades |
|---|---|---|
| Single Choice | Automatic comparison | System |
| Multiple Choice | Automatic comparison | System |
| Short Answer | Manual review | Teacher |
| Long Answer | Manual review | Teacher |
| **Final Result** | Combined score | Stored in DB |

---

## 🛠️ Tech Stack

| Layer | Technology | Role |
|---|---|---|
| **Frontend** | HTML5, CSS3, JavaScript | UI and interactivity |
| **Backend** | PHP (Procedural) | Server-side logic & routing |
| **Database** | MySQL | Data storage |
| **Browser API** | `getUserMedia` | Live camera access |
| **Browser API** | Fullscreen API | Exam mode enforcement |
| **Time Sync** | `server_time.php` | Prevent timer manipulation |
| **Dev Tools** | XAMPP / WAMP | Local server environment |

---

## 📁 Project Structure

```
EDUGUARD/
│
├── 📂 admin/
│   ├── dashboard.php         # Admin home
│   ├── manage_users.php      # Add/edit/delete users
│   └── manage_exams.php      # Create and schedule exams
│
├── 📂 student/
│   ├── dashboard.php         # Student home
│   ├── exam.php              # Exam interface (camera + fullscreen)
│   └── results.php           # View results after evaluation
│
├── 📂 teacher/
│   ├── dashboard.php         # Teacher home
│   ├── questions.php         # Create and manage questions
│   └── evaluate.php          # Manual evaluation panel
│
├── 📂 assets/
│   ├── css/                  # Stylesheets
│   └── images/               # Static images and icons
│
├── 📂 config/
│   └── database.php          # MySQL connection credentials
│
├── server_time.php            # Returns server time in seconds
├── index.php                  # Login page + role-based redirect
├── logout.php                 # Session destroy and logout
├── exam_done.php              # Post-submission confirmation screen
└── README.md
```

---

## ⚙️ Installation & Setup

### Prerequisites

| Tool | Download |
|---|---|
| XAMPP or WAMP | https://www.apachefriends.org/ |
| Git | https://git-scm.com/ |
| Modern Browser | Chrome / Firefox (camera support required) |

---

### Step 1 — Clone the Repository

```bash
git clone https://github.com/BRUNDAVANAMSUREKHA/EDUGUARD.git
```

---

### Step 2 — Move to Server Root

```bash
# XAMPP (Windows)
C:\xampp\htdocs\EDUGUARD\

# WAMP (Windows)
C:\wamp64\www\EDUGUARD\

# XAMPP (Mac/Linux)
/Applications/XAMPP/htdocs/EDUGUARD/
```

---

### Step 3 — Configure Database

Edit **`config/database.php`** with your MySQL credentials:

```php
<?php
$host     = 'localhost';
$username = 'root';        // your MySQL username
$password = '';            // your MySQL password
$database = 'eduguard_db';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

---

### Step 4 — Import Database

1. Open **phpMyAdmin** → http://localhost/phpmyadmin
2. Click **"New"** → create database named `eduguard_db`
3. Select the database → click **Import**
4. Upload the `.sql` file from the repository
5. Click **Go**

---

### Step 5 — Start the Server

- Open **XAMPP Control Panel**
- Start **Apache** ✅ and **MySQL** ✅

---

### Step 6 — Open in Browser

```
http://localhost/EDUGUARD/
```

---

## 🖥️ Usage Guide

### Admin
1. Log in with admin credentials
2. Add teachers and students under **Manage Users**
3. Create exams and assign them under **Manage Exams**

### Teacher
1. Log in with teacher credentials
2. Go to **Questions** → add questions with type and marks
3. After exam completion → go to **Evaluate** to grade subjective answers

### Student
1. Log in with student credentials
2. Click on an assigned exam
3. **Allow camera access** and enter fullscreen when prompted
4. Answer all questions within the time limit
5. Submit — results will be available after teacher evaluation

---

## ⚠️ Important Notes

- 📷 **Camera permission** must be granted by the browser before the exam starts
- 🖥️ **Fullscreen mode** is required — exiting may trigger warnings
- 🔐 **HTTPS recommended** for production — required for secure `getUserMedia` access
- 🌐 Best used in **Google Chrome** or **Mozilla Firefox**
- ⏱️ Exam timer is synced with the **server**, not the browser clock

---

## 🔭 Roadmap

```
v1.0  ✅  Role-based login (Admin, Teacher, Student)
v1.1  ✅  Question management (4 types)
v1.2  ✅  Camera + fullscreen proctoring
v1.3  ✅  Auto + manual grading system
v2.0  🚧  AI-based answer evaluation
v2.1  📅  Email notifications for results
v2.2  📅  Analytics dashboard for teachers
v2.3  📅  Mobile app version
v3.0  📅  Cloud deployment + multi-institution support
```

---

## 👩‍💻 Author

<div align="center">

**Surekha Brundavanam**

[![GitHub](https://img.shields.io/badge/GitHub-BRUNDAVANAMSUREKHA-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/BRUNDAVANAMSUREKHA)

</div>

---

## 📄 License

⚠️ This is a proprietary project. All rights reserved © 2025 Surekha Brundavanam. Usage, reproduction, or distribution without permission is strictly prohibited.

---

<div align="center">

**🎓 EDUGUARD — Empowering Digital Learning Through Secure Examination**

*If this project helped you, consider giving it a ⭐ on GitHub!*

</div>
