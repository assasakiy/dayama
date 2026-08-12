# Design System Reference

Full token list and component API for the test-blog dashboard.

## CSS Design Tokens (`resources/css/app.css`)

### Light Mode
```css
/* Neutral palette */
--color-background:        oklch(1 0 0);           /* pure white */
--color-surface:           oklch(0.985 0.005 155);  /* near-white */
--color-surface-muted:     oklch(0.965 0.008 155);  /* light grey */
--color-border-subtle:     oklch(0.92 0 0);         /* subtle border */
--color-border-strong:     oklch(0.85 0 0);         /* strong border */

/* Text */
--color-foreground:        oklch(0.18 0 0);         /* near-black */
--color-muted-foreground:  oklch(0.48 0 0);         /* grey */
--color-subtle-foreground: oklch(0.56 0 0);         /* lighter grey */

/* Primary — Islamic Green */
--color-primary:           oklch(0.52 0.17 155);
--color-primary-foreground: oklch(0.99 0 0);
--color-primary-muted:     oklch(0.94 0.04 155);
--color-primary-border:    oklch(0.82 0.08 155);

/* Secondary — Emerald */
--color-secondary:         oklch(0.60 0.15 165);
--color-secondary-foreground: oklch(0.99 0 0);

/* Accent — Gold */
--color-accent:            oklch(0.76 0.15 80);
--color-accent-foreground: oklch(0.25 0.03 80);

/* Status colors */
--color-success:           oklch(0.62 0.16 145);
--color-warning:           oklch(0.7 0.16 75);
--color-danger:            oklch(0.62 0.22 25);
--color-destructive:       oklch(0.62 0.22 25);
--color-info:              oklch(0.62 0.13 240);

/* Sizing */
--radius-sm: 0.25rem; --radius-md: 0.5rem; --radius-lg: 0.75rem;
--shadow-elevated: 0 1px 2px oklch(0 0 0 / 0.04), 0 4px 12px oklch(0 0 0 / 0.04);
--shadow-pop: 0 4px 18px oklch(0 0 0 / 0.08);
```

### Dark Mode (`.dark` class)
```css
--color-background:     oklch(0.16 0.01 155);
--color-surface:        oklch(0.2 0.01 155);
--color-surface-muted:  oklch(0.24 0.01 155);
--color-border-subtle:  oklch(0.3 0 0);
--color-foreground:     oklch(0.96 0 0);
--color-primary:        oklch(0.66 0.16 155);
```

---

## Component API Reference

> **CRITICAL RULE**: ALWAYS use these predefined components. DO NOT build custom buttons, cards, or dialogs from raw HTML. Using these components guarantees design consistency across the dashboard.

### `Btn` — `@dashboard/Components/ui/btn`
Unified button for all dashboard pages.

```tsx
interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    loading?: boolean;         // shows spinner, disables button
    icon?: React.ReactNode;    // rendered before children in a fixed-width slot
    variant?: 'primary' | 'secondary' | 'danger' | 'ghost' | 'outline';
    size?: 'sm' | 'md' | 'lg';
    children?: React.ReactNode;
}
```

| Variant | Classes |
|---------|---------|
| `primary` | `bg-primary text-primary-foreground hover:bg-primary/90 border border-transparent shadow-sm` |
| `secondary` | `bg-surface-muted text-foreground hover:bg-border/50 border border-border-subtle` |
| `danger` | `bg-destructive/10 text-destructive hover:bg-destructive/20 border border-destructive/20` |
| `ghost` | `text-muted-foreground hover:text-foreground hover:bg-surface-muted border border-transparent` |
| `outline` | `bg-background text-foreground hover:bg-surface-muted border border-border-subtle shadow-sm` |

| Size | Classes |
|------|---------|
| `sm` | `px-3 py-1.5 text-xs gap-1.5` |
| `md` | `px-4 py-2 text-sm gap-2` |
| `lg` | `px-5 py-2.5 text-sm gap-2` |

---

### `Badge` — `@dashboard/Components/ui/badge`
Uses `class-variance-authority`. Variants: `default`, `secondary`, `destructive`, `outline`.
Typically overridden with inline className for status colors:
```tsx
<Badge className="bg-emerald-500/10 text-emerald-600 border-emerald-500/20">
    <Eye className="w-3 h-3 mr-1" /> Aktif
</Badge>
```

---

### `Card` — `@dashboard/Components/ui/card`
**Always use Card or standard standard utility classes (`bg-background border border-border-subtle rounded-md`) for layout blocks.**
```tsx
<Card>                  → bg-background border border-border-subtle rounded-md
  <CardHeader>          → p-4 border-b border-border-subtle
    <CardTitle>         → text-sm font-semibold
    <CardDescription>  → text-sm text-muted-foreground
  </CardHeader>
  <CardContent>         → p-4
</Card>
```

---

### `Input` — `@dashboard/Components/ui/input`
```tsx
<Input label="Nama" error={errors.name} value={...} onChange={...} />
```
- Built-in `label` and `error` props
- Base: `h-9 rounded-sm border-border-subtle bg-background focus:ring-primary`

---

### `Textarea` — `@dashboard/Components/ui/textarea`
Same API as Input. `min-h-[80px]`, `rounded-sm`, `border-border-subtle`.

---

### `Select` — `@dashboard/Components/ui/select`
Radix-based. Usage:
```tsx
<Select value={v} onValueChange={setV}>
    <SelectTrigger><SelectValue placeholder="Pilih..." /></SelectTrigger>
    <SelectContent>
        <SelectItem value="a">Option A</SelectItem>
    </SelectContent>
</Select>
```

---

### `Dialog` — `@dashboard/Components/ui/dialog`
Radix-based. **Use this for ALL popups and modals.**
Standard structure:
```tsx
<Dialog open={open} onOpenChange={setOpen}>
    <DialogContent>
        <DialogHeader>
            <DialogTitle>Title</DialogTitle>
        </DialogHeader>
        {/* Body content */}
        <div className="px-4 sm:px-6 py-4">...</div>
        <DialogFooter>
            <Btn variant="outline">Batal</Btn>
            <Btn>Simpan</Btn>
        </DialogFooter>
    </DialogContent>
</Dialog>
```

---

### `ConfirmDialog` — `@dashboard/Components/ui/confirm-dialog`
```tsx
<ConfirmDialog
    open={!!deleteTarget}
    onOpenChange={() => setDeleteTarget(null)}
    title="Hapus Item"
    message="Apakah Anda yakin?"
    confirmLabel="Hapus"
    variant="danger"
    onConfirm={handleDelete}
/>
```

---

### `Tabs` — `@dashboard/Components/ui/tabs`
Radix-based:
```tsx
<Tabs defaultValue="tab1">
    <TabsList><TabsTrigger value="tab1">Tab 1</TabsTrigger></TabsList>
    <TabsContent value="tab1">...</TabsContent>
</Tabs>
```

---

## Shared Components

### `MediaPicker` — `@dashboard/Components/MediaPicker`
Modal media picker with upload support. Usage:
```tsx
<MediaPicker
    open={!!mediaPicker}
    onClose={() => setMediaPicker(null)}
    onSelect={(media) => { /* use media.url */ }}
/>
```

### `TipTapEditor` — `@dashboard/Components/TipTapEditor`
Rich text editor with toolbar.

---

### `IconPicker` — `@dashboard/Components/ui/icon-picker`
Modal picker with 2 tabs: "Library" (preset Lucide icons) and "Upload" (custom SVG/PNG upload).
Outputs string format: `lucide:IconName` or `url:/storage/...`.

```tsx
<IconPicker
    label="Ikon Fasilitas"
    value={facility.icon}
    onChange={val => updateFacility('icon', val)}
/>
```

---

## Page Patterns

### Index Page (List View)
```
DashboardLayout > Head + space-y-5
├── Header row (flex justify-between)
│   ├── Title + description
│   └── Primary action button
├── Table or Card grid
│   ├── Table: bg-background border rounded-lg overflow-hidden
│   └── Cards: grid gap-4 md:grid-cols-2 lg:grid-cols-3
└── Pagination (if needed)
```

### Edit Page
```
DashboardLayout > Head + space-y-5
├── Header row
│   ├── Back button (Btn ghost + ArrowLeft icon)
│   ├── Title + subtitle
│   └── Save button (Btn primary + Save icon)
├── Form sections (Card or div with border)
└── Sticky save bar (if needed)
```

### Modal CRUD (Ctas, Faqs)
```
Index page with inline Dialog
├── Header + "Tambah" button opens dialog
├── List of items (table or cards)
├── Dialog with form fields
│   ├── DialogHeader > DialogTitle
│   ├── Form body (px-6 py-4)
│   └── DialogFooter > Batal + Simpan
└── ConfirmDialog for delete
```

---

## Sidebar Navigation Structure
```
Dashboard
├── Dashboard (/)
Media
├── Media (/media)
Post Management
├── All Posts, Drafts, Published, Trash
├── Categories, Tags, Comments
My Content
├── Bookmarks, Reading History
Activity
├── Activity Logs
Landing Management          ← Dynamic: Pages replaced with individual page links
├── [Home] (/landing/pages/1/edit)
├── [Profil] (/landing/pages/2/edit)
├── [etc...]
├── FAQ (/landing/faqs)
├── CTA (/landing/ctas)
Institusi Management        ← Module terpisah (bukan bagian Landing)
├── Lembaga (/institutions)
├── Guru / Ustadz (/institutions/teachers)    🔜
├── Siswa (/institutions/students)            🔜
├── Kelas (/institutions/classes)             🔜
Management Settings
├── Global, Blog, Landing Settings
Mail Management
├── Mail (SMTP), Email Templates
Access Management
├── Users, Roles, Permissions, Permission Groups
```
