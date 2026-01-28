# 🚀 Personal Portfolio Website (PHP 8 + Custome Router + PSR-4 MVC + TailwindCSS + MySQL) Enterprise Architecture

A modern, scalable, and production-ready **Portfolio Website** built with  
**PHP 8**, **Modern MVC structure**, **TailwindCSS**, **JavaScript**, and **MySQL**, following a clean, zero-crash, Fully structured Enterprise-Architecture, Fail-safe Models, Cache Engine, Dynamic Layouts, Auto URL Paths.

# This project is built with a zero-crash, self-healing backend architecture to always stay online:

- Safe Mode Architecture (`Config-Path Safety Layer, Enterprise Fail-Safe UX Layer`)
- Single Entry-Point Architecture (`All HTTP traffic now goes through only: "public/index.php" `)
- Fully custom Router (`GET/POST/ANY`)
- Single-entry front controller architecture
- PSR-4 namespaces
- Autoload-first bootstrap
- JSON fallback engine (`If tables are empty → JSON defaults load`)
- If JSON missing → hard-coded fallbacks load
- Smart caching system (`Cache boosts performance automatically`)
- No controller or model can crash the page
- Auto-logs errors.
- Dynamic layouts + auto asset injection
- Enterprise-grade Contact API

---

# 🔥 Key Concepts

### 🧠 **Zero-Crash Enterprise Backend Architecture & Unified 4-Layer Data Architecture**

Every page & model follows:

**A → B → C → D data fallback pipeline**
**LEVEL** **SOURCE** **PURPOSE**

1.  **A. Cache (storage/cache/\*.json) Fastest response**
2.  **B. MySQL Database Real data**
3.  **C. JSON Defaults (app/resources/defaults/\*) Safe content if DB empty**
4.  **D. Hard-coded fallbacks Last layer, prevents UI break**

This guarantees **no empty UI**, **zero fatal errors**, and **production reliability**.

---

### 🧱 Config-Path Safety Layer (Zero-Crash Guarantee)

This project introduces a **Config-Path Safety Layer** to prevent **fatal runtime crashes**
caused by missing or misconfigured path constants.

In real production environments, configuration drift can occur due to:
- Partial deployments
- Environment mismatch (localhost vs live)
- Missing JSON fallback files
- Disabled feature modules
- Refactoring or incremental rollout

### ❌ Problem (Traditional PHP Apps)
In most PHP applications, directly accessing undefined constants like:

```php
HEADER_DEFAULT_FILE
FOOTER_DEFAULT_FILE
```

results in:
  - Fatal errors
  - White screen of death
  - Broken production pages
This violates enterprise uptime guarantees.

### ⚡ Cache Engine (Enterprise-Hardened)

The cache system is fully self-healing and defensive by design.

**Core Rules:**

  1. **Cache is never trusted blindly**
  2. **Any corruption → cache ignored + deleted**
  3. **Cache failure never escalates to Safe Mode**
  4. **Page always renders using fallback layers**

### 🧨 Cache Corruption Detection (DC-01 → DC-07)

The cache engine explicitly detects and handles all real-world corruption scenarios.

Supported Corruption Classes
DC Code	Description
  DC-01	Invalid JSON (syntax corruption)
  DC-02	Root structure invalid
  DC-03	Partial cache write / missing payload
  DC-04	Schema corruption (missing required fields)
  DC-05	Semantic corruption (invalid business meaning)
  DC-06	Cache expired (TTL auto-resolved)
  DC-07	Cache corrupted but recoverable (treated as optimization failure)


### 🔍 DC-07: Cache Corrupted (Invalid JSON)

Why this exists
Invalid cache files are common in real systems due to:
  1. **Crashes during write**
  2. **Disk I/O interruption**
  3. **Manual file edits**
  4. **Partial deployments**

Setup
  1. **Cache file contains invalid JSON**

Expected Flow

  Cache (invalid)
    ↓
  Cache ignored + deleted
    ↓
  DB
    ↓
  JSON defaults
    ↓
  Hard fallback (if required)


Guaranteed Result

  1. **Cache ignored**
  2. **Error logged**
  3. **Page renders normally**
  4. **safe_mode = false**

Cache corruption is treated as a recoverable optimization failure, not a system failure.

### 🧩 Cache Validation Architecture

Cache validation is split into infrastructure and business contracts.

Infrastructure (CacheService)

  1. **File existence**
  2. **JSON syntax**
  3. **Root structure**
  4. **TTL expiry**
  5. **Payload presence**

Business Validators (app/CacheValidators/)

  1. **Schema validation**
  2. **Semantic validation**
  3. **Cross-section protection**

Validator Layer
  app/CacheValidators/
  ├── Contracts/
  |     ├── CacheValidatorInterface.php
  ├── Global/
  ├── Lists/
  ├── Pages/


This separation ensures:

  1. **CacheService never becomes a god-class**
  2. **Business rules evolve independently**
  3. **Cross-section corruption is prevented**

### 🔐 Safe Mode Architecture (Enterprise Fail-Safe UX Layer)

This project implements a Safe Mode UI + Controller Architecture, inspired by real-world production systems (Google, Stripe, AWS dashboards).

**Safe Mode ensures that even if critical backend failures occur, the application:**

  1. **Never crashes**
  2. **Never exposes errors to users**
  3. **Never shows broken or misleading UI**
  4. **Always preserves navigation & trust**

**Instead of crashing, the system:**
  - Normal Mode  → Full UI + Interactions
  - Safe Mode    → Read-only + Minimal + Trusted UI

**Key Guarantees**

  ❌ No fatal error reaches the browser
  ❌ No partial or corrupt data is cached
  ✅ Errors are logged internally
  ✅ User always sees a valid page

---

## .htaccess handles rewriting:

- RewriteEngine On
- RewriteBase /Portfolio/public/
- RewriteCond %{REQUEST_FILENAME} !-f
- RewriteCond %{REQUEST_FILENAME} !-d
- RewriteRule ^ index.php [QSA,L]

## 🛠 Requirements

- PHP 8.x or newer
- MySQL (XAMPP recommended)
- Composer
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

- Fully modular PSR-4 MVC structure
- Custom Router + routes/web.php
- Single Entry-Point Architecture (`All HTTP traffic now goes through only: "public/index.php" `)
- Clean folder structure (`config/`, `public/`, `rotues/`, `app/`)
- Dynamic header/footer with asset injection
- Auto-path resolver (no hardcoded URLs)
- Enterprise folder organization
- Centralized path resolver: `paths.php`
- Centralized configuration: `config.php`
- Environment-independent (localhost / domain / subfolder)
- Dynamic layout system (layout_head.php, layout_foot.php)
- Enterprise error-handling (ErrorHandler.php)
- Reusable model architecture
- Smart controllers with clean output
- Centralized helpers for clean code

### 💾 **Backend**

- PDO database connection wrapper & Singleton with auto-reconnect(`DB.php`)
- Query health-check (SELECT 1)
- Auto-fallback content (DB-failure proof)
- Centralized logger & Error logging (`logger.php`)
- Automatic caching engine (cache/\*.json)
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

- `.gitignore` prevents leaking secrets (`config/config.php`, `logs/`, `uploads/`, `storage/catch/`, `vendor`, `node_modules/`)
- `config_example.php` for safe public template
- Logs + uploads excluded from Git
- No credentials leak
- Sanitized output using helpers
- Secure mail system
- This project now implements **enterprise-grade contact form security**, including:

  ### ✔ 1. Honeypot Bot Protection

      - Invisible field `hp_name` detects bots automatically.

  ### ✔ 2. IP-Based Rate Limiting

      - Protects your email inbox from abuse:

          | Window | Limit |
          |--------|--------|
          | Per 60 seconds | 1 message |
          | Per hour | Max 5 messages |

  ### ✔ 3. Email Delivery Audit Logging

      - Every submission is stored safely in DB before attempting to send email.

        | Column | Meaning |
        |--------|---------|
        | `email_sent = 1` | Email delivered successfully |
        | `email_sent = 0` | Delivery failed |
        | `email_error` | Stores SMTP failure message (truncated) |

      - This guarantees **no message is ever lost**, even if your email provider fails.

  ### ✔ 4. PHPMailer Enterprise Pipeline

      - Modern PHPMailer integration with:

        - try/catch guards
        - authenticated SMTP delivery
        - safer From/Reply-To handling
        - HTML message template
        - spam-safe headers

  ### ✔ 5. Hardened Frontend JS Pipeline

      - contact.js now includes:

        - loading states
        - Top-right toast messages
        - AJAX submission
        - graceful fallback
        - improved error handling
        - No page reload
        - Clean UX

---

# 📨 **Enterprise Contact API Architecture (Updated)**

Your contact functionality now works like a **real API service**:

**Pipeline:**  
  - Input validation
  - Honeypot spam detection
  - IP-based rate limiting
  - reCAPTCHA verification (Pending)
  - Insert message into DB (email_sent = 0)
  - Attempt SMTP delivery
  - Update DB with success/failure
  - Return JSON response
  - Log everything internally

This makes your contact form **reliable, secure, and production-ready.** and **Logging ensures no message is ever lost.**

---

# 🎯 **File Load Ordering Fix (New)**

`public/contact.php` now loads files in a safe deterministic order:

1. `paths.php`
2. `bootstrap.php`
3. `vendor/autoload.php` (PHPMailer)
4. Controller execution
5. View rendering

This prevents:

- header not rendering
- nav links disappearing
- PATH constant errors
- duplicate config loading

---

### ✔ **Unified Model Architecture**

Every Model follows:

| Stage | Source        | Description            |
| ----- | ------------- | ---------------------- |
| A     | Cache         | Fastest response       |
| B     | DB            | Fetch real data        |
| C     | JSON          | User-editable defaults |
| D     | Hard Fallback | Never-break guarantee  |

### ✔ **Safe View Rendering**

All pages use:

````php
["from_db" => bool, "data" => []]

### Dynamic Content System
- All webpages load via Models --> cache --> DB --> .JSON --> Fallback
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
|── app/ # This folder contains all backend application logic & It is the “brain” of your portfolio
|    ├── CacheValidators/                   # All page cache validators class and functions
|    │    ├── Contracts/                    # Validator Interface
|    │    │    └── CacheValidatorInterface.php 
|    │    │  
|    │    ├── Global/
|    │    │     ├── HeaderCacheValidator.php
|    │    │     └── FooterCacheValidator.php
|    │    │
|    │    ├── Lists/
|    │    │     ├── Notes/
|    │    │     │     ├── NoteCategoriesCacheValidator.php
|    │    │     │     ├── NotePinnedCacheValidator.php
|    │    │     │     ├── NotesListCacheValidator.php
|    │    │     │     └── NoteTagsCacheValidator.php
|    │    │     │     
|    │    │     ├── Projects/
|    │    │           └── ProjectListCacheValidator.php
|    │    │
|    │    ├── Pages/      # Page-level & section-level each pages Validators
|    │    │     ├── About/
|    │    │     │     └── *.php
|    │    │     ├── Contact/
|    │    │     │     └── *.php
|    │    │     ├── Home/
|    │    │     │     └── *.php
|    │    │     ├── Notes/
|    │    │     │     └── *.php
|    │    │     ├── Project/    
|    │    │           └── *.php                    
|    |
|    ├── Controllers/            # 🎯 Page Controllers
│    │   ├── HomeController.php       # Loads Homepage sections using unified flow`
│    │   ├── AboutController.php      # Loads About page (DB → JSON → fallback`)
│    │   ├── ProjectController.php    # Project pages with pagination + filters`
│    │   ├── NotesController.php      # Notes, categories, tags, pinned notes`
│    │   └── ContactController.php    # Developer contact info
│    │   └── DownloadController.php    # Call Service call for download cv
│    │
|    ├── Helpers/ # These files contain reusable PHP helper functions used across all pages.`)
|    |     ├── helpers.php # Keep code DRY and avoid repeating logic everywhere.`)
|    |     ├── sanitizer.php # Security layer for all user-facing output.`)
│    │     ├── view_helpers.php # Keep view logic clean without mixing PHP logic in templates.`)
│    │     ├── logger.php # Logging utility (normal + cv logs file)
|    |
|    ├── Models/ # Models are responsible for data fetching, fallback values, and business logic. Views NEVER touch SQL; all SQL is inside Models. (DB → Cache → JSON → Fallback)
│    │    ├── AboutModel.php # Loads about sections using DB → JSON → fallback`)
│    │    ├── ContactModel.php # Contact section loader`)
│    │    ├── ContactMessageModel.php # Send Gmail`)
│    │    ├── HomeModel.php    # Homepage model with unified architecture`)
│    │    ├── NoteModel.php     # Notes, categories, tags, pinned notes`)
│    │    ├── ProjectModel.php  # Project list, filters, pagination, tech relations`)
│    │    ├── SkillModel.php # Skill icons + categories with fallback`)
│    │
|    ├── Servicess/ # These are services — reusable backend components.`)
│    |    ├── CacheService.php # JSON caching (fast responses)`)
│    |    ├── MailService.php # Email handler (contact form)`)
│    │    ├── HeaderService.php  # Dynamic header data provider
│    │    └── FooterService.php  # Dynamic footer data provider
│    │    └── CVService.php  # Complete isolation from Downloadcontrollers with rateLimit
│    ├── Core/ # Core contains the foundation of your backend system.`)
│    │    ├── App.php  # Enterprise Router for your MVC system
│    │    ├── Controller.php
│    │    ├── DB.php # PDO connection wrapper
│    │    ├── ErrorHandler.php
│    │    ├── Router.php
│    │
│    ├── views/
│    │    ├── layouts/            # 🖼 Layout System
│    │    │     ├── layout_head.php     # <head> section + CSS/JS inject
│    │    │     ├── layout_foot.php     # Footer scripts
│    │    │     ├── header.php          # Navigation bar
│    │    │     ├── footer.php          # Footer UI
│    │    │
│    │    ├── home/
│    │    │     ├── index.php
│    │    │
│    │    ├── pages/              # 📄 Page Views
│    │    |     ├── about.php
│    │    |     ├── projects.php
│    │    |     ├── notes.php
│    │    |     ├── contact.php
│    │    |     ├── project-detail.php   # single project detail page according to {slug}
│    │    |     ├── note-detail.php   # single note detail page according to {slug}
|    |    |
│    │    ├── components/              # 📄 Page Views
│    │          ├── header.php          # Navigation bar
│    │          ├── footer.php          # Footer UI
│    │
│    │
│    ├── resources/
│       └── defaults/              # JSON fallback files when DB is empty)
│           │
│           ├── header/                 # JSON defaults for header)
│           │   ├── header.json
│           │   ├── navigation.json
|           |
│           ├── footer/                 # JSON defaults for footer)
│           │   ├── footer.json
│           │   ├── links.json
│           │   ├── social.json
|           |
│           ├── about/                 # JSON defaults for About page)
│           │   ├── content.json
│           │   ├── education.json
│           │   ├── experience.json
│           │   ├── hero.json
│           │   ├── skills.json
│           │   ├── stats.json
│           │
│           ├── contact/                 # JSON defaults for About contact)
│           │   ├── content_hero.json
│           │   ├── contact_info.json
│           │   ├── contact_map.json
│           │   ├── contact_socials.json
│           │   ├── conatct_toast.json
│           │
│           ├── home/                  # Defaults for Home page sections)
│           │   ├── about.json
│           │   ├── contact.json
│           │   ├── home.json
│           │   ├── projects.json
│           │   ├── skills.json
│           │
│           ├── notes/                 # Notes system defaults)
│           │   ├── categories.json
│           │   ├── notes.json
│           │   ├── pinned.json
│           │   ├── tags.json
│           │
│           └── projects/              # Project page fallback data)
│               ├── featured.json
│               ├── projects.json
│               ├── tech_list.json
│
│── config/ # Configuration files that initialize everything.`)
│    ├── config.php # Private config (ignored by Git)`)
│    ├── config_example.php # Public-safe template`)
│    ├── paths.php # Auto URL + PATH generator`)
│
│── logs/ # Debugging and monitoring.`)
│     ├── app.log # Runtime logs (ignored by Git)
│     ├── cv.log # Runtime logs (ignored by Git)
│
│
│── public/
│     ├── assets/ # Contains all public-facing files (CSS, JS, images). This folder loads directly in the browser.`)
│     │      ├── css/                        # All stylesheet files`)
│     │      │    ├── about.css
│     │      │    ├── animations.css
│     │      │    ├── footer.css
│     │      │    ├── global.css
│     │      │    ├── header.css
│     │      │    ├── index.css
│     │      │    ├── notes.css
│     │      │    ├── toast.css
│     │      │
│     │      ├── js/                         # All dynamic client-side JS logic`)
│     │      │   ├── about.js
│     │      │   ├── footer.js
│     │      │   ├── header.js
│     │      │   ├── index.js
│     │      │   ├── notes.js
│     │      │   ├── projects.js
│     │      │   ├── scroll-progress.js
│     │      │   ├── tailwind-config-global.js
│     │      │   ├── tailwind-config.js
│     │      │   ├── toast.js
│     │      │
│     │      ├── images/                     # All website images, icons, thumbnails`)
│     │          └── projects/              # All projects images
│     │                 └── portfolio.png
│     │
│     ├── downloads/                # Resume
│     │       └── .pdf
│     ├── bootstrap.php # GLOBAL BOOTSTRAP — loads everything required files for each page
│     ├── index.php # single entry point
│     ├── .htaccess # Router rewrite
│     ├── downloadcv.php # download the CV
│     ├── send_message.php # Send Email (SMTP) & legacy fallback if needed
│
├── routes/
│    └── web.php
├── storage/
│     ├── cache/                  # ⚡ Cached JSON files (ignored from Git)
│         ├── *.json  # Improves performance dramatically`)
│
├── vendor/
│     ├── composer/
│     ├── phpmailer/
│     ├── autoload.php
├── .env
├── .gitignore
├── .htaccess
├── composer.json
├── composer.lock
├── README.md


---

# 🧪 Testing Contact System (New)

You can now test all phases easily:

### ✔ Honeypot Test
Open DevTools → fill hidden field → submit → expect:


### ✔ Rate Limiting Test
Send 2 messages within 60 seconds → expect:


### ✔ Email Delivery Logging Test
Temporarily break EMAIL_PASS in config.php.

Submit form → DB should store:

| email_sent | email_error |
|-----------|-------------|
| 0 | SMTP authentication error… |

### ✔ DB Success Test
Fix email credentials → submit message → DB:

| email_sent | email_error |
|-----------|-------------|
| 1 | NULL |

---

# ⚡ JavaScript Contact Pipeline (Updated)

contact.js now:

- Sends AJAX requests
- Handles loading animation
- Displays dynamic toast messages
- Works even if JavaScript errors occur
- No page reload required
- No dependency on reCAPTCHA for now

---


## 🔐 Sensitive Files (NOT uploaded to GitHub)

  - `.gitignore` protects these:
    ── config/config.php # Project sensitive data (ignored by Git)
    ── logs/ # Logs file (ignored by Git)
    ── uploads/ # User uploads (ignored by Git)
    ── vendor/ # (ignored by Git)
      ── composer/ # (ignored by Git)
      ── phpmailer/ # (ignored by Git)
      ── autoload.php # (ignored by Git)

---


### 1️⃣ Clone the repository
```bash
git clone https://github.com/Userrr404/Portfolio.git
cd Portfolio

2️⃣ Install PHP dependencies (PHPMailer)
Your project requires Composer because PHPMailer is installed via Composer.
    - Open terminal (C:\xampp\htdocs\Portfolio\):
      ── Run:
        - composer install

      ── If PHPMailer is not installed yet:
        - composer require phpmailer/phpmailer

      ── This will generate:
        - vendor/
        - vendor/autoload.php

✔ This file is required for email sending.

### 2️⃣ Create your secure config
cp config/config_example.php config/config.php

    - Then open config.php and set:
      ── 🔧 Database Settings:
          ── DB_HOST=localhost
          ── DB_NAME=portfolio
          ── DB_USER=root
          ── DB_PASS=

      ── 🔧 Email Settings (PHPMailer SMTP Settings)
          ── EMAIL_HOST=smtp.gmail.com
          ── EMAIL_PORT=587
          ── EMAIL_USER=yourgmail@gmail.com
          ── EMAIL_PASS=your-app-password

📌 Important: Gmail no longer accepts normal password.
Use App Password from:
https://myaccount.google.com/apppasswords

### 3️⃣ Import the MySQL database
    - CREATE DATABASE portfolio;
    - Then import SQL file
      ── phpMyAdmin → Import → <your_database>.sql

### 4️⃣ Move project to XAMPP + Run
    - Move the project to:
      ── C:\xampp\htdocs\Portfolio\

    - Start Apache + MySQL
      ── Then open:
        -- http://localhost/Portfolio/public/

```

---

🎯 Final Notes

### This architecture is built for:
  - Real production hosting
  - Zero downtime
  - Automatic fallback safety
  - Speed via caching
  - Clean MVC separation
  - Future scalability (middleware, modules, services)
````
