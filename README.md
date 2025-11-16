# 🚀 Personal Portfolio Website (PHP + TailwindCSS + MySQL)

A modern, scalable, and production-ready **Portfolio Website** built with  
**PHP 8**, **TailwindCSS**, **JavaScript**, and **MySQL**, following a clean, enterprise-grade architecture.

## 🛠 Requirements
- PHP 8.x or newer
- MySQL
- XAMPP
- Git (for version control)

---

## ⚙️ Installation (Local XAMPP)
  - 

The system includes:

- Dynamic asset loading  
- Auto-detected BASE_URL (no hardcoded paths)  
- Modular layouts (`layout_head.php`, `layout_foot.php`)  
- Clean folder architecture (MVC-ready)  
- Secure configuration handling  
- Logging system  
- Reusable components  
- SEO-friendly structure  

---

# 📌 Features

### 🧩 **Architecture**
- Clean folder structure (`core/`, `config/`, `includes/`, `views/`)
- Dynamic header/footer with asset injection
- Centralized path resolver: `paths.php`
- Centralized configuration: `config.php`
- Environment-independent (localhost / domain / subfolder)

### 💾 **Backend**
- PDO database connection wrapper (`db_connection.php`)
- Error logging (`logger.php`)
- Auto-resolving URLs & paths based on server environment

### 🎨 **Frontend**
- TailwindCSS + custom CSS
- Fully responsive layout
- Modular JS (header.js, footer.js, scroll-progress.js)
- Structured assets folder

### 🔒 **Security**
- `.gitignore` prevents leaking secrets
- `config_example.php` for safe public template
- Logs + uploads excluded from Git

---

# 📁 **Project Folder Structure**
Portfolio/
│── assets/
│ ├── css/ # Stylesheets (global.css, header.css, footer.css...)
│ ├── js/ # Frontend scripts
│ ├── images/ # Logos, banners, icons
│
│── config/
│ ├── config.php # Private config (ignored by Git)
│ ├── config_example.php # Public-safe template
│ ├── paths.php # Auto URL + PATH generator
│
│── core/
│ ├── db_connection.php # PDO connection wrapper
│ ├── HeaderData.php # Dynamic header data provider
│ ├── FooterData.php # Dynamic footer data provider
│
│── includes/
│ ├── layout_head.php # <head> section + CSS/JS inject
│ ├── layout_foot.php # Footer scripts
│ ├── header.php # Navigation bar
│ ├── footer.php # Footer UI
│ ├── logger.php # Logging utility
│
│── logs/
│ ├── app.log # Runtime logs (ignored by Git)
│
│── uploads/ # User uploads (ignored by Git)
│
│── views/
│ ├── index.php # Homepage
│ ├── about.php # About section
│ ├── projects.php # Portfolio projects
│ ├── notes.php # Notes / blogs
│ ├── contact.php # Contact page
│
│── .gitignore


---

## 🔐 Sensitive Files (NOT uploaded to GitHub)

  - `.gitignore` protects these:
    ── config/config.php # Project sensitive data (ignored by Git)
    ── logs/ # Logs file (ignored by Git)
    ── uploads/ # User uploads (ignored by Git)

---


### 1️⃣ Clone the repository
```bash
git clone https://github.com/Userrr404/Portfolio.git
cd Portfolio

### 2️⃣ Copy config template
cp config/config_example.php config/config.php

    - Then open config.php and set:
      ── DB_HOST=localhost
      ── DB_NAME=portfolio
      ── DB_USER=root
      ── DB_PASS=

### 3️⃣ Import MySQL database
    - CREATE DATABASE portfolio;
    - Then import SQL file
      ── phpMyAdmin → Import → <your_database>.sql

### 4️⃣ Run the project
    - Move the project to:
      ── C:\xampp\htdocs\Portfolio\

    - Start Apache + MySQL
      ── Then open:
        -- http://localhost/Portfolio/
