# 02 — Design & UI Guidelines

## Color Palette
| Token | Value (approx) | Usage |
|---|---|---|
| `--bg-primary` | #0A0A0A / near-black | Page background |
| `--bg-panel` | #141414 | Cards, form fields, panels |
| `--border-accent` | #F7941D (orange) | Card borders, dividers |
| `--accent-gradient-start` | #FF7A18 | Gradient buttons/badges start |
| `--accent-gradient-end` | #FFC542 | Gradient buttons/badges end |
| `--text-primary` | #FFFFFF | Headings, primary text |
| `--text-secondary` | #C9C9C9 | Body copy, placeholders |
| `--text-on-accent` | #1A1200 / near-black | Text on orange buttons/badges |

All orange elements use a **gradient**, not a flat color — top-left lighter orange to bottom-right deeper orange (or left-to-right, consistent per component type).

## Typography
- **Display/Headings (H1, H2 hero titles):** Serif, italic — visually similar to a classic serif display face (e.g., Playfair Display italic or similar). Large size, high contrast white/cream.
- **Body & UI text (nav, buttons, form fields, cards):** Clean sans-serif (e.g., Inter, or similar geometric/humanist sans).
- **Badges/pills/eyebrow labels:** Sans-serif, bold, uppercase, small size, dark text on orange gradient pill.

## Layout Patterns (observed across pages, reuse consistently)
1. **Top accent bar** — thin horizontal orange gradient strip at very top of every page.
2. **Bottom accent bar** — matching thin strip at the very bottom, above footer text.
3. **Split hero layout** — Left column: eyebrow pill badge → large italic serif headline → underline accent swoosh → 1-2 lines of supporting text/tags. Right column: either a form, a set of feature cards, or decorative graphic.
4. **Feature/info cards** — dark panel, thin orange border, rounded corners (~12-16px), icon + label, stacked vertically (3 cards typical) or in a grid (2×3 for services page).
5. **Decorative orbit graphic** — glowing orange concentric rings / atom orbit lines, placed as a large background/side element on inner pages (About, Mission, Support, Blog, Services CTA sections).
6. **Footer bar** — dark background, thin top orange divider, left side = location/contact meta text, right side = "OGC NewFinity" wordmark.

## Components to Build
- Pill badge (eyebrow label)
- Gradient underline swoosh (SVG, under H1s)
- Primary button (gradient fill)
- Secondary/outline button (orange border, transparent fill)
- Info/feature card (icon + title + optional description)
- Service card (icon/illustration + title, grid layout)
- Form fields (text input, textarea, select) — dark bg, orange border, orange placeholder-adjacent icon feel
- Footer bar
- Header/nav bar (not fully visible in refs yet — build clean dark nav with logo left, links right, matching palette)

## Logo
Two logo variants provided: light version and dark version (globe/orbit line-art mark with "OGC" wordmark inside, pink/magenta glow variant and gold/bronze glow variant). Use light-background-safe version where needed; primary usage is the mark on dark background.

## Responsiveness
Must be fully responsive (mobile, tablet, desktop). Split hero layouts stack vertically on mobile (text column first, then form/cards/graphic).

## Reference Pages Already Designed (as visual mockups, to be rebuilt as real theme templates)
- Homepage
- Submit Your Idea (idea submission form page)
- Contact (Get in Touch)
- About Us
- Our Mission
- Our Services
- Help & Support
- Blog & Insights
