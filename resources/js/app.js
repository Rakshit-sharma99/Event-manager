/* ================================================================
 *  Eventra — Main JavaScript
 *  Alpine.js + GSAP + Particle System + Page Transitions
 * ================================================================ */

import './bootstrap';

/* ── Alpine.js ── */
import Alpine from 'alpinejs';
window.Alpine = Alpine;

/* ── GSAP + ScrollTrigger ── */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

/* ════════════════════════════════════════════
 *  PARTICLE CANVAS — Purple star-dust effect
 * ════════════════════════════════════════════ */
function initParticles() {
    const canvas = document.getElementById('particle-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let particles = [];
    let animId;

    const colors = [
        '#FFD000', // Bright gold sunlight
        '#FF85A1', // Rose pink
        '#B794F4', // Pastel lavender
        '#FFFEEA', // Super warm cream
        '#FDA4AF'  // Peach sunset
    ];

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    // 4-pointed star path with dynamic glowing color
    function drawStar(ctx, cx, cy, size, opacity, color) {
        ctx.save();
        ctx.globalAlpha = opacity;
        ctx.fillStyle = color;
        ctx.shadowColor = color;
        ctx.shadowBlur = 12;
        ctx.beginPath();
        for (let i = 0; i < 4; i++) {
            const angle = (Math.PI / 2) * i - Math.PI / 2;
            const outerX = cx + Math.cos(angle) * size;
            const outerY = cy + Math.sin(angle) * size;
            const innerAngle = angle + Math.PI / 4;
            const innerX = cx + Math.cos(innerAngle) * (size * 0.35);
            const innerY = cy + Math.sin(innerAngle) * (size * 0.35);
            if (i === 0) ctx.moveTo(outerX, outerY);
            else ctx.lineTo(outerX, outerY);
            ctx.lineTo(innerX, innerY);
        }
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function createParticle() {
        const isStar = Math.random() < 0.2;
        return {
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            size: isStar ? 3.5 + Math.random() * 4.5 : 1 + Math.random() * 2.5,
            speedY: -(0.1 + Math.random() * 0.25),
            speedX: (Math.random() - 0.5) * 0.15,
            opacity: 0.15 + Math.random() * 0.35,
            fadeDir: Math.random() < 0.5 ? 1 : -1,
            fadeSpeed: 0.0015 + Math.random() * 0.003,
            color: colors[Math.floor(Math.random() * colors.length)],
            isStar,
        };
    }

    function init() {
        resize();
        particles = [];
        const count = Math.min(65, Math.floor(canvas.width * canvas.height / 22000));
        for (let i = 0; i < count; i++) {
            particles.push(createParticle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        particles.forEach((p, i) => {
            p.x += p.speedX;
            p.y += p.speedY;
            p.opacity += p.fadeDir * p.fadeSpeed;

            if (p.opacity <= 0.08 || p.opacity >= 0.5) p.fadeDir *= -1;
            p.opacity = Math.max(0.05, Math.min(0.5, p.opacity));

            if (p.y < -20 || p.x < -20 || p.x > canvas.width + 20) {
                particles[i] = createParticle();
                particles[i].y = canvas.height + 10;
            }

            if (p.isStar) {
                drawStar(ctx, p.x, p.y, p.size, p.opacity, p.color);
            } else {
                ctx.save();
                ctx.globalAlpha = p.opacity;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.shadowColor = p.color;
                ctx.shadowBlur = 10;
                ctx.fill();
                ctx.restore();
            }
        });

        animId = requestAnimationFrame(animate);
    }

    init();
    animate();

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(init, 250);
    });
}

/* ════════════════════════════════════════════
 *  GSAP SCROLL ANIMATIONS
 * ════════════════════════════════════════════ */
function initScrollAnimations() {
    // Fade-up cards
    gsap.utils.toArray('[data-animate="fade-up"]').forEach((el) => {
        gsap.from(el, {
            y: 30,
            opacity: 0,
            duration: 0.6,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                once: true,
            },
        });
    });

    // Staggered children
    gsap.utils.toArray('[data-animate="stagger"]').forEach((container) => {
        const children = container.children;
        gsap.from(children, {
            y: 30,
            opacity: 0,
            scale: 0.95,
            duration: 0.5,
            stagger: 0.1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: container,
                start: 'top 85%',
                once: true,
            },
        });
    });

    // Word-by-word reveal for headings
    gsap.utils.toArray('[data-animate="words"]').forEach((el) => {
        const text = el.textContent;
        el.innerHTML = text.split(' ').map(w => `<span class="inline-block">${w}&nbsp;</span>`).join('');
        gsap.from(el.children, {
            y: 20,
            opacity: 0,
            duration: 0.4,
            stagger: 0.08,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 85%',
                once: true,
            },
        });
    });

    // Counter animation
    gsap.utils.toArray('[data-counter]').forEach((el) => {
        const target = parseInt(el.dataset.counter, 10) || 0;
        const suffix = el.dataset.counterSuffix || '';
        const prefix = el.dataset.counterPrefix || '';

        ScrollTrigger.create({
            trigger: el,
            start: 'top 85%',
            once: true,
            onEnter: () => {
                gsap.to({ val: 0 }, {
                    val: target,
                    duration: 2,
                    ease: 'power2.out',
                    onUpdate: function () {
                        el.textContent = prefix + Math.round(this.targets()[0].val).toLocaleString() + suffix;
                    },
                });
            },
        });
    });
}

/* ════════════════════════════════════════════
 *  PAGE TRANSITION — fade in from bottom
 * ════════════════════════════════════════════ */
function initPageTransition() {
    const main = document.querySelector('main') || document.querySelector('.page-enter');
    if (main && !main.classList.contains('no-transition')) {
        main.style.opacity = '0';
        main.style.transform = 'translateY(12px)';
        requestAnimationFrame(() => {
            main.style.transition = 'opacity 0.35s ease-out, transform 0.35s ease-out';
            main.style.opacity = '1';
            main.style.transform = 'translateY(0)';
        });
    }
}

/* ════════════════════════════════════════════
 *  SPLASH SCREEN — first visit only
 * ════════════════════════════════════════════ */
function initSplash() {
    try {
        if (sessionStorage.getItem('eventra-splash-shown')) return;
    } catch (e) {
        // Safe fallback if sessionStorage is inaccessible (e.g. Incognito mode/third-party blockers)
        return;
    }

    const splash = document.getElementById('eventra-splash');
    if (!splash) return;

    try {
        sessionStorage.setItem('eventra-splash-shown', 'true');
    } catch (e) {}
    splash.style.display = 'flex';

    setTimeout(() => {
        splash.style.opacity = '0';
        splash.style.transition = 'opacity 0.4s ease-out';
        setTimeout(() => splash.remove(), 400);
    }, 600);
}

/* ════════════════════════════════════════════
 *  FLOATING LINES — WebGL background for auth pages
 * ════════════════════════════════════════════ */
import mountFloatingLines from './floating-lines';
window.mountFloatingLines = mountFloatingLines;

/* ════════════════════════════════════════════
 *  INIT ON DOM READY
 * ════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    initSplash();
    initParticles();
    initPageTransition();
    initScrollAnimations();

    // Auto-init FloatingLines if container exists
    const flContainer = document.getElementById('floating-lines-login') || document.getElementById('floating-lines-register');
    if (flContainer) {
        mountFloatingLines(flContainer, {
            enabledWaves: ['top', 'middle', 'bottom'],
            lineCount: 8,
            lineDistance: 8,
            bendRadius: 8,
            bendStrength: -2,
            interactive: true,
            parallax: true,
            animationSpeed: 1,
            gradientStart: '#e945f5',
            gradientMid: '#6f6f6f',
            gradientEnd: '#6a6a6a',
        });
    }
});

/* ── Start Alpine.js ── */
Alpine.start();

// Refresh ScrollTrigger on window load to fix size/offset miscalculations after resources load
window.addEventListener('load', () => {
    if (window.ScrollTrigger) {
        window.ScrollTrigger.refresh();
    }
});

