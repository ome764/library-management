Library Management - Phase 1 (Frontend + Placeholders)

Overview:
This repository contains a Phase 1 implementation for a Library Management System focusing on frontend pages (HTML/CSS/JS), PHP backend placeholders, and a SQL schema for Phase 2.

Folder layout:
- `index.html` — Cover page (project info, objectives, features)
- `catalog.html`, `book.html` — Static frontend pages for Phase 1
- `assets/css/style.css` — Basic styles
- `assets/js/main.js` — Minimal client-side JS (search + demo borrow)
- `php/` — PHP placeholders (`db.php`, `register.php`, `login.php`, `borrow.php`)
- `sql/schema.sql` — MySQL schema and sample data
- `testcases.md` — Test case templates and examples

Phase 1 - How to run locally (XAMPP on Windows):
1) Copy this `library-management` folder to your XAMPP `htdocs` folder if not already there.
   Example default path: `C:\xampp\htdocs\library-management`
2) Start Apache and MySQL from XAMPP Control Panel.
3) Import the database schema using phpMyAdmin or the MySQL CLI:

   Using phpMyAdmin:
   - Open `http://localhost/phpmyadmin`
   - Click Import → choose `sql/schema.sql` → Go

   Or using MySQL CLI (PowerShell):
   ```powershell
   cd C:\xampp\mysql\bin; .\mysql.exe -u root < "C:\xampp\htdocs\library-management\sql\schema.sql"
   ```

4) Open the app in your browser:
   - Cover page: `http://localhost/library-management/index.html`
   - Catalog: `http://localhost/library-management/catalog.html`

Authentication (Phase 2 -> implemented for demo):
- Registration page: `http://localhost/library-management/register.html`
- Login page: `http://localhost/library-management/login.html`

Notes about auth implementation:
- PHP endpoints using PDO are in `php/`.
- `php/register.php` hashes passwords using `password_hash` and stores users in the `users` table.
- `php/login.php` validates credentials and starts a session; `php/logout.php` ends the session.
- `php/session.php` returns the logged-in user (used by client JS to show login/logout).

Security notes:
- For production, add HTTPS, stronger session settings, CSRF protection, and input sanitization where needed.

Live demo / SSE:
- A demo Server-Sent Events (SSE) endpoint is available at `php/stream.php` that emits periodic "new_book" events (for demo purposes it picks random books).
- The frontend listens for these events and shows animated toast notifications and updates the catalog live.

Note: `php/stream.php` is intended for demo/live preview only. It keeps the HTTP connection open for the duration of the stream and should be adapted or replaced for production-grade real-time updates (e.g., WebSocket server, message queue).

Notes and next steps (Phase 2):
- Implement real PHP logic for `register.php`, `login.php`, `borrow.php` using the `sql/schema.sql` database.
- Add server-side validation and password hashing (`password_hash`).
- Implement session management and role-based access control for admin/librarian features.
- Add admin panel to manage books, users, and reports.

Submission checklist for Phase 1:
- [x] Project documentation (Problem Statement, Objectives) — in `index.html`
- [x] Frontend implementation (HTML/CSS/JS) — static pages
- [x] Static pages with dummy data — `catalog.html`, `book.html`
- [x] Client-side validation — `assets/js/main.js`
- [x] Responsive design (basic) — `assets/css/style.css`
- [x] Test cases template — `testcases.md`

Replace placeholders (team member names, GitHub URL) and extend the code for Phase 2.
