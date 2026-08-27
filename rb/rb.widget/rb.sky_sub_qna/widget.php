<style>
    .rb_w100_div {
        background-color: #000;
        margin-top: 0px;
        padding: 70px 0;
        text-align: center;
    } /* 기본 스타일 (PC) */

    .rb_w100_div ul {margin:0 auto;} /* 내부 ul 중앙정렬 */
    
    .content_box_01 {
        text-align: center;
        position: relative;
    }

    .content_box_01 h2 {
        color: #fff;
        font-size: 50px;
        margin-bottom: 10px;
        word-break: keep-all; /* 단어 단위 줄바꿈 */
    }

    .content_box_01 h3 {
        color: #ddd;
        font-size: 50px;
        margin-bottom: 25px;
		font-weight:400;
        word-break: keep-all;
    }

    .content_box_01 h4 {
        color: #ddd;
        font-size: 25px;
        margin-bottom: 25px;
        word-break: keep-all;
    }

    .btn_qna {
        border: 0px solid #fff;
        border-radius: 30px;
        padding: 15px 40px;
        background: #383838;
        color: #fff;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
    }

    .car_main_bn {
        position: absolute;
        top: 10px;
        right: 50px;
        transform: translateX(100%); /* 오른쪽 바깥쪽 */
        opacity: 0;
        transition: transform 1s ease, opacity 1s ease; /* 부드러운 애니메이션 */
    }

    .car_main_bn.show {
        transform: translateX(0); /* 제자리로 이동 */
        opacity: 1;
    }

    .car_main_bn img {
        width: 550px;
    }

    /* 모바일 & 태블릿 수정 (1024px 이하) */
    @media all and (max-width:1024px) {
        .rb_w100_div {
            background-color: #000; /* ★수정됨: 보라색(#a510ea) -> 검정색(#000) */
            padding-top: 60px;
            padding-bottom: 60px;
            padding-left: 0;
            text-align: center;
        }

        .rb_w100_div ul {
            padding-left: 20px;
            padding-right: 20px;
            width: 100% !important;
        }

        .content_box_01 h2 {
            color: #fff;
            font-size: 30px; /* 모바일에서 폰트 크기 조정 */
            margin-bottom: 15px;
            line-height: 1.0;
        }

        .content_box_01 h3 {
            color: #ddd; /* ★수정됨: 연보라색(#e8d6f0) -> 회색(#ddd) PC와 통일 */
            font-size: 30px;
            margin-bottom: 30px;
            line-height: 1.0;
        }

        .content_box_01 h4 {
            color: #ddd; /* ★수정됨: 연보라색(#e8d6f0) -> 회색(#ddd) PC와 통일 */
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.0;
        }

        .car_main_bn {
            display: none; /* 모바일에서는 이미지 숨김 유지 */
        }
        
        .btn_qna {
            font-size: 15px;
            padding: 12px 30px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const carBn = document.querySelector('.car_main_bn');
    if (carBn) {
        setTimeout(() => {
            carBn.classList.add('show'); // 페이지 로드 후 바로 애니메이션 실행
        }, 300); // 0.3초 지연 후 실행 (자연스러움)
    }
});
</script>

<div class="rb_w100_div rb_w100_<?php echo $row_mod['md_id'] ?>" class="sky_wbn01">
    <div class="content_box_01">
        <h2>
		— <br />
		Your Car, Your Adventure
		</h2>
		<h3>We Make It Happen.</h3>
        <h4>신차 패키지부터 차박 시공까지, 궁금한 건 무엇이든 OK.</h4>
        <a href="/bbs/board.php?bo_table=qa"><button class="btn_qna">지금 문의하기</button></a>
        <div class="car_main_bn"><img src="/img/tasman.png" /></div>
    </div>
</div>

<script>
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
</script>