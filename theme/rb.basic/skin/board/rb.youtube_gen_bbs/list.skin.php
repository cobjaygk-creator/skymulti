<?php
if (!defined('_GNUBOARD_')) exit;

/*
 * 그누보드 list.skin.php용 YouTube 직접 출력 스킨
 * - DB 저장 없음
 * - 게시글 생성 없음
 * - 페이지를 열 때 YouTube API에서 직접 조회
 * - 다음 페이지 토큰으로 과거 영상 조회
 *
 * 반드시 아래 API 키를 새로 발급한 값으로 교체하세요.
 */
$youtube_channel_id = 'UCeVuOfZ9F5Rz10gFgutJYjQ';
$youtube_api_key = 'AIzaSyDwYP3-ZtgvdIB0ayEiW9rih2YwbN-OwZQ';
$youtube_page_size = 12;
$youtube_page_token = isset($_GET['yt_page_token']) ? trim((string)$_GET['yt_page_token']) : '';

function youtube_api_request($endpoint, $params, $api_key) {
    $params['key'] = $api_key;
    $url = 'https://www.googleapis.com/youtube/v3/' . $endpoint . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER => array('Accept: application/json'),
    ));
    $body = curl_exec($ch);
    $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($body === false || $curl_error !== '') {
        throw new Exception('YouTube API 연결에 실패했습니다.');
    }
    $data = json_decode($body, true);
    if ($http_code !== 200 || !is_array($data)) {
        $message = isset($data['error']['message']) ? $data['error']['message'] : 'HTTP ' . $http_code;
        throw new Exception('YouTube API 오류: ' . $message);
    }
    return $data;
}

$youtube_items = array();
$youtube_next_token = '';
$youtube_prev_token = '';
$youtube_error = '';

function youtube_duration_seconds($duration) {
    if (!preg_match('/^PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?$/', (string)$duration, $m)) return 0;
    return ((int)($m[1] ?? 0) * 3600) + ((int)($m[2] ?? 0) * 60) + (int)($m[3] ?? 0);
}

function youtube_relative_date($date) {
    $timestamp = strtotime($date);
    if (!$timestamp) return '';
    $diff = max(0, time() - $timestamp);
    if ($diff < 3600) return '오늘';
    if ($diff < 86400) return floor($diff / 3600) . '시간전';
    if ($diff < 30 * 86400) return floor($diff / 86400) . '일전';
    return date('Y.m.d', $timestamp);
}
function youtube_view_count($views) {
    $views = (int)$views;
    if ($views >= 100000000) return rtrim(rtrim(number_format($views / 100000000, 1, '.', ''), '0'), '.') . '억회';
    if ($views >= 10000) return rtrim(rtrim(number_format($views / 10000, 1, '.', ''), '0'), '.') . '만회';
    if ($views >= 1000) return rtrim(rtrim(number_format($views / 1000, 1, '.', ''), '0'), '.') . '천회';
    return number_format($views) . '회';
}

try {
    if ($youtube_api_key === '' || strpos($youtube_api_key, '여기에_') === 0) {
        throw new Exception('YouTube API 키를 설정해주세요.');
    }
    if (!function_exists('curl_init')) {
        throw new Exception('서버에서 PHP cURL 확장을 사용할 수 없습니다.');
    }

    $channel = youtube_api_request('channels', array(
        'part' => 'contentDetails',
        'id' => $youtube_channel_id,
        'maxResults' => 1,
    ), $youtube_api_key);
    $uploads_id = isset($channel['items'][0]['contentDetails']['relatedPlaylists']['uploads'])
        ? $channel['items'][0]['contentDetails']['relatedPlaylists']['uploads'] : '';
    if ($uploads_id === '') throw new Exception('채널 업로드 목록을 찾을 수 없습니다.');

    $params = array(
        'part' => 'snippet,contentDetails',
        'playlistId' => $uploads_id,
        // 일반 영상을 건너뛰고 최신 Shorts를 찾기 위해 한 페이지를 넉넉히 조회합니다.
        'maxResults' => 50,
    );
    if ($youtube_page_token !== '') $params['pageToken'] = $youtube_page_token;
    $playlist = youtube_api_request('playlistItems', $params, $youtube_api_key);
    $youtube_next_token = isset($playlist['nextPageToken']) ? $playlist['nextPageToken'] : '';
    $youtube_prev_token = isset($playlist['prevPageToken']) ? $playlist['prevPageToken'] : '';

    $stat_map = array();
    $ids_for_stats = array();
    foreach ((array)$playlist['items'] as $entry) {
        $candidate_id = isset($entry['contentDetails']['videoId']) ? $entry['contentDetails']['videoId'] : '';
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate_id)) $ids_for_stats[] = $candidate_id;
    }
    if ($ids_for_stats) {
        $stats = youtube_api_request('videos', array('part' => 'statistics,contentDetails', 'id' => implode(',', $ids_for_stats)), $youtube_api_key);
        foreach ((array)$stats['items'] as $stat) $stat_map[$stat['id']] = array(
            'views' => isset($stat['statistics']['viewCount']) ? (int)$stat['statistics']['viewCount'] : 0,
            'duration' => youtube_duration_seconds($stat['contentDetails']['duration'] ?? ''),
        );
    }
    foreach ((array)$playlist['items'] as $entry) {
        $snippet = isset($entry['snippet']) ? $entry['snippet'] : array();
        $video_id = isset($entry['contentDetails']['videoId']) ? $entry['contentDetails']['videoId'] : '';
        if (!preg_match('/^[A-Za-z0-9_-]{11}$/', $video_id)) continue;
        $title = isset($snippet['title']) ? $snippet['title'] : '';
        if ($title === 'Deleted video' || $title === 'Private video') continue;
        $published = isset($snippet['publishedAt']) ? $snippet['publishedAt'] : '';
        // YouTube에서 제공하는 가장 큰 표준 썸네일을 우선 사용합니다.
        $thumb = 'https://i.ytimg.com/vi/' . rawurlencode($video_id) . '/maxresdefault.jpg';
        $youtube_items[] = array(
            'id' => $video_id,
            'title' => $title,
            'published' => $published,
            'thumb' => $thumb,
            'url' => 'https://www.youtube.com/watch?v=' . $video_id,
            'views' => isset($stat_map[$video_id]['views']) ? $stat_map[$video_id]['views'] : 0,
            'duration' => isset($stat_map[$video_id]['duration']) ? $stat_map[$video_id]['duration'] : 0,
        );
    }
    // Shorts 탭과 맞추기 위해 3분 이하 세로 숏폼을 우선 표시합니다.
    $youtube_items = array_values(array_filter($youtube_items, function($item) {
        return $item['duration'] > 0 && $item['duration'] <= 180;
    }));
    $unique_items = array();
    $seen_video_ids = array();
    foreach ($youtube_items as $item) {
        if (isset($seen_video_ids[$item['id']])) continue;
        $seen_video_ids[$item['id']] = true;
        $unique_items[] = $item;
    }
    $youtube_items = $unique_items;
    $youtube_items = array_slice($youtube_items, 0, $youtube_page_size);
} catch (Throwable $e) {
    $youtube_error = $e->getMessage();
}

$base_url = G5_BBS_URL . '/board.php?bo_table=' . urlencode($bo_table);
$page_link = function($token) use ($base_url) {
    return $base_url . '&yt_page_token=' . urlencode($token);
};
?>

<style>
/* 기존 그누보드 게시글 목록이 파일에 남아 있어도 YouTube 목록과 중복되지 않도록 숨김 */
.rb_bbs_wrap > .swiper-container,
.rb_bbs_wrap .gallery_top_mt,
.rb_bbs_wrap .rb_bbs_top,
.rb_bbs_wrap .btm_btns,
.rb_bbs_wrap > form > .swiper-container,
body .gallery_top_mt,
body .gallery_v_mtop,
body .swiper-container:not(.yt-direct-wrap .swiper-container){display:none!important}
.yt-direct-wrap{max-width:1200px;margin:0 auto;padding:20px 0 60px}
.yt-direct-title{margin:0 0 24px;font-size:28px;font-weight:700;color:#111827}
.yt-direct-error{padding:18px;border-radius:8px;background:#fff1f2;color:#be123c}
.yt-direct-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:22px 10px}
.yt-direct-card{min-width:0}
.yt-direct-thumb{display:block;position:relative;width:100%;height:auto!important;aspect-ratio:9/16!important;overflow:hidden;border-radius:8px;background:#e5e7eb}
.yt-direct-thumb img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .2s ease}
.yt-direct-thumb:hover img{transform:scale(1.03)}
.yt-direct-card h4{margin:9px 2px 5px;font-size:14px;line-height:1.35;color:#111827;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.yt-direct-date{margin:0 2px;color:#6b7280;font-size:13px}
.yt-direct-views{margin:2px 2px 0;color:#6b7280;font-size:12px}
.yt-direct-paging{display:flex;justify-content:center;gap:10px;margin-top:34px}
.yt-direct-more{display:inline-block;padding:11px 28px;border:1px solid #d1d5db;border-radius:6px;color:#374151;text-decoration:none;background:#fff;cursor:pointer;font:inherit}
.yt-direct-more:hover{border-color:#2563eb;color:#2563eb}
.yt-direct-more:disabled{opacity:.55;cursor:wait}
.yt-modal-overlay{display:none;position:fixed;z-index:99999;inset:0;background:rgba(0,0,0,.82);align-items:center;justify-content:center;padding:20px}
.yt-modal-content{position:relative;width:min(900px,94vw);background:#000;border-radius:8px;overflow:hidden}
.yt-modal-content.shorts-view{width:min(440px,51.5vh,92vw)}
.yt-modal-content.shorts-view{width:min(440px,51.5vh,92vw);height:min(88vh,782px)}
.yt-modal-close{position:absolute;z-index:2;right:8px;top:4px;border:0;background:rgba(0,0,0,.5);color:#fff;font-size:28px;line-height:34px;cursor:pointer}
.yt-video-frame{transition:opacity .22s ease,transform .22s ease}
.yt-video-frame.switching{opacity:0;transform:translateY(14px)}
.yt-video-wheel-layer{display:none}
.yt-video-wrapper{position:relative;height:0;padding-bottom:56.25%}
.yt-video-wrapper.shorts-view{padding-bottom:177.77%}
.yt-video-wrapper.shorts-view{height:100%;padding-bottom:0}
.yt-video-wrapper iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
@media (max-width:1100px){.yt-direct-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:20px 8px}}
@media (max-width:800px){.yt-direct-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:18px 8px}}
@media (max-width:520px){.yt-direct-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 8px}.yt-direct-wrap{padding:12px 0 40px}}
</style>

<div class="yt-direct-wrap">
    <h3 class="yt-direct-title"><?php echo get_head_title($g5['title']); ?></h3>

    <?php if ($youtube_error) { ?>
        <div class="yt-direct-error"><?php echo htmlspecialchars($youtube_error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php } elseif (!$youtube_items) { ?>
        <div class="no_data">표시할 영상이 없습니다.</div>
    <?php } else { ?>
        <div class="yt-direct-grid" id="yt-direct-grid" data-next-token="<?php echo htmlspecialchars($youtube_next_token, ENT_QUOTES, 'UTF-8'); ?>">
            <?php foreach ($youtube_items as $item) { ?>
                <article class="yt-direct-card">
                    <a class="yt-direct-thumb" data-video-id="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" href="javascript:void(0);" onclick="openYoutubeModal('<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>', true); return false;">
                        <img src="<?php echo htmlspecialchars($item['thumb'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.onerror=null;this.src='https://i.ytimg.com/vi/<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>/hqdefault.jpg';" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                    </a>
                    <h4><a data-video-id="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" href="javascript:void(0);" onclick="openYoutubeModal('<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>', true); return false;"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></a></h4>
                    <p class="yt-direct-date"><?php echo htmlspecialchars(youtube_relative_date($item['published']), ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="yt-direct-views">조회수 <?php echo htmlspecialchars(youtube_view_count($item['views']), ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            <?php } ?>
        </div>
        <nav class="yt-direct-paging" aria-label="유튜브 영상 더보기">
            <?php if ($youtube_next_token) { ?><button type="button" class="yt-direct-more" id="yt-direct-more">더보기</button><?php } ?>
        </nav>
    <?php } ?>
</div>

<div id="youtubeModal" class="yt-modal-overlay" role="dialog" aria-modal="true" aria-label="유튜브 영상">
    <div class="yt-modal-content">
        <button type="button" class="yt-modal-close" onclick="closeYoutubeModal()" aria-label="닫기">×</button>
        <div class="yt-video-wrapper"><iframe id="modalYoutubeFrame" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
        <div class="yt-video-wrapper"><iframe id="modalYoutubeFrame" class="yt-video-frame" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><div class="yt-video-wheel-layer" aria-hidden="true"></div></div>
    </div>
</div>

<script>
(function () {
    var wraps = document.querySelectorAll('.yt-direct-wrap');
    for (var wi = 1; wi < wraps.length; wi++) wraps[wi].remove();
    var grid = document.getElementById('yt-direct-grid');
    var button = document.getElementById('yt-direct-more');
    if (!grid) return;
    var initialSeen = {};
    Array.prototype.slice.call(grid.children).forEach(function (card) {
        var link = card.querySelector('a[data-video-id]');
        var id = link ? link.getAttribute('data-video-id') : '';
        if (id && initialSeen[id]) card.remove();
        else if (id) initialSeen[id] = true;
    });
    if (!button) return;
    button.addEventListener('click', function () {
        var token = grid.getAttribute('data-next-token');
        if (!token) return;
        button.disabled = true;
        button.textContent = '불러오는 중…';
        var url = new URL(window.location.href);
        url.searchParams.set('yt_page_token', token);
        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { if (!response.ok) throw new Error('HTTP ' + response.status); return response.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nextGrid = doc.querySelector('#yt-direct-grid');
                if (!nextGrid) throw new Error('영상 목록을 찾을 수 없습니다.');
                var existing = {};
                Array.prototype.forEach.call(grid.querySelectorAll('a[data-video-id]'), function (el) { existing[el.getAttribute('data-video-id')] = true; });
                Array.prototype.slice.call(nextGrid.children).forEach(function (card) {
                    var link = card.querySelector('a[data-video-id]');
                    var id = link ? link.getAttribute('data-video-id') : '';
                    if (!id || existing[id]) return;
                    existing[id] = true;
                    grid.appendChild(card);
                });
                var next = nextGrid.getAttribute('data-next-token') || '';
                grid.setAttribute('data-next-token', next);
                if (!next) button.remove(); else { button.disabled = false; button.textContent = '더보기'; }
            })
            .catch(function () { button.disabled = false; button.textContent = '다시 시도'; });
    });
})();

function openYoutubeModal(videoId, isShorts) {
    if (!videoId) return;
    var overlay = document.getElementById('youtubeModal');
    var content = overlay.querySelector('.yt-modal-content');
    var wrapper = overlay.querySelector('.yt-video-wrapper');
    var frame = document.getElementById('modalYoutubeFrame');
    content.classList.toggle('shorts-view', isShorts);
    wrapper.classList.toggle('shorts-view', isShorts);
    frame.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1';
    frame.classList.add('switching');
    overlay.style.display = 'flex';
    setTimeout(function () {
        frame.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1';
        frame.onload = function () { frame.classList.remove('switching'); };
        setTimeout(function () { frame.classList.remove('switching'); }, 260);
    }, 120);
    overlay.setAttribute('data-current-video', videoId);
}
function closeYoutubeModal() {
    var overlay = document.getElementById('youtubeModal');
    var frame = document.getElementById('modalYoutubeFrame');
    frame.src = '';
    overlay.style.display = 'none';
}
function moveYoutubeModal(direction) {
    var overlay = document.getElementById('youtubeModal');
    var ids = Array.prototype.map.call(document.querySelectorAll('#yt-direct-grid a[data-video-id]'), function (el) { return el.getAttribute('data-video-id'); });
    var index = ids.indexOf(overlay.getAttribute('data-current-video'));
    var nextIndex = index + direction;
    if (nextIndex < 0 || nextIndex >= ids.length) return;
    openYoutubeModal(ids[nextIndex], true);
}
document.addEventListener('click', function (e) {
    var overlay = document.getElementById('youtubeModal');
    if (e.target === overlay) closeYoutubeModal();
});
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeYoutubeModal(); });
var youtubeModalWheelLock = false;
document.getElementById('youtubeModal').addEventListener('wheel', function (e) {
function handleYoutubeWheel(e) {
    if (youtubeModalWheelLock) return;
    if (Math.abs(e.deltaY) < 10) return;
    e.preventDefault();
    var modal = document.getElementById('youtubeModal');
    var ids = Array.prototype.map.call(document.querySelectorAll('#yt-direct-grid a[data-video-id]'), function (el) { return el.getAttribute('data-video-id'); });
    var current = this.getAttribute('data-current-video');
    var current = modal.getAttribute('data-current-video');
    var index = ids.indexOf(current);
    var nextIndex = e.deltaY > 0 ? index + 1 : index - 1;
    if (nextIndex < 0 || nextIndex >= ids.length) return;
    youtubeModalWheelLock = true;
    openYoutubeModal(ids[nextIndex], true);
    setTimeout(function () { youtubeModalWheelLock = false; }, 450);
}
document.getElementById('youtubeModal').addEventListener('wheel', handleYoutubeWheel, { passive: false });
document.querySelector('.yt-video-wheel-layer').addEventListener('wheel', handleYoutubeWheel, { passive: false });
var youtubeTouchStartY = 0;
document.getElementById('youtubeModal').addEventListener('touchstart', function (e) { youtubeTouchStartY = e.touches[0].clientY; }, { passive: true });
document.getElementById('youtubeModal').addEventListener('touchend', function (e) {
    var delta = youtubeTouchStartY - e.changedTouches[0].clientY;
    if (Math.abs(delta) > 45) moveYoutubeModal(delta > 0 ? 1 : -1);
}, { passive: false });
}, { passive: true });
</script>