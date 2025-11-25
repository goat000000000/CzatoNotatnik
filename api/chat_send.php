<?php
require_once 'config.php';
require_once 'auth_middleware.php';

require_login();

$data = json_decode(file_get_contents("php://input"), true);
$text = trim($data['text'] ?? '');

if ($text === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Pusta wiadomość.']);
    exit;
}

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("INSERT INTO messages (user_id, text) VALUES (?, ?)");
$stmt->execute([$user_id, $text]);

echo json_encode(['success' => true]);
