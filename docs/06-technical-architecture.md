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
├── home.php                   → Blog posts index (when a static front page is set)
├── page.php                   → Universal Page fallback — hero/layout/visibility/subtitle for any page without a dedicated template
├── page-about-ogc-newfinity.php → About Us
├── page-services.php          → Our Services
├── page-our-mission.php       → Our Mission
├── page-submit-idea.php       → Submit Your Idea (form)
├── page-support.php           → Support slug alias (loads help-support content)
├── page-help-support.php      → Help & Support
├── page-contact.php           → Contact / Get in Touch
├── page-coming-soon.php       → Coming Soon placeholder pages
├── page-legal.php             → Legal pages (Privacy / Terms / etc.)
├── comments.php               → Comment form / list markup
├── single.php                 → Single blog post
├── header.php                 → Site header/nav
├── footer.php                 → Site footer
├── assets/
│   ├── css/
│   │   └── main.css           → compiled/organized styles (or per-component files)
│   ├── js/
│   │   └── main.js            → nav toggle, form handling, front-end interactions
│   ├── images/
│   │   └── (logos, icons, decorative graphics)
│   └── fonts/                 → self-hosted fonts if not using Google Fonts CDN
├── inc/
│   ├── theme-setup.php        → add_theme_support, menus, image sizes
│   ├── enqueue.php            → wp_enqueue_scripts hook logic
│   ├── theme-options.php      → Branded Theme Options admin + getters + CSS vars + page-settings helpers — see doc 04
│   ├── meta-box-page-settings.php → Per-page Hero Background / layout / visibility / subtitle meta box — see doc 04
│   ├── migrate-hero-bg-to-postmeta.php → One-time migration of legacy hero theme_mods → post meta
│   ├── ads.php                → AdSense loader, placement gates, article/listing injection — see doc 04
│   ├── customizer-bridge.php  → Live-preview Customizer (header/footer/colors/buttons/fonts; same theme_mods)
│   ├── blog.php               → Blog listing helpers, card excerpt, filter AJAX
│   ├── widgets.php            → Theme widget area registration
│   ├── contact-form.php       → Contact form handler
│   ├── submit-idea-form.php   → Submit Idea form handler
│   ├── form-helpers.php       → Shared form utilities
│   ├── seed-content.php       → Content seeding entry point
│   ├── seed-articles.php      → Blog article seed data
│   ├── seed-coming-soon.php   → Coming Soon page seed data
│   ├── seed-legal.php         → Legal page seed data
│   └── template-tags.php      → reusable helper functions (card renderers, badge renderer, etc.)
├── template-parts/
│   ├── ad-slot.php            → AdSense / advertiser slot markup — see doc 04
│   ├── page-widget-area.php   → Optional per-page widget sidebar
│   ├── background-layer.php   → Shared decorative background layer
│   ├── blog-sidebar.php       → Blog listing / single sidebar
│   ├── blog-post-card.php     → Blog card markup
│   ├── blog-engage.php        → Post engagement UI
│   ├── icon.php               → Icon sprite / SVG helper partial
│   ├── hero-brand-cube.php    → Home / about hero illustration
│   ├── hero-code-screen.php   → Services hero illustration
│   ├── hero-envelope-send.php → Contact hero illustration
│   ├── hero-idea-blueprint.php → Submit Idea hero illustration
│   ├── hero-mission-compass.php → Mission hero illustration
│   ├── hero-orbit-atom.php    → Shared orbit hero illustration
│   └── hero-support-network.php → Support hero illustration
└── screenshot.png             → theme screenshot for WP admin theme picker
```

## Key Principles
1. **CSS Custom Properties First** — all brand colors/fonts defined as `:root` CSS variables, overridable at runtime by Theme Options output (see doc 04). Never hardcode hex colors inside individual template files.
2. **Reusable template-parts** — hero sections, cards, and CTA bands are repeated across many pages; build them once as `get_template_part()` includes with parameters, not copy-pasted per page.
3. **Classic PHP templates** — one template file per page type, matched via WordPress template hierarchy (`page-{slug}.php`) or assigned via a custom Page Template dropdown in the editor. Pages without a dedicated template use `page.php`, which still honors OGC Page Settings (hero / layout / visibility / subtitle).
4. **No demo/placeholder content shipped** — every template pulls real content either from the WP editor (post content) or from custom fields/theme options — never lorem ipsum left behind.
5. **Progressive build order:**
   1. Skeleton (style.css header + functions.php + index.php) → confirm theme activates in wp-admin.
   2. Header/footer + base CSS variables + typography.
   3. Homepage template.
   4. Remaining page templates one at a time.
   5. Theme Options admin page wiring (doc 04).
   6. Per-page meta box (Hero Background / layout / visibility) — see doc 04. Site-wide AI chat is **not** a theme feature (see doc 09 / superseded doc 05).
   7. Final QA pass against `03-content-sitemap-structure.md` content rules.

## Naming Conventions
- Function prefix: `ogc_` (e.g. `ogc_enqueue_assets()`, `ogc_register_theme_options()`) to avoid collisions with plugins.
- CSS class prefix: `.ogc-` (e.g. `.ogc-hero`, `.ogc-card`, `.ogc-badge`) for anything theme-specific, to avoid conflicts with plugin/Gutenberg default classes.

## Status
This document defines the target structure. Actual folder/file creation happens only when Cursor is instructed to build the skeleton (Step 1 above) — not before, per project plan.
