<?php
// session.php - return current logged-in user (if any) as JSON
header('Content-Type: application/json');
session_start();
if(!empty($_SESSION['user'])){
    echo json_encode(['status'=>'ok','user'=>$_SESSION['user']]);
} else {
    echo json_encode(['status'=>'guest']);
}
?>
