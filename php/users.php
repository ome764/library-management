<?php
// users.php - return users list as JSON
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

try{
    $stmt = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
    $users = $stmt->fetchAll();
    echo json_encode(['status'=>'ok','users'=>$users]);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Server error']);
}
?>