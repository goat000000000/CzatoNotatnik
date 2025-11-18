<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        $pdo->prepare("UPDATE users SET last_online = NOW() WHERE id = ?")->execute([$user['id']]);

        echo json_encode([
            'success' => true,
            'role' => $user['role'],
            'name' => $user['name']
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Zły login lub hasło.']);
    }

} elseif ($method === 'GET') {
    if (isset($_SESSION['user'])) {
        echo json_encode(['logged' => true, 'user' => $_SESSION['user']]);
    } else {
        echo json_encode(['logged' => false]);
    }

} elseif ($method === 'DELETE') {
    session_destroy();
    echo json_encode(['success' => true]);
}
