/**
 * A very small terminal emulator.
 *
 * Everything the terminal knows lives in the portfolio manifest that the page
 * embeds as JSON, so there is no second place to keep content in sync. The
 * parser is deliberately plain JavaScript — no terminal library, no framework.
 */

const COMMAND_REFERENCE = [
    ['help', 'list every command I know'],
    ['whoami', 'who is typing this'],
    ['about', 'the long version of the bio'],
    ['projects', 'published project entries'],
    ['project <slug>', 'details for a single project'],
    ['skills', 'active and learning skills'],
    ['journey', 'the learning timeline'],
    ['contact', 'public contact links'],
    ['clear', 'clear the screen'],
    ['sudo', 'try your luck'],
];

const STATUS_LABELS = {
    live: 'Live',
    complete: 'Complete',
    in_progress: 'In progress',
    archived: 'Archived',
};

export class DeskTerminal {
    constructor(root, manifest = {}) {
        this.root = root;
        this.manifest = manifest;
        this.output = root.querySelector('[data-terminal-output]');
        this.form = root.querySelector('[data-terminal-form]');
        this.input = root.querySelector('[data-terminal-input]');
        this.mirror = root.querySelector('[data-terminal-mirror]');
        this.caret = root.querySelector('.terminal__caret');
        this.field = root.querySelector('.terminal__field');
        this.prompt = manifest?.terminal?.prompt ?? 'kevin@desk:~$';
        this.history = [];
        this.historyIndex = 0;
        this.typeTimer = null;
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        this.commands = this.buildCommands();
    }

    /* --------------------------------------------------------------------
     | Wiring
     | ------------------------------------------------------------------ */

    mount() {
        if (!this.output || !this.form || !this.input || !this.mirror || !this.caret || !this.field) {
            return;
        }

        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.submit(this.input.value);
            this.setInputValue('');
        });

        this.form.addEventListener('click', (event) => {
            this.focusInput(event);
        });

        this.input.addEventListener('input', () => {
            this.syncCaret();
        });

        this.input.addEventListener('keyup', () => {
            this.syncCaret();
        });

        this.input.addEventListener('focus', () => {
            this.syncCaret();
        });

        document.addEventListener('selectionchange', () => {
            if (document.activeElement === this.input) {
                this.syncCaret();
            }
        });

        this.input.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowUp') {
                event.preventDefault();
                this.recallHistory(-1);
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                this.recallHistory(1);
            }

            if (event.key === 'Tab') {
                event.preventDefault();
                this.complete();
            }
        });

        this.root.querySelectorAll('[data-terminal-command]').forEach((button) => {
            button.addEventListener('click', () => {
                this.submit(button.dataset.terminalCommand ?? '');
            });
        });

        this.syncCaret();
        this.boot();
    }

    /**
     * Park the block caret at the text insertion point by measuring the text
     * before the selection in a hidden mirror with identical font metrics.
     * Accounts for the input scrolling once the command outgrows the field.
     */
    syncCaret() {
        const position = this.input.selectionStart ?? this.input.value.length;
        const hasSelection = this.input.selectionStart !== this.input.selectionEnd;

        this.caret.hidden = hasSelection;
        this.mirror.textContent = this.input.value.slice(0, position);

        const offset = this.mirror.getBoundingClientRect().width - this.input.scrollLeft;
        const maxOffset = Math.max(0, this.field.clientWidth - this.caret.offsetWidth);
        this.caret.style.left = `${Math.min(Math.max(offset, 0), maxOffset)}px`;
    }

    setInputValue(value) {
        this.input.value = value;
        this.input.setSelectionRange(value.length, value.length);
        this.syncCaret();
    }

    /**
     * Clicking anywhere on the prompt row focuses the input, so the prompt
     * behaves like a real shell rather than a separate label.
     */
    focusInput(event) {
        if (event.target === this.input) {
            return;
        }

        event.preventDefault();
        this.input.focus();
        this.input.setSelectionRange(this.input.value.length, this.input.value.length);
        this.syncCaret();
    }

    /* --------------------------------------------------------------------
     | Output primitives
     | ------------------------------------------------------------------ */

    writeLine(text, variant = 'default') {
        const line = document.createElement('div');
        line.className = `terminal__line terminal__line--${variant}`;
        line.textContent = text;
        this.output.append(line);
        this.scrollToBottom();

        return line;
    }

    /**
     * Write a line character by character. Resolves when the line is done.
     * Honours prefers-reduced-motion by rendering the text immediately.
     */
    typeLine(text, variant = 'default', { speed = 4, maxMs = 700 } = {}) {
        const line = this.writeLine('', variant);

        if (this.reducedMotion.matches || text.length === 0) {
            line.textContent = text;
            return Promise.resolve();
        }

        const step = Math.max(1, Math.round(text.length / (maxMs / speed)));
        let index = 0;

        return new Promise((resolve) => {
            const tick = () => {
                index = Math.min(text.length, index + step);
                line.textContent = text.slice(0, index);

                if (index >= text.length) {
                    resolve();
                    return;
                }

                this.typeTimer = window.setTimeout(tick, speed);
            };

            tick();
        });
    }

    scrollToBottom() {
        this.output.scrollTop = this.output.scrollHeight;
    }

    cancelTyping() {
        if (this.typeTimer) {
            window.clearTimeout(this.typeTimer);
            this.typeTimer = null;
        }
    }

    /* --------------------------------------------------------------------
     | Commands
     | ------------------------------------------------------------------ */

    buildCommands() {
        return {
            help: () => this.commandHelp(),
            whoami: () => this.commandWhoami(),
            about: () => this.commandAbout(),
            projects: () => this.commandProjects(),
            project: (args) => this.commandProject(args),
            skills: () => this.commandSkills(),
            journey: () => this.commandJourney(),
            contact: () => this.commandContact(),
            clear: () => this.commandClear(),
            sudo: () => this.commandSudo(),
        };
    }

    commandHelp() {
        this.writeLine('AVAILABLE COMMANDS', 'heading');
        this.writeLine('');

        COMMAND_REFERENCE.forEach(([usage, summary]) => {
            this.writeLine(`  ${usage.padEnd(16)} ${summary}`);
        });

        this.writeLine('');
        this.writeLine('Commands are case-insensitive.', 'muted');
    }

    commandWhoami() {
        const profile = this.manifest.profile ?? {};
        this.writeLine(
            `${profile.name ?? 'Kevin Kanigara'} — ${profile.role ?? 'Software Engineering Student'} (${profile.city ?? 'Bandung'})`,
        );
    }

    commandAbout() {
        const about = this.manifest.about ?? { paragraphs: [], links: [] };

        (about.paragraphs ?? []).forEach((paragraph) => {
            this.writeLine(paragraph);
            this.writeLine('');
        });

        (about.links ?? []).forEach((link) => {
            this.writeLine(`${link.label}: ${link.value}`, 'accent');
            this.writeLine(link.url);
        });
    }

    commandProjects() {
        const projects = Object.entries(this.manifest.projects ?? {});

        if (projects.length === 0) {
            this.writeLine('The project folder is empty. The desk is not.');
            this.writeLine('Ask me directly — there is always something half-finished on the desk.', 'muted');
            return;
        }

        this.writeLine('PROJECTS', 'heading');
        this.writeLine('');

        const maxSlug = Math.max(...projects.map(([slug]) => slug.length), 0);

        projects.forEach(([slug, project]) => {
            const status = STATUS_LABELS[project.status] ?? 'Unlisted';
            this.writeLine(`  ${slug.padEnd(maxSlug + 2)} ${status}`);
        });

        this.writeLine('');
        this.writeLine('Use "project <slug>" for the details.', 'muted');
    }

    commandProject(args) {
        const slug = args[0];

        if (!slug) {
            this.writeLine('Usage: project <slug>', 'error');
            return;
        }

        const project = (this.manifest.projects ?? {})[slug];

        if (!project) {
            this.writeLine(`No project named "${slug}".`, 'error');
            this.writeLine('Type "projects" to see what is listed.', 'error');
            return;
        }

        this.writeLine(project.title, 'heading');
        this.writeLine(`Status: ${STATUS_LABELS[project.status] ?? 'Unlisted'}`, 'accent');
        this.writeLine('');
        this.writeLine(project.summary);
        this.writeLine('');
        this.writeLine(`Stack: ${(project.stack ?? []).join(', ')}`, 'muted');

        if (project.url) {
            this.writeLine(project.url, 'accent');
        }
    }

    commandSkills() {
        const skills = this.manifest.skills ?? { active: [], learning: [] };

        this.writeLine('ACTIVE', 'heading');
        this.writeLine(`  ${(skills.active ?? []).join(', ')}`);
        this.writeLine('');
        this.writeLine('LEARNING', 'heading');
        this.writeLine(`  ${(skills.learning ?? []).join(', ')}`);
    }

    commandJourney() {
        const journey = this.manifest.journey ?? [];

        this.writeLine('LEARNING JOURNEY', 'heading');
        this.writeLine('');

        journey.forEach((phase) => {
            this.writeLine(`${phase.grade} — ${phase.title}`, 'accent');
            this.writeLine(`  ${phase.detail}`);
            this.writeLine('');
        });
    }

    commandContact() {
        const contact = this.manifest.contact ?? { links: [], location: '' };

        this.writeLine('CONTACT', 'heading');
        this.writeLine('');

        (contact.links ?? []).forEach((link) => {
            this.writeLine(`  ${link.label.padEnd(10)} ${link.value}`);
            this.writeLine(`  ${' '.repeat(11)}${link.url}`, 'muted');
        });

        if (contact.location) {
            this.writeLine('');
            this.writeLine(`Based in ${contact.location}`, 'muted');
        }
    }

    commandClear() {
        this.cancelTyping();
        this.output.replaceChildren();
    }

    commandSudo() {
        this.writeLine('User is not in the sudoers file. This incident will be reported.', 'error');
    }

    /* --------------------------------------------------------------------
     | Input handling
     | ------------------------------------------------------------------ */

    submit(rawCommand) {
        const command = (rawCommand ?? '').trim();

        if (command === '') {
            return;
        }

        this.cancelTyping();
        this.history.unshift(command);
        this.historyIndex = 0;
        this.echo(command);
        this.execute(command);
    }

    echo(command) {
        const line = document.createElement('div');
        line.className = 'terminal__line terminal__echo';

        const prompt = document.createElement('span');
        prompt.className = 'terminal__prompt';
        prompt.textContent = this.prompt;

        const typed = document.createElement('span');
        typed.className = 'terminal__cmd';
        typed.textContent = command;

        line.append(prompt, typed);
        this.output.append(line);
        this.scrollToBottom();
    }

    execute(command) {
        const [name, ...args] = command.split(/\s+/);
        const handler = this.commands[name.toLowerCase()];

        if (!handler) {
            this.writeLine(`Command not recognized: ${name}`, 'error');
            this.writeLine("Type 'help' for a list.", 'error');
            return;
        }

        handler(args);
    }

    recallHistory(direction) {
        if (this.history.length === 0) {
            return;
        }

        this.historyIndex = Math.min(
            Math.max(this.historyIndex + direction, 0),
            this.history.length,
        );

        this.setInputValue(this.history[this.historyIndex] ?? '');
    }

    complete() {
        const value = this.input.value.trim().toLowerCase();

        if (value === '') {
            return;
        }

        const match = Object.keys(this.commands).find((command) => command.startsWith(value));

        if (match) {
            this.setInputValue(match === 'project' ? `${match} ` : match);
            return;
        }

        this.writeLine(`No command starts with "${value}".`, 'error');
    }

    /* --------------------------------------------------------------------
     | Boot sequence
     | ------------------------------------------------------------------ */

    async boot() {
        const boot = this.manifest.terminal?.boot ?? [];

        for (const [text, variant] of boot) {
            await this.typeLine(text, variant);
        }
    }
}
