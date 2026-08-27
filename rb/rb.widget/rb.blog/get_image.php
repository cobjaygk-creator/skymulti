<?php
// 파일명: get_image.php (위젯 폴더 내에 저장)

// 이미지 URL과 블로그 ID를 GET 파라미터로 받습니다.
$image_url = $_GET['url'] ?? '';
$blog_id = $_GET['id'] ?? '';

// URL 디코딩
$image_url = urldecode($image_url);

if (empty($image_url) || empty($blog_id)) {
    header('HTTP/1.0 404 Not Found');
    exit;
}

// ----------------------------------------------------
// file_get_contents를 이용한 이미지 데이터 가져오기 (Referer 위장)
// ----------------------------------------------------

$image_data = false;

if (ini_get('allow_url_fopen')) {
    // Referer 헤더를 설정하기 위한 Stream Context 생성
    $opts = [
        'http' => [
            'method' => 'GET',
            // 핵심: 리퍼러를 네이버 블로그로 위장
            'header' => "Referer: https://blog.naver.com/{$blog_id}\r\n" .
                        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    // 이미지 데이터 로드 시도
    $image_data = @file_get_contents($image_url, false, $context);
    
    // 로드에 성공하면 MIME 타입을 PHP 내부 함수로 유추 시도
    if ($image_data) {
        $mime_type = 'image/jpeg'; // 기본값 설정
        
        // PHP finfo 확장 기능이 활성화된 경우 MIME 타입 정확히 감지
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type_guess = finfo_buffer($finfo, $image_data);
            finfo_close($finfo);
            
            if (strpos($mime_type_guess, 'image/') === 0) {
                $mime_type = $mime_type_guess;
            }
        }
        
        // ----------------------------------------------------
        // 이미지 출력
        // ----------------------------------------------------
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . strlen($image_data));
        header('Cache-Control: max-age=86400, public'); 
        echo $image_data;
        exit;
    }
}

// 로드 실패 시
header('HTTP/1.0 404 Not Found');
exit;
?>