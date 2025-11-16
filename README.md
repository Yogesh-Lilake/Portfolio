# 🚀 Personal Portfolio Website (PHP + TailwindCSS + MySQL)

A modern, scalable, and production-ready **Portfolio Website** built with  
**PHP 8**, **TailwindCSS**, **JavaScript**, and **MySQL**, following a clean, Fully structured Enterprise-Architecture, Fail-safe Models, Cache Engine, Dynamic Layouts, Auto URL Paths.


# This project is built with a zero-crash, self-healing backend architecture:
  - If the database fails --> website still loads using catch + fallback.
  - If tables are empty --> default content appears.
  - Auto-logs errors.
  - Environment-independent paths.
  - Clean MVC-ready folder structure.

## 🛠 Requirements
- PHP 8.x or newer
- MySQL
- XAMPP
- Git (for version control)
- VS code (recommended)

---

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
- MVC-ready
- Clean folder structure (`core/`, `config/`, `includes/`, `views/`)
- Dynamic header/footer with asset injection
- Auto-path resolver (no hardcoded URLs)
- Centralized path resolver: `paths.php`
- Centralized configuration: `config.php`
- Environment-independent (localhost / domain / subfolder)
- Dynamic layout system (layout_head.php, layout_foot.php)
- Enterprise error-handling (ErrorHandler.php)
- Reusable model architecture

### 💾 **Backend**
- PDO database connection wrapper (`db_connection.php`)
- Auto-fallback content (DB-failure proof)
- Centralized logger & Error logging (`logger.php`)
- Automatic caching engine (cache/*.json)
- Helpers for safe queries + sanitization
- Zero fatal errors (try/catch everywhere)
- Auto-resolving URLs & paths based on server environment

### 🎨 **Frontend**
- TailwindCSS + custom CSS + (CDN or local config supported)
- Smooth animations
- Fully responsive layout
- Modular JS (header.js, footer.js, scroll-progress.js)
- Structured assets folder
- SEO-optimized HTML structure

### 🔒 **Security**
- `.gitignore` prevents leaking secrets (`config.php`, `logs/`, `uploads/`, `catch/`)
- `config_example.php` for safe public template
- Logs + uploads excluded from Git
- No credentials leak
- Sanitized output using helpers

### Dynamic Content System
- All webpages load via Models --> cache --> Fallback
  - Home Section → HomeModel.php
  - About Section → AboutModel.php
  - Skills Section → SkillModel.php
  - Projects Section → ProjectModel.php
  - Contact Section → ContactModel.php

- Even If:
  - MySQL stops
  - A table is missing
  - A query fails
  - Hosting disconnects

---

# 📁 **Project Folder Structure**
Portfolio/
|
|── app/ (`This folder contains all backend application logic & It is the “brain” of your portfolio`)
| ├── Helpers/ (`These files contain reusable PHP helper functions used across all pages.`)
| |     ├── helpers.php (`Keep code DRY and avoid repeating logic everywhere.`)
| |     ├── sanitizer.php (`Security layer for all user-facing output.`)
│ │     ├── view_helpers.php (`Keep view logic clean without mixing PHP logic in templates.`)
| |
| ├── Models/ (`Models are responsible for data fetching, fallback values, and business logic. Views NEVER touch SQL; all SQL is inside Models.`)
│ │    ├── AboutModel.php
│ │    ├── ContactModel.php
│ │    ├── HomeModel.php
│ │    ├── ProjectModel.php
│ │    ├── SkillModel.php
│ │
| ├── Servicess/ (`These are services — reusable backend components.`)
│ |    ├── CacheService.php
│ |    ├── MailService.php
│ 
│── assets/ (`Contains all public-facing files (CSS, JS, images). This folder loads directly in the browser.`)
│ ├── css/ # Stylesheets (global.css, header.css, footer.css...)
│ ├── js/ # Frontend scripts
│ ├── images/ # Logos, banners, icons
│
│── cache/ (`This is your website’s high-performance memory.`)
│   ├── about.json
│   ├── contact.json
│   ├── home.json
│   ├── skills.json
│   ├── projects.json
│   ├── featured_projects.json
│
│── config/ (`Configuration files that initialize everything.`)
│ ├── config.php # Private config (ignored by Git)
│ ├── config_example.php # Public-safe template
│ ├── env.php # Loads hosting provider environment variables
│ ├── paths.php # Auto URL + PATH generator
│
│── core/ (`Core contains the foundation of your backend system.`)
│ ├── Controller.php
│ ├── db_connection.php # PDO connection wrapper
│ ├── ErrorHandler.php
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
│── logs/ (`Debugging and monitoring.`)
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

### 2️⃣ Create your secure config
cp config/config_example.php config/config.php

    - Then open config.php and set:
      ── DB_HOST=localhost
      ── DB_NAME=portfolio
      ── DB_USER=root
      ── DB_PASS=

### 3️⃣ Import the MySQL database
    - CREATE DATABASE portfolio;
    - Then import SQL file
      ── phpMyAdmin → Import → <your_database>.sql

### 4️⃣ Move project to XAMPP + Run
    - Move the project to:
      ── C:\xampp\htdocs\Portfolio\

    - Start Apache + MySQL
      ── Then open:
        -- http://localhost/Portfolio/
