<?php
require_once 'config.php';
require_once 'auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query('SELECT content, updated_at FROM board WHERE id = 1');
    $content = $stmt->fetch(PDO::FETCH_ASSOC);
    // If row missing, create default
    if (!$content) {
        $pdo->prepare('INSERT INTO board (id, content) VALUES (1, "")')->execute();
        $content = ['content'=>'', 'updated_at'=>null];
    }
    echo json_encode(['board' => $content]);
    exit;
}

if ($method === 'POST') {
    require_auth();
    require_teacher(); // only teacher edits
    $data = json_decode(file_get_contents('php://input'), true);
    $content = $data['content'] ?? '';
    $stmt = $pdo->prepare('UPDATE board SET content = ?, updated_at = NOW() WHERE id = 1');
    $stmt->execute([$content]);
    echo json_encode(['ok' => true, 'content' => $content]);
    exit;
}
