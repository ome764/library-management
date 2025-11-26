echo json_encode(['status'=>'ok','message'=>'Borrow request simulated (Phase 1)']);
<?php
// borrow.php - create borrowing record for logged-in user and decrement availability
header('Content-Type: application/json');
session_start();
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['status'=>'error','message'=>'Use POST']);
    exit;
}

require_once __DIR__ . '/db.php';

// Accept JSON body
if(strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false){
    $data = json_decode(file_get_contents('php://input'), true);
} else {
    $data = $_POST;
}

$book_id = (int)($data['book_id'] ?? 0);
if(!$book_id){
    echo json_encode(['status'=>'error','message'=>'Missing book_id']);
    exit;
}

if(empty($_SESSION['user']['id'])){
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    exit;
}

$user_id = (int)$_SESSION['user']['id'];

try{
    // transaction: check availability, insert borrowing, decrement copies
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT copies_available FROM books WHERE id = ? FOR UPDATE');
    $stmt->execute([$book_id]);
    $row = $stmt->fetch();
    if(!$row){
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['status'=>'error','message'=>'Book not found']);
        exit;
    }

    if($row['copies_available'] < 1){
        $pdo->rollBack();
        echo json_encode(['status'=>'error','message'=>'No copies available']);
        exit;
    }

    // Insert borrowing: due date 14 days from now
    $due = (new DateTime('+14 days'))->format('Y-m-d H:i:s');
    $ins = $pdo->prepare('INSERT INTO borrowings (user_id,book_id,borrowed_at,due_at) VALUES (?,?,NOW(),?)');
    $ins->execute([$user_id,$book_id,$due]);

    $upd = $pdo->prepare('UPDATE books SET copies_available = copies_available - 1 WHERE id = ?');
    $upd->execute([$book_id]);

    $pdo->commit();
    echo json_encode(['status'=>'ok','message'=>'Book borrowed','due_at'=>$due]);
} catch (Exception $e){
    if($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Server error']);
}

?>