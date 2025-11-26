<?php
// stream.php — simple Server-Sent Events (SSE) demo stream
// Sends periodic "new_book" events with a random book from the DB.
// This is for demo/live-update visual effect only.

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
set_time_limit(0);
require_once __DIR__ . '/db.php';

// helper to send an event
function send_event($event, $data){
    echo "event: {$event}\n";
    echo "data: " . json_encode($data) . "\n\n";
    @ob_flush();
    @flush();
}

// Send a greeting immediately
send_event('message', ['text' => 'Live updates connected']);

$count = 0;
// For demo, send up to 12 updates (will keep connection open while Apache allows)
while($count < 12){
    try{
        // pick a random book for demo
        $row = $pdo->query('SELECT id,title FROM books ORDER BY RAND() LIMIT 1')->fetch();
        if($row){
            send_event('new_book', ['id' => (int)$row['id'], 'title' => $row['title']]);
        }
    } catch(Exception $e){
        send_event('error', ['message'=>'Stream error']);
    }
    $count++;
    sleep(8); // wait 8 seconds between events
}

// close stream
send_event('message', ['text' => 'Live updates ended']);
?>
