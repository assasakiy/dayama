# Project Rules — Test Blog (Laravel + Inertia + React)

## Tech Stack
- **Backend**: Laravel 12 (PHP 8.x), Inertia.js
- **Frontend**: React 19 + TypeScript, Vite 8, Tailwind CSS v4 (no tailwind.config.js — uses `@theme` in CSS)
- **UI Primitives**: Radix UI (`@radix-ui/react-dialog`, `@radix-ui/react-select`, `@radix-ui/react-tabs`, etc.)
- **Icons**: Lucide React
- **Build**: `npm run build` → `vite build` (always verify after changes)

## Project Structure
```
resources/
├── css/app.css              ← Design tokens (@theme block, oklch colors)
├── js/
│   ├── dashboard/
│   │   ├── Components/
│   │   │   ├── ui/          ← Reusable UI primitives (btn, badge, card, dialog, input, etc.)
│   │   │   ├── MediaPicker.tsx
│   │   │   ├── MediaViewer.tsx
│   │   │   ├── TipTapEditor.tsx
│   │   │   └── GlobalToast.tsx
│   │   ├── Layouts/
│   │   │   ├── DashboardLayout.tsx   ← Main sidebar + topbar layout
│   │   │   └── AccountSettingsLayout.tsx
│   │   ├── Pages/
│   │   │   ├── Dashboard.tsx
│   │   │   ├── Posts/        ← Blog CRUD (Index, Form, Show, Revisions)
│   │   │   ├── Categories/
│   │   │   ├── Tags/
│   │   │   ├── Comments/
│   │   │   ├── Media/
│   │   │   ├── Landing/      ← Landing page management
│   │   │   │   ├── Pages/    ← Section-based page editor (Index, Edit)
│   │   │   │   ├── Ctas/     ← Call-to-action blocks (Index with modal)
│   │   │   │   └── Faqs/     ← FAQ management (Index with modal)
│   │   │   ├── Institutions/  ← Lembaga pendidikan (module terpisah)
│   │   │   │   ├── Index.tsx  ← Daftar lembaga + create modal
│   │   │   │   └── Edit.tsx   ← Edit profil lembaga
│   │   │   ├── Settings/
│   │   │   ├── Users/
│   │   │   ├── Roles/
│   │   │   └── ...
│   │   ├── hooks/
│   │   ├── lib/
│   │   └── types/
│   └── web/                  ← Public-facing website JS
├── views/
│   ├── web/                  ← Blade templates for public site
│   └── app.blade.php         ← Root Inertia template
```

## Documentation References
Whenever there is a significant architectural or UI pattern change, **YOU MUST UPDATE THE RELEVANT REFERENCE DOCUMENTS** below to keep the project context up to date.

- `references/design-system.md`: Full token list and component API.
- `references/landing-module.md`: Landing page architecture.
- `references/institutions-module.md`: Institutions architecture.
- `references/rbac-module.md`: Roles, Permissions, and Access Management.

## UI/UX Rules (MANDATORY)

### Buttons
- **CRITICAL**: Always use the `<Btn>` component from `@dashboard/Components/ui/btn` for all buttons in the dashboard to ensure design consistency. Do not build custom button HTML.
- **Always use the `icon` prop** for icons. Never put `<Icon />` as a child of `<Btn>`.
  ```tsx
  // ✅ CORRECT
  <Btn icon={<Plus className="w-4 h-4" />}>Tambah</Btn>
  <Btn icon={<Trash2 className="w-4 h-4" />} />  // icon-only
  
  // ❌ WRONG — causes asymmetric padding
  <Btn><Plus className="w-4 h-4" /> Tambah</Btn>
  ```
- Variants: `primary` (default), `secondary`, `danger`, `ghost`, `outline`
- Sizes: `sm`, `md` (default), `lg`
- **Posts/Index uses inline `<Link>` with manual classes** for the "New Post" button — this is the one exception.

### Cards & Containers
- **CRITICAL**: Always use the standard `<Card>` component or standard classes `bg-background border border-border-subtle rounded-lg` for containers to ensure consistency.
- Inner sections: `bg-surface-muted/50` for subtle differentiation
- Hover: `hover:shadow-sm hover:border-primary/20`
- **Never use colored card backgrounds** — always white (`bg-background`)

### Tables
- Wrap in: `bg-background border border-border-subtle rounded-lg overflow-hidden`
- Header row: `border-b border-border-subtle bg-surface-muted/50`
- Body dividers: `divide-y divide-border-subtle`
- Row hover: `hover:bg-surface-muted/30 transition-colors`

### Page Layout Pattern
Every dashboard page follows this structure:
```tsx
<DashboardLayout>
    <Head title="Page Title" />
    <div className="space-y-5">
        {/* Header: title left, action right */}
        <div className="flex items-center justify-between">
            <div>
                <h1 className="text-xl font-semibold tracking-tight">Title</h1>
                <p className="text-sm text-muted-foreground mt-1">Description</p>
            </div>
            <Btn icon={<Plus className="w-4 h-4" />}>Action</Btn>
        </div>

        {/* Content */}
        ...
    </div>
</DashboardLayout>
```

### Dialogs & Modals
- **CRITICAL**: Use `Dialog` from `@dashboard/Components/ui/dialog` (Radix-based) for all popups and modals. Do not create custom modal logic.
- Structure: `DialogContent > DialogHeader > DialogTitle` + content + `DialogFooter`
- Footer buttons: `<Btn variant="outline">Batal</Btn>` + `<Btn>Simpan</Btn>`
- Delete confirmation: Use `ConfirmDialog` component

### Form Fields
- Use `<Input>` and `<Textarea>` from `@dashboard/Components/ui/` — they include label & error support
- Labels: Use the built-in `label` prop, or plain `<label className="text-sm font-medium">`
- **Never import `<Label>` from shadcn** — it doesn't exist. Use `<label>` HTML tag.

### Status Badges
- Use `<Badge>` from `@dashboard/Components/ui/badge`
- Active/Published: `className="bg-emerald-500/10 text-emerald-600 border-emerald-500/20"`
- Inactive/Draft: `className="bg-zinc-500/10 text-zinc-500 border-zinc-500/20"`

## Color Token Quick Reference
| Token | Light | Purpose |
|-------|-------|---------|
| `bg-background` | white | Page/card backgrounds |
| `bg-surface` | near-white | Elevated surfaces |
| `bg-surface-muted` | light grey | Subtle differentiation |
| `border-border-subtle` | light grey | Default borders |
| `text-foreground` | near-black | Primary text |
| `text-muted-foreground` | grey | Secondary text |
| `bg-primary` | Islamic green | Primary actions |
| `text-primary-foreground` | white | Text on primary bg |
| `bg-destructive` | red | Danger/delete actions |

## Sidebar Navigation
The sidebar in `DashboardLayout.tsx` uses a `baseMenuGroups` array (line ~80).
Landing pages are **dynamically injected** from `props.landing_pages` — replacing the generic "Pages" link with individual page edit links (Home, Profil, etc.).

**Institutions** is a **separate sidebar group** (not under Landing Management):
- Group: `Institusi Management`
- Items: Lembaga, Guru/Ustadz (🔜), Siswa (🔜), Kelas (🔜)
- See `references/institutions-module.md` for full architecture.

## Common Mistakes to Avoid
1. ❌ Don't import `Label` — use `<label>` tag
2. ❌ Don't put icons as `<Btn>` children — use `icon` prop
3. ❌ Don't add `mr-1` / `mr-2` to icons inside buttons — `gap` handles spacing
4. ❌ Don't use colored card backgrounds — keep `bg-background`
5. ❌ Don't use `tailwind.config.js` — all theming is in `app.css` `@theme`
6. ❌ Don't create custom buttons/cards/modals — always use the standard primitives from `@dashboard/Components/ui/`.
7. ✅ Always `npm run build` after frontend changes to verify
8. ✅ Always update `.agents/references/` docs when you make significant changes to data models, routes, or component APIs.
