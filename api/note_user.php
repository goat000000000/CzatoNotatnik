<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Brak autoryzacji']);
    exit;
}

$userId = $_SESSION['user']['id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === "GET") {

    $stmt = $pdo->prepare("SELECT content FROM notes WHERE user_id = ?");
    $stmt->execute([$userId]);

    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'content' => $note['content'] ?? ''
    ]);
}

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $content = $data['content'] ?? '';

    $stmt = $pdo->prepare("SELECT id FROM notes WHERE user_id = ?");
    $stmt->execute([$userId]);

    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, content) VALUES (?, ?)");
        $stmt->execute([$userId, $content]);
    } else {
        $stmt = $pdo->prepare("UPDATE notes SET content = ? WHERE user_id = ?");
        $stmt->execute([$content, $userId]);
    }

    echo json_encode(['success' => true]);
}
