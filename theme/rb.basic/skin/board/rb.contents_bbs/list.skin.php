<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 스타일시트 연결
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

$bo_gallery_width = isset($board['bo_gallery_width']) ? $board['bo_gallery_width'] : '';
$bo_gallery_height = isset($board['bo_gallery_height']) ? $board['bo_gallery_height'] : '';
?>

<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.php?bo_gallery_width=<?php echo $bo_gallery_width; ?>&bo_gallery_height=<?php echo $bo_gallery_height; ?>">

<div class="rb_bbs_wrap" style="width:<?php echo $width; ?>">

    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

    <div class="btns_gr_wrap">
       <div class="sub" style="width:<?php echo $rb_core['sub_width'] ?>px;">
            <?php if(!$wr_id) { ?>
            <div class="btns_gr">
                <?php if ($admin_href) { ?>
                <button type="button" class="fl_btns" onclick="window.open('<?php echo $admin_href ?>');">
                    <img src="<?php echo $board_skin_url ?>/img/ico_set.svg"><span class="tooltips">관리</span>
                </button>
                <?php } ?>
                
               <!-- <?php if ($is_admin) { ?>
                <button type="button" class="fl_btns" onclick="if(confirm('유튜브 채널의 최신 영상을 가져오시겠습니까?')) location.href='<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>&exec=youtube_sync';">
                    <img src="<?php echo $board_skin_url ?>/img/ico_rss.svg" style="filter: hue-rotate(180deg);"><span class="tooltips" style="width:110px;">유튜브 가져오기</span>
                </button>
                <?php } ?>-->

                <button type="button" class="fl_btns btn_bo_sch">
                    <img src="<?php echo $board_skin_url ?>/img/ico_ser.svg"><span class="tooltips">검색</span>
                </button>

                <?php if ($rss_href) { ?>
                <button type="button" class="fl_btns" onclick="window.open('<?php echo $rss_href ?>');">
                    <img src="<?php echo $board_skin_url ?>/img/ico_rss.svg"><span class="tooltips">RSS</span>
                </button>
                <?php } ?>

                <?php if ($write_href) { ?>
                <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                    <img src="<?php echo $board_skin_url ?>/img/ico_write.svg"><span class="tooltips">글 등록</span>
                </button>
                <?php } ?>
            </div>
            <?php } ?>
            <div class="cb"></div>
        </div>
    </div>

    <ul class="rb_bbs_top">
        <h3 class="board_title_n"><?php echo get_head_title($g5['title']); ?></h3>
        <?php if($board['bo_read_point'] || $board['bo_write_point']) { ?>
        <li class="point_info_btns_wrap">
            <button type="button" class="point_info_btns" id="point_info_opens_btn">
                <i><svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.523 0 20 4.477 20 10C20 15.523 15.523 20 10 20C4.477 20 0 15.523 0 10C0 4.477 4.477 0 10 0ZM11.5 5H9C8.46957 5 7.96086 5.21071 7.58579 5.58579C7.21071 5.96086 7 6.46957 7 7V14C7 14.2652 7.10536 14.5196 7.29289 14.7071C7.48043 14.8946 7.73478 15 8 15C8.26522 15 8.51957 14.8946 8.70711 14.7071C8.89464 14.5196 9 14.2652 9 14V12H11.5C12.4283 12 13.3185 11.6313 13.9749 10.9749C14.6313 10.3185 15 9.42826 15 8.5C15 7.57174 14.6313 6.6815 13.9749 6.02513C13.3185 5.36875 12.4283 5 11.5 5ZM11.5 7C11.8978 7 12.2794 7.15804 12.5607 7.43934C12.842 7.72064 13 8.10218 13 8.5C13 8.89782 12.842 9.27936 12.5607 9.56066C12.2794 9.84196 11.8978 10 11.5 10H9V7H11.5Z" fill="#09244B"/></svg></i>
                <span class="pc">포인트정책</span>
            </button>
            <div class="point_info_opens">
                <h6><?php echo $board['bo_subject'] ?> 포인트 정책</h6>
                <ul>
                    <dl><dd>글읽기</dd><dd class="font-B"><?php echo number_format($board['bo_read_point']); ?>P</dd></dl>
                    <dl><dd>글쓰기</dd><dd class="font-B"><?php echo number_format($board['bo_write_point']); ?>P</dd></dl>
                </ul>
            </div>
        </li>
        <?php } ?>

		  
        <!--<li class="cnts">전체 <?php echo number_format($total_count) ?>건 / <?php echo $page ?> 페이지</li>-->
        <div class="cb"></div>
    </ul>

    <?php if ($is_category) { ?>
    <nav id="bo_cate" class="swiper-container swiper-container-category">
        <ul id="bo_cate_ul" class="swiper-wrapper swiper-wrapper-category">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <script>
        $(document).ready(function() {
            $("#bo_cate_ul li").addClass("swiper-slide swiper-slide-category");
            var activeElement = document.querySelector('#bo_cate_on');
            var initialSlideIndex = 0;
            if (activeElement) {
                var parentLi = activeElement.closest('li.swiper-slide-category');
                var allSlides = document.querySelectorAll('li.swiper-slide-category');
                initialSlideIndex = Array.prototype.indexOf.call(allSlides, parentLi);
            }
            var swiper = new Swiper('.swiper-container-category', {
                slidesPerView: 'auto', spaceBetween: 0, observer: true, observeParents: true, touchRatio: 1, initialSlide: initialSlideIndex
            });
        });
    </script>
    <?php } ?>

    <ul class="rb_gallery_grid">
        <?php
        for ($i=0; $i<count($list); $i++) {
            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);
            $img_content = $thumb['src'] ? '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >' : '<img src="'.G5_THEME_URL.'/rb.img/no_image.png" alt="이미지가 없습니다." >';
            
            // 원본 링크 (블로그/유튜브)
            $origin_link = $list[$i]['wr_link1'] ? $list[$i]['wr_link1'] : $list[$i]['href'];
            
            // 관리자 수정 링크
            $update_href = G5_BBS_URL.'/write.php?w=u&bo_table='.$bo_table.'&wr_id='.$list[$i]['wr_id'];

            // 본문 내용 텍스트만 추출
            $wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
            $wr_content = preg_replace("/&nbsp;/","",$wr_content);
            $wr_content = get_text($wr_content);
            
            // 여분필드 (wr_1: 가격 등)
            $wr_1 = isset($list[$i]["wr_1"]) ? explode("|", $list[$i]["wr_1"]) : [];
        ?>
        
        <li class="bbs_prd_list"> <?php if ($is_checkbox) { ?>
            <div class="gall_chk_is">
                <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                <label for="chk_wr_id_<?php echo $i ?>"></label>
            </div>
            <?php } ?>

            <div class="bbs_prd_list_wrap" onclick="window.open('<?php echo $origin_link; ?>', '_self');">
                <div class="bbs_prd_list_img">
                    <div class="gallery-item-ico">
                        <?php if ($list[$i]['icon_new']) echo "<span class=\"bbs_list_label label3\">새글</span>"; ?>
                        <?php if ($list[$i]['icon_hot']) echo "<span class=\"bbs_list_label label\">인기</span>"; ?>
                    </div>
                    
                    <?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?>
                </div>

                <ul class="bbs_prd_list_con">
                    <li class="bbs_prd_list_con_li1">
                        <?php if($list[$i]['ca_name']) { echo $list[$i]['ca_name']; } else { echo 'Category'; } ?>
                        <?php if($list[$i]['wr_comment'] > 0) { ?>
                            <span style="color:#ed6478; font-weight:bold;">(+<?php echo number_format($list[$i]['wr_comment']); ?>)</span>
                        <?php } ?>
                    </li>

                    <li class="bbs_prd_list_con_li2 cut">
                        <a href="<?php echo $origin_link ?>" target="_blank" class="font-B"><?php echo $list[$i]['subject'] ?></a>
                    </li>

                    <?php if($list[$i]['wr_6']) { ?>
                    <li class="bbs_prd_list_con_li3 cut2"><?php echo get_text($list[$i]['wr_6']) ?></li>
                    <?php } else { ?>
                    <li class="bbs_prd_list_con_li3 cut2"><?php echo cut_str($wr_content, 50); ?></li>
                    <?php } ?>

                    <?php if(isset($wr_1[0]) && $wr_1[0]) { ?>
                    <li class="font-B info_pri_wrap main_color">
                        <dd><?php echo get_text($wr_1[0]); ?></dd>
                    </li>
                    <?php } else { ?>
                        <li class="font-B info_pri_wrap" style="font-size:14px; color:#999;">
                            <?php echo date("Y.m.d", strtotime($list[$i]['wr_datetime'])); ?>
                        </li>
                    <?php } ?>

                    <?php if($list[$i]['wr_4']) { ?>
                    <div class="bbs_prd_list_con_li4">
                        <li><span><?php echo get_text($list[$i]['wr_4']) ?></span></li>
                    </div>
                    <?php } ?>
                </ul>
            </div>

            <?php if ($is_admin) { ?>
            <a href="<?php echo $update_href; ?>" class="admin-view-btn" title="게시글 수정" target="_blank" onclick="event.stopPropagation();">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19.14 12.94C19.16 12.78 19.17 12.62 19.17 12.45C19.17 12.28 19.16 12.12 19.14 11.96L21.41 10.19C21.61 10.03 21.67 9.75 21.54 9.53L19.39 5.81C19.26 5.59 18.99 5.51 18.77 5.59L16.09 6.67C15.53 6.24 14.92 5.89 14.26 5.61L13.85 2.76C13.81 2.51 13.6 2.32 13.34 2.32H9.04C8.78 2.32 8.57 2.51 8.53 2.76L8.12 5.61C7.46 5.89 6.85 6.24 6.29 6.67L3.61 5.59C3.39 5.51 3.12 5.59 2.99 5.81L0.84 9.53C0.71 9.75 0.77 10.03 0.97 10.19L3.24 11.96C3.22 12.12 3.21 12.29 3.21 12.45C3.21 12.62 3.22 12.78 3.24 12.94L0.97 14.71C0.77 14.87 0.71 15.15 0.84 15.37L2.99 19.09C3.12 19.31 3.39 19.39 3.61 19.31L6.29 18.23C6.85 18.66 7.46 19.01 8.12 19.29L8.53 22.14C8.57 22.39 8.78 22.58 9.04 22.58H13.34C13.6 22.58 13.81 22.39 13.85 22.14L14.26 19.29C14.92 19.01 15.53 18.66 16.09 18.23L18.77 19.31C18.99 19.39 19.26 19.31 19.39 19.09L21.54 15.37C21.67 15.15 21.61 14.87 21.41 14.71L19.14 12.94ZM11.19 15.31C9.61 15.31 8.33 14.03 8.33 12.45C8.33 10.87 9.61 9.59 11.19 9.59C12.77 9.59 14.05 10.87 14.05 12.45C14.05 14.03 12.77 15.31 11.19 15.31Z" fill="white"/></svg>
            </a>
            <?php } ?>

        </li>
        <?php } ?>
        
        <?php if (count($list) == 0) { echo "<li class=\"empty_list\">데이터가 없습니다.</li>"; } ?>
    </ul>
    <ul class="btm_btns">
        <dd class="btm_btns_right">
            <?php if ($rss_href) { ?>
            <button type="button" class="fl_btns rss_pc" onclick="window.open('<?php echo $rss_href ?>');">RSS</button>
            <?php } ?>
            <?php if ($write_href) { ?>
            <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                <img src="<?php echo $board_skin_url ?>/img/ico_write.svg"><span class="font-R">글 등록</span>
            </button>
            <?php } ?>
        </dd>
        
        <dd class="btm_btns_left">
        <?php if ($is_checkbox) { ?>
            <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
            <label for="chkall"></label>
        <?php } ?>
        <?php if ($is_admin == 'super' || $is_auth) { ?>
            <?php if ($is_checkbox) { ?>
                <button type="submit" name="btn_submit" class="fl_btns" value="선택삭제" onclick="document.pressed=this.value"><span class="font-B">선택삭제</span></button>
                <button type="submit" name="btn_submit" class="fl_btns" value="선택복사" onclick="document.pressed=this.value"><span class="font-B">선택복사</span></button>
                <button type="submit" name="btn_submit" class="fl_btns" value="선택이동" onclick="document.pressed=this.value"><span class="font-B">선택이동</span></button>
            <?php } ?>
        <?php } ?>
        <button type="button" name="btn_submit" class="fl_btns btn_bo_sch"><span class="font-B">검색</span></button>      
        </dd>
        <dd class="cb"></dd>
    </ul>
    
    <?php echo $write_pages; ?>
    </form>
</div>

<div class="bo_sch_wrap">
    <fieldset class="bo_sch">
        <h3>검색</h3><legend>게시물 검색</legend>
        <form name="fsearch" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sop" value="and">
        <label for="sfl" class="sound_only">검색대상</label>
        <select name="sfl" id="sfl" class="select"><?php echo get_board_sfl_select_options($sfl); ?></select>
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <div class="sch_bar">
            <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" id="stx" required class="input" maxlength="20" placeholder="검색어를 입력해주세요">
            <button type="submit" value="검색" class="sch_btn" title="검색"><img src="<?php echo $board_skin_url ?>/img/ico_ser.svg"></button>
        </div>
        <button type="button" class="bo_sch_cls"><img src="<?php echo $board_skin_url ?>/img/icon_close.svg"></button>
        </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
</div>

<script>
    // 검색창 토글
    $(".btn_bo_sch").on("click", function() { $(".bo_sch_wrap").toggle(); })
    $('.bo_sch_bg, .bo_sch_cls').click(function(){ $('.bo_sch_wrap').hide(); });
    
    // 포인트 팝업
    $(document).ready(function() {
        $(document).click(function(event) {
            if (!$(event.target).closest('#point_info_opens_btn, .point_info_opens').length) {
                if ($('.point_info_opens').is(':visible')) { $('.point_info_opens').hide(); $('#point_info_opens_btn').removeClass('act'); }
            }
        });
        $('#point_info_opens_btn').click(function(event) { event.stopPropagation(); $('.point_info_opens').toggle(); $(this).toggleClass('act'); });
    });
</script>

<?php if($is_checkbox) { ?>
<noscript><p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p></noscript>
<?php } ?>

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]") f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked) chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") { select_copy("copy"); return; }
    if(document.pressed == "선택이동") { select_copy("move"); return; }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다.")) return false;

        f.removeAttribute("target");
        f.action = "<?php echo G5_BBS_URL; ?>/board_list_update.php";
        
        if (!f.querySelector("input[name='btn_submit'][type='hidden']")) {
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "btn_submit";
            input.value = "선택삭제";
            f.appendChild(input);
        }
    }
    return true;
}

function select_copy(sw) {
    var f = document.fboardlist;
    if (sw == 'copy') str = "복사"; else str = "이동";
    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");
    f.sw.value = sw;
    f.target = "move";
    f.action = "<?php echo G5_BBS_URL; ?>/move.php";
    f.submit();
}

jQuery(function($){
    $(".btn_more_opt.is_list_btn").on("click", function(e) {
        e.stopPropagation();
        $(".more_opt.is_list_btn").toggle();
    });
    $(document).on("click", function (e) {
        if(!$(e.target).closest('.is_list_btn').length) {
            $(".more_opt.is_list_btn").hide();
        }
    });
});
</script>
<?php } ?>

<script>
    /* 페이지 로드 시 게시글 워터폴(순차 등장) 효과 실행 */
    $(document).ready(function() {
        // 레이아웃이 잡힐 때까지 아주 잠깐 대기 후 실행 (0.1초)
        setTimeout(function() {
            $('.rb_gallery_grid').addClass('animate-active');
        }, 100);
    });
</script>