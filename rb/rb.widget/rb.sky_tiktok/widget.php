<style>
    /* 전체 컨테이너 */
    .tiktok-marquee-wrap {
        width: 100%;
        position: relative;
        overflow: hidden; /* 넘치는 부분 숨김 */
        padding: 40px 0;
        background: #fff; /* 배경색 (페이드 효과와 맞춰야 함) */
    }

    /* 좌우 페이드 효과 (가림막) */
    .tiktok-marquee-wrap::before,
    .tiktok-marquee-wrap::after {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        width: 0; /* 가려지는 영역의 너비 */
        z-index: 2;
        pointer-events: none; /* 클릭 통과 */
    }

    /* 왼쪽 페이드 */
    .tiktok-marquee-wrap::before {
        left: 0;
        background: linear-gradient(to right, #fff, transparent);
    }

    /* 오른쪽 페이드 */
    .tiktok-marquee-wrap::after {
        right: 0;
        background: linear-gradient(to left, #fff, transparent);
    }

    /* 움직이는 트랙 */
    .tiktok-track {
        display: flex;
        gap: 20px; /* 아이템 사이 간격 */
        width: max-content; /* 내용물만큼 길어짐 */
        animation: scroll-left 40s linear infinite; /* 40초 동안 흐름 (속도 조절 가능) */
    }

    /* 마우스 올리면 멈춤 */
    .tiktok-track:hover {
        animation-play-state: paused;
    }

    /* 개별 아이템 (크기 고정) */
    .tiktok-item {
        width: 190px; /* ★ 여기서 카드 크기 조절 */
        flex-shrink: 0; /* 화면이 작아져도 찌그러지지 않음 */
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 9 / 16; /* 틱톡 비율 고정 */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background: #000;
        transition: transform 0.3s;
    }
    
    .tiktok-item:hover {
        transform: scale(1.05); /* 호버 시 살짝 커짐 */
        z-index: 1;
    }

    .tiktok-link {
        display: block;
        width: 100%;
        height: 100%;
    }

    .tiktok-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tiktok-icon {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 40px; height: 40px;
        background: url('https://cdn-icons-png.flaticon.com/512/3046/3046121.png') no-repeat center/contain;
        opacity: 0.8;
    }

    /* 흐르는 애니메이션 정의 */
    @keyframes scroll-left {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); } /* 전체 길이의 절반(원본 세트)만큼 이동 후 리셋 */
    }

    /* 모바일 조정 */
    @media (max-width: 768px) {
        .tiktok-marquee-wrap::before,
        .tiktok-marquee-wrap::after {
            width: 50px; /* 모바일에서는 페이드 영역 좁게 */
        }
        .tiktok-item {
            width: 160px; /* 모바일에서는 카드 크기 축소 */
        }
    }
</style>

<div class="tiktok-marquee-wrap">
    <div class="tiktok-track">
    <?php
    // ==========================================
    // 설정
    // ==========================================
    $rss_url = "https://rss.app/feeds/itRv1YB8tQ8GWzb7.xml"; 
    $cache_file = G5_DATA_PATH . '/cache/tiktok_rss_cache.xml'; 
    $cache_time = 21600; 

    // 데이터 가져오기 로직 (cURL)
    if(!is_dir(dirname($cache_file))) @mkdir(dirname($cache_file), 0755, true);

    $rss_data = "";
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        $rss_data = file_get_contents($cache_file);
    } else {
        if(function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rss_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
            $rss_data = curl_exec($ch);
            curl_close($ch);
            if ($rss_data) file_put_contents($cache_file, $rss_data);
        }
    }

    // 아이템 리스트 생성
    $items_html = ""; 
    
    if ($rss_data) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($rss_data);
        
        if ($xml && isset($xml->channel->item)) {
            $limit = 10; // 10개를 가져옵니다
            $count = 0;

            foreach ($xml->channel->item as $item) {
                if ($count >= $limit) break;

                $title = (string)$item->title;
                $link = (string)$item->link;
                
                // 썸네일 추출
                $thumb = '';
                $media = $item->children('media', true);
                if (isset($media->content) && isset($media->content->attributes()->url)) {
                    $thumb = (string)$media->content->attributes()->url;
                } elseif (isset($media->thumbnail) && isset($media->thumbnail->attributes()->url)) {
                    $thumb = (string)$media->thumbnail->attributes()->url;
                } else {
                    preg_match('/<img[^>]+src="([^">]+)"/', (string)$item->description, $match);
                    if (isset($match[1])) $thumb = $match[1];
                }
                if (!$thumb) $thumb = 'https://via.placeholder.com/200x350?text=No+Image';

                // HTML 조립
                $items_html .= '<div class="tiktok-item">';
                $items_html .= '  <a href="' . $link . '" target="_blank" class="tiktok-link">';
                $items_html .= '    <img src="' . $thumb . '" alt="' . htmlspecialchars($title) . '" class="tiktok-thumb">';
                $items_html .= '    <div class="tiktok-icon"></div>';
                $items_html .= '  </a>';
                $items_html .= '</div>';

                $count++;
            }
        }
    }

    // ★ 중요: 무한 루프처럼 보이게 하기 위해 데이터를 두 번 출력합니다 ★
    if ($items_html) {
        echo $items_html; // 원본 세트
        echo $items_html; // 복제 세트 (이어지는 부분)
    } else {
        echo '<p style="padding:20px;">데이터 로딩 중...</p>';
    }
    ?>
    </div>
</div>