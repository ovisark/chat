<?php
$room_id = isset($_POST['room_id']) ? $_POST['room_id'] : 1;
$mysql = new mysqli("localhost", "root", "", "mycite");

$res = $mysql->query("SELECT m1.id, m1.login, m1.message, m1.reply_to, m2.message AS reply_message, m2.login AS reply_login 
                      FROM messages m1 
                      LEFT JOIN messages m2 ON m1.reply_to = m2.id 
                      WHERE m1.room_id = $room_id 
                      ORDER BY m1.id");

$messages = [];

while ($d = $res->fetch_assoc()) {
    $messages[] = [
        'id' => $d['id'],
        'login' => $d['login'],
        'message' => htmlspecialchars($d['message'], ENT_QUOTES),
        'reply_to' => $d['reply_to'],
        'reply_login' => $d['reply_login'],
        'reply_message' => htmlspecialchars($d['reply_message'], ENT_QUOTES)
    ];
}

header('Content-Type: application/json');
echo json_encode($messages);

?>
