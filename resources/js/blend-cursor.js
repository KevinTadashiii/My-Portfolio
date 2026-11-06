/**
 * Blend cursor (after DeepSeek's "Into the Unknown" hover).
 *
 * A white circle drawn on a fixed, full-viewport canvas with
 * `mix-blend-mode: difference` trails the pointer while it hovers the
 * hero title's real text, so the title text inverts as the circle passes
 * over it. The circle grows (radius 16 -> 32) while it is over text and
 * fades in/out smoothly. Hovering the heading's empty box (padding,
 * line gaps, space after the text) does not activate it — only glyphs.
 * The native cursor is left alone.
 *
 * Only runs on fine pointers (mouse / pen) without reduced motion.
 * Touch devices and `prefers-reduced-motion` keep the static title.
 */

const BLEND_TARGET_SELECTOR = '[data-cursor="blend"]';
const CANVAS_CLASS = 'blend-cursor-canvas';
const MAX_RADIUS = 32;
const REST_SCALE = 0.5;
const HOVER_SCALE = 1;
const LERP_RATE = 0.24;
const NEAR_DISTANCE = 50;
const NEAR_RATE = 0.7;
const FAR_RATE = 0.4;
const SETTLE_EPSILON = 0.002;
const MAX_DPR = 2;

function canHover() {
    if (typeof window.matchMedia !== 'function') {
        return false;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return false;
    }

    if (window.matchMedia('(hover: none), (pointer: coarse)').matches) {
        return false;
    }

    return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
}

function isBlendTarget(element) {
    return element instanceof Element && element.closest(BLEND_TARGET_SELECTOR) !== null;
}

function rectContainsPoint(rects, x, y) {
    for (let index = 0; index < rects.length; index += 1) {
        const rect = rects[index];

        if (x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom) {
            return true;
        }
    }

    return false;
}

function isCharRectHit(node, offset, x, y) {
    if (!(node instanceof Text)) {
        return false;
    }

    const text = node.textContent ?? '';

    if (text.length === 0) {
        return false;
    }

    // Caret points sit between characters, so check the character on
    // each side of the caret — either one counts as a glyph hit.
    const first = Math.max(offset - 1, 0);
    const last = Math.min(offset, text.length - 1);

    for (let start = first; start <= last; start += 1) {
        const range = document.createRange();
        range.setStart(node, start);
        range.setEnd(node, start + 1);

        try {
            if (rectContainsPoint(range.getClientRects(), x, y)) {
                return true;
            }
        } finally {
            range.detach();
        }
    }

    return false;
}

function isTextNodeRectHit(target, x, y) {
    const walker = document.createTreeWalker(target, NodeFilter.SHOW_TEXT);
    let node = walker.nextNode();

    while (node) {
        if ((node.textContent ?? '').length > 0) {
            const range = document.createRange();
            range.selectNodeContents(node);

            try {
                if (rectContainsPoint(range.getClientRects(), x, y)) {
                    return true;
                }
            } finally {
                range.detach();
            }
        }

        node = walker.nextNode();
    }

    return false;
}

/**
 * True only when the pointer is over a real glyph of the title.
 * Uses caret hit-testing so the heading box, line gaps, and space
 * after the text do not activate the effect. Falls back to
 * text-node rects where caret APIs are unavailable.
 */
function isPointerOverText(target, x, y) {
    try {
        if (typeof document.caretPositionFromPoint === 'function') {
            const position = document.caretPositionFromPoint(x, y);

            if (!position || !position.offsetNode || !target.contains(position.offsetNode)) {
                return false;
            }

            if (position.offsetNode instanceof Text) {
                return isCharRectHit(position.offsetNode, position.offset, x, y);
            }

            return isTextNodeRectHit(target, x, y);
        }

        if (typeof document.caretRangeFromPoint === 'function') {
            const range = document.caretRangeFromPoint(x, y);

            if (!range || !target.contains(range.startContainer)) {
                return false;
            }

            if (range.startContainer instanceof Text) {
                return isCharRectHit(range.startContainer, range.startOffset, x, y);
            }

            return isTextNodeRectHit(target, x, y);
        }
    } catch {
        return false;
    }

    return isTextNodeRectHit(target, x, y);
}

export function initBlendCursor() {
    const target = document.querySelector(BLEND_TARGET_SELECTOR);

    if (!target || !canHover()) {
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.className = CANVAS_CLASS;
    canvas.setAttribute('aria-hidden', 'true');
    document.body.appendChild(canvas);

    const context = canvas.getContext('2d');

    if (!context) {
        canvas.remove();
        return;
    }

    let targetX = 0;
    let targetY = 0;
    let cursorX = 0;
    let cursorY = 0;
    let opacity = 0;
    let grow = REST_SCALE;
    let hovered = false;
    let frameId = 0;
    let lastX = 0;
    let lastY = 0;
    let lastRadius = 0;

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, MAX_DPR);
        const width = Math.round(window.innerWidth * dpr);
        const height = Math.round(window.innerHeight * dpr);

        if (canvas.width === width && canvas.height === height) {
            return;
        }

        canvas.width = width;
        canvas.height = height;
        context.setTransform(dpr, 0, 0, dpr, 0, 0);
        opacity = 0;
        hovered = false;
        lastRadius = 0;
    }

    function clearLastCircle() {
        if (lastRadius > 0) {
            context.clearRect(lastX - lastRadius - 2, lastY - lastRadius - 2, (lastRadius + 2) * 2, (lastRadius + 2) * 2);
        }
    }

    function step() {
        const dx = targetX - cursorX;
        const dy = targetY - cursorY;
        const rate = Math.hypot(dx, dy) < NEAR_DISTANCE ? NEAR_RATE : FAR_RATE;

        cursorX += dx * rate;
        cursorY += dy * rate;
        opacity += ((hovered ? 1 : 0) - opacity) * LERP_RATE;
        grow += ((hovered ? HOVER_SCALE : REST_SCALE) - grow) * LERP_RATE;

        clearLastCircle();

        const radius = MAX_RADIUS * grow;

        if (opacity > SETTLE_EPSILON) {
            context.save();
            context.globalAlpha = opacity;
            context.fillStyle = '#fff';
            context.beginPath();
            context.arc(cursorX, cursorY, radius, 0, Math.PI * 2);
            context.fill();
            context.restore();
            lastX = cursorX;
            lastY = cursorY;
            lastRadius = radius;
        } else {
            lastRadius = 0;
        }

        const positionSettled = Math.abs(dx) + Math.abs(dy) < 0.1;
        const opacitySettled = Math.abs((hovered ? 1 : 0) - opacity) < SETTLE_EPSILON;
        const growSettled = Math.abs((hovered ? HOVER_SCALE : REST_SCALE) - grow) < SETTLE_EPSILON;

        if (positionSettled && opacitySettled && growSettled) {
            cursorX = targetX;
            cursorY = targetY;
            opacity = hovered ? 1 : 0;
            grow = hovered ? HOVER_SCALE : REST_SCALE;
            frameId = 0;
            return;
        }

        frameId = window.requestAnimationFrame(step);
    }

    function ensureLoop() {
        if (!frameId && !document.hidden) {
            frameId = window.requestAnimationFrame(step);
        }
    }

    function onPointerMove(event) {
        if (!isBlendTarget(event.target)) {
            hovered = false;

            if (opacity > SETTLE_EPSILON || lastRadius > 0) {
                ensureLoop();
            }

            return;
        }

        targetX = event.clientX;
        targetY = event.clientY;

        if (opacity <= SETTLE_EPSILON && lastRadius === 0) {
            cursorX = targetX;
            cursorY = targetY;
        }

        hovered = isPointerOverText(target, event.clientX, event.clientY);

        if (hovered || opacity > SETTLE_EPSILON || lastRadius > 0) {
            ensureLoop();
        }
    }

    function onPointerLeave() {
        hovered = false;

        if (opacity > SETTLE_EPSILON || lastRadius > 0) {
            ensureLoop();
        }
    }

    function onScroll() {
        onPointerLeave();
    }

    function onVisibilityChange() {
        if (document.hidden) {
            if (frameId) {
                window.cancelAnimationFrame(frameId);
                frameId = 0;
            }

            return;
        }

        if (hovered || opacity > SETTLE_EPSILON) {
            ensureLoop();
        }
    }

    resize();
    window.addEventListener('resize', resize);

    target.addEventListener('pointermove', onPointerMove, { passive: true });
    target.addEventListener('pointerover', onPointerMove, { passive: true });
    target.addEventListener('pointerleave', onPointerLeave, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
    document.addEventListener('visibilitychange', onVisibilityChange);
}
