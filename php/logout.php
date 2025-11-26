<?php
// logout.php - destroy session and redirect to homepage
session_start();
// Unset session and destroy
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// If this is an AJAX call, return JSON
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
    header('Content-Type: application/json');
    echo json_encode(['status'=>'ok','message'=>'Logged out']);
    exit;
}

header('Location: /library-management/index.html');
exit;

?>
