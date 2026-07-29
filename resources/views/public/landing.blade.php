@extends('layouts.public')

@section('title', 'Taller Pro — Taller Automotriz Profesional')
@section('description', 'Mecánica general, mantenimiento, diagnóstico electrónico, chapa y pintura. Tu vehículo en las mejores manos.')

@push('styles')
<style>
/* ═══════════════════════════════════════════
   LANDING V3 — Maximum animations & dynamics
   ═══════════════════════════════════════════ */

/* Mouse glow follower */
.mouse-glow{position:fixed;width:500px;height:500px;border-radius:50%;pointer-events:none;z-index:1;
    background:radial-gradient(circle,rgba(215,25,32,.06) 0%,transparent 70%);
    transform:translate(-50%,-50%);transition:left .3s ease-out,top .3s ease-out;opacity:.7;}

/* ── HERO ── */
.hero{min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;padding:0 32px;}
.hero-bg-layer{position:absolute;inset:0;pointer-events:none;
    background:radial-gradient(ellipse 80% 70% at 25% 40%,rgba(215,25,32,.08) 0%,transparent 60%),
               radial-gradient(ellipse 60% 60% at 75% 60%,rgba(20,40,100,.1) 0%,transparent 60%),
               radial-gradient(ellipse 40% 40% at 50% 100%,rgba(215,25,32,.04) 0%,transparent 50%);
    animation:bgPulse 8s ease-in-out infinite;}
@keyframes bgPulse{0%,100%{opacity:1;}50%{opacity:.6;}}

.hero-grid-pattern{position:absolute;inset:0;opacity:.02;pointer-events:none;
    background-image:linear-gradient(rgba(255,255,255,.1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.1) 1px,transparent 1px);
    background-size:80px 80px;animation:gridMove 20s linear infinite;}
@keyframes gridMove{0%{background-position:0 0;}100%{background-position:80px 80px;}}

/* Floating orbs */
.hero-orb{position:absolute;border-radius:50%;pointer-events:none;filter:blur(90px);}
.hero-orb-1{width:450px;height:450px;background:rgba(215,25,32,.12);top:5%;left:-8%;animation:orbFloat1 10s ease-in-out infinite;}
.hero-orb-2{width:350px;height:350px;background:rgba(30,60,180,.08);bottom:5%;right:-5%;animation:orbFloat2 12s ease-in-out infinite;}
.hero-orb-3{width:200px;height:200px;background:rgba(215,25,32,.06);top:60%;left:40%;animation:orbFloat3 8s ease-in-out infinite;}
@keyframes orbFloat1{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(40px,-30px) scale(1.1);}}
@keyframes orbFloat2{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(-30px,20px) scale(1.15);}}
@keyframes orbFloat3{0%,100%{transform:translate(0,0);}33%{transform:translate(20px,-15px);}66%{transform:translate(-15px,10px);}}

/* Vertical deco lines */
.hero-vline{position:absolute;top:0;width:1px;height:100%;pointer-events:none;}
.hero-vline-1{left:10%;background:linear-gradient(180deg,transparent,rgba(215,25,32,.12) 30%,rgba(215,25,32,.12) 70%,transparent);animation:lineGlow 4s ease-in-out infinite;}
.hero-vline-2{right:35%;background:linear-gradient(180deg,transparent,rgba(255,255,255,.03) 40%,rgba(255,255,255,.03) 60%,transparent);}
@keyframes lineGlow{0%,100%{opacity:.3;}50%{opacity:1;}}
/* Animated dot on line */
.hero-vline-dot{position:absolute;left:10%;width:5px;height:5px;background:var(--accent);border-radius:50%;box-shadow:0 0 10px var(--accent);animation:dotTravel 6s linear infinite;margin-left:-2px;}
@keyframes dotTravel{0%{top:-5px;}100%{top:100%;}}
@media(max-width:900px){.hero-vline,.hero-vline-dot{display:none;}}

.hero-inner{max-width:1280px;margin:0 auto;position:relative;z-index:2;width:100%;
    display:grid;grid-template-columns:1.15fr .85fr;gap:48px;align-items:center;padding:120px 0 80px;}
@media(max-width:900px){.hero-inner{grid-template-columns:1fr;gap:40px;padding:100px 0 60px;}}

/* Hero badge with shimmer */
.hero-badge{display:inline-flex;align-items:center;gap:9px;
    background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.15);
    color:var(--accent);padding:8px 18px;border-radius:100px;
    font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;
    font-family:var(--font-body);margin-bottom:28px;
    opacity:0;animation:fadeUp .8s .2s forwards;
    position:relative;overflow:hidden;}
.hero-badge::after{content:'';position:absolute;top:0;left:-100%;width:60%;height:100%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,.1),transparent);
    animation:shimmerBadge 3s infinite;}
@keyframes shimmerBadge{0%{left:-100%;}100%{left:200%;}}
.hero-badge-dot{width:6px;height:6px;background:var(--accent);border-radius:50%;box-shadow:0 0 10px var(--accent);animation:pulseDot 2s infinite;}
@keyframes pulseDot{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(1.6);}}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:none;}}

/* Oversized title */
.hero-title{font-family:var(--font-display);font-size:clamp(42px,6.5vw,82px);font-weight:900;line-height:.98;letter-spacing:-.05em;color:#fff;margin-bottom:6px;
    opacity:0;animation:fadeUp 1s .35s forwards;}
.hero-title-outline{font-family:var(--font-display);font-size:clamp(42px,6.5vw,82px);font-weight:900;line-height:.98;letter-spacing:-.05em;
    color:transparent;-webkit-text-stroke:1.5px rgba(255,255,255,.2);margin-bottom:8px;
    opacity:0;animation:fadeUp 1s .5s forwards;}
.hero-title em,.hero-title-outline em{font-style:normal;color:var(--accent);-webkit-text-stroke:0;
    text-shadow:0 0 60px rgba(215,25,32,.4);animation:titleGlow 3s ease-in-out infinite;}
@keyframes titleGlow{0%,100%{text-shadow:0 0 40px rgba(215,25,32,.3);}50%{text-shadow:0 0 80px rgba(215,25,32,.5),0 0 120px rgba(215,25,32,.2);}}

.hero-sub{font-size:16px;color:var(--pub-muted);line-height:1.8;margin:24px 0 32px;max-width:440px;opacity:0;animation:fadeUp .8s .65s forwards;}
.hero-info{display:flex;flex-direction:column;gap:9px;margin-bottom:36px;opacity:0;animation:fadeUp .8s .8s forwards;}
.hero-info-item{display:flex;align-items:center;gap:10px;font-size:13.5px;color:rgba(255,255,255,.35);transition:all .3s;}
.hero-info-item:hover{color:rgba(255,255,255,.7);padding-left:6px;}
.hero-info-item i{color:var(--accent);font-size:13px;width:16px;text-align:center;transition:transform .3s;}
.hero-info-item:hover i{transform:scale(1.3);}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;opacity:0;animation:fadeUp .8s .95s forwards;}
.btn-hero-primary{display:inline-flex;align-items:center;gap:8px;padding:15px 30px;border-radius:12px;font-size:14px;font-weight:700;
    font-family:var(--font-display);background:var(--accent);color:#fff;text-decoration:none;border:none;cursor:pointer;
    box-shadow:0 0 24px var(--accent-glow);transition:all .3s;position:relative;overflow:hidden;}
.btn-hero-primary::after{content:'';position:absolute;top:50%;left:50%;width:0;height:0;background:rgba(255,255,255,.15);border-radius:50%;transition:all .5s;transform:translate(-50%,-50%);}
.btn-hero-primary:hover::after{width:300px;height:300px;}
.btn-hero-primary:hover{transform:translateY(-3px);box-shadow:0 0 40px var(--accent-glow),0 0 80px rgba(215,25,32,.15);}
.btn-whatsapp{display:inline-flex;align-items:center;gap:8px;padding:14px 26px;border-radius:12px;font-size:14px;font-weight:700;
    background:#25D366;color:#fff;text-decoration:none;font-family:var(--font-display);box-shadow:0 0 20px rgba(37,211,102,.2);transition:all .3s;position:relative;overflow:hidden;}
.btn-whatsapp::after{content:'';position:absolute;top:50%;left:50%;width:0;height:0;background:rgba(255,255,255,.15);border-radius:50%;transition:all .5s;transform:translate(-50%,-50%);}
.btn-whatsapp:hover::after{width:300px;height:300px;}
.btn-whatsapp:hover{transform:translateY(-3px);box-shadow:0 0 32px rgba(37,211,102,.3);}
.btn-hero-outline{display:inline-flex;align-items:center;gap:8px;padding:14px 24px;border-radius:12px;font-size:14px;font-weight:600;
    border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.7);text-decoration:none;transition:all .3s;font-family:var(--font-display);}
.btn-hero-outline:hover{background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.2);color:#fff;transform:translateY(-2px);}
.btn-hero-outline i{transition:transform .3s;}
.btn-hero-outline:hover i{transform:translateY(3px);}

/* Hero card with 3D tilt */
.hero-visual{opacity:0;animation:fadeUp 1.2s .5s forwards;}
.hero-card{width:100%;max-width:400px;margin:0 auto;
    background:rgba(255,255,255,.025);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);
    border:1px solid rgba(255,255,255,.06);border-radius:28px;padding:36px;position:relative;overflow:hidden;
    box-shadow:0 40px 100px rgba(0,0,0,.6),0 0 0 1px rgba(255,255,255,.03) inset;
    transition:transform .15s ease-out,box-shadow .3s;animation:borderGlow 4s ease-in-out infinite;}
.hero-card::before{content:'';position:absolute;top:-80px;right:-80px;width:240px;height:240px;
    background:radial-gradient(circle,rgba(215,25,32,.12) 0%,transparent 70%);pointer-events:none;animation:orbFloat3 6s ease-in-out infinite;}
.hero-card::after{content:'';position:absolute;bottom:-80px;left:-80px;width:200px;height:200px;
    background:radial-gradient(circle,rgba(30,60,150,.08) 0%,transparent 70%);pointer-events:none;}
/* Spinning deco ring */
.hero-card-ring{position:absolute;top:-40px;right:-40px;width:120px;height:120px;border:1px solid rgba(215,25,32,.08);border-radius:50%;animation:spin 30s linear infinite;pointer-events:none;}
.hero-card-ring::before{content:'';position:absolute;top:0;left:50%;width:6px;height:6px;background:var(--accent);border-radius:50%;margin-left:-3px;margin-top:-3px;}

.hc-header{display:flex;align-items:center;gap:14px;margin-bottom:10px;position:relative;z-index:1;}
.hc-icon{width:48px;height:48px;background:rgba(215,25,32,.1);border-radius:14px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(215,25,32,.15);animation:float 4s ease-in-out infinite;}
.hc-icon i{color:var(--accent);font-size:21px;}
.hc-title{font-family:var(--font-display);font-size:17px;font-weight:800;color:#fff;}
.hc-subtitle{font-size:12px;color:var(--pub-muted);margin-top:1px;}
.hc-status{display:flex;align-items:center;gap:8px;margin-top:8px;position:relative;z-index:1;}
.hc-status-dot{width:8px;height:8px;background:#10B981;border-radius:50%;box-shadow:0 0 10px #10B981;animation:pulseDot 2s infinite;}
.hc-status-text{font-size:12.5px;font-weight:600;color:#10B981;}
.stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:24px;position:relative;z-index:1;}
.stat-item{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:16px;padding:20px 16px;text-align:center;transition:all .3s;animation:borderGlow 4s ease-in-out infinite;}
.stat-item:nth-child(2){animation-delay:1s;}.stat-item:nth-child(3){animation-delay:2s;}.stat-item:nth-child(4){animation-delay:3s;}
.stat-item:hover{border-color:rgba(215,25,32,.3);background:rgba(215,25,32,.05);transform:translateY(-3px);}
.stat-num{font-family:var(--font-display);font-size:32px;font-weight:900;color:#fff;line-height:1;}
.stat-num span{color:var(--accent);}
.stat-label{font-size:11px;color:var(--pub-muted);margin-top:6px;font-weight:500;}

/* ═══ MARQUEE ═══ */
.marquee-band{overflow:hidden;white-space:nowrap;border-top:1px solid rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.04);background:rgba(255,255,255,.01);padding:18px 0;}
.marquee-track{display:inline-flex;animation:marquee 25s linear infinite;}
.marquee-track:hover{animation-play-state:paused;}
@keyframes marquee{0%{transform:translateX(0);}100%{transform:translateX(-50%);}}
.marquee-item{font-family:var(--font-display);font-size:13px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:rgba(255,255,255,.1);padding:0 40px;display:inline-flex;align-items:center;gap:40px;transition:color .3s;}
.marquee-item:hover{color:var(--accent);}
.marquee-item::after{content:'◆';font-size:6px;color:var(--accent);opacity:.5;}

/* ═══ STATS ═══ */
.stats-bar{position:relative;padding:56px 32px;background:var(--pub-surface);border-top:1px solid rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.04);}
.stats-bar::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent 10%,var(--accent) 50%,transparent 90%);opacity:.3;animation:lineSlide 4s ease-in-out infinite;}
@keyframes lineSlide{0%,100%{opacity:.3;}50%{opacity:.6;}}
.stats-bar-inner{max-width:1280px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr);gap:0;text-align:center;}
@media(max-width:600px){.stats-bar-inner{grid-template-columns:repeat(2,1fr);gap:32px 0;}}
.sbar-item{position:relative;padding:0 24px;}
.sbar-item:not(:last-child)::after{content:'';position:absolute;top:10%;right:0;width:1px;height:80%;background:rgba(255,255,255,.05);}
@media(max-width:600px){.sbar-item:nth-child(2)::after{display:none;}}
.sbar-num{font-family:var(--font-display);font-size:clamp(36px,5vw,52px);font-weight:900;color:#fff;letter-spacing:-.03em;line-height:1;}
.sbar-num span{color:var(--accent);text-shadow:0 0 30px rgba(215,25,32,.3);}
.sbar-label{font-size:12px;color:var(--pub-muted);margin-top:8px;font-weight:500;}

/* ═══ SERVICES ═══ */
.services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;margin-top:56px;}
.service-card{background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.04);border-radius:20px;padding:32px 24px;
    transition:all .4s cubic-bezier(.4,0,.2,1);cursor:default;position:relative;overflow:hidden;}
.service-card::before{content:'';position:absolute;inset:0;background:linear-gradient(160deg,rgba(215,25,32,.08) 0%,transparent 50%);opacity:0;transition:opacity .4s;}
.service-card::after{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;
    background:conic-gradient(from 0deg,transparent,rgba(215,25,32,.08),transparent,transparent);
    animation:spin 8s linear infinite;opacity:0;transition:opacity .4s;}
.service-card:hover{transform:translateY(-8px) scale(1.02);border-color:rgba(215,25,32,.2);
    box-shadow:0 24px 60px rgba(0,0,0,.4),0 0 0 1px rgba(215,25,32,.1);background:rgba(255,255,255,.035);}
.service-card:hover::before{opacity:1;}
.service-card:hover::after{opacity:1;}
.service-icon{width:52px;height:52px;border-radius:14px;background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.1);
    display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:22px;margin-bottom:18px;
    position:relative;z-index:1;transition:all .4s;}
.service-card:hover .service-icon{background:rgba(215,25,32,.15);box-shadow:0 0 24px rgba(215,25,32,.2);transform:scale(1.1) rotate(-5deg);}
.service-name{font-family:var(--font-display);font-size:15px;font-weight:600;color:rgba(255,255,255,.85);position:relative;z-index:1;}

/* ═══ QUOTE ═══ */
.quote-section{position:relative;padding:100px 32px;text-align:center;overflow:hidden;
    background:linear-gradient(180deg,var(--pub-bg),var(--pub-surface) 50%,var(--pub-bg));}
.quote-deco{display:flex;align-items:center;justify-content:center;gap:16px;margin-bottom:32px;}
.quote-deco-line{width:60px;height:2px;background:var(--accent);border-radius:1px;}
.quote-deco-diamond{width:8px;height:8px;background:var(--accent);transform:rotate(45deg);animation:pulseDot 3s ease-in-out infinite;}
.quote-text{font-family:var(--font-display);font-size:clamp(22px,3.5vw,38px);font-weight:300;font-style:italic;
    color:rgba(255,255,255,.25);line-height:1.5;max-width:750px;margin:0 auto;letter-spacing:-.02em;}
.quote-text em{font-style:normal;color:var(--accent);font-weight:600;position:relative;}
.quote-text em::after{content:'';position:absolute;bottom:-4px;left:0;right:0;height:2px;background:var(--accent);opacity:.3;border-radius:1px;}
/* Big background text */
.quote-bg-text{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    font-family:var(--font-display);font-size:clamp(80px,15vw,200px);font-weight:900;
    color:rgba(255,255,255,.015);pointer-events:none;white-space:nowrap;letter-spacing:-.05em;}

/* ═══ NOSOTROS ═══ */
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center;}
@media(max-width:900px){.about-grid{grid-template-columns:1fr;gap:40px;}}
.feature-list{display:flex;flex-direction:column;position:relative;padding-left:24px;}
.feature-list::before{content:'';position:absolute;left:0;top:20px;bottom:20px;width:1px;
    background:linear-gradient(180deg,transparent,rgba(215,25,32,.25) 20%,rgba(215,25,32,.25) 80%,transparent);}
/* Animated scanner on the line */
.feature-list::after{content:'';position:absolute;left:-2px;width:5px;height:40px;border-radius:3px;
    background:linear-gradient(180deg,transparent,var(--accent),transparent);
    animation:scanLine 4s ease-in-out infinite;opacity:.6;}
@keyframes scanLine{0%{top:20px;}100%{top:calc(100% - 60px);}}
.feature-item{display:flex;align-items:flex-start;gap:18px;padding:18px 0;position:relative;}
.feature-item::before{content:'';position:absolute;left:-24px;top:30px;width:9px;height:9px;border-radius:50%;
    background:var(--accent);box-shadow:0 0 10px var(--accent-glow);z-index:1;transition:all .3s;}
.feature-item:hover::before{box-shadow:0 0 20px var(--accent-glow);transform:scale(1.4);}
.feature-icon{width:44px;height:44px;border-radius:12px;background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.1);
    display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .3s;}
.feature-item:hover .feature-icon{background:rgba(215,25,32,.12);box-shadow:0 0 16px rgba(215,25,32,.15);transform:rotate(-5deg) scale(1.05);}
.feature-icon i{color:var(--accent);font-size:17px;}
.feature-title{font-family:var(--font-display);font-size:15px;font-weight:700;color:#fff;margin-bottom:5px;}
.feature-desc{font-size:13px;color:var(--pub-muted);line-height:1.6;}

.about-visual-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.about-visual-item{aspect-ratio:1;border-radius:20px;background:linear-gradient(135deg,rgba(255,255,255,.02),rgba(255,255,255,.01));
    border:1px solid rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;
    overflow:hidden;position:relative;transition:all .4s;animation:borderGlow 5s ease-in-out infinite;}
.about-visual-item:nth-child(1){animation-delay:0s;}.about-visual-item:nth-child(2){transform:translateY(24px);animation-delay:1.2s;}
.about-visual-item:nth-child(3){animation-delay:2.5s;}.about-visual-item:nth-child(4){transform:translateY(24px);animation-delay:3.7s;}
.about-visual-item:hover{border-color:rgba(215,25,32,.2);transform:scale(1.03);}
.about-visual-item:nth-child(2):hover,.about-visual-item:nth-child(4):hover{transform:translateY(24px) scale(1.03);}
.about-visual-item i{font-size:32px;color:rgba(255,255,255,.08);}
.about-visual-item span{font-size:11px;color:rgba(255,255,255,.1);}

/* ═══ MAP ═══ */
#landingMap{height:480px;width:100%;border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,.04);box-shadow:0 24px 64px rgba(0,0,0,.4);}
.map-card{background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.04);border-radius:16px;padding:20px 22px;cursor:pointer;transition:all .35s;}
.map-card:hover{border-color:rgba(215,25,32,.25);transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.3);}

/* ═══ CTA ═══ */
.cta-section{position:relative;overflow:hidden;padding:120px 32px;background:var(--pub-surface);}
.cta-section::before{content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(215,25,32,.06) 0%,transparent 70%);
    pointer-events:none;animation:ctaPulse 5s ease-in-out infinite;}
@keyframes ctaPulse{0%,100%{opacity:.6;}50%{opacity:1;}}
.cta-section::after{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--accent),transparent);opacity:.25;}
/* Animated circles */
.cta-circle{position:absolute;border-radius:50%;border:1px solid rgba(215,25,32,.06);pointer-events:none;}
.cta-circle-1{width:400px;height:400px;top:-100px;right:-100px;animation:spin 40s linear infinite;}
.cta-circle-2{width:300px;height:300px;bottom:-80px;left:-80px;animation:spin 30s linear infinite reverse;}
.cta-title{font-family:var(--font-display);font-size:clamp(34px,5vw,60px);font-weight:900;line-height:1.05;letter-spacing:-.04em;}
.cta-title em{font-style:normal;color:var(--accent);text-shadow:0 0 40px rgba(215,25,32,.25);animation:titleGlow 3s ease-in-out infinite;}
.contact-box{text-align:center;transition:transform .3s;}
.contact-box:hover{transform:translateY(-6px);}
.contact-icon{width:52px;height:52px;border-radius:16px;background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.1);
    display:flex;align-items:center;justify-content:center;margin:0 auto 12px;transition:all .3s;}
.contact-box:hover .contact-icon{background:rgba(215,25,32,.15);box-shadow:0 0 24px rgba(215,25,32,.15);transform:rotate(-8deg) scale(1.1);}
.contact-icon i{font-size:20px;color:var(--accent);}
.contact-text{font-size:13px;color:var(--pub-muted);}

/* ═══ FOOTER ═══ */
.pub-footer{background:var(--pub-bg);border-top:1px solid rgba(255,255,255,.04);padding:64px 32px 36px;}
.pub-footer-inner{max-width:1280px;margin:0 auto;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:56px;margin-bottom:56px;}
@media(max-width:700px){.footer-grid{grid-template-columns:1fr;gap:36px;}}
.footer-brand-desc{font-size:14px;color:var(--pub-muted);line-height:1.8;max-width:280px;}
.footer-socials{display:flex;gap:10px;margin-top:20px;}
.footer-social{width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);
    display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.35);text-decoration:none;font-size:15px;transition:all .3s;}
.footer-social:hover{color:#fff;background:rgba(215,25,32,.1);border-color:rgba(215,25,32,.2);transform:translateY(-3px) rotate(-5deg);}
.footer-col-title{font-family:var(--font-display);font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.8);margin-bottom:18px;}
.footer-link{display:block;color:var(--pub-muted);text-decoration:none;font-size:13.5px;padding:5px 0;transition:all .2s;position:relative;}
.footer-link::before{content:'→';position:absolute;left:-16px;opacity:0;color:var(--accent);transition:all .2s;}
.footer-link:hover{color:#fff;padding-left:8px;}
.footer-link:hover::before{left:0;opacity:1;}
.footer-contact{display:flex;align-items:center;gap:10px;font-size:13.5px;color:var(--pub-muted);padding:5px 0;}
.footer-contact i{color:var(--accent);font-size:13px;flex-shrink:0;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.04);padding-top:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
.footer-bottom p,.footer-bottom a{font-size:12px;color:rgba(255,255,255,.2);text-decoration:none;}
.footer-bottom a:hover{color:var(--accent);}
</style>
@endpush

@section('content')

{{-- Mouse glow --}}
<div class="mouse-glow" id="mouseGlow"></div>

{{-- ═══ HERO ═══ --}}
<section class="hero">
    <div class="hero-bg-layer"></div>
    <div class="hero-grid-pattern"></div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="hero-orb hero-orb-3"></div>
    <div class="hero-vline hero-vline-1"></div>
    <div class="hero-vline hero-vline-2"></div>
    <div class="hero-vline-dot"></div>

    <div class="hero-inner">
        <div>
            <div class="hero-badge"><span class="hero-badge-dot"></span> Taller Automotriz Profesional</div>
            <h1 class="hero-title">Nos encargamos</h1>
            <p class="hero-title-outline">del cuidado integral</p>
            <h1 class="hero-title">de <em>tu vehículo</em></h1>
            <p class="hero-sub">Servicio de calidad, diagnóstico preciso y atención personalizada.<br>Tu auto en las mejores manos desde el primer día.</p>
            <div class="hero-info">
                <div class="hero-info-item"><i class="bi bi-geo-alt-fill"></i><span>Calle Primavera esq. Lluvia de Oro — 5to anillo Radial 26, Santa Cruz, Bolivia</span></div>
                <div class="hero-info-item"><i class="bi bi-telephone-fill"></i><span>78559066 / 704-07035</span></div>
                <div class="hero-info-item"><i class="bi bi-clock-fill"></i><span>Lun — Sáb: 8:00 am – 6:00 pm</span></div>
            </div>
            <div class="hero-actions">
                <a href="{{ auth()->check() ? route('cliente.inicio') : route('register') }}" class="btn-hero-primary"><i class="bi bi-calendar-check"></i> Solicitar cita</a>
                <a href="https://wa.me/59178559066" target="_blank" class="btn-whatsapp"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                <a href="#servicios" class="btn-hero-outline">Ver servicios <i class="bi bi-arrow-down"></i></a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card" id="heroCard">
                <div class="hero-card-ring"></div>
                <div class="hc-header">
                    <div class="hc-icon"><i class="bi bi-tools"></i></div>
                    <div><p class="hc-title">Taller Pro</p><p class="hc-subtitle">Sistema automotriz</p></div>
                </div>
                <div class="red-line" style="margin:20px 0;"></div>
                <p style="font-size:10px;color:var(--pub-muted);letter-spacing:.14em;text-transform:uppercase;font-weight:600;margin-bottom:6px;position:relative;z-index:1;">Estado del sistema</p>
                <div class="hc-status"><span class="hc-status-dot"></span><span class="hc-status-text">Operativo — Atendiendo ahora</span></div>
                <div class="stat-grid" data-counter-trigger>
                    <div class="stat-item"><div class="stat-num"><span data-count="{{ $stats['ordenes_activas'] ?? 0 }}" data-suffix="+">0</span></div><div class="stat-label">Órdenes activas</div></div>
                    <div class="stat-item"><div class="stat-num"><span data-count="{{ $stats['mecanicos'] ?? 0 }}" data-suffix="+">0</span></div><div class="stat-label">Mecánicos</div></div>
                    <div class="stat-item"><div class="stat-num"><span data-count="{{ $stats['clientes'] ?? 0 }}" data-suffix="+">0</span></div><div class="stat-label">Clientes</div></div>
                    <div class="stat-item"><div class="stat-num"><span data-count="{{ $sucursales->count() }}" data-suffix="+">0</span></div><div class="stat-label">Sucursales</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══ MARQUEE ═══ --}}
<div class="marquee-band">
    <div class="marquee-track">
        @for($i = 0; $i < 2; $i++)
        <span class="marquee-item">Mecánica General</span><span class="marquee-item">Mantenimiento</span>
        <span class="marquee-item">Diagnóstico Electrónico</span><span class="marquee-item">Chapa y Pintura</span>
        <span class="marquee-item">Detailing</span><span class="marquee-item">Ceramic Coating</span>
        <span class="marquee-item">Aire Acondicionado</span><span class="marquee-item">Importación Autopartes</span>
        @endfor
    </div>
</div>

{{-- ═══ STATS ═══ --}}
<div class="stats-bar">
    <div class="stats-bar-inner reveal-blur" data-counter-trigger>
        <div class="sbar-item"><div class="sbar-num"><span data-count="10" data-suffix="+">0</span></div><div class="sbar-label">Años de experiencia</div></div>
        <div class="sbar-item"><div class="sbar-num"><span data-count="5000" data-suffix="+">0</span></div><div class="sbar-label">Vehículos atendidos</div></div>
        <div class="sbar-item"><div class="sbar-num"><span data-count="13" data-suffix="+">0</span></div><div class="sbar-label">Tipos de servicios</div></div>
        <div class="sbar-item"><div class="sbar-num"><span data-count="98" data-suffix="%">0</span></div><div class="sbar-label">Satisfacción del cliente</div></div>
    </div>
</div>

{{-- ═══ SERVICIOS ═══ --}}
<section class="pub-section" id="servicios">
    <div class="pub-container">
        <div style="text-align:center;max-width:620px;margin:0 auto;" class="reveal">
            <div class="section-label" style="justify-content:center;">Nuestros Servicios</div>
            <h2 class="section-title">Todo lo que tu vehículo necesita</h2>
            <p class="section-sub" style="margin:0 auto;">Contamos con técnicos especializados y equipos de diagnóstico de última generación.</p>
        </div>
        <div class="services-grid">
            @foreach([
                ['bi-wrench-adjustable','Mecánica General'],['bi-calendar2-check','Mantenimiento Preventivo'],
                ['bi-droplet-fill','Cambio de Aceite'],['bi-disc','Suspensión y Frenos'],
                ['bi-cpu-fill','Diagnóstico Electrónico'],['bi-thermometer-snow','Aire Acondicionado'],
                ['bi-brush-fill','Chapa y Pintura'],['bi-stars','Detailing'],
                ['bi-shield-shaded','Ceramic Coating'],['bi-sun-fill','Láminas Protección Solar'],
                ['bi-wind','Desinfección / Ozono UV'],['bi-box-arrow-in-down','Importación de Autopartes'],
                ['bi-bicycle','Motos y ATVs'],
            ] as $idx => [$icon, $name])
            <div class="service-card reveal stagger-{{ $idx + 1 }}">
                <div class="service-icon"><i class="bi {{ $icon }}"></i></div>
                <div class="service-name">{{ $name }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══ QUOTE ═══ --}}
<div class="quote-section">
    <div class="quote-bg-text" data-parallax="0.15">TALLER PRO</div>
    <div class="quote-deco reveal">
        <div class="quote-deco-line"></div>
        <div class="quote-deco-diamond"></div>
        <div class="quote-deco-line"></div>
    </div>
    <p class="quote-text reveal">"Tu vehículo merece la <em>mejor atención</em>. Nosotros nos encargamos de todo para que tú solo disfrutes el camino."</p>
</div>

{{-- ═══ NOSOTROS ═══ --}}
<section class="pub-section" id="nosotros" style="background:var(--pub-surface);">
    <div class="pub-container">
        <div class="about-grid">
            <div class="reveal-left">
                <div class="section-label">¿Por qué elegirnos?</div>
                <h2 class="section-title">Expertos que cuidan<br>tu inversión</h2>
                <p style="font-size:15px;color:var(--pub-muted);line-height:1.8;margin-bottom:36px;">En Taller Pro combinamos tecnología de diagnóstico con años de experiencia para ofrecerte el mejor servicio automotriz de Santa Cruz.</p>
                <div class="feature-list">
                    @foreach([
                        ['bi-patch-check-fill','Técnicos certificados','Personal capacitado en las últimas tecnologías automotrices.'],
                        ['bi-lightning-charge-fill','Diagnóstico rápido','Equipos electrónicos de última generación para detección precisa.'],
                        ['bi-shield-check','Garantía en trabajos','Todos nuestros servicios cuentan con garantía por escrito.'],
                        ['bi-geo-alt-fill','Múltiples sucursales','Encuentra el punto más cercano a ti en Santa Cruz.'],
                    ] as [$icon, $title, $desc])
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi {{ $icon }}"></i></div>
                        <div><p class="feature-title">{{ $title }}</p><p class="feature-desc">{{ $desc }}</p></div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="about-visual-grid reveal-right">
                @for($i = 0; $i < 4; $i++)
                <div class="about-visual-item"><i class="bi bi-image"></i><span>Foto del taller</span></div>
                @endfor
            </div>
        </div>
    </div>
</section>

{{-- ═══ MAPA ═══ --}}
<section class="pub-section" id="mapa">
    <div class="pub-container">
        <div style="text-align:center;max-width:580px;margin:0 auto 48px;" class="reveal">
            <div class="section-label" style="justify-content:center;">Sucursales</div>
            <h2 class="section-title">Encuéntranos cerca de ti</h2>
            <p class="section-sub" style="margin:0 auto;">Tenemos múltiples puntos de atención en Santa Cruz.</p>
        </div>
        <div class="reveal-scale"><div id="landingMap"></div></div>
        @if($sucursales->isNotEmpty())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px;margin-top:20px;">
            @foreach($sucursales as $suc)
            <div class="map-card reveal stagger-{{ $loop->index + 1 }}" onclick="centerMapTo({{ $suc->latitud }},{{ $suc->longitud }},{{ $loop->index }})">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:40px;height:40px;border-radius:12px;background:rgba(215,25,32,.06);border:1px solid rgba(215,25,32,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-geo-alt-fill" style="color:var(--accent);"></i></div>
                    <div><p style="font-family:var(--font-display);font-size:14px;font-weight:700;color:#fff;">{{ $suc->nombre }}</p><p style="font-size:12px;color:var(--pub-muted);">{{ $suc->ciudad }}</p></div>
                </div>
                @if($suc->direccion)<p style="font-size:12px;color:var(--pub-muted);margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,.04);"><i class="bi bi-pin-map" style="margin-right:5px;color:var(--accent);"></i>{{ $suc->direccion }}</p>@endif
                @if($suc->telefono)<p style="font-size:12px;color:var(--pub-muted);margin-top:6px;"><i class="bi bi-telephone" style="margin-right:5px;color:var(--accent);"></i>{{ $suc->telefono }}</p>@endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ═══ CTA ═══ --}}
<section class="cta-section" id="contacto">
    <div class="cta-circle cta-circle-1"></div>
    <div class="cta-circle cta-circle-2"></div>
    <div class="pub-container" style="text-align:center;max-width:720px;margin:0 auto;position:relative;z-index:1;">
        <div class="reveal-blur">
            <div class="section-label" style="justify-content:center;">Contacto</div>
            <h2 class="cta-title" style="margin-bottom:20px;">¿Listo para agendar<br>tu <em>próxima cita</em>?</h2>
            <p class="section-sub" style="margin:0 auto 40px;max-width:500px;">Regístrate para agendar tu cita en línea, hacer seguimiento de tu vehículo y más.</p>
        </div>
        <div class="reveal" style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ auth()->check() ? route('cliente.inicio') : route('register') }}" class="btn-hero-primary" style="font-size:15px;padding:16px 34px;"><i class="bi bi-calendar-plus"></i> Agendar cita</a>
            <a href="https://wa.me/59178559066" target="_blank" class="btn-whatsapp" style="font-size:15px;padding:16px 34px;"><i class="bi bi-whatsapp"></i> Escribir por WhatsApp</a>
        </div>
        <div class="reveal" style="margin-top:56px;display:flex;justify-content:center;gap:48px;flex-wrap:wrap;">
            <div class="contact-box"><div class="contact-icon"><i class="bi bi-telephone-fill"></i></div><p class="contact-text">78559066 / 704-07035</p></div>
            <div class="contact-box"><div class="contact-icon"><i class="bi bi-envelope-fill"></i></div><p class="contact-text">contacto@tallerpro.bo</p></div>
            <div class="contact-box"><div class="contact-icon"><i class="bi bi-clock-fill"></i></div><p class="contact-text">Lun–Sáb: 8:00 – 18:00</p></div>
        </div>
    </div>
</section>

{{-- ═══ FOOTER ═══ --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">
        <div class="footer-grid">
            <div>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
                    <div style="width:40px;height:40px;background:var(--accent);border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px var(--accent-glow);"><i class="bi bi-tools" style="color:#fff;font-size:16px;"></i></div>
                    <span style="font-family:var(--font-display);font-size:20px;font-weight:800;color:#fff;">Taller Pro</span>
                </div>
                <p class="footer-brand-desc">Tu taller automotriz de confianza en Santa Cruz de la Sierra, Bolivia.</p>
                <div class="footer-socials">
                    <a href="#" class="footer-social"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="footer-social"><i class="bi bi-instagram"></i></a>
                    <a href="https://wa.me/59178559066" target="_blank" class="footer-social"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div>
                <p class="footer-col-title">Servicios</p>
                @foreach(['Mecánica General','Mantenimiento','Diagnóstico Electrónico','Chapa y Pintura','Detailing','Aire Acondicionado'] as $s)
                <a href="#servicios" class="footer-link">{{ $s }}</a>
                @endforeach
            </div>
            <div>
                <p class="footer-col-title">Contacto</p>
                <div class="footer-contact"><i class="bi bi-geo-alt-fill"></i>Calle Primavera esq. Lluvia de Oro, SCZ</div>
                <div class="footer-contact"><i class="bi bi-telephone-fill"></i>78559066 / 704-07035</div>
                <div class="footer-contact"><i class="bi bi-envelope-fill"></i>contacto@tallerpro.bo</div>
                <div class="footer-contact"><i class="bi bi-clock-fill"></i>Lun–Sáb: 8:00 – 18:00</div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Taller Pro — Todos los derechos reservados</p>
            <a href="{{ route('login') }}">Panel administrativo</a>
        </div>
    </div>
</footer>

@endsection

@push('scripts')
<script>
const SUCURSALES=@json($sucursales);
const MAP_STYLES=[
    {elementType:'geometry',stylers:[{color:'#080c14'}]},{elementType:'labels.text.fill',stylers:[{color:'#5a6a80'}]},
    {elementType:'labels.text.stroke',stylers:[{color:'#080c14'}]},{featureType:'road',elementType:'geometry',stylers:[{color:'#151d2e'}]},
    {featureType:'road',elementType:'labels.text.fill',stylers:[{color:'#3a4a60'}]},{featureType:'road.highway',elementType:'geometry',stylers:[{color:'#1a2744'}]},
    {featureType:'water',elementType:'geometry',stylers:[{color:'#060a12'}]},{featureType:'poi',elementType:'geometry',stylers:[{color:'#0c1220'}]},
    {featureType:'poi',elementType:'labels.text.fill',stylers:[{color:'#5a6a80'}]},{featureType:'transit',elementType:'geometry',stylers:[{color:'#151d2e'}]},
    {featureType:'administrative',elementType:'geometry.stroke',stylers:[{color:'#1e2d45'}]},{featureType:'landscape',elementType:'geometry',stylers:[{color:'#0c1220'}]},
];
let map,markers=[],openInfoWindow=null;
function initLandingMap(){
    var dc=SUCURSALES.length?{lat:parseFloat(SUCURSALES[0].latitud),lng:parseFloat(SUCURSALES[0].longitud)}:{lat:-17.7833,lng:-63.1821};
    map=new google.maps.Map(document.getElementById('landingMap'),{center:dc,zoom:SUCURSALES.length>1?12:14,mapTypeControl:false,streetViewControl:false,fullscreenControl:false,styles:MAP_STYLES});
    var bounds=new google.maps.LatLngBounds();
    if(navigator.geolocation){navigator.geolocation.getCurrentPosition(function(p){var up={lat:p.coords.latitude,lng:p.coords.longitude};new google.maps.Marker({map:map,position:up,title:'Tu ubicación',icon:{path:google.maps.SymbolPath.CIRCLE,scale:9,fillColor:'#4285F4',fillOpacity:1,strokeColor:'#fff',strokeWeight:2},zIndex:0});bounds.extend(up);if(SUCURSALES.length>0)map.fitBounds(bounds,{padding:60});},function(){},{timeout:8000});}
    SUCURSALES.forEach(function(suc,i){
        var pos={lat:parseFloat(suc.latitud),lng:parseFloat(suc.longitud)};bounds.extend(pos);
        var marker=new google.maps.Marker({map:map,position:pos,title:suc.nombre,animation:google.maps.Animation.DROP,icon:{path:google.maps.SymbolPath.CIRCLE,scale:11,fillColor:'#D71920',fillOpacity:1,strokeColor:'#fff',strokeWeight:2.5}});
        var iw=new google.maps.InfoWindow({content:'<div style="min-width:210px;padding:16px 18px;font-family:Inter,system-ui,sans-serif;background:#0c1220;color:#f1f5f9;border-radius:14px;border:1px solid rgba(255,255,255,.06);"><p style="font-weight:800;font-size:14px;margin:0 0 4px;font-family:Outfit,sans-serif;">'+suc.nombre+'</p>'+(suc.ciudad?'<p style="font-size:12px;color:#5a6a80;margin:0 0 4px;">'+suc.ciudad+'</p>':'')+(suc.direccion?'<p style="font-size:12px;color:#5a6a80;margin:0 0 12px;">'+suc.direccion+'</p>':'')+'<a href="https://www.google.com/maps/dir/?api=1&destination='+suc.latitud+','+suc.longitud+'" target="_blank" style="display:inline-flex;align-items:center;gap:5px;padding:8px 14px;background:#D71920;color:#fff;border-radius:9px;text-decoration:none;font-size:12px;font-weight:700;box-shadow:0 4px 12px rgba(215,25,32,.3);"><i class=\'bi bi-cursor-fill\'></i> Cómo llegar</a></div>'});
        marker.addListener('click',function(){if(openInfoWindow)openInfoWindow.close();iw.open(map,marker);openInfoWindow=iw;});
        markers.push({marker:marker,iw:iw});
    });
    if(SUCURSALES.length>1)map.fitBounds(bounds,{padding:60});
}
function centerMapTo(lat,lng,idx){map.panTo({lat:parseFloat(lat),lng:parseFloat(lng)});map.setZoom(16);if(openInfoWindow)openInfoWindow.close();if(markers[idx]){markers[idx].iw.open(map,markers[idx].marker);openInfoWindow=markers[idx].iw;}document.getElementById('mapa').scrollIntoView({behavior:'smooth'});}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&callback=initLandingMap" async defer></script>
@endpush