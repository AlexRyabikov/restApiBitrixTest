<?php

declare(strict_types=1);

use App\Database;
use App\NoteController;
use App\NoteRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/NoteRepository.php';
require_once __DIR__ . '/../src/NoteController.php';

$bitrixProlog = __DIR__ . '/../bitrix/modules/main/include/prolog_before.php';
if (file_exists($bitrixProlog)) {
    require_once $bitrixProlog;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$controller = new NoteController(new NoteRepository(Database::connection()));

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', (string) $uri)));

if (count($segments) >= 2 && $segments[0] === 'api' && $segments[1] === 'notes') {
    $id = isset($segments[2]) ? (int) $segments[2] : null;
    $body = json_decode((string) file_get_contents('php://input'), true);
    $payload = is_array($body) ? $body : [];

    if ($method === 'GET' && $id === null) {
        $controller->list();
        exit;
    }

    if ($method === 'GET' && $id !== null) {
        $controller->view($id);
        exit;
    }

    if ($method === 'POST' && $id === null) {
        $controller->create($payload);
        exit;
    }

    if ($method === 'PUT' && $id !== null) {
        $controller->update($id, $payload);
        exit;
    }

    if ($method === 'DELETE' && $id !== null) {
        $controller->delete($id);
        exit;
    }
}

http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'error' => 'NOT_FOUND',
    'message' => 'Route not found',
], JSON_UNESCAPED_UNICODE);
