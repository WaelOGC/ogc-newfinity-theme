# 02 — Design & UI Guidelines

## Official Visual Identity Layer (permanent)

The theme’s permanent site-wide visual system is:

1. **Three.js deep-space background** (`template-parts/background-layer.php` + `assets/js/space-background.js`)
   - WebGL canvas (Three.js r128 from cdnjs), fixed full-viewport, behind all content (`pointer-events: none`).
   - Procedural low-poly asteroids (deformed icosahedrons) + drifting particle field.
   - Brand-tinted lights/particles for the WebGL scene use `#FFB000` (`0xFFB000`) — scene/cube lighting is intentionally separate from UI chrome tokens.
   - Desktop: full detail + mouse-parallax camera. Tablet/mobile: reduced particle/asteroid counts, lower geometry detail, auto-rotation only (no mouse parallax).
   - `prefers-reduced-motion: reduce` → one static frame, no rotation/particles/parallax.
   - Do **not** reinvent page backgrounds with flat CSS starfields, orbit SVG layers, or alternate 3D stacks. Reuse this layer only.

2. **Glass cards** (`.glass-card` / `.ogc-glass-card` in `assets/css/glass.css`)
   - Semi-transparent dark panel + `backdrop-filter` blur + thin accent border.
   - Hover: slight `translateY` lift + soft `--ogc-link-color` glow border/shadow.
   - Mobile uses a lighter blur (8px) for performance; grids stack to 1 column.
   - This is the official card treatment for services, stats, forms, feature panels, etc. Do **not** mix with the old flat solid charcoal card style.

3. **Buttons** — primary `.ogc-btn` (embossed dark chip, brand border, white label) and secondary `.ogc-btn-outline`. Hover/focus/active/disabled treatments are configured in Theme Options → Buttons & Effects (`assets/css/button-effects.css`). Never solid gradient-filled glowing CTA buttons from external demos.
4. **Icons** — always `.ogc-icon` chips via `ogc_icon()` / `template-parts/icon.php` (embossed container + outline SVG).

## Color Palette
| Token | Value (approx) | Usage |
|---|---|---|
| `--bg-primary` / `--ogc-bg` | #0A0A0A / near-black | Page / WebGL clear color |
| `--bg-panel` / `--ogc-bg-panel` | #141414 | Glass card base mix, panels |
| `--border-accent` | #FFD27A | Dividers / accent borders (overridable via Theme Options) |
| `--accent-gradient-start` | #FFD27A | Accent bars / gradient strips / pills |
| `--accent-gradient-end` | #FFEAC2 | Accent bars / gradient strips / pills |
| `--ogc-text-primary` | #F5F5F5 | Body / non-interactive text |
| `--ogc-text-muted` | #C9C9C9 | Secondary muted copy |
| `--ogc-link-color` | #FFD27A | Links, nav, icon stroke, borders |
| `--ogc-link-hover` | #FFEAC2 | Link/nav/icon hover |
| `--ogc-btn-hover-bg` | rgba(255,210,122,0.16) | Soft brand hover tint |
| `--ogc-brand-rgb` | 255, 210, 122 | For `rgba(var(--ogc-brand-rgb), a)` borders/glows |
| `--text-on-accent` | #1A1200 / near-black | Text on solid brand badges |

All of the above (plus header/footer/button tokens) are overridden at runtime by `ogc_theme_options_css()` from **wp-admin → OGC NewFinity**. See `docs/04-admin-theme-options.md`.

Interactive text (links, nav) uses `--ogc-link-color` / `--ogc-link-hover`. Button **labels** are white on primary; outline buttons use link color. Body paragraphs use `--ogc-text-primary` — never brand yellow.

## Typography
- **Display/Headings (H1–H6, hero titles):** Space Grotesk, bold (700), **not** italic. Token: `--ogc-font-heading`.
- **Body & UI text (nav, buttons, form fields, cards):** Inter. Token: `--ogc-font-body`.
- **Header / footer logos:** PNG mark via `ogc_get_logo()`; widths and optional uploads controlled in Theme Options (Header / Footer tabs).
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
- Gradient underline / H1 accent (`.hero-shimmer` + Theme Options “H1 Underline Style”)
- **Primary button** — `.ogc-btn` embossed chip (white label, brand border)
- **Outline button** — `.ogc-btn-outline` secondary CTA
- Info/feature card — `.glass-card`
- Service card — `.glass-card` + real 6-service list from docs/01
- Form fields — dark / glass-compatible, accent border
- Footer bar + header/nav (already implemented; editable via Theme Options)

## Logo
- **Canonical file:** `assets/images/Identity/logo-ogc-newfinity-light.png` — default site-wide mark.
- Header / footer logos are overridable in Theme Options (separate uploads + width controls). Defaults: header ~40px height, footer ~54px width next to the “OGC NewFinity” wordmark.
- Favicons: WP Site Icon (Customizer → Site Identity) with bundled files in `assets/images/Identity/favicon/` as fallback.

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
