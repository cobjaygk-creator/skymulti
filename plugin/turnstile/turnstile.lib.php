<?php
if (!defined('_GNUBOARD_')) exit;

function turnstile_config()
{
    static $settings = null;

    if ($settings !== null) {
        return $settings;
    }

    $settings = array(
        'enabled' => false,
        'site_key' => '',
        'secret_key' => '',
        'allowed_hostnames' => array(),
        'action' => 'gnuboard_form',
        'timeout' => 5
    );

    $config_file = G5_DATA_PATH.'/turnstile.config.php';
    if (is_file($config_file)) {
        $local = include $config_file;
        if (is_array($local)) {
            $settings = array_merge($settings, $local);
        }
    }

    return $settings;
}

function captcha_html($class = 'captcha')
{
    $settings = turnstile_config();
    $site_key = isset($settings['site_key']) ? trim($settings['site_key']) : '';

    if (empty($settings['enabled']) || $site_key === '') {
        return '<fieldset id="captcha" class="captcha turnstile-captcha"><p class="turnstile-error">자동등록방지 설정이 완료되지 않았습니다. 관리자에게 문의해 주세요.</p></fieldset>';
    }

    $action = isset($settings['action']) ? $settings['action'] : 'gnuboard_form';
    $html = '<fieldset id="captcha" class="captcha turnstile-captcha">';
    $html .= '<legend>자동등록방지</legend>';
    $html .= '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
    $html .= '<script src="'.G5_CAPTCHA_URL.'/turnstile.js"></script>';
    $html .= '<div class="cf-turnstile" data-sitekey="'.htmlspecialchars($site_key, ENT_QUOTES, 'UTF-8').'" data-action="'.htmlspecialchars($action, ENT_QUOTES, 'UTF-8').'" data-theme="auto" data-language="ko"></div>';
    $html .= '<p class="turnstile-guide">자동등록방지 확인 후 작성할 수 있습니다.</p>';
    $html .= '</fieldset>';

    return $html;
}

function chk_captcha_js()
{
    return "if (!chk_captcha()) return false;\n";
}

function chk_captcha()
{
    $settings = turnstile_config();
    if (empty($settings['enabled'])) {
        return false;
    }

    $secret = isset($settings['secret_key']) ? trim($settings['secret_key']) : '';
    $token = isset($_POST['cf-turnstile-response']) ? trim($_POST['cf-turnstile-response']) : '';
    if ($secret === '' || $token === '' || strlen($token) > 2048 || !function_exists('curl_init')) {
        return false;
    }

    $post = array(
        'secret' => $secret,
        'response' => $token
    );
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $post['remoteip'] = $_SERVER['REMOTE_ADDR'];
    }

    $timeout = isset($settings['timeout']) ? (int)$settings['timeout'] : 5;
    if ($timeout < 2 || $timeout > 10) {
        $timeout = 5;
    }

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post, '', '&'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    $body = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $status !== 200) {
        return false;
    }

    $result = json_decode($body, true);
    if (!is_array($result) || empty($result['success'])) {
        return false;
    }

    $expected_action = isset($settings['action']) ? (string)$settings['action'] : 'gnuboard_form';
    if ($expected_action !== '' && (!isset($result['action']) || !hash_equals($expected_action, (string)$result['action']))) {
        return false;
    }

    $allowed = isset($settings['allowed_hostnames']) && is_array($settings['allowed_hostnames'])
        ? $settings['allowed_hostnames'] : array();
    $hostname = isset($result['hostname']) ? strtolower(trim($result['hostname'])) : '';
    $allowed = array_map('strtolower', array_map('trim', $allowed));
    if (!$allowed || $hostname === '' || !in_array($hostname, $allowed, true)) {
        return false;
    }

    return true;
}
