import './bootstrap';

import Alpine from 'alpinejs';
import { gsap } from 'gsap';
import Lenis from 'lenis';
import Chart from 'chart.js/auto';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { createIcons, icons } from 'lucide';
import { mountLaserFlow } from './components/LaserFlow.jsx';
import './components/LaserFlow.css';

window.Alpine = Alpine;
window.Chart = Chart;
Alpine.start();

const lenis = new Lenis({ lerp: 0.08, wheelMultiplier: 0.9 });
function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}
requestAnimationFrame(raf);

AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 40 });
createIcons({ icons });

document.addEventListener('mousemove', (event) => {
    document.documentElement.style.setProperty('--mx', `${(event.clientX / window.innerWidth) * 100}%`);
    document.documentElement.style.setProperty('--my', `${(event.clientY / window.innerHeight) * 100}%`);
});

document.querySelectorAll('.magnetic').forEach((button) => {
    button.addEventListener('mousemove', (event) => {
        const rect = button.getBoundingClientRect();
        gsap.to(button, { x: (event.clientX - rect.left - rect.width / 2) * 0.18, y: (event.clientY - rect.top - rect.height / 2) * 0.18, duration: 0.25 });
    });
    button.addEventListener('mouseleave', () => gsap.to(button, { x: 0, y: 0, duration: 0.35 }));
});

gsap.from('[data-reveal]', { y: 24, opacity: 0, duration: .8, stagger: .08, ease: 'power3.out' });

document.querySelectorAll('[data-counter]').forEach((el) => {
    const value = Number(el.dataset.counter || 0);
    const state = { value: 0 };
    gsap.to(state, {
        value,
        duration: 1.2,
        ease: 'power2.out',
        onUpdate: () => {
            el.textContent = Math.round(state.value).toLocaleString('en-IN');
        },
    });
});

document.querySelectorAll('[data-laserflow]').forEach((element) => {
    const customOptions = element.dataset.laserflow && element.dataset.laserflow !== ''
        ? JSON.parse(element.dataset.laserflow)
        : {};

    mountLaserFlow(element, {
        horizontalBeamOffset: 0.0,
        verticalBeamOffset: 0.35,
        horizontalSizing: 1.3,
        verticalSizing: 2.8,
        wispDensity: 1.2,
        wispSpeed: 28.0,
        wispIntensity: 11.0,
        flowSpeed: 0.42,
        flowStrength: 0.35,
        fogIntensity: 0.36,
        fogScale: 0.25,
        fogFallSpeed: 0.75,
        decay: 1.25,
        falloffStart: 1.4,
        mouseTiltStrength: 0.015,
        mouseSmoothTime: 0.08,
        color: '#00D9FF',
        globalIntensity: 0.78,
        mobileIntensity: 0.28,
        ...customOptions,
    });
});
