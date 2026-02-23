# Design System

## Theme: Mystic Arcanist

The application uses a custom dark theme called "Mystic Arcanist" - designed to evoke the atmosphere of a D&D session with arcane purple accents, dungeon-depth backgrounds, and magical glow effects.

---

## Color Palette

### Core Backgrounds (Dungeon Depths)

| Name | Hex | Tailwind Class | Usage |
|------|-----|----------------|-------|
| Graphite | `#2A2B2E` | `bg-graphite` | Main page background |
| Graphite 50 | `#3A3B3F` | `bg-graphite-50` | Slightly lighter variant |
| Graphite 900 | `#1A1B1E` | `bg-graphite-900` | Darker variant (sidebar) |
| Gunmetal | `#42434A` | `bg-gunmetal` | Cards, panels, header |
| Gunmetal Light | `#52535A` | `bg-gunmetal-light` | Hover states |
| Gunmetal Dark | `#32333A` | `bg-gunmetal-dark` | Pressed states |
| Charcoal | `#5A5A66` | `bg-charcoal` | Inputs, borders |
| Charcoal Light | `#6A6A76` | `bg-charcoal-light` | Input hover |
| Charcoal Dark | `#4A4A56` | `bg-charcoal-dark` | Input focus |

### Accent Colors (Arcane)

| Name | Hex | Tailwind Class | Usage |
|------|-----|----------------|-------|
| Arcane Purple | `#806EB3` | `text-arcane-purple` | Gradient start, secondary |
| Arcane Periwinkle | `#A682FF` | `text-arcane-periwinkle` | Primary accent, links, focus |
| Arcane Grey | `#C4C1E0` | `text-arcane-grey` | Labels, muted text |
| Arcane Lavender | `#A5A2D4` | `text-arcane-lavender` | Subtle accents |

### Semantic Colors

| Name | Hex | Tailwind Class | Usage |
|------|-----|----------------|-------|
| Nature | `#A4C2A8` | `text-nature` | Success, growth, allies |
| Nature Dark | `#8AAF8E` | `text-nature-dark` | Darker success |
| Nature Light | `#B8D4BC` | `text-nature-light` | Lighter success |
| Legendary Gold | `#D2BB5C` | `text-legendary-gold` | Special items, gold |
| Legendary Amber | `#FFB30F` | `text-legendary-amber` | Highlights, fire |
| Danger | `#EF4444` | `text-danger` | Errors, destructive |
| Danger Dark | `#DC2626` | `text-danger-dark` | Darker danger |
| Danger Light | `#F87171` | `text-danger-light` | Lighter danger |

### Text Colors

| Name | Hex | Tailwind Class | Usage |
|------|-----|----------------|-------|
| White | `#FFFFFF` | `text-white` | Headings, primary text |
| Slate 200 | `#E2E8F0` | `text-slate-200` | Body text |
| Slate 300 | `#CBD5E1` | `text-slate-300` | Secondary text |
| Slate 400 | `#94A3B8` | `text-slate-400` | Muted text |
| Slate 500 | `#64748B` | `text-slate-500` | Placeholder text |

---

## Gradients

### Background Gradients

| Name | Definition | Class | Usage |
|------|------------|-------|-------|
| Arcane Flow | `135deg, #806EB3 → #A682FF` | `bg-arcane-flow` | Primary buttons |
| Arcane Flow Hover | `135deg, #9080C3 → #B692FF` | `bg-arcane-flow-hover` | Button hover |
| Nature Flow | `135deg, #8AAF8E → #A4C2A8` | `bg-nature-flow` | Success elements |
| Legendary Flow | `135deg, #D2BB5C → #FFB30F` | `bg-legendary-flow` | Special items |
| Ethereal Mist | `135deg, #A682FF → #A5A2D4` | `bg-ethereal-mist` | Special cards |

### Border Gradients

| Name | Definition | Usage |
|------|------------|-------|
| Luminous Border | `135deg, #A682FF → #5A5A66` | Active cards |
| Luminous Border Subtle | Same with 50% opacity | Subtle highlights |

---

## Shadows

### Dark Elevation Shadows

| Name | Definition | Class | Usage |
|------|------------|-------|-------|
| Dark SM | `0 1px 2px rgba(0,0,0,0.3)` | `shadow-dark-sm` | Subtle elevation |
| Dark MD | `0 4px 6px rgba(0,0,0,0.4)` | `shadow-dark-md` | Cards |
| Dark LG | `0 10px 15px rgba(0,0,0,0.5)` | `shadow-dark-lg` | Modals |
| Dark XL | `0 20px 25px rgba(0,0,0,0.6)` | `shadow-dark-xl` | Overlays |

### Glow Shadows

| Name | Definition | Class | Usage |
|------|------------|-------|-------|
| Glow Arcane SM | `0 0 10px rgba(166,130,255,0.2)` | `shadow-glow-arcane-sm` | Subtle glow |
| Glow Arcane | `0 0 20px rgba(166,130,255,0.3)` | `shadow-glow-arcane` | Primary glow |
| Glow Arcane LG | `0 0 30px rgba(166,130,255,0.4)` | `shadow-glow-arcane-lg` | Strong glow |
| Glow Nature | `0 0 20px rgba(164,194,168,0.3)` | `shadow-glow-nature` | Success glow |
| Glow Legendary | `0 0 20px rgba(255,179,15,0.3)` | `shadow-glow-legendary` | Gold glow |
| Glow Danger | `0 0 20px rgba(239,68,68,0.3)` | `shadow-glow-danger` | Error glow |

---

## Animations

### Glow Pulse
Pulsing glow effect for active states.

```css
@keyframes glow-pulse {
    0%, 100% { box-shadow: 0 0 20px rgba(166, 130, 255, 0.3); }
    50% { box-shadow: 0 0 30px rgba(166, 130, 255, 0.5); }
}
```

Class: `animate-glow-pulse`

### Shimmer
Shimmer effect for loading states.

```css
@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}
```

Class: `animate-shimmer`

---

## Component Styles

### Cards

```html
<!-- Standard Card -->
<div class="bg-gunmetal shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/10">
    <!-- Content -->
</div>

<!-- Glass Card (for special content) -->
<div class="bg-gunmetal/70 backdrop-blur-sm shadow-dark-md rounded-lg p-6 border border-arcane-periwinkle/20">
    <!-- Content -->
</div>

<!-- Secret/DM Content Card -->
<div class="bg-danger/10 border border-danger/30 shadow-dark-md rounded-lg p-6">
    <!-- Content -->
</div>
```

### Buttons

```html
<!-- Primary Button -->
<button class="px-4 py-2 bg-arcane-periwinkle text-white rounded-md
               hover:bg-arcane-purple transition-colors">
    Save
</button>

<!-- Secondary Button -->
<button class="px-4 py-2 bg-gunmetal border border-charcoal text-arcane-grey rounded-md
               hover:bg-charcoal hover:text-white transition-colors">
    Cancel
</button>

<!-- Danger Button -->
<button class="px-4 py-2 bg-danger text-white rounded-md
               hover:shadow-[0_0_15px_rgba(239,68,68,0.3)] transition-all">
    Delete
</button>
```

### Inputs

```html
<!-- Text Input -->
<input class="w-full bg-charcoal border-charcoal text-white rounded-md
              placeholder-slate-500
              focus:border-arcane-periwinkle focus:ring-arcane-periwinkle" />

<!-- Select -->
<select class="w-full bg-charcoal border-charcoal text-white rounded-md
               focus:border-arcane-periwinkle focus:ring-arcane-periwinkle">
    <option>...</option>
</select>

<!-- Checkbox -->
<input type="checkbox" class="rounded bg-charcoal border-charcoal
                              text-arcane-periwinkle focus:ring-arcane-periwinkle" />
```

### Badges

```html
<!-- Character Subtypes -->
<span class="bg-nature/20 text-nature px-2 py-0.5 rounded text-xs">PC</span>
<span class="bg-arcane-periwinkle/20 text-arcane-periwinkle px-2 py-0.5 rounded text-xs">NPC</span>
<span class="bg-danger/20 text-danger-light px-2 py-0.5 rounded text-xs">Villain</span>
<span class="bg-arcane-purple/20 text-arcane-purple px-2 py-0.5 rounded text-xs">Ally</span>
<span class="bg-charcoal text-arcane-grey px-2 py-0.5 rounded text-xs">Neutral</span>

<!-- Status Badges -->
<span class="bg-nature/20 text-nature ...">Active</span>
<span class="bg-legendary-gold/20 text-legendary-gold ...">Setup</span>
<span class="bg-arcane-grey/20 text-arcane-grey ...">Paused</span>
```

### Links

```html
<!-- Standard Link -->
<a class="text-arcane-periwinkle hover:text-white transition-colors">
    Link text
</a>

<!-- Muted Link -->
<a class="text-arcane-grey hover:text-arcane-periwinkle transition-colors">
    Link text
</a>
```

### Modals

```html
<!-- Modal Backdrop -->
<div class="fixed inset-0 bg-black/70 backdrop-blur-sm"></div>

<!-- Modal Content -->
<div class="relative bg-gunmetal rounded-lg max-w-md w-full p-6
            border border-arcane-periwinkle/20">
    <h3 class="text-lg font-medium text-white mb-4">Title</h3>
    <!-- Content -->
</div>
```

---

## Layout Patterns

### Page Background
```html
<div class="min-h-screen bg-graphite">
```

### Sidebar
```html
<aside class="w-64 bg-graphite-900 border-r border-charcoal/30">
```

### Header
```html
<header class="bg-gunmetal shadow-dark-md border-b border-charcoal/30">
```

### Content Area
```html
<main class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
```

---

## Typography

### Font Family
- Primary: `Figtree` (falls back to system sans-serif)

### Text Sizes

| Usage | Class | Size |
|-------|-------|------|
| Page Title | `text-xl font-semibold` | 1.25rem |
| Section Title | `text-lg font-medium` | 1.125rem |
| Card Title | `text-base font-medium` | 1rem |
| Body Text | `text-sm` | 0.875rem |
| Small/Labels | `text-xs` | 0.75rem |
| Uppercase Label | `text-sm font-medium uppercase` | 0.875rem |

### Text Colors by Context

| Context | Color Class |
|---------|-------------|
| Page heading | `text-white` |
| Section heading | `text-white` |
| Body text | `text-slate-200` or `text-arcane-grey` |
| Muted text | `text-arcane-grey` |
| Labels | `text-arcane-grey` |
| Placeholder | `placeholder-slate-500` |
| Links | `text-arcane-periwinkle` |
| Error text | `text-danger-light` |
| Success text | `text-nature` |

---

## Spacing Scale

Following Tailwind's default spacing scale:

| Class | Size |
|-------|------|
| `p-1` / `m-1` | 0.25rem (4px) |
| `p-2` / `m-2` | 0.5rem (8px) |
| `p-3` / `m-3` | 0.75rem (12px) |
| `p-4` / `m-4` | 1rem (16px) |
| `p-6` / `m-6` | 1.5rem (24px) |
| `p-8` / `m-8` | 2rem (32px) |

Common patterns:
- Card padding: `p-6`
- Section spacing: `space-y-6`
- Grid gaps: `gap-6` or `gap-4`

---

## Responsive Breakpoints

| Breakpoint | Min Width | Usage |
|------------|-----------|-------|
| sm | 640px | Small tablets |
| md | 768px | Tablets |
| lg | 1024px | Small desktops |
| xl | 1280px | Desktops |
| 2xl | 1536px | Large desktops |

Common responsive patterns:
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
```

---

## Configuration

All theme customizations are defined in `tailwind.config.js`. To modify the design system:

1. Edit `tailwind.config.js` colors, shadows, or animations
2. Run `npm run build` to regenerate styles
3. Update this documentation

The configuration extends (not replaces) Tailwind's defaults, so all standard Tailwind utilities remain available.
