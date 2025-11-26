<?php
// login.php - verify credentials and start a session
header('Content-Type: application/json');
session_start();
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['status'=>'error','message'=>'Use POST']);
    exit;
}

if(strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false){
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if($email === '' || $password === ''){
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}

require_once __DIR__ . '/db.php';

try{
    $stmt = $pdo->prepare('SELECT id,name,email,password,role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if(!$user || !password_verify($password, $user['password'])){
        echo json_encode(['status'=>'error','message'=>'Invalid credentials']);
        exit;
    }

    // Set minimal session info
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    echo json_encode(['status'=>'ok','message'=>'Login successful','user'=>$_SESSION['user']]);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Server error']);
}

?>