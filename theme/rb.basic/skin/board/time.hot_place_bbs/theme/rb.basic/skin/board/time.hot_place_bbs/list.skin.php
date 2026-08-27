<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// var_dump($list);
// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?ver='.G5_TIME_YMDHIS.'">', 0);

// 1차 탭(전국 및 도/광역시/특별시)과 2차 탭(시/구) 데이터를 정의
$regions = [
        "서울" => [
            "중구", "용산구", "성동구",
            "광진구", "동대문구", "중랑구", "성북구",
            "강북구", "도봉구", "노원구", "은평구",
            "서초구", "강남구", "송파구", "강동구",
            "양천구", "구로구", "금천구", "영등포구",
            "관악구", "마포구", "종로구"
        ],
        "경기" => [
            "수원시 장안구", "수원시 권선구", "수원시 팔달구", "수원시 영통구",
            "성남시 수정구", "성남시 중원구", "성남시 분당구",
            "의정부시",
            "안양시 만안구", "안양시 동안구",
            "부천시 원미구", "부천시 오정구", "부천시 소사구",
            "광명시",
            "평택시",
            "동두천시",
            "안산시 상록구", "안산시 단원구",
            "고양시 덕양구", "고양시 일산동구", "고양시 일산서구",
            "과천시",
            "구리시",
            "남양주시",
            "오산시",
            "시흥시",
            "군포시",
            "의왕시",
            "하남시",
            "용인시 처인구", "용인시 기흥구", "용인시 수지구",
            "파주시",
            "이천시",
            "안성시",
            "김포시",
            "화성시",
            "광주시",
            "양주시",
            "포천시",
            "여주시",
            "연천군",
            "가평군",
            "양평군"
        ],
        "부산" => [
            "중구", "동구", "서구", "영도구", "부산진구",
            "동래구", "남구", "북구", "강서구", "해운대구",
            "사하구", "금정구", "연제구", "수영구",
            "사상구", "기장군"
        ],
        "대구" => [
            "중구", "동구", "서구", "남구",
            "북구", "수성구", "달서구", "달성군"
        ],
        "인천" => [
            "중구", "동구", "미추홀구", "연수구",
            "남동구", "부평구", "계양구", "서구",
            "강화군", "옹진군"
        ],
        "광주" => [
            "동구", "서구", "남구", "북구", "광산구"
        ],
        "대전" => [
            "동구", "중구", "서구", "유성구", "대덕구"
        ],
        "울산" => [
            "중구", "동구", "서구", "남구", "북구", "울주군"
        ],
        "세종" => ["세종시"],
        "강원" => [
            "춘천시", "원주시", "강릉시", "동해시", "태백시", "속초시",
            "삼척시", "홍천군", "횡성군", "영월군", "평창군",
            "정선군", "철원군", "화천군", "양구군", "인제군",
            "고성군", "양양군"
        ],
        "충남" => [
            "천안시 동남구", "천안시 서북구",
            "공주시", "보령시", "아산시", "서산시", "논산시",
            "계룡시", "당진시", "금산군", "부여군", "서천군",
            "청양군", "홍성군", "예산군", "태안군"
        ],
        "충북" => [
            "청주시 상당구", "청주시 흥덕구", "청주시 서원구", "청주시 청원구",
            "충주시", "제천시", "보은군", "옥천군",
            "영동군", "진천군", "괴산군", "음성군", "단양군"
        ],
        "경남" => [
            "창원시 의창구", "창원시 성산구", "창원시 진해구", "창원시 마산합포구", "창원시 마산회원구",
            "진주시", "통영시", "사천시", "김해시",
            "밀양시", "거제시", "양산시", "의령군", "함안군",
            "창녕군", "고성군", "남해군", "하동군", "산청군",
            "함양군", "거창군", "합천군"
        ],
        "경북" => [
            "포항시 북구", "포항시 남구",
            "경주시", "김천시", "안동시", "구미시",
            "영주시", "영천시", "상주시", "문경시",
            "경산시", "군위군", "의성군", "청송군",
            "영양군", "영덕군", "청도군", "고령군",
            "성주군", "칠곡군", "예천군", "봉화군",
            "울진군", "울릉군"
        ],
        "전남" => [
            "목포시", "여수시", "순천시", "나주시", "광양시",
            "담양군", "곡성군", "구례군", "고흥군", "보성군",
            "화순군", "장흥군", "강진군", "해남군", "영암군",
            "무안군", "함평군", "영광군", "장성군", "완도군",
            "진도군", "신안군"
        ],
        "전북" => [
            "전주시 완산구", "전주시 덕진구",
            "군산시", "익산시", "정읍시", "남원시",
            "김제시", "완주군", "진안군", "무주군",
            "장수군", "임실군", "순창군", "고창군", "부안군"
        ],
        "제주" => ["제주시", "서귀포시"]
    ];
    


// 현재 선택된 지역 및 서브 지역
$loca = isset($_GET['loca']) ? $_GET['loca'] : '';
$sub_loca = isset($_GET['sub_loca']) ? $_GET['sub_loca'] : '';

// 카테고리 필터링 변수 정의
$ca = isset($_GET['sca']) ? str_replace("/", "", $_GET['sca']) : '';

// 2차 탭 목록 가져오기
$selected_province = $loca;
$sub_regions = isset($regions[$selected_province]) ? $regions[$selected_province] : [];

// 지도 좌표 설정
if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) {

    // 1차 선택에 따른 기본 좌표 설정
    $province_coords = [
        "서울" => [37.5665, 126.9780],
        "부산" => [35.1796, 129.0756],
        "대구" => [35.8714, 128.6014],
        "인천" => [37.4563, 126.7052],
        "광주" => [35.1595, 126.8526],
        "대전" => [36.3504, 127.3845],
        "울산" => [35.5384, 129.3114],
        "세종" => [36.4803, 127.2898],
        "경기" => [37.274666, 127.009620],
        "강원" => [37.8228, 128.1555],
        "충남" => [36.8151, 127.1135],
        "충북" => [36.6424, 127.4890],
        "경남" => [35.2289, 128.6811],
        "경북" => [36.0194, 129.3435],
        "전남" => [34.8675, 126.9910],
        "전북" => [35.8198, 127.1468],
        "제주" => [33.4996, 126.5312]
    ];

    // 기본 좌표 (전체지역을 선택했을 때)
    $lat = 36.5; // 대한민국 중앙 위도
    $lng = 127.8; // 대한민국 중앙 경도
    $map_level = 13; // 전체 지도 보기에는 적당한 레벨 설정

    if(!empty($selected_province) && isset($province_coords[$selected_province])) {
        $lat = $province_coords[$selected_province][0];
        $lng = $province_coords[$selected_province][1];
        $map_level = 5; // 도 선택 시 지도 레벨 5로 설정
    }

    // 2차 선택에 따른 좌표 재설정
    if(!empty($sub_loca)) {
        // 각 시/구에 대한 좌표를 정의합니다. 중첩 배열로 수정되었습니다.
        $city_coords = [
            "서울" => [
                "중구" => [37.564235, 126.997466],
                "용산구" => [37.5326, 126.9900],
                "성동구" => [37.5637, 127.0373],
                "광진구" => [37.5384, 127.0827],
                "동대문구" => [37.5799, 127.0374],
                "중랑구" => [37.6063, 127.0928],
                "성북구" => [37.5895, 127.0169],
                "강북구" => [37.6391, 127.0256],
                "도봉구" => [37.6686, 127.0364],
                "노원구" => [37.6545, 127.0778],
                "은평구" => [37.6173, 126.9227],
                "서초구" => [37.4833, 127.0410],
                "강남구" => [37.4979, 127.0276],
                "송파구" => [37.5145, 127.1052],
                "강동구" => [37.5300, 127.1237],
                "양천구" => [37.5172, 126.8808],
                "구로구" => [37.4955, 126.8878],
                "금천구" => [37.4563, 126.8959],
                "영등포구" => [37.5263, 126.9006],
                "관악구" => [37.4783, 126.9510],
                "마포구" => [37.5663, 126.9086],
                "종로구" => [37.5720, 126.9794],
            ],

            "부산" => [
                "중구" => [35.1028, 129.0403],
                "동구" => [35.1631, 129.0401],
                "서구" => [35.0992, 128.9680],
                "영도구" => [35.1005, 129.0681],
                "부산진구" => [35.1584, 129.0595],
                "동래구" => [35.2004, 129.0735],
                "남구" => [35.1418, 129.0901],
                "북구" => [35.2259, 129.0563],
                "강서구" => [35.1461, 128.8681],
                "해운대구" => [35.1635, 129.1631],
                "사하구" => [35.1025, 128.9581],
                "금정구" => [35.2754, 129.0708],
                "연제구" => [35.1890, 129.0712],
                "수영구" => [35.1580, 129.1229],
                "사상구" => [35.1773, 128.9386],
                "기장군" => [35.2383, 129.2043],
            ],

            "대구" => [
                "중구" => [35.8700, 128.5900],
                "동구" => [35.8794, 128.6053],
                "서구" => [35.8663, 128.5610],
                "남구" => [35.8385, 128.5831],
                "북구" => [35.8995, 128.6114],
                "수성구" => [35.8414, 128.6215],
                "달서구" => [35.8295, 128.5436],
                "달성군" => [35.8528, 128.5458],
            ],

            "인천" => [
                "중구" => [37.4563, 126.7052],
                "동구" => [37.4850, 126.7105],
                "미추홀구" => [37.4326, 126.6558],
                "연수구" => [37.4419, 126.7058],
                "남동구" => [37.4274, 126.6968],
                "부평구" => [37.4903, 126.7158],
                "계양구" => [37.5383, 126.7050],
                "서구" => [37.4543, 126.6548],
                "강화군" => [37.7797, 126.4185],
                "옹진군" => [37.7460, 126.3683],
            ],

            "광주" => [
                "동구" => [35.1600, 126.9150],
                "서구" => [35.1567, 126.8950],
                "남구" => [35.1233, 126.8967],
                "북구" => [35.1842, 126.9170],
                "광산구" => [35.1931, 126.8002],
            ],

            "대전" => [
                "동구" => [36.3219, 127.4314],
                "중구" => [36.3210, 127.4250],
                "서구" => [36.3500, 127.4325],
                "유성구" => [36.3414, 127.3855],
                "대덕구" => [36.3543, 127.4364],
            ],

            "울산" => [
                "중구" => [35.5452, 129.3162],
                "남구" => [35.5292, 129.2903],
                "동구" => [35.5371, 129.3325],
                "북구" => [35.5683, 129.3844],
                "울주군" => [35.6147, 129.4255],
            ],

            "세종" => [
                "세종시" => [36.4803, 127.2898],
            ],

            "경기" => [
                "수원시" => [37.2635, 127.0286],
                "수원시장안구" => [37.2635, 127.0286],
                "수원시권선구" => [37.2756, 126.9497],
                "수원시팔달구" => [37.2631, 127.0000],
                "수원시영통구" => [37.2385, 127.0621],
                "성남시" => [37.4173, 127.1267],
                "성남시수정구" => [37.4333, 127.1300],
                "성남시중원구" => [37.4300, 127.1389],
                "성남시분당구" => [37.3833, 127.1111],
                "의정부시" => [37.7388, 127.0803],
                "안양시" => [37.3996, 126.9281],
                "안양시만안구" => [37.4011, 126.9233],
                "안양시동안구" => [37.3922, 126.9370],
                "부천시" => [37.4999, 126.7669],
                "부천시원미구" => [37.5027, 126.7857],
                "부천시오정구" => [37.4740, 126.7843],
                "부천시소사구" => [37.5041, 126.7767],
                "광명시" => [37.4118, 126.8780],
                "평택시" => [36.9750, 127.1000],
                "동두천시" => [37.9030, 127.0675],
                "안산시" => [37.3117, 126.8303],
                "안산시상록구" => [37.3117, 126.8303],
                "안산시단원구" => [37.3191, 126.8472],
                "고양시" => [37.6589, 126.8448],
                "고양시덕양구" => [37.6589, 126.8448],
                "고양시일산동구" => [37.6570, 126.7820],
                "고양시일산서구" => [37.6508, 126.7322],
                "과천시" => [37.4311, 126.9953],
                "구리시" => [37.5991, 127.1367],
                "남양주시" => [37.6541, 127.2063],
                "오산시" => [37.1450, 127.0480],
                "시흥시" => [37.4489, 126.7560],
                "군포시" => [37.3590, 126.9267],
                "의왕시" => [37.3656, 126.9158],
                "하남시" => [37.5333, 127.2180],
                "용인시" => [37.2360, 127.1745],
                "용인시처인구" => [37.2360, 127.1745],
                "용인시기흥구" => [37.2414, 127.1263],
                "용인시수지구" => [37.3414, 127.1275],
                "파주시" => [37.7600, 126.7667],
                "이천시" => [37.2753, 127.3071],
                "안성시" => [37.0167, 127.2733],
                "김포시" => [37.5583, 126.7892],
                "화성시" => [37.1956, 126.8356],
                "광주시" => [37.4181, 127.2153],
                "양주시" => [37.7300, 127.0650],
                "포천시" => [37.8981, 127.1793],
                "여주시" => [37.2325, 127.4314],
                "연천군" => [38.0933, 127.3250],
                "가평군" => [37.8056, 127.4583],
                "양평군" => [37.4414, 127.5032],
            ],

            // 강원 시/구
            "강원" => [
                "춘천시" => [37.8813, 127.7295],
                "원주시" => [37.3210, 127.9294],
                "강릉시" => [37.7519, 128.8761],
                "동해시" => [37.5236, 129.1085],
                "태백시" => [37.1330, 128.9814],
                "속초시" => [38.2058, 128.5911],
                "삼척시" => [37.4474, 129.1603],
                "홍천군" => [37.8633, 127.8783],
                "횡성군" => [37.8444, 127.9992],
                "영월군" => [37.2097, 128.3133],
                "평창군" => [37.7414, 128.5243],
                "정선군" => [37.2525, 128.7355],
                "철원군" => [38.1620, 127.0758],
                "화천군" => [38.0597, 127.8664],
                "양구군" => [37.3031, 127.7514],
                "인제군" => [38.1058, 128.1492],
                "고성군" => [38.2870, 128.4497],
                "양양군" => [38.0667, 128.6167],
            ],

            // 충남 시/구
            "충남" => [
                "천안시 동남구" => [36.8065, 127.1280],
                "천안시 서북구" => [36.8080, 127.0690],
                "공주시" => [36.4550, 127.1281],
                "보령시" => [36.2913, 126.6119],
                "아산시" => [36.8044, 127.1683],
                "서산시" => [36.7850, 126.4503],
                "논산시" => [36.1942, 127.1043],
                "계룡시" => [36.3219, 127.2975],
                "당진시" => [36.9914, 126.6356],
                "금산군" => [36.5314, 127.1633],
                "부여군" => [36.3272, 126.9222],
                "서천군" => [36.1047, 126.5472],
                "청양군" => [36.4642, 126.9836],
                "홍성군" => [36.3913, 126.6319],
                "예산군" => [36.6361, 126.6433],
                "태안군" => [36.7997, 126.3886],
            ],

            // 충북 시/구
            "충북" => [
                "청주시 상당구" => [36.6420, 127.4890],
                "청주시 흥덕구" => [36.6340, 127.4410],
                "청주시 서원구" => [36.6230, 127.4580],
                "청주시 청원구" => [36.6270, 127.5010],
                "충주시" => [36.9883, 127.9253],
                "제천시" => [37.1433, 128.1892],
                "보은군" => [36.5683, 127.5933],
                "옥천군" => [36.6269, 127.9153],
                "영동군" => [36.5192, 127.6672],
                "진천군" => [36.6286, 127.5883],
                "괴산군" => [36.9944, 127.9508],
                "음성군" => [36.6592, 127.7633],
                "단양군" => [36.9333, 128.2667],
            ],

            // 경남 시/구
            "경남" => [
                "창원시 의창구" => [35.2270, 128.6740],
                "창원시 성산구" => [35.2200, 128.6800],
                "창원시 진해구" => [35.1660, 128.7360],
                "창원시 마산합포구" => [35.2090, 128.6830],
                "창원시 마산회원구" => [35.1980, 128.7040],
                "진주시" => [35.1797, 128.1013],
                "통영시" => [34.8531, 128.4192],
                "사천시" => [35.0028, 128.0214],
                "김해시" => [35.2281, 128.9025],
                "밀양시" => [35.4981, 128.8794],
                "거제시" => [34.8653, 128.6206],
                "양산시" => [35.3542, 129.0392],
                "의령군" => [35.2961, 128.5853],
                "함안군" => [35.1761, 128.5392],
                "창녕군" => [35.1411, 128.5003],
                "고성군" => [35.5283, 128.3908],
                "남해군" => [34.9581, 127.8103],
                "하동군" => [35.2319, 127.5975],
                "산청군" => [35.0167, 127.4992],
                "함양군" => [35.0703, 127.5464],
                "거창군" => [35.5333, 127.9333],
                "합천군" => [35.3122, 128.3133],
            ],

            // 경북 시/구
            "경북" => [
                "포항시 북구" => [36.0820, 129.3610],
                "포항시 남구" => [36.0150, 129.3410],
                "경주시" => [35.8428, 129.2153],
                "김천시" => [36.1206, 128.1536],
                "안동시" => [36.5686, 128.7294],
                "구미시" => [36.1191, 128.3444],
                "영주시" => [36.9233, 128.6908],
                "영천시" => [35.9231, 128.7567],
                "상주시" => [36.4342, 128.1642],
                "문경시" => [36.5175, 128.3586],
                "경산시" => [35.8328, 128.7494],
                "군위군" => [36.4614, 129.0583],
                "의성군" => [36.1942, 128.3781],
                "청송군" => [36.5811, 129.3503],
                "영양군" => [36.4547, 128.3961],
                "영덕군" => [36.5831, 129.3936],
                "청도군" => [35.7769, 128.1272],
                "고령군" => [35.5583, 128.4411],
                "성주군" => [35.5775, 128.1625],
                "칠곡군" => [35.9931, 128.4564],
                "예천군" => [36.4022, 128.7422],
                "봉화군" => [36.8556, 128.5172],
                "울진군" => [36.9269, 129.3333],
                "울릉군" => [37.4997, 130.9425],
            ],

            // 전남 시/구
            "전남" => [
                "목포시" => [34.8114, 126.3928],
                "여수시" => [34.7603, 127.6681],
                "순천시" => [34.9506, 127.4853],
                "나주시" => [35.0053, 126.7283],
                "광양시" => [34.9333, 127.6842],
                "담양군" => [35.0250, 126.9922],
                "곡성군" => [35.1481, 126.6969],
                "구례군" => [35.3475, 127.1267],
                "고흥군" => [34.7456, 126.4356],
                "보성군" => [34.7672, 126.4842],
                "화순군" => [35.0194, 126.5331],
                "장흥군" => [34.7736, 126.3075],
                "강진군" => [34.7283, 126.1511],
                "해남군" => [34.8161, 126.1114],
                "영암군" => [34.7325, 126.3736],
                "무안군" => [34.9322, 126.6581],
                "함평군" => [35.0167, 126.4219],
                "영광군" => [35.0972, 126.5569],
                "장성군" => [35.0483, 126.7492],
                "완도군" => [34.8356, 126.7211],
                "진도군" => [34.6222, 126.6900],
                "신안군" => [34.4553, 126.8122],
            ],

            // 전북 시/구
            "전북" => [
                "전주시 완산구" => [35.8242, 127.1489],
                "전주시 덕진구" => [35.8265, 127.1305],
                "군산시" => [35.9783, 126.7053],
                "익산시" => [35.9483, 126.9586],
                "정읍시" => [35.5697, 126.8300],
                "남원시" => [35.4144, 127.3781],
                "김제시" => [35.8078, 126.9092],
                "완주군" => [35.7442, 127.3694],
                "진안군" => [35.5772, 127.2192],
                "무주군" => [35.8703, 127.6303],
                "장수군" => [35.9233, 127.6486],
                "임실군" => [35.6114, 127.4275],
                "순창군" => [35.5522, 127.0644],
                "고창군" => [35.5333, 126.7825],
                "부안군" => [35.7081, 126.6533],
            ],

            // 제주 시/구
            "제주" => [
                "제주시" => [33.4996, 126.5312],
                "서귀포시" => [33.2525, 126.5603],
            ],
        ];

        if(isset($city_coords[$selected_province][$sub_loca])) {
            $lat = $city_coords[$selected_province][$sub_loca][0];
            $lng = $city_coords[$selected_province][$sub_loca][1];
            $map_level = 5; // 특정 시/구 선택 시 확대 레벨 설정
        }
    }
}

// 검색 재설정
function get_board_sfl_select_options2($sfl){
    global $is_admin;
    $str = '';

    $str .= '<option value="wr_subject" '.get_selected($sfl, 'wr_subject').'>제목</option>';
    $str .= '<option value="wr_9" '.get_selected($sfl, 'wr_9', true).'>광역시/도</option>';
    $str .= '<option value="wr_10" '.get_selected($sfl, 'wr_10').'>시/군/구</option>';
    $str .= '<option value="wr_8" '.get_selected($sfl, 'wr_8', true).'>업체명</option>';

    return run_replace('get_board_sfl_select_options2', $str, $sfl);
}

// 카테고리 재설정
$category_option2 = '';
if ($board['bo_use_category']) {
    $is_category = true;
    $category_href = get_pretty_url($bo_table);

    $category_option2 .= '<li><a href="'.$category_href.'"';
    if ($ca == '')
        $category_option2 .= ' id="bo_cate_on"';
    $category_option2 .= '>전체</a></li>';

    $categories = explode('|', $board['bo_category_list']); // 구분자가 | 로 되어 있음
    for ($i=0; $i<count($categories); $i++) {
        $category = trim($categories[$i]);
        if ($category=='') continue;
        $category_option2 .= '<li><a href="'.(get_pretty_url($bo_table,'','sop=and&sfl=wr_9&stx='.$loca.'&loca='.$loca.'&sca='.urlencode($category))).'&sub_loca='.$sub_loca.'"';
        $category_msg = '';
        if ($category == $ca) { // 현재 선택된 카테고리라면
            $category_option2 .= ' id="bo_cate_on"';
            $category_msg = '<span class="sound_only">열린 분류 </span>';
        }
        $category_option2 .= '>'.$category_msg.$category.'</a></li>';
    }
}
?>

<!-- 스타일시트 포함 -->
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.php?columns=<?php echo $board['bo_gallery_cols']; ?>">

<?php if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) {  ?>

    <!-- 상단 영역을 초기화 (100%로 만들기 위해) -->
    <style>
        #container_title {display: none;}
        .sub {min-height: auto; padding-top: 0px; padding-bottom: 0px;}

        /* 1차 탭 스타일 */
        .tab-menu.primary-tabs {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            list-style: none;
            padding: 0;
            margin-top: 20px;
            border-bottom: 1px solid #e0e0e0;
            background-color: #fff;
            scroll-behavior: smooth; /* 부드러운 스크롤 효과 */
            -webkit-overflow-scrolling: touch; /* 모바일 터치 스크롤 부드럽게 */
            white-space: nowrap; /* 텍스트 줄바꿈 방지 */
            box-sizing: border-box; /* 패딩과 보더 포함 */
        }

        .tab-menu.primary-tabs li {
            flex: 0 0 auto;
            margin-right: 5px;
        }

        .tab-menu.primary-tabs li a {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 10px;
            text-decoration: none;
            color: #555;
            font-size: 15px;
            font-weight: 500;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s ease;
        }

        .tab-menu.primary-tabs li a:hover {
            background-color: #00a3ff;
            color: #ffffff;
        }

        .tab-menu.primary-tabs li a.active {
            background-color: #00a3ff;
            color: #fff;
        }

 /* 2차 탭 스타일 */
 .tab-menu.secondary-tabs {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            list-style: none;
            border-bottom: 1px solid #dfdfdf;
            scroll-behavior: smooth; /* 부드러운 스크롤 효과 */
            -webkit-overflow-scrolling: touch; /* 모바일 터치 스크롤 부드럽게 */
            white-space: nowrap; /* 텍스트 줄바꿈 방지 */
            box-sizing: border-box; /* 패딩과 보더 포함 */
            cursor: grab; /* 드래그 가능한 커서 */
            padding: 0 10px; /* 탭 간 여백 추가 */
        }

        .tab-menu.secondary-tabs.active {
            cursor: grabbing; /* 드래그 중일 때 커서 */
        }

        .tab-menu.secondary-tabs li {
            flex: 0 0 auto;
            margin-right: 10px; /* 탭 간 간격 추가 */
        }

        .tab-menu.secondary-tabs li a {
            display: inline-flex;
            padding: 0px 5px; /* 탭 패딩 조정 */
            border: 0px;
            font-size: 13px;
            color: #000;
            background-color: transparent;
            line-height: 35px;
            position: relative;
            white-space: nowrap; /* 텍스트 줄바꿈 방지 */
        }

        .tab-menu.secondary-tabs li a:hover {
            color: #00a3ff;
            background-color: #f0f0f0; /* 스크롤 힌트를 위한 배경색 변경 */
        }

        .tab-menu.secondary-tabs li a.active {
            color: #00a3ff;
            font-weight: 600;
        }

        /* 스크롤바 숨기기 */
        .tab-menu.secondary-tabs::-webkit-scrollbar {
            display: none;
        }
        .tab-menu.secondary-tabs {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;     /* Firefox */
        }


        /* 반응형 디자인 */
        @media (max-width: 768px) {
            .tab-menu.secondary-tabs li a {
                padding: 10px 15px;
                font-size: 14px;
            }
            .thumbnail {
                height: 300px; /* 높이를 고정 */
            }

        }

        @media (max-width: 480px) {
            .tab-menu.secondary-tabs li a {
                padding: 8px 12px;
                font-size: 12px;
            }

            .thumbnail {
                height: 235px !important; /* !important를 추가하여 강제로 우선 적용 */
            }
        }

        /* 썸네일 이미지 스타일 */
        .bbs_prd_list_wrap {
            display: flex;
            align-items: flex-start; /* 위쪽 정렬 */
            padding: 0;
            border: none;
            flex-direction: column;
            width: 100%;
            height: auto; /* 높이를 자동으로 조정 */
        }
        .thumbnail {
            width: 100%; /* 가로를 100%로 설정 */
            margin: 0;
            overflow: hidden; /* 이미지가 영역을 넘어가지 않도록 */
            border-radius: 8px; /* 약간의 둥근 모서리 추가 */
            display: flex;
            align-items: center; /* 세로 정렬 */
            justify-content: center; /* 가로 정렬 */
        }
        .thumbnail img {
            width: 100%; /* 가로 크기를 자동 조정 */
            height: 100%; /* 높이를 부모 요소에 맞춤 */
            object-fit: cover; /* 이미지가 비율을 유지하며 영역을 채움 */
        }

        .bbs_prd_list_con {
            width: 100%;
            padding: 10px 15px;
            background: #fff; /* 흰색 배경 추가 */
            border-top: none; /* 이미지와 연결되는 상단 경계선 제거 */
            border-radius: 0 0 8px 8px; /* 둥근 모서리와 일관성 유지 */
            height: 130px;
        }

        .bbs_prd_list_con li {
            margin-bottom: 5px;
            font-size: 14px;
            line-height: 1.5; /* 읽기 편한 줄 간격 */
            color: #333; /* 텍스트 색상 */
        }

        .bbs_prd_list_con .bbs_prd_list_con_li2 a {
            font-size: 16px;
            font-weight: bold;
            color: #000; /* 강조된 텍스트 색상 */
            text-decoration: none;
        }

        .bbs_prd_list_con .bbs_prd_list_con_li2 a:hover {
            color: #00a3ff; /* 강조된 텍스트 호버 색상 */
        }

        .bbs_prd_list_con .bbs_prd_list_con_li3 {
            font-size: 14px;
            color: #666; /* 설명 텍스트 색상 */
        }

        .bbs_prd_list_con .bbs_prd_list_con_li4 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        /* 지도 컨테이너 수정 */
        #map {
            position: relative; /* 조절 핸들을 포함하기 위해 position을 relative로 설정 */
        }

        /* 조절 핸들 스타일 */

        
        #map-resize-handle {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 50px;
            background: #00a3ff;
            border-radius: 30%;
            margin-bottom: 5px;
            cursor: ns-resize;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            
            /* 화살표를 중앙에 배치하기 위해 flexbox 사용 */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 위쪽 화살표 */
        #map-resize-handle::before {
            content: '';
            display: block;
            width: 0;
            height: 0;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 8px solid #fff;
        }

        /* 아래쪽 화살표 */
        #map-resize-handle::after {
            content: '';
            display: block;
            width: 0;
            height: 0;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-top: 8px solid #fff;
            margin-top: 4px; /* 화살표 간 간격 */
        }

        /* 지도 높이 조절 애니메이션 */
        #map {
            transition: height 0.3s ease;
        }

        /* 조절 핸들 호버 효과 */
        #map-resize-handle:hover {
            opacity: 0.7;
        }
    </style>
    </section>
    <!-- } -->

    <!-- Kakao Maps SDK -->
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $config['cf_kakao_js_apikey'] ?>&libraries=services"></script>
    <div id="map" style="width: 100%; height: 40vh; margin:0px; position: relative;">
        <!-- 조절 핸들 추가 -->
        <div id="map-resize-handle"></div>
    </div>

    <script>
        var mapContainer = document.getElementById('map'), // 지도를 표시할 div
            mapOption = {
                center: new kakao.maps.LatLng(<?php echo isset($lat) ? $lat : 0; ?>, <?php echo isset($lng) ? $lng : 0; ?>), // 지도의 중심좌표
                level:<?php echo isset($map_level) ? $map_level : 5; ?> // 지도 초기 확대레벨
            };

        var map = new kakao.maps.Map(mapContainer, mapOption);

        // 지도 타입 컨트롤
        var mapTypeControl = new kakao.maps.MapTypeControl();
        map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPRIGHT);

        // 줌 컨트롤
        var zoomControl = new kakao.maps.ZoomControl();
        map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);

        <?php

        // 카테고리 필터링 변수 정의 (이미 상단에서 정의됨)
        // $ca = isset($_GET['sca']) ? $_GET['sca'] : '';

        if(!empty($ca)) {
            $sql = " SELECT * FROM g5_write_{$bo_table} WHERE ca_name = '{$ca}' ORDER BY wr_id ASC ";
        } else if(!empty($loca)) {
            $sql = " SELECT * FROM g5_write_{$bo_table} WHERE wr_9 = '{$loca}' ";
            if(!empty($sub_loca)) {
                $sql .= " AND wr_10 like '%{$sub_loca}%' ";
            }
            $sql .= " ORDER BY wr_id ASC ";
        } else {
            $sql = "SELECT * FROM g5_write_".$bo_table." ORDER BY wr_id ASC ";
        }

        $result = sql_query($sql);
        $cnt = 0;
        while ($row = sql_fetch_array($result)) {

            //필드분할
            $wr_1 = isset($row["wr_1"]) ? explode("|", $row["wr_1"]) : [];
            $wr_2 = isset($row["wr_2"]) ? explode("|", $row["wr_2"]) : [];
            $wr_3 = isset($row["wr_3"]) ? explode("|", $row["wr_3"]) : [];
            $wr_4 = isset($row["wr_4"]) ? explode("|", $row["wr_4"]) : [];
            $wr_5 = isset($row["wr_5"]) ? explode("|", $row["wr_5"]) : [];

            if(isset($wr_3[4]) && isset($wr_3[5])) {
                $thumb = get_list_thumbnail($board['bo_table'], $row['wr_id'], 100, 100, false, true);
                if(isset($thumb['src'])) {
                    $img_content = $thumb['src'];
                }
    ?>

    var imageSrc = '<?php echo $board_skin_url ?>/img/pin.svg',
        imageSize = new kakao.maps.Size(24, 35), // 마커이미지의 크기입니다
        imageOption = {
            offset: new kakao.maps.Point(12, 35)
        }; // 마커이미지의 옵션입니다. 마커의 좌표와 일치시킬 이미지 안에서의 좌표를 설정합니다.

    // 마커의 이미지정보를 가지고 있는 마커이미지를 생성합니다
    var markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize, imageOption),
        markerPosition = new kakao.maps.LatLng(<?php echo $wr_3[4] ?>, <?php echo $wr_3[5] ?>); // 마커가 표시될 위치입니다

    // 마커를 생성합니다
    var marker = new kakao.maps.Marker({
        position: markerPosition,
        image: markerImage
    });

    // 마커가 지도 위에 표시되도록 설정합니다
    marker.setMap(map);

    // 커스텀 오버레이에 표시할 컨텐츠 입니다
    var content = '<div class="wrap">' +
        '    <div class="info">' +
        '        <div class="body">' +
        '            <div class="desc">' +
        '                <img src="<?php echo $board_skin_url ?>/img/close_black_24dp.svg" class="close" onclick="closeOverlay_<?php echo $row['wr_id'] ?>()" title="닫기">' +
        '                <div class="titles"><a href="<?php echo G5_BBS_URL ?>/board.php?bo_table=<?php echo $bo_table ?>&wr_id=<?php echo $row['wr_id']; ?>" class="cut80"><?php echo $row['wr_subject']; ?></a></div>' +
        '                <?php if(isset($row['wr_8'])) { ?><div class="sub3 cut80"><?php echo $row['wr_8']; ?></div><?php } ?>' +
        '            </div>' +
        '        </div>' +
        '    </div>' +
        '</div>';

    // 마커 위에 커스텀오버레이를 표시합니다
    var position = new kakao.maps.LatLng(<?php echo $wr_3[4] ?>, <?php echo $wr_3[5] ?>);

    // 마커를 중심으로 커스텀 오버레이를 표시하기위해 CSS를 이용해 위치를 설정
    var overlay_<?php echo $row['wr_id'] ?> = new kakao.maps.CustomOverlay({
        content: content,
        map: map,
        position: position,
        yAnchor: 1
    });

    // 마커를 클릭했을 때 커스텀 오버레이를 표시합니다
    kakao.maps.event.addListener(marker, 'click', function() {
        overlay_<?php echo $row['wr_id'] ?>.setMap(map);
    });

    // 커스텀 오버레이를 닫기 위해 호출되는 함수입니다
    function closeOverlay_<?php echo $row['wr_id'] ?>() {
        overlay_<?php echo $row['wr_id'] ?>.setMap(null);
    }

    overlay_<?php echo $row['wr_id'] ?>.setMap(null);

    <?php
        $cnt++;
    }
}
?>
    </script>

    <section class="sub" style="width:<?php echo $rb_core['sub_width'] ?>px; padding-top:40px; padding-bottom:80px;">

<?php } ?>

<div class="rb_bbs_wrap" id="scroll_container" style="width:<?php echo $width; ?>;">

    <form name="fboardlist" id="fboardlist" action="<?php echo G5_BBS_URL; ?>/board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
        <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
        <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
        <input type="hidden" name="stx" value="<?php echo $stx ?>">
        <input type="hidden" name="spt" value="<?php echo $spt ?>">
        <input type="hidden" name="sst" value="<?php echo $sst ?>">
        <input type="hidden" name="sod" value="<?php echo $sod ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <input type="hidden" name="sw" value="">

        <div class="btns_gr_wrap">

            <!-- $rb_core['sub_width'] 는 반드시 포함해주세요 (환경설정 > 서브가로폭에 따른 버튼의 위치 설정) -->
            <div class="sub" style="width:<?php echo $rb_core['sub_width'] ?>px;">

                <?php if(!$wr_id) { // 목록보기를 했을 경우 노출되는 부분 방지?>

                <div class="btns_gr">
                    <?php if ($admin_href) { ?>
                    <button type="button" class="fl_btns" onclick="window.open('<?php echo $admin_href ?>');">
                        <img src="<?php echo $board_skin_url ?>/img/ico_set.svg" alt="관리">
                        <span class="tooltips">관리</span>
                    </button>
                    <?php } ?>

                    <button type="button" class="fl_btns btn_bo_sch">
                        <img src="<?php echo $board_skin_url ?>/img/ico_ser.svg" alt="검색">
                        <span class="tooltips">검색</span>
                    </button>


                    <?php if ($rss_href) { ?>
                    <button type="button" class="fl_btns" onclick="window.open('<?php echo $rss_href ?>');">
                        <img src="<?php echo $board_skin_url ?>/img/ico_rss.svg" alt="RSS">
                        <span class="tooltips">RSS</span>
                    </button>
                    <?php } ?>


                    <?php if ($write_href) { ?>
                    <button type="button" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                        <img src="<?php echo $board_skin_url ?>/img/ico_write.svg" alt="글 등록">
                        <span class="tooltips">글 등록</span>
                    </button>
                    <?php } ?>

                </div>
                <?php } ?>

                <div class="cb"></div>
            </div>
        </div>

        <?php if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) {  ?>
        <!-- 1차 탭 -->
        <ul class="tab-menu primary-tabs">
            <li><a href="?bo_table=<?php echo $bo_table ?>&sop=and&sfl=wr_9&stx=&loca=" class="<?php echo (empty($loca)) ? 'active' : ''; ?>">전체지역</a></li>
            <?php foreach($regions as $province => $cities): ?>
                <li>
                    <a href="?bo_table=<?php echo $bo_table ?>&sop=and&sfl=wr_9&stx=<?php echo urlencode($province); ?>&loca=<?php echo urlencode($province); ?>" class="<?php echo ($loca == $province) ? 'active' : ''; ?>">
                        <?php echo $province; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- 2차 탭 -->
        <?php if(!empty($selected_province) && !empty($sub_regions)): ?>
            <ul class="tab-menu secondary-tabs">
                <li><a href="?bo_table=<?php echo $bo_table ?>&sop=and&sfl=wr_9&stx=<?php echo urlencode($selected_province); ?>&loca=<?php echo urlencode($selected_province); ?>&sub_loca=" class="<?php echo (empty($sub_loca)) ? 'active' : ''; ?>">전체</a></li>
                <?php foreach($sub_regions as $city): ?>
                    <li>
                        <a href="?bo_table=<?php echo $bo_table ?>&sop=and&sfl=wr_9&stx=<?php echo urlencode($selected_province); ?>&loca=<?php echo urlencode($selected_province); ?>&sub_loca=<?php echo urlencode($city); ?>" class="<?php echo (isset($sub_loca) && $sub_loca == $city) ? 'active' : ''; ?>">
                            <?php echo $city; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function(){
                // 1차 탭 클릭 시 페이지 리로드 (기존 기능 유지)
                $(".primary-tabs a").click(function(e){
                    // 기본 동작을 막지 않습니다. PHP로 페이지를 새로고침하여 필터링합니다.
                });

                // 2차 탭 클릭 시 페이지 리로드 (기존 기능 유지)
                $(".secondary-tabs a").click(function(e){
                    // 기본 동작을 막지 않습니다. PHP로 페이지를 새로고침하여 필터링합니다.
                });
            });
        </script>

        <!-- 추가적인 JavaScript (드래그 스크롤 기능) -->
<!-- ... 기존 PHP 코드 ... -->

<!-- 추가적인 JavaScript (드래그 스크롤 기능) -->
<script>
    $(document).ready(function() {
        const secondaryTabs = document.querySelector('.tab-menu.secondary-tabs');
        if (secondaryTabs) {
            let isDown = false;
            let startX;
            let scrollLeft;
            let moved = false; // 드래그 여부를 추적하기 위한 변수

            // 마우스 드래그 이벤트
            secondaryTabs.addEventListener('mousedown', (e) => {
                isDown = true;
                secondaryTabs.classList.add('active');
                startX = e.pageX - secondaryTabs.offsetLeft;
                scrollLeft = secondaryTabs.scrollLeft;
                moved = false;
            });

            secondaryTabs.addEventListener('mouseleave', () => {
                isDown = false;
                secondaryTabs.classList.remove('active');
            });

            secondaryTabs.addEventListener('mouseup', () => {
                isDown = false;
                secondaryTabs.classList.remove('active');
            });

            secondaryTabs.addEventListener('mousemove', (e) => {
                if(!isDown) return;
                e.preventDefault();
                const x = e.pageX - secondaryTabs.offsetLeft;
                const walk = (x - startX) * 1; // 스크롤 속도 조절
                if (Math.abs(walk) > 5) { // 이동 거리가 5px 이상일 때만 드래그로 간주
                    moved = true;
                }
                secondaryTabs.scrollLeft = scrollLeft - walk;
            });

            // 터치 드래그 이벤트 (모바일 지원)
            secondaryTabs.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - secondaryTabs.offsetLeft;
                scrollLeft = secondaryTabs.scrollLeft;
                moved = false;
            });

            secondaryTabs.addEventListener('touchend', () => {
                isDown = false;
            });

            secondaryTabs.addEventListener('touchmove', (e) => {
                if(!isDown) return;
                const x = e.touches[0].pageX - secondaryTabs.offsetLeft;
                const walk = (x - startX) * 1; // 스크롤 속도 조절
                if (Math.abs(walk) > 5) { // 이동 거리가 5px 이상일 때만 드래그로 간주
                    moved = true;
                }
                secondaryTabs.scrollLeft = scrollLeft - walk;
            });

            // 클릭 이벤트 시 드래그 여부에 따라 동작 결정
            secondaryTabs.querySelectorAll('a').forEach(function(tabLink) {
                tabLink.addEventListener('click', function(e) {
                    if (moved) {
                        e.preventDefault(); // 드래그 시 클릭 이벤트 방지
                        moved = false;
                    }
                });
            });
        }

        // 지도 높이 조절 기능 (기존 코드 유지)
        const mapContainer = document.getElementById('map');
        const resizeHandle = document.getElementById('map-resize-handle');
        let isResizing = false;
        let startY;
        let startHeight;

        // 마우스 다운 이벤트
        resizeHandle.addEventListener('mousedown', function(e) {
            isResizing = true;
            startY = e.clientY;
            startHeight = mapContainer.offsetHeight;
            document.body.style.cursor = 'ns-resize';
            e.preventDefault(); // 선택 텍스트 방지
        });

        // 마우스 이동 이벤트
        document.addEventListener('mousemove', function(e) {
            if (!isResizing) return;
            const dy = e.clientY - startY;
            let newHeight = startHeight + dy;

            // 최소 및 최대 높이 설정 (필요에 따라 조정)
            const minHeight = 200; // 최소 높이 200px
            const maxHeight = window.innerHeight * 0.8; // 최대 높이 화면의 80%
            if (newHeight < minHeight) newHeight = minHeight;
            if (newHeight > maxHeight) newHeight = maxHeight;

            mapContainer.style.height = newHeight + 'px';
            map.relayout(); // Kakao Maps에 리사이즈 알리기
        });

        // 마우스 업 이벤트
        document.addEventListener('mouseup', function(e) {
            if (isResizing) {
                isResizing = false;
                document.body.style.cursor = 'default';
            }
        });

        // 터치 이벤트 지원 (모바일)
        resizeHandle.addEventListener('touchstart', function(e) {
            isResizing = true;
            startY = e.touches[0].clientY;
            startHeight = mapContainer.offsetHeight;
            document.body.style.cursor = 'ns-resize';
            e.preventDefault();
        });

        document.addEventListener('touchmove', function(e) {
            if (!isResizing) return;
            const dy = e.touches[0].clientY - startY;
            let newHeight = startHeight + dy;

            const minHeight = 200;
            const maxHeight = window.innerHeight * 0.8;
            if (newHeight < minHeight) newHeight = minHeight;
            if (newHeight > maxHeight) newHeight = maxHeight;

            mapContainer.style.height = newHeight + 'px';
            map.relayout();
        });

        document.addEventListener('touchend', function(e) {
            if (isResizing) {
                isResizing = false;
                document.body.style.cursor = 'default';
            }
        });

        // 윈도우 리사이즈 시 지도 리사이즈
        window.addEventListener('resize', function() {
            map.relayout();
        });
    });
</script>


        <!-- 추가적인 CSS (드래그 활성화 시 커서 변경) -->
        <style>
            .tab-menu.secondary-tabs.active {
                cursor: grabbing; /* 드래그 중일 때 커서 */
            user-select: none; /* 텍스트 선택 방지 */
            }
            /* 오버레이가 있다면 z-index를 낮게 설정 */
.overlay-class { /* 실제 오버레이 클래스명으로 변경 */
    z-index: 1;
}

.tab-menu.secondary-tabs {
    z-index: 10; /* 탭의 z-index를 높게 설정 */
}
        </style>

        <?php } ?>
        <ul class="rb_bbs_top" <?php if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) {  ?>style="top:0px;"<?php } ?>>

            <?php if($board['bo_read_point'] || $board['bo_write_point'] || $board['bo_comment_point'] || $board['bo_download_point']) { ?>
            <li class="point_info_btns_wrap">
                <button type="button" class="point_info_btns" id="point_info_opens_btn">
                    <i><svg width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 0C15.523 0 20 4.477 20 10C20 15.523 15.523 20 10 20C4.477 20 0 15.523 0 10C0 4.477 4.477 0 10 0ZM11.5 5H9C8.46957 5 7.96086 5.21071 7.58579 5.58579C7.21071 5.96086 7 6.46957 7 7V14C7 14.2652 7.10536 14.5196 7.29289 14.7071C7.48043 14.8946 7.73478 15 8 15C8.26522 15 8.51957 14.8946 8.70711 14.7071C8.89464 14.5196 9 14.2652 9 14V12H11.5C12.4283 12 13.3185 11.6313 13.9749 10.9749C14.6313 10.3185 15 9.42826 15 8.5C15 7.57174 14.6313 6.6815 13.9749 6.02513C13.3185 5.36875 12.4283 5 11.5 5ZM11.5 7C11.8978 7 12.2794 7.15804 12.5607 7.43934C12.842 7.72064 13 8.10218 13 8.5C13 8.89782 12.842 9.27936 12.5607 9.56066C12.2794 9.84196 11.8978 10 11.5 10H9V7H11.5Z" fill="#09244B" />
                        </svg></i>
                    <span class="pc">포인트정책</span></button>

                <div class="point_info_opens">
                    <h6><?php echo $board['bo_subject'] ?> 포인트 정책</h6>
                    <ul>
                        <?php if($board['bo_read_point']) { ?>
                        <dl>
                            <dd>글읽기</dd>
                            <dd class="font-B"><?php echo number_format($board['bo_read_point']); ?>P</dd>
                        </dl>
                        <?php } ?>
                        <?php if($board['bo_write_point']) { ?>
                        <dl>
                            <dd>글쓰기</dd>
                            <dd class="font-B"><?php echo number_format($board['bo_write_point']); ?>P</dd>
                        </dl>
                        <?php } ?>
                        <?php if($board['bo_comment_point']) { ?>
                        <dl>
                            <dd>댓글</dd>
                            <dd class="font-B"><?php echo number_format($board['bo_comment_point']); ?>P</dd>
                        </dl>
                        <?php } ?>
                        <?php if($board['bo_download_point']) { ?>
                        <dl>
                            <dd>다운로드</dd>
                            <dd class="font-B"><?php echo number_format($board['bo_download_point']); ?>P</dd>
                        </dl>
                        <?php } ?>
                    </ul>
                </div>

                <script>
                    $(document).ready(function() {
                        $(document).click(function(event) {
                            if (!$(event.target).closest('#point_info_opens_btn, .point_info_opens').length) {
                                if ($('.point_info_opens').is(':visible')) {
                                    $('.point_info_opens').hide();
                                    $('#point_info_opens_btn').removeClass('act');
                                }
                            }
                        });

                        $('#point_info_opens_btn').click(function(event) {
                            event.stopPropagation();
                            $('.point_info_opens').toggle();
                            $(this).toggleClass('act');
                        });
                    });
                </script>


            </li>
            <?php } ?>

            <?php if ($is_checkbox) { ?>
            <li>
                <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
                <label for="chkall"></label>
            </li>
            <?php } ?>


            <div class="cb"></div>
        </ul>
        <!-- } -->

        <!-- 카테고리 { -->
        <?php if ($is_category) { ?>
        <nav id="bo_cate" class="swiper-container swiper-container-category">
            <ul id="bo_cate_ul" class="swiper-wrapper swiper-wrapper-category">
                <?php echo $category_option2 ?>
            </ul>
        </nav>
        <script>
            $(document).ready(function() {
                $("#bo_cate_ul li").addClass("swiper-slide swiper-slide-category");
            });

            var swiper = new Swiper('.swiper-container-category', {
                slidesPerView: 'auto', //가로갯수
                spaceBetween: 0, // 간격
                //slidesOffsetBefore: 40, //좌측여백
                //slidesOffsetAfter: 40, // 우측여백
                observer: true, //리셋
                observeParents: true, //리셋
                touchRatio: 1, // 드래그 가능여부

            });
        </script>
        <?php } ?>
        <!-- } -->

        <ul class="rb_bbs_list rb_gallery_grid" <?php if (!$is_category) { ?>style="padding-top:20px !important;"<?php } ?>>

        <?php
        for ($i=0; $i<count($list); $i++) {
            // 썸네일 이미지 가져오기
            $thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height'], false, true);

            if($thumb['src']) {
                if (strstr($list[$i]['wr_option'], 'secret')) {
                    $img_content = '<img src="'.G5_THEME_URL.'/rb.img/sec_image.png" alt="'.$thumb['alt'].'" >';
                } else {
                    $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" >';
                }
            } else {
                $img_content = '<img src="'.G5_THEME_URL.'/rb.img/no_image.png" alt="이미지가 없습니다." >';
            }

            $wr_href = $list[$i]['href'];
            $sec_txt = '<span style="opacity:0.6">작성자 및 관리자 외 열람할 수 없습니다.<br>비밀글 기능으로 보호된 글입니다.</span>';

            $wr_content = preg_replace("/<(.*?)\>/","",$list[$i]['wr_content']);
            $wr_content = preg_replace("/&nbsp;/","",$wr_content);
            $wr_content = get_text($wr_content);

            //필드분할
            $wr_1 = isset($list[$i]["wr_1"]) ? explode("|", $list[$i]["wr_1"]) : [];
            $wr_2 = isset($list[$i]["wr_2"]) ? explode("|", $list[$i]["wr_2"]) : [];
            $wr_3 = isset($list[$i]["wr_3"]) ? explode("|", $list[$i]["wr_3"]) : [];
            $wr_4 = isset($list[$i]["wr_4"]) ? explode("|", $list[$i]["wr_4"]) : [];
            $wr_5 = isset($list[$i]["wr_5"]) ? explode("|", $list[$i]["wr_5"]) : [];

            ?>
            <div class="bbs_prd_list">
                <div class="bbs_prd_list_wrap" onclick="location.href='<?php echo $wr_href ?>';">
                    <!-- 썸네일 이미지 추가 -->
                    <div class="thumbnail">
                        <?php echo $img_content; ?>
                    </div>
                    <ul class="bbs_prd_list_con">
                        <?php if(isset($wr_1[1]) && $wr_1[1] || isset($wr_1[2]) && $wr_1[2] || isset($wr_1[3]) && $wr_1[3]) { ?>
                        <li class="bbs_prd_list_con_li1 font-B">
                            <?php if(isset($wr_1[2]) && $wr_1[2]) { ?>
                                <?php echo isset($wr_1[2]) ? $wr_1[2] : ''; ?>
                            <?php } else { ?>
                                <?php echo isset($wr_1[0]) ? $wr_1[0] : ''; ?>
                                <?php if(isset($wr_1[1]) && $wr_1[1]) { ?> ~ <?php } ?>
                                <?php echo isset($wr_1[1]) ? $wr_1[1] : ''; ?>
                            <?php } ?>
                        </li>
                        <?php } ?>

                        <li class="bbs_prd_list_con_li2 cut2"><a href="<?php echo $wr_href ?>" class="font-B"><?php echo $list[$i]['subject'] ?></a></li>
                        <?php if($list[$i]['wr_8']) { ?>
                        <li class="bbs_prd_list_con_li3 cut"><?php echo $list[$i]['wr_8'] ?></li>
                        <?php } ?>

                        <div class="bbs_prd_list_con_li4 mt-10">
                            <?php if(isset($wr_4[1]) && $wr_4[1]) { ?>
                                <li><span class="font-R rc_label1"><?php echo $wr_4[1] ?></span></li>
                            <?php } else { ?>
                                <li><span class="font-R rc_label1"><?php echo $wr_4[0] ?></span></li>
                            <?php } ?>

                            <?php if(isset($wr_5[1]) && $wr_5[1]) { ?>
                                <li><span class="font-R rc_label2"><?php echo $wr_5[1] ?></span></li>
                            <?php } ?>

                            <?php if(isset($wr_5[0]) && $wr_5[0]) { ?>
                                <li><span class="font-B rc_label_txt main_color"><?php echo $wr_5[0] ?></span></li>
                            <?php } ?>
                        </div>
                    </ul>
                </div>

                <?php if(isset($list[$i]['wr_9']) && $list[$i]['wr_9']) { ?>
                    <?php if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) {  ?>
                        <div class="lists_rc_p1" onclick="panTo_<?php echo $list[$i]['wr_id'] ?>()">
                    <?php } else { ?>
                        <div class="lists_rc_p1" onclick="panTo_<?php echo $list[$i]['wr_id'] ?>()">
                    <?php } ?>
                            <li class="">
                                <dd><img src="<?php echo $board_skin_url ?>/img/ico_pin.svg" alt="위치"></dd>
                            </li>
                            <li class="font-B color-999">
                                <dd><?php echo $list[$i]['wr_9'] ?> <?php echo $list[$i]['wr_10'] ?></dd>
                            </li>
                        </div>
                    <?php if(isset($config['cf_kakao_js_apikey']) && $config['cf_kakao_js_apikey']) {  ?>
                        <script>
                            function panTo_<?php echo $list[$i]['wr_id'] ?>() {
                                // 이동할 위도 경도 위치를 생성합니다
                                var moveLatLon = new kakao.maps.LatLng(<?php echo $wr_3[4] ?>, <?php echo $wr_3[5] ?>);

                                // 지도 중심을 부드럽게 이동시킵니다
                                // 만약 이동할 거리가 지도 화면보다 크면 부드러운 효과 없이 이동합니다
                                map.setLevel(5);
                                map.panTo(moveLatLon);
                                $('html, body').animate({ scrollTop: 0 }, 'fast');

                                overlay_<?php echo $list[$i]['wr_id'] ?>.setMap(map);
                            }
                        </script>
                    <?php } ?>
                <?php } ?>

                <?php if($list[$i]['icon_new'] || $list[$i]['icon_hot'] || $list[$i]['is_notice']) { ?>
                    <li class="gallery-item-ico lists_rc_p2">
                        <?php if ($list[$i]['is_notice']) echo "<span class=\"bbs_list_label label1\">프리미엄</span>"; ?>
                        <?php if ($list[$i]['icon_new']) echo "<span class=\"bbs_list_label label3\">신규</span>"; ?>
                        <?php if ($list[$i]['icon_hot']) echo "<span class=\"bbs_list_label label\">인기</span>"; ?>
                    </li>
                <?php } ?>

                <?php if ($is_checkbox) { ?>
                    <div class="gall_chk_is">
                        <input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>" class="">
                        <label for="chk_wr_id_<?php echo $i ?>"></label>
                    </div>
                <?php } ?>

        </div>


        <?php } ?>

        </ul>

        <?php if (count($list) == 0) { echo "<div class=\"no_data\" style=\"text-align:center; padding-top:0px !important;\">데이터가 없습니다.</div>"; } ?>

        <ul class="btm_btns">

            <dd class="btm_btns_right">

                <?php if ($rss_href) { ?>
                <button type="button" name="btn_submit" class="fl_btns rss_pc" onclick="window.open('<?php echo $rss_href ?>');">
                    RSS
                </button>
                <?php } ?>

                <?php if ($write_href) { ?>
                <button type="button" name="btn_submit" class="fl_btns main_color_bg" onclick="location.href='<?php echo $write_href ?>';">
                    <img src="<?php echo $board_skin_url ?>/img/ico_write.svg" alt="글 등록">
                    <span class="font-R">글 등록</span>
                </button>
                <?php } ?>

            </dd>

            <dd class="btm_btns_left">
                <?php if ($is_admin == 'super' || $is_auth) { ?>
                <?php if ($is_checkbox) { ?>
                <button type="submit" name="btn_submit" class="fl_btns" value="선택삭제" onclick="document.pressed=this.value">
                    <span class="font-B">선택삭제</span>
                </button>

                <button type="submit" name="btn_submit" class="fl_btns" value="선택복사" onclick="document.pressed=this.value">
                    <span class="font-B">선택복사</span>
                </button>

                <button type="submit" name="btn_submit" class="fl_btns" value="선택이동" onclick="document.pressed=this.value">
                    <span class="font-B">선택이동</span>
                </button>
                <?php } ?>
                <?php } ?>

                <button type="button" name="btn_submit" class="fl_btns btn_bo_sch"><span class="font-B">검색</span></button>
            </dd>
            <dd class="cb"></dd>
        </ul>


        <!-- 페이지 -->
        <?php echo $write_pages; ?>
        <!-- 페이지 -->

    </form>

</div>

<!-- 게시판 검색 시작 { -->
<div class="bo_sch_wrap">
    <fieldset class="bo_sch">
        <h3>검색</h3>
        <legend>게시물 검색</legend>
        <form name="fsearch" method="get">
            <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
            <input type="hidden" name="sca" value="<?php echo $ca ?>">
            <input type="hidden" name="sop" value="and">
            <label for="sfl" class="sound_only">검색대상</label>

            <select name="sfl" id="sfl" class="select">
                <?php echo get_board_sfl_select_options2($sfl); ?>
            </select>

            <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
            <div class="sch_bar">
                <input type="text" name="stx" value="<?php echo stripslashes($stx); ?>" id="stx" required class="input" maxlength="20" placeholder="검색어를 입력해주세요">
                <button type="submit" value="검색" class="sch_btn" title="검색"><img src="<?php echo $board_skin_url ?>/img/ico_ser.svg" alt="검색"></button>
            </div>
            <button type="button" class="bo_sch_cls"><img src="<?php echo $board_skin_url ?>/img/icon_close.svg" alt="검색 닫기"></button>
        </form>
    </fieldset>
    <div class="bo_sch_bg"></div>
</div>
<script>
    // 게시판 검색
    $(".btn_bo_sch").on("click", function() {
        $(".bo_sch_wrap").toggle();
    })
    $('.bo_sch_bg, .bo_sch_cls').click(function() {
        $('.bo_sch_wrap').hide();
    });
</script>
<!-- } 게시판 검색 끝 -->

<?php if($is_checkbox) { ?>
<noscript>
    <p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<?php if ($is_checkbox) { ?>
<script>
    function all_checked(sw) {
        var f = document.fboardlist;

        for (var i = 0; i < f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]")
                f.elements[i].checked = sw;
        }
    }

    function fboardlist_submit(f) {
        var chk_count = 0;

        for (var i = 0; i < f.length; i++) {
            if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
                chk_count++;
        }

        if (!chk_count) {
            alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
            return false;
        }

        if (document.pressed == "선택복사") {
            select_copy("copy");
            return;
        }

        if (document.pressed == "선택이동") {
            select_copy("move");
            return;
        }

        if (document.pressed == "선택삭제") {
            if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
                return false;

            f.removeAttribute("target");
            f.action = g5_bbs_url + "/board_list_update.php";
        }

        return true;
    }

    // 선택한 게시물 복사 및 이동
    function select_copy(sw) {
        var f = document.fboardlist;

        if (sw == 'copy')
            str = "복사";
        else
            str = "이동";

        var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

        f.sw.value = sw;
        f.target = "move";
        f.action = g5_bbs_url + "/move.php";
        f.submit();
    }

    // 게시판 리스트 관리자 옵션
    jQuery(function($) {
        $(".btn_more_opt.is_list_btn").on("click", function(e) {
            e.stopPropagation();
            $(".more_opt.is_list_btn").toggle();
        });
        $(document).on("click", function(e) {
            if (!$(e.target).closest('.is_list_btn').length) {
                $(".more_opt.is_list_btn").hide();
            }
        });
    });
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->

</div>

<!-- 추가적인 JavaScript (드래그 스크롤 기능) -->
<script>
    $(document).ready(function() {
        const secondaryTabs = document.querySelector('.tab-menu.secondary-tabs');
        if (secondaryTabs) {
            let isDown = false;
            let startX;
            let scrollLeft;

            // 마우스 드래그 이벤트
            secondaryTabs.addEventListener('mousedown', (e) => {
                isDown = true;
                secondaryTabs.classList.add('active');
                startX = e.pageX - secondaryTabs.offsetLeft;
                scrollLeft = secondaryTabs.scrollLeft;
            });

            secondaryTabs.addEventListener('mouseleave', () => {
                isDown = false;
                secondaryTabs.classList.remove('active');
            });

            secondaryTabs.addEventListener('mouseup', () => {
                isDown = false;
                secondaryTabs.classList.remove('active');
            });

            secondaryTabs.addEventListener('mousemove', (e) => {
                if(!isDown) return;
                e.preventDefault();
                const x = e.pageX - secondaryTabs.offsetLeft;
                const walk = (x - startX) * 1; // 스크롤 속도 조절 (1은 기본 속도)
                secondaryTabs.scrollLeft = scrollLeft - walk;
            });

            // 터치 드래그 이벤트 (모바일 지원)
            secondaryTabs.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - secondaryTabs.offsetLeft;
                scrollLeft = secondaryTabs.scrollLeft;
            });

            secondaryTabs.addEventListener('touchend', () => {
                isDown = false;
            });

            secondaryTabs.addEventListener('touchmove', (e) => {
                if(!isDown) return;
                const x = e.touches[0].pageX - secondaryTabs.offsetLeft;
                const walk = (x - startX) * 1; // 스크롤 속도 조절
                secondaryTabs.scrollLeft = scrollLeft - walk;
            });
        }
    });
</script>

<!-- 추가적인 CSS (드래그 활성화 시 커서 변경) -->
<style>
    .tab-menu.secondary-tabs.active {
        cursor: grabbing; /* 드래그 중일 때 커서 */
    }
</style>

<!-- 게시글 목록 끝 -->


