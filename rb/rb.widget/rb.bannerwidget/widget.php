<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

//모듈정보 불러오기
$md_id = $row_mod['md_id']; //현재 모듈 ID
$rb_skin = sql_fetch (" select * from {$rb_module_table} where md_id = '{$md_id}' "); //환경설정 테이블 조회
$md_subject = $rb_skin['md_title']; //모듈 타이틀

?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/rb/rb.widget/rb.bannerwidget/style.css?ver=<?php echo G5_TIME_YMDHIS ?>">

<div class="rb_banner_collection">
    <div class="rb_banner_collection_container">
        <!--<div class="rb_banner_collection_header">
            <h3 class="rb_banner_collection_title">추천 서비스</h3>
            <p class="rb_banner_collection_subtitle">다양한 서비스를 한눈에 확인하세요</p>
        </div>-->
        
        <div class="rb_banner_grid">
            <!-- 배너 아이템들 -->
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_01.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_02.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_03.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_04.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_05.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_06.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_07.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_08.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_09.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_10.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_11.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_12.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_13.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_14.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_15.png" alt=""></div>
            </div>
            <div class="rb_banner_item">
                    <div class="rb_banner_image"><img src="/img/mbn/logo_16.png" alt=""></div>
            </div>
 
        </div>
    </div>
</div>