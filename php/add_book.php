<?php
// php/add_book.php
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // جلب البيانات من الفورم
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $year = intval($_POST['year'] ?? date('Y'));
    
    // إضافة حقول افتراضية للبيانات الناقصة
    $isbn = '';
    $copies_total = 1;
    $copies_available = 1;
    $description = '';

    if ($title && $author && $category && $year) {
        try {
            $stmt = $pdo->prepare("INSERT INTO books (title, author, category, year, isbn, copies_total, copies_available, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([
                $title, 
                $author, 
                $category, 
                $year,
                $isbn,
                $copies_total,
                $copies_available,
                $description
            ]);
            
            if ($success) {
                // إرجاع JSON علشان الـ AJAX
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'تم إضافة الكتاب بنجاح!',
                    'book_id' => $pdo->lastInsertId()
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'خطأ في إضافة الكتاب.'
                ]);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'جميع الحقول مطلوبة.'
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'طلب غير صالح.'
    ]);
}
?>