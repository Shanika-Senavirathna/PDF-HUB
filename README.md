# 📄 PDF Hub Website

A web-based document management and sharing system built using **PHP, MySQL, HTML, and CSS** in **Visual Studio Code**. This application allows users to register, manage personal PDF files, share documents publicly, and download files securely.

---

## ✨ Key Features
- 🔐 **User Authentication:** Secure User Registration (`register.php`) and Login system (`login.php`, `logout.php`).
- 📂 **Personal PDF Dashboard:** Upload, view, and manage private PDF documents (`personal.php`).
- 🌐 **Public Documents Hub:** Access and browse publicly shared PDF documents (`public.php`).
- 🔗 **File Sharing:** Share PDF files seamlessly across the platform (`share.php`).
- ⬇️ **Download & Delete:** Secure options to download (`download.php`) or delete (`delete.php`) PDF files.

---

## 🛠️ Tech Stack
- **IDE / Editor:** Visual Studio Code (VS Code)
- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Environment:** XAMPP / WAMP Server

---

## 🚀 How to Setup and Run

1. **Download/Clone Repository:**
   - Extract this project into your local server directory (e.g., `C:/xampp/htdocs/pdf-hub`).

2. **Database Setup:**
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
   - Create a new database (e.g., `pdf_hub_db`).
   - Import the `database.sql` file provided in this repository.

3. **Configure Connection:**
   - Verify database configuration settings inside the `config/` folder.

4. **Run Application:**
   - Open your web browser and navigate to:  
     `http://localhost/pdf-hub/login.php`
