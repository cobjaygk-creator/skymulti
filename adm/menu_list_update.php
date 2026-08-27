<?php
$sub_menu = "100290";
require_once './_common.php';

check_demo();

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

check_admin_token();

// 이전 메뉴정보 삭제 (전체 갱신 방식)
$sql = " delete from {$g5['menu_table']} ";
sql_query($sql);

$count = isset($_POST['me_name']) ? count($_POST['me_name']) : 0;

// 코드 생성용 변수
$code_seq_1 = 0; // 1차 메뉴 시퀀스
$code_seq_2 = 0; // 2차 메뉴 시퀀스
$last_depth = 0;
$primary_code = ""; // 현재 1차 코드

for ($i = 0; $i < $count; $i++) {
    // 데이터 정제
    $me_name        = strip_tags(trim($_POST['me_name'][$i]));
    $me_link        = trim($_POST['me_link'][$i]);
    $me_target      = trim($_POST['me_target'][$i]);
    $me_use         = trim($_POST['me_use'][$i]);
    $me_mobile_use  = trim($_POST['me_mobile_use'][$i]);
    $me_level       = trim($_POST['me_level'][$i]);
    $me_level_opt   = trim($_POST['me_level_opt'][$i]);
    $depth          = (int)$_POST['me_depth'][$i]; // JS에서 넘겨준 깊이 값 (1 or 2)

    if (!$me_name || !$me_link) continue;

    // 링크 필터링
    $me_link = clean_xss_tags($me_link);

    // me_code 생성 로직
    if ($depth == 1) {
        // 1차 메뉴: 새로운 그룹 시작
        $code_seq_1++; 
        $code_seq_2 = 0; // 2차 초기화
        
        // base36 변환 (10 -> a, 36 -> 10) - 그누보드 표준 방식 준수
        // 기존 코드는 2자리수. (예: 10, 11... 99, a0...)
        // 여기서는 단순화를 위해 base_convert 사용, 시작값은 10진수 36('10')부터 시작한다고 가정하거나 0부터
        // 그누보드 기본: 2자리 문자열.
        $calc_code = base_convert($code_seq_1 * 10, 10, 36); // 간격을 좀 둠
        if(strlen($calc_code) < 2) $calc_code = '0'.$calc_code;
        $primary_code = substr($calc_code, 0, 2);
        
        $me_code = $primary_code;
    } else {
        // 2차 메뉴: 현재 primary_code에 붙임
        $code_seq_2++;
        $sub_code = base_convert($code_seq_2 * 10, 10, 36);
        if(strlen($sub_code) < 2) $sub_code = '0'.$sub_code;
        
        $me_code = $primary_code . substr($sub_code, 0, 2);
    }

    // DB 입력
    $sql = " insert into {$g5['menu_table']}
                set me_code         = '{$me_code}',
                    me_name         = '{$me_name}',
                    me_link         = '{$me_link}',
                    me_target       = '{$me_target}',
                    me_order        = '{$i}', 
                    me_use          = '{$me_use}',
                    me_mobile_use   = '{$me_mobile_use}',
                    me_level        = '{$me_level}',
                    me_level_opt    = '{$me_level_opt}' ";
    sql_query($sql);
}

run_event('admin_menu_list_update');

goto_url('./menu_list.php');
?>