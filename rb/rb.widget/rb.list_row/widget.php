<?php
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가

//모듈정보 불러오기
$md_id = $row_mod['md_id']; //현재 모듈 ID
$rb_skin = sql_fetch (" select * from {$rb_module_table} where md_id = '{$md_id}' "); //환경설정 테이블 조회
$md_subject = $rb_skin['md_title']; //모듈 타이틀

?>

<link rel="stylesheet" href="<?php echo G5_URL ?>/rb/rb.widget/rb.list_row/style.css?ver=<?php echo G5_TIME_YMDHIS ?>">

<div class="rb_menu_list_btm">
    <ul class="rb_menu_list_btm_l">
        <li class="rb_menu_list_btm_tit">
            <dl class="rb_menu_list_btm_tit_dl">
                <dd class="font-B rb_menu_list_btm_tit_span1">부가기능 (무료)</dd>
            </dl>
        </li>
        <li class="rb_menu_list_btm_list">
            <dl class="rb_menu_list_btm_list_dl">
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">위젯</span></dd>
                <dd>
                    <a href="javascript:void(0);">새글</a>
                    <a href="javascript:void(0);">인기글</a>
                    <a href="javascript:void(0);" class="dss">날씨</a>
                    <a href="javascript:void(0);" class="dss">환율</a>
                    <a href="javascript:void(0);">아웃로그인</a>
                    <a href="javascript:void(0);">구매후기 <span class="gnb_ico gnb_new_ico_new">New</span></a>
                    <a href="javascript:void(0);">포인트 랭킹 위젯</a>
                </dd>
            </dl>
            <dl class="rb_menu_list_btm_list_dl">
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">게시판 스킨</span></dd>
                <dd>
                    <a href="javascript:void(0);">베이직</a>
                    <a href="javascript:void(0);">베이직2</a>
                    <a href="javascript:void(0);">갤러리</a>
                    <a href="javascript:void(0);">웹진</a>
                    <a href="javascript:void(0);">웹진2</a>
                    <a href="javascript:void(0);">태그</a>
                    <a href="javascript:void(0);">공지강조</a>
                    <a href="javascript:void(0);">1:1문의</a>
                    <a href="javascript:void(0);">FAQ</a>
                    <a href="javascript:void(0);" class="dss">출석부</a>
                    <a href="javascript:void(0);">이벤트</a>
                    <a href="javascript:void(0);">익명</a>
                    <a href="javascript:void(0);">리빌더 전용게시판</a>
                </dd>
            </dl>

            <dl>
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">시스템</span></dd>
                <dd>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">환경 설정</a>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">모듈 관리</a>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">배너 관리</a>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">통합 SEO 관리</a>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">게시물 관리</a>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">실시간 활동알림</a>
                    <a href="javascript:alert('빌더 설치시 기본제공 됩니다.');">미니홈</a>
                </dd>
            </dl>
            <dl>
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">마켓</span></dd>
                <dd>
                    <a href="https://demo.rebuilder.co.kr/shop/" target="_blank">기본 테마</a>
                </dd>
            </dl>
        </li>
    </ul>
    


    
    <ul class="rb_menu_list_btm_r">
        <li class="rb_menu_list_btm_tit">
            <dl class="rb_menu_list_btm_tit_dl">
                <dd class="font-B rb_menu_list_btm_tit_span1">
                부가기능 (유료)
                </dd>
            </dl>
        </li>
        <li class="rb_menu_list_btm_list">

            <dl class="rb_menu_list_btm_list_dl">
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">위젯</span></dd>
                <dd>
                    <a href="javascript:void(0);" class="dss">인기글</a>
                    <a href="javascript:void(0);" class="dss">날씨</a>
                    <a href="javascript:void(0);" class="dss">환율</a>
                    <a href="javascript:void(0);">캘린더(미니)</a>
                    <a href="javascript:void(0);">MP3 플레이어</a>
                </dd>
            </dl>

            
            <dl class="rb_menu_list_btm_list_dl">
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">게시판 스킨</span></dd>
                <dd>
                    <a href="javascript:void(0);">유튜브</a>
                    <a href="javascript:void(0);">캘린더</a>
                    <a href="javascript:void(0);">채용정보</a>
                    <a href="javascript:void(0);">컨텐츠</a>
                    <a href="javascript:void(0);">포인트자료실</a>
                    <a href="javascript:void(0);">별점</a>
                    <a href="javascript:void(0);">거래</a>
                </dd>
            </dl>
            
            
            <dl class="rb_menu_list_btm_list_dl">
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">게시판 확장기능</span></dd>
                <dd>
                    <a href="javascript:void(0);">댓글 파일첨부</a>
                    <a href="javascript:void(0);">댓글 추천</a>
                    <a href="javascript:void(0);">댓글 페이스콘</a>
                    <a href="javascript:void(0);">게시물 신고 <span class="gnb_ico gnb_new_ico_test">Debug</span></a>
                    <a href="javascript:void(0);" class="dss">이미지 워터마크</a>
                    <a href="javascript:void(0);" class="dss">맨션(@)+알림</a>
                </dd>
            </dl>

            <dl>
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">시스템</span></dd>
                <dd>
                    <a href="javascript:void(0);">자동번역</a>
                    <a href="javascript:void(0);">광고 관리 <span class="gnb_ico gnb_new_ico_test">Debug</span></a>
                    <a href="javascript:void(0);">예치금 관리</a>
                    <a href="javascript:void(0);">예약 관리 <span class="gnb_ico gnb_new_ico_test">Debug</span></a>
                    <a href="javascript:void(0);" class="dss">인트로(폐쇄형)</a>
                    <a href="javascript:void(0);" class="dss">다크모드</a>
                    <a href="javascript:void(0);" class="dss">카카오 알림톡 <span class="gnb_ico gnb_new_ico_work">work</span></a>
                    <a href="javascript:void(0);">폼 생성 관리</a>
                    <a href="javascript:void(0);">그룹팝업 관리</a>
                    <a href="javascript:void(0);" class="dss">포인트 쿠폰관리</a>
                    <a href="javascript:void(0);" class="dss">추천인 관리</a>
                    <a href="javascript:void(0);" class="dss">관리자모드 UI</a>
                    <a href="javascript:void(0);">구독+알림</a>
                    <a href="javascript:void(0);">1:1 채팅(AJAX)</a>
                    <a href="javascript:void(0);">포인트 충전/출금</a>
                    <a href="javascript:void(0);" class="dss">레벨링</a>
                    <a href="javascript:void(0);" class="dss">포인트 선물</a>
                    <a href="javascript:void(0);">SMS 인증 <span class="gnb_ico gnb_new_ico_test">Debug</span></a>
                    <a href="javascript:void(0);">SMS 비번 초기화</a>
                </dd>
            </dl>
            

            
            <dl>
                <dd class="rb_menu_list_btm_list_tit"><span class="font-B">마켓</span></dd>
                <dd>
                    <a href="javascript:void(0);">입점 시스템 <span class="gnb_ico gnb_new_ico_free">Beta</span></a>
                    <a href="javascript:void(0);" class="dss">컨텐츠 상품관리</a>
                    <a href="javascript:void(0);" class="dss">타임딜</a>
                    <a href="javascript:void(0);" class="dss">체험단 관리</a>
                    
                </dd>
            </dl>

        </li>
    </ul>
    
    
    <div class="cb"></div>
    
</div>