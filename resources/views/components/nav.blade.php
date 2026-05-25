@php
    $isLanding = request()->routeIs('landing');
@endphp

<nav
    x-data="{ scrolled: {{ $isLanding ? 'false' : 'true' }}, mobileOpen: false }"
    @if($isLanding)
    @scroll.window="scrolled = (window.scrollY > 20)"
    @endif
    :class="scrolled 
        ? 'top-3 bg-[#0F0F14]/75 backdrop-blur-lg border border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.4)] py-1.5 w-[94%] max-w-[1400px] rounded-full' 
        : 'top-5 bg-[#0F0F14]/30 backdrop-blur-md border border-white/5 shadow-md py-3 w-[96%] max-w-[1440px] rounded-full'"
    class="fixed inset-x-0 mx-auto z-50 transition-all duration-300 text-white"
>
    <div class="w-full px-6 md:px-10 flex items-center justify-between h-12 md:h-14">
        {{-- Logo --}}
        <a href="{{ route('landing') }}" class="flex items-center gap-2 text-h4 font-extrabold group">
            <svg class="w-7 h-7 transition-transform duration-300 group-hover:rotate-12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="logo-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#6C5CE7" />
                        <stop offset="100%" stop-color="#A855F7" />
                    </linearGradient>
                </defs>
                <path d="M12 2C12 7.5 16.5 12 22 12C16.5 12 12 16.5 12 22C12 16.5 7.5 12 2 12C7.5 12 12 7.5 12 2Z" fill="none" stroke="url(#logo-grad)" stroke-width="2.5" stroke-linejoin="round"/>
            </svg>
            <span class="text-white font-extrabold text-xl tracking-tight">Eventra</span>
        </a>

        {{-- Desktop Links — Integrated GooeyNav --}}
        <div 
            x-data="gooeyNav()"
            class="gooey-nav-container hidden md:block"
            x-init="initNav()"
            @resize.window.debounce.100ms="updateActivePosition()"
        >
            <nav>
                <ul x-ref="navUl">
                    <li :class="activeIndex === 0 ? 'active' : ''" @click="clickItem($event, 0)">
                        <a href="{{ route('events.index') }}" @click.prevent="navigate('{{ route('events.index') }}', 0)">Events</a>
                    </li>
                    <li :class="activeIndex === 1 ? 'active' : ''" @click="clickItem($event, 1)">
                        <a href="{{ route('vendors.index') }}" @click.prevent="navigate('{{ route('vendors.index') }}', 1)">Vendors</a>
                    </li>
                    <li :class="activeIndex === 2 ? 'active' : ''" @click="clickItem($event, 2)">
                        <a href="{{ $isLanding ? '#how-it-works' : route('landing') . '#how-it-works' }}" @click.prevent="navigate('{{ $isLanding ? '#how-it-works' : route('landing') . '#how-it-works' }}', 2)">How It Works</a>
                    </li>
                </ul>
            </nav>
            <span class="effect filter" x-ref="filterEffect"></span>
            <span class="effect text" x-ref="textEffect"></span>
        </div>

        {{-- Desktop Actions --}}
        <div class="hidden md:flex items-center gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-2 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 text-white text-sm font-semibold hover:shadow-glow hover:-translate-y-0.5 transition-all duration-200">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-2 rounded-full border border-white/30 text-white text-sm font-semibold hover:border-white hover:bg-white/10 transition-all duration-200">Log in</a>
                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-primary-500 via-secondary-500 to-accent text-white text-sm font-semibold hover:shadow-glow hover:-translate-y-0.5 transition-all duration-200">Get Started</a>
            @endauth
        </div>

        {{-- Mobile Hamburger --}}
        <button
            @click="mobileOpen = !mobileOpen"
            class="md:hidden flex flex-col gap-1.5 w-8 h-8 items-center justify-center focus:outline-none"
            aria-label="Toggle Menu"
        >
            <span :class="mobileOpen ? 'rotate-45 translate-y-[8px]' : ''" class="w-6 h-0.5 bg-white transition-all duration-300 rounded-full"></span>
            <span :class="mobileOpen ? 'opacity-0 scale-0' : ''" class="w-6 h-0.5 bg-white transition-all duration-200 rounded-full"></span>
            <span :class="mobileOpen ? '-rotate-45 -translate-y-[8px]' : ''" class="w-6 h-0.5 bg-white transition-all duration-300 rounded-full"></span>
        </button>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 -translate-y-4"
        @click.outside="mobileOpen = false"
        class="md:hidden bg-[#0F0F14]/95 backdrop-blur-md border-b border-white/10 shadow-md mt-4 rounded-3xl"
    >
        <div class="px-8 py-6 flex flex-col gap-4">
            <a href="{{ route('events.index') }}" @click="mobileOpen = false" class="text-body-lg font-medium text-white/80 hover:text-white transition-colors">Events</a>
            <a href="{{ route('vendors.index') }}" @click="mobileOpen = false" class="text-body-lg font-medium text-white/80 hover:text-white transition-colors">Vendors</a>
            <a href="{{ $isLanding ? '#how-it-works' : route('landing') . '#how-it-works' }}" @click="mobileOpen = false" class="text-body-lg font-medium text-white/80 hover:text-white transition-colors">How It Works</a>
            <hr class="border-white/10">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 text-white text-center font-semibold transition-all">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-full border border-white/30 text-white text-center font-semibold hover:bg-white/10 transition-all">Log in</a>
                <a href="{{ route('register') }}" class="px-6 py-3 rounded-full bg-gradient-to-r from-primary-500 via-secondary-500 to-accent text-white text-center font-semibold transition-all">Get Started</a>
            @endauth
        </div>
    </div>
</nav>

<script>
function registerGooeyNav() {
    const data = () => ({
        activeIndex: -1,
        animationTime: 600,
        particleCount: 15,
        particleDistances: [90, 10],
        particleR: 100,
        timeVariance: 300,
        colors: [1, 2, 3, 1, 2, 3, 1, 4],

        initNav() {
            const currentPath = window.location.pathname;
            const currentHash = window.location.hash;
            
            const eventsPath = new URL('{{ route('events.index') }}', window.location.origin).pathname;
            const vendorsPath = new URL('{{ route('vendors.index') }}', window.location.origin).pathname;
            const howItWorksHash = '#how-it-works';

            if (currentPath.startsWith(eventsPath)) {
                this.activeIndex = 0;
            } else if (currentPath.startsWith(vendorsPath)) {
                this.activeIndex = 1;
            } else if (currentPath === '/' && currentHash === howItWorksHash) {
                this.activeIndex = 2;
            }

            this.$nextTick(() => {
                setTimeout(() => {
                    this.updateActivePosition();
                }, 50);
            });
        },

        updateActivePosition() {
            const listItems = this.$el.querySelectorAll('nav ul li');
            if (this.activeIndex === -1) {
                if (this.$refs.filterEffect) this.$refs.filterEffect.style.opacity = '0';
                if (this.$refs.textEffect) this.$refs.textEffect.style.opacity = '0';
                return;
            }

            const activeLi = listItems[this.activeIndex];
            if (!activeLi) return;

            const containerRect = this.$el.getBoundingClientRect();
            const pos = activeLi.getBoundingClientRect();

            const styles = {
                opacity: '1',
                left: `${pos.x - containerRect.x}px`,
                top: `${pos.y - containerRect.y}px`,
                width: `${pos.width}px`,
                height: `${pos.height}px`
            };

            const filterEffect = this.$refs.filterEffect;
            const textEffect = this.$refs.textEffect;

            if (filterEffect && textEffect) {
                Object.assign(filterEffect.style, styles);
                Object.assign(textEffect.style, styles);
                textEffect.innerText = activeLi.innerText.trim();
                textEffect.classList.add('active');
            }
        },

        clickItem(e, index) {
            if (this.activeIndex === index) return;

            const liEl = e.currentTarget;
            this.activeIndex = index;
            this.updateActivePosition();

            const filterEffect = this.$refs.filterEffect;
            const textEffect = this.$refs.textEffect;

            if (filterEffect) {
                filterEffect.style.opacity = '1';
                const oldParticles = filterEffect.querySelectorAll('.particle');
                oldParticles.forEach(p => filterEffect.removeChild(p));
                this.makeParticles(filterEffect);
            }

            if (textEffect) {
                textEffect.style.opacity = '1';
                textEffect.classList.remove('active');
                void textEffect.offsetWidth;
                textEffect.classList.add('active');
            }
        },

        navigate(href, index) {
            const isAnchor = href.includes('#');
            const currentPath = window.location.pathname;

            if (isAnchor) {
                const hash = href.substring(href.indexOf('#'));
                if (currentPath === '/') {
                    const el = document.querySelector(hash);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth' });
                        history.pushState(null, null, hash);
                        return;
                    }
                }
            }

            setTimeout(() => {
                window.location.href = href;
            }, 250);
        },

        noise(n = 1) {
            return n / 2 - Math.random() * n;
        },

        getXY(distance, pointIndex, totalPoints) {
            const angle = ((360 + this.noise(8)) / totalPoints) * pointIndex * (Math.PI / 180);
            return [distance * Math.cos(angle), distance * Math.sin(angle)];
        },

        createParticle(i, t, d, r) {
            let rotate = this.noise(r / 10);
            return {
                start: this.getXY(d[0], this.particleCount - i, this.particleCount),
                end: this.getXY(d[1] + this.noise(7), this.particleCount - i, this.particleCount),
                time: t,
                scale: 1 + this.noise(0.2),
                color: this.colors[Math.floor(Math.random() * this.colors.length)],
                rotate: rotate > 0 ? (rotate + r / 20) * 10 : (rotate - r / 20) * 10
            };
        },

        makeParticles(element) {
            const d = this.particleDistances;
            const r = this.particleR;
            const bubbleTime = this.animationTime * 2 + this.timeVariance;
            element.style.setProperty('--time', `${bubbleTime}ms`);

            for (let i = 0; i < this.particleCount; i++) {
                const t = this.animationTime * 2 + this.noise(this.timeVariance * 2);
                const p = this.createParticle(i, t, d, r);
                element.classList.remove('active');

                setTimeout(() => {
                    const particle = document.createElement('span');
                    const point = document.createElement('span');
                    particle.classList.add('particle');
                    particle.style.setProperty('--start-x', `${p.start[0]}px`);
                    particle.style.setProperty('--start-y', `${p.start[1]}px`);
                    particle.style.setProperty('--end-x', `${p.end[0]}px`);
                    particle.style.setProperty('--end-y', `${p.end[1]}px`);
                    particle.style.setProperty('--time', `${p.time}ms`);
                    particle.style.setProperty('--scale', `${p.scale}`);
                    particle.style.setProperty('--color', `var(--color-${p.color}, white)`);
                    particle.style.setProperty('--rotate', `${p.rotate}deg`);

                    point.classList.add('point');
                    particle.appendChild(point);
                    element.appendChild(particle);
                    
                    requestAnimationFrame(() => {
                        element.classList.add('active');
                    });

                    setTimeout(() => {
                        try {
                            element.removeChild(particle);
                        } catch (err) {}
                    }, t);
                }, 30);
            }
        }
    });

    if (window.Alpine) {
        Alpine.data('gooeyNav', data);
    } else {
        document.addEventListener('alpine:init', () => {
            Alpine.data('gooeyNav', data);
        });
    }
}
registerGooeyNav();
</script>
