# 06 — Technical Architecture (Theme File Structure)

## Base Approach
Custom WordPress theme, built from scratch (no parent/starter theme dependency), classic PHP template structure (not full-site-editing/block-theme, unless decided otherwise later). Plain, clean, maintainable code — this is important since the client will hand-edit small things over time via Cursor.

## Planned Folder Structure

```
ogc-newfinity/
├── style.css                  → theme header (required by WP) + can hold minimal fallback CSS
├── functions.php              → theme setup, enqueue scripts/styles, register menus/widgets, includes loader
├── index.php                  → fallback template
├── front-page.php             → Home page template
├── page-about.php             → About Us
├── page-services.php          → Our Services
├── page-mission.php           → Our Mission
├── page-submit-idea.php       → Submit Your Idea (form)
├── page-support.php           → Help & Support
├── page-contact.php           → Contact / Get in Touch
├── archive.php / index.php    → Blog listing (Blog & Insights)
├── single.php                 → Single blog post
├── header.php                 → Site header/nav
├── footer.php                 → Site footer
├── assets/
│   ├── css/
│   │   └── main.css           → compiled/organized styles (or per-component files)
│   ├── js/
│   │   └── main.js            → nav toggle, form handling, AI chat widget logic
│   ├── images/
│   │   └── (logos, icons, decorative graphics)
│   └── fonts/                 → self-hosted fonts if not using Google Fonts CDN
├── inc/
│   ├── theme-setup.php        → add_theme_support, menus, image sizes
│   ├── enqueue.php            → wp_enqueue_scripts hook logic
│   ├── theme-options.php      → Branded Theme Options admin + getters + CSS vars — see doc 04
│   ├── customizer-bridge.php  → Live-preview Customizer (hero/header/footer/colors/buttons/fonts; same theme_mods)
│   ├── ai-chat-widget.php     → Gemini API REST endpoint + widget render — see doc 05
│   └── template-tags.php      → reusable helper functions (card renderers, badge renderer, etc.)
├── template-parts/
│   ├── hero-split.php         → reusable split-hero layout (badge + headline + underline + right column)
│   ├── card-feature.php       → reusable feature/info card
│   ├── card-service.php       → reusable service card
│   ├── ad-slot.php            → Ads Manager slots (sidebar / inline) — see doc 04
│   └── cta-band.php           → reusable CTA section
└── screenshot.png             → theme screenshot for WP admin theme picker
```

## Key Principles
1. **CSS Custom Properties First** — all brand colors/fonts defined as `:root` CSS variables, overridable at runtime by Theme Options output (see doc 04). Never hardcode hex colors inside individual template files.
2. **Reusable template-parts** — hero sections, cards, and CTA bands are repeated across many pages; build them once as `get_template_part()` includes with parameters, not copy-pasted per page.
3. **Classic PHP templates** — one template file per page type, matched via WordPress template hierarchy (`page-{slug}.php`) or assigned via a custom Page Template dropdown in the editor.
4. **No demo/placeholder content shipped** — every template pulls real content either from the WP editor (post content) or from custom fields/theme options — never lorem ipsum left behind.
5. **Progressive build order:**
   1. Skeleton (style.css header + functions.php + index.php) → confirm theme activates in wp-admin.
   2. Header/footer + base CSS variables + typography.
   3. Homepage template.
   4. Remaining page templates one at a time.
   5. Theme Options admin page wiring (doc 04).
   6. AI chat widget (doc 05).
   7. Final QA pass against `03-content-sitemap-structure.md` content rules.

## Naming Conventions
- Function prefix: `ogc_` (e.g. `ogc_enqueue_assets()`, `ogc_register_theme_options()`) to avoid collisions with plugins.
- CSS class prefix: `.ogc-` (e.g. `.ogc-hero`, `.ogc-card`, `.ogc-badge`) for anything theme-specific, to avoid conflicts with plugin/Gutenberg default classes.

## Status
This document defines the target structure. Actual folder/file creation happens only when Cursor is instructed to build the skeleton (Step 1 above) — not before, per project plan.
