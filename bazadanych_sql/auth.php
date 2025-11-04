<?php
require_once 'config.php';

// POST /login { username, password }
// GET /logout

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, password_hash, role FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        // update last_online
        $pdo->prepare('UPDATE users SET last_online = NOW() WHERE id = ?')->execute([$user['id']]);
        echo json_encode(['ok' => true, 'user' => ['id'=>$user['id'], 'name'=>$user['name'], 'role'=>$user['role']]]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
    exit;
}

if ($method === 'GET') {
    // logout? or session check
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        session_destroy();
        echo json_encode(['ok' => true]);
        exit;
    }
    // session info
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['user' => ['id'=>$_SESSION['user_id'], 'name'=>$_SESSION['name'], 'role'=>$_SESSION['role']]]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
    }
    exit;
}


function require_auth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
}
function require_teacher() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
}