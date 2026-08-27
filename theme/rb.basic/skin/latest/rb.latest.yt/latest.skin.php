<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// 스킨별 고유 ID 생성 (ID 충돌 방지)
$uniqid = uniqid(); 
$modal_id = "yt_modal_v2_" . $uniqid;
$iframe_id = "yt_frame_v2_" . $uniqid;

$rb_skin = sql_fetch (" select * from {$rb_module_table} where md_id = '{$options}' "); 

$thumb_width = 400;
$thumb_height = 333;
$list_count = (is_array($list) && $list) ? count($list) : 0;

if($rb_skin['md_title']) { $bo_subject = $rb_skin['md_title']; } 
else { $bo_subject = $rb_skin['md_title']; }

if($rb_skin['md_sca']) { $links_url = get_pretty_url($bo_table,'','sca='.urlencode($rb_skin['md_sca'])); } 
else { $links_url = get_pretty_url($bo_table); }
?>

<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/style.css">

<style>
/* 모달 배경 (_v2) */
#<?php echo $modal_id ?>.yt-modal-overlay-v2 { 
    display: none; position: fixed; z-index: 9999990; left: 0; top: 0; width: 100%; height: 100%; 
    background-color: rgba(0,0,0,0.9); 
    backdrop-filter: blur(5px);
}

/* 모달 컨텐츠 박스 (_v2) */
#<?php echo $modal_id ?> .yt-modal-content-v2 { 
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
    width: 90%; max-width: 1000px; 
    background: #000; box-shadow: 0 10px 50px rgba(0,0,0,0.5); 
    z-index: 9999991; 
}

/* 영상 래퍼 (_v2) */
#<?php echo $modal_id ?> .yt-video-wrapper-v2 { 
    position: relative; width: 100%; height: 0; padding-bottom: 56.25%; overflow: hidden; background:#000;
}
#<?php echo $modal_id ?> .yt-video-wrapper-v2 iframe { 
    position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999992;
}

/* 닫기 버튼 (_v2) */
#<?php echo $modal_id ?> .yt-modal-close-v2 { 
    position: absolute; top: -50px; right: 0; 
    color: #fff; font-size: 40px; font-weight: 300; cursor: pointer; 
    background: none; border: none; line-height: 1; padding: 0;
    z-index: 9999993;
}
#<?php echo $modal_id ?> .yt-modal-close-v2:hover { color: #ff0000; }

/* 쇼츠 전용 스타일 (_v2) */
#<?php echo $modal_id ?> .yt-modal-content-v2.shorts-view-v2 { max-width: 450px !important; } 
#<?php echo $modal_id ?> .yt-video-wrapper-v2.shorts-view-v2 { padding-bottom: 177.77% !important; }

/* 플레이 아이콘 오버레이 (_v2) */
.play-icon-overlay-v2 {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 50px; height: 50px; background: rgba(0,0,0,0.6); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; pointer-events: none;
    z-index: 10;
}
.play-icon-overlay-v2 svg { width: 20px; height: 20px; fill: #fff; }
</style>

<div class="bbs_main add_c">
    
    <ul class="bbs_main_wrap_tit" style="display:<?php echo (isset($rb_skin['md_title_hide']) && $rb_skin['md_title_hide'] == '1') ? 'none' : 'block'; ?>">
        <li class="bbs_main_wrap_tit_l">
            <a href="<?php echo $links_url; ?>">
                <h2 class="<?php echo isset($rb_skin['md_title_font']) ? $rb_skin['md_title_font'] : 'font-B'; ?>" style="color:<?php echo isset($rb_skin['md_title_color']) ? $rb_skin['md_title_color'] : '#25282b'; ?>; font-size:<?php echo isset($rb_skin['md_title_size']) ? $rb_skin['md_title_size'] : '20'; ?>px; "><?php echo $bo_subject ?></h2>
            </a>
        </li>
        <div class="cb"></div>
    </ul>
    
    <ul class="bbs_main_wrap_yt_v2">
        <div class="rb_swiper" 
            id="rb_swiper_<?php echo $rb_skin['md_id'] ?>" 
            data-pc-w="<?php echo $rb_skin['md_col'] ?>" 
            data-pc-h="<?php echo $rb_skin['md_row'] ?>" 
            data-mo-w="<?php echo $rb_skin['md_col_mo'] ?>" 
            data-mo-h="<?php echo $rb_skin['md_row_mo'] ?>" 
            data-pc-gap="<?php echo $rb_skin['md_gap'] ?>" 
            data-mo-gap="<?php echo $rb_skin['md_gap_mo'] ?>" 
            data-autoplay="<?php echo $rb_skin['md_auto_is'] ?>" 
            data-autoplay-time="<?php echo $rb_skin['md_auto_time'] ?>" 
            data-pc-swap="<?php echo $rb_skin['md_swiper_is'] ?>" 
            data-mo-swap="<?php echo $rb_skin['md_swiper_is'] ?>"
        >
            <div class="rb_swiper_inner">
                <div class="rb-swiper-wrapper swiper-wrapper">
                
                <?php
                    for ($i=0; $i<$list_count; $i++) {
                        $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);
                        
                        // [1] 유튜브 링크 찾기
                        $video_url = '';
                        if(isset($list[$i]['wr_link1']) && $list[$i]['wr_link1']) { $video_url = $list[$i]['wr_link1']; } 
                        else if(isset($list[$i]['wr_1']) && $list[$i]['wr_1']) { $video_url = $list[$i]['wr_1']; }
                        
                        $video_id = '';
                        $is_shorts = 0;
                        
                        // [2] 정규식으로 ID 추출
                        if ($video_url) {
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video_url, $match)) {
                                $video_id = $match[1];
                            }
                            if (strpos($video_url, 'shorts') !== false) {
                                $is_shorts = 1;
                            }
                            if ($video_id) {
                                $img = "https://i.ytimg.com/vi/".$video_id."/hqdefault.jpg";
                            }
                        }
                        
                        // 본문에서 iframe 추출
                        if (!$video_id) {
                             if (preg_match('/src=["\']https?:\/\/(?:www\.)?youtube\.com\/embed\/([^"\']{11})["\']/i', $list[$i]['wr_content'], $match)) {
                                $video_id = $match[1]; 
                            }
                        }

                        // 썸네일 결정
                        if (!isset($img) || !$img) {
                            if($thumb['src']) {
                                $img = strstr($list[$i]['wr_option'], 'secret') ? G5_THEME_URL.'/rb.img/sec_image.png' : $thumb['src'];
                            } else { 
                                $img = G5_THEME_URL.'/rb.img/no_image.png';
                                $thumb['alt'] = '이미지가 없습니다.';
                            }
                        }
                        
                        // 클래스명 변경: skin_list_image_v2
                        $img_content = '<img src="'.$img.'" alt="'.$thumb['alt'].'" class="skin_list_image_v2">';
                        $wr_href = get_pretty_url($bo_table, $list[$i]['wr_id']);
                        
                        // ★ 모달 링크 생성
                        if ($video_id) {
                            $is_shorts_str = $is_shorts ? 'true' : 'false';
                            $link_attr = "href='javascript:;' onclick=\"openYoutubeModal_{$uniqid}('{$video_id}', {$is_shorts_str}); return false;\"";
                        } else {
                            $link_attr = "href='{$wr_href}'";
                        }

                        $sec_txt = '<span style="opacity:0.6">작성자 및 관리자 외 열람할 수 없습니다.<br>비밀글 기능으로 보호된 글입니다.</span>';
                        $wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
                        $wr_content = preg_replace("/&nbsp;/","",$wr_content);
                        $wr_content = preg_replace("/&gt;/","",$wr_content);
                        $wr_content = get_text($wr_content);
                ?>
                
                <div class="rb_swiper_list">
                    <div>
                        <?php if($rb_skin['md_thumb_is'] == 1) { ?>
                        <ul class="bbs_main_wrap_yt_ul1_v2" style="position:relative;">
                            <a <?php echo $link_attr; ?>>
                                <?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?>
                                <?php if($video_id) { ?>
                                <div class="play-icon-overlay-v2">
                                    <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                                <?php } ?>
                            </a>
                            <?php if($rb_skin['md_icon_is'] == 1) { ?>
                                <div class="icon_abs_v2">
                                <?php if ($list[$i]['icon_new']) echo "<span class=\"bbs_list_label_v2 label3\">새글</span>"; ?>
                                <?php if ($list[$i]['icon_hot']) echo "<span class=\"bbs_list_label_v2 label1\">인기</span>"; ?>
                                </div>
                            <?php } ?>
                        </ul>
                        <?php } ?>
                        
                        <?php if($rb_skin['md_content_is'] == 1) { ?>
                        <div>
                                <?php if (strstr($list[$i]['wr_option'], 'secret')) { ?>
                                    <li class="bbs_main_wrap_yt_cont_v2"><?php echo $sec_txt; ?></li>
                                <?php } else { ?>
                                    <li class="bbs_main_wrap_yt_cont_v2 cut2"><a <?php echo $link_attr; ?>><?php echo $wr_content; ?></a></li>
                                <?php } ?>
                        </div>
                        <?php } ?>
                        
                        <div class="yt_list_v2" style="<?php if($rb_skin['md_thumb_is'] != 1) { ?>margin-top:0<?php } ?> <?php if($rb_skin['md_nick_is'] != 1) { ?>min-height:auto;<?php } ?>">
                            <?php if($rb_skin['md_nick_is'] == 1 || $rb_skin['md_date_is'] == 1 || $rb_skin['md_ca_is'] == 1 || $rb_skin['md_comment_is'] == 1 || $rb_skin['md_subject_is'] == 1) { ?>
                            <?php if($rb_skin['md_nick_is'] == 1) { ?><ul class="yt_list_l_v2"><?php echo get_member_profile_img($list[$i]['mb_id'], 37, 37); ?></ul><?php } ?>
                            <ul class="yt_list_r_v2" <?php if($rb_skin['md_nick_is'] != 1) { ?>style="padding-left:0"<?php } ?>>
                                <li class="gallery-item-info-v2">
                                <?php if($rb_skin['md_nick_is'] == 1) { ?><?php echo $list[$i]['wr_name'] ?>　<?php } ?>
                                <?php if($rb_skin['md_date_is'] == 1) { ?><?php echo passing_time($list[$i]['wr_datetime']) ?>　<?php } ?>
                                <?php if($rb_skin['md_ca_is'] == 1 && $list[$i]['ca_name']) { ?><?php echo $list[$i]['ca_name'] ?>　<?php } ?>
                                <?php if($rb_skin['md_comment_is'] == 1) { ?>
                                    조회 <?php echo number_format($list[$i]['wr_hit']); ?>　
                                    <?php if($list[$i]['wr_comment'] > 0) { ?>댓글 <?php echo number_format($list[$i]['wr_comment']); ?><?php } ?>
                                <?php } ?>
                                </li>
                                <?php if($rb_skin['md_subject_is'] == 1) { ?>
                                <li class="gallery-item-tit-v2 cut"><a <?php echo $link_attr; ?> class="font-B"><?php echo $list[$i]['subject'] ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </div>
                        <div class="cb"></div>
                    </div>
                </div>
                
                <?php 
                    unset($img);
                    }  
                ?>
                
                <?php if ($list_count == 0) { ?><div class="no_data" style="width:100% !important;">데이터가 없습니다.</div><?php }  ?>
            </div>
        </div>

        <?php if($rb_skin['md_swiper_is'] == 1) { ?>
        <div class="rb_swiper_paging_btn">
            <button type="button" class="swiper-button-prev rb-swiper-prev"><img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_prev.svg"></button>
            <button type="button" class="swiper-button-next rb-swiper-next"><img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_next.svg"></button>
        </div>
        <?php } ?>
    </div>
</ul>
</div>

<div id="<?php echo $modal_id ?>" class="yt-modal-overlay-v2">
    <div class="yt-modal-content-v2">
        <button class="yt-modal-close-v2" onclick="closeYoutubeModal_<?php echo $uniqid ?>()">&times;</button>
        <div class="yt-video-wrapper-v2">
            <iframe id="<?php echo $iframe_id ?>" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#<?php echo $modal_id ?>').appendTo('body');
    
    $('#<?php echo $modal_id ?>').on('click', function(e) {
        if (e.target == this) {
            closeYoutubeModal_<?php echo $uniqid ?>();
        }
    });
});

function openYoutubeModal_<?php echo $uniqid ?>(videoId, isShorts) {
    if(!videoId) return;
    
    var $modal = $('#<?php echo $modal_id ?>');
    var $iframe = $('#<?php echo $iframe_id ?>');
    // JS 선택자 변경: _v2 클래스 찾기
    var $content = $modal.find('.yt-modal-content-v2');
    var $wrapper = $modal.find('.yt-video-wrapper-v2');

    // 스타일 초기화 (클래스명 변경)
    $content.removeClass('shorts-view-v2');
    $wrapper.removeClass('shorts-view-v2');

    if (isShorts) {
        $content.addClass('shorts-view-v2');
        $wrapper.addClass('shorts-view-v2');
    }

    $iframe.attr('src', "https://www.youtube.com/embed/" + videoId + "?autoplay=1&rel=0");
    $modal.fadeIn(300);
}

function closeYoutubeModal_<?php echo $uniqid ?>() {
    var $modal = $('#<?php echo $modal_id ?>');
    var $iframe = $('#<?php echo $iframe_id ?>');
    
    $iframe.attr('src', ''); 
    $modal.fadeOut(300, function(){
        $iframe.attr('src', '');
    });
}
</script>