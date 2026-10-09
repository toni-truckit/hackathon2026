# Design System Master File

> **LOGIC:** When building a page, check `design-system/truckit-connect/pages/[page-name].md` first.
> Page rules **override** this Master.  
> **Repo brand:** `resources/css/app.css` `--color-primary` (`#fc6a3e`) is the Truckit orange for the wider site.

---

**Project:** Truckit Connect  
**Generated:** 2026-10-09 (ui-ux-pro-max) + manual Truckit alignment  
**Category:** Service / marketplace marketing + Filament admin (light)

---

## Global Rules (site-wide, non-landing)

### Color Palette

| Role | Hex | CSS Variable |
|------|-----|--------------|
| Primary | `#fc6a3e` | `--color-primary` (existing app theme) |
| Primary scale | `#fa4115` … `#430707` | `--color-primary-*` in `app.css` |
| Background (app layout) | `neutral-100` | `layouts/app` body |
| Surfaces | white / `white/70` + blur | header, footer, content cards |

**Deprecated for Connect marketing:** Generated purple/green palette below — do **not** use unless a future page override says so.

| Role (generated — ignore) | Hex |
|---------------------------|-----|
| ~~Primary~~ | ~~`#7C3AED`~~ |
| ~~CTA~~ | ~~`#22C55E`~~ |

**Claude landing:** See [`pages/claude-landing.md`](pages/claude-landing.md) for dark theme tokens.

### Typography

- **Heading Font:** Lexend
- **Body Font:** Source Sans 3
- **Mood:** corporate, trustworthy, accessible, readable, professional, clean
- **Google Fonts:** [Lexend + Source Sans 3](https://fonts.google.com/share?selection.family=Lexend:wght@300;400;500;600;700|Source+Sans+3:wght@300;400;500;600;700)

**CSS Import:**
```css
@import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap');
```

### Spacing Variables

| Token | Value | Usage |
|-------|-------|-------|
| `--space-xs` | `4px` / `0.25rem` | Tight gaps |
| `--space-sm` | `8px` / `0.5rem` | Icon gaps, inline spacing |
| `--space-md` | `16px` / `1rem` | Standard padding |
| `--space-lg` | `24px` / `1.5rem` | Section padding |
| `--space-xl` | `32px` / `2rem` | Large gaps |
| `--space-2xl` | `48px` / `3rem` | Section margins |
| `--space-3xl` | `64px` / `4rem` | Hero padding |

### Shadow Depths

| Level | Value | Usage |
|-------|-------|-------|
| `--shadow-sm` | `0 1px 2px rgba(0,0,0,0.05)` | Subtle lift |
| `--shadow-md` | `0 4px 6px rgba(0,0,0,0.1)` | Cards, buttons |
| `--shadow-lg` | `0 10px 15px rgba(0,0,0,0.1)` | Modals, dropdowns |
| `--shadow-xl` | `0 20px 25px rgba(0,0,0,0.15)` | Hero images, featured cards |

---

## Component Specs

### Buttons

```css
/* Primary Button */
.btn-primary {
  background: #22C55E;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 200ms ease;
  cursor: pointer;
}

.btn-primary:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

/* Secondary Button */
.btn-secondary {
  background: transparent;
  color: #7C3AED;
  border: 2px solid #7C3AED;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 600;
  transition: all 200ms ease;
  cursor: pointer;
}
```

### Cards

```css
.card {
  background: #FAF5FF;
  border-radius: 12px;
  padding: 24px;
  box-shadow: var(--shadow-md);
  transition: all 200ms ease;
  cursor: pointer;
}

.card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}
```

### Inputs

```css
.input {
  padding: 12px 16px;
  border: 1px solid #E2E8F0;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 200ms ease;
}

.input:focus {
  border-color: #7C3AED;
  outline: none;
  box-shadow: 0 0 0 3px #7C3AED20;
}
```

### Modals

```css
.modal-overlay {
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: var(--shadow-xl);
  max-width: 500px;
  width: 90%;
}
```

---

## Style Guidelines

**Style:** Social Proof-Focused

**Keywords:** Testimonials prominent, client logos displayed, case studies sections, reviews/ratings, user avatars, success metrics, credibility markers

**Best For:** B2B SaaS, professional services, premium products, e-commerce conversion pages, established brands

**Key Effects:** Testimonial carousel animations, logo grid fade-in, stat counter animations (number count-up), review star ratings

### Page Pattern

**Pattern Name:** Marketplace / Directory

- **Conversion Strategy:**  map hover pins,  card carousel, Search bar is the CTA. Reduce friction to search. Popular searches suggestions.
- **CTA Placement:** Hero Search Bar + Navbar 'List your item'
- **Section Order:** 1. Hero (Search focused), 2. Categories, 3. Featured Listings, 4. Trust/Safety, 5. CTA (Become a host/seller)

---

## Anti-Patterns (Do NOT Use)

- ❌ Complex navigation
- ❌ Hidden contact info

### Additional Forbidden Patterns

- ❌ **Emojis as icons** — Use SVG icons (Heroicons, Lucide, Simple Icons)
- ❌ **Missing cursor:pointer** — All clickable elements must have cursor:pointer
- ❌ **Layout-shifting hovers** — Avoid scale transforms that shift layout
- ❌ **Low contrast text** — Maintain 4.5:1 minimum contrast ratio
- ❌ **Instant state changes** — Always use transitions (150-300ms)
- ❌ **Invisible focus states** — Focus states must be visible for a11y

---

## Pre-Delivery Checklist

Before delivering any UI code, verify:

- [ ] No emojis used as icons (use SVG instead)
- [ ] All icons from consistent icon set (Heroicons/Lucide)
- [ ] `cursor-pointer` on all clickable elements
- [ ] Hover states with smooth transitions (150-300ms)
- [ ] Light mode: text contrast 4.5:1 minimum
- [ ] Focus states visible for keyboard navigation
- [ ] `prefers-reduced-motion` respected
- [ ] Responsive: 375px, 768px, 1024px, 1440px
- [ ] No content hidden behind fixed navbars
- [ ] No horizontal scroll on mobile
