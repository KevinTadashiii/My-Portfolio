import { DeskTerminal } from './terminal.js';
import { initBlendCursor } from './blend-cursor.js';

/**
 * Desk behaviour: the terminal lives in exactly one place at a time.
 *
 * Collapsed it sits inside the laptop screen; expanded it is docked into the
 * panel next to the laptop, wired to it with a cord. It is the same element
 * either way, so there is never a second command input.
 */

const STATUS_LABELS = {
    live: 'Live',
    complete: 'Complete',
    in_progress: 'In progress',
    archived: 'Archived',
};

function readManifest() {
    const node = document.getElementById('portfolio-manifest');

    if (!node) {
        return {};
    }

    try {
        return JSON.parse(node.textContent);
    } catch {
        return {};
    }
}

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

const PROJECTS_PER_PAGE = 5;

function initProjectPagination() {
    const grid = document.querySelector('[data-project-grid]');
    const pagination = document.querySelector('[data-project-pagination]');

    if (!grid || !pagination) {
        return;
    }

    const cards = Array.from(grid.querySelectorAll('[data-project-card]'));
    const pageCount = Math.ceil(cards.length / PROJECTS_PER_PAGE);

    if (pageCount <= 1) {
        return;
    }

    let currentPage = 1;

    function createButton(label, { page, disabled = false, current = false, ariaLabel }) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'project-pagination__button';
        button.textContent = label;
        button.disabled = disabled;

        if (ariaLabel) {
            button.setAttribute('aria-label', ariaLabel);
        }

        if (current) {
            button.setAttribute('aria-current', 'page');
        }

        if (page !== undefined) {
            button.addEventListener('click', () => {
                if (page === currentPage) {
                    return;
                }

                currentPage = page;
                render();
                pagination.querySelector('[aria-current="page"]')?.focus({ preventScroll: true });
            });
        }

        return button;
    }

    function render() {
        cards.forEach((card, index) => {
            card.hidden = Math.floor(index / PROJECTS_PER_PAGE) + 1 !== currentPage;
        });

        pagination.replaceChildren(
            createButton('← Prev', {
                page: currentPage - 1,
                disabled: currentPage === 1,
                ariaLabel: 'Previous project page',
            }),
            ...Array.from({ length: pageCount }, (_, index) => {
                const page = index + 1;

                return createButton(String(page), {
                    page,
                    current: page === currentPage,
                    ariaLabel: `Project page ${page}`,
                });
            }),
            createButton('Next →', {
                page: currentPage + 1,
                disabled: currentPage === pageCount,
                ariaLabel: 'Next project page',
            }),
        );

        pagination.hidden = false;
    }

    render();
}

/* Navbar sliding marker */
function initNavMarker() {
    const nav = document.querySelector('.site-nav');
    const marker = document.querySelector('.site-nav__marker');
    const links = Array.from(nav?.querySelectorAll('a') ?? []);
    const sections = links
        .map((link) => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

    if (!nav || !marker || links.length === 0) {
        return;
    }

    let activeIndex = 0;

    function setMarker(index) {
        if (index === activeIndex) {
            return;
        }
        activeIndex = index;
        const link = links[index];
        marker.style.transform = `translateX(${link.offsetLeft}px) scaleX(1)`;
        marker.style.width = `${link.offsetWidth}px`;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const index = sections.indexOf(entry.target);
                    if (index !== -1) {
                        setMarker(index);
                    }
                }
            });
        },
        { threshold: 0.25, rootMargin: '-50% 0px -50% 0px' }
    );

    sections.forEach((section) => observer.observe(section));
    setMarker(0);
}

export function initDesk() {
    initProjectPagination();
    initNavMarker();
    initBlendCursor();

    const desk = document.querySelector('[data-desk]');
    const terminalRoot = document.querySelector('[data-terminal]');

    if (!desk || !terminalRoot) {
        return;
    }

    const manifest = readManifest();
    const screenSlot = document.querySelector('[data-terminal-screen-slot]');
    const panelSlot = document.querySelector('[data-terminal-panel]');
    const panelHost = panelSlot?.querySelector('.terminal-panel') ?? null;
    const toggleButtons = Array.from(document.querySelectorAll('[data-terminal-toggle]'));

    const terminal = new DeskTerminal(terminalRoot, manifest);
    terminal.mount();

    let expanded = false;

    function placeTerminal() {
        const host = expanded ? panelHost : screenSlot;

        if (host && terminalRoot.parentElement !== host) {
            host.appendChild(terminalRoot);
        }

        terminalRoot.dataset.placement = expanded ? 'panel' : 'screen';
        desk.dataset.terminalState = expanded ? 'expanded' : 'collapsed';

        toggleButtons.forEach((button) => {
            button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
    }

    function setExpanded(next) {
        expanded = next;
        placeTerminal();

        if (!expanded) {
            return;
        }

        if (window.matchMedia('(max-width: 767px)').matches) {
            panelSlot?.scrollIntoView({
                behavior: prefersReducedMotion() ? 'auto' : 'smooth',
                block: 'center',
            });
        }
    }

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => setExpanded(!expanded));
    });

    /* --------------------------------------------------------------------
     | Project dialog — one card, shared by the section and the terminal
     | ------------------------------------------------------------------ */

    const dialog = document.getElementById('project-dialog');

    function openProject(slug) {
        const project = manifest.projects?.[slug];

        if (!project || !dialog) {
            return;
        }

        dialog.querySelector('#project-dialog-title').textContent = project.title;
        dialog.querySelector('[data-project-status]').textContent =
            `Status: ${STATUS_LABELS[project.status] ?? 'Unlisted'}`;
        dialog.querySelector('[data-project-summary]').textContent = project.summary;

        const stack = dialog.querySelector('[data-project-stack]');
        stack.replaceChildren(
            ...(project.stack ?? []).map((technology) => {
                const tag = document.createElement('span');
                tag.textContent = technology;
                return tag;
            }),
        );

        const link = dialog.querySelector('[data-project-link]');
        link.hidden = !project.url;

        if (project.url) {
            link.href = project.url;
        }

        dialog.showModal();
    }

    document.querySelectorAll('[data-project-open]').forEach((button) => {
        button.addEventListener('click', () => openProject(button.dataset.projectOpen));
    });

    dialog?.querySelector('[data-project-close]')?.addEventListener('click', () => dialog.close());
    dialog?.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    window.addEventListener('portfolio:open-project', (event) => {
        openProject(event.detail?.slug);
    });

    /* --------------------------------------------------------------------
     | Reveal on scroll — progressive enhancement
     | Content is visible by default; animation only runs if JS initializes
     | and user hasn't requested reduced motion.
     | ------------------------------------------------------------------ */

    const revealTargets = Array.from(document.querySelectorAll('.reveal'));

    // Mark elements as animation candidates (sets initial hidden state via CSS)
    revealTargets.forEach((target) => target.classList.add('animate-in'));

    if ('IntersectionObserver' in window && !prefersReducedMotion()) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -6% 0px' },
        );

        revealTargets.forEach((target) => observer.observe(target));
    } else {
        // No IntersectionObserver or reduced motion: reveal immediately
        revealTargets.forEach((target) => target.classList.add('is-visible'));
    }

    placeTerminal();
}
