function chk_captcha() {
    var response = document.querySelector('input[name="cf-turnstile-response"]');
    if (!response || !response.value) {
        alert('자동등록방지 확인을 완료해 주세요.');
        return false;
    }
    return true;
}

