<?php
$sub_menu = "100290";
require_once './_common.php';

if ($is_admin != 'super') {
    alert_close('최고관리자만 접근 가능합니다.');
}

$g5['title'] = '메뉴 추가';
require_once G5_PATH . '/head.sub.php';

$new    = isset($_GET['new']) ? clean_xss_tags($_GET['new'], 1, 1) : '';
$code   = isset($_GET['code']) ? (string)preg_replace('/[^0-9a-zA-Z]/', '', $_GET['code']) : '';

// 코드
if ($new == 'new' || !$code) {
    $code = (int)base_convert(substr($code, 0, 2), 36, 10);
    $code += 36;
    $code = base_convert((string)$code, 10, 36);
}
?>

<div id="menu_frm" class="new_win">
    <h1><?php echo $g5['title']; ?></h1>

    <form name="fmenuform" id="fmenuform" class="new_win_con">

        <div class="new_win_desc">
            <label for="me_type">대상선택</label>
            <select name="me_type" id="me_type">
                <option value="">직접입력</option>
                <option value="group">게시판그룹</option>
                <option value="board">게시판</option>
                <option value="content">내용관리</option>
            </select>
        </div>

        <div id="menu_result"></div>

    </form>

</div>

<script>
    $(function() {
        // 초기 로드
        $("#menu_result").load(
            "./menu_form_search.php"
        );

        // 검색 결과 로드 함수
        function menu_result_change(type) {
            $("#menu_result").empty().load(
                "./menu_form_search.php", {
                    type: type
                }
            );
        }

        // 대상선택 변경 시
        $("#me_type").on("change", function() {
            var type = $(this).val();
            menu_result_change(type);
        });

        // [직접 입력] 추가 버튼 클릭 시
        $(document).on("click", "#add_manual", function() {
            var me_name = $.trim($("#me_name").val());
            var me_link = $.trim($("#me_link").val());

            if (!me_name || !me_link) {
                alert("메뉴명과 링크를 입력해주세요.");
                return;
            }

            // 부모창의 함수 호출
            apply_to_parent(me_name, me_link);
        });

        // [검색 결과] 선택 버튼 클릭 시
        $(document).on("click", ".add_select", function() {
            var me_name = $.trim($(this).siblings("input[name='subject[]']").val());
            var me_link = $.trim($(this).siblings("input[name='link[]']").val());

            // 부모창의 함수 호출
            apply_to_parent(me_name, me_link);
        });
    });

    // HTML 특수문자 변환
    function htmlEscape(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // 부모창(opener)으로 데이터 전송 및 창 닫기
    function apply_to_parent(name, link) {
        if (opener && !opener.closed && typeof opener.add_menu_list === 'function') {
            name = htmlEscape(name);
            link = htmlEscape(link);
            
            // 부모창에 정의된 add_menu_list 함수 실행
            opener.add_menu_list(name, link, "<?php echo $code; ?>");
            window.close();
        } else {
            alert("부모창(메뉴설정 화면)을 찾을 수 없거나 연결이 끊어졌습니다.");
        }
    }
</script>

<?php
require_once G5_PATH . '/tail.sub.php';
?>