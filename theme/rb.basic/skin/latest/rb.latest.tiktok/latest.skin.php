<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$widget_id = 'rb_tk_' . $rb_skin['md_id'];
$rb_skin = sql_fetch (" select * from {$rb_module_table} where md_id = '{$options}' "); 

$thumb_width = 450;
$thumb_height = 800; 
$list_count = (is_array($list) && $list) ? count($list) : 0;

if($rb_skin['md_title']) { $bo_subject = $rb_skin['md_title']; } 
else { $bo_subject = $rb_skin['md_title']; }

if($rb_skin['md_sca']) { $links_url = get_pretty_url($bo_table,'','sca='.urlencode($rb_skin['md_sca'])); } 
else { $links_url = get_pretty_url($bo_table); }
?>

<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/style.css">

<style>
/* --- 애니메이션 및 레이아웃 --- */
.rb-tk-reveal {
    opacity: 0;
    transform: translateY(15px);
    transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    will-change: transform, opacity;
}
.rb-tk-reveal.active {
    opacity: 1;
    transform: translateY(0);
}

/* 모달 스타일 */
.rb-tk-modal-overlay { display: none; position: fixed; z-index: 9999999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(5px); }
.rb-tk-modal-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 1000px; background: transparent !important; }
.rb-tk-video-wrapper { position: relative; width: 100%; height: 0; padding-bottom: 56.25%; overflow: hidden; border-radius: 12px; background: #000; }
.rb-tk-video-wrapper iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border:0; }
.rb-tk-modal-close { position: absolute; top: -50px; right: -10px; color: #fff; font-size: 40px; cursor: pointer; background: none; border: none; z-index: 20; }
.rb-tk-modal-content.is-vertical { width: 331px !important; height: 780px !important; max-width: 90vw !important; max-height: 90vh !important; }
.rb-tk-video-wrapper.is-vertical { width: 100% !important; height: 100% !important; padding-bottom: 0 !important; }

.rb-tk-container .swiper-slide { width: 250px !important; }
@media (max-width: 768px) { .rb-tk-container .swiper-slide { width: 165px !important; } }

.rb-tk-play-icon {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 50px; height: 50px; background-color: rgba(0,0,0,0.4); border-radius: 50%;
    background-image: url('https://cdn-icons-png.flaticon.com/512/3046/3046121.png');
    background-repeat: no-repeat; background-position: center center; background-size: 28px;
    pointer-events: none; transition: 0.2s; z-index: 10;
}
</style>

<div class="rb-tk-main-wrap" id="reveal_target_<?php echo $widget_id; ?>">
    <div class="rb-tk-header" style="display:<?php echo (isset($rb_skin['md_title_hide']) && $rb_skin['md_title_hide'] == '1') ? 'none' : 'flex'; ?>;">
        <div class="rb-tk-title-box">
            <a href="<?php echo $links_url; ?>">
                <h2 style="color:<?php echo $rb_skin['md_title_color'] ?>; font-size:<?php echo $rb_skin['md_title_size'] ?>px;"><?php echo $bo_subject ?></h2>
            </a>
        </div>
        <div class="rb-tk-nav-group">
            <div class="rb-tk-btn-prev rb-tk-nav"><img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_prev.svg"></div>
            <div class="rb-tk-btn-next rb-tk-nav"><img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_next.svg"></div>
        </div>
    </div>
    
    <div class="rb-tk-slider-outer">
        <div class="swiper-container rb-tk-container" id="<?php echo $widget_id; ?>">
            <div class="swiper-wrapper">
                <?php
                for ($i=0; $i<$list_count; $i++) {
                    $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height, false, true);
                    $video_url = $list[$i]['wr_link1'] ?: $list[$i]['wr_1'];
                    $video_id = ''; $video_platform = '';

                    if (strpos($video_url, 'tiktok.com') !== false && preg_match('/video\/([0-9]+)/', $video_url, $match)) {
                        $video_id = $match[1]; $video_platform = 'tiktok';
                    } else if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video_url, $match)) {
                        $video_id = $match[1]; $video_platform = 'youtube';
                    }

                    $img = $thumb['src'] ?: "https://i.ytimg.com/vi/".$video_id."/hqdefault.jpg";
                    if(!$video_id && !$thumb['src']) $img = G5_THEME_URL.'/rb.img/no_image.png';
                    $link_attr = $video_id ? "href='javascript:;' onclick=\"openRbTkModal('{$video_id}', true, '{$video_platform}'); return false;\"" : "href='".get_pretty_url($bo_table, $list[$i]['wr_id'])."'";
                ?>
                <div class="swiper-slide rb-tk-reveal">
                    <div class="rb-tk-card">
                        <div class="rb-tk-thumb-box">
                            <a <?php echo $link_attr; ?>>
                                <img src="<?php echo $img ?>" alt="" class="rb-tk-img">
                                <?php if($video_id) { ?><div class="rb-tk-play-icon"></div><?php } ?>
                            </a>
                        </div>
                        <div class="rb-tk-info-box">
                            <div class="rb-tk-subject cut"><a <?php echo $link_attr; ?>><?php echo $list[$i]['subject'] ?></a></div>
                            <div class="rb-tk-meta"><?php echo $list[$i]['wr_name'] ?> · <?php echo passing_time($list[$i]['wr_datetime']) ?></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div id="rbTkModal" class="rb-tk-modal-overlay">
    <div class="rb-tk-modal-content">
        <button class="rb-tk-modal-close" onclick="closeRbTkModal()">&times;</button>
        <div class="rb-tk-video-wrapper">
            <iframe id="rbTkFrame" src="" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var <?php echo $widget_id; ?>_swiper = new Swiper('#<?php echo $widget_id; ?>', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        autoplay: { delay: 3000, disableOnInteraction: false },
        navigation: { nextEl: '.rb-tk-btn-next', prevEl: '.rb-tk-btn-prev' },
    });

    const revealTarget = document.querySelector('#reveal_target_<?php echo $widget_id; ?>');
    const revealItems = revealTarget.querySelectorAll('.rb-tk-reveal');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                revealItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.classList.add('active');
                    }, index * 40);
                });
                observer.unobserve(entry.target);
            }
        });
    }, { 
        threshold: 0.01,
        rootMargin: '50px 0px'
    });

    observer.observe(revealTarget);
    $('#rbTkModal').appendTo('body');
});

function openRbTkModal(videoId, isVertical, platform) {
    if(!videoId) return;
    var iframe = document.getElementById('rbTkFrame');
    var modalContent = document.querySelector('.rb-tk-modal-content');
    var videoWrapper = document.querySelector('.rb-tk-video-wrapper');

    modalContent.classList.remove('is-vertical');
    videoWrapper.classList.remove('is-vertical');

    var embedUrl = "";
    if (platform === 'tiktok') {
        embedUrl = "https://www.tiktok.com/embed/v2/" + videoId;
        modalContent.classList.add('is-vertical');
        videoWrapper.classList.add('is-vertical');
    } else {
        embedUrl = "https://www.youtube.com/embed/" + videoId + "?autoplay=1&rel=0";
        if(isVertical) {
             modalContent.classList.add('is-vertical');
             videoWrapper.classList.add('is-vertical');
        }
    }

    iframe.src = embedUrl;
    $('#rbTkModal').fadeIn(300);
}

function closeRbTkModal() {
    var iframe = document.getElementById('rbTkFrame');
    iframe.src = ""; 
    $('#rbTkModal').fadeOut(300);
}
</script>