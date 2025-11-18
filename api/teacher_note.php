<?php
require_once 'config.php';
require_once 'auth_middleware.php';

require_login();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {

    $stmt = $pdo->query("SELECT content FROM board WHERE id = 1 LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'content' => $row['content'] ?? ""
    ]);
    exit;
}


if ($method === 'POST') {

    require_teacher(); 

    $data = json_decode(file_get_contents('php://input'), true);
    $content = $data['content'] ?? '';

    $pdo->query("INSERT IGNORE INTO board (id, content) VALUES (1, '')");

    $stmt = $pdo->prepare("UPDATE board SET content=? WHERE id=1");
    $stmt->execute([$content]);

    echo json_encode(['success' => true]);
    exit;
}
