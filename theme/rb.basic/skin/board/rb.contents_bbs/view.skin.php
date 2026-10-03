<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);


//필드분할
$wr_1 = isset($view["wr_1"]) ? explode("|", $view["wr_1"]) : [];
$wr_2 = isset($view["wr_2"]) ? explode("|", $view["wr_2"]) : [];
$wr_3 = isset($view["wr_3"]) ? explode("|", $view["wr_3"]) : [];

?>
<style>
#scroll_container {margin-top: 20px;}
#scroll_container .rb_bbs_top{display: none;}
</style>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>


<div class="rb_bbs_wrap" style="width:<?php echo $width; ?>">
        
    <div class="btns_gr_wrap">
      
       <div class="sub" style="width:<?php echo $rb_core['sub_width'] ?>px;">
            
            <div class="btns_gr">
                <?php if ($admin_href) { ?>
                <button type="button" class="fl_btns" onclick="window.open('<?php echo $admin_href ?>');">
                <img src="<?php echo $board_skin_url ?>/img/ico_set.svg">
                <span class="tooltips">관리</span>
                </button>
                <?php } ?>
                
                <?php if ($scrap_href) { ?>
                <a class="fl_btns" href="<?php echo $scrap_href;  ?>" target="_blank" onclick="win_scrap(this.href); return false;">
                <img src="<?php echo $board_skin_url ?>/img/ico_scr.svg">
                <span class="tooltips">스크랩</span>
                </a>
                <?php } ?>
                
                <?php if ($list_href) { ?>
                <button type="button" class="fl_btns" onclick="location.href='<?php echo $list_href ?>';">
                <img src="<?php echo $board_skin_url ?>/img/ico_list.svg">
                <span class="tooltips">목록</span>
                </button>
                <?php } ?>

                
                <?php if ($write_href) { ?>
                <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                <img src="<?php echo $board_skin_url ?>/img/ico_write.svg">
                <span class="tooltips">글 등록</span>
                </button>
                <?php } ?>
                
            </div>
            
            <div class="cb"></div>
        </div>
    </div>
    
    
    <div class="bbs_sv_wrap">
        
        
        <ul class="bbs_sv_wrap_ul2">
            <div class="gap_btm_bd flex_top_cat">
                
                <?php if ($category_name) { ?>
                <li class="view_info_span"><?php echo $view['ca_name'] ?></li>
                <?php } ?>
                
                <li class="rb_bbs_for_mem_names">
                        <?php
                        $view['icon_new'] = "";
                        if ($view['wr_datetime'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - ($board['bo_new'] * 3600)))
                            $view['icon_new'] = "<span class=\"lb_ico_new\">신규</span>";
                        $view['icon_hot'] = "";
                        if ($board['bo_hot'] > 0 && $view['wr_hit'] >= $board['bo_hot'])
                            $view['icon_hot'] = "<span class=\"lb_ico_hot\">인기</span>";

                        echo $view['icon_new']; //뉴아이콘
                        echo $view['icon_hot']; //인기아이콘 
                        ?>
                </li>

                
                <div id="bo_v_share">
                        <?php include_once(G5_SNS_PATH."/view.sns.skin.php"); ?>
                        <ul class="copy_urls">
                            <li>
                                <a href="javascript:void(0);" id="data-copy">
                                   <img src="<?php echo $board_skin_url ?>/img/ico_sha.png" alt="공유링크 복사" width="32">
                                </a>
                            </li>
                            <?php
                            $currents_url = G5_URL.$_SERVER['REQUEST_URI'];
                            ?>
                            <input type="hidden" id="data-area" class="data-area" value="<?php echo $currents_url ?>">
                            <script>
                                $(document).ready(function() {

                                    $('#data-copy').click(function() {
                                        $('#data-area').attr('type', 'text'); // 화면에서 hidden 처리한 input box type을 text로 일시 변환
                                        $('#data-area').select(); // input에 담긴 데이터를 선택
                                        var copy = document.execCommand('copy'); // clipboard에 데이터 복사
                                        $('#data-area').attr('type', 'hidden'); // input box를 다시 hidden 처리
                                        if (copy) {
                                            alert("공유 링크가 복사 되었습니다."); // 사용자 알림
                                        }
                                    });

                                });
                            </script>
                        </ul>

                </div>
                
                
            </div>
            <div class="gap_btm_bd">

                <ul class="font-B view_info_tit"><?php echo get_text($view['wr_subject']);?></ul>
                
                <?php if(isset($view['wr_6']) && $view['wr_6']) { ?>
                <ul class="view_info_sub"><?php echo isset($view['wr_6']) ? get_text($view['wr_6'], true) : ''; ?></ul>
                <?php } ?>
                
                <?php if(isset($wr_1[0]) && $wr_1[0] || isset($wr_1[1]) && $wr_1[1]) { ?>
                <ul class="font-B view_info_pri1 main_color">
                    <?php if(isset($wr_1[0]) && $wr_1[0]) { ?>
                    <li><?php echo isset($wr_1[0]) ? get_text($wr_1[0]) : ''; ?></li>
                    <?php } ?>
                    <?php if(isset($wr_1[0]) && $wr_1[0]) { ?>
                    <li class="view_info_pri1 labels main_color font-B"><?php echo isset($wr_1[1]) ? get_text($wr_1[1]) : ''; ?></li>
                    <?php } ?>
                </ul>
                <?php } ?>
                
                
                <ul class="view_info_btns">
                    
                    <?php if ( $good_href || $nogood_href) { ?>
                    <div id="bo_v_act">
                        <?php if ($good_href) { ?>
                        <span class="bo_v_act_gng">
                            <a href="<?php if(!$is_member) { ?>javascript:alert('로그인 후 이용하실 수 있습니다.');<?php } else { ?><?php echo $good_href.'&amp;'.$qstr ?><?php } ?>" id="good_button" class="bo_v_good">
                            <svg width="18" height="18" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5722 1.54713C18.6672 2.76813 20.1412 5.24713 20.0762 8.13913C19.9952 11.7491 17.1862 14.9331 12.3972 17.7771C11.6872 18.1991 10.9392 18.7461 10.0782 18.7461C9.23319 18.7461 8.45319 18.1891 7.75819 17.7761C2.97119 14.9331 0.161193 11.7481 0.0801927 8.13913C0.0151927 5.24713 1.48919 2.76913 3.58419 1.54713C5.54419 0.406128 8.00619 0.399127 10.0782 2.08413C12.1502 0.399127 14.6122 0.405128 16.5722 1.54713ZM15.5652 3.27613C14.1712 2.46413 12.4292 2.49313 10.9212 4.01913C10.8108 4.13033 10.6794 4.21859 10.5348 4.27882C10.3901 4.33905 10.2349 4.37005 10.0782 4.37005C9.92148 4.37005 9.76631 4.33905 9.62163 4.27882C9.47695 4.21859 9.34562 4.13033 9.23519 4.01913C7.72719 2.49313 5.98519 2.46413 4.59119 3.27613C3.14719 4.11813 2.03119 5.90413 2.08019 8.09613C2.13619 10.6071 4.12019 13.2901 8.78019 16.0581C9.18819 16.3011 9.61419 16.6121 10.0782 16.7411C10.5422 16.6121 10.9682 16.3011 11.3762 16.0581C16.0362 13.2901 18.0202 10.6081 18.0762 8.09513C18.1262 5.90513 17.0092 4.11813 15.5652 3.27613Z" fill="#09244B"/>
                            </svg>
                             <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                            <b id="bo_v_act_good" class="font-R"></b>
                        </span>
                        <?php } ?>

                    </div>
                    <?php } else {
                        if($board['bo_use_good'] || $board['bo_use_nogood']) {
                    ?>
                    <div id="bo_v_act">
                        <?php if($board['bo_use_good']) { ?>
                            <span class="bo_v_act_gng">

                                <a href="<?php if(!$is_member) { ?>javascript:alert('로그인 후 이용하실 수 있습니다.');<?php } else { ?>javascript:void(0);<?php } ?>" class="bo_v_good">
                                <svg width="18" height="18" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5722 1.54713C18.6672 2.76813 20.1412 5.24713 20.0762 8.13913C19.9952 11.7491 17.1862 14.9331 12.3972 17.7771C11.6872 18.1991 10.9392 18.7461 10.0782 18.7461C9.23319 18.7461 8.45319 18.1891 7.75819 17.7761C2.97119 14.9331 0.161193 11.7481 0.0801927 8.13913C0.0151927 5.24713 1.48919 2.76913 3.58419 1.54713C5.54419 0.406128 8.00619 0.399127 10.0782 2.08413C12.1502 0.399127 14.6122 0.405128 16.5722 1.54713ZM15.5652 3.27613C14.1712 2.46413 12.4292 2.49313 10.9212 4.01913C10.8108 4.13033 10.6794 4.21859 10.5348 4.27882C10.3901 4.33905 10.2349 4.37005 10.0782 4.37005C9.92148 4.37005 9.76631 4.33905 9.62163 4.27882C9.47695 4.21859 9.34562 4.13033 9.23519 4.01913C7.72719 2.49313 5.98519 2.46413 4.59119 3.27613C3.14719 4.11813 2.03119 5.90413 2.08019 8.09613C2.13619 10.6071 4.12019 13.2901 8.78019 16.0581C9.18819 16.3011 9.61419 16.6121 10.0782 16.7411C10.5422 16.6121 10.9682 16.3011 11.3762 16.0581C16.0362 13.2901 18.0202 10.6081 18.0762 8.09513C18.1262 5.90513 17.0092 4.11813 15.5652 3.27613Z" fill="#09244B"/>
                                </svg>
                                <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                                <b id="bo_v_act_good" class="font-R"></b>
                            </span>
                        <?php } ?>

                    </div>
                    <?php
                            }
                        }
                    ?>
					<a href="/bbs/write.php?bo_table=qa" class="view_info_btns_btn1 font-R">
						예약문의
					</a>
                    <!--<?php if(isset($wr_2[0]) && $wr_2[0]) { ?>
                    <a href="<?php if(isset($wr_2[1]) && $wr_2[1]) { ?><?php echo isset($wr_2[1]) ? get_text($wr_2[1]) : ''; ?><?php } else { ?>javascript:void(0);<?php } ?>" class="view_info_btns_btn1 font-R" <?php if(isset($wr_2[2]) && $wr_2[2]) { ?>target="_blank"<?php } ?>>
                        
                        <?php if(isset($wr_2[0]) && $wr_2[0]) { ?>
                            <?php echo isset($wr_2[0]) ? get_text($wr_2[0]) : ''; ?>
                        <?php } ?>
                        
                    </a>
                    <?php } ?>-->
                </ul>
                
                
            </div>
            
            <?php if(isset($wr_3[0]) && $wr_3[0] || isset($wr_3[2]) && $wr_3[2] || isset($wr_3[4]) && $wr_3[4] || isset($wr_3[6]) && $wr_3[6] || isset($wr_3[8]) && $wr_3[8]) { ?>
            <div class="gap_btm_bd">
                
                <?php if(isset($wr_3[0]) && $wr_3[0]) { ?>
                <ul class="opt_box_wrap">
                    <li class="font-B"><?php echo isset($wr_3[0]) ? get_text($wr_3[0]) : ''; ?></li><li><?php echo isset($wr_3[1]) ? get_text($wr_3[1]) : ''; ?></li>
                </ul>
                <?php } ?>
                
                <?php if(isset($wr_3[2]) && $wr_3[2]) { ?>
                <ul class="opt_box_wrap">
                    <li class="font-B"><?php echo isset($wr_3[2]) ? get_text($wr_3[2]) : ''; ?></li><li><?php echo isset($wr_3[3]) ? get_text($wr_3[3]) : ''; ?></li>
                </ul>
                <?php } ?>
                
                <?php if(isset($wr_3[4]) && $wr_3[4]) { ?>
                <ul class="opt_box_wrap">
                    <li class="font-B"><?php echo isset($wr_3[4]) ? get_text($wr_3[4]) : ''; ?></li><li><?php echo isset($wr_3[5]) ? get_text($wr_3[5]) : ''; ?></li>
                </ul>
                <?php } ?>
                
                <?php if(isset($wr_3[6]) && $wr_3[6]) { ?>
                <ul class="opt_box_wrap">
                    <li class="font-B"><?php echo isset($wr_3[6]) ? get_text($wr_3[6]) : ''; ?></li><li><?php echo isset($wr_3[7]) ? get_text($wr_3[7]): ''; ?></li>
                </ul>
                <?php } ?>
                
                <?php if(isset($wr_3[8]) && $wr_3[8]) { ?>
                <ul class="opt_box_wrap">
                    <li class="font-B"><?php echo isset($wr_3[8]) ? get_text($wr_3[8]) : ''; ?></li><li><?php echo isset($wr_3[9]) ? get_text($wr_3[9]) : ''; ?></li>
                </ul>
                <?php } ?>
                
            </div>
            <?php } ?>
            
            <?php 
            if(isset($board['bo_use_signature']) && $board['bo_use_signature']) {
                // 서명 출력
                include_once(G5_PATH.'/rb/rb.mod/signature/signature.skin.php');
            } 
            ?>

                
                <?php
                $cnt = 0;
                if ($view['file']['count']) {
                    for ($i=0; $i<count($view['file']); $i++) {
                        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                        //if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'])
                            $cnt++;
                    }
                }

                ?>

                <?php if($cnt) { ?>
                
                <div class="down_link_wrap">
                <h2 id="container_title" class="none_btm_pd font-R">다운로드</h2>

                    <div class="rb_bbs_file">
                        <?php
                        // 가변 파일
                        for ($i=0; $i<count($view['file']); $i++) {
                            if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
                            //if (isset($view['file'][$i]['source']) && $view['file'][$i]['source']) {
                        ?>
                        <ul class="rb_bbs_file_for view_file_download" onclick="location.href='<?php echo $view['file'][$i]['href']; ?>';">
                            <i><img src="<?php echo $board_skin_url ?>/img/ico_file.svg"></i>
                            <a href="javascript:void(0);"><?php echo $view['file'][$i]['source'] ?></a> <?php echo number_format($view['file'][$i]['download']); ?>회
                            <?php if($view['file'][$i]['content']) { ?>
                            <li class="file_contents"><?php echo $view['file'][$i]['content'] ?></li>
                            <?php } ?>
                        </ul>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    
                </div>
                <?php } ?>



                <?php if(isset($view['link']) && array_filter($view['link'])) { ?>
                <div class="down_link_wrap">
                <h2 id="container_title" class="none_btm_pd font-R">링크</h2>
                    <div class="rb_bbs_file">
                        <?php
                        // 링크
                        $cnt = 0;
                        for ($i=1; $i<=count($view['link']); $i++) {
                            if ($view['link'][$i]) {
                                $cnt++;
                                $link = cut_str($view['link'][$i], 70);
                        ?>
                        <ul class="rb_bbs_file_for" onclick="window.open('<?php echo $view['link_href'][$i]; ?>');">
                            <i><img src="<?php echo $board_skin_url ?>/img/ico_link.svg"></i>
                            <a href="javascript:void(0);"><?php echo $link ?></a>　<?php echo $view['link_hit'][$i] ?>회
                        </ul>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>
                
            
            
            
            
        </ul>
        
        
        
        
        <ul class="bbs_sv_wrap_ul1">
            <div>
                <?php 
                $v_img_count = count($view['file']);
                if ($v_img_count > 0) {
                    echo "<div class=\"swiper-container swiper-container-pf\"><ul class=\"swiper-wrapper swiper-wrapper-pf\">\n";
                    for ($i = 0; $i < $v_img_count; $i++) {
                        // Check if $view['file'][$i]['view'] exists and is not empty
                        if (isset($view['file'][$i]['view']) && $view['file'][$i]['view']) {
                            echo "<li class=\"swiper-slide swiper-slide-pf\">" . get_view_thumbnail($view['file'][$i]['view']) . "</li>";
                        }
                    }
                    echo "</ul><div class=\"swiper-pagination swiper-pagination-pf\"></div></div>\n";
                ?>

                <script>
                    var swiper = new Swiper('.swiper-container-pf', {
                        slidesPerView: 1, //가로갯수
                        spaceBetween: 0, // 간격
                        observer: true, //리셋
                        observeParents: true, //리셋
                        touchRatio: 1, // 드래그 가능여부
                        autoHeight: true,

                        autoplay: { //오토플레이
                            delay: 3000, //시간
                            disableOnInteraction: false, //마우스온여부
                        },

                        pagination: {
                            el: '.swiper-pagination-pf',
                            dynamicBullets: true,
                            clickable: true,
                        }

                    });
                </script>

                    <?php
                    }

                ?>
            </div>
            
            <?php if(isset($view['wr_5']) && $view['wr_5']) { ?>
            <div class="at_cont">
                <?php echo isset($view['wr_5']) ? get_text($view['wr_5'], true) : ''; ?>
            </div>
            <?php } ?>




            <div id="bo_v_con">
            
            <?php
			// 여분필드 wr_1에 내용관리 ID가 저장되어 있는 경우
			if ($view['wr_10']) {
				$content_id = $view['wr_10'];
				
				// 내용관리 테이블에서 해당 내용 가져오기
				$sql = "SELECT co_subject, co_content 
						FROM {$g5['content_table']} 
						WHERE co_id = '{$content_id}'";
				
				$content_result = sql_fetch($sql);
				
				if ($content_result) {
			?>
			<div id="attached_content" class="price_set">
				<div class="price_dec">
					<?php 
					// HTML 태그 허용하여 출력 (내용관리는 보통 HTML 에디터로 작성됨)
					echo conv_content($content_result['co_content'], 1); 
					?>
				</div>
			</div>
			<?php
				}
			}
			?>
			<div class="price_bn">
				<img src="<?php echo $board_skin_url ?>/img/sky_price_bn.png" />
			</div>

			<?php if(isset($view['wr_7']) && $view['wr_7']) { ?>
   
			<?php
				$input = get_text($view['wr_7']); // 동영상 ID 자체 혹은 URL
				$videoId = getYouTubeVideoId($input);
			?>

				<div class="youtube_wrap">

					<div class="youtube">
						<iframe src="https://www.youtube.com/embed/<?php echo $videoId; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>

					<?php if (strlen($view['wr_7']) == 11) { // 아이디만 입력했을 경우 ?>
						<a href="https://youtu.be/<?php echo get_text($view['wr_1']); ?>" class="urls font-B" target="_blank">https://youtu.be/<span class="main_color"><?php echo get_text($view['wr_1']); ?></span></a>
					<?php } else { ?>
						<a href="<?php echo get_text($view['wr_7']); ?>" class="urls font-B" target="_blank"><?php echo get_text($view['wr_7']); ?></a>
					<?php } ?>

				</div>
				<?php } ?>
                
                
                </div>
            
            
            <div>
                
                
                <ul class="btm_btns">


                    <dd class="btm_btns_right">

                        <?php if ($list_href) { ?>
                        <a href="<?php echo $list_href ?>" type="button" class="fl_btns font-B">목록</a>
                        <?php } ?>


                        <?php if ($scrap_href) { ?>
                        <a href="<?php echo $scrap_href;  ?>" class="fl_btns font-B" target="_blank" onclick="win_scrap(this.href); return false;">스크랩</a>
                        <?php } ?>

                        <?php if ($write_href) { ?>
                        <button type="button" name="btn_submit" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                            <img src="<?php echo $board_skin_url ?>/img/ico_write.svg">
                            <span class="font-R">글 등록</span>
                        </button>
                        <?php } ?>

                        <div class="cb"></div>

                    </dd>

                    <div id="bo_v_btns">
                        <?php ob_start(); ?>

                        <?php if($update_href || $delete_href || $copy_href || $move_href || $search_href) { ?>

                            <?php if ($reply_href) { ?>
                            <?php } ?>

                            <?php if ($update_href) { ?>
                            <a href="<?php echo $update_href ?>" class="fl_btns">
                            <span class="font-B">수정</span>
                            </a>
                            <?php } ?>

                            <?php if ($copy_href) { ?>
                            <a href="<?php echo $copy_href ?>" onclick="board_move(this.href); return false;" class="fl_btns">
                            <span class="font-B">복사</span>
                            </a>
                            <?php } ?>

                            <?php if ($move_href) { ?>
                            <a href="<?php echo $move_href ?>" onclick="board_move(this.href); return false;" class="fl_btns">
                            <span class="font-B">이동</span>
                            </a>
                            <?php } ?>

                            <?php if ($delete_href) { ?>
                            <a href="<?php echo $delete_href ?>" onclick="del(this.href); return false;" class="fl_btns">
                            <span class="font-B">삭제</span>
                            </a>
                            <?php } ?>

                        <?php } ?>

                        <?php
                        $link_buttons = ob_get_contents();
                        ob_end_flush();
                        ?>

                    </div>


                    <dd class="cb"></dd>


                </ul>

                </div>
        </ul>
        
        <div class="cb"></div>
    </div>
    
    
    
    

</div>






<script>
<?php if ($board['bo_download_point'] < 0) { ?>
$(function() {
    $("a.view_file_download").click(function() {
        if(!g5_is_member) {
            alert("다운로드 권한이 없습니다.\n회원이시라면 로그인 후 이용해 보십시오.");
            return false;
        }

        var msg = "파일을 다운로드 하시면 포인트가 차감(<?php echo number_format($board['bo_download_point']) ?>점)됩니다.\n\n포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.\n\n그래도 다운로드 하시겠습니까?";

        if(confirm(msg)) {
            var href = $(this).attr("href")+"&js=on";
            $(this).attr("href", href);

            return true;
        } else {
            return false;
        }
    });
});
<?php } ?>

function board_move(href)
{
    window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
}
</script>

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 추천, 비추천
    $("#good_button, #nogood_button").click(function() {
        var $tx;
        if(this.id == "good_button")
            $tx = $("#bo_v_act_good");
        else
            $tx = $("#bo_v_act_nogood");

        excute_good(this.href, $(this), $tx);
        return false;
    });

    // 이미지 리사이즈
    $("#bo_v_atc").viewimageresize();
});

function excute_good(href, $el, $tx)
{
    $.post(
        href,
        { js: "on" },
        function(data) {
            if(data.error) {
                alert(data.error);
                return false;
            }

            if(data.count) {
                $el.find("strong").text(number_format(String(data.count)));
                if($tx.attr("id").search("nogood") > -1) {
                    $tx.text("이 글을 비추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                } else {
                    $tx.text("이 글을 추천하셨습니다.");
                    $tx.fadeIn(200).delay(2500).fadeOut(200);
                }
            }
        }, "json"
    );
}
</script>
<script>
    $(window).on('load', function() {
        setTimeout(function() {
            var $target = $(".rb_bbs_wrap"); // 이동할 목표 지점 (게시판 목록 시작)
            var headerH = $("#header").outerHeight() || 0; // 헤더 높이 계산 (fixed 헤더 고려)
            
            // 목표 위치 계산 (헤더 높이와 약간의 여백 30px을 뺌)
            var targetPos = $target.offset().top - headerH - 30;

            // 이동 위치가 0보다 작으면 0으로 고정
            if (targetPos < 0) targetPos = 0;

            // 부드러운 스크롤 애니메이션 실행 (1초 동안)
            $('html, body').animate({ scrollTop: targetPos }, 500, 'swing');
        }, 400); // 0.8초 대기 후 실행
    });
</script>