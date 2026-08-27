<?php
if (!defined('_GNUBOARD_')) exit;

// ===== 설정: 블로그 아이디만 입력하세요 =====
$blog_id = 'skyjun3189';  // 네이버 블로그 아이디
$max_items = 50;         // 가져올 총 게시글 수 (네이버 RSS 최대치에 따라 50개로 제한될 수 있음)
$items_per_page = 20;     // 페이지당 표시할 게시글 수
// ==========================================

$rbw_id  = 'rb-blog-'.substr(md5(uniqid('', true)), 0, 6);
$rbw_url = G5_URL.'/rb/rb.widget/rb.blog';
$rss_url = "https://rss.blog.naver.com/{$blog_id}.xml";
$blog_url = "https://blog.naver.com/{$blog_id}";

// RSS 파싱
$items = array();
$error_msg = '';

try {
    // libxml 에러 처리 활성화
    libxml_use_internal_errors(true);
    
    // RSS 읽기
    $rss_content = false;
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rss_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $rss_content = curl_exec($ch);
        curl_close($ch);
        
        if ($rss_content === false) $rss_content = false;
    }
    
    if ($rss_content === false && ini_get('allow_url_fopen')) {
        $rss_content = @file_get_contents($rss_url);
    }
    
    if ($rss_content === false) {
        throw new Exception('RSS를 불러올 수 없습니다.');
    }
    
    $xml = simplexml_load_string($rss_content);
    
    if ($xml === false) {
        throw new Exception('RSS 파싱에 실패했습니다.');
    }
    
    // 블로그 제목
    $blog_title = (string)$xml->channel->title;
    $blog_title = strip_tags($blog_title);
    if (empty($blog_title)) $blog_title = '블로그';
    
    // RSS 아이템 파싱
    $count = 0;
    foreach ($xml->channel->item as $item) {
        if ($count >= $max_items) break;
        
        $title = (string)$item->title;
        $link = (string)$item->link;
        $pubDate = (string)$item->pubDate;
        $raw_description = (string)$item->description;
        
        // 썸네일 추출
        $thumb_url = '';
        if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $raw_description, $matches)) {
            $thumb_url = $matches[1];
        }

        $title = strip_tags($title);
        $description = strip_tags($raw_description);
        
        $date_formatted = '';
        if ($pubDate) {
            $timestamp = strtotime($pubDate);
            if ($timestamp) {
                $date_formatted = date('Y.m.d', $timestamp);
            }
        }
        
        $items[] = array(
            'title' => $title,
            'link' => $link,
            'date' => $date_formatted,
            'thumb' => $thumb_url,
            'description' => $description
        );
        
        $count++;
    }
    
    libxml_clear_errors();
    
} catch (Exception $e) {
    $error_msg = $e->getMessage();
    $blog_title = '블로그';
}

// ===== 페이징 로직 처리 =====
$total_count = count($items);
$total_page  = ($total_count > 0) ? ceil($total_count / $items_per_page) : 1;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
if ($current_page > $total_page) $current_page = $total_page;

$offset = ($current_page - 1) * $items_per_page;
$paged_items = array_slice($items, $offset, $items_per_page);

// 페이지 URL 생성 헬퍼 함수
function get_page_url($page_num) {
    $params = $_GET;
    $params['page'] = $page_num;
    return '?' . http_build_query($params);
}
?>

<!-- 기존 스타일 시트 (필요시 유지/삭제) -->
<link rel="stylesheet" href="<?php echo $rbw_url; ?>/style.css">

<!-- 갤러리형 스타일 + 페이징 -->
<style>
#<?php echo $rbw_id; ?> {
    max-width: 1400px;
    margin: 0 auto;
    font-family: 'Noto Sans KR', sans-serif;
}

/* 헤더 스타일 */
#<?php echo $rbw_id; ?> .rbb-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 0 10px;
}
#<?php echo $rbw_id; ?> .rbb-title {
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
}
#<?php echo $rbw_id; ?> .rbb-ico-btn {
    color: #666;
    text-decoration: none;
    font-size: 0.9rem;
}

/* 그리드 레이아웃 설정 */
#<?php echo $rbw_id; ?> .rbb-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* PC: 4열 */
    gap: 20px;
    padding: 0;
    margin: 0;
    list-style: none;
}

/* 카드 아이템 스타일 */
#<?php echo $rbw_id; ?> .rbb-item {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s;
    /* border: 1px solid #eee; */ /* 테두리 필요시 주석 해제 */
}
#<?php echo $rbw_id; ?> .rbb-item:hover {
    transform: translateY(-5px);
}

#<?php echo $rbw_id; ?> .rbb-link {
    display: block;
    text-decoration: none;
    color: inherit;
    height: 100%;
}

/* 썸네일 영역 */
#<?php echo $rbw_id; ?> .rbb-thumb-box {
    width: 100%;
    height: 200px;
    overflow: hidden;
    background-color: #f8f8f8;
    position: relative;
    border-radius: 8px;
}
#<?php echo $rbw_id; ?> .rbb-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s;
}
#<?php echo $rbw_id; ?> .rbb-link:hover .rbb-thumb-img {
    transform: scale(1.05);
}
#<?php echo $rbw_id; ?> .rbb-no-img {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eee;
    color: #aaa;
    font-size: 0.9rem;
}

/* 텍스트 영역 */
#<?php echo $rbw_id; ?> .rbb-text-box {
    padding: 15px 5px;
}
#<?php echo $rbw_id; ?> .rbb-item-date {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
}
#<?php echo $rbw_id; ?> .rbb-item-title {
    font-size: 1.05rem;
    font-weight: bold;
    color: #111;
    margin-bottom: 8px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    height: 2.8em;
}
#<?php echo $rbw_id; ?> .rbb-item-desc {
    font-size: 0.9rem;
    color: #888;
    line-height: 1.5;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    height: 3em; 
}
#<?php echo $rbw_id; ?> .rbb-item-meta {
    font-size: 0.8rem;
    color: #999;
    display: flex;
    gap: 10px;
}
#<?php echo $rbw_id; ?> .rbb-author {
    font-weight: 500;
}

/* 페이징 스타일 */
#<?php echo $rbw_id; ?> .rbb-pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 30px;
    padding-bottom: 20px;
}
#<?php echo $rbw_id; ?> .rbb-page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 5px;
    font-size: 14px;
    color: #555;
    text-decoration: none;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    transition: all 0.2s;
}
#<?php echo $rbw_id; ?> .rbb-page-link:hover {
    background-color: #f1f1f1;
    color: #333;
}
#<?php echo $rbw_id; ?> .rbb-page-link.active {
    background-color: #333;
    color: #fff;
    border-color: #333;
    font-weight: bold;
}

/* 반응형 */
@media (max-width: 1024px) {
    #<?php echo $rbw_id; ?> .rbb-list { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    #<?php echo $rbw_id; ?> .rbb-list { grid-template-columns: repeat(2, 1fr); gap: 15px; }
    #<?php echo $rbw_id; ?> .rbb-thumb-box { height: 160px; }
}
@media (max-width: 480px) {
    #<?php echo $rbw_id; ?> .rbb-list { grid-template-columns: repeat(1, 1fr); }
}
</style>

<div id="<?php echo $rbw_id; ?>" class="rb-blog">
  <div class="rbb-card">
    <!-- 헤더 영역 -->
    <div class="rbb-header">
      <div class="rbb-title-wrap">
        <div class="rbb-title"><?php echo htmlspecialchars($blog_title); ?></div>
      </div>
      <a href="<?php echo $blog_url; ?>" target="_blank" class="rbb-ico-btn" aria-label="블로그 방문" title="블로그 방문">
        더보기 >
      </a>
    </div>

    <div class="rbb-body">
      <?php if ($error_msg): ?>
        <div class="rbb-error" style="padding: 20px; text-align: center; color: #666;">
          <?php echo htmlspecialchars($error_msg); ?>
        </div>
      <?php elseif (empty($paged_items)): ?>
        <div class="rbb-empty" style="padding: 20px; text-align: center; color: #666;">
          게시글이 없습니다.
        </div>
      <?php else: ?>
        <ul class="rbb-list">
          <?php foreach ($paged_items as $post): ?>
          <li class="rbb-item">
            <a href="<?php echo htmlspecialchars($post['link']); ?>" target="_blank" class="rbb-link">
              
              <div class="rbb-thumb-box">
                <?php if (!empty($post['thumb'])): ?>
                    <img src="<?php echo htmlspecialchars($post['thumb']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="rbb-thumb-img" referrerpolicy="no-referrer">
                <?php else: ?>
                    <div class="rbb-no-img">No Image</div>
                <?php endif; ?>
              </div>

              <div class="rbb-text-box">
                  <?php if ($post['date']): ?>
                  <div class="rbb-item-date"><?php echo $post['date']; ?></div>
                  <?php endif; ?>

                  <div class="rbb-item-title"><?php echo htmlspecialchars($post['title']); ?></div>
                  
                  <?php if ($post['description']): ?>
                  <div class="rbb-item-desc"><?php echo htmlspecialchars($post['description']); ?>...</div>
                  <?php endif; ?>
                  
                  <div class="rbb-item-meta">
                      <span class="rbb-author"><?php echo htmlspecialchars($blog_title); ?></span>
                  </div>
              </div>

            </a>
          </li>
          <?php endforeach; ?>
        </ul>

        <!-- 페이징 영역 추가 -->
        <?php if ($total_page > 1): ?>
        <div class="rbb-pagination">
            <?php 
            // 이전 페이지
            if ($current_page > 1) {
                echo '<a href="'.get_page_url($current_page - 1).'" class="rbb-page-link">&lt;</a>';
            }
            
            // 페이지 번호
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_page, $start_page + 4);
            // 시작 페이지가 뒤로 밀려서 끝 페이지가 부족할 때 보정
            if ($end_page - $start_page < 4) {
                $start_page = max(1, $end_page - 4);
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                $active_class = ($i == $current_page) ? 'active' : '';
                echo '<a href="'.get_page_url($i).'" class="rbb-page-link '.$active_class.'">'.$i.'</a>';
            }
            
            // 다음 페이지
            if ($current_page < $total_page) {
                echo '<a href="'.get_page_url($current_page + 1).'" class="rbb-page-link">&gt;</a>';
            }
            ?>
        </div>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</div>