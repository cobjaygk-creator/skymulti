<style>
    
    .rb_w100_div {background-color: #f9f9f9; padding-top:50px; padding-bottom: 50px; text-align: center;} /* 기본 스타일 */
    .rb_w100_div ul {margin:0 auto;} /* 내부 ul 중앙정렬 */
    
    @media all and (max-width:1024px) {
        /* 내부 ul 반응형 처리 */
        .rb_w100_div ul {padding-left: 20px; padding-right: 20px; width: 100% !important;}
    }

    
</style>

<div class="rb_w100_div rb_w100_<?php echo $row_mod['md_id'] ?>">
   
    <!-- 가로 100% div 안에 빌더 메인 가로폭을 가진 ul을 넣을 수 있습니다 -->
    <!-- 불필요하시면 지우셔도 됩니다. -->
    <ul style="width:<?php echo $rb_core['main_width'] ?>px;">
        dsfdsf
    </ul>
    
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