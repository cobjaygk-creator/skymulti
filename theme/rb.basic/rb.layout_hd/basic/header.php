<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 레이아웃 폴더내 style.css 파일
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/rb.layout_hd/'.$rb_core['layout_hd'].'/style.css?ver='.G5_TIME_YMDHIS.'">', 0);

// ▼▼▼ [모달창일 경우 헤더 출력 중지] ▼▼▼
if (isset($_REQUEST['is_modal']) && $_REQUEST['is_modal'] == '1') return;
// ▲▲▲ [추가 끝] ▲▲▲
?>
   
    <header id="header">
       
        <div class="gnb_wrap">
            
            <div class="inner" style="width:<?php echo $tb_width_inner ?>; <?php echo $tb_width_padding ?>">
               
                <ul class="tog_wrap mobile">
                    <li>
                        <button type="button" alt="메뉴열기" id="tog_gnb_mobile">
                            <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 14C17.2549 14.0003 17.5 14.0979 17.6854 14.2728C17.8707 14.4478 17.9822 14.687 17.9972 14.9414C18.0121 15.1958 17.9293 15.4464 17.7657 15.6418C17.6021 15.8373 17.3701 15.9629 17.117 15.993L17 16H1C0.74512 15.9997 0.499968 15.9021 0.314632 15.7272C0.129296 15.5522 0.017765 15.313 0.00282788 15.0586C-0.0121092 14.8042 0.0706746 14.5536 0.234265 14.3582C0.397855 14.1627 0.629904 14.0371 0.883 14.007L1 14H17ZM17 7C17.2652 7 17.5196 7.10536 17.7071 7.29289C17.8946 7.48043 18 7.73478 18 8C18 8.26522 17.8946 8.51957 17.7071 8.70711C17.5196 8.89464 17.2652 9 17 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8C0 7.73478 0.105357 7.48043 0.292893 7.29289C0.48043 7.10536 0.734784 7 1 7H17ZM17 0C17.2652 0 17.5196 0.105357 17.7071 0.292893C17.8946 0.48043 18 0.734784 18 1C18 1.26522 17.8946 1.51957 17.7071 1.70711C17.5196 1.89464 17.2652 2 17 2H1C0.734784 2 0.48043 1.89464 0.292893 1.70711C0.105357 1.51957 0 1.26522 0 1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H17Z" fill="#09244B"/>
                            </svg>
                        </button>
                        
                        <script>
                            $(document).ready(function() {
                                $('#tog_gnb_mobile').click(function() {
                                    $('#cbp-hrmenu-btm').addClass('active');
                                    $('#m_gnb_close_btn').addClass('active');
                                    $('main').addClass('moves');
                                    $('header').addClass('moves');
                                });
                            });
                        </script>
                    </li>
                </ul>
                <ul class="logo_wrap">
                    <li>
                        <a href="<?php echo G5_URL ?>" alt="<?php echo $config['cf_title']; ?>">
                           
                            <picture id="logo_img">
                                <?php if (!empty($rb_builder['bu_logo_mo']) && !empty($rb_builder['bu_logo_mo_w'])) { ?>
                                    <source id="sourceSmall" srcset="<?php echo G5_URL ?>/data/logos/mo?ver=<?php echo G5_SERVER_TIME ?>" media="(max-width: 1024px)">
                                <?php } else { ?>
                                    <source id="sourceSmall" srcset="<?php echo G5_THEME_URL ?>/rb.img/logos/mo.png?ver=<?php echo G5_SERVER_TIME ?>" media="(max-width: 1024px)">
                                <?php } ?>
                                
                                <?php if (!empty($rb_builder['bu_logo_pc']) && !empty($rb_builder['bu_logo_pc_w'])) { ?>
                                    <source id="sourceLarge" srcset="<?php echo G5_URL ?>/data/logos/pc?ver=<?php echo G5_SERVER_TIME ?>" media="(min-width: 1025px)">
                                <?php } else { ?>
                                    <source id="sourceLarge" srcset="<?php echo G5_THEME_URL ?>/rb.img/logos/pc.png?ver=<?php echo G5_SERVER_TIME ?>" media="(max-width: 1024px)">
                                <?php } ?>
                                
                                <?php if (!empty($rb_builder['bu_logo_pc']) && !empty($rb_builder['bu_logo_pc_w'])) { ?>
                                    <img id="fallbackImage" src="<?php echo G5_URL ?>/data/logos/pc?ver=<?php echo G5_SERVER_TIME ?>" alt="<?php echo $config['cf_title']; ?>" class="responsive-image">
                                <?php } else { ?>
                                    <img id="fallbackImage" src="<?php echo G5_THEME_URL ?>/rb.img/logos/pc.png?ver=<?php echo G5_SERVER_TIME ?>" alt="<?php echo $config['cf_title']; ?>" class="responsive-image">
                                <?php } ?>
                            </picture>

                        </a>
                    </li>
                </ul>
                
                <nav id="cbp-hrmenu" class="cbp-hrmenu pc">
                    <ul>
                    <?php
                    if(IS_MOBILE()) {
                        $menu_datas = get_menu_db(1, true);
                    } else { 
                        $menu_datas = get_menu_db(0, true);
                    }

                    $i = 0;
                    foreach($menu_datas as $row) {
                        if(empty($row)) continue;

                        // 1차 메뉴 권한 체크
                        if (!$is_admin && isset($row['me_level']) && $row['me_level'] > 0) {
                            if (isset($row['me_level_opt']) && $row['me_level_opt'] == 2) {
                                if ($row['me_level'] != $member['mb_level']) continue;
                            } else {
                                if ($row['me_level'] > $member['mb_level']) continue;
                            }
                        }
                    ?>
                        <li>
                            <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="font-B"><?php echo $row['me_name'] ?></a>
                            <?php
                            $k = 0;
                            $sub_menu_html = '';
                            
                            foreach((array)$row['sub'] as $row2) {
                                if(empty($row2)) continue;

                                // 2차 메뉴 권한 체크
                                if (!$is_admin && isset($row2['me_level']) && $row2['me_level'] > 0) {
                                    if (isset($row2['me_level_opt']) && $row2['me_level_opt'] == 2) {
                                        if ($row2['me_level'] != $member['mb_level']) continue;
                                    } else {
                                        if ($row2['me_level'] > $member['mb_level']) continue;
                                    }
                                }

                                $sub_menu_html .= '<li><a href="'.$row2['me_link'].'" target="_'.$row2['me_target'].'">'.$row2['me_name'].'</a></li>';
                                $k++;
                            }

                            if($k > 0) {
                                echo '<div class="cbp-hrsub"><div class="cbp-hrsub-inner"><div><ul>'.$sub_menu_html.'</ul></div></div></div>';
                            }
                            ?>
                        </li>
                    <?php
                        $i++;
                    }

                    if ($i == 0) {
                    ?>
                        <li><a href="javascript:void(0);">메뉴 준비 중입니다.</a></li>
                    <?php } ?>
                    </ul>
                </nav>
                
                <ul class="snb_wrap">
                    <li class="qm_wrap" style="display:none !important;">
                        <button type="button" alt="검색" id="search_top_btn">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.49928 1.91687e-08C7.14387 0.000115492 5.80814 0.324364 4.60353 0.945694C3.39893 1.56702 2.36037 2.46742 1.57451 3.57175C0.788656 4.67609 0.278287 5.95235 0.0859852 7.29404C-0.106316 8.63574 0.0250263 10.004 0.469055 11.2846C0.913084 12.5652 1.65692 13.7211 2.63851 14.6557C3.6201 15.5904 4.81098 16.2768 6.11179 16.6576C7.4126 17.0384 8.78562 17.1026 10.1163 16.8449C11.447 16.5872 12.6967 16.015 13.7613 15.176L17.4133 18.828C17.6019 19.0102 17.8545 19.111 18.1167 19.1087C18.3789 19.1064 18.6297 19.0012 18.8151 18.8158C19.0005 18.6304 19.1057 18.3796 19.108 18.1174C19.1102 17.8552 19.0094 17.6026 18.8273 17.414L15.1753 13.762C16.1633 12.5086 16.7784 11.0024 16.9504 9.41573C17.1223 7.82905 16.8441 6.22602 16.1475 4.79009C15.4509 3.35417 14.3642 2.14336 13.0116 1.29623C11.659 0.449106 10.0952 -0.000107143 8.49928 1.91687e-08ZM1.99928 8.5C1.99928 6.77609 2.6841 5.12279 3.90308 3.90381C5.12207 2.68482 6.77537 2 8.49928 2C10.2232 2 11.8765 2.68482 13.0955 3.90381C14.3145 5.12279 14.9993 6.77609 14.9993 8.5C14.9993 10.2239 14.3145 11.8772 13.0955 13.0962C11.8765 14.3152 10.2232 15 8.49928 15C6.77537 15 5.12207 14.3152 3.90308 13.0962C2.6841 11.8772 1.99928 10.2239 1.99928 8.5Z" fill="#09244B"/>
                            </svg>
                        </button>
                        
                        <div id="search_box_wrap">
                            <form name="fsearchbox" method="get" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
                            <ul>
                                <input type="text" name="stx" maxlength="20" class="w100 font-B" id="ser_inp_fc" placeholder="통합검색">
                                <button type="submit" alt="검색" class="ser_inner_btn">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.49928 1.91687e-08C7.14387 0.000115492 5.80814 0.324364 4.60353 0.945694C3.39893 1.56702 2.36037 2.46742 1.57451 3.57175C0.788656 4.67609 0.278287 5.95235 0.0859852 7.29404C-0.106316 8.63574 0.0250263 10.004 0.469055 11.2846C0.913084 12.5652 1.65692 13.7211 2.63851 14.6557C3.6201 15.5904 4.81098 16.2768 6.11179 16.6576C7.4126 17.0384 8.78562 17.1026 10.1163 16.8449C11.447 16.5872 12.6967 16.015 13.7613 15.176L17.4133 18.828C17.6019 19.0102 17.8545 19.111 18.1167 19.1087C18.3789 19.1064 18.6297 19.0012 18.8151 18.8158C19.0005 18.6304 19.1057 18.3796 19.108 18.1174C19.1102 17.8552 19.0094 17.6026 18.8273 17.414L15.1753 13.762C16.1633 12.5086 16.7784 11.0024 16.9504 9.41573C17.1223 7.82905 16.8441 6.22602 16.1475 4.79009C15.4509 3.35417 14.3642 2.14336 13.0116 1.29623C11.659 0.449106 10.0952 -0.000107143 8.49928 1.91687e-08ZM1.99928 8.5C1.99928 6.77609 2.6841 5.12279 3.90308 3.90381C5.12207 2.68482 6.77537 2 8.49928 2C10.2232 2 11.8765 2.68482 13.0955 3.90381C14.3145 5.12279 14.9993 6.77609 14.9993 8.5C14.9993 10.2239 14.3145 11.8772 13.0955 13.0962C11.8765 14.3152 10.2232 15 8.49928 15C6.77537 15 5.12207 14.3152 3.90308 13.0962C2.6841 11.8772 1.99928 10.2239 1.99928 8.5Z" fill="#09244B"/>
                                    </svg>
                                </button>
                            </ul>
                            <ul class="ser_ul_pd pt20">
                                <li class="font-B">많이 검색된 키워드</li>
                                <li class="mt-5">
                                    <?php echo popular("theme/rb.basic", 5); // 인기검색어  ?>
                                </li>
                            </ul>
                            <ul class="ser_ul_pd">
                                <li class="font-B mt-5">검색조건</li>
                                <li class="mt-10">
                                    <select name="sfl" id="sfl" class="select w40">
                                        <option value="wr_subject||wr_content"<?php echo get_selected($sfl, "wr_subject||wr_content") ?>>제목+내용</option>
                                        <option value="wr_subject"<?php echo get_selected($sfl, "wr_subject") ?>>제목</option>
                                        <option value="wr_content"<?php echo get_selected($sfl, "wr_content") ?>>내용</option>
                                        <option value="mb_id"<?php echo get_selected($sfl, "mb_id") ?>>회원아이디</option>
                                        <option value="wr_name"<?php echo get_selected($sfl, "wr_name") ?>>이름</option>
                                    </select>
                                    　
                                    <input type="radio" value="and" <?php echo ($sop == "and") ? "checked" : ""; ?> id="sop_and" name="sop">
                                    <label for="sop_and">and</label>　
                                    <input type="radio" value="or" <?php echo ($sop == "or") ? "checked" : ""; ?> id="sop_or" name="sop" >
                                    <label for="sop_or">or</label>
                                </li>
                            </ul>
                            </form>
                        </div>
                        
                        <script>
                            function fsearchbox_submit(f) {
                                var stx = f.stx.value.trim();
                                if (stx.length < 2) {
                                    alert("검색어는 두글자 이상 입력해주세요.");
                                    f.stx.select();
                                    f.stx.focus();
                                    return false;
                                }
                                var cnt = 0;
                                for (var i = 0; i < stx.length; i++) {
                                    if (stx.charAt(i) == ' ') cnt++;
                                }
                                if (cnt > 1) {
                                    alert("빠른 검색을 위해 공백은 한번만 입력할 수 있어요.");
                                    f.stx.select();
                                    f.stx.focus();
                                    return false;
                                }
                                f.stx.value = stx;
                                return true;
                            }
                            
                            $(document).ready(function() {
                                var isSearchBoxVisible = false;
                                $('#search_top_btn').click(function(event) {
                                    event.stopPropagation();
                                    isSearchBoxVisible = !isSearchBoxVisible;
                                    if (isSearchBoxVisible) {
                                        $('#search_box_wrap').show();
                                        $('#search_top_btn').addClass('ser_open');
                                        $('#ser_inp_fc').focus();
                                    } else {
                                        $('#search_box_wrap').hide();
                                        $('#search_top_btn').removeClass('ser_open');
                                    }
                                });
                                $(document).click(function() {
                                    if (isSearchBoxVisible) {
                                        $('#search_box_wrap').hide();
                                        $('#search_top_btn').removeClass('ser_open');
                                        isSearchBoxVisible = false;
                                    }
                                });
                                $('#search_box_wrap').click(function(event) {
                                    event.stopPropagation();
                                });
                                $('.ser_label').click(function() {
                                    var dataKey = $(this).attr('data-key');
                                    $('#ser_inp_fc').val(dataKey);
                                });
                            });
                        </script>
                        
                        <div class="cb"></div>
                    </li>
                    <li class="member_info_wrap"  style="display:none !important;">
                        <?php if($is_member) { ?>
                        <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php" class="font-B notranslate"><?php echo $member['mb_nick'] ?></a>　
                        <?php } ?>
                    </li>
                    <li class="my_btn_wrap">
                        <?php if($is_member) { ?>
                            <button type="button" alt="로그아웃" class="btn_round" onclick="location.href='<?php echo G5_BBS_URL ?>/logout.php';">로그아웃</button>
                            <button type="button" alt="마이페이지" class="btn_round arr_bg font-B" onclick="location.href='<?php echo G5_URL; ?>/rb/home.php?mb_id=<?php echo $member['mb_id']; ?>';">My</button>
                        <?php } else { ?>
                            <button type="button" alt="로그인" class="btn_round"  onclick="location.href='<?php echo G5_BBS_URL ?>/login.php?url=<?php echo urlencode(getCurrentUrl()); ?>';">로그인</button>
                            <button type="button" alt="회원가입" class="btn_round arr_bg font-B" onclick="location.href='<?php echo G5_BBS_URL ?>/register.php';">회원가입</button>
                        <?php } ?>
                    </li>
                    
                    <div class="cb"></li>
                </ul>
                <div class="cb"></div>
            </div>
        </div>
        </header>
    <?php if (!defined('_INDEX_')) { 
        
        // ▼▼▼▼▼ [설정 영역] 페이지별 유튜브 영상 ID 설정 ▼▼▼▼▼
        // '게시판ID 또는 페이지ID' => '유튜브영상ID' 형식으로 작성
        $page_video_map = array(
            'free'    => 'jF3cfp6UKz4', 
            'notice'  => 'Wc2UN3yL2CU', 
            'gallery' => 'YLeJ9QRaM7I', 
            'company' => 'jF3cfp6UKz4', 
            'commercial' => 'IIV7STOoH6k', 
        );

        // 기본 영상 ID (설정되지 않은 페이지에서 나올 영상)
        $default_video_id = "Wc2UN3yL2CU"; 
        
        // ▲▲▲▲▲ [설정 끝] ▲▲▲▲▲


        // 현재 페이지의 영상 ID 결정 로직
        $sub_youtube_id = $default_video_id; // 기본값 설정

        if (isset($bo_table) && $bo_table && array_key_exists($bo_table, $page_video_map)) {
            // 게시판인 경우
            $sub_youtube_id = $page_video_map[$bo_table];
        } elseif (isset($co_id) && $co_id && array_key_exists($co_id, $page_video_map)) {
            // 내용 페이지인 경우
            $sub_youtube_id = $page_video_map[$co_id];
        }
        
        // 고유 플레이어 ID 생성
        $sub_player_unique_id = "sub_player_" . uniqid();
    ?>

        <div id="main_visual_bg">
            
            <div class="sub_video_bg">
                <div class="sub_video_fg">
                    <div id="<?php echo $sub_player_unique_id; ?>"></div>
                </div>
            </div>

            <!--<div class="sub_visual_content">
                <h2><?php echo get_head_title($g5['title']); ?></h2>
            </div>-->

        </div>

        <?php if(!defined('G5_YOUTUBE_API_LOADED')) { define('G5_YOUTUBE_API_LOADED', true); ?>
        <script src="https://www.youtube.com/iframe_api"></script>
        <?php } ?>

        <script>
            var subPlayer;
            
            // API가 준비되면 실행 (이미 로드되어 있을 수 있으므로 체크)
            if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
                window.onYouTubeIframeAPIReady = function() {
                    loadSubVideo();
                };
            } else {
                $(document).ready(function(){
                    loadSubVideo();
                });
            }

            function loadSubVideo() {
                subPlayer = new YT.Player('<?php echo $sub_player_unique_id; ?>', {
                    videoId: '<?php echo $sub_youtube_id; ?>',
                    playerVars: { 
                        'autoplay': 1, 'controls': 0, 'showinfo': 0, 'rel': 0, 
                        'loop': 1, 'playlist': '<?php echo $sub_youtube_id; ?>', 
                        'playsinline': 1, 'mute': 1 
                    },
                    events: {
                        'onReady': function(e) { 
                            e.target.mute(); 
                            e.target.setPlaybackRate(0.8); // 속도 조절
                            e.target.playVideo(); 
                        },
                        'onStateChange': function(e) { if (e.data === YT.PlayerState.ENDED) { e.target.playVideo(); } }
                    }
                });
            }
        </script>

    <?php } ?>
    <script>
    $(document).ready(function() {
        $(window).on('scroll', function() {
            // 스크롤이 10px 이상 내려오면 scrolled 클래스 추가
            if ($(this).scrollTop() > 10) {
                $('#header').addClass('scrolled');
            } else {
                $('#header').removeClass('scrolled');
            }
        });
    });
</script>