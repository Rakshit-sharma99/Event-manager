import * as THREE from 'three';

const VERTEX_SHADER = `
varying vec2 vUv;

void main() {
    vUv = uv;
    gl_Position = vec4(position.xy, 0.0, 1.0);
}
`;

const FRAGMENT_SHADER = `
precision highp float;

varying vec2 vUv;

uniform float uTime;
uniform vec2 uResolution;
uniform vec2 uMouse;
uniform vec3 uColor;
uniform float uHorizontalBeamOffset;
uniform float uVerticalBeamOffset;
uniform float uHorizontalSizing;
uniform float uVerticalSizing;
uniform float uWispDensity;
uniform float uWispSpeed;
uniform float uWispIntensity;
uniform float uFlowSpeed;
uniform float uFlowStrength;
uniform float uFogIntensity;
uniform float uFogScale;
uniform float uFogFallSpeed;
uniform float uDecay;
uniform float uFalloffStart;
uniform float uMouseTiltStrength;
uniform float uMobileMode;
uniform float uGlobalIntensity;

float hash(vec2 p) {
    return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453123);
}

float noise(vec2 p) {
    vec2 i = floor(p);
    vec2 f = fract(p);
    vec2 u = f * f * (3.0 - 2.0 * f);
    return mix(
        mix(hash(i + vec2(0.0, 0.0)), hash(i + vec2(1.0, 0.0)), u.x),
        mix(hash(i + vec2(0.0, 1.0)), hash(i + vec2(1.0, 1.0)), u.x),
        u.y
    );
}

float fbm(vec2 p) {
    float value = 0.0;
    float amplitude = 0.5;
    for (int i = 0; i < 5; i++) {
        value += amplitude * noise(p);
        p *= 2.03;
        amplitude *= 0.52;
    }
    return value;
}

float laserLine(float distanceToLine, float sharpness, float bloom) {
    float core = exp(-pow(distanceToLine * sharpness, 2.0));
    float glow = exp(-pow(distanceToLine * bloom, 2.0));
    return core * 1.8 + glow * 0.62;
}

void main() {
    vec2 uv = vUv;
    vec2 aspect = vec2(uResolution.x / uResolution.y, 1.0);

    float mouseTiltX = (uMouse.x - 0.5) * uMouseTiltStrength;
    float mouseTiltY = (uMouse.y - 0.5) * uMouseTiltStrength;

    float beamX = mix(0.645, 0.50, uMobileMode) + uHorizontalBeamOffset * 0.08 + mouseTiltX;
    float beamY = mix(0.785 - uVerticalBeamOffset * 0.24, 0.58 - uVerticalBeamOffset * 0.08, uMobileMode) + mouseTiltY;

    vec2 p = (uv - vec2(beamX, beamY)) * aspect;
    float radial = length(p);

    float below = smoothstep(beamY + 0.44 * uVerticalSizing, beamY - 0.14, uv.y);
    float above = smoothstep(beamY - 0.03, beamY + 0.34, uv.y);
    float verticalMask = max(below, above * 0.65);
    float verticalBeam = laserLine(abs(uv.x - beamX), 720.0 * uDecay, 58.0 / max(uFalloffStart, 0.1)) * verticalMask;

    float horizontalSpan = smoothstep(beamX - 0.18 * uHorizontalSizing, beamX - 0.02, uv.x)
        * (1.0 - smoothstep(beamX + 0.47 * uHorizontalSizing, beamX + 0.59 * uHorizontalSizing, uv.x));
    float horizontalBeam = laserLine(abs(uv.y - beamY), 900.0 * uDecay, 74.0 / max(uFalloffStart, 0.1)) * horizontalSpan;

    float coreBloom = exp(-radial * 8.2) * 4.1;
    float halo = exp(-radial * 2.25) * uFogIntensity;
    float fogNoise = fbm(vec2(uv.x * 3.4 + uTime * uFlowSpeed, uv.y * 2.0 - uTime * uFogFallSpeed) / max(uFogScale, 0.05));
    float fog = halo * smoothstep(0.16, 0.92, fogNoise) * 1.45;

    vec2 flowUv = vec2((uv.x - beamX) * 22.0, (uv.y - beamY) * 9.0 + uTime * uWispSpeed * 0.04);
    float wisps = pow(fbm(flowUv * uWispDensity), 5.0) * uWispIntensity * 0.045;
    wisps *= exp(-abs(uv.x - beamX) * 4.0) * exp(-abs(uv.y - beamY) * 1.45);

    float lowerBlue = exp(-abs(uv.x - beamX) * 4.2) * smoothstep(beamY + 0.25, beamY - 0.20, uv.y) * 0.38;
    float rightMagenta = smoothstep(beamX - 0.02, beamX + 0.34, uv.x) * exp(-abs(uv.y - beamY) * 4.0) * 0.52;
    float edgeAura = exp(-distance(uv, vec2(0.92, 0.58)) * 3.0) * 0.36;

    float intensity = verticalBeam + horizontalBeam + coreBloom + fog + wisps + lowerBlue;
    vec3 cyan = uColor;
    vec3 whiteHot = vec3(1.0, 1.0, 1.0);
    vec3 magenta = vec3(0.78, 0.18, 1.0);
    vec3 deepBlue = vec3(0.03, 0.12, 0.55);

    vec3 color = cyan * intensity;
    color += whiteHot * pow(max(verticalBeam + horizontalBeam + coreBloom, 0.0), 1.25) * 0.72;
    color += magenta * (rightMagenta + edgeAura) * (0.82 + fogNoise * 0.45);
    color += deepBlue * lowerBlue;
    color *= 1.0 + sin(uTime * 7.0 + fogNoise * 5.0) * 0.025;
    color *= uGlobalIntensity;

    float alpha = clamp(intensity * 0.5 + rightMagenta * 0.55 + edgeAura * 0.38, 0.0, 0.96);
    alpha *= min(1.0, uGlobalIntensity * 1.12);
    gl_FragColor = vec4(color, alpha);
}
`;

function hexToRgb(hex) {
    const cleaned = hex.replace('#', '');
    const bigint = parseInt(cleaned, 16);
    return {
        r: ((bigint >> 16) & 255) / 255,
        g: ((bigint >> 8) & 255) / 255,
        b: (bigint & 255) / 255,
    };
}

export function mountLaserFlow(target, options = {}) {
    if (!target) return null;

    const props = {
        horizontalBeamOffset: 0.0,
        verticalBeamOffset: 0.35,
        horizontalSizing: 1.3,
        verticalSizing: 2.8,
        wispDensity: 1.2,
        wispSpeed: 28.0,
        wispIntensity: 18.0,
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
        dpr: Math.min(window.devicePixelRatio || 1, window.innerWidth < 768 ? 1.15 : 1.65),
        ...options,
    };

    const isMobile = window.matchMedia('(max-width: 767px)').matches;
    const renderer = new THREE.WebGLRenderer({
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance',
    });
    renderer.setClearColor(0x000000, 0);
    target.appendChild(renderer.domElement);

    const scene = new THREE.Scene();
    const camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0, 1);
    const geometry = new THREE.PlaneGeometry(2, 2);
    const { r, g, b } = hexToRgb(props.color);
    const uniforms = {
        uTime: { value: 0 },
        uResolution: { value: new THREE.Vector2(1, 1) },
        uMouse: { value: new THREE.Vector2(0.5, 0.5) },
        uColor: { value: new THREE.Vector3(r, g, b) },
        uHorizontalBeamOffset: { value: props.horizontalBeamOffset },
        uVerticalBeamOffset: { value: props.verticalBeamOffset },
        uHorizontalSizing: { value: props.horizontalSizing },
        uVerticalSizing: { value: props.verticalSizing },
        uWispDensity: { value: props.wispDensity },
        uWispSpeed: { value: props.wispSpeed },
        uWispIntensity: { value: props.wispIntensity },
        uFlowSpeed: { value: props.flowSpeed },
        uFlowStrength: { value: props.flowStrength },
        uFogIntensity: { value: props.fogIntensity },
        uFogScale: { value: props.fogScale },
        uFogFallSpeed: { value: props.fogFallSpeed },
        uDecay: { value: props.decay },
        uFalloffStart: { value: props.falloffStart },
        uMouseTiltStrength: { value: isMobile ? 0.004 : props.mouseTiltStrength },
        uMobileMode: { value: isMobile ? 1 : 0 },
        uGlobalIntensity: { value: isMobile ? (props.mobileIntensity ?? 0.28) : (props.globalIntensity ?? 0.78) },
    };
    const material = new THREE.ShaderMaterial({
        vertexShader: VERTEX_SHADER,
        fragmentShader: FRAGMENT_SHADER,
        uniforms,
        transparent: true,
        depthTest: false,
        depthWrite: false,
        blending: THREE.AdditiveBlending,
    });
    scene.add(new THREE.Mesh(geometry, material));

    const mouse = new THREE.Vector2(0.5, 0.5);
    const smoothMouse = new THREE.Vector2(0.5, 0.5);
    const clock = new THREE.Clock();
    let animationFrame = 0;

    const resize = () => {
        const { clientWidth, clientHeight } = target;
        renderer.setPixelRatio(props.dpr);
        renderer.setSize(clientWidth, clientHeight, false);
        uniforms.uResolution.value.set(clientWidth * props.dpr, clientHeight * props.dpr);
    };

    const pointerMove = (event) => {
        mouse.set(event.clientX / window.innerWidth, 1.0 - event.clientY / window.innerHeight);
    };

    const render = () => {
        const delta = Math.min(clock.getDelta(), 0.05);
        uniforms.uTime.value += delta;
        smoothMouse.lerp(mouse, Math.min(1, delta / Math.max(props.mouseSmoothTime, 0.001)));
        uniforms.uMouse.value.copy(smoothMouse);
        renderer.render(scene, camera);
        animationFrame = requestAnimationFrame(render);
    };

    const observer = new ResizeObserver(resize);
    observer.observe(target);
    window.addEventListener('pointermove', pointerMove, { passive: true });
    resize();
    render();

    return {
        destroy() {
            cancelAnimationFrame(animationFrame);
            observer.disconnect();
            window.removeEventListener('pointermove', pointerMove);
            geometry.dispose();
            material.dispose();
            renderer.dispose();
            renderer.domElement.remove();
        },
    };
}

export default mountLaserFlow;
