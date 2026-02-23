# DnDaedrAI

A D&D campaign management application with AI-assisted content generation for Dungeon Masters.

## Features

- **Campaign Management** - Create and manage multiple campaigns
- **Knowledge Graph** - Characters, Places, Items, Factions, Plots with relationships
- **Session Planning** - Plan, track, and recap game sessions
- **Session 0 Setup** - Guided questionnaire for campaign setup
- **Global Search** - Search across all campaign content
- **Dark Theme** - Custom "Mystic Arcanist" design system
- **AI Ready** - Settings for OpenAI/Anthropic API keys (AI features coming soon)

## Tech Stack

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vue 3 + TypeScript
- **Bridge:** Inertia.js
- **Database:** SQLite
- **Styling:** Tailwind CSS

## Quick Start

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build and serve
npm run build
php artisan serve
```

## Documentation

Full documentation is available in the [docs/](./docs/) folder:

- [Project Overview](./docs/README.md)
- [Architecture](./docs/ARCHITECTURE.md) - System design and tech stack
- [Data Models](./docs/DATA_MODELS.md) - Database schema and relationships
- [API Routes](./docs/API_ROUTES.md) - All endpoints documented
- [Frontend](./docs/FRONTEND.md) - Vue components and patterns
- [Design System](./docs/DESIGN_SYSTEM.md) - Colors, styling, components
- [Features](./docs/FEATURES.md) - Complete feature list and status

## License

MIT
