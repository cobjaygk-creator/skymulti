<?php
$sub_menu = "100290";
require_once './_common.php';

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

// 메뉴테이블 생성 체크
if (!isset($g5['menu_table'])) {
    die('<meta charset="utf-8">dbconfig.php 파일에 <strong>$g5[\'menu_table\'] = G5_TABLE_PREFIX.\'menu\';</strong> 를 추가해 주세요.');
}

$sql = " select * from {$g5['menu_table']} order by me_code ";
$result = sql_query($sql);

$g5['title'] = "메뉴설정";
require_once './admin.head.php';

add_stylesheet('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">');
?>

<style>
/* 전체 레이아웃 */
.menu-manager-wrap { display: flex; gap: 20px; align-items: flex-start; margin-top: 20px; }
.menu-tree-panel { width:600px; background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; min-height: 600px; }
.menu-detail-panel { flex:1; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; padding: 20px; position: sticky; top: 20px; }

/* 툴바 */
.tree-toolbar { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; display: flex; gap: 5px; }
.tree-toolbar .btn_tool { padding: 5px 12px; border: 1px solid #ccc; background: #fff; cursor: pointer; border-radius: 3px; font-size: 13px; color:#333; display:flex; align-items:center; gap:5px; }
.tree-toolbar .btn_tool:hover { background: #f1f1f1; border-color:#bbb; }
.tree-toolbar .btn_tool:disabled { color: #aaa; background: #f9f9f9; border-color: #eee; cursor: not-allowed; opacity: 0.7; }
.tree-toolbar .btn_red { color: #e84118; border-color: #e84118; margin-left: auto; }
.tree-toolbar .btn_red:hover { background: #fff5f5; }

/* 트리 리스트 */
#menu-sortable { list-style: none; padding: 0; margin: 0; }
.menu-item { 
    background: #fff; border: 1px solid #e5e5e5; margin-bottom: 5px; padding: 10px; 
    cursor: pointer; transition: all 0.2s; display: flex; align-items: center; border-radius: 3px;
    position: relative;
}
.menu-item:hover { border-color: #bbb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.menu-item.active { border-color: #253dbe; background: #f4f6ff; z-index: 1; }
.menu-item.active .menu-text { font-weight: bold; color: #253dbe; }

/* 1차, 2차 구분 (들여쓰기 시각화) */
.menu-item.depth-1 { margin-left: 0; border-left: 4px solid #555; }
.menu-item.depth-2 { margin-left: 40px; border-left: 4px solid #bbb; }
.menu-item .menu-icon { margin-right: 10px; color: #ccc; cursor: move; }
.menu-item.depth-2 .menu-icon { transform: rotate(90deg) scale(0.8); }

/* 우측 폼 스타일 */
.detail-row { margin-bottom: 15px; }
.detail-row label { display: block; font-weight: bold; margin-bottom: 5px; font-size: 13px; color: #555; }
.detail-row input[type="text"], .detail-row select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; box-sizing: border-box; }
.detail-row .help-block { font-size: 11px; color: #888; margin-top: 4px; }
.empty-msg { text-align: center; color: #999; margin-top: 100px; }
</style>

<div class="local_desc01 local_desc">
    <p>
        <strong>[메뉴관리]</strong> 좌측 메뉴를 클릭하여 우측에서 설정을 변경하세요.<br>
        <strong>[구조변경]</strong> 드래그앤드롭 또는 상단 버튼(화살표, 들여쓰기)을 사용하세요.<br>
        <strong>[추가팁]</strong> 1차 메뉴를 선택하고 '추가'를 누르면 자동으로 하위 메뉴가 생성됩니다.
    </p>
</div>

<form name="fmenulist" id="fmenulist" method="post" action="./menu_list_update.php" onsubmit="return fmenulist_submit(this);">
    <input type="hidden" name="token" value="">

    <div class="menu-manager-wrap">
        <div class="menu-tree-panel">
            <div class="tree-toolbar">
                <button type="button" class="btn_tool" id="btn-up" onclick="menu_action('up')" title="위로" disabled><i class="fas fa-arrow-up"></i></button>
                <button type="button" class="btn_tool" id="btn-down" onclick="menu_action('down')" title="아래로" disabled><i class="fas fa-arrow-down"></i></button>
                <div style="width:1px; background:#ddd; margin:0 5px;"></div>
                <button type="button" class="btn_tool" id="btn-outdent" onclick="menu_action('outdent')" title="내어쓰기 (1차메뉴로)" disabled><i class="fas fa-outdent"></i> 1차메뉴로</button>
                <button type="button" class="btn_tool" id="btn-indent" onclick="menu_action('indent')" title="들여쓰기 (2차메뉴로)" disabled><i class="fas fa-indent"></i> 2차메뉴로</button>
                <div style="width:1px; background:#ddd; margin:0 5px;"></div>
                <button type="button" class="btn_tool" onclick="add_new_menu()" title="메뉴 추가"><i class="fas fa-plus"></i> 추가</button>
                <button type="button" class="btn_tool btn_red" onclick="delete_menu()" title="삭제"><i class="fas fa-trash"></i> 삭제</button>
            </div>
            
            <ul id="menu-sortable">
                <?php
                for ($i = 0; $row = sql_fetch_array($result); $i++) {
                    // 코드 길이에 따라 뎁스 결정 (2자리=1차, 4자리=2차)
                    $depth = (strlen($row['me_code']) == 2) ? 1 : 2;
                    $me_name = get_sanitize_input($row['me_name']);
                ?>
                <li class="menu-item depth-<?php echo $depth; ?>" id="item_<?php echo $i; ?>">
                    <span class="menu-icon"><i class="fas fa-bars"></i></span>
                    <span class="menu-text"><?php echo $me_name; ?></span>
                    
                    <input type="hidden" name="me_name[]" value="<?php echo $me_name; ?>" class="h-name">
                    <input type="hidden" name="me_link[]" value="<?php echo $row['me_link']; ?>" class="h-link">
                    <input type="hidden" name="me_target[]" value="<?php echo $row['me_target']; ?>" class="h-target">
                    <input type="hidden" name="me_order[]" value="<?php echo $row['me_order']; ?>" class="h-order">
                    <input type="hidden" name="me_use[]" value="<?php echo $row['me_use']; ?>" class="h-use">
                    <input type="hidden" name="me_mobile_use[]" value="<?php echo $row['me_mobile_use']; ?>" class="h-mobile-use">
                    <input type="hidden" name="me_level[]" value="<?php echo $row['me_level']; ?>" class="h-level">
                    <input type="hidden" name="me_level_opt[]" value="<?php echo isset($row['me_level_opt']) ? $row['me_level_opt'] : 1; ?>" class="h-level-opt">
                    <input type="hidden" name="me_depth[]" value="<?php echo $depth; ?>" class="h-depth">
                </li>
                <?php } ?>
            </ul>
        </div>

        <div class="menu-detail-panel">
            <h3 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:10px;">상세 설정</h3>
            <div id="detail-form-area" style="display:none; margin-top: 15px;">
                <div class="detail-row">
                    <label>메뉴명</label>
                    <input type="text" id="edit_name" onkeyup="sync_to_tree()">
                </div>
                <div class="detail-row">
                    <label>링크 <button type="button" class="btn btn_03 btn_sm" onclick="open_link_search()" style="float:right; margin-top:-10px; margin-bottom:5px;">검색</button></label>
                    <input type="text" id="edit_link" onkeyup="sync_to_tree()">
                    <p class="help-block">http:// 포함 입력 (예: /bbs/board.php?bo_table=free)</p>
                </div>
                <div class="detail-row">
                    <label>새창 여부</label>
                    <select id="edit_target" onchange="sync_to_tree()">
                        <option value="self">현재창</option>
                        <option value="blank">새창</option>
                    </select>
                </div>
                <div class="detail-row" style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>PC 사용</label>
                        <select id="edit_use" onchange="sync_to_tree()">
                            <option value="1">사용함</option>
                            <option value="0">사용안함</option>
                        </select>
                    </div>
                    <div style="flex:1;">
                        <label>모바일 사용</label>
                        <select id="edit_mobile_use" onchange="sync_to_tree()">
                            <option value="1">사용함</option>
                            <option value="0">사용안함</option>
                        </select>
                    </div>
                </div>
                <div class="detail-row">
                     <label>권한 설정</label>
                     <div style="display:flex; gap:5px;">
                        <?php echo str_replace('me_level', 'edit_level', get_member_level_select('me_level', 1, $member['mb_level'], 1)); ?>
                        <select id="edit_level_opt" onchange="sync_to_tree()">
                            <option value="1">이상 접근</option>
                            <option value="2">만 접근</option>
                        </select>
                     </div>
                </div>
            </div>
            <div id="no-selection-msg" class="empty-msg">
                <i class="fas fa-mouse-pointer" style="font-size:30px; margin-bottom:10px; display:block;"></i>
                좌측 목록에서 메뉴를 선택하거나<br>추가 버튼을 눌러주세요.
            </div>
        </div>
    </div>

    <div class="btn_fixed_top">
        <input type="submit" value="저장" class="btn_submit btn">
    </div>
</form>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
    // 1. Sortable 초기화
    $("#menu-sortable").sortable({
        placeholder: "ui-state-highlight",
        axis: "y",
        handle: ".menu-icon", // 아이콘으로만 드래그 가능하게 하면 클릭 충돌 방지에 좋음 (선택사항)
        start: function(e, ui){
            ui.placeholder.height(ui.item.height());
        },
        update: function(event, ui) {
            // 순서 변경 후 버튼 상태 업데이트
            update_toolbar();
        }
    });
    // $("#menu-sortable").disableSelection(); // 입력폼 포커스 문제를 위해 비활성화 권장 안함

    // 2. 아이템 클릭 시 활성화 및 툴바 갱신
    $(document).on("click", ".menu-item", function(e) {
        // 드래그 중 클릭 방지
        if($(this).hasClass('ui-sortable-helper')) return;

        $(".menu-item").removeClass("active");
        $(this).addClass("active");
        
        load_detail($(this));
        update_toolbar(); // 버튼 활성/비활성 갱신
    });

    // 3. 외부 클릭 시 선택 해제 (선택사항)
    $(document).on("click", function(e) {
        if (!$(e.target).closest(".menu-manager-wrap").length) {
            // $(".menu-item").removeClass("active");
            // update_toolbar();
        }
    });

    // 4. 권한 셀렉트 이벤트
    $(document).on("change", "select[name='edit_level']", function() {
        sync_to_tree();
    });
});

/**
 * 툴바 버튼 상태 업데이트 (핵심 로직)
 */
function update_toolbar() {
    var $item = $(".menu-item.active");
    
    // 선택된 항목이 없으면 모든 이동 버튼 비활성화
    if ($item.length === 0) {
        $("#btn-up, #btn-down, #btn-indent, #btn-outdent").prop("disabled", true);
        return;
    }

    // 기본적으로 모두 활성화 후 조건에 따라 비활성화
    $("#btn-up, #btn-down, #btn-indent, #btn-outdent").prop("disabled", false);

    // 1. 위/아래 이동 불가 체크
    if ($item.prev().length === 0) $("#btn-up").prop("disabled", true);
    if ($item.next().length === 0) $("#btn-down").prop("disabled", true);

    // 2. 들여쓰기(Indent) 불가 체크
    // - 이미 2차 메뉴인 경우 (깊이 제한)
    // - 맨 위에 있는 메뉴인 경우 (부모가 될 형이 없음)
    if ($item.hasClass("depth-2") || $item.prev().length === 0) {
        $("#btn-indent").prop("disabled", true);
    }

    // 3. 내어쓰기(Outdent) 불가 체크
    // - 이미 1차 메뉴인 경우
    if ($item.hasClass("depth-1")) {
        $("#btn-outdent").prop("disabled", true);
    }
}

// 상세 정보 로드
function load_detail($item) {
    $("#no-selection-msg").hide();
    $("#detail-form-area").show();

    $("#edit_name").val($item.find(".h-name").val());
    $("#edit_link").val($item.find(".h-link").val());
    $("#edit_target").val($item.find(".h-target").val());
    $("#edit_use").val($item.find(".h-use").val());
    $("#edit_mobile_use").val($item.find(".h-mobile-use").val());
    
    var level = $item.find(".h-level").val();
    $("select[name='edit_level']").val(level);
    $("#edit_level_opt").val($item.find(".h-level-opt").val());
}

// 폼 -> 트리 동기화
function sync_to_tree() {
    var $item = $(".menu-item.active");
    if($item.length == 0) return;

    var name = $("#edit_name").val();
    $item.find(".menu-text").text(name);
    $item.find(".h-name").val(name);
    $item.find(".h-link").val($("#edit_link").val());
    $item.find(".h-target").val($("#edit_target").val());
    $item.find(".h-use").val($("#edit_use").val());
    $item.find(".h-mobile-use").val($("#edit_mobile_use").val());
    $item.find(".h-level").val($("select[name='edit_level']").val());
    $item.find(".h-level-opt").val($("#edit_level_opt").val());
}

// 트리 조작 액션
function menu_action(action) {
    var $item = $(".menu-item.active");
    if($item.length == 0) return;

    if (action === 'up') {
        var $prev = $item.prev();
        if ($prev.length) {
            $item.insertBefore($prev);
        }
    } else if (action === 'down') {
        var $next = $item.next();
        if ($next.length) {
            $item.insertAfter($next);
        }
    } else if (action === 'indent') {
        // 들여쓰기: 1차 -> 2차
        if ($item.prev().length == 0) {
            alert("첫번째 메뉴는 하위 메뉴가 될 수 없습니다.");
            return;
        }
        $item.removeClass('depth-1').addClass('depth-2');
        $item.find(".h-depth").val(2);
    } else if (action === 'outdent') {
        // 내어쓰기: 2차 -> 1차
        $item.removeClass('depth-2').addClass('depth-1');
        $item.find(".h-depth").val(1);
    }

    // 변경 후 버튼 상태 갱신
    update_toolbar();
    
    // 스크롤이 필요하면 이동
    $item[0].scrollIntoView({behavior: "smooth", block: "nearest"});
}

// 메뉴 추가 (개선됨)
function add_new_menu() {
    var new_idx = new Date().getTime();
    var $active = $(".menu-item.active");
    
    // 기본 생성 값
    var init_depth = 1; 
    var depth_class = "depth-1";
    
    // HTML 템플릿 생성 (일단 생성하고 삽입 후 속성 변경)
    var html = `
    <li class="menu-item ${depth_class}" id="item_${new_idx}">
        <span class="menu-icon"><i class="fas fa-bars"></i></span>
        <span class="menu-text">새 메뉴</span>
        <input type="hidden" name="me_name[]" value="새 메뉴" class="h-name">
        <input type="hidden" name="me_link[]" value="" class="h-link">
        <input type="hidden" name="me_target[]" value="self" class="h-target">
        <input type="hidden" name="me_order[]" value="0" class="h-order">
        <input type="hidden" name="me_use[]" value="1" class="h-use">
        <input type="hidden" name="me_mobile_use[]" value="1" class="h-mobile-use">
        <input type="hidden" name="me_level[]" value="1" class="h-level">
        <input type="hidden" name="me_level_opt[]" value="1" class="h-level-opt">
        <input type="hidden" name="me_depth[]" value="${init_depth}" class="h-depth">
    </li>
    `;

    var $newItem = $(html);

    if ($active.length > 0) {
        if ($active.hasClass("depth-1")) {
            // [조건3] 1차 메뉴 선택 시 -> 그 메뉴의 하위(2차)로 추가
            $newItem.removeClass("depth-1").addClass("depth-2");
            $newItem.find(".h-depth").val(2);
            $active.after($newItem); // 바로 아래 추가
        } else {
            // [조건3] 2차 메뉴 선택 시 -> 같은 레벨(2차) 형제로 추가
            $newItem.removeClass("depth-1").addClass("depth-2");
            $newItem.find(".h-depth").val(2);
            $active.after($newItem);
        }
    } else {
        // 선택된게 없으면 맨 뒤에 1차 메뉴로 추가
        $("#menu-sortable").append($newItem);
    }
    
    // 새로 추가된 메뉴 활성화
    $(".menu-item").removeClass("active");
    $newItem.addClass("active");
    
    load_detail($newItem);
    update_toolbar();
    $("#edit_name").focus();
}

function delete_menu() {
    var $item = $(".menu-item.active");
    if($item.length == 0) {
        alert("삭제할 메뉴를 선택해주세요.");
        return;
    }
    if(!confirm("정말 삭제하시겠습니까?")) return;
    
    $item.remove();
    $("#detail-form-area").hide();
    $("#no-selection-msg").show();
    update_toolbar();
}

function open_link_search() {
    var url = "./menu_form.php?code=search_only&new=new"; 
    window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");
}

function add_menu_list(name, link, code) {
    var $item = $(".menu-item.active");
    if($item.length) {
        $("#edit_name").val(name);
        $("#edit_link").val(link);
        sync_to_tree();
    } else {
        add_new_menu();
        $("#edit_name").val(name);
        $("#edit_link").val(link);
        sync_to_tree();
    }
}

function fmenulist_submit(f) {
    var list = $(".menu-item");
    if(list.length == 0) return true;

    if($(list[0]).hasClass("depth-2")) {
        alert("첫번째 메뉴는 반드시 1차 메뉴여야 합니다. (내어쓰기를 해주세요)");
        return false;
    }
    return true;
}
</script>

<?php
require_once './admin.tail.php';
?>