# 🎓 EDUGUARD - Online Exam Management System

**EDUGUARD** is a web-based Exam Management System built using PHP and MySQL. It enables educational institutions to manage exams, students, and teachers efficiently with enhanced security features like camera monitoring and fullscreen mode.

---

## 🚀 Features

- 🔐 **Role-Based Login System**
  - **Admin**: Manage users and exams
  - **Teacher**: Create questions, evaluate answers
  - **Student**: Take exams with live camera & fullscreen monitoring

- 🧠 **Question Types**
  - Single-choice
  - Multiple-choice
  - Short answer
  - Long answer

- 🎥 **Security**
  - Camera access during exams
  - Fullscreen enforcement
  - Prevents retaking after submission

- 📊 **Grading System**
  - Auto-evaluation for objective questions
  - Manual evaluation for subjective answers
  - Final results stored in `results` table

---

## 🛠️ Technologies Used

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP (Procedural)
- **Database**: MySQL
- **Browser APIs**: `getUserMedia` for camera access

---

## 📁 Directory Structure

```
EDUGUARD/
├── admin/           # Admin dashboard and management tools
├── student/         # Student interface and exam pages
├── teacher/         # Teacher panel and question management
├── assets/          # CSS and static resources
├── config/          # Database configuration
├── server_time.php  # Returns server time in seconds
├── index.php        # Login page and role redirect
├── logout.php       # Logout logic
└── exam_done.php    # Confirmation after exam submission
```

---

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/BRUNDAVANAMSUREKHA/EDUGUARD.git
```

### 2. Setup Environment

- Place it inside your web server directory (e.g. `htdocs` for XAMPP).
- Create a database in **phpMyAdmin** and import the SQL structure (if you have it).
- Update `/config/database.php` with your MySQL credentials.

### 3. Start the Server

- Run XAMPP or your preferred local server.
- Open in browser:  
  ```
  http://localhost/EDUGUARD/
  ```

---

## ✅ Usage

- **Admin Login** → Add users, exams
- **Teacher Login** → Create/edit questions, evaluate answers
- **Student Login** → Attempt exams (camera & fullscreen required)

> 🔒 Once a student submits an exam, they cannot reattempt it.

---

## 📌 Notes

- Ensure camera and fullscreen permissions are granted by the browser.
- Best used in modern browsers like Chrome or Firefox.
- For production, serve over HTTPS to support secure camera access.

---

## 🤝 Contributing

Feel free to fork the repo and submit a pull request. All improvements are welcome!

---

## 📬 Contact

Maintained by [@BRUNDAVANAMSUREKHA](https://github.com/BRUNDAVANAMSUREKHA)  
For issues, please use the [GitHub Issues](https://github.com/BRUNDAVANAMSUREKHA/EDUGUARD/issues) section.

---

**EDUGUARD** – Empowering digital learning through secure examination.
