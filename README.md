# Portfolio Website (PHP + MySQL + XAMPP)
A modern, secure, and scalable personal portfolio website built using **HTML, CSS, JavaScript, PHP, and MySQL**, with an enterprise-style folder structure and config system.

This project is designed to be:
- Secure (no secrets in GitHub)
- Scalable (MVC-ready architecture)
- Maintainable (clean config + logger + DB wrapper)
- Environment-independent (local/production auto-detection)
- Developer-friendly (paths auto-generated dynamically)

---

## 🚀 Features
- Dynamic and responsive frontend
- Centralized `config.php` with environment auto-detection
- Strong `paths.php` (no hardcoded paths or URLs)
- PDO-based Database Wrapper (`db_connection.php`)
- Enterprise-style Logger (`logger.php`)
- Secure Secrets Handling (`config_example.php`)
- Organized folder structure (MVC-friendly)
- Clean asset management (CSS, JS, images)
- Reusable components (header/footer)

---

## 📁 Project Structure
Portfolio/
│── config/
│ ├── config.php # Private config (NOT uploaded to GitHub)
│ ├── config_example.php # Safe template for GitHub
│ ├── paths.php # Dynamic URL + path resolver
│
│── includes/
│ ├── db_connection.php # PDO Singleton DB class
│ ├── logger.php # Logging system
│
│── logs/
│ ├── app.log # Log file (ignored by Git)
│
│── assets/
│ ├── css/
│ ├── js/
│ ├── images/
│
│── views/
│ ├── index.php
│ ├── about.php
│ ├── projects.php
│ ├── notes.php
│ ├── contact.php
| 
│
│── uploads/ # User uploads (ignored by Git)
│── README.md
│── .gitignore


---

## 🔐 Sensitive Files (NOT uploaded to GitHub)

  - `.gitignore` protects these:
    ── config/config.php # Project sensitive data (ignored by Git)
    ── logs/ # Logs file (ignored by Git)
    ── uploads/ # User uploads (ignored by Git)

---

## 🛠 Requirements
- PHP 8.x or newer
- MySQL
- XAMPP
- Git (for version control)

---

## ⚙️ Installation (Local XAMPP)

### 1️⃣ Clone the repository
```bash
git clone https://github.com/Userrr404/Portfolio.git
