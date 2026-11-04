@extends('layouts.portfolio')

@section('content')
    @php
        $profile = config('portfolio.profile');
        $about = config('portfolio.about');
        $projects = config('portfolio.projects');
        $skills = config('portfolio.skills');
        $journey = config('portfolio.journey');
        $contact = config('portfolio.contact');
    @endphp

    {{-- ---------------------------------------------------------------- --}}
    {{-- The desk                                                           --}}
    {{-- ---------------------------------------------------------------- --}}
    <section class="desk-section" id="top" aria-labelledby="desk-heading">
        <div class="desk-intro">
            <p class="eyebrow">{{ $profile['handle'] }} &mdash; {{ $profile['location'] }}</p>
            <h1 class="display-title" id="desk-heading">
                A student desk, wired to a <em>terminal</em>.
            </h1>
            <p class="lede">{{ $profile['tagline'] }}</p>
        </div>

        <div class="desk" data-desk data-terminal-state="collapsed">
            <div class="desk__cell desk__cell--laptop">
                <x-desk.laptop />
            </div>

            <div class="desk__cell desk__cell--notebook">
                <x-desk.notebook />
            </div>

            <div class="desk__cell desk__cell--coffee">
                <x-desk.coffee />
            </div>

            <div class="desk__cell desk__cell--tools">
                <x-desk.tool-rack />
            </div>

            <div class="desk__cell desk__cell--notes">
                <x-desk.sticky-notes />
            </div>

            <div class="desk__cell desk__cell--folder">
                <x-desk.project-folder />
            </div>

            <div class="desk__cell desk__cell--journey">
                <x-desk.journey-scroll />
            </div>

            <div class="desk__cell desk__cell--postcard">
                <x-desk.postcard />
            </div>

            <div class="desk__cell desk__cell--panel" id="terminal-panel" data-terminal-panel>
                <div class="terminal-panel">
                    <x-terminal />
                </div>
            </div>
        </div>
    </section>

    {{-- ---------------------------------------------------------------- --}}
    {{-- About                                                              --}}
    {{-- ---------------------------------------------------------------- --}}
    <div class="sections">
        <section class="section" id="about" aria-labelledby="about-heading">
            <div class="section__head">
                <p class="eyebrow">01 &mdash; about</p>
                <h2 class="section__title" id="about-heading">Who is typing this?</h2>
            </div>

            <div class="paper section__body">
                <div class="prose">
                    @foreach ($about['paragraphs'] as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        {{-- Projects                                                           --}}
        {{-- ---------------------------------------------------------------- --}}
        <section class="section" id="projects" aria-labelledby="projects-heading">
            <div class="section__head">
                <p class="eyebrow">02 &mdash; projects</p>
                <h2 class="section__title" id="projects-heading">The project folder</h2>
            </div>

            <div class="paper section__body">
                @if (count($projects) > 0)
                    <div class="project-grid" data-project-grid>
                        @foreach ($projects as $slug => $project)
                            <button
                                type="button"
                                class="project-card"
                                data-project-card
                                data-project-open="{{ $slug }}"
                            >
                                <span class="project-card__title">{{ $project['title'] }}</span>
                                <span class="project-card__summary">{{ $project['summary'] }}</span>
                                <span class="project-card__stack">
                                    @foreach ($project['stack'] as $technology)
                                        <span>{{ $technology }}</span>
                                    @endforeach
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <nav class="project-pagination" data-project-pagination aria-label="Project pages" hidden></nav>
                @else
                    <div class="empty-state">
                        <p class="empty-state__title">The project folder is empty. The desk is not.</p>
                        <p class="empty-state__body">
                            Nothing gets filed here until it builds, survives its own write-up and
                            can be shown without a disclaimer. Everything else is still on the desk,
                            somewhere between the coffee and the 2 AM bug &mdash; ask me what's
                            currently there.
                        </p>
                        <a class="empty-state__link" href="#contact">ask what's on the desk &rarr;</a>
                    </div>
                @endif
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        {{-- Skills                                                             --}}
        {{-- ---------------------------------------------------------------- --}}
        <section class="section" id="skills" aria-labelledby="skills-heading">
            <div class="section__head">
                <p class="eyebrow">03 &mdash; skills</p>
                <h2 class="section__title" id="skills-heading">The tool rack</h2>
            </div>

            <div class="paper section__body">
                <div class="skill-group">
                    <p class="skill-group__title">Active</p>
                    <ul class="skill-tags">
                        @foreach ($skills['active'] as $skill)
                            <li class="skill-tag">{{ $skill }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="skill-group">
                    <p class="skill-group__title">Learning</p>
                    <ul class="skill-tags">
                        @foreach ($skills['learning'] as $skill)
                            <li class="skill-tag skill-tag--learning">{{ $skill }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        {{-- Journey                                                            --}}
        {{-- ---------------------------------------------------------------- --}}
        <section class="section" id="journey" aria-labelledby="journey-heading">
            <div class="section__head">
                <p class="eyebrow">04 &mdash; journey</p>
                <h2 class="section__title" id="journey-heading">How I got here</h2>
            </div>

            <div class="paper section__body">
                <ol class="timeline">
                    @foreach ($journey as $phase)
                        <li class="timeline__item">
                            <p class="timeline__grade">{{ $phase['grade'] }}</p>
                            <h3 class="timeline__title">{{ $phase['title'] }}</h3>
                            <p class="timeline__detail">{{ $phase['detail'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        {{-- Contact                                                            --}}
        {{-- ---------------------------------------------------------------- --}}
        <section class="section" id="contact" aria-labelledby="contact-heading">
            <div class="section__head">
                <p class="eyebrow">05 &mdash; contact</p>
                <h2 class="section__title" id="contact-heading">Send a postcard</h2>
            </div>

            <div class="paper section__body">
                <ul class="contact-list">
                    @foreach ($contact['links'] as $link)
                        <li>
                            <a class="contact-link" href="{{ $link['url'] }}" rel="noopener noreferrer" target="_blank">
                                <span class="contact-link__label">{{ $link['label'] }}</span>
                                <span class="contact-link__value">{{ $link['value'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <p class="contact-note">
                    Based in {{ $contact['location'] }} &mdash; reply usually arrives faster than a
                    letter.
                </p>
            </div>
        </section>
    </div>

    {{-- ---------------------------------------------------------------- --}}
    {{-- Project detail dialog (shared by the cards and the terminal)         --}}
    {{-- ---------------------------------------------------------------- --}}
    <dialog class="project-dialog" id="project-dialog" aria-labelledby="project-dialog-title">
        <div class="project-dialog__inner">
            <button type="button" class="project-dialog__close" data-project-close aria-label="Close project details">
                &times;
            </button>
            <h3 class="project-dialog__title" id="project-dialog-title"></h3>
            <p class="project-dialog__status" data-project-status></p>
            <p class="project-dialog__summary" data-project-summary></p>
            <div class="project-card__stack" data-project-stack></div>
            <a class="project-dialog__link" data-project-link href="#" target="_blank" rel="noopener noreferrer">
                open the repository &rarr;
            </a>
        </div>
    </dialog>

    <script type="application/json" id="portfolio-manifest">@json(config('portfolio'))</script>
@endsection
