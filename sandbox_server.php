<?php
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$agent  = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$raw    = file_get_contents('php://input');

function respond(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function parseBody(string $raw): ?array {
    $type = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($type, 'application/json')) {
        $data = json_decode($raw, true);
        return is_array($data) ? $data : null;
    }
    parse_str($raw, $data);
    return $data;
}

function validCar(?array $d): bool {
    return $d && !empty($d['make']) && !empty($d['model'])
        && isset($d['year']) && ctype_digit((string)$d['year']);
}

if ($path === '/' && $method === 'GET') {
    respond(200, ['message' => 'Hello, ' . $agent]);
}

if ($path === '/cars' && $method === 'POST') {
    $data = parseBody($raw);
    if (!validCar($data)) {
        respond(400, ['error' => 'Invalid or missing make, model, year']);
    }
    header('Location: /cars/1');
    respond(201, ['id' => 1] + $data);
}

if (preg_match('#^/cars/(\d+)$#', $path, $m)) {
    if ($method === 'PUT') {
        $data = parseBody($raw);
        if (!validCar($data)) {
            respond(400, ['error' => 'PUT requires all fields: make, model, year']);
        }
        respond(200, ['id' => (int)$m[1]] + $data);
    }
    if ($method === 'DELETE') {
        http_response_code(204);
        exit;
    }
}

respond(404, ['error' => 'Not found']);