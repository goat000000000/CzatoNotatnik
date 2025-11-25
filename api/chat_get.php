<?php
require_once 'config.php';
require_once 'auth_middleware.php';

require_login();

$stmt = $pdo->query("
    SELECT messages.text, messages.created_at, users.username 
    FROM messages 
    JOIN users ON users.id = messages.user_id
    ORDER BY messages.id DESC 
    LIMIT 50
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'messages' => $messages]);
