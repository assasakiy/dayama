# Landing Module Architecture

## Overview
The landing module manages the public-facing website (non-blog) pages.
Pages are section-based: each page has multiple sections with structured JSON content stored in a `landing_sections` table.

## Database Schema

### `landing_pages` table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| name | string | Display name (e.g., "Home", "Profil") |
| slug | string | URL slug |
| is_active | boolean | Whether the page is live |
| order | int | Sort order |
| created_at / updated_at | timestamps | |

### `landing_sections` table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| landing_page_id | bigint | FK to landing_pages |
| key | string | Section identifier (e.g., "hero", "features", "stats") |
| content | json | Structured content blob |
| order | int | Sort order within the page |

### `landing_ctas` table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| name | string | Internal name |
| title | string | Display title |
| subtitle | string | |
| button_text | string | |
| button_url | string | |
| style | string | Style variant |
| is_active | boolean | |

### `landing_faqs` table
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | PK |
| question | string | |
| answer | text | |
| order | int | Sort order |
| is_active | boolean | |

## Default Pages (from LandingSeeder)
1. **Home** (`/`) — Sections: hero, features, stats, programs, cta
2. **Profil** (`/profil`) — About page
3. **Pendidikan** (`/pendidikan`) — Education programs
4. **Layanan** (`/layanan`) — Services
5. **Media** (`/media`) — Media/gallery

## Section Types & Content Schema

### Hero Section
```json
{
  "title": "string",
  "subtitle": "string",
  "image_url": "string",
  "button_text": "string",
  "button_url": "string"
}
```

### Features/Programs Section (has `items` array)
```json
{
  "title": "string",
  "subtitle": "string",
  "items": [
    { "title": "string", "description": "string", "icon": "string", "url": "string" }
  ]
}
```

### Stats Section (has `items` array)
```json
{
  "title": "string",
  "subtitle": "string",
  "items": [
    { "value": "string", "label": "string" }
  ]
}
```

## Controller Routing

### `LandingController` (Dashboard)
| Method | Route | Description |
|--------|-------|-------------|
| GET | `/landing/pages` | Pages index |
| GET | `/landing/pages/{id}/edit` | Edit page sections |
| PUT | `/landing/pages/{id}/sections/{key}` | Update section |
| PUT | `/landing/pages/{id}/sections` | Update all sections |
| GET/POST/PUT/DELETE | `/landing/faqs` | FAQ CRUD |
| GET/POST/PUT/DELETE | `/landing/ctas` | CTA CRUD |

## Frontend File Map

```
Pages/Landing/
├── Pages/
│   ├── Index.tsx     → Card grid of pages, click to edit
│   └── Edit.tsx      → Section-based editor with tabs
├── Ctas/
│   └── Index.tsx     → Card grid + create/edit modal
└── Faqs/
    └── Index.tsx     → List + create/edit modal

# NOTE: Institutions telah dipindah ke module terpisah.
# Lihat references/institutions-module.md
```

## Edit Page Section Rendering
The `Pages/Edit.tsx` detects section type by key name and renders appropriate UI:
- `hero`, `cta` → Simple text fields + image picker
- `features`, `programs`, `services` → Items list with title/description/icon/url (uses `<IconPicker>` for icon field)
- `stats` → Items list with value/label
- Default → Generic key-value editor
