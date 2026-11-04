# Portfolio

A personal portfolio website for **Muhammad Akbar Kevin Kanigara** — a Software Engineering student (RPL) at SMKN 2 Bandung, Indonesia.

Built with Laravel 13, Blade, Tailwind CSS, Alpine.js, and Vite. The site uses a "student desk" metaphor with an interactive terminal that answers commands.

---

## Features

- **Single-page layout** with smooth scroll navigation
- **Interactive desk** — laptop, notebook, coffee, tools, sticky notes, folder, journey scroll, postcard
- **Functional terminal** — type `help` to see available commands (`whoami`, `about`, `projects`, `skills`, `journey`, `contact`, `clear`, `sudo`)
- **Project gallery** — filterable, paginated cards with detail dialogs
- **Timeline journey** — from Grade 6 to present
- **Single source of truth** — all content lives in `config/portfolio.php`; Blade views and the terminal both read from it
- **Responsive** — works on mobile and desktop
- **Accessible** — semantic HTML, ARIA labels, skip links, keyboard navigation

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13 (PHP 8.3+) |
| Frontend | Blade, Tailwind CSS 4, Alpine.js 3, Vite 8 |
| Fonts | Lora, Inter, JetBrains Mono, Caveat (via Bunny Fonts) |
| Dev tools | Pest, Laravel Pint, Laravel Pail |

---

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- Node.js 18+ (and npm)

### Installation

```bash
# Clone the repository
git clone https://github.com/KevinTadashiii/My-Portfolio.git
cd My-Portfolio

# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Build frontend assets
npm run build

# Start the development server
composer run dev
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Project Structure

```
├── app/
│   ├── Http/Controllers/SitemapController.php
│   └── Providers/
├── config/
│   └── portfolio.php        # All site content lives here
├── database/
├── public/
│   ├── build/               # Vite-compiled assets
│   └── favicon.*
├── resources/
│   ├── css/app.css
│   ├── js/
│   │   ├── app.js
│   │   ├── desk.js          # Desk interactions, project cards
│   │   └── terminal.js      # Terminal logic, commands, boot sequence
│   └── views/
│       ├── components/
│       │   ├── desk/        # Blade components for each desk item
│       │   └── terminal.blade.php
│       ├── layouts/
│       │   └── portfolio.blade.php
│       └── welcome.blade.php # Main page (extends portfolio layout)
├── routes/web.php
└── tests/
```

---

## Customizing Content

All copy, projects, skills, journey phases, and terminal behavior are defined in **`config/portfolio.php`**. Edit that file — no template changes needed.

### Adding a Project

1. Add a new entry to the `projects` array in `config/portfolio.php` with a unique slug:
   ```php
   'my-new-project' => [
       'title' => 'Project Title',
       'summary' => 'One-line description.',
       'stack' => ['Laravel', 'Tailwind'],
       'status' => 'live',        // live | in_progress | complete
       'url' => 'https://github.com/...',
   ],
   ```
2. The project appears automatically on the page and in the terminal (`projects` command).

### Updating the Terminal

- `terminal.boot` — lines shown on load
- `terminal.suggestions` — tab-completion hints
- `terminal.prompt` — prompt string

### Skills

Two lists: `skills.active` and `skills.learning`.

### Journey

Add phases to `journey` with `grade`, `title`, and `detail`.

---

## Testing

```bash
# Run the test suite (Pest)
composer test
# or
php artisan test
```

---

## Frontend Development

```bash
# Start Vite dev server with HMR
npm run dev

# Production build
npm run build
```

---

## Deployment

The project is a standard Laravel application. Deploy to any platform that supports PHP:

- **Laravel Cloud** (recommended)
- Forge + Vapor
- Railway, Render, Fly.io
- Shared hosting (with `public/` as document root)

### Laravel Cloud

```bash
# Install the Cloud CLI
curl -fsSL https://cloud.laravel.com/install.sh | bash

# Login and deploy
cloud login
cloud deploy
```

---

## License

MIT License — see [LICENSE](LICENSE) if present, otherwise the standard MIT terms apply.

---

## Author

**Muhammad Akbar Kevin Kanigara**  
Software Engineering Student (RPL) — SMKN 2 Bandung  
Bandung, Indonesia  

- GitHub: [@KevinTadashiii](https://github.com/KevinTadashiii)
- Email: [kevintadashiii@gmail.com](mailto:kevintadashiii@gmail.com)
- Instagram: [@bayabaytjn_](https://www.instagram.com/bayabaytjn_)

---

> Built with Laravel and coffee from the terminal.