<?php
// fix_books_table.php - إصلاح جدول الكتب
require_once "db.php";

try {
    // إضافة الأعمدة الناقصة إذا كانت غير موجودة
    $columns_to_add = [
        "category" => "ALTER TABLE books ADD COLUMN category VARCHAR(100) NOT NULL DEFAULT 'General'",
        "year" => "ALTER TABLE books ADD COLUMN year INT NOT NULL DEFAULT 2000",
        "isbn" => "ALTER TABLE books ADD COLUMN isbn VARCHAR(40) DEFAULT NULL",
        "copies_total" => "ALTER TABLE books ADD COLUMN copies_total INT NOT NULL DEFAULT 1",
        "copies_available" => "ALTER TABLE books ADD COLUMN copies_available INT NOT NULL DEFAULT 1",
        "description" => "ALTER TABLE books ADD COLUMN description TEXT DEFAULT NULL"
    ];

    echo "<h2>جاري إصلاح جدول الكتب...</h2>";

    foreach ($columns_to_add as $column => $sql) {
        try {
            $pdo->exec($sql);
            echo "<p>✅ تم إضافة العمود: $column</p>";
        } catch (PDOException $e) {
            echo "<p>⏩ العمود $column موجود بالفعل</p>";
        }
    }

    echo "<h2>🎉 تم إصلاح الجدول بنجاح!</h2>";
    echo "<p><a href='seed_books.php'>إضافة البيانات التجريبية</a></p>";

} catch (Exception $e) {
    echo "<h2>❌ خطأ في إصلاح الجدول:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>