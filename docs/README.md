# DnDaedrAI

A D&D campaign management application with AI-assisted content generation. Built for Dungeon Masters who want to organize their campaigns, track characters, manage world-building, and (soon) leverage AI to generate NPCs, encounters, and story content.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12 (PHP 8.2+) |
| Frontend | Vue 3 + TypeScript |
| Bridge | Inertia.js |
| Database | SQLite |
| Styling | Tailwind CSS |
| Auth | Laravel Breeze + Sanctum |

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/DnDaedrAI.git
cd DnDaedrAI

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

### Development

```bash
# Run both Laravel and Vite dev servers
npm run dev
```

Visit `http://localhost:8000` in your browser.

## Project Structure

```
DnDaedrAI/
├── app/
│   ├── Http/Controllers/     # Request handlers
│   ├── Models/               # Eloquent models
│   └── Services/             # Business logic (future)
├── database/
│   └── migrations/           # Database schema
├── resources/
│   ├── js/
│   │   ├── Components/       # Reusable Vue components
│   │   ├── Layouts/          # Page layouts
│   │   └── Pages/            # Inertia pages
│   └── css/                  # Global styles
├── routes/
│   └── web.php               # Application routes
└── docs/                     # This documentation
```

## Documentation

- [Architecture](./ARCHITECTURE.md) - System design and tech stack details
- [Data Models](./DATA_MODELS.md) - Database schema and relationships
- [API Routes](./API_ROUTES.md) - All endpoints and their purposes
- [Frontend](./FRONTEND.md) - Vue components and page structure
- [Design System](./DESIGN_SYSTEM.md) - Color palette and styling guide
- [Features](./FEATURES.md) - Complete feature list and status

## Core Concepts

### Knowledge Graph
The application uses a **node-edge graph** to represent campaign data:
- **Nodes** = Entities (Characters, Places, Items, Factions, Plots)
- **Edges** = Relationships between entities

This allows flexible querying and visualization of how everything in your campaign connects.

### Campaign-Scoped Data
All data belongs to a campaign, which belongs to a user. This ensures complete data isolation between campaigns and users.

### Confidence Levels
All nodes have a `confidence` field:
- `canon` - Established facts in your campaign
- `likely` - Probable but not confirmed
- `rumor` - In-world rumors that may be false
- `unknown` - Placeholder or uncertain information

### Secret Content
Nodes can be marked as `is_secret` to hide them from any future player-facing views.

## License

MIT
