<?php
// register.php - register a new user into the `users` table
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['status'=>'error','message'=>'Use POST']);
    exit;
}

// Accept form-encoded or JSON body
if(strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false){
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = in_array($data['role'] ?? 'member', ['member','librarian','admin']) ? $data['role'] : 'member';

if($name === '' || $email === '' || $password === ''){
    echo json_encode(['status'=>'error','message'=>'Missing fields']);
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo json_encode(['status'=>'error','message'=>'Invalid email']);
    exit;
}

require_once __DIR__ . '/db.php';

try{
    // Check if email exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if($stmt->fetch()){
        echo json_encode(['status'=>'error','message'=>'Email already registered']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)');
    $ins->execute([$name,$email,$hash,$role]);

    echo json_encode(['status'=>'ok','message'=>'Registration successful']);
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Server error']);
}

?>