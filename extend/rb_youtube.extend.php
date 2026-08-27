<?php
if (!defined('_GNUBOARD_')) exit;

// 쇼츠 URL 여부 판별
function isYouTubeShorts($input) {
    // /shorts/VIDEO_ID 형태면 쇼츠로 간주
    return (bool)preg_match('#(?:https?://)?(?:www\.)?youtube\.com/shorts/([a-zA-Z0-9_-]{11})#', $input);
}

// ID 추출 함수
function getYouTubeVideoId($input) {
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) return $input;

    preg_match(
        '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|shorts\/|.+\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
        $input,
        $m
    );
    return isset($m[1]) ? $m[1] : null;
}
