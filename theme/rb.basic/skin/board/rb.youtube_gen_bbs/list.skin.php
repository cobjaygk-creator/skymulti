<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// ==============================================================================
// [핵심 함수] 유튜브 카테고리 판별 (키워드 -> 통신 -> 썸네일 분석 3단계 방어)
// ==============================================================================
function get_youtube_category_and_link($video_id, $original_link, $title, $description) {
    // 1단계: 텍스트 분석 (가장 빠르고 서버 부하 없음)
    // 대소문자 구분 없이 제목이나 설명에 #shorts, shorts 등이 있으면 무조건 쇼츠 처리
    if (stripos($title, 'shorts') !== false || stripos($description, 'shorts') !== false) {
        return array('category' => '쇼츠', 'link' => "https://www.youtube.com/shorts/" . $video_id);
    }

    // 2단계: cURL 통신 (닷홈 환경 고려하여 타임아웃 짧게 설정)
    $test_url = "https://www.youtube.com/shorts/" . $video_id;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test_url);
    curl_setopt($ch, CURLOPT_NOBODY, true); // 본문은 안 받음 (속도 위함)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // 리다이렉트 금지 (중요)
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 3초 넘어가면 포기
    // 실제 크롬 브라우저 헤더 완벽 위장
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1'
    ));
    
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        return array('category' => '쇼츠', 'link' => $test_url);
    } 
    // 303은 명확히 일반 영상으로 이동하라는 신호
    elseif ($http_code == 303) {
        return array('category' => '동영상', 'link' => $original_link);
    }

    // 3단계: 통신이 차단되었거나(403, 429) 실패했을 경우(0) -> 최후의 수단
    // 제목이 짧거나 특정 패턴이면 쇼츠일 확률이 높음 (보조 수단)
    // 닷홈에서는 외부 통신이 불안정하므로 여기서 "동영상"으로 퉁치지 않고, 
    // 만약 영상 ID로 만든 shorts 링크를 그대로 반환해서 나중에 클릭 시 판단하게 유도할 수도 있음.
    // 하지만 여기서는 기본값 '동영상'으로 하되, 썸네일 분석은 복잡하므로 생략하고 기본 처리.
    
    return array('category' => '동영상', 'link' => $original_link);
}

// ==============================================================================
// [시작] 유튜브 채널 연동 및 자동 저장 로직
// ==============================================================================
$youtube_channel_id = 'UCeVuOfZ9F5Rz10gFgutJYjQ'; // ★채널 ID
$is_sync_mode  = (isset($_GET['exec']) && $_GET['exec'] == 'youtube_sync'); 

if ($is_admin && $is_sync_mode && $youtube_channel_id) {
    // 1. 유튜브 RSS XML 로드
    $rss_url = "https://www.youtube.com/feeds/videos.xml?channel_id={$youtube_channel_id}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rss_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Accept-Language: ko-KR,ko;q=0.9,en-US;q=0.8,en;q=0.7',
		'Referer: https://www.youtube.com/'
	));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); 
    $xml_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($http_code != 200 || !$xml_data) {
        alert("유튜브 채널에 접속할 수 없습니다.\\n응답코드: {$http_code}\\n에러내용: {$curl_error}");
    }

    $xml = simplexml_load_string($xml_data);

    if ($xml === false) {
        alert("RSS 데이터 형식이 올바르지 않습니다. (XML 파싱 실패)");
    }

    $cnt = 0;
    $upload_dir = G5_DATA_PATH.'/file/'.$bo_table;

    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, G5_DIR_PERMISSION);
        @chmod($upload_dir, G5_DIR_PERMISSION);
    }

    $w_mb_id = isset($member['mb_id']) && $member['mb_id'] ? $member['mb_id'] : $config['cf_admin'];
    $w_password = isset($member['mb_password']) && $member['mb_password'] ? $member['mb_password'] : ''; 

    // [Step 1] 데이터 배열화
    $items = array();
    
    foreach ($xml->entry as $entry) {
        $media = $entry->children('http://search.yahoo.com/mrss/');
        
        $title = trim((string)$entry->title);
        $link = trim((string)$entry->link['href']);
        $videoId = str_replace('https://www.youtube.com/watch?v=', '', $link);
        $description = trim((string)$media->group->description);
        $pubDate = (string)$entry->published;
        $thumbnail_url = (string)$media->group->thumbnail->attributes()->url;

        // 본문 생성 (iframe)
        $content_html = '<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;margin-bottom:20px;"><iframe src="https://www.youtube.com/embed/'.$videoId.'" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
        $content_html .= nl2br($description);

        $items[] = array(
            'title'       => $title,
            'link'        => $link, 
            'videoId'     => $videoId,
            'description' => $description, 
            'content'     => $content_html,
            'thumb_url'   => $thumbnail_url,
            'pubDate'     => $pubDate,
            'timestamp'   => strtotime($pubDate) 
        );
    }

    // [Step 2] 정렬
    usort($items, function($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    // [Step 3] DB 저장
    foreach ($items as $item) {
        $subject = $item['title'];
        $content = $item['content'];
        $pubdate = date('Y-m-d H:i:s', $item['timestamp']);
        $img_src = $item['thumb_url'];
        
        // ★ 핵심: 쇼츠 여부 판별 및 링크 확정
        $info = get_youtube_category_and_link($item['videoId'], $item['link'], $item['title'], $item['description']);
        
        $ca_name = $info['category']; // '쇼츠' or '동영상'
        $final_link = $info['link']; // 최종 링크 (/shorts/... or /watch?...)

        // 중복 체크 (VideoID 기준)
        $row = sql_fetch(" select count(*) as cnt from {$write_table} where wr_content like '%{$item['videoId']}%' ");
        if ($row['cnt'] > 0) continue; 

        $wr_num = get_next_num($write_table);
        $wr_reply = '';

        $sql = " insert into {$write_table}
                    set wr_num = '{$wr_num}',
                        wr_reply = '{$wr_reply}',
                        wr_comment = 0,
                        ca_name = '{$ca_name}', 
                        wr_option = 'html1',
                        wr_subject = '".sql_real_escape_string($subject)."',
                        wr_content = '".sql_real_escape_string($content)."',
                        wr_link1 = '{$final_link}', 
                        wr_link2 = '',
                        wr_link1_hit = 0,
                        wr_link2_hit = 0,
                        wr_hit = 0,
                        wr_good = 0,
                        wr_nogood = 0,
                        mb_id = '{$w_mb_id}',
                        wr_password = '{$w_password}',
                        wr_name = '{$board['bo_subject']}', 
                        wr_email = '',
                        wr_homepage = '',
                        wr_datetime = '{$pubdate}',
                        wr_file = 0,
                        wr_last = '".G5_TIME_YMDHIS."',
                        wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                        wr_1 = '', wr_2 = '', wr_3 = '', wr_4 = '', wr_5 = '',
                        wr_6 = '', wr_7 = '', wr_8 = '', wr_9 = '', wr_10 = '' ";
        sql_query($sql);
        
        $wr_id = sql_insert_id();

        // 삭제 기능 정상화
        sql_query(" update {$write_table} set wr_parent = '{$wr_id}' where wr_id = '{$wr_id}' ");

        // 썸네일 다운로드
        if ($img_src) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $img_src);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $img_data = curl_exec($ch);
            curl_close($ch);

            if ($img_data) {
                $ext = 'jpg';
                $filename = time().'_'.rand(1000,9999).'.'.$ext;
                $dest_file = $upload_dir.'/'.$filename;

                $fp = fopen($dest_file, 'w');
                if($fp) {
                    fwrite($fp, $img_data);
                    fclose($fp);
                    @chmod($dest_file, G5_FILE_PERMISSION);

                    $filesize = filesize($dest_file);
                    $sql_file = " insert into {$g5['board_file_table']}
                                    set bo_table = '{$bo_table}',
                                        wr_id = '{$wr_id}',
                                        bf_no = 0,
                                        bf_source = 'youtube_thumb.{$ext}',
                                        bf_file = '{$filename}',
                                        bf_download = 0,
                                        bf_content = '',
                                        bf_filesize = '{$filesize}',
                                        bf_width = 0,
                                        bf_height = 0,
                                        bf_type = 1,
                                        bf_datetime = '".G5_TIME_YMDHIS."' ";
                    sql_query($sql_file);
                    sql_query(" update {$write_table} set wr_file = 1 where wr_id = '{$wr_id}' ");
                }
            }
        }

        sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '{$pubdate}', '{$w_mb_id}' ) ");

        $cnt++;
        
        // 차단 방지 딜레이 (0.5초)
        usleep(500000); 
    }

    sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write + {$cnt} where bo_table = '{$bo_table}' ");

    alert($cnt."개의 유튜브 영상을 가져왔습니다.", G5_BBS_URL."/board.php?bo_table=".$bo_table);
}
// ==============================================================================
// [끝] 유튜브 연동 로직
// ==============================================================================


add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

$bo_page_rows = isset($board['bo_page_rows']) ? $board['bo_page_rows'] : '';
$bo_mobile_page_rows = isset($board['bo_mobile_page_rows']) ? $board['bo_mobile_page_rows'] : '';
$bo_gallery_cols = isset($board['bo_gallery_cols']) ? $board['bo_gallery_cols'] : '';
$bo_gallery_height = isset($board['bo_gallery_height']) ? $board['bo_gallery_height'] : '';
$bo_mobile_gallery_height = isset($board['bo_mobile_gallery_height'])  ? $board['bo_mobile_gallery_height'] : '';

?>

<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.php?bo_gallery_height=<?php echo $bo_gallery_height; ?>&bo_mobile_gallery_height=<?php echo $bo_mobile_gallery_height; ?>">

<style>
/* 유튜브 모달 */
.yt-modal-overlay { display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); backdrop-filter: blur(5px); }
.yt-modal-content { 
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
    width: 90%; max-width: 900px; /* 기본 동영상 크기 */
    background: #000; box-shadow: 0 5px 30px rgba(0,0,0,0.5); 
    transition: all 0.3s ease;
}
.yt-video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; transition: all 0.3s ease; }
.yt-video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
.yt-modal-close { position: absolute; top: -40px; right: 0; color: #fff; font-size: 30px; font-weight: bold; cursor: pointer; background: none; border: none; padding: 0 10px; }
.yt-modal-close:hover { color: #ff0000; }

/* 쇼츠 모달 스타일 */
.yt-modal-content.shorts-view { max-width: 450px; } 
.yt-video-wrapper.shorts-view { padding-bottom: 177.77%; } /* 9:16 비율 */

/* 관리자용 톱니바퀴 버튼 */
.admin-view-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    z-index: 20;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0,0,0,0.6);
    border-radius: 50%;
    transition: all 0.3s ease;
    cursor: pointer;
}
.admin-view-btn:hover {
    background-color: rgba(255, 0, 0, 0.8);
    transform: rotate(90deg);
}
.admin-view-btn svg {
    width: 14px;
    height: 14px;
    fill: #fff;
}
</style>


<h3 class="board_title_n"><?php echo get_head_title($g5['title']); ?></h3>        
<div class="rb_bbs_wrap" id="scroll_container" style="width:<?php echo $width; ?>">
  
    <form name="fboardlist"  id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
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
                <img src="<?php echo $board_skin_url ?>/img/ico_set.svg">
                <span class="tooltips">관리</span>
                </button>
                <?php } ?>
                <?php if ($is_admin) { ?>
                <button type="button" class="fl_btns" onclick="if(confirm('유튜브 채널의 최신 영상을 가져오시겠습니까?')) location.href='<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>&exec=youtube_sync';">
                <img src="<?php echo $board_skin_url ?>/img/ico_rss.svg" style="filter: hue-rotate(180deg);">
                <span class="tooltips" style="width:110px;">유튜브 가져오기</span>
                </button>
                <?php } ?>
                <button type="button" class="fl_btns btn_bo_sch">
                <img src="<?php echo $board_skin_url ?>/img/ico_ser.svg">
                <span class="tooltips">검색</span>
                </button>
                <?php if ($rss_href) { ?>
                <button type="button" class="fl_btns" onclick="window.open('<?php echo $rss_href ?>');">
                <img src="<?php echo $board_skin_url ?>/img/ico_rss.svg">
                <span class="tooltips">RSS</span>
                </button>
                <?php } ?>
                <?php if ($write_href) { ?>
                <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                <img src="<?php echo $board_skin_url ?>/img/ico_write.svg">
                <span class="tooltips">글 등록</span>
                </button>
                <?php } ?>
            </div>
            <?php } ?>
            <div class="cb"></div>
        </div>
    </div>
    
    <ul class="rb_bbs_top">
        <?php if($board['bo_read_point'] || $board['bo_write_point'] || $board['bo_comment_point'] || $board['bo_download_point']) { ?>
        <li class="point_info_btns_wrap">
            <button type="button" class="point_info_btns" id="point_info_opens_btn">
            <i><svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 0C15.523 0 20 4.477 20 10C20 15.523 15.523 20 10 20C4.477 20 0 15.523 0 10C0 4.477 4.477 0 10 0ZM11.5 5H9C8.46957 5 7.96086 5.21071 7.58579 5.58579C7.21071 5.96086 7 6.46957 7 7V14C7 14.2652 7.10536 14.5196 7.29289 14.7071C7.48043 14.8946 7.73478 15 8 15C8.26522 15 8.51957 14.8946 8.70711 14.7071C8.89464 14.5196 9 14.2652 9 14V12H11.5C12.4283 12 13.3185 11.6313 13.9749 10.9749C14.6313 10.3185 15 9.42826 15 8.5C15 7.57174 14.6313 6.6815 13.9749 6.02513C13.3185 5.36875 12.4283 5 11.5 5ZM11.5 7C11.8978 7 12.2794 7.15804 12.5607 7.43934C12.842 7.72064 13 8.10218 13 8.5C13 8.89782 12.842 9.27936 12.5607 9.56066C12.2794 9.84196 11.8978 10 11.5 10H9V7H11.5Z" fill="#09244B"/></svg></i>
            <span class="pc">포인트정책</span></button>
            <div class="point_info_opens">
                <h6><?php echo $board['bo_subject'] ?> 포인트 정책</h6>
                <ul>
                    <?php if($board['bo_read_point']) { ?>
                    <dl><dd>글읽기</dd><dd class="font-B"><?php echo number_format($board['bo_read_point']); ?>P</dd></dl>
                    <?php } ?>
                    <?php if($board['bo_write_point']) { ?>
                    <dl><dd>글쓰기</dd><dd class="font-B"><?php echo number_format($board['bo_write_point']); ?>P</dd></dl>
                    <?php } ?>
                    <?php if($board['bo_comment_point']) { ?>
                    <dl><dd>댓글</dd><dd class="font-B"><?php echo number_format($board['bo_comment_point']); ?>P</dd></dl>
                    <?php } ?>
                    <?php if($board['bo_download_point']) { ?>
                    <dl><dd>다운로드</dd><dd class="font-B"><?php echo number_format($board['bo_download_point']); ?>P</dd></dl>
                    <?php } ?>
                </ul>
            </div>
            <script>
                $(document).ready(function() {
                    $(document).click(function(event) {
                        if (!$(event.target).closest('#point_info_opens_btn, .point_info_opens').length) {
                            if ($('.point_info_opens').is(':visible')) {
                                $('.point_info_opens').hide();
                                $('#point_info_opens_btn').removeClass('act');
                            }
                        }
                    });
                    $('#point_info_opens_btn').click(function(event) {
                        event.stopPropagation(); 
                        $('.point_info_opens').toggle();
                        $(this).toggleClass('act');
                    });
                });
            </script>
        </li>
        <?php } ?>
        <?php if ($is_checkbox) { ?>
        <li>
            <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
            <label for="chkall"></label>
        </li>
        <?php } ?>
        <li class="cnts">전체 <?php echo number_format($total_count) ?>건 / <?php echo $page ?> 페이지</li>
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
    
    <div class="swiper-container swiper-container-<?php echo $bo_table ?>">
    <ul class="gallery_top_mt rb_bbs_list swiper-wrapper swiper-wrapper-<?php echo $bo_table ?>">
       
        <?php 
        for ($i=0; $i<count($list); $i++) { 
            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);
            
            if($thumb['src']) {
                $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >';
            } else { 
                $img_content = '<img src="'.G5_THEME_URL.'/rb.img/no_image.png" alt="이미지가 없습니다." >';
            }
            
            $wr_link1 = $list[$i]['wr_link1'];
            $video_id = '';
            $is_shorts = false;

            if($wr_link1) {
                // shorts 경로 체크 (DB에 이미 쇼츠 링크로 저장되어 있다면)
                if (strpos($wr_link1, '/shorts/') !== false) {
                    $parts = explode('/shorts/', $wr_link1);
                    $video_id = end($parts);
                    $is_shorts = true;
                } else {
                    parse_str(parse_url($wr_link1, PHP_URL_QUERY), $params);
                    $video_id = isset($params['v']) ? $params['v'] : '';
                    if(!$video_id) { $parts = explode('/', $wr_link1); $video_id = end($parts); }
                }
            }
            
            if($video_id) {
                $link_attr = "href='javascript:;' onclick=\"openYoutubeModal('{$video_id}', ".($is_shorts ? 'true' : 'false').")\"";
            } else {
                $link_attr = "href='{$list[$i]['href']}'";
            }

            $wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
            $wr_content = preg_replace("/&nbsp;/","",$wr_content);
            $wr_content = get_text($wr_content);
        ?>
        
        <div class="gallery_v_mtop swiper-slide swiper-slide-<?php echo $bo_table ?>">
            <ul class="gallery-item-img">
                <a <?php echo $link_attr; ?>><?php echo run_replace('thumb_image_tag', $img_content, $thumb); ?></a>
                <div class="gallery-item-ico">
                    <?php if ($list[$i]['icon_new']) echo "<span class=\"bbs_list_label label3\">새글</span>"; ?>
                </div>
                
                <?php if ($is_admin) { ?>
                <a href="<?php echo $list[$i]['href']; ?>" class="admin-view-btn" title="관리자 상세페이지 이동 (수정/삭제)" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.14 12.94C19.16 12.78 19.17 12.62 19.17 12.45C19.17 12.28 19.16 12.12 19.14 11.96L21.41 10.19C21.61 10.03 21.67 9.75 21.54 9.53L19.39 5.81C19.26 5.59 18.99 5.51 18.77 5.59L16.09 6.67C15.53 6.24 14.92 5.89 14.26 5.61L13.85 2.76C13.81 2.51 13.6 2.32 13.34 2.32H9.04C8.78 2.32 8.57 2.51 8.53 2.76L8.12 5.61C7.46 5.89 6.85 6.24 6.29 6.67L3.61 5.59C3.39 5.51 3.12 5.59 2.99 5.81L0.84 9.53C0.71 9.75 0.77 10.03 0.97 10.19L3.24 11.96C3.22 12.12 3.21 12.29 3.21 12.45C3.21 12.62 3.22 12.78 3.24 12.94L0.97 14.71C0.77 14.87 0.71 15.15 0.84 15.37L2.99 19.09C3.12 19.31 3.39 19.39 3.61 19.31L6.29 18.23C6.85 18.66 7.46 19.01 8.12 19.29L8.53 22.14C8.57 22.39 8.78 22.58 9.04 22.58H13.34C13.6 22.58 13.81 22.39 13.85 22.14L14.26 19.29C14.92 19.01 15.53 18.66 16.09 18.23L18.77 19.31C18.99 19.39 19.26 19.31 19.39 19.09L21.54 15.37C21.67 15.15 21.61 14.87 21.41 14.71L19.14 12.94ZM11.19 15.31C9.61 15.31 8.33 14.03 8.33 12.45C8.33 10.87 9.61 9.59 11.19 9.59C12.77 9.59 14.05 10.87 14.05 12.45C14.05 14.03 12.77 15.31 11.19 15.31Z" fill="white"/></svg>
                </a>
                <?php } ?>

                <?php if ($is_checkbox) { ?>
                <div class="gall_chk_is">
                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="">
                    <label for="chk_wr_id_<?php echo $i ?>"></label>
                </div>
                <?php } ?>
            </ul>
            
            <ul class="gallery-item-info">
            <?php echo passing_time($list[$i]['wr_datetime']) ?>　
            <?php if($list[$i]['ca_name']) { ?>
                <span class="bo_cate_link"><?php echo $list[$i]['ca_name'] ?></span>
            <?php } ?>
            </ul>
            
            <ul class="gallery-item-tit cut2"><a <?php echo $link_attr; ?> class="font-B"><?php echo $list[$i]['subject'] ?></a></ul>
            <!--<ul class="gallery-item-con cut2">
                <a <?php echo $link_attr; ?>><?php echo $wr_content ?></a>
            </ul>-->
            
            <!--<ul class="gallery-item-info gallery-item-info-sub">
                <span class="prof_tiny_name font-B"><?php echo $list[$i]['name'] ?>　</span>
                    조회 <?php echo number_format($list[$i]['wr_hit']); ?>
            </ul>-->
        </div>
        
        <?php } ?>
        
    </ul>
    </div>
    
    <script>
        var swiper = new Swiper('.swiper-container-<?php echo $bo_table ?>', {
            slidesPerView: <?php echo isset($bo_gallery_cols) ? $bo_gallery_cols : '3'; ?>, //가로갯수
            spaceBetween: 30, // 간격
            slidesPerColumnFill: 'row', //세로형
            slidesPerColumn: 4, // 세로갯수
            observer: true, //리셋
            observeParents: true, //리셋
            touchRatio: 0, // 드래그 가능여부
            simulateTouch: false, //마우스 클릭드래그를 허용함.

            breakpoints: { // 반응형
                1024: { slidesPerView: <?php echo isset($bo_gallery_cols) ? $bo_gallery_cols : '3'; ?>, spaceBetween: 30, slidesPerColumnFill: 'row', slidesPerColumn: 9999 },
                768: { slidesPerView: <?php echo isset($bo_gallery_cols) ? $bo_gallery_cols : '3'; ?>, spaceBetween: 20, slidesPerColumnFill: 'row', slidesPerColumn: 9999 },
                450: { slidesPerView: 2, spaceBetween: 20, slidesPerColumnFill: 'row', slidesPerColumn: 9999 },
                10: { slidesPerView: 1, spaceBetween: 20, slidesPerColumnFill: 'row', slidesPerColumn: 9999 }
            }
        });
    </script>
    
    <?php if (count($list) == 0) { echo "<div class=\"no_data\" style=\"text-align:center; padding-top:0px !important;\">데이터가 없습니다.</div>"; } ?>
    
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

<div id="youtubeModal" class="yt-modal-overlay">
    <div class="yt-modal-content">
        <button class="yt-modal-close" onclick="closeYoutubeModal()">&times;</button>
        <div class="yt-video-wrapper">
            <iframe id="modalYoutubeFrame" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
function openYoutubeModal(videoId, isShorts) {
    if(!videoId) return;
    var iframe = document.getElementById('modalYoutubeFrame');
    var modalContent = document.querySelector('.yt-modal-content');
    var videoWrapper = document.querySelector('.yt-video-wrapper');

    // 스타일 초기화 후 쇼츠 여부에 따라 클래스 추가
    modalContent.classList.remove('shorts-view');
    videoWrapper.classList.remove('shorts-view');

    if (isShorts) {
        modalContent.classList.add('shorts-view');
        videoWrapper.classList.add('shorts-view');
    }

    iframe.src = "https://www.youtube.com/embed/" + videoId + "?autoplay=1";
    $('#youtubeModal').fadeIn(300);
    
    $('#youtubeModal').on('click', function(e) {
        if (e.target == this) {
            closeYoutubeModal();
        }
    });
}

function closeYoutubeModal() {
    var iframe = document.getElementById('modalYoutubeFrame');
    iframe.src = ""; // 소리 끄기
    $('#youtubeModal').fadeOut(300);
}
</script>


<div class="bo_sch_wrap">
    <fieldset class="bo_sch">
        <h3>검색</h3>
        <legend>게시물 검색</legend>
        <form name="fsearch" method="get">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sca" value="<?php echo $sca ?>">
        <input type="hidden" name="sop" value="and">
        <label for="sfl" class="sound_only">검색대상</label>
        
        <select name="sfl" id="sfl" class="select">
            <?php echo get_board_sfl_select_options($sfl); ?>
        </select>
        
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
    // 게시판 검색
    $(".btn_bo_sch").on("click", function() {
        $(".bo_sch_wrap").toggle();
    })
    $('.bo_sch_bg, .bo_sch_cls').click(function(){
        $('.bo_sch_wrap').hide();
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