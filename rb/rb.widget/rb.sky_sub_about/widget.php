<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PC/모바일 대응 스크롤 이미지 + 소개페이지</title>
<style>
body,html{margin:0;padding:0;font-family:sans-serif;background:#fff;}
.hero{position:relative;background:#fff;}
.hero-inner{position:sticky;top:0;display:flex;justify-content:center;align-items:center;overflow:hidden;}
.hero img{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);object-fit:cover;z-index:1;}
#overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:2;opacity:0;transition:opacity 0.5s ease;pointer-events:none;}
.hero-text{position:relative;z-index:3;text-align:left;width:600px;}
.hero-text h1{font-size:3.6rem;margin:0;}
.hero-text p{margin:5px 0 0;}
.text-before {
  position: absolute; /* 추가 */
  top: -480px;           /* 이미지 위에서 40% 위치 */
  left: 50%;          /* 가로 중앙 */
  transform: translateX(-50%); /* 중앙 정렬 */
  color: black;
  transition: opacity 0.2s;
  z-index: 3;          /* 이미지보다 위 */
  width:600px;
}
.text-after{color:white;position:absolute;top:200px;left:0%;transform:translateX(-50%);opacity:0;transition:opacity 0.2s;width:600px;}
.content{padding:50px;background:#f5f5f5;}
.content h2{font-size:2rem;margin-bottom:20px;text-align:center;}
.content .item{opacity:0;transform:translateY(30px);transition:opacity 0.8s ease, transform 0.8s ease;margin-bottom:40px;}
.content .item h3{font-size:1.5rem;margin-bottom:5px;}
.content .item p{margin:0;color:#555;line-height:1.5;}

/* PC/모바일 높이 차이 */
@media(min-width:769px){ .hero{height:200vh;} .hero-inner{height:100vh;} }
@media(max-width:768px){ .hero{height:100vh;} .hero-inner{height:100vh;} .hero img{width:100%;height:100%;} #overlay{opacity:1;} .text-before{opacity:0;} .text-after{opacity:1;position:relative;transform:none;} }
</style>
</head>
<body>

<section class="hero" id="hero">
  <div class="hero-inner">
    <img id="heroImg" src="/img/12121234324.jpg" alt="배경 이미지">
    <div id="overlay"></div>
    <div class="hero-text">
      <div class="text-before" id="textBefore">
        <h1>RIVEN BY EXPERTISE, POWERED BY TRUST</h1>
        <p>From delivery-day excitement to long-term care, we ensure your car stays at its best.</p>
      </div>
      <div class="text-after" id="textAfter">
        <h1>YOUR CAR, OUR COMMITMENT</h1>
        <p>Comprehensive auto services with expert hands, transparent pricing, and lasting quality.</p>
      </div>
    </div>
  </div>
</section>
<!--
<section class="content">
  <h2>스카이멀티 소개</h2>

  <div class="item">
    <h3>전문인력으로 구성된 최고의 서비스</h3>
    <p>숙련된 전문 엔지니어와 차량 전문가가 각 단계별로 철저히 관리하여 최상의 품질 서비스를 제공합니다.</p>
  </div>

  <div class="item">
    <h3>고객님의 차는 내 차보다 소중합니다.</h3>
    <p>차량 한 대 한 대를 내 차처럼 세심하게 점검하고 관리하여 고객님의 안전과 만족을 최우선으로 합니다.</p>
  </div>

  <div class="item">
    <h3>신차인수 받는 감동 끝까지 전달해드리겠습니다.</h3>
    <p>신차 구매의 설렘과 감동을 끝까지 유지할 수 있도록 차량 점검, 세차, 케어까지 완벽하게 지원합니다.</p>
  </div>

  <div class="item">
    <h3>원스톱 서비스</h3>
    <p>차량토탈케어/타이어/경정비/광택/스팀세차 등 체계적인 서비스로 회원님의 시간을 절약해드리는 원스톱 토탈 서비스가 준비되어 있습니다.</p>
  </div>

  <div class="item">
    <h3>철저한 사후 관리</h3>
    <p>5년,10년 이상 타는 차! 업계 최초 보증기간 2년(단, 전장제품은 1년)으로, 오랜 기간 믿고 맡기실 수 있는 철저한 사후 관리 시스템을 제공합니다.</p>
  </div>

  <div class="item">
    <h3>합리적인 가격</h3>
    <p>실속 있는 패키지와 합리적인 가격으로 풍부한 서비스를 제공하며, 회원님의 차량을 안전하게 업그레이드 해드립니다.</p>
  </div>

</section>
-->

<script>
const hero=document.getElementById("hero");
const heroImg=document.getElementById("heroImg");
const overlay=document.getElementById("overlay");
const textBefore=document.getElementById("textBefore");
const textAfter=document.getElementById("textAfter");

const startSize=600;
let currentWidth=startSize;
let currentHeight=startSize;
let delayedScrollDone=false;
let isPC=false;

function easeInOutQuart(t){return t<0.5?8*t*t*t*t:1-Math.pow(-2*t+2,4)/2;}
function smoothStep(current,target,factor=0.12){return current+(target-current)*factor;}

function startScrollAnimation(){
  isPC=true;
  function animate(){
    const scrollY=window.scrollY;
    const heroTop=hero.offsetTop;
    const heroHeight=hero.offsetHeight;
    const windowHeight=window.innerHeight;

    let progress=Math.min(Math.max((scrollY-heroTop)/(heroHeight-windowHeight),0),1);
    let eased=easeInOutQuart(progress);

    const targetWidth=startSize+eased*(window.innerWidth-startSize);
    const targetHeight=startSize+eased*(window.innerHeight-startSize);

    currentWidth=smoothStep(currentWidth,targetWidth);
    currentHeight=smoothStep(currentHeight,targetHeight);

    heroImg.style.width=`${currentWidth}px`;
    heroImg.style.height=`${currentHeight}px`;

    textBefore.style.opacity=1-eased*2;
    textAfter.style.opacity=eased>0.5?(eased-0.5)*2:0;

    overlay.style.opacity=progress>=1?1:0;

    if(progress>=1&&!delayedScrollDone){
      delayedScrollDone=true;
      setTimeout(()=>{window.scrollTo({top:heroTop+heroHeight,behavior:"smooth"});},800);
    }

    requestAnimationFrame(animate);
  }
  requestAnimationFrame(animate);
}

function showMobileComplete(){
  isPC=false;
  heroImg.style.width="100%";
  heroImg.style.height="100%";
  overlay.style.opacity=1;
  textBefore.style.opacity=0;
  textAfter.style.opacity=1;
  textAfter.style.position="relative";
  textAfter.style.transform="none";
}

// 초기 실행 및 resize 대응
function initHero(){
  if(window.innerWidth>768){
    startScrollAnimation();
  } else {
    showMobileComplete();
  }
}

window.addEventListener("load",initHero);
window.addEventListener("resize",initHero);

// 아래 컨텐츠 fade-in 애니메이션
const items=document.querySelectorAll('.content .item');
function fadeInOnScroll(){
  const windowBottom=window.innerHeight*0.85+window.scrollY;
  items.forEach(item=>{
    if(item.offsetTop<windowBottom){
      item.style.opacity=1;
      item.style.transform='translateY(0)';
    }
  });
}
window.addEventListener('scroll',fadeInOnScroll);
window.addEventListener('load',fadeInOnScroll);
</script>

</body>
</html>
