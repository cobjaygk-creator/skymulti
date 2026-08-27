<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$rb_skin = sql_fetch (" select * from {$rb_module_table} where md_id = '{$options}' "); //최신글 환경설정 테이블 조회 (삭제금지)
$thumb_width = 510;
$thumb_height = 282;

if(isset($rb_skin['md_title']) && $rb_skin['md_title']) {
    $bo_subjects = $rb_skin['md_title'];
}

?>

<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/style.css?ver=<?php echo G5_SERVER_TIME ?>">

<div class="bbs_main add_c">
    <!-- 제목 영역 -->
    <ul class="bbs_main_wrap_tit" style="display:<?php echo (isset($rb_skin['md_title_hide']) && $rb_skin['md_title_hide'] == '1') ? 'none' : 'block'; ?>">
        <li class="bbs_main_wrap_tit_l">
            <a href="javascript:void(0);">
                <h2 class="<?php echo isset($rb_skin['md_title_font']) ? $rb_skin['md_title_font'] : 'font-B'; ?>" style="color:<?php echo isset($rb_skin['md_title_color']) ? $rb_skin['md_title_color'] : '#25282b'; ?>; font-size:<?php echo isset($rb_skin['md_title_size']) ? $rb_skin['md_title_size'] : '20'; ?>px; "><?php echo $bo_subjects ?></h2>
            </a>
        </li>
        <li class="cb"></li>
    </ul>

    <div class="latest-tabs-wrap">

        <!-- 탭 { -->
        <?php if (count($tabs) > 1): ?>
        <nav class="bo_tab swiper-container swiper-container-tab-<?php echo $rb_skin['md_id']; ?>">
            <ul class="bo_tab_ul swiper-wrapper swiper-wrapper-tab-<?php echo $rb_skin['md_id']; ?>">
                <?php foreach ($tabs as $i => $tab): ?>
                <li class="swiper-slide swiper-slide-tab-<?php echo $rb_skin['md_id']; ?>">
                    <a href="javascript:void(0);" data-tab="tab-<?php echo $rb_skin['md_id'].'-'.$i; ?>" class="<?php echo $i == 0 ? 'active' : ''; ?>">
                        <?php
							if ($tab['sca']) {
								// 카테고리가 있을 때
								echo $tab['sca'];
							} else {
								// 카테고리가 없을 때
								echo '전체';
							}
						?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <script>
            $(document).ready(function() {
                setTimeout(function() {

                    var swiper = new Swiper('.swiper-container-tab-<?php echo $rb_skin['md_id']; ?>', {
                        slidesPerView: 'auto',
                        spaceBetween: 5,
                        touchRatio: 1,
                        observer: true,
                        observeParents: true
                    });

                }, 50);
            });
        </script>
        <?php endif; ?>

        <!-- } -->

        <div class="latest-tab-wrap">

            <!-- 탭별 콘텐츠 -->
            <?php foreach ($tabs as $i => $tab): ?>
            <?php
        $list = $tab['list'];
        $list_count = count($list);
        $bo_table = $tab['bo_table'];
        $cate = $tab['sca'];
      ?>

            <div class="latest-tab-content-thumb-top<?php echo $i == 0 ? ' active' : ''; ?>" id="tab-<?php echo $rb_skin['md_id'].'-'.$i; ?>">

                <?php
        if(isset($cate) && $cate) { 
            $links_url = get_pretty_url($bo_table,'','sca='.urlencode($cate));
        } else {
            $links_url = get_pretty_url($bo_table);
        }
        ?>
                <button type="button" class="more_btn" onclick="location.href='<?php echo $links_url ?>';" style="display:<?php echo (isset($rb_skin['md_title_hide']) && $rb_skin['md_title_hide'] == '1') ? 'none' : 'block'; ?>">더보기</button>

                <div class="rb_swiper" id="rb_swiper_<?php echo $rb_skin['md_id'].'_'.$i ?>" data-pc-w="<?php echo $rb_skin['md_col'] ?>" data-pc-h="<?php echo $rb_skin['md_row'] ?>" data-mo-w="<?php echo $rb_skin['md_col_mo'] ?>" data-mo-h="<?php echo $rb_skin['md_row_mo'] ?>" data-pc-gap="<?php echo $rb_skin['md_gap'] ?>" data-mo-gap="<?php echo $rb_skin['md_gap_mo'] ?>" data-autoplay="<?php echo $rb_skin['md_auto_is'] ?>" data-autoplay-time="<?php echo $rb_skin['md_auto_time'] ?>" data-pc-swap="<?php echo $rb_skin['md_swiper_is'] ?>" data-mo-swap="<?php echo $rb_skin['md_swiper_is'] ?>">

                    <div class="rb_swiper_inner">
                        <div class="rb-swiper-wrapper swiper-wrapper">

                            <?php foreach ($list as $row): ?>
                            <?php
                              $thumb = get_list_thumbnail($bo_table, $row['wr_id'], $thumb_width, $thumb_height, false, true);
                              $img = ($thumb['src'] && !strstr($row['wr_option'], 'secret')) ? $thumb['src'] : (strstr($row['wr_option'], 'secret') ? G5_THEME_URL.'/rb.img/sec_image.png' : G5_THEME_URL.'/rb.img/no_image.png');
                              $thumb_alt = $thumb['alt'] ?: '이미지';
                              $wr_href = get_pretty_url($bo_table, $row['wr_id']);
                              $wr_content = strip_tags($row['wr_content']);
                              $is_secret = strstr($row['wr_option'], 'secret');
                            ?>

                            <div class="rb_swiper_list main_list">

                                <!-- for { -->

                                <div>

                                    <?php if($rb_skin['md_thumb_is'] == 1) { //모듈설정:썸네일 출력여부(1,0)?>
                                    <ul class="bbs_main_wrap_con_ul1">
                                        <a href="<?php echo $wr_href ?>"><img src="<?php echo $img ?>" alt="<?php echo $thumb_alt ?>" class="skin_list_image"></a>

                                        <?php if($rb_skin['md_icon_is'] == 1) { //모듈설정:아이콘 출력여부(1,0)?>
                                        <div class="icon_abs">
                                            <?php if ($row['icon_new']) echo "<span class=\"bbs_list_label label3\">새글</span>"; ?>
                                            <?php if ($row['icon_hot']) echo "<span class=\"bbs_list_label label1\">인기</span>"; ?>
                                        </div>
                                        <?php } ?>
                                    </ul>
                                    <?php } ?>

                                    <ul class="bbs_main_wrap_con_ul2" <?php if($rb_skin['md_thumb_is'] != 1) { //모듈설정:썸네일 출력하지 않는경우 ?>style="width:100%" <?php } ?>>

                                        <?php if($rb_skin['md_nick_is'] == 1 || $rb_skin['md_date_is'] == 1 || $rb_skin['md_ca_is'] == 1 || $rb_skin['md_comment_is'] == 1) {?>
                                        <li class="bbs_main_wrap_con_info">

                                            <?php if($rb_skin['md_nick_is'] == 1) { //모듈설정:작성자 출력여부(1,0)?>
                                            <span class="font-B"><?php echo $row['wr_name'] ?></span>　
                                            <?php } ?>

                                            <?php if($rb_skin['md_date_is'] == 1) { //모듈설정:작성일 출력여부(1,0)?>
                                            <?php echo passing_time($row['wr_datetime']) ?>　
                                            <?php } ?>

                                            <?php if($rb_skin['md_ca_is'] == 1 && $row['ca_name']) { //모듈설정:카테고리 출력여부(1,0) || 카테고리 있을때만?>

											 <!-- 카데고리 출력 시작-->
												<?php
												$category_img_map = array(
													'현대' => $latest_skin_url.'/img/logo_hd.png',
													'기아' => $latest_skin_url.'/img/logo_kia.png',
													'제네시스' => $latest_skin_url.'/img/logo_gen.png',
													'테슬라' => $latest_skin_url.'/img/logo_tes.png',
													'KG모빌리티' => $latest_skin_url.'/img/logo_kgm.png',
													'쉐보레' => $latest_skin_url.'/img/logo_che.png',
													'르노' => $latest_skin_url.'/img/logo_ren.png',
												);

												$category_name = $row['ca_name'];

												if(isset($category_img_map[$category_name])) {
													echo '<img src="'.$category_img_map[$category_name].'" alt="'.$category_name.'" style="width:auto; height:16px;border:0;">';
												} else {
													echo $category_name;
												}
												?>
											<!-- 카테고리 출력 끝-->


                                            <?php } ?>

                                            <?php if($rb_skin['md_comment_is'] == 1) { //모듈설정:댓글 출력여부(1,0 || 댓글이 0개 이상인 경우)?>
                                            <?php if($row['comment_cnt']) { ?>
                                            댓글 <?php echo number_format($row['wr_comment']); ?>　
                                            <?php } ?>
                                            조회 <?php echo number_format($row['wr_hit']); ?>　
                                            <?php } ?>



                                        </li>
                                        <?php } ?>

                                        <?php if($rb_skin['md_subject_is'] == 1) { //모듈설정:제목 출력여부(1,0) ?>
                                        <li class="bbs_main_wrap_con_subj cut"><a href="<?php echo $wr_href ?>" class="font-B"><?php echo $row['subject'] ?></a></li>
                                        <?php } ?>
										<?php if (!empty($row['wr_6'])) { ?>
											<li class="bbs_prd_list_con_li3 cut2  main_price_dec">
												<?php echo get_text($row['wr_6']); ?>
											</li>
										<?php } ?>

									<?php if (!empty($row['wr_1'])) { 
										// 제거할 문자열 배열
										$remove_labels = ['|상품별 선택', '|가격표참조'];

										// wr_1 값에서 제거
										$price_text = str_replace($remove_labels, '', get_text($row['wr_1']));
									?>
										<li class="font-B info_pri_wrap price_main">
											<dd><?php echo $price_text; ?></dd>
										</li>
									<?php } ?>



											

                                        <?php if($rb_skin['md_content_is'] == 1) { //모듈설정:본문 출력여부(1,0)?>

                                        <?php if ($is_secret) { ?>
                                        <li class="bbs_main_wrap_con_cont">
                                            <a href="<?php echo $wr_href ?>" style="opacity:0.6" class="cut2">작성자 및 관리자 외 열람할 수 없습니다.<br>비밀글 기능으로 보호된 글입니다.</a>
                                        </li>
                                        <?php } else { ?>
                                        <li class="bbs_main_wrap_con_cont cut2">
                                            <a href="<?php echo $wr_href ?>" class="cut2"><?php echo $wr_content ?></a>
                                        </li>
                                        <?php } ?>

                                        <?php } ?>




                                    </ul>
                                    <div class="cb"></div>
                                </div>
                            </div>
                            <!-- } -->

                            <?php endforeach; ?>

                            <?php if ($list_count == 0): ?>
                            <div class="no_data" style="width:100% !important;">데이터가 없습니다.</div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php if($rb_skin['md_swiper_is'] == 1) { //모듈설정:스와이프 사용여부(1,0)?>
                    <div class="rb_swiper_paging_btn" style="display:<?php echo (isset($rb_skin['md_title_hide']) && $rb_skin['md_title_hide'] == '1') ? 'none' : 'block'; ?>">
                        <!-- 좌우 페이징 { -->
                        <button type="button" class="swiper-button-prev rb-swiper-prev">
                            <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_prev.svg">
                        </button>
                        <button type="button" class="swiper-button-next rb-swiper-next">
                            <img src="<?php echo G5_THEME_URL ?>/rb.img/icon/arr_next.svg">
                        </button>
                        <!-- } -->
                    </div>
                    <?php } ?>

                </div>

            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.swiper-slide-tab-<?php echo $rb_skin['md_id']; ?> a', function(e) {
        e.preventDefault();

        const $this = $(this);
        const targetId = $this.data('tab');
        const $wrap = $this.closest('.latest-tabs-wrap');

        // 탭 버튼 상태 변경
        $wrap.find('.swiper-slide-tab-<?php echo $rb_skin['md_id']; ?> a')
            .removeClass('active');
        $this.addClass('active');

        // 탭 콘텐츠 전환
        $wrap.find('.latest-tab-content-thumb-top').removeClass('active');
        const $targetTab = $wrap.find('#' + targetId).addClass('active');

        // 탭 콘텐츠가 보이도록 된 후 슬라이더 초기화
        setTimeout(() => {
            // Swiper 초기화는 보이는 상태에서만 실행
            $targetTab.find('.rb_swiper').each(function() {
                if (!$(this).hasClass('swiper-initialized')) {
                    // 이미 초기화된 게 아니면 실행
                    setupResponsiveSlider($(this));
                }
            });
        }, 10);
    });
</script>

<script>
    $(document).ready(function() {
        // PHP 변수를 JS 변수로 가져오기
        var skinId = "<?php echo $rb_skin['md_id']; ?>";
        // Swiper 클래스명 지정
        var swiperContainerClass = '.swiper-container-tab-' + skinId;
        
        var swiperInstance = null;
        var autoPlayTimer = null;
        var autoDelay = 4000; // 3초 간격 (조절 가능)

        // 1. Swiper 초기화 (탭 메뉴 슬라이드 기능)
        setTimeout(function() {
            if ($(swiperContainerClass).length > 0) {
                swiperInstance = new Swiper(swiperContainerClass, {
                    slidesPerView: 'auto',
                    spaceBetween: 5,
                    touchRatio: 1,
                    observer: true,
                    observeParents: true,
                    freeMode: true 
                });
            }
            startAutoPlay(); // 로딩 완료 후 자동재생 시작
        }, 50);

        // 2. [수정됨] 자동재생 함수 (인덱스 계산 방식)
        function startAutoPlay() {
            // 기존 타이머 초기화 (중복 실행 방지)
            if (autoPlayTimer) clearInterval(autoPlayTimer);
            
            autoPlayTimer = setInterval(function() {
                // 전체 탭 버튼 리스트 가져오기
                var $allTabs = $('.swiper-slide-tab-' + skinId + ' a');
                
                // 현재 활성화(active)된 탭이 몇 번째인지 찾기 (0부터 시작)
                var currentIndex = $allTabs.index($('.swiper-slide-tab-' + skinId + ' a.active'));
                
                // 다음 순서 계산: (현재 + 1)을 전체 개수로 나눈 나머지
                // 예: 총 5개일 때, 4번(마지막) -> (4+1)%5 = 0 (첫번째로 돌아감)
                var nextIndex = (currentIndex + 1) % $allTabs.length;

                // 계산된 다음 탭 클릭 (강제 트리거)
                $allTabs.eq(nextIndex).trigger('click');
                
            }, autoDelay);
        }

        // 3. 마우스 호버 시 멈춤/재개 기능
        $('.latest-tabs-wrap').hover(
            function() { clearInterval(autoPlayTimer); }, // 마우스 올리면 정지
            function() { startAutoPlay(); }               // 마우스 떼면 다시 시작
        );

        // 4. 탭 클릭 및 화면 전환 처리 (애니메이션 로직)
        $(document).on('click', '.swiper-slide-tab-' + skinId + ' a', function(e) {
            e.preventDefault();

            const $this = $(this);
            const targetId = $this.data('tab');
            const $wrap = $this.closest('.latest-tabs-wrap');
            const $contentWrap = $wrap.find('.latest-tab-wrap'); // 컨텐츠 감싸는 박스
            const $parentLi = $this.parent('li'); // li 태그
            
            // 이미 활성화된 탭을 또 클릭했다면 무시
            if ($this.hasClass('active')) return;

            // [메뉴] 버튼 활성화 스타일 변경
            $wrap.find('.swiper-slide-tab-' + skinId + ' a').removeClass('active');
            $this.addClass('active');

            // [메뉴] 탭 메뉴바 위치 이동 (Swiper)
            if (swiperInstance) {
                swiperInstance.slideTo($parentLi.index(), 300);
            }

            // [높이 고정] 전환 시 화면 덜컹거림 방지
            var currentHeight = $contentWrap.height();
            $contentWrap.css('height', currentHeight + 'px');
            $contentWrap.css('overflow', 'hidden');

            // [컨텐츠] 1단계: 기존 컨텐츠 퇴장 (Fade Out + Scale Down)
            var $currentContent = $wrap.find('.latest-tab-content-thumb-top.active');
            
            if ($currentContent.length > 0) {
                $currentContent.addClass('fading-out');

                // 0.15초 뒤에 교체 (CSS transition 시간과 동일하게)
                setTimeout(function() {
                    // 2단계: 기존 탭 끄고 새 탭 켜기
                    $currentContent.removeClass('active fading-out');
                    showNewTab($wrap, targetId);

                    // 3단계: 높이 고정 해제 (자연스럽게 늘어나도록)
                    $contentWrap.css('height', 'auto'); 
                    
                }, 3550); 
            } else {
                // 처음에 아무것도 없을 경우 바로 표시
                showNewTab($wrap, targetId);
                $contentWrap.css('height', 'auto');
            }
        });

        // [함수] 새 탭 보여주기 및 내부 슬라이더 초기화
        function showNewTab($wrap, targetId) {
            const $targetTab = $wrap.find('#' + targetId).addClass('active');

            // 내부 이미지 슬라이더(있을 경우) 재초기화
            $targetTab.find('.rb_swiper').each(function() {
                if (!$(this).hasClass('swiper-initialized')) {
                    if(typeof setupResponsiveSlider === 'function') {
                        setupResponsiveSlider($(this));
                    }
                } else if (this.swiper) {
                    this.swiper.update();
                }
            });
            return $targetTab;
        }
    });
</script>