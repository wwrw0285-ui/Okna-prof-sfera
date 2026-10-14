<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$EMAIL_TO   = 'info@prof-sfera.ru';
$EMAIL_FROM = 'noreply@prof-sfera.ru';
$SITE_NAME  = 'Окна проф-сфера';
$SUBJECT    = 'Новая заявка с сайта «Окна проф-сфера»';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$name    = trim($data['name']    ?? '');
$phone   = trim($data['phone']   ?? '');
$message = trim($data['message'] ?? '');
$source  = trim($data['source']  ?? 'Сайт');

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Укажите имя и телефон']);
    exit;
}
$phoneClean = preg_replace('/\D/', '', $phone);
if (strlen($phoneClean) < 10) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Некорректный номер телефона']);
    exit;
}

$body  = "Новая заявка с сайта «{$SITE_NAME}»\n";
$body .= "----------------------------------------\n";
$body .= "Имя:        {$name}\n";
$body .= "Телефон:    {$phone}\n";
if ($message !== '') $body .= "Сообщение:  {$message}\n";
$body .= "Источник:   {$source}\n";
$body .= "Время:      " . date('d.m.Y H:i:s') . "\n";
$body .= "IP клиента: " . ($_SERVER['REMOTE_ADDR'] ?? 'неизвестен') . "\n";

$subjectEnc = '=?UTF-8?B?' . base64_encode($SUBJECT) . '?=';
$headers   = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "Content-Transfer-Encoding: 8bit";
$headers[] = "From: {$SITE_NAME} <{$EMAIL_FROM}>";
$headers[] = "Reply-To: {$EMAIL_FROM}";
$headers[] = "X-Mailer: PHP/" . phpversion();

$mailSent = @mail($EMAIL_TO, $subjectEnc, $body, implode("\r\n", $headers), "-f{$EMAIL_FROM}");

if ($mailSent) echo json_encode(['ok'=>true]);
else { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'Не удалось отправить заявку. Позвоните: +7 (981) 024-21-77']); }