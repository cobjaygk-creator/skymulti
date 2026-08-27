<style>

.redcap-difference {
    background: #000;
    color: #fff;
    text-align: center;
    margin-top: -80px;
    padding: 150px 20px;
    width: 100vw;
    position: relative;
    left: 50%;
    transform: translateX(-50%);
}

/* 텍스트의 초기 상태 (투명하고 위로 약간 이동) */
.redcap-difference h2,
.redcap-difference .subtitle {
    opacity: 0;
    transform: translateY(-20px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

/* 텍스트 애니메이션 지연 시간 */
.redcap-difference h2 {
    transition-delay: 0s; /* 먼저 나타남 */
}

.redcap-difference .subtitle {
    transition-delay: 0.2s; /* h2 다음에 나타남 */
}

/* 텍스트 애니메이션 최종 상태 */
.redcap-difference h2.is-visible,
.redcap-difference .subtitle.is-visible {
    opacity: 1;
    transform: translateY(0);
}


.redcap-difference h2 {
    font-size: 80px;
    margin: 0px 0 28px 0px;
    line-height: 80px;
    font-weight: 100 !important;
}

.redcap-difference h2 span {
    font-size: 80px;
    margin: 0px 0 10px 0px;
    line-height: 70px;
    color: #8aff00;
}

.redcap-difference .subtitle {
    margin-bottom: 50px;
    font-size: 30px;
    color: #ddd;
}

.cards {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.card {
    color: #fff;
    width: 400px;
    padding: 0px 0;
    border-radius: 30px;
    text-align: center;
    /* 카드의 초기 상태 (투명하고 아래로 약간 이동) */
    opacity: 0;
    transform: translateY(50px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}

/* 각 카드의 애니메이션 지연 시간 */
.card:nth-child(1) {
    transition-delay: 0.4s;
}
.card:nth-child(2) {
    transition-delay: 0.6s;
}
.card:nth-child(3) {
    transition-delay: 0.8s;
}

.card.is-visible {
    /* 카드의 최종 상태 (불투명하고 제자리로 이동) */
    opacity: 1;
    transform: translateY(0);
}

.card-image {
    position: relative;
    margin-bottom: 20px;
}

.card-image img {}

.overlay-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-weight: bold;
    font-size: 35px;
    text-transform: capitalize;
}

.btn {
    display: inline-block;
    margin-top: 15px;
    background: #d40000;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
}

.rb_w100_div2 {
    background-color: #f9f9f9;
    padding-top: 0px;
    padding-bottom: 0px;
    text-align: center;
}

.rb_w100_div2 ul {
    margin: 0 auto;
}

.sky_wbn01 {}

@media all and (max-width: 1024px) {
    .rb_w100_div2 ul {
        padding-left: 20px;
        padding-right: 20px;
        width: 100% !important;
    }
}

@media all and (max-width: 600px) {
    .redcap-difference {
        background: #000;
        color: #fff;
        text-align: center;
        padding: 100px 0 40px 0;
        position: relative;
        left: 0;
        transform: translateX(0%);
    }

    .redcap-difference h2 {
        font-size: 40px;
        margin: 0px 0 0px 0px;
        line-height: 50px;
        font-weight: 100 !important;
    }

    .redcap-difference h2 span {
        font-size: 40px;
        margin: 0px 0 0px 0px;
        line-height: 60px;
        color: #8aff00;
    }

    .redcap-difference .subtitle {
        margin-bottom: 0px;
        font-size: 18px;
        color: #ddd;
    }

    .cards {
        display: none;
    }
}
</style>

<div class="rb_w100_div2 rb_w100_<?php echo $row_mod['md_id'] ?>" class="sky_wbn01">
        <section class="redcap-difference" id="animationSection">
            <div class="container">
                <h2 id="mainHeading"><span>WHY</span> go with Skymulti</h2>
                <p class="subtitle" id="subTitle">스카이멀티는 고객 만족을 최우선으로,<br />차별화된 서비스를 제공합니다.</p>

                <div class="cards">
                    <div class="card">
                        <div class="card-image">
                            <img src="/img/nm_feature_img_01.png" alt="careful">
                        </div>
                        <h3>원스톱 서비스</h3>
                        <p>회원님의 시간을 절약해드리는<br />원스톱 토탈 서비스가 준비된 스카이멀티입니다.</p>
                    </div>
                    <div class="card">
                        <div class="card-image">
                            <img src="/img/nm_feature_img_02.png" alt="careful">
                        </div>
                        <h3>철저한 사후 관리</h3>
                        <p>5년,10년 이상 타는 차! 업계 최초 보증기간 2년<br />(단, 전장제품에 한하여 1년)</p>
                    </div>
                    <div class="card">
                        <div class="card-image">
                            <img src="/img/nm_feature_img_03.png" alt="careful">
                        </div>
                        <h3>합리적인 가격</h3>
                        <p>실속있는 패키지! 합리적인 가격으로<br />회원님의 차량을 업그레이드 해드립니다.</p>
                    </div>
                </div>
            </div>
        </section>

</div>

<script>
    // 부모 width를 무시하고 div 를 100%로 만들고, 모듈설정 버튼의 100% 처리를 위해 스크립트를 사용 합니다.
    // 복제 사용을 위해 $row_mod['md_id'](모듈ID) 를 활용 합니다.
    function adjustDivWidth_<?php echo $row_mod['md_id'] ?>() {
        const content_w = $('.rb_w100_<?php echo $row_mod['md_id'] ?>');
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


    // ---- 스크롤 애니메이션 추가 ----
    const animationSection = document.getElementById('animationSection');
    const mainHeading = document.getElementById('mainHeading');
    const subTitle = document.getElementById('subTitle');
    const cards = document.querySelectorAll('.card');
    let hasAnimated = false;

    function handleScrollAnimation() {
        if (hasAnimated) {
            return;
        }

        const rect = animationSection.getBoundingClientRect();
        const viewportHeight = window.innerHeight;

        // 뷰포트의 상단에서 특정 지점에 도달했을 때 애니메이션 시작
        if (rect.top <= viewportHeight / 2) {
            // 텍스트에 애니메이션 클래스 추가
            mainHeading.classList.add('is-visible');
            subTitle.classList.add('is-visible');

            // 카드에 애니메이션 클래스 추가 (시간차 적용)
            cards.forEach(card => {
                card.classList.add('is-visible');
            });

            hasAnimated = true;
            window.removeEventListener('scroll', handleScrollAnimation);
        }
    }

    window.addEventListener('scroll', handleScrollAnimation);
    handleScrollAnimation();
</script>