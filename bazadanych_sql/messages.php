<?php
require_once 'config.php';
require_once 'auth.php'; // for middleware functions

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
    $stmt = $pdo->prepare('SELECT m.id, m.text, m.created_at, u.id as user_id, u.name FROM messages m JOIN users u ON m.user_id = u.id WHERE m.id > ? ORDER BY m.id ASC');
    $stmt->execute([$last_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['messages' => $rows]);
    exit;
}

if ($method === 'POST') {
    require_auth();
    $data = json_decode(file_get_contents('php://input'), true);
    $text = trim($data['text'] ?? '');
    if ($text === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Empty text']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO messages (user_id, text) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], $text]);
    $id = $pdo->lastInsertId();
    // optional: return inserted message
    $stmt = $pdo->prepare('SELECT m.id, m.text, m.created_at, u.id as user_id, u.name FROM messages m JOIN users u ON m.user_id = u.id WHERE m.id = ?');
    $stmt->execute([$id]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['message' => $msg]);
    exit;
}
