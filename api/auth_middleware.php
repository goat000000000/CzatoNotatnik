<?php
require_once 'config.php';

function require_login() {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Musisz być zalogowany.']);
        exit;
    }
}

function require_teacher() {
    require_login();
    if ($_SESSION['user']['role'] !== 'teacher') {
        http_response_code(403);
        echo json_encode(['error' => 'Brak uprawnień.']);
        exit;
    }
}
