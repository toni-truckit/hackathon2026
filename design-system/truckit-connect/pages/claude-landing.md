# Claude landing page — design overrides

> **PROJECT:** Truckit Connect  
> **Authority:** This file **overrides** [`MASTER.md`](../MASTER.md) for the home / Claude connector landing.  
> **Visual reference:** [Figma 366:1203](https://www.figma.com/design/4EFpcDj8ROK7m5pCbXcTmv/Truckit-Mobile-Apps?node-id=366-1203)  
> **Stack:** Laravel Livewire + Blade + Tailwind v4 (`resources/css/app.css`)

The ui-ux-pro-max generator suggested a purple/light marketplace palette — **ignore that for this page**. Truckit orange + dark UI come from Figma and existing `--color-primary` in the app.

---

## Brief (unslop-ui — deliberate choices)

| Decision | Choice | Why |
|----------|--------|-----|
| **Reference** | Figma frame above | Not a generic “dark SaaS” template; match shipped design. |
| **Color** | Truckit orange `#f05a28` / `#ff7550` on `#08090a` surfaces | Brand + Figma; not purple/green AI defaults. |
| **Type** | **Inter** (UI), **Geist Mono** or `ui-monospace` (step labels, meta) | Specified in Figma — intentional, not autopilot sans. Mark with `unslop-ignore` on font imports if scanner is extended to Blade. |
| **Layout intent** | Educate → try prompts → trust → setup MCP → FAQ → CTA | Structure follows conversion to “Add Truckit to Claude”, not search-first marketplace pattern from MASTER. |

---

## Color palette (landing only)

Map to Tailwind `@theme` extensions or CSS variables under `.landing` / `layouts/landing` wrapper:

| Role | Hex | Usage |
|------|-----|--------|
| `canvas` | `#08090a` | Page background |
| `canvas-elevated` | `#0a0b0c` | Alternate sections |
| `surface` | `#0f1011` | Cards, prompt list, setup panel |
| `surface-inset` | `#0c0d0e` | Step cards, chat chrome |
| `border-subtle` | `rgba(255,255,255,0.06)` | Section dividers |
| `border-default` | `rgba(255,255,255,0.08–0.1)` | Cards, inputs |
| `text-primary` | `#f7f8f8` | Headings |
| `text-body` | `#e6e7e9` | Chat / emphasis body |
| `text-muted` | `#8a8f98` | Paragraphs |
| `text-faint` | `#62666d` | Footnotes, meta |
| `accent-label` | `#ff7550` | Section labels (“How it works”) |
| `accent-brand` | `#f05a28` | CTA text on white buttons; align with `--color-primary` |
| `accent-glow` | `rgba(247,147,30,0.16)` | Hero radial only — **one** glow, not neon everywhere |

**CTA buttons:** Primary = white fill + orange text; secondary = `bg-white/5` + hairline border.

---

## Typography

| Element | Font | Size / weight | Tracking |
|---------|------|---------------|----------|
| H1 (hero) | Inter Semibold | ~64px / 3 lines | `-2.24px`; gradient white → 62% white per Figma |
| H2 (sections) | Inter Semibold | 44px | `-1.32px` |
| H3 (cards) | Inter Semibold | 16–20px | `-0.4px` |
| Body | Inter Regular | 15–18px | `-0.176px` |
| Label | Inter Semibold | 13px | accent color |
| Step index | Geist Mono / mono | 13px | accent |
| Code (MCP URL) | Geist Mono / mono | 14px | in bordered field |

Preload Inter (+ mono) only on [`layouts/landing.blade.php`](../../../resources/views/layouts/landing.blade.php). Do not change Josefin on other pages.

**Gradient headline:** Figma uses `bg-clip-text` gradient — allowed here with comment `<!-- unslop-ignore: Figma 366:1239 hero title -->`.

---

## Spacing & layout

| Token | Value | Usage |
|-------|-------|--------|
| Content max width | `1168px` | Match Figma container (`max-w-[1168px]`) |
| Section padding Y | `120px` desktop, `64–80px` mobile | Between major blocks |
| Section padding X | `24px` | Horizontal gutter |
| Hero padding top | `112px` below header | |
| Grid gaps | `64px` hero columns; `32px` feature grid | |

**Breakpoints (ui-ux-pro-max checklist):** 375, 768, 1024, 1440 — verify no horizontal scroll.

---

## Components

### Sticky header

- `backdrop-blur` + `bg-[rgba(8,9,10,0.82)]`, bottom border `white/6`
- Height ~64px; logo + anchor nav + white pill CTA

### Buttons

- Radius `8–10px`, semibold 14–15px
- `cursor-pointer`, `transition-colors duration-200`, `focus-visible:ring-2 focus-visible:ring-white/30`
- No `hover:-translate-y-1` on landing (avoid layout shift per MASTER anti-patterns)

### Chat mock (hero)

- Window: gradient border, shadow with single orange ambient (Figma shadow stack)
- Static demo content only — not a live iframe

### Copy row (try asking + MCP URL)

- Row height ~56px; ghost **Copy** button; Alpine clipboard + “Copied” 2s

### FAQ

- `<details>` preferred (a11y); `+` indicator via CSS; borders `white/8`

### Cards (3-step, 4-up features)

- Not the generic “3 feature cards below centered hero” slop pattern — copy and grid match Figma sections with distinct headlines and step numbers.

---

## Section order & anchors

1. `#top` — Hero  
2. `#how-it-works` — Three steps  
3. `#try-asking` — Sample prompts  
4. (no id) — Why Truckit  
5. `#setup` — MCP connector steps  
6. `#questions` — FAQ  
7. — Final CTA band  
8. — Minimal footer (disclaimer)

Nav: How it works · Try asking · Questions · Add Truckit to Claude → `#setup`

---

## Effects

| Allowed | Avoid |
|---------|--------|
| One hero radial gradient | Extra `shadow-[0_0_…]` neon on every card |
| `backdrop-blur` on header | Pulsing badges, count-up stats (not in Figma) |
| `motion-reduce:transition-none` | Decorative animation on scroll |

---

## Icons

- SVG from Figma exports / Lucide — **no emoji icons**
- “Why Truckit” row icons: 20×20, orange stroke

---

## ui-ux-pro-max pre-delivery (this page)

- [ ] SVG icons only  
- [ ] `cursor-pointer` on links and buttons  
- [ ] Hover transitions 150–300ms  
- [ ] Contrast: muted text `#8a8f98` on `#08090a` ≥ 4.5:1 (verify quote card)  
- [ ] `focus-visible` rings on interactive elements  
- [ ] `prefers-reduced-motion`  
- [ ] Responsive at 375 / 768 / 1024 / 1440  
- [ ] `scroll-mt-*` so anchors clear sticky header  

---

## unslop-ui gate (run before merge)

```bash
# CSS + any new landing CSS file (scanner does not include .blade.php yet)
python3 .ai/skills/unslop-ui/scripts/devibe_scan.py resources/css

# After landing Blade exists, eyeball: spacing, overflow, generic hero+3-card sameness
```

**Current baseline scan** (`resources/views`): 0 mechanical tells — existing light UI uses Josefin, not flagged. **Expect flags** when implementing if purple/indigo or shadcn card classes slip in — fix with Truckit tokens, not a new default palette.

**Intentional tells (Figma):** Inter, gradient hero title, dark + orange glow — document with `unslop-ignore` only where values are copied from Figma, not invented.

---

## Implementation mapping

| Design system | Code location |
|---------------|----------------|
| Tokens | `resources/css/app.css` `@theme` or `.landing` variables |
| Layout | `resources/views/layouts/landing.blade.php` |
| Sections | `resources/views/components/landing/*.blade.php` |
| Config copy / MCP URL | `config/truckit-connect.php` |
