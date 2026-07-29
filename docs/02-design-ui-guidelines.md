# 02 — Design & UI Guidelines

## Official Visual Identity Layer (permanent)

The theme’s permanent site-wide visual system is:

1. **Three.js deep-space background** (`template-parts/background-layer.php` + `assets/js/space-background.js`)
   - WebGL canvas (Three.js r128 from cdnjs), fixed full-viewport, behind all content (`pointer-events: none`).
   - Procedural low-poly asteroids (deformed icosahedrons) + drifting particle field.
   - Brand-tinted lights/particles use `#FFB000` (`0xFFB000`) — never generic amber.
   - Desktop: full detail + mouse-parallax camera. Tablet/mobile: reduced particle/asteroid counts, lower geometry detail, auto-rotation only (no mouse parallax).
   - `prefers-reduced-motion: reduce` → one static frame, no rotation/particles/parallax.
   - Do **not** reinvent page backgrounds with flat CSS starfields, orbit SVG layers, or alternate 3D stacks. Reuse this layer only.

2. **Glass cards** (`.glass-card` / `.ogc-glass-card` in `assets/css/glass.css`)
   - Semi-transparent dark panel + `backdrop-filter` blur + thin accent border.
   - Hover: slight `translateY` lift + soft `--ogc-link-color` glow border/shadow.
   - Mobile uses a lighter blur (8px) for performance; grids stack to 1 column.
   - This is the official card treatment for services, stats, forms, feature panels, etc. Do **not** mix with the old flat solid charcoal card style.

3. **Buttons** — always `.ogc-btn` (outline / transparent default). Never solid gradient-filled glowing CTA buttons from external demos.

## Color Palette
| Token | Value (approx) | Usage |
|---|---|---|
| `--bg-primary` / `--ogc-bg` | #0A0A0A / near-black | Page / WebGL clear color |
| `--bg-panel` / `--ogc-bg-panel` | #141414 | Glass card base mix, panels |
| `--border-accent` | #F7941D (orange) | Dividers / legacy accent borders |
| `--accent-gradient-start` | #FF7A18 | Accent bars / gradient strips |
| `--accent-gradient-end` | #FFC542 | Accent bars / gradient strips |
| `--ogc-text-primary` | #F5F5F5 | Body / non-interactive text |
| `--ogc-text-muted` | #C9C9C9 | Secondary muted copy |
| `--ogc-link-color` | #FFB000 | Links, nav, button border/text, 3D accents |
| `--ogc-link-hover` | #FFCB79 | Link/nav/button hover |
| `--ogc-btn-hover-bg` | #FF8F3154 | Button hover fill tint |
| `--text-on-accent` | #1A1200 / near-black | Text on solid orange badges (if used) |

Interactive text (links, nav, buttons) uses `--ogc-link-color` / `--ogc-link-hover` only. Body paragraphs use `--ogc-text-primary` — never orange.

## Typography
- **Display/Headings (H1–H6, hero titles):** Space Grotesk, bold (700), **not** italic. Token: `--ogc-font-heading`.
- **Body & UI text (nav, buttons, form fields, cards):** Inter. Token: `--ogc-font-body`.
- **Header logo mark:** Bold “OGC” text in Space Grotesk (`ogc_get_logo( context => header )`). Full PNG logo reserved for footer / large placements.
- **Badges/pills/eyebrow labels:** Sans-serif, bold, uppercase, small size; dark text on orange gradient pill when used.

## Layout Patterns (reuse consistently)
1. **Top accent bar** — thin horizontal orange gradient strip at very top of every page.
2. **Bottom accent bar** — matching thin strip at the bottom of the footer.
3. **Split hero layout** — Left: eyebrow pill → large Space Grotesk headline → supporting text. Right: form, glass cards, or decorative graphic.
4. **Feature/info & service cards** — use `.glass-card` (not flat solid panels).
5. **Deep-space WebGL background** — site-wide via the shared background partial (see Official Visual Identity Layer above).
6. **Footer** — translucent over the space background; brand + quick links + contact + legal.

## Components to Build
- Pill badge (eyebrow label)
- Gradient underline swoosh (SVG, under H1s) — optional accent
- **Primary button** — `.ogc-btn` outline system (not gradient fill)
- Info/feature card — `.glass-card`
- Service card — `.glass-card` + real 6-service list from docs/01
- Form fields — dark / glass-compatible, accent border
- Footer bar + header/nav (already implemented)

## Logo
- **Canonical file:** `assets/images/Identity/logo-ogc-newfinity-light.png` — used site-wide (header, footer, favicon source).
- Header: logo image alone at ~40px height (legible orbit-globe + OGC lettering).
- Footer: same file at ~52px height next to the “OGC NewFinity” wordmark.
- Favicons generated from the same mark: `assets/images/Identity/favicon/` (32 / 180 / 512) and registered as WordPress Site Icon.

## Responsiveness
Must be fully responsive (mobile, tablet, desktop). Space background and glass cards both adapt detail/layout per breakpoint (see Official Visual Identity Layer). Split heroes stack vertically on mobile (text first).

## Reference Pages Already Designed (as visual mockups, to be rebuilt as real theme templates)
- Homepage
- Submit Your Idea (idea submission form page)
- Contact (Get in Touch)
- About Us
- Our Mission
- Our Services
- Help & Support
- Blog & Insights
