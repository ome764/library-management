<?php
// books.php - return books list or a single book as JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/db.php';

// Debug logging
error_log("Books API called: " . date('Y-m-d H:i:s'));

try {
    // Check database connection
    if (!$pdo) {
        throw new Exception('فشل الاتصال بقاعدة البيانات');
    }
    
    // Test query to check if books table exists
    $test = $pdo->query("SHOW TABLES LIKE 'books'")->fetch();
    if (!$test) {
        throw new Exception('جدول الكتب غير موجود في قاعدة البيانات');
    }

    // Handle single book request
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        error_log("Fetching book with ID: " . $id);
        
        $stmt = $pdo->prepare('SELECT id, title, author, isbn, copies_total, copies_available, description, category, year FROM books WHERE id = ?');
        $stmt->execute([$id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($book) {
            echo json_encode([
                'status' => 'ok',
                'book' => $book
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => 'الكتاب غير موجود'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Handle search or list all books
    $q = trim($_GET['q'] ?? '');
    error_log("Search query: " . $q);

    if ($q !== '') {
        $like = "%" . $q . "%";
        $stmt = $pdo->prepare('SELECT id, title, author, isbn, copies_total, copies_available, description, category, year 
                              FROM books 
                              WHERE title LIKE ? OR author LIKE ? OR category LIKE ?
                              ORDER BY title');
        $stmt->execute([$like, $like, $like]);
    } else {
        $stmt = $pdo->query('SELECT id, title, author, isbn, copies_total, copies_available, description, category, year 
                            FROM books 
                            ORDER BY title');
    }
    
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count total books
    $total = $pdo->query('SELECT COUNT(*) as total FROM books')->fetch()['total'];
    
    error_log("Found " . count($books) . " books out of " . $total . " total");
    
    echo json_encode([
        'status' => 'ok',
        'books' => $books,
        'total' => $total
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>