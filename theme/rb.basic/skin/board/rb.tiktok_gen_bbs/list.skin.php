<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// ==============================================================================
// [핵심 함수] 틱톡 URL에서 비디오 ID 추출
// ==============================================================================
function get_tiktok_id($url) {
    preg_match('~(?:video/|embed/v2/|player/v1/)([0-9]{8,})~i', (string)$url, $matches);
    if (isset($matches[1])) {
        return $matches[1];
    }

    // 영상 ID만 저장된 사용자 입력도 지원
    if (preg_match('/^([0-9]{8,})$/', trim((string)$url), $matches)) {
        return $matches[1];
    }

    return '';
}

// ==============================================================================
// [시작] 틱톡 RSS 연동 및 자동 저장 로직
// ==============================================================================
$tiktok_rss_url = 'https://rss.app/feeds/oqgZK17Bf3lGKvci.xml'; // ★ 사용자 RSS URL
$is_sync_mode   = (isset($_GET['exec']) && $_GET['exec'] == 'tiktok_sync'); 

// Manual synchronization uses the isolated module shared with cron.
if ($is_admin && $is_sync_mode) {
    include_once G5_PLUGIN_PATH.'/tiktok_rss/tiktok_rss.lib.php';
    try {
        $sync_result = skymulti_tiktok_sync(array('bo_table' => $bo_table));
        alert($sync_result['inserted'].'개의 TikTok 영상이 동기화되었습니다.', G5_BBS_URL.'/board.php?bo_table='.$bo_table);
    } catch (Exception $sync_error) {
        alert('TikTok RSS 동기화 실패: '.$sync_error->getMessage());
    }
}

// Old inline code is retained for one-step rollback, but cannot execute.
if (false && $is_admin && $is_sync_mode && $tiktok_rss_url) {
    // 1. RSS XML 로드
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tiktok_rss_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); 
    $xml_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($http_code != 200 || !$xml_data) {
        alert("RSS 피드에 접속할 수 없습니다.\\n응답코드: {$http_code}\\n에러내용: {$curl_error}");
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
    $entries = isset($xml->channel->item) ? $xml->channel->item : $xml->entry;

    if($entries) {
        foreach ($entries as $entry) {
            $namespaces = $entry->getNamespaces(true);
            $media = $entry->children($namespaces['media']);

            $title = trim((string)$entry->title);
            $link  = trim((string)$entry->link);
            $videoId = get_tiktok_id($link); 
            
            if (!$videoId) continue;

            $description = trim(strip_tags((string)$entry->description)); 
            $pubDate = (string)$entry->pubDate;

            // 썸네일 추출
            $thumbnail_url = '';
            if (isset($media->content) && isset($media->content->attributes()->url)) {
                $thumbnail_url = (string)$media->content->attributes()->url;
            } elseif (isset($media->thumbnail) && isset($media->thumbnail->attributes()->url)) {
                $thumbnail_url = (string)$media->thumbnail->attributes()->url;
            } else {
                preg_match('/<img[^>]+src="([^">]+)"/', (string)$entry->description, $match);
                if (isset($match[1])) {
                    $thumbnail_url = $match[1];
                }
            }

            // 본문 생성 (iframe)
            $content_html = '<div style="position:relative;padding-bottom:177.77%;height:0;overflow:hidden;margin-bottom:20px;"><iframe src="https://www.tiktok.com/embed/v2/'.$videoId.'" style="position:absolute;top:0;left:0;width:100%;height:100%;" frameborder="0" allowfullscreen></iframe></div>';
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
    }

    // [Step 2] 정렬
    usort($items, function($a, $b) {
        return $a['timestamp'] - $b['timestamp'];
    });

    // [Step 3] DB 저장
    foreach ($items as $item) {
        // 중복 체크
        $row = sql_fetch(" select count(*) as cnt from {$write_table} where wr_link1 like '%{$item['videoId']}%' ");
        if ($row['cnt'] > 0) continue; 

        $wr_num = get_next_num($write_table);
        $subject = $item['title'];
        $content = $item['content'];
        $pubdate = date('Y-m-d H:i:s', $item['timestamp']);
        $img_src = $item['thumb_url'];
        
        $ca_name = '틱톡'; 
        $final_link = $item['link'];

        $sql = " insert into {$write_table}
                    set wr_num = '{$wr_num}',
                        wr_reply = '',
                        wr_comment = 0,
                        ca_name = '{$ca_name}', 
                        wr_option = 'html1',
                        wr_subject = '".sql_real_escape_string($subject)."',
                        wr_content = '".sql_real_escape_string($content)."',
                        wr_link1 = '{$final_link}', 
                        wr_link2 = '',
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
                        wr_ip = '{$_SERVER['REMOTE_ADDR']}' ";
        sql_query($sql);
        
        $wr_id = sql_insert_id();
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
                                        bf_source = 'tiktok_thumb.{$ext}',
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
        usleep(200000); 
    }

    sql_query(" update {$g5['board_table']} set bo_count_write = bo_count_write + {$cnt} where bo_table = '{$bo_table}' ");
    alert($cnt."개의 틱톡 영상을 가져왔습니다.", G5_BBS_URL."/board.php?bo_table=".$bo_table);
}

add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
$bo_gallery_cols = isset($board['bo_gallery_cols']) ? $board['bo_gallery_cols'] : '3';
?>

<style>
/* -------------------------------------------------------------------------
   [수정됨] 틱톡 썸네일 전용 스타일 (세로 비율 고정)
   ------------------------------------------------------------------------- */
.gallery-item-img {
    position: relative;
    width: 100%;
    aspect-ratio: 9 / 16;
    padding-bottom: 0 !important;
    overflow: hidden;
    border-radius: 8px; /* 모서리 둥글게 */
    background-color: #000; /* 로딩 중 배경색 */
    box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* 그림자 효과 */
}

@supports not (aspect-ratio: 9 / 16) {
    .gallery-item-img { height: 0; padding-bottom: 177.77% !important; }
}

.tiktok-thumb-link {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 4;
    pointer-events: auto;
}

/* 이미지가 찌그러지지 않고 영역을 꽉 채우도록 설정321 */
.gallery-item-img img {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important; /* 비율 유지하며 꽉 채우기 */
    border: none !important;
}

.tiktok-play-icon {
    position: absolute; z-index: 6; top: 50%; left: 50%;
    width: 56px; height: 56px; display: flex;
    align-items: center; justify-content: center;
    border-radius: 50%; color: #fff; background: rgba(0,0,0,.62);
    box-shadow: 0 4px 18px rgba(0,0,0,.35);
    transform: translate(-50%, -50%); pointer-events: none;
    transition: transform .2s ease, background-color .2s ease;
}
.tiktok-play-icon svg { width: 25px; height: 25px; margin-left: 3px; fill: currentColor; }
.tiktok-thumb-link:hover + .tiktok-play-icon,
.tiktok-thumb-link:focus + .tiktok-play-icon {
    background: rgba(254,44,85,.9);
    transform: translate(-50%, -50%) scale(1.08);
}

/* 틱톡 뱃지 */
.tiktok_badge { 
    position:absolute; top:10px; right:10px; 
    padding:3px 8px; background:rgba(0,0,0,0.7); 
    color:#fff; font-size:11px; border-radius:4px; z-index:5; 
}

/* 모달 스타일 */
.yt-modal-overlay { display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.85); backdrop-filter: blur(5px); }
.yt-modal-overlay.is-open { display: block !important; }
.yt-modal-content { 
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
    width: min(90vw, calc((100vh - 40px) * 9 / 16)); max-width: 400px;
    aspect-ratio: 9 / 16; max-height: calc(100vh - 40px);
    background: #000; box-shadow: 0 5px 30px rgba(0,0,0,0.5); 
    border-radius: 12px; overflow: hidden;
}
.yt-video-wrapper { position: relative; width: 100%; height: 100%; overflow: hidden; }
.yt-video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
.yt-modal-close { 
    position: absolute; top: 10px; right: 10px; z-index: 100;
    color: #fff; font-size: 30px; font-weight: bold; cursor: pointer; 
    background: rgba(0,0,0,0.5); border: none; width: 40px; height: 40px; border-radius: 50%; 
}

/* 관리자 버튼 스타일 */
.admin-view-btn {
    position: absolute; bottom: 10px; left: 10px; z-index: 20; 
    width: 28px; height: 28px; background-color: rgba(0,0,0,0.6); 
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
}
.admin-view-btn svg { width: 16px; height: 16px; fill: #fff; }
</style>


<h3 class="board_title_n"><?php echo get_head_title($g5['title']); ?></h3>        
<div class="rb_bbs_wrap" id="scroll_container" style="width:<?php echo $width; ?>">
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
            <div class="btns_gr">
                <?php if ($is_admin) { ?>
                <button type="button" class="fl_btns" onclick="if(confirm('틱톡 영상을 가져오시겠습니까?')) location.href='<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>&exec=tiktok_sync';">
                <img src="<?php echo $board_skin_url ?>/img/ico_rss.svg" style="filter: hue-rotate(180deg);">
                <span class="tooltips" style="width:110px;">틱톡 가져오기</span>
                </button>
                <?php } ?>
                
                <?php if ($write_href) { ?>
                <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                <img src="<?php echo $board_skin_url ?>/img/ico_write.svg">
                <span class="tooltips">글 등록</span>
                </button>
                <?php } ?>

                
                </div>
            <div class="cb"></div>
        </div>
    </div>
    
    <div class="swiper-container swiper-container-<?php echo $bo_table ?>">
    <ul class="gallery_top_mt rb_bbs_list swiper-wrapper">
        <?php 
        for ($i=0; $i<count($list); $i++) { 
            // ★ 수정: 썸네일 생성 시 세로 비율(9:16)에 맞는 크기로 요청 (가로 400, 세로 712)
            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], 400, 712, false, true);
            
            if($thumb['src']) {
                $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >';
            } else { 
                $img_content = '<img src="'.G5_THEME_URL.'/rb.img/no_image.png" alt="이미지가 없습니다." >';
            }
            
            $wr_link1 = $list[$i]['wr_link1'];
            $video_id = '';
            
            if($wr_link1) $video_id = get_tiktok_id($wr_link1);
            if(!$video_id && !empty($list[$i]['wr_content'])) {
                $video_id = get_tiktok_id($list[$i]['wr_content']);
            }
            
            if($video_id) {
                $link_attr = "href='#tiktokModal' data-video-id='{$video_id}' onclick=\"return openTiktokModal('{$video_id}', event);\"";
            } else {
                $link_attr = "href='{$list[$i]['href']}'";
            }
        ?>
        
        <div class="gallery_v_mtop swiper-slide">
            <div class="gallery-item-img">
                <span class="tiktok_badge">TikTok</span>
                <a <?php echo $link_attr; ?> class="tiktok-thumb-link" aria-label="<?php echo $video_id ? '틱톡 영상 재생' : '게시물 보기'; ?>"><?php echo $img_content; ?></a>
                <?php if ($video_id) { ?>
                <span class="tiktok-play-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                </span>
                <?php } ?>
                
                <?php if ($is_admin) { ?>
                <a href="<?php echo $list[$i]['href']; ?>" class="admin-view-btn" title="상세보기" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                </a>
                <?php } ?>

                <?php if ($is_checkbox) { ?>
                <div class="gall_chk_is" style="position:absolute; bottom:10px; right:10px; z-index:20;">
                    <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
                    <label for="chk_wr_id_<?php echo $i ?>"></label>
                </div>
                <?php } ?>
            </div>
            
            <div style="margin-top:10px; padding:0 5px;">
                <div style="font-size:12px; color:#888; margin-bottom:4px;">
                    <?php echo passing_time($list[$i]['wr_datetime']) ?>
                </div>
                <div class="gallery-item-tit cut2">
                    <a <?php echo $link_attr; ?> class="font-B" style="font-size:14px; line-height:1.4;">
                        <?php echo $list[$i]['subject'] ?>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </ul>
    </div>
    
    <?php if (count($list) == 0) { echo "<div class=\"no_data\" style=\"text-align:center; padding:50px 0;\">게시물이 없습니다.</div>"; } ?>

    <?php if ($is_checkbox) { ?>
    <ul class="btm_btns">
        <li class="btm_btns_left">
            <button type="button" class="fl_btns" onclick="toggle_all_checked();">
                <span class="font-B">전체선택</span>
            </button>
            <button type="submit" name="btn_submit" class="fl_btns" value="선택삭제" onclick="document.pressed=this.value">
                <span class="font-B">선택삭제</span>
            </button>
        </li>
        <li class="cb"></li>
    </ul>
    <?php } ?>

    <?php echo $write_pages; ?>
    </form>
</div>

<div id="tiktokModal" class="yt-modal-overlay" role="dialog" aria-modal="true" aria-label="틱톡 영상" aria-hidden="true">
    <div class="yt-modal-content">
        <button type="button" class="yt-modal-close" onclick="closeTiktokModal()" aria-label="영상 닫기">&times;</button>
        <div class="yt-video-wrapper">
            <iframe id="modalTiktokFrame" src="" title="TikTok video player" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
function openTiktokModal(videoId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    if(!/^\d+$/.test(String(videoId))) return false;
    var iframe = document.getElementById('modalTiktokFrame');
    var modal = document.getElementById('tiktokModal');
    if (!iframe || !modal) return false;
    iframe.src = "https://www.tiktok.com/player/v1/" + encodeURIComponent(videoId) + "?autoplay=1&rel=0";
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    var closeButton = modal.querySelector('.yt-modal-close');
    if (closeButton) closeButton.focus();
    return false;
}

function closeTiktokModal() {
    var iframe = document.getElementById('modalTiktokFrame');
    var modal = document.getElementById('tiktokModal');
    if (!modal) return;
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('is-open');
    if (iframe) iframe.src = "";
    document.body.style.overflow = '';
}

// Swiper가 일반 click 이벤트를 취소해도 먼저 재생 이벤트를 처리한다.
document.addEventListener('click', function(e) {
    var link = e.target.closest ? e.target.closest('.tiktok-thumb-link[data-video-id]') : null;
    if (!link) return;
    openTiktokModal(link.getAttribute('data-video-id'), e);
}, true);

document.getElementById('tiktokModal').addEventListener('click', function(e) {
    if (e.target === this) closeTiktokModal();
});

document.addEventListener('keydown', function(e) {
    var modal = document.getElementById('tiktokModal');
    if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) closeTiktokModal();
});

// Swiper 반응형 설정
var swiper = new Swiper('.swiper-container-<?php echo $bo_table ?>', {
    slidesPerView: 4, // PC 기본 4개
    spaceBetween: 20,
    slidesPerColumnFill: 'row',
    slidesPerColumn: 9999, // 줄바꿈 허용
    observer: true,
    observeParents: true,
    breakpoints: {
        1200: { slidesPerView: 4, spaceBetween: 20 },
        900:  { slidesPerView: 3, spaceBetween: 15 },
        600:  { slidesPerView: 2, spaceBetween: 10 },
        0:    { slidesPerView: 2, spaceBetween: 10 } // 모바일 2열
    }
});
</script>

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]") {
            f.elements[i].checked = sw;
        }
    }
}

function toggle_all_checked() {
    var f = document.fboardlist;
    var has_unchecked = false;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && !f.elements[i].checked) {
            has_unchecked = true;
            break;
        }
    }

    all_checked(has_unchecked);
}

function fboardlist_submit(f) {
    var chk_count = 0;
    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked) chk_count++;
    }
    if (!chk_count) { alert("게시물을 선택하세요."); return false; }
    if(document.pressed == "선택삭제") {
        if (!confirm("정말 삭제하시겠습니까?")) return false;
        f.removeAttribute("target");
        f.action = "<?php echo G5_BBS_URL; ?>/board_list_update.php";
        var input = document.createElement("input");
        input.type = "hidden"; input.name = "btn_submit"; input.value = "선택삭제";
        f.appendChild(input);
    }
    return true;
}
</script>
<?php } ?>
