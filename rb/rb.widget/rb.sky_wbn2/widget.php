<style>
/* 공통 스타일 */
.redcap-difference {
    background: url('./m_feature_bg.jpg') center/cover no-repeat;
    color: #fff;
    text-align: center;
    padding: 180px 20px;
    width: 100vw;
    position: relative;
    left: 50%;
    transform: translateX(-50%);
    box-sizing: border-box;
}

.redcap-difference h2 {
    font-size: 90px;
    margin: 0px 0 28px 0px;
    line-height: 1.1;
    font-weight: 100 !important;
    word-break: keep-all;
}

.redcap-difference h2 span {
    font-size: 80px;
    margin: 0px 0 10px 0px;
    line-height: 70px;
    color: #8aff00;
    display: inline-block;
}

.redcap-difference .subtitle {
    margin-bottom: 50px;
    font-size: 30px;
    color: #ddd;
    word-break: keep-all;
}

.cards {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.card {
    color: #fff;
    width: 100%;
    max-width: 400px;
    padding: 60px 20px;
    border-radius: 30px;
    text-align: center;
    box-sizing: border-box;
}

.cards h3 {
    font-size: 30px;
    margin: 30px 0 10px 0;
    word-break: keep-all;
}

.cards p {
    font-size: 16px;
    word-break: keep-all;
    line-height: 1.6;
}

.card-image {
    position: relative;
    margin-bottom: 20px;
}

.card-image img {
    max-width: 100%;
    height: auto;
}

.rb_w100_div2 {
    background-color: #f9f9f9;
    padding-top: 0px;
    padding-bottom: 0px;
    text-align: center;
    overflow: hidden;
}

.rb_w100_div2 ul {
    margin: 0 auto;
    padding: 0;
    list-style: none;
}

/* 태블릿 스타일 (1024px 이하) */
@media all and (max-width: 1024px) {
    .rb_w100_div2 ul {
        width: 100% !important;
    }
    .redcap-difference h2 { font-size: 60px; }
    .redcap-difference h2 span { font-size: 55px; }
}

/* 모바일 전용 스타일 (768px 이하) - 배경 꽉 차게 수정 */
@media all and (max-width: 768px) {
    .redcap-difference {
        width: 100vw !important; /* 화면 가로 꽉 채움 */
        margin: 0 !important; /* 외부 여백 제거 */
        border-radius: 0 !important; /* 둥근 모서리 제거 */
        padding: 60px 20px;
        left: 50% !important;
        transform: translateX(-50%) !important;
    }

    .redcap-difference h2 { 
        font-size: 42px; 
        line-height: 1.2;
    }
    
    .redcap-difference h2 span { 
        font-size: 38px; 
        line-height: 1.1;
        display: block; 
    }

    .redcap-difference .subtitle {
        font-size: 18px;
        margin-bottom: 30px;
    }

    .cards {
        gap: 20px;
    }

    .card {
        padding: 40px 20px;
        border-radius: 20px;
        background: rgba(0,0,0,0.15); /* 배경 위에서 글자 가독성을 위해 살짝 어둡게 */
        max-width: 90%; /* 카드는 화면 중앙에 약간의 여백 생성 */
    }
    
    .cards h3 { font-size: 24px; }
    .cards p { font-size: 14px; }
}
</style>

<div class="rb_w100_div2 rb_w100_<?php echo $row_mod['md_id'] ?>">
    <ul style="max-width:<?php echo $rb_core['main_width'] ?>px; width: 100%;">
        <section class="redcap-difference">
          <div class="container">
            <h2><span>WHY</span> go with Skymulti</h2>
            <p class="subtitle">스카이멀티는 고객 만족을 최우선으로,<br />차별화된 서비스를 제공합니다.</p>

            <div class="cards">
              <div class="card">
                <div class="card-image">
                  <img src="img/nm_feature_img_01.png" alt="One-stop Service">
                </div>
                <h3>원스톱 서비스</h3>
                <p>회원님의 시간을 절약해드리는<br />원스톱 토탈 서비스가 준비된 스카이멀티입니다.</p>
              </div>

              <div class="card">
                <div class="card-image">
                  <img src="img/nm_feature_img_02.png" alt="After Service">
                </div>
                <h3>철저한 사후 관리</h3>
                <p>5년,10년 이상 타는 차! 업계 최초 보증기간 2년<br />(단, 전장제품에 한하여 1년)</p>
              </div>

              <div class="card">
                <div class="card-image">
                  <img src="img/nm_feature_img_03.png" alt="Reasonable Price">
                </div>
                <h3>합리적인 가격</h3>
                <p>실속있는 패키지! 합리적인 가격으로<br />회원님의 차량을 업그레이드 해드립니다.</p>
              </div>
            </div>
          </div>
        </section>
    </ul>
</div>

<script>
    function adjustDivWidth_<?php echo $row_mod['md_id'] ?>() {
        const content_w = $('.rb_w100_<?php echo $row_mod['md_id'] ?>');
        const firstAdminOv_w = content_w.nextUntil('.admin_ov').next('.admin_ov');
        
        // 데스크탑, 모바일 모두 breakout(100vw) 처리 유지
        content_w.css({
            'width': '100vw',
            'position': 'relative',
            'left': '50%',
            'transform': 'translateX(-50%)',
            'margin-left': '0', 
            'margin-right': '0'
        });
        
        if (firstAdminOv_w.length) {
            firstAdminOv_w.css({
                'width': '100vw',
                'left': '50%',
                'transform': 'translateX(-50%)'
            });
        }
    }

    $(document).ready(adjustDivWidth_<?php echo $row_mod['md_id'] ?>);
    $(window).resize(adjustDivWidth_<?php echo $row_mod['md_id'] ?>);
</script>