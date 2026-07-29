<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Taller Pro — Taller Automotriz')</title>
    <meta name="description" content="@yield('description', 'Taller automotriz profesional. Mecánica general, mantenimiento, diagnóstico y más.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --pub-bg:#080c14;--pub-surface:#0c1220;--pub-card:#111a2e;
            --pub-border:rgba(255,255,255,.06);--accent:#D71920;--accent-h:#b81218;
            --accent-glow:rgba(215,25,32,.4);--pub-text:#F1F5F9;--pub-muted:#5a6a80;
            --pub-dim:#2a3548;--font-display:'Outfit',sans-serif;
            --font-body:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html{scroll-behavior:smooth;}
        body{background:var(--pub-bg);color:var(--pub-text);font-family:var(--font-body);line-height:1.6;overflow-x:hidden;-webkit-font-smoothing:antialiased;}
        /* Grain */
        body::after{content:'';position:fixed;inset:0;z-index:9999;pointer-events:none;opacity:.03;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");background-size:180px;}
        ::selection{background:rgba(215,25,32,.3);color:#fff;}

        /* ═══ NAVBAR ═══ */
        .pub-nav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,12,20,.6);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid rgba(255,255,255,.04);transition:transform .4s cubic-bezier(.4,0,.2,1),background .3s;}
        .pub-nav.nav-hidden{transform:translateY(-100%);}
        .pub-nav.nav-scrolled{background:rgba(8,12,20,.92);}
        .pub-nav-inner{max-width:1280px;margin:0 auto;padding:0 32px;height:72px;display:flex;align-items:center;justify-content:space-between;gap:24px;}
        .pub-logo{display:flex;align-items:center;gap:12px;text-decoration:none;}
        .pub-logo-icon{width:40px;height:40px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px var(--accent-glow),0 0 60px rgba(215,25,32,.15);transition:transform .3s;}
        .pub-logo:hover .pub-logo-icon{transform:rotate(-8deg) scale(1.05);}
        .pub-logo-text{font-family:var(--font-display);font-size:20px;font-weight:800;color:#fff;letter-spacing:-.02em;}
        .pub-nav-links{display:flex;align-items:center;gap:2px;list-style:none;}
        .pub-nav-links a{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:500;color:rgba(255,255,255,.5);text-decoration:none;transition:all .2s;letter-spacing:.02em;position:relative;}
        .pub-nav-links a::after{content:'';position:absolute;bottom:2px;left:50%;width:0;height:2px;background:var(--accent);transition:all .3s;transform:translateX(-50%);border-radius:1px;}
        .pub-nav-links a:hover{color:#fff;}
        .pub-nav-links a:hover::after{width:20px;}
        .pub-nav-actions{display:flex;align-items:center;gap:10px;}
        .btn-nav-login{padding:8px 18px;border-radius:9px;font-size:13px;font-weight:600;color:#fff;border:1px solid rgba(255,255,255,.1);background:transparent;text-decoration:none;transition:.2s;}
        .btn-nav-login:hover{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.2);}
        .btn-nav-tienda{padding:9px 20px;border-radius:9px;font-size:13px;font-weight:700;color:#fff;background:var(--accent);text-decoration:none;transition:all .25s;display:flex;align-items:center;gap:7px;box-shadow:0 0 20px var(--accent-glow);animation:btnPulse 3s ease-in-out infinite;}
        @keyframes btnPulse{0%,100%{box-shadow:0 0 20px var(--accent-glow);}50%{box-shadow:0 0 32px var(--accent-glow),0 0 60px rgba(215,25,32,.15);}}
        .btn-nav-tienda:hover{background:var(--accent-h);transform:translateY(-1px);}
        .mob-menu-btn{display:none;background:none;border:none;color:rgba(255,255,255,.5);font-size:24px;cursor:pointer;}
        @media(max-width:768px){.pub-nav-links{display:none;}.btn-nav-login{display:none;}.mob-menu-btn{display:block;}.pub-nav-inner{padding:0 20px;}}

        /* ═══ SECTIONS ═══ */
        .pub-section{padding:120px 32px;}
        @media(max-width:768px){.pub-section{padding:80px 20px;}}
        .pub-container{max-width:1280px;margin:0 auto;}
        .section-label{display:inline-flex;align-items:center;gap:12px;font-family:var(--font-body);font-size:11px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:var(--accent);margin-bottom:20px;}
        .section-label::before{content:'';width:32px;height:2px;background:var(--accent);animation:labelLine 2s ease-in-out infinite;}
        @keyframes labelLine{0%,100%{width:32px;opacity:1;}50%{width:20px;opacity:.5;}}
        .section-title{font-family:var(--font-display);font-size:clamp(32px,5vw,56px);font-weight:800;line-height:1.08;color:#fff;margin-bottom:20px;letter-spacing:-.04em;}
        .section-sub{font-size:16px;color:var(--pub-muted);max-width:520px;line-height:1.8;}

        /* ═══ SCROLL ANIMATIONS — REPEATING ═══ */
        .reveal{opacity:0;transform:translateY(40px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1);will-change:opacity,transform;}
        .reveal.visible{opacity:1;transform:none;}
        .reveal-left{opacity:0;transform:translateX(-50px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1);}
        .reveal-left.visible{opacity:1;transform:none;}
        .reveal-right{opacity:0;transform:translateX(50px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1);}
        .reveal-right.visible{opacity:1;transform:none;}
        .reveal-scale{opacity:0;transform:scale(.92);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1);}
        .reveal-scale.visible{opacity:1;transform:none;}
        /* Rotate in */
        .reveal-rotate{opacity:0;transform:perspective(800px) rotateY(8deg) translateX(30px);transition:opacity .9s cubic-bezier(.16,1,.3,1),transform .9s cubic-bezier(.16,1,.3,1);}
        .reveal-rotate.visible{opacity:1;transform:none;}
        /* Blur in */
        .reveal-blur{opacity:0;filter:blur(8px);transform:translateY(20px);transition:opacity .8s,filter .8s,transform .8s;transition-timing-function:cubic-bezier(.16,1,.3,1);}
        .reveal-blur.visible{opacity:1;filter:none;transform:none;}
        /* Stagger */
        .stagger-1{transition-delay:.05s}.stagger-2{transition-delay:.1s}.stagger-3{transition-delay:.15s}
        .stagger-4{transition-delay:.2s}.stagger-5{transition-delay:.25s}.stagger-6{transition-delay:.3s}
        .stagger-7{transition-delay:.35s}.stagger-8{transition-delay:.4s}.stagger-9{transition-delay:.45s}
        .stagger-10{transition-delay:.5s}.stagger-11{transition-delay:.55s}.stagger-12{transition-delay:.6s}
        .stagger-13{transition-delay:.65s}

        /* ═══ AMBIENT ANIMATIONS ═══ */
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @keyframes floatSlow{0%,100%{transform:translate(0,0)}33%{transform:translate(10px,-8px)}66%{transform:translate(-6px,4px)}}
        @keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
        @keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        @keyframes borderGlow{0%,100%{border-color:rgba(215,25,32,.1)}50%{border-color:rgba(215,25,32,.3)}}
        @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}

        .red-line{height:3px;border-radius:2px;background:linear-gradient(90deg,var(--accent),transparent);}
    </style>
    @stack('styles')
</head>
<body>

<nav class="pub-nav" id="pubNav">
    <div class="pub-nav-inner">
        <a href="{{ url('/') }}" class="pub-logo"><div class="pub-logo-icon"><i class="bi bi-tools" style="color:#fff;font-size:17px;"></i></div><span class="pub-logo-text">Taller Pro</span></a>
        <ul class="pub-nav-links">
            <li><a href="#servicios">Servicios</a></li><li><a href="#nosotros">Nosotros</a></li>
            <li><a href="#mapa">Sucursales</a></li><li><a href="#contacto">Contacto</a></li>
        </ul>
        <div class="pub-nav-actions">
            @auth<a href="{{ route('cliente.inicio') }}" class="btn-nav-login"><i class="bi bi-person-circle" style="font-size:14px;"></i> Mi cuenta</a>
            @else<a href="{{ route('login') }}" class="btn-nav-login">Iniciar sesión</a>@endauth
            <a href="{{ route('tienda') }}" class="btn-nav-tienda"><i class="bi bi-shop-window" style="font-size:14px;"></i> Tienda</a>
        </div>
        <button class="mob-menu-btn" onclick="toggleMobMenu()"><i class="bi bi-list" id="mobMenuIcon"></i></button>
    </div>
    <div id="mobMenu" style="display:none;background:rgba(8,12,20,.98);border-top:1px solid rgba(255,255,255,.04);padding:12px 20px 20px;">
        <ul style="list-style:none;display:flex;flex-direction:column;gap:2px;margin-bottom:16px;">
            <li><a href="#servicios" onclick="closeMobMenu()" style="display:block;padding:12px 10px;color:rgba(255,255,255,.5);text-decoration:none;font-size:15px;">Servicios</a></li>
            <li><a href="#nosotros" onclick="closeMobMenu()" style="display:block;padding:12px 10px;color:rgba(255,255,255,.5);text-decoration:none;font-size:15px;">Nosotros</a></li>
            <li><a href="#mapa" onclick="closeMobMenu()" style="display:block;padding:12px 10px;color:rgba(255,255,255,.5);text-decoration:none;font-size:15px;">Sucursales</a></li>
            <li><a href="#contacto" onclick="closeMobMenu()" style="display:block;padding:12px 10px;color:rgba(255,255,255,.5);text-decoration:none;font-size:15px;">Contacto</a></li>
        </ul>
        <div style="display:flex;gap:10px;">
            @auth<a href="{{ route('cliente.inicio') }}" style="flex:1;text-align:center;padding:12px;border-radius:9px;border:1px solid rgba(255,255,255,.08);color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Mi cuenta</a>
            @else<a href="{{ route('login') }}" style="flex:1;text-align:center;padding:12px;border-radius:9px;border:1px solid rgba(255,255,255,.08);color:#fff;text-decoration:none;font-size:13px;font-weight:600;">Iniciar sesión</a>@endauth
            <a href="{{ route('tienda') }}" style="flex:1;text-align:center;padding:12px;border-radius:9px;background:var(--accent);color:#fff;text-decoration:none;font-size:13px;font-weight:700;">Tienda</a>
        </div>
    </div>
</nav>

<div style="padding-top:72px;">@yield('content')</div>

<script>
function toggleMobMenu(){var m=document.getElementById('mobMenu'),o=m.style.display==='block';m.style.display=o?'none':'block';document.getElementById('mobMenuIcon').className=o?'bi bi-list':'bi bi-x-lg';}
function closeMobMenu(){document.getElementById('mobMenu').style.display='none';document.getElementById('mobMenuIcon').className='bi bi-list';}

/* Smart navbar */
(function(){var last=0,nav=document.getElementById('pubNav');window.addEventListener('scroll',function(){var c=window.scrollY;if(c>80){nav.classList.add('nav-scrolled');if(c>last&&c>200)nav.classList.add('nav-hidden');else nav.classList.remove('nav-hidden');}else{nav.classList.remove('nav-scrolled','nav-hidden');}last=c;});})();

/* ═══ SCROLL REVEAL — REPEATING (toggle on/off) ═══ */
(function(){
    var els=document.querySelectorAll('.reveal,.reveal-left,.reveal-right,.reveal-scale,.reveal-rotate,.reveal-blur');
    if(!els.length)return;
    var obs=new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if(e.isIntersecting){
                e.target.classList.add('visible');
            } else {
                // Remove visible so it animates again when scrolling back
                e.target.classList.remove('visible');
            }
        });
    },{threshold:0.1,rootMargin:'0px 0px -50px 0px'});
    els.forEach(function(el){obs.observe(el);});
})();

/* Counter animation — repeating */
function animateCounters(container){
    var counters=container?container.querySelectorAll('[data-count]'):document.querySelectorAll('[data-count]');
    counters.forEach(function(el){
        var t=parseInt(el.dataset.count,10),s=el.dataset.suffix||'',d=1600,st=null;
        el.textContent='0'+s;
        function step(ts){if(!st)st=ts;var p=Math.min((ts-st)/d,1),e=1-Math.pow(1-p,4),c=Math.floor(e*t);el.textContent=c+s;if(p<1)requestAnimationFrame(step);else el.textContent=t+s;}
        requestAnimationFrame(step);
    });
}
var cObs=new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
        if(entry.isIntersecting){animateCounters(entry.target);}
    });
},{threshold:0.2});
document.querySelectorAll('[data-counter-trigger]').forEach(function(el){cObs.observe(el);});

/* ═══ PARALLAX on scroll ═══ */
(function(){
    var parallaxEls=document.querySelectorAll('[data-parallax]');
    if(!parallaxEls.length)return;
    window.addEventListener('scroll',function(){
        var scrollY=window.scrollY;
        parallaxEls.forEach(function(el){
            var speed=parseFloat(el.dataset.parallax)||0.3;
            var rect=el.getBoundingClientRect();
            var offset=(rect.top+rect.height/2-window.innerHeight/2)*speed;
            el.style.transform='translateY('+offset+'px)';
        });
    },{passive:true});
})();

/* ═══ Mouse glow follower ═══ */
(function(){
    var glow=document.getElementById('mouseGlow');
    if(!glow)return;
    document.addEventListener('mousemove',function(e){
        glow.style.left=e.clientX+'px';
        glow.style.top=e.clientY+'px';
    },{passive:true});
})();

/* ═══ Tilt effect on hero card ═══ */
(function(){
    var card=document.getElementById('heroCard');
    if(!card)return;
    card.addEventListener('mousemove',function(e){
        var rect=card.getBoundingClientRect();
        var x=(e.clientX-rect.left)/rect.width-.5;
        var y=(e.clientY-rect.top)/rect.height-.5;
        card.style.transform='perspective(600px) rotateY('+x*8+'deg) rotateX('+(-y*8)+'deg)';
    });
    card.addEventListener('mouseleave',function(){card.style.transform='perspective(600px) rotateY(0) rotateX(0)';});
})();
</script>
@stack('scripts')
</body>
</html>