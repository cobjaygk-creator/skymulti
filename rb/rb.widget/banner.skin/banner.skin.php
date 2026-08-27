<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

global $row_mod, $rb_module_table;
$rb_skin = sql_fetch("select * from {$rb_module_table} where md_id = '{$row_mod['md_id']}'"); // 환경설정 테이블 조회 (삭제금지)
$md_banner_bg = isset($rb_skin['md_banner_bg']) ? $rb_skin['md_banner_bg'] : '';
?>

<!-- Font Awesome (무료 버전) CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-HHsOC...your-integrity-hash..." crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
/* 기존 스와이퍼 관련 스타일 */
.swiper-wrapper-slide_wide_bn { text-align: center; }
.swiper-button-next-wide { right:10%; }
.swiper-button-prev-wide { left:10%; }
@media all and (max-width:1024px) {
    .swiper-button-next-wide { right:20px; }
    .swiper-button-prev-wide { left:20px; }
    .swiper-button-next-wide svg { width: 10px; }
    .swiper-button-prev-wide svg { width: 10px; }
    .rb_wide_bn_wrap { padding: 0px; }
}

/* 추가: 하단 아이콘 박스 */
.bottom-icons-box {
    position: relative;
    width: 100%;
    max-width: 1200px;
    margin: -49px auto 0 auto; /* 배너 하단에 겹치도록 (필요시 조정) */
    z-index: 15;
}
.top_ad img {
    width: 100%;
    height: 600px;
    box-sizing: border-box;
}
.bottom-icons-inner {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}
.hot-event-btn {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-decoration: none;
    color: #000;
    font-weight: bold;
}
.hot-event-btn .main-text {
    font-size: 1.5rem;
    margin-bottom: 5px;
}
.hot-event-btn .sub-text {
    font-size: 1.1rem;
    color: #999;
}
/* 기존 데스크탑용 아이콘 목록 수정: 슬라이드형으로 변경 (좌우 스크롤) */
.icon-list {
    display: contents;
    flex-wrap: nowrap;
    gap: 15px;
    justify-content: center; /* 아이콘 중앙정렬 */
    overflow-x: auto;
}
.icon-list .icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 1.0rem;
    color: #333;
    text-decoration: none;
    width: 80px;
    text-align: center;
}
.icon-list .icon i {
    font-size: 2.5rem; /* 아이콘 크기 */
    margin-bottom: 5px;
}

/* 모바일 환경에서 아이콘 영역을 깔끔하게 보이도록 수정 (슬라이드형 유지) */
@media (max-width: 768px) {
    .swiper-container {
        height: 250px;
    }
    .arrow-btn {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
    .arrow-left { left: 10px; }
    .arrow-right { right: 10px; }
    .bottom-icons-box {
        margin: -30px auto 0 auto;
    }
    .bottom-icons-inner {
        flex-direction: row;
        justify-content: space-around;
        align-items: center;
    }
    .top_ad img {
        width: 100%;
        height: auto;
        box-sizing: border-box;
    }

    .hot-event-btn {
        margin-bottom: 10px;
        align-items: center;
        text-align: center;
    }

    .icon-list {
        display: grid;  /* grid 레이아웃으로 변경 */
        grid-template-columns: repeat(4, 1fr);  /* 1줄에 4개씩 배치 */
        gap: 10px; /* 아이콘 간의 간격 설정 */
        row-gap: 15px;  /* 첫 번째 줄과 두 번째 줄 간의 간격 설정 */
        width: 100%; /* 아이콘 목록을 가득 차게 설정 */
        justify-items: center;  /* 아이콘을 가운데 정렬 */
    }
    .icon-list .icon {
        font-size: 1.2rem; /* 아이콘 크기 증가 */
        text-align: center;
        margin-bottom: 0;  /* 아이콘 간의 간격을 0으로 유지 */
    }
    .icon-list .icon i {
        font-size: 2rem; /* 아이콘 크기 증가 */
        margin-bottom: 5px;
    }
}

/* 추가: 이쁘고 색감있는 아이콘 스타일 (아이콘별 컬러 지정) */
.icon-list .icon:nth-child(1) i { color: #FF5722; } /* Deep Orange */
.icon-list .icon:nth-child(2) i { color: #9C27B0; } /* Purple */
.icon-list .icon:nth-child(3) i { color: #3F51B5; } /* Indigo */
.icon-list .icon:nth-child(4) i { color: #009688; } /* Teal */
.icon-list .icon:nth-child(5) i { color: #4CAF50; } /* Green */
.icon-list .icon:nth-child(6) i { color: #FFC107; } /* Amber */
.icon-list .icon:nth-child(7) i { color: #E91E63; } /* Pink */
.icon-list .icon:nth-child(8) i { color: #00BCD4; } /* Cyan */
</style>

<?php
$i = 0; // $i 변수를 초기화

while ($row = sql_fetch_array($result)) {
    $bn_border  = isset($row['bn_border']) && $row['bn_border'] ? ' bn_border' : '';
    $bn_radius  = isset($row['bn_radius']) && $row['bn_radius'] ? ' bn_radius' : '';
    
    // 새창 옵션
    $bn_new_win = isset($row['bn_new_win']) && $row['bn_new_win'] ? ' target="_blank"' : '';
    
    if ($i == 0) {
        // swiper-container에 "slide-widget" 클래스 추가
        echo '<div class="mod_bn_wrap rb_wide_bn_wrap rb_wide_bn_'.$row_mod['md_id'].'" style="background-color:'.$md_banner_bg.'">';
        echo '<div class="swiper-container swiper-container-slide_wide_bn_'.$row_mod['md_id'].' slide-widget">';
        echo '<ul class="swiper-wrapper swiper-wrapper-slide_wide_bn swiper-wrapper-slide_wide_bn_'.$row_mod['md_id'].'">'.PHP_EOL;
    }
    
    $bimg = G5_DATA_PATH.'/banners/'.$row['bn_id'];
    if (file_exists($bimg)) {
        $banner = '';
        $size = getimagesize($bimg);
        $img_width = $size[0];
        echo '<div class="swiper-slide swiper-slide-slide_wide_bn_'.$row_mod['md_id'].' slide_item top_ad">'.PHP_EOL;
        if ($row['bn_url'][0] == '#')
            $banner .= '<a href="'.$row['bn_url'].'">';
        else if ($row['bn_url'] && $row['bn_url'] != 'http://') {
            $banner .= '<a href="'.G5_URL.'/rb/rb.mod/banner/bannerhit.php?bn_id='.$row['bn_id'].'"'.$bn_new_win.'>';
        }
        echo $banner.'<img src="'.G5_DATA_URL.'/banners/'.$row['bn_id'].'?ver='.G5_SERVER_TIME.'" title="'.get_text($row['bn_alt']).'" width="100%" class="'.$bn_radius.'" style="max-width:'.$img_width.'px;">';
        if ($banner) {
            echo '</a>'.PHP_EOL;
        }
        
        if (isset($row['bn_ad_ico']) && $row['bn_ad_ico']) {
            echo '<span class="ico_ad">AD</span>'.PHP_EOL;
        }
        echo '</div>'.PHP_EOL;
    }
    $i++;
}

if ($i > 0) echo '</ul>';

if ($i > 1) echo '
<div class="swiper-button-next swiper-button-next-wide swiper-button-next-wide_'.$row_mod['md_id'].'">
<svg width="24" height="46" viewBox="0 0 24 46" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1 45L22.3333 23L1 1" stroke="#09244B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</div>
<div class="swiper-button-prev swiper-button-prev-wide swiper-button-prev-wide_'.$row_mod['md_id'].'">
<svg width="24" height="46" viewBox="0 0 24 46" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M23 0.999999L1.66667 23L23 45" stroke="#09244B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
</div>'.PHP_EOL;

if ($i > 0) echo '</div></div>';
?>

<!-- 하단 아이콘 박스 -->
<div class="bottom-icons-box">
  <div class="bottom-icons-inner">
    <!-- "지금 핫한 이벤트 보기" 버튼 -->
    <a href="#" class="hot-event-btn">
      <span class="main-text">🔥지금 파이로 쇼핑해볼까?</span>
      <span class="sub-text">Pi shopping</span>
    </a>
    <!-- 아이콘 메뉴 (Font Awesome 아이콘 사용) -->
    <div class="icon-list">
      <a href="#" class="icon">
        <i class="fa-brands fa-pied-piper"></i>
        <span>매장입점</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-truck-fast"></i>
        <span>매장지도</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-utensils"></i>
        <span>준비중</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-shirt"></i>
        <span>준비중</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-spray-can-sparkles"></i>
        <span>준비중</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-apple-whole"></i>
        <span>준비중</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-film"></i>
        <span>준비중</span>
      </a>
      <a href="#" class="icon">
        <i class="fa-solid fa-video"></i>
        <span>준비중</span>
      </a>
    </div>
  </div>
</div>

<!-- Swiper JS CDN -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container-slide_wide_bn_<?php echo $row_mod['md_id'] ?>', {
        slidesPerView: 1,
        spaceBetween: 0,
        observer: true,
        observeParents: true,
        autoHeight: true,
        <?php if ($i > 1) { ?>
            touchRatio: 1,
            loop: true,
        <?php } else { ?>
            touchRatio: 0,
            loop: false,
        <?php } ?>
        navigation: {
            nextEl: '.swiper-button-next-wide_<?php echo $row_mod['md_id'] ?>',
            prevEl: '.swiper-button-prev-wide_<?php echo $row_mod['md_id'] ?>',
        },
        autoplay: {
            delay: 3000, // 3초마다 자동 슬라이드
            disableOnInteraction: false,
        }
    });
</script>
<script>
    // 부모 width를 무시하고 div를 100%로 만들고, 모듈설정 버튼의 100% 처리를 위해 스크립트를 사용합니다.
    function adjustDivWidth_<?php echo $row_mod['md_id'] ?>() {
        const content_w = $('.rb_wide_bn_<?php echo $row_mod['md_id'] ?>');
        const firstAdminOv_w = content_w.nextUntil('.admin_ov').next('.admin_ov');
        
        if ($(window).width() > <?php echo $rb_core['main_width'] ?>) {
            content_w.css({
                'width': '100vw',
                'position': 'relative',
                'left': '50%',
                'transform': 'translateX(-50%)'
            });
            firstAdminOv_w.css({
                'width': '100vw',
                'left': '50%',
                'transform': 'translateX(-50%)'
            });
        } else {
            content_w.css({
                'width': '100%',
                'position': 'static',
                'left': '0',
                'transform': 'none'
            });
            firstAdminOv_w.css({
                'width': '100%',
                'left': '0',
                'transform': 'none'
            });
        }
    }
    $(document).ready(adjustDivWidth_<?php echo $row_mod['md_id'] ?>);
    $(window).resize(adjustDivWidth_<?php echo $row_mod['md_id'] ?>);
</script>
</body>
</html>
