# Cloudflare Turnstile captcha

그누보드의 기존 captcha 함수 인터페이스를 유지하는 Turnstile 플러그인입니다.

## 안전한 전환 순서

1. Cloudflare Turnstile에서 `skymulty.co.kr`, `www.skymulty.co.kr`용 Managed 위젯을 생성합니다.
2. `data/turnstile.config.php`에 Site key와 Secret key를 입력합니다.
3. Cloudflare 테스트 키 또는 별도 점검 환경에서 화면 출력과 서버 검증을 확인합니다.
4. `enabled`를 `true`로 변경합니다.
5. 관리자 > 환경설정 > 캡챠 선택에서 `Cloudflare Turnstile`을 선택합니다.
6. 비회원 글쓰기, 댓글, 회원가입, 비밀번호 찾기를 각각 점검합니다.

## 즉시 롤백

관리자 > 환경설정 > 캡챠 선택을 `Kcaptcha`로 되돌립니다.

관리자 화면을 사용할 수 없으면 DB의 환경설정 테이블에서 `cf_captcha` 값을
`kcaptcha`로 되돌리면 됩니다. 플러그인 파일은 삭제하지 않아도 됩니다.

## 장애 원칙

키 누락, API 통신 실패, hostname/action 불일치 시 검증은 실패합니다(fail closed).
운영 전환 전에 호스팅 서버에서 Cloudflare HTTPS API 호출이 가능한지 확인해야 합니다.

