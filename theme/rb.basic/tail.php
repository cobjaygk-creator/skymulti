<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/tail.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    include_once(G5_THEME_SHOP_PATH.'/shop.tail.php');
    return;
}
?>
<?php if (!defined("_INDEX_")) { ?>
    <?php if(isset($bo_table) && $bo_table) { ?>
    <div class="rb_bo_btm flex_box rb_sub_module" data-layout="rb_bo_btm_<?php echo $bo_table ?>"></div>
    <?php } ?>
    <?php if(isset($co_id) && $co_id) { ?>
    <div class="rb_co_btm flex_box rb_sub_module" data-layout="rb_co_btm_<?php echo $co_id ?>"></div>
    <?php } ?>
    <?php if(isset($fr_id) && $fr_id) { ?>
    <div class="rb_fr_btm flex_box rb_sub_module" data-layout="rb_fr_btm_<?php echo $fr_id ?>"></div>
    <?php } ?>
<?php } ?>

<?php if (!defined('_INDEX_') && !$sidebar_hidden) { ?>
   
    <?php if (!empty($side_float)) { ?>
    </div>
    <?php } ?>
    <?php if (isset($rb_core['sidemenu']) && $rb_core['sidemenu'] == "left" || isset($rb_core['sidemenu']) && $rb_core['sidemenu'] == "right") { ?>
    <div id="rb_sidemenu" class="rb_sidemenu rb_sidemenu_<?php echo isset($rb_core['sidemenu']) ? $rb_core['sidemenu'] : ''; ?> <?php if (isset($rb_core['sidemenu_hide']) && $rb_core['sidemenu_hide'] == "1") { ?>pc<?php } ?>" style="width:<?php echo isset($rb_core['sidemenu_width']) ? $rb_core['sidemenu_width'] : '200'; ?>px; <?php if (isset($rb_core['sidemenu']) && $rb_core['sidemenu'] == "left") { ?>padding-right:<?php echo isset($rb_core['sidemenu_padding']) ? $rb_core['sidemenu_padding'] : '0'; ?>px;<?php } else if (isset($rb_core['sidemenu']) && $rb_core['sidemenu'] == "right") { ?>padding-left:<?php echo isset($rb_core['sidemenu_padding']) ? $rb_core['sidemenu_padding'] : '0'; ?>px;<?php } ?>"><div class="flex_box" data-layout="rb_sidemenu"></div></div>
    <?php } ?>

    <div class="cb"></div>

<?php } ?>

</section>
</div>


<?php 

    if (isset($rb_core['layout_ft']) && $rb_core['layout_ft'] == "") {
        echo "<div class='no_data' style='padding:30px 0 !important; margin-top:0px; border:0px !important; background-color:#f9f9f9;'><span class='no_data_section_ul1 font-B color-000'>선택된 푸터 레이아웃이 없습니다.</span><br>환경설정 패널에서 먼저 푸터 레이아웃을 설정해주세요.</div>";
    } else if (isset($rb_core['layout_ft'])) { 
        // 레이아웃 인클루드
        include_once(G5_THEME_PATH . '/rb.layout_ft/' . $rb_core['layout_ft'] . '/footer.php'); 
    } else {
        echo "<div class='no_data' style='padding:30px 0 !important; margin-top:0px; border:0px !important; background-color:#f9f9f9;'><span class='no_data_section_ul1 font-B color-000'>푸터 레이아웃 설정이 올바르지 않습니다.</span><br>환경설정 패널에서 먼저 푸터 레이아웃을 설정해주세요.</div>";
    }

    ?>




<!-- 전체메뉴 { -->
<nav id="cbp-hrmenu-btm" class="cbp-hrmenu cbp-hrmenu-btm mobile">

    <div class="user_prof_bg">
        <?php if($is_member) { ?>
        <li class="user_prof_bg_info font-B"><?php echo $member['mb_nick'] ?></li>
        <li class="user_prof_bg_info font-B"><span><?php echo $member['mb_level'] ?> Lv</span> <a href="<?php echo G5_BBS_URL; ?>/point.php" target="_blank" class="win_point font-B"><span><?php echo number_format($member['mb_point']); ?> P</span></a></li>
        <?php } else { ?>
        <li class="user_prof_bg_info font-B">Guest</li>
        <?php } ?>
    </div>
    <div class="user_prof">
        <?php if($is_member) { ?>
        <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php" class="font-B"><?php echo get_member_profile_img($member['mb_id']); ?></a>
        <?php } else { ?>
        <?php echo get_member_profile_img($member['mb_id']); ?>
        <?php } ?>
    </div>
    <div class="user_prof_btns">
        <li class="">
            <?php if($is_member) { ?>
            <button type="button" alt="로그아웃" class="btn_round" onclick="location.href='<?php echo G5_BBS_URL ?>/logout.php';">로그아웃</button>
            <button type="button" alt="마이페이지" class="btn_round arr_bg font-B" onclick="location.href='<?php echo G5_URL; ?>/rb/home.php?mb_id=<?php echo $member['mb_id']; ?>';">My</button>
            <?php } else { ?>
            <button type="button" alt="로그인" class="btn_round" onclick="location.href='<?php echo G5_BBS_URL ?>/login.php?url=<?php echo urlencode(getCurrentUrl()); ?>';">로그인</button>
            <button type="button" alt="회원가입" class="btn_round arr_bg font-B" onclick="location.href='<?php echo G5_BBS_URL ?>/register.php';">회원가입</button>
            <?php } ?>
        </li>
    </div>



    <ul>


        <?php
                        if(IS_MOBILE()) {
                            $menu_datas = get_menu_db(1, true);
                        } else { 
                            $menu_datas = get_menu_db(0, true);
                        }

                        $gnb_zindex = 999;
                        $i = 0;
                        foreach ($menu_datas as $row) {
                            if (empty($row)) continue;

                            // 1차 메뉴 권한 체크
                            if (!$is_admin && isset($row['me_level']) && $row['me_level'] > 0) {
                                if (isset($row['me_level_opt']) && $row['me_level_opt'] == 2) {
                                    if ($row['me_level'] != $member['mb_level']) continue;
                                } else {
                                    if ($row['me_level'] > $member['mb_level']) continue;
                                }
                            }

                            $add_arr = (isset($row['sub']) && $row['sub']) ? 'add_arr_svg' : '';
                            $add_arr_btn = (isset($row['sub']) && $row['sub']) ? '<button type="button" class="add_arr_btn"></button>' : '';
                        ?>
        <li class="<?php echo $add_arr ?>">
            <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="font-B"><?php echo $row['me_name'] ?></a>
            <?php echo $add_arr_btn ?>
            <?php
                                $k = 0;
                                foreach ((array) $row['sub'] as $row2) {
                                    if (empty($row2)) continue;

                                    // 2차 메뉴 권한 체크
                                    if (!$is_admin && isset($row2['me_level']) && $row2['me_level'] > 0) {
                                        if (isset($row2['me_level_opt']) && $row2['me_level_opt'] == 2) {
                                            if ($row2['me_level'] != $member['mb_level']) continue;
                                        } else {
                                            if ($row2['me_level'] > $member['mb_level']) continue;
                                        }
                                    }

                                    if ($k == 0)
                                        echo '<div class="cbp-hrsub"><div class="cbp-hrsub-inner"><div><!--<h4 class="font-B">그룹</h4>--><ul>' . PHP_EOL;
                                ?>
        <li><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>"><?php echo $row2['me_name'] ?></a></li>
        <?php
                                    $k++;
                                }

                                if ($k > 0)
                                    echo '</ul></div></div></div>' . PHP_EOL;
                                ?>
        </li>
        <?php
                            $i++;
                        }
                        ?>

    </ul>


</nav>


<!-- } -->



<button type="button" id="m_gnb_close_btn" class="mobile">
    <img src="<?php echo G5_URL ?>/rb/rb.config/image/icon_close.svg">
</button>

<script>
    $(document).ready(function() {
        $('#m_gnb_close_btn').click(function() {
            $('#cbp-hrmenu-btm').removeClass('active');
            $('#m_gnb_close_btn').removeClass('active');
            $('main').removeClass('moves');
            $('header').removeClass('moves');
        });
    });
</script>


<script src="<?php echo G5_THEME_URL ?>/rb.js/cbpHorizontalMenu.min.js"></script>
<script>
    $(function() {
        cbpHorizontalMenu.init();
        cbpHorizontalMenu_btm.init();
    });
</script>
<!-- } -->

<!-- 캘린더 옵션 { -->
<script>
    $.datepicker.setDefaults({
        closeText: "닫기",
        prevText: "이전달",
        nextText: "다음달",
        currentText: "오늘",
        monthNames: ["1월", "2월", "3월", "4월", "5월", "6월",
            "7월", "8월", "9월", "10월", "11월", "12월"
        ],
        monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월",
            "7월", "8월", "9월", "10월", "11월", "12월"
        ],
        dayNames: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"],
        dayNamesShort: ["일", "월", "화", "수", "목", "금", "토"],
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        weekHeader: "주",
        dateFormat: "yy-mm-dd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: "년"
    })

    $(".datepicker_inp").datepicker({
        //minDate: 0
    })
</script>

<?php if($bo_table && $wr_id) { // 댓글 수정모드일때 높이값 갱신?>
<script>
    (function() {
        if (!window.comment_box || window.comment_box.__patched) return;

        var _orig = window.comment_box;

        function kick() {
            var ta = document.getElementById('wr_content');
            if (!ta) return;
            if (window.jQuery) $('#wr_content').trigger('input');
            else {
                ta.style.minHeight = '150px';
                ta.style.height = 'auto';
                ta.style.height = ta.scrollHeight + 'px';
            }
        }

        window.comment_box = function() {
            var ret = _orig.apply(this, arguments);
            // 레이아웃 반영 후 두 번 정도 태워줌
            requestAnimationFrame(kick);
            setTimeout(kick, 0);
            return ret;
        };
        window.comment_box.__patched = true;
    })();
</script>
<script>
(function () {
  // 1) check_byte를 원형 보존 + 최근 target만 기록 (동작은 100% 동일)
  if (typeof window.check_byte === 'function' && !window.check_byte.__rb_wrapped) {
    var __orig_check_byte = window.check_byte;
    window.__rb_last_cb_target = null;

    window.check_byte = function (content, target) {
      window.__rb_last_cb_target = target || window.__rb_last_cb_target;
      return __orig_check_byte.apply(this, arguments);
    };
    window.check_byte.__rb_wrapped = true;
    window.check_byte.__rb_orig = __orig_check_byte;
  }

  // 2) comment_box 원본 보존 후, 실행 직후 "한 번만" 재계산
  if (typeof window.comment_box === 'function' && !window.comment_box.__rb_patched) {
    var _orig_comment_box = window.comment_box;

    window.comment_box = function () {
      var ret = _orig_comment_box.apply(this, arguments);

      setTimeout(function () {
        // 최근에 페이지가 사용한 target을 우선 사용
        var target = window.__rb_last_cb_target
                  || (document.getElementById('char_count') ? 'char_count' : null)
                  || (document.getElementById('char_cnt')   ? 'char_cnt'   : null);

        if (typeof window.check_byte === 'function' && target) {
          // 원래 출력 형식(N 글자 등)을 그대로 유지하려고 원본 check_byte를 호출
          var fn = window.check_byte.__rb_orig || window.check_byte;
          fn('wr_content', target);
        }
      }, 0);

      return ret;
    };
    window.comment_box.__rb_patched = true;
  }
})();
</script>

<?php } ?>

<link rel="stylesheet" href="<?php echo G5_THEME_URL ?>/rb.css/datepicker.css" />
<!-- } -->

<?php
    //리빌드세팅
    if($is_admin) {
        include_once(G5_PATH.'/rb/rb.config/right.php'); //환경설정
    }
         
    // HOOK 추가, (tail.php 가 로드되는 페이지에서만 / 쪽지, 로그인 등의 모듈 페이지에서는 실행 되지않게 하기위함.)
    // 관련 HOOK : add_event('tail_sub', 'aaa');
    $rb_hook_tail = "true";

?>


<?php
if(G5_DEVICE_BUTTON_DISPLAY && !G5_IS_MOBILE) { ?>
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<!-- } 하단 끝 -->


<!-- 우측 고정 퀵메뉴 { -->
<style>
.sky-fab{position:fixed;right:20px;bottom:110px;z-index:990;display:flex;flex-direction:column;gap:12px;margin:0;padding:0;list-style:none}
.sky-fab li{position:relative}
.sky-fab a{display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;box-shadow:0 6px 16px rgba(0,0,0,.28);transition:transform .2s ease,box-shadow .2s ease;text-decoration:none}
.sky-fab a:hover{transform:translateY(-3px) scale(1.06);box-shadow:0 10px 22px rgba(0,0,0,.35)}
.sky-fab svg{width:20px;height:20px;display:block}
.sky-fab .fab-tel{background:#2f80ed}
.sky-fab .fab-kakao{background:#fee500}
.sky-fab .fab-yt{background:#ff0000}
.sky-fab .tip{position:absolute;right:52px;top:50%;transform:translateY(-50%) translateX(8px);white-space:nowrap;background:rgba(20,20,20,.92);color:#fff;font-size:13px;line-height:1;padding:8px 13px;border-radius:20px;opacity:0;pointer-events:none;transition:opacity .2s ease,transform .2s ease}
.sky-fab li:hover .tip,.sky-fab li:focus-within .tip{opacity:1;transform:translateY(-50%) translateX(0)}
@media (max-width:900px){
  .sky-fab{right:12px;bottom:80px;gap:10px}
  .sky-fab a{width:48px;height:48px}
  .sky-fab svg{width:24px;height:24px}
  .sky-fab .tip{display:none}
}
</style>

<ul class="sky-fab" aria-label="빠른 연락">
  <li>
    <a class="fab-tel" href="tel:15668283" aria-label="전화 상담 1566-8283">
      <svg viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
    </a>
    <span class="tip">전화 1566-8283</span>
  </li>
  <li>
    <a class="fab-kakao" href="https://pf.kakao.com/_FkkNxd" target="_blank" rel="noopener" aria-label="카카오톡 채팅상담">
      <svg viewBox="0 0 24 24" fill="#3a1d1d" aria-hidden="true"><path d="M12 3C6.48 3 2 6.58 2 11c0 2.83 1.88 5.32 4.7 6.73l-.95 3.45a.5.5 0 0 0 .77.54l4.08-2.7c.46.05.92.08 1.4.08 5.52 0 10-3.58 10-8S17.52 3 12 3z"/></svg>
    </a>
    <span class="tip">카카오톡 상담</span>
  </li>
  <li>
    <a class="fab-yt" href="https://www.youtube.com/channel/UCeVuOfZ9F5Rz10gFgutJYjQ" target="_blank" rel="noopener" aria-label="유튜브 공식채널">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" fill-rule="evenodd" d="M21.6 7.2a2.5 2.5 0 0 0-1.76-1.77C18.27 5 12 5 12 5s-6.27 0-7.84.43A2.5 2.5 0 0 0 2.4 7.2C2 8.78 2 12 2 12s0 3.22.4 4.8a2.5 2.5 0 0 0 1.76 1.77C5.73 19 12 19 12 19s6.27 0 7.84-.43a2.5 2.5 0 0 0 1.76-1.77C22 15.22 22 12 22 12s0-3.22-.4-4.8zM10 15V9l5.2 3L10 15z"/></svg>
    </a>
    <span class="tip">유튜브 채널</span>
  </li>
</ul>
<!-- } 우측 고정 퀵메뉴 -->


<script>
    $(function() {
        // 폰트 리사이즈 쿠키있으면 실행
        font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
    });

</script>

<?php
include_once(G5_THEME_PATH."/tail.sub.php");