<style>
    /* =================================================================
       [1] PC 버전 스타일 설정
       ================================================================= */
    
    /* 메인 타이틀 (PC) */
    .typing-title {
        font-family: 'Noto Sans KR';
        font-weight: 700;        
        line-height: 1.1;        
        
        font-size: 200px;         
        
        color: rgba(255, 255, 255, 0.37); 
        text-align: center; 
        display: block;
        margin-bottom: 15px;
        min-height: 80px;
        
        opacity: 1;
        transition: opacity 1.5s ease; 
    }

    /* 서브 설명글 (PC) */
    .typing-desc {
        font-family: 'Noto Sans KR', sans-serif;
        font-weight: 300;        
        
        /* 요청하신 35px 유지 */
        font-size: 35px;         
        
        color: rgba(255, 255, 255, 0.37); 
        text-align: center;
        display: block;
        min-height: 36px;
        letter-spacing: 0.5px;
        
        opacity: 1;
        transition: opacity 1.5s ease;
    }

    /* 페이드 아웃 클래스 */
    .text-fade-out {
        opacity: 0 !important;
    }

    /* [기본 레이아웃] */
    .yt_bg_wrapper_<?php echo $row_mod['md_id'] ?> {
        position: relative; overflow: hidden; width: 100%; height: 880px; background-color: #000;
    }
    .video-background {
        position: absolute; top: 0; right: 0; bottom: 0; left: 0; z-index: 0; overflow: hidden;
    }
    .video-foreground {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 100vw; height: 56.25vw; min-height: 100%; min-width: 177.77vh; pointer-events: none;
    }
    .video-foreground iframe { width: 100%; height: 100%; }
    .video-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); 
        z-index: 1;
    }
    .video-content-wrap {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 100;
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        text-align: center; color: #fff; pointer-events: none;
    }
    .typewriter-container {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 200px; pointer-events: auto; width: 100%;
        max-width: <?php echo $rb_core['main_width'] ?>px; padding: 0 20px; box-sizing: border-box;
    }

    /* 커서 효과 */
.typewriter-container .cursor {
    display: inline-block;
    width: 4px;
    vertical-align: sub;
    margin-left: 8px;
    background-color: rgba(255, 255, 255,0.5);
    animation: blink 1s step-end infinite;
    box-shadow: 0 0 10px rgba(255,255,255,0.6);
}
    .typing-title .cursor { height: 75px; }
    .typing-desc .cursor { height: 26px; background-color: rgba(255, 255, 255, 0.5); }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }


    /* =================================================================
       [2] 모바일 버전 스타일 설정 (여기 수정됨)
       ================================================================= */
    @media (max-width: 768px) {
        .yt_bg_wrapper_<?php echo $row_mod['md_id'] ?> { height: 450px; background-color: #000; }
        .video-background { display: block !important; }
        .video-foreground { width: 300%; height: 100%; min-width: 0; min-height: 0; left: 50%; top: 50%; transform: translate(-50%, -50%); }

        /* ★ 핵심 수정: 모바일에서 텍스트 컨테이너 하단 정렬 ★ */
        .video-content-wrap {
            top:150px !important;
        }

        .typing-title { 
            font-size: 65px; 
            margin-bottom: 0px; 
            min-height: 40px; 
            line-height: 1.2;
        }

        .typing-desc { 
            font-size: 15px; 
            min-height: 35px; 
            word-break: keep-all; 
            line-height: 1.0;
        }
        
        .typing-title .cursor { height: 45px; margin-left: 4px; }
        .typing-desc .cursor { height: 18px; margin-left: 2px; }
    }
</style>

<?php
// ▼▼▼▼▼ 설정 영역 (연속 재생 방지 기능 추가) ▼▼▼▼▼

// 1. 영상 목록 정의
$video_list = array(
    "jF3cfp6UKz4", 
    "Wc2UN3yL2CU", 
    "YLeJ9QRaM7I"  
);

// 2. 이전에 재생된 영상 ID 가져오기 (쿠키 확인)
$last_played_id = isset($_COOKIE['rb_last_video_id']) ? $_COOKIE['rb_last_video_id'] : '';

// 3. 후보군 생성: 전체 목록에서 직전 영상을 뺍니다.
$candidates = array_diff($video_list, array($last_played_id));

// (만약 영상이 1개뿐이거나 오류로 후보가 비었으면 전체 목록 사용)
if (empty($candidates)) {
    $candidates = $video_list;
}

// 4. 후보군 중에서 랜덤 선택
$youtube_id = $candidates[array_rand($candidates)];

$unique_player_id = "player_" . $row_mod['md_id'];
?>

<div class="yt_bg_wrapper_<?php echo $row_mod['md_id'] ?>">
    
    <div class="video-background">
        <div class="video-foreground">
            <div id="<?php echo $unique_player_id; ?>"></div>
        </div>
    </div>

    <div class="video-overlay"></div>

    <div class="video-content-wrap">
        <div class="typewriter-container">
            <h2 id="typing_title_<?php echo $row_mod['md_id'] ?>" class="typing-title"></h2>
            <p id="typing_desc_<?php echo $row_mod['md_id'] ?>" class="typing-desc"></p>
        </div>
    </div>

</div>

<script src="https://www.youtube.com/iframe_api"></script>

<script>
    // 현재 재생 중인 영상 ID를 쿠키에 저장 (다음번 새로고침 시 피하기 위해)
    document.cookie = "rb_last_video_id=<?php echo $youtube_id; ?>; path=/; max-age=3600"; // 1시간 동안 기억

    // 2. 유튜브 플레이어
    var player_<?php echo $row_mod['md_id']; ?>;
    function onYouTubeIframeAPIReady() {
        player_<?php echo $row_mod['md_id']; ?> = new YT.Player('<?php echo $unique_player_id; ?>', {
            videoId: '<?php echo $youtube_id; ?>',
            playerVars: { 
                'autoplay': 1, 'controls': 0, 'showinfo': 0, 'rel': 0, 
                'loop': 1, 'playlist': '<?php echo $youtube_id; ?>', 
                'playsinline': 1, 'mute': 1 
            },
            events: {
                'onReady': onPlayerReady_<?php echo $row_mod['md_id']; ?>,
                'onStateChange': onPlayerStateChange_<?php echo $row_mod['md_id']; ?>
            }
        });
    }
    function onPlayerReady_<?php echo $row_mod['md_id']; ?>(event) { event.target.mute(); event.target.playVideo(); }
    function onPlayerStateChange_<?php echo $row_mod['md_id']; ?>(event) { if (event.data === YT.PlayerState.ENDED) { event.target.playVideo(); } }


    // 3. 타이핑 효과
    $(document).ready(function() {
        
        const textGroups = [
            {
                title: "Perfection.",
                desc: "Detailing beyond limits. The ultimate care."
            },
            {
                title: "Passion.",
                desc: "We tune with heart, not just tools."
            },
            {
                title: "Trust.",
                desc: "Premium service you can rely on."
            }
        ];

        const titleEl = document.getElementById('typing_title_<?php echo $row_mod['md_id'] ?>');
        const descEl = document.getElementById('typing_desc_<?php echo $row_mod['md_id'] ?>');
        const cursorHTML = '<span class="cursor"></span>';

        if (!titleEl || !descEl) return;

        const wait = (ms) => new Promise(resolve => setTimeout(resolve, ms));

        async function typeWriterLoop() {
            let i = 0;
            while (true) { 
                const currentGroup = textGroups[i % textGroups.length];
                
                $(titleEl).removeClass('text-fade-out');
                $(descEl).removeClass('text-fade-out');

                titleEl.innerHTML = cursorHTML;
                await typeText(titleEl, currentGroup.title);
                titleEl.innerHTML = currentGroup.title; 
                
                descEl.innerHTML = cursorHTML;
                await typeText(descEl, currentGroup.desc);
                
                await wait(3500); 

                $(titleEl).addClass('text-fade-out');
                $(descEl).addClass('text-fade-out');
                
                await wait(1500); 

                titleEl.innerHTML = "";
                descEl.innerHTML = "";
                
                i++;
            }
        }

        async function typeText(element, text) {
            let currentString = "";
            for (let char of text) {
                currentString += char;
                element.innerHTML = currentString + cursorHTML;
                await wait(80); 
            }
        }

        typeWriterLoop();
    });

    
    // 4. 레이아웃 확장
    function adjustYtWidth_<?php echo $row_mod['md_id'] ?>() {
        const content_w = $('.yt_bg_wrapper_<?php echo $row_mod['md_id'] ?>');
        const firstAdminOv_w = content_w.nextUntil('.admin_ov').next('.admin_ov');
        
        if ($(window).width() > <?php echo $rb_core['main_width'] ?>) {
            content_w.css({ 'width': '100vw', 'left': '50%', 'transform': 'translateX(-50%)', 'position': 'relative' });
            firstAdminOv_w.css({ 'width': '100vw', 'left': '50%', 'transform': 'translateX(-50%)' });
        } else {
            content_w.css({ 'width': '100%', 'left': '0', 'transform': 'none', 'position': 'static' });
            firstAdminOv_w.css({ 'width': '100%', 'left': '0', 'transform': 'none' });
        }
    }
    $(document).ready(adjustYtWidth_<?php echo $row_mod['md_id'] ?>);
    $(window).resize(adjustYtWidth_<?php echo $row_mod['md_id'] ?>);
</script>