<?php
// init_database.php - إنشاء الجداول إذا كانت غير موجودة
require_once "db.php";

try {
    // إنشاء جدول الكتب إذا لم يكن موجوداً
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS books (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            year INT NOT NULL,
            isbn VARCHAR(40) DEFAULT NULL,
            copies_total INT NOT NULL DEFAULT 1,
            copies_available INT NOT NULL DEFAULT 1,
            description TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // إنشاء جدول المستخدمين إذا لم يكن موجوداً
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('member','librarian','admin') DEFAULT 'member',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // إنشاء جدول الاستعارات إذا لم يكن موجوداً
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS borrowings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            book_id INT NOT NULL,
            borrowed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            due_at DATETIME NULL,
            returned_at DATETIME NULL,
            fine_amount DECIMAL(8,2) DEFAULT 0.00,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    echo "<h2>✅ تم إنشاء الجداول بنجاح!</h2>";
    echo "<p><a href='../catalog.html'>الذهاب للفهرس</a></p>";

} catch (PDOException $e) {
    echo "<h2>❌ خطأ في إنشاء الجداول:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>