<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?ver='.G5_TIME_YMDHIS.'">', 0);
add_stylesheet('<link rel="stylesheet" href="'.G5_THEME_URL.'/rb.css/style.shop.css?ver='.G5_TIME_YMDHIS.'">', 0);
add_javascript('<script src="'.G5_URL.'/rb/rb.mod/partner/partner.js"></script>', 0);

$thumb_width = 120;
$thumb_height = 120;

$main = isset($_GET['main']) ? $_GET['main'] : '';
$sub = isset($_GET['sub']) ? $_GET['sub'] : '';

//예치금 사용여부 판단
$table_rb_point_c_set = sql_query("DESCRIBE rb_point_c_set", false);


$partner_id = $member['mb_id'];

//최근 7일 정산내역
$dates = [];
$sales = [];

for ($i = 6; $i >= 0; $i--) {
    $dates[] = date('Y-m-d', strtotime("-{$i} days"));
    $sales[date('Y-m-d', strtotime("-{$i} days"))] = 0;
}

// 데이터 조회
$sql = "SELECT DATE(ct_js_time) as dt, SUM(ct_js_price) as total
        FROM {$g5['g5_shop_cart_table']}
        WHERE ct_status = '완료'
          AND ct_partner = '{$partner_id}'
          AND DATE(ct_js_time) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY DATE(ct_js_time)";
$result = sql_query($sql);

// 결과 반영
while ($row = sql_fetch_array($result)) {
    $key = $row['dt'];
    if (isset($sales[$key])) {
        $sales[$key] = (int)$row['total'];
    }
}



//주문내역 최근
$status_names = ['주문' => 0, '입금' => 0, '준비' => 0, '배송' => 0, '완료' => 0, '취소' => 0];
$status_map = [
    '주문' => ['주문'],
    '입금' => ['입금'],
    '준비' => ['준비'],
    '배송' => ['배송'],
    '완료' => ['완료'],
    '취소' => ['취소']
];

// 오늘 주문 상태별 카운트
$sql2 = "SELECT a.od_status
        FROM {$g5['g5_shop_order_table']} a
        WHERE DATE(a.od_time) = CURDATE()
        AND a.od_id IN (
            SELECT DISTINCT od_id
            FROM {$g5['g5_shop_cart_table']}
            WHERE ct_partner = '{$partner_id}'
        )";
$res = sql_query($sql2);


$total_order_count = 0;
while ($row = sql_fetch_array($res)) {
    $od_status = trim($row['od_status']);
    $total_order_count++;

    foreach ($status_map as $label => $codes) {
        if (in_array($od_status, $codes)) {
            $status_names[$label]++;
            break;
        }
    }
}
?>

<div class="rb_prof_tab rb_prof_partner">


    <div>

        <nav id="bo_cate" class="swiper-container swiper-container-category">
            <ul id="bo_cate_ul" class="swiper-wrapper swiper-wrapper-category">
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/partner.php" <?php if($main == "") { ?>id="bo_cate_on" <?php } ?>>홈</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/partner.php?main=item&sub=itemlist" <?php if($main == "item") { ?>id="bo_cate_on" <?php } ?>>상품관리</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/partner.php?main=order&sub=orderlist" <?php if($main == "order") { ?>id="bo_cate_on" <?php } ?>>주문관리</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/partner.php?main=qa&sub=qalist" <?php if($main == "qa") { ?>id="bo_cate_on" <?php } ?>>상품문의</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/partner.php?main=review&sub=uselist" <?php if($main == "review") { ?>id="bo_cate_on" <?php } ?>>사용후기</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL ?>/rb/partner.php?main=amount" <?php if($main == "amount") { ?>id="bo_cate_on" <?php } ?>>정산내역</a></li>

                <?php if ($table_rb_point_c_set) { ?>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL; ?>/rb/point_c.php" target="_blank" class="win_point">예치금내역</a></li>
                <li class="swiper-slide swiper-slide-category"><a href="<?php echo G5_URL; ?>/rb/point_c.php?types=acc" target="_blank" class="win_point">출금신청</a></li>
                <?php } ?>

            </ul>
        </nav>

        <script>
            $(document).ready(function() {
                $("#bo_cate_ul li").addClass("swiper-slide swiper-slide-category");
            });

            // class="act"를 가진 요소를 찾기
            var activeElement = document.querySelector('.swiper-slide-category a#bo_cate_on');

            // 초기 슬라이드 인덱스를 담을 변수
            var initialSlideIndex = 0;

            if (activeElement) {
                // 부모 li 태그를 가져옴
                var parentLi = activeElement.closest('.swiper-slide-category');

                // 모든 슬라이드 요소들을 가져옴
                var allSlides = document.querySelectorAll('.swiper-slide-category');

                // 부모 li 태그의 인덱스를 계산
                initialSlideIndex = Array.prototype.indexOf.call(allSlides, parentLi);
            }

            var swiper = new Swiper('.swiper-container-category', {
                slidesPerView: 'auto', //가로갯수
                spaceBetween: 0, // 간격
                //slidesOffsetBefore: 40, //좌측여백
                //slidesOffsetAfter: 40, // 우측여백
                observer: true, //리셋
                observeParents: true, //리셋
                touchRatio: 1, // 드래그 가능여부
                initialSlide: initialSlideIndex, // 초기 슬라이드 인덱스 설정

            });
        </script>


    </div>

    <?php if($main == "") { ?>
    <div>
        <ul class="cont_info_wrap">
            <li class="cont_info_wrap_l">
                <dd>닉네임</dd>
                <dd><?php echo $mb['mb_nick'] ?>
                    <!--<span>@<?php echo $mb['mb_id'] ?></span>-->
                </dd>
            </li>
            <li class="cont_info_wrap_r">
                <dd>회원레벨</dd>
                <dd><?php echo $mb['mb_level'] ?>레벨</dd>
            </li>
            <div class="cb"></div>
        </ul>
        <ul class="cont_info_wrap">

            <?php if ($table_rb_point_c_set) { ?>
            <li class="cont_info_wrap_l">
                <dd><?php echo $pnt_c_name ?></dd>
                <dd>
                    <a href="<?php echo G5_URL; ?>/rb/point_c.php" target="_blank" class="win_point"><?php echo number_format($member['rb_point']); ?><?php echo $pnt_c_name_st ?></a>
                </dd>
            </li>
            <?php } else { ?>
            <li class="cont_info_wrap_l">
                <dd>포인트</dd>
                <dd>
                    <a href="<?php echo G5_BBS_URL ?>/point.php" target="_blank" class="win_point"><?php echo number_format($member['mb_point']); ?>P</a>
                </dd>
            </li>
            <?php } ?>


            <li class="cont_info_wrap_r">
                <dd>정산계좌</dd>
                <dd><?php echo ($member['mb_bank'] ? $member['mb_bank'] : '미등록'); ?></dd>
            </li>
            <div class="cb"></div>
        </ul>
        <ul class="cont_info_wrap">
            <li class="cont_info_wrap_l">
                <dd>판매수수료</dd>
                <dd>
                    <?php 
                    if(isset($pa['pa_ssr2']) && $pa['pa_ssr2'] > 0) { 
                        echo number_format($pa['pa_ssr2'])."원 (판매대금-".number_format($pa['pa_ssr2'])."원)";
                    } else { 
                        echo ($pa['pa_ssr'] ? $pa['pa_ssr'] : '0')."% (판매대금의 ".number_format($pa['pa_ssr'])."%)";
                    }
                    ?>
                </dd>
            </li>
            <li class="cont_info_wrap_r">
                <dd>출금가능일</dd>
                <dd>
                    <?php 
                        if(isset($pa['pa_day']) && $pa['pa_day'] > 0) { 
                            echo "정산 완료일로부터 ".number_format($pa['pa_day'])."일";
                        } else { 
                            echo "즉시출금 가능";
                        }
                        ?>
                </dd>
            </li>
            <div class="cb"></div>
        </ul>
    </div>

    <style>
        .rb_chart {
            border-radius: 10px;
            background-color: #fff;
            margin-top: 20px;
            padding: 0px;
        }
        
        .rb_chart_div1 {}
        .rb_chart_div2 {float:left; width: 48%;}
        .rb_chart_div3 {float:right; width: 48%;}

        .rb_chart h2 {
            padding: 0px 0px;
            margin-top: 0px;
            padding-top: 20px;
        }

        .apexcharts-text tspan {
            font-family: "font-R";
        }
        .apexcharts-xaxistooltip-text {font-size: 11px !important;}

        @media all and (max-width:1024px) {
            .rb_chart_div2 {width: 100%; float:none;}
            .rb_chart_div3 {width: 100%; float:none;}
            .rb_chart {margin-top:0px;}
            .rb_prof_partner .tbl_wrap {overflow-x: auto;}
            .rb_prof_partner .tbl_wrap table {width: 100%}
            .rb_prof_partner .tbl_head01 {margin-top: 0px;}
        }

    </style>
    <div>
        
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <div class="rb_chart rb_chart_div1 mt-20">
            <li class="bbs_main_wrap_tit_l">
                <h2 class="font-B font-18">최근 7일 정산내역</h2>
            </li>
            <li class="bbs_main_wrap_tit_r mt-20">
                <button type="button" class="more_btn" onclick="location.href='<?php echo G5_URL ?>/rb/partner.php?main=amount';">더보기</button>
            </li>
            <div class="cb"></div>
            <div id="rb-chart1" class="font-R" ></div>
        </div>
        
        <div class="cb"></div>
        
        <div class="rb_chart rb_chart_div2">
            <li class="bbs_main_wrap_tit_l">
                <h2 class="font-B font-18">오늘 주문현황 <span class="main_color"><?php echo number_format($total_order_count) ?>건</span></h2>
            </li>
            <li class="bbs_main_wrap_tit_r mt-20">
                <button type="button" class="more_btn" onclick="location.href='<?php echo G5_URL ?>/rb/partner.php?main=order&sub=orderlist';">더보기</button>
            </li>
            <div class="cb"></div>
            <div id="rb-chart2" class="font-R"></div>
        </div>
        
        <div class="rb_chart rb_chart_div3">
            <li class="bbs_main_wrap_tit_l">
                <h2 class="font-B font-18">상품문의</h2>
            </li>
            <li class="bbs_main_wrap_tit_r mt-20">
                <button type="button" class="more_btn" onclick="location.href='<?php echo G5_URL ?>/rb/partner.php?main=qa&sub=qalist';">더보기</button>
            </li>
            <div class="cb"></div>
            <?php 
            $main_qa = "true";
            include_once(G5_PATH.'/rb/rb.mod/partner/itemqalist.php'); 
            ?>
        </div>
        
        
        <div class="cb"></div>
        
        <script>
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    const dates = <?= json_encode(array_values($dates)) ?>;
    const sales = <?= json_encode(array_values($sales)) ?>;

    const options1 = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        series: [{
            name: '',
            data: sales
        }],
        xaxis: {
            categories: dates,
            labels: {
                style: { fontSize: '11px', colors: '#000' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#000' },
                formatter: function(val) {
                    return numberWithCommas(val);
                }
            }
        },
        stroke: {
            width: 2,
            curve: 'smooth',
            colors: ['<?= isset($rb_config['co_color']) ? $rb_config['co_color'] : '#aa20ff' ?>'],
        },
        markers: {
            size: 0,
            hover: {
                size: 7,
                sizeOffset: 3,
            },
            colors: ['<?= isset($rb_config['co_color']) ? $rb_config['co_color'] : '#aa20ff' ?>'],
            strokeColors: '<?= isset($rb_config['co_color']) ? $rb_config['co_color'] : '#aa20ff' ?>',
            strokeWidth: 3
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        tooltip: {
            enabled: true,
            y: {
                formatter: val => numberWithCommas(val) + "원"
            },
            style: { fontSize: '12px' },
            marker: {
                show: true,
                fillColors: ['<?= isset($rb_config['co_color']) ? $rb_config['co_color'] : '#aa20ff' ?>']
            }
        },
        grid: {
            show: true,
            borderColor: '#e5e5ef',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        }
    };

    const chart1 = new ApexCharts(document.querySelector("#rb-chart1"), options1);
    chart1.render();
</script>

        <script>
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    const months = ['주문', '입금', '준비', '배송', '완료', '취소'];
    const values = <?= json_encode(array_values($status_names)) ?>;

    const options2 = {
        chart: {
            type: 'bar',
            height: 230,
            toolbar: { show: false }
        },
        series: [{
            name: '',
            data: values
        }],
        xaxis: {
            categories: months,
            labels: {
                style: { fontSize: '11px', colors: '#000' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#000' },
                formatter: val => numberWithCommas(val)
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '35%',
                distributed: true
            }
        },
        dataLabels: {
            enabled: true,
            position: 'top',
            offsetY: 0,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            },
            formatter: function(val) {
                return numberWithCommas(val) + "건";
            }
        },
        fill: {
            colors: ['<?= isset($rb_config['co_color']) ? $rb_config['co_color'] : '#aa20ff'; ?>']
        },
        states: {
            hover: {
                filter: {
                    type: 'opacity',
                    value: 0.8
                }
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: val => numberWithCommas(val) + "건"
            },
            style: {
                fontSize: '12px'
            }
        },
        grid: {
            show: true,
            borderColor: '#e5e5ef',
            strokeDashArray: 3,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        legend: {
            show: false
        }
    };

    const chart2 = new ApexCharts(document.querySelector("#rb-chart2"), options2);
    chart2.render();
</script>




    </div>
    <?php } ?>

    <?php 
        //상품관리
        if(isset($pa['pa_is']) && $pa['pa_is'] == 1) { 
            if($main == "item" && $sub == "itemlist") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/itemlist.php');
            }
            if($main == "item" && $sub == "itemform") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/itemform.php');
            }
            if($main == "order" && $sub == "orderlist") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/orderlist.php');
            }
            if($main == "order" && $sub == "orderform") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/orderform.php');
            }
            if($main == "qa" && $sub == "qalist") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/itemqalist.php');
            }
            if($main == "qa" && $sub == "qaform") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/itemqaform.php');
            }
            if($main == "review" && $sub == "uselist") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/itemuselist.php');
            }
            if($main == "review" && $sub == "useform") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/itemuseform.php');
            }
            if($main == "amount") { 
                include_once(G5_PATH.'/rb/rb.mod/partner/amount.php');
            }
        }
        ?>


</div>


<div class="cb"></div>