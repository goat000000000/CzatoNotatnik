<?php
require_once 'config.php';
require_once 'auth.php';
require_auth();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT id, content, updated_at FROM notes WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$note) {
        echo json_encode(['note' => ['id'=>null,'content'=>'','updated_at'=>null]]);
    } else {
        echo json_encode(['note' => $note]);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $content = $data['content'] ?? '';
    // upsert
    $stmt = $pdo->prepare('SELECT id FROM notes WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        $pdo->prepare('UPDATE notes SET content = ?, updated_at = NOW() WHERE user_id = ?')->execute([$content, $_SESSION['user_id']]);
    } else {
        $pdo->prepare('INSERT INTO notes (user_id, content) VALUES (?, ?)')->execute([$_SESSION['user_id'], $content]);
    }
    echo json_encode(['ok' => true, 'content' => $content]);
    exit;
}
