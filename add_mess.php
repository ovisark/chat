<?php
if (isset($_POST['mess']) && trim($_POST['mess']) != "") {
    session_start();

    // Получение и проверка данных
    $mess = trim($_POST['mess']);
    $reply_to = isset($_POST['reply_to']) && !empty($_POST['reply_to']) ? (int)$_POST['reply_to'] : NULL;
    $room_id = isset($_POST['room_id']) && !empty($_POST['room_id']) ? (int)$_POST['room_id'] : 1;
    $login = isset($_SESSION['login']) ? $_SESSION['login'] : 'Anonymous';

    // Подключение к базе данных
    $mysql = new mysqli("localhost", "root", "", "mycite");

    // Проверка на ошибки подключения
    if ($mysql->connect_error) {
        die("Connection failed: " . $mysql->connect_error);
    }

    // Подготовка SQL запроса
    $stmt = $mysql->prepare("INSERT INTO messages (login, message, reply_to, room_id) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $mysql->error);
    }

    // Привязка параметров
    if (!$stmt->bind_param("ssii", $login, $mess, $reply_to, $room_id)) {
        die("Binding parameters failed: " . $stmt->error);
    }

    // Выполнение запроса
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    // Закрытие соединения
    $stmt->close();
    $mysql->close();
}
?>
