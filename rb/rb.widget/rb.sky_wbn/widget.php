<style>
    
    .rb_w100_div {background-color: #a510ea; padding-top:60px; padding-bottom: 60px; padding-left:0; text-align: center;} /* 기본 스타일 */
    .rb_w100_div ul {margin:0 auto;} /* 내부 ul 중앙정렬 */
	.content_box_01{text-align: center;position:relative;}
	.content_box_01 h2{color:#fff;font-size:50px;margin-bottom:10px;}
	.content_box_01 h3{color:#e8d6f0;font-size:25px;margin-bottom:25px;}
	.btn_qna{border:0px solid #fff; border-radius:30px;padding:15px 40px;background:#383838;color:#fff;font-size:17px;font-weight:600;}
.car_main_bn {
  position: absolute;
  top: 10px;
  right: 50px;
  transform: translateX(100%); /* 기본은 오른쪽 밖 */
  opacity: 0;
  transition: transform 1s ease, opacity 1s ease; /* 부드럽게 나타나도록 */
}

.car_main_bn.show {
  transform: translateX(0); /* 제자리로 이동 */
  opacity: 1;
}

.car_main_bn img {
  width: 550px;
}
    
    @media all and (max-width:1024px) {
        /* 내부 ul 반응형 처리 */
		 .rb_w100_div {background-color: #a510ea; padding-top:45px; padding-bottom: 45px; padding-left:0; text-align: center;} /* 기본 스타일 */
        .rb_w100_div ul {padding-left: 20px; padding-right: 20px; width: 100% !important;}
			.content_box_01 h2{color:#fff;font-size:34px;margin-bottom:10px;}
	.content_box_01 h3{color:#e8d6f0;font-size:16px;margin-bottom:25px;}
		.car_main_bn{display:none;}
    }

    
</style>
<script>
const carBn = document.querySelector('.car_main_bn');

window.addEventListener('scroll', () => {
  const rect = carBn.getBoundingClientRect();
  if(rect.top < window.innerHeight && rect.bottom > 0){
    carBn.classList.add('show');
  }
});
</script>

<div class="rb_w100_div rb_w100_<?php echo $row_mod['md_id'] ?>" class="sky_wbn01">
   
    <!-- 가로 100% div 안에 빌더 메인 가로폭을 가진 ul을 넣을 수 있습니다 -->
    <!-- 불필요하시면 지우셔도 됩니다. -->
    <div class="content_box_01">
          <h2>궁금한 게 있다면,<br />스카이멀티에게 물어보세요!</h2>
		  <h3>신차 패키지부터 차박 시공까지, 궁금한 건 무엇이든 OK.</h3>
		  <a href="/bbs/board.php?bo_table=qa"><button class="btn_qna">지금 문의하기</button></a>
		  <div class="car_main_bn"><img src="img/tasman.png" /></div>
    </div>
    
</div>


    <script>
        
        //부모 width를 무시하고 div 를 100%로 만들고, 모듈설정 버튼의 100% 처리를 위해 스크립트를 사용 합니다.
        //복제 사용을 위해 $row_mod['md_id'](모듈ID) 를 활용 합니다.
        
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