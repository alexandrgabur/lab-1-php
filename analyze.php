<?php
// Запуск: php analyze.php https://en.wikipedia.org/wiki/HTTP
$url = $argv[1] ?? 'https://en.wikipedia.org/wiki/HTTP';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_ENCODING       => '',
    CURLOPT_USERAGENT      => 'Имя Фамилия',
    CURLOPT_HTTPHEADER     => ['Accept: text/html', 'Accept-Language: en'],
    CURLINFO_HEADER_OUT    => true,
]);

$response = curl_exec($ch);
if ($response === false) {
    die('Ошибка: ' . curl_error($ch) . PHP_EOL);
}

$headerSize  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$respHeaders = substr($response, 0, $headerSize);
$body        = substr($response, $headerSize);

echo '=== URL ===' . PHP_EOL . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) . PHP_EOL;
echo '=== Статус ===' . PHP_EOL . curl_getinfo($ch, CURLINFO_RESPONSE_CODE) . PHP_EOL;
echo '=== Заголовки запроса ===' . PHP_EOL . curl_getinfo($ch, CURLINFO_HEADER_OUT);
echo '=== Заголовки ответа ===' . PHP_EOL . $respHeaders;
echo '=== Тело ответа ===' . PHP_EOL . strlen($body) . ' байт' . PHP_EOL;
echo substr($body, 0, 300) . PHP_EOL;
curl_close($ch);