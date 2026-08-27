<?php
include_once dirname(__FILE__).'/../../common.php';
include_once dirname(__FILE__).'/tiktok_rss.lib.php';
$settings = include G5_DATA_PATH.'/tiktok_rss.config.php';
$is_cli = (PHP_SAPI === 'cli');
$provided = isset($_GET['token']) ? (string)$_GET['token'] : '';
$expected = isset($settings['token']) ? (string)$settings['token'] : '';
$valid = $expected !== '' && (function_exists('hash_equals') ? hash_equals($expected, $provided) : $expected === $provided);
if (!$is_cli && !$valid) {
    http_response_code(403); header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok'=>false, 'message'=>'Forbidden')); exit;
}
try {
    $result = skymulti_tiktok_sync();
    if (!$is_cli) header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result).PHP_EOL;
} catch (Exception $e) {
    if (!$is_cli) { http_response_code(500); header('Content-Type: application/json; charset=utf-8'); }
    echo json_encode(array('ok'=>false, 'message'=>$e->getMessage())).PHP_EOL; exit(1);
}
