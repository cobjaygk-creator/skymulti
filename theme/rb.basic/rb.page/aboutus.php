<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&display=swap" rel="stylesheet">

<style>

/* 헤더를 항상 검은색으로 고정 (스크롤 여부 상관없이) */
    #header {
        background-color: #111 !important; 
        border-bottom: 1px solid #333;
    }

    /* 메뉴 글씨 및 아이콘을 흰색으로 변경 */
    #header .gnb_wrap a, 
    #header .gnb_wrap button, 
    #header .gnb_wrap svg,
    #header .member_info_wrap a {
        color: #fff !important;
        fill: #fff !important;
    }
    
    /* 로고가 검은색 이미지라면 흰색으로 반전 */
    #header .logo_wrap img {
        filter: brightness(0) invert(1); 
    }

    /* 헤더 높이만큼 본문 내용을 아래로 밀어줌 (겹침 방지) */
    .mk-header-section {
        padding-top: 160px !important; /* 헤더 높이에 맞춰 조절 */
    }

    /* 관리자용 여백 제거 */
    #contents_wrap .sub.co_gap_pc_15 { display: none !important; }
    .contents_wrap, #contents_wrap { padding-top: 0 !important; margin-top: 0 !important; }
    /* [스코프 래퍼] 이 영역 안에서만 스타일 적용 */
    .mk-content-wrapper {
        font-family: 'Noto Sans KR', sans-serif;
        color: #333;
        line-height: 1.5;
        background-color: #fff;
        width: 100%;
        overflow-x: hidden; /* 가로 스크롤 방지 */
        position: relative;
    }

    /* Box-sizing 리셋 (이 영역 내부만) */
    .mk-content-wrapper * {
        box-sizing: border-box;
    }

    /* 공통 컨테이너 */
    .mk-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
    }

    /* * [SECTION 1] 헤더 영역 
     */
    .mk-header-section {
        padding-top: 80px;
        margin-bottom: 120px;
    }

    .mk-header-text-area {
        margin-bottom: 60px;
    }

    .mk-category-title {
        color: #e60012; /* 마니커 레드 */
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 24px;
        display: block;
        opacity: 0;
        animation: mkFadeInUp 0.8s ease forwards;
    }

    .mk-main-headline {
        font-size: 56px;
        font-weight: 700;
        line-height: 1.3;
        color: #111;
        letter-spacing: -1.5px;
        word-break: keep-all;
        opacity: 0;
        animation: mkFadeInUp 0.8s ease 0.2s forwards;
        margin: 0; /* h1 기본 마진 제거 */
    }

    /* 풀 가로 이미지 래퍼 (부모 컨테이너 탈출) */
    .mk-full-banner {
        width: 100vw;
        height: 60vh;
        min-height: 500px;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        overflow: hidden;
        
        /* 애니메이션 */
        clip-path: inset(0 45% 0 45%);
        animation: mkExpandFullWidth 1.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        animation-delay: 0.6s;
    }

    .mk-full-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.1);
        animation: mkZoomOutImage 2s ease-out forwards;
        animation-delay: 0.6s;
        display: block;
    }

    /* 키프레임 정의 (이름 충돌 방지 mk 접두어) */
    @keyframes mkFadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes mkExpandFullWidth {
        from { clip-path: inset(0 45% 0 45%); }
        to { clip-path: inset(0 0 0 0); }
    }
    @keyframes mkZoomOutImage {
        to { transform: scale(1); }
    }


    /* * [SECTION 2] 경영철학 (카드 리스트)
     */
    .mk-ph-section {
        padding-bottom: 150px;
    }

    .mk-ph-intro-text {
        margin-bottom: 80px;
    }

    .mk-ph-slogan {
        font-size: 38px;
        font-weight: 700;
        line-height: 1.4;
        color: #222;
        letter-spacing: -1px;
        margin: 0;
    }

    /* 카드 리스트 */
    .mk-ph-list {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .mk-card {
        position: relative;
        width: 100%;
        height: 420px;
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        align-items: center;
        color: #fff;
        
        /* 스크롤 애니메이션 초기 상태 */
        opacity: 0;
        transform: translateY(80px);
        transition: opacity 1s ease, transform 1s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .mk-card-bg {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 1;
        transition: transform 0.8s ease;
    }

    .mk-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
        z-index: 2;
    }

    .mk-card-content {
        position: relative;
        z-index: 3;
        width: 100%;
        padding: 0 80px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mk-card-txt-group {
        max-width: 650px;
    }

    .mk-card-icon {
        display: inline-block;
        margin-bottom: 20px;
        width: 50px; height: 50px;
        border: 2px solid rgba(255,255,255,0.8);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        color: #fff; /* 아이콘 컬러 명시 */
    }

    .mk-card-title {
        font-size: 40px;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        color: #fff; /* 타이틀 화이트 강제 */
        margin-top: 0;
    }

    .mk-card-desc {
        font-size: 18px;
        font-weight: 300;
        line-height: 1.6;
        opacity: 0.9;
        word-break: keep-all;
        color: #fff;
        margin: 0;
    }

    .mk-card-num {
        font-size: 140px;
        font-weight: 300;
        line-height: 1;
        color: transparent;
        -webkit-text-stroke: 1px rgba(255, 255, 255, 0.6);
        font-family: 'Helvetica Neue', Arial, sans-serif;
        opacity: 0.5;
    }

    /* Hover Effect */
    .mk-card:hover .mk-card-bg {
        transform: scale(1.05);
    }
    .mk-card:hover .mk-card-num {
        color: rgba(255,255,255,0.1);
        -webkit-text-stroke: 1px #fff;
        opacity: 1;
        transition: 0.4s;
    }

    /* Active Class */
    .mk-card.mk-active {
        opacity: 1;
        transform: translateY(0);
    }

    /* 모바일 반응형 */
    @media (max-width: 768px) {
        .mk-header-section { padding-top: 40px; margin-bottom: 80px; }
        .mk-main-headline { font-size: 32px; }
        .mk-full-banner { height: 300px; min-height: auto; }
        
        .mk-ph-intro-text { margin-bottom: 40px; }
        .mk-ph-slogan { font-size: 26px; }

        .mk-card { height: auto; padding: 50px 0; }
        .mk-card-content { 
            flex-direction: column; 
            align-items: flex-start; 
            padding: 0 30px;
        }
        .mk-card-txt-group { max-width: 100%; }
        .mk-card-num {
            position: absolute;
            right: 20px; top: 20px;
            font-size: 60px;
        }
        .mk-card-title { font-size: 28px; }
        .mk-card-desc { font-size: 15px; }
    }
</style>

<div class="mk-content-wrapper">

    <header class="mk-header-section">
        <div class="mk-container">
            <div class="mk-header-text-area">
                <span class="mk-category-title">회사소개</span>
                <h1 class="mk-main-headline">
                    40년의 시간, 변함없는 신뢰,<br>
                    당신의 선택은 언제나 마니커
                </h1>
            </div>
        </div>

        <div class="mk-full-banner">
            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2070&auto=format&fit=crop" alt="마니커 가족 이미지">
        </div>
    </header>

    <section class="mk-ph-section">
        <div class="mk-container">
            <div class="mk-ph-intro-text">
                <h2 class="mk-ph-slogan">
                    고객의 건강한 삶을 지키고,<br>
                    이해관계자 모두의 더 나은 삶을 지원합니다.
                </h2>
            </div>

            <div class="mk-ph-list">
                
                <article class="mk-card mk-scroll-spy">
                    <div class="mk-card-bg" style="background-image: url('https://images.unsplash.com/photo-1501854140884-074cf27f738d?q=80&w=2070&auto=format&fit=crop');"></div>
                    <div class="mk-card-content">
                        <div class="mk-card-txt-group">
                            <span class="mk-card-icon">🌿</span>
                            <h3 class="mk-card-title">자연친화경영</h3>
                            <p class="mk-card-desc">
                                우리는 자연의 가치를 알고, 인류와 자연, 동물이 조화롭게 공존할 수 있는 지속 가능한 생태계 구축에 앞장섭니다.<br>
                                무항생제 닭고기 생산, 자연 친화적인 사육 환경, 환경 오염을 최소화하는 노력을 실천합니다.
                            </p>
                        </div>
                        <span class="mk-card-num">01</span>
                    </div>
                </article>

                <article class="mk-card mk-scroll-spy">
                    <div class="mk-card-bg" style="background-image: url('https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=2000&auto=format&fit=crop');"></div>
                    <div class="mk-card-content">
                        <div class="mk-card-txt-group">
                            <span class="mk-card-icon">🤝</span>
                            <h3 class="mk-card-title">고객친화경영</h3>
                            <p class="mk-card-desc">
                                제품 및 서비스를 통해 고객의 삶의 질 개선과 더 나은 미래 구축을 위해 지속적인 노력을 전개합니다.<br>
                                고객의 건강과 행복을 위해 노력하며, 고객과 함께 성장해 나갈 수 있는 기업 문화를 구축하기 위해 노력합니다.
                            </p>
                        </div>
                        <span class="mk-card-num">02</span>
                    </div>
                </article>

                <article class="mk-card mk-scroll-spy">
                    <div class="mk-card-bg" style="background-image: url('https://images.unsplash.com/photo-1473649085228-583485e6e4d7?q=80&w=2000&auto=format&fit=crop');"></div>
                    <div class="mk-card-content">
                        <div class="mk-card-txt-group">
                            <span class="mk-card-icon">♻️</span>
                            <h3 class="mk-card-title">지속가능경영</h3>
                            <p class="mk-card-desc">
                                비용과 자원의 최적화를 통한 지속가능한 성장을 추구하며, 투자자들에게는 장기적인 안전성과 성장 잠재력을 보장하며,
                                임직원들에게는 근무 환경 제공을 통해 지속가능경영을 실천하기 위해 노력합니다.
                            </p>
                        </div>
                        <span class="mk-card-num">03</span>
                    </div>
                </article>

                 <article class="mk-card mk-scroll-spy">
                    <div class="mk-card-bg" style="background-image: url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070&auto=format&fit=crop');"></div>
                    <div class="mk-card-content">
                        <div class="mk-card-txt-group">
                            <span class="mk-card-icon">🚀</span>
                            <h3 class="mk-card-title">도전혁신경영</h3>
                            <p class="mk-card-desc">
                                국내 닭고기 산업의 혁신을 이룬 최초의 도전정신을 바탕으로 마니커는 새로운 시도를 두려워하지 않습니다.<br>
                                모든 임직원이 개척자 정신을 기본으로 새로운 제품 개발과 연구에 전념합니다.
                            </p>
                        </div>
                        <span class="mk-card-num">04</span>
                    </div>
                </article>

            </div>
        </div>
    </section>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const observerOptions = {
            root: null,
            rootMargin: "0px",
            threshold: 0.15 // 15% 보일 때 실행
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // 클래스명 충돌 방지를 위해 mk-active 사용
                    entry.target.classList.add("mk-active");
                }
            });
        }, observerOptions);

        // 선택자도 변경된 클래스명으로 적용
        const scrollElements = document.querySelectorAll(".mk-scroll-spy");
        scrollElements.forEach((el) => observer.observe(el));
    });
</script>