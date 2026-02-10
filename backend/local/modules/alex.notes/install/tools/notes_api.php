<?php

use Bitrix\Main\Loader;
use Alex\Notes\NoteController;
use Alex\Notes\NoteRepository;

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('PUBLIC_AJAX_MODE', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    return;
}

if (!Loader::includeModule('alex.notes')) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'MODULE_ERROR',
        'message' => 'alex.notes module is not installed',
    ], JSON_UNESCAPED_UNICODE);
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
    return;
}

$controller = new NoteController(new NoteRepository());

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', (string) $uri)));

// /bitrix/tools/alex.notes/notes_api.php/api/notes
$apiIndex = array_search('api', $segments, true);
if ($apiIndex !== false) {
    $segments = array_slice($segments, $apiIndex);
}

$body = json_decode((string) file_get_contents('php://input'), true);
$payload = is_array($body) ? $body : [];

$controller->dispatch($method, $segments, $payload);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
