<?php
// seed_books.php - إضافة بيانات تجريبية
require_once "db.php";

$books = [
    ['The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', 1925, '9780743273565', 2, 2, 'رواية كلاسيكية في عصر الجاز'],
    ['To Kill a Mockingbird', 'Harper Lee', 'Fiction', 1960, '9780061120084', 3, 3, 'رواية حاصلة على جائزة بوليتزر عن العدالة'],
    ['1984', 'George Orwell', 'Dystopian', 1949, '9780451524935', 2, 2, 'رواية ديستوبية عن المراقبة'],
    ['A Brief History of Time', 'Stephen Hawking', 'Science', 1988, '9780553380163', 1, 1, 'كتاب علم الكونيات'],
    ['The Alchemist', 'Paulo Coelho', 'Philosophy', 1988, '9780061122415', 2, 2, 'قصة رمزية فلسفية'],
    ['Clean Code', 'Robert C. Martin', 'Programming', 2008, '9780132350884', 2, 2, 'دليل حرفية البرمجيات'],
    ['Design Patterns', 'Erich Gamma et al.', 'Programming', 1994, '9780201633610', 1, 1, 'أنماط التصميم في البرمجة'],
    ['Sapiens', 'Yuval Noah Harari', 'History', 2011, '9780062316097', 2, 2, 'تاريخ موجز للبشرية'],
    ['The Pragmatic Programmer', 'Andrew Hunt & David Thomas', 'Programming', 1999, '9780201616224', 1, 1, 'نهج عملي في البرمجة'],
    ['Pride and Prejudice', 'Jane Austen', 'Classic', 1813, '9780141439518', 2, 2, 'رواية رومانسية'],
];

try {
    // حذف البيانات القديمة إذا كانت موجودة
    $pdo->exec("DELETE FROM books");
    
    // إضافة البيانات الجديدة
    $stmt = $pdo->prepare("INSERT INTO books (title, author, category, year, isbn, copies_total, copies_available, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $count = 0;
    foreach ($books as $book) {
        $stmt->execute([
            $book[0], // title
            $book[1], // author
            $book[2], // category
            $book[3], // year
            $book[4], // isbn
            $book[5], // copies_total
            $book[6], // copies_available
            $book[7]  // description
        ]);
        $count++;
    }
    
    echo "<h2>✅ تم إضافة $count كتاب بنجاح!</h2>";
    echo "<p><a href='../catalog.html'>عرض الفهرس</a> | <a href='books.php'>عرض البيانات كـ JSON</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ خطأ في إضافة البيانات:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p><a href='fix_books_table.php'>إصلاح الجدول أولاً</a></p>";
}
?>