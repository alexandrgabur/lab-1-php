<?php
$base = 'http://localhost:8000';   // для проверки в sandbox.usm.com: 'http://sandbox.usm.com'
$name = 'Имя Фамилия';             // впишите своё имя и фамилию

function sendRequest(string $method, string $url, array $headers = [], ?string $body = null): void {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLINFO_HEADER_OUT    => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $response = curl_exec($ch);
    echo "===== $method $url =====" . PHP_EOL;
    if ($response === false) {
        echo 'Ошибка: ' . curl_error($ch) . PHP_EOL . PHP_EOL;
        return;
    }
    echo '--- Запрос ---' . PHP_EOL . curl_getinfo($ch, CURLINFO_HEADER_OUT);
    echo $body !== null ? $body . PHP_EOL : '';
    echo '--- Ответ ---' . PHP_EOL . $response . PHP_EOL . PHP_EOL;
    curl_close($ch);
}

// 1. GET с User-Agent
sendRequest('GET', $base . '/', ['User-Agent: ' . $name]);

// 2. POST /cars
$post = http_build_query(['make' => 'Toyota', 'model' => 'Corolla', 'year' => 2020]);
sendRequest('POST', $base . '/cars', [
    'User-Agent: ' . $name,
    'Content-Type: application/x-www-form-urlencoded',
], $post);

// 3. PUT /cars/1
$put = json_encode(['make' => 'Toyota', 'model' => 'Corolla', 'year' => 2021]);
sendRequest('PUT', $base . '/cars/1', [
    'User-Agent: ' . $name,
    'Content-Type: application/json',
], $put);

// 4. Примеры ошибок: 400 и 404
sendRequest('POST', $base . '/cars', ['User-Agent: ' . $name], 'make=Toyota');
sendRequest('GET', $base . '/unknown', ['User-Agent: ' . $name]);