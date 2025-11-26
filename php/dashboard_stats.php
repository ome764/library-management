<?php
// dashboard_stats.php - return dashboard statistics
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

try{
    // إحصائيات الكتب
    $books_total = $pdo->query('SELECT COUNT(*) as total FROM books')->fetch()['total'];
    
    // إحصائيات المستخدمين
    $users_total = $pdo->query('SELECT COUNT(*) as total FROM users')->fetch()['total'];
    
    // الكتب المستعارة حالياً
    $borrowed_books = $pdo->query('SELECT COUNT(*) as total FROM borrowings WHERE returned_at IS NULL')->fetch()['total'];
    
    // الحجوزات النشطة
    $reservations = $pdo->query('SELECT COUNT(*) as total FROM reservations WHERE status = "waiting"')->fetch()['total'];
    
    // الكتب المتاحة حالياً
    $available_books = $pdo->query('SELECT SUM(copies_available) as total FROM books')->fetch()['total'];
    
    echo json_encode([
        'status'=>'ok',
        'stats'=>[
            'total_books' => (int)$books_total,
            'total_users' => (int)$users_total,
            'borrowed_books' => (int)$borrowed_books,
            'reservations' => (int)$reservations,
            'available_books' => (int)$available_books
        ]
    ]);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Server error']);
}
?>