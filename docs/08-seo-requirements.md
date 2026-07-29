# 08 — SEO Requirements

## Why This Document Exists
The client does not want to face SEO problems again after uploading the theme to the real WordPress host. The theme itself must be built SEO-correct and SEO-friendly from day one at the code level. After upload, the client should only need to fill in simple per-page fields (SEO title, meta description) using a standard SEO plugin — no code editing should ever be required for SEO.

## Theme-Level SEO Requirements (must be correct from the first build, via Cursor)
1. Proper HTML5 semantic structure on every template: one true H1 per page, logical heading order (H1 then H2 then H3, no skipped levels), semantic tags (header, nav, main, footer, article, section) used correctly — already partly in place in the skeleton, must be maintained across all page templates.
2. Clean, valid, crawlable HTML — no broken tags, no content hidden in ways that block indexing.
3. Fast-loading, lightweight code: no unnecessary render-blocking scripts, images must support width/height attributes and lazy loading (loading="lazy") to avoid layout shift and support Core Web Vitals.
4. Descriptive, meaningful alt text placeholders on every image used in templates (logo, icons, decorative graphics where meaningful, hero images) — real descriptive alt text written per image when content is added, not left blank or generic.
5. Clean semantic URLs — matches docs/03 sitemap structure (no duplicate URLs, no messy query-string based page addressing).
6. Mobile-friendly and fully responsive (already required in docs/02) — this is a direct ranking factor.
7. Correct wp_head() placement (already in place) so any SEO plugin can inject its meta tags, Open Graph tags, and schema markup without conflicts.
8. Theme must not hardcode a fixed meta description or title anywhere — always defer to WordPress's own title tag support (already enabled via add_theme_support('title-tag') in the skeleton) and to whatever SEO plugin is installed.
9. Schema.org structured data readiness: theme should not block or duplicate schema markup that a plugin like Yoast/RankMath would add (Organization schema, LocalBusiness schema for the Hague location, breadcrumbs).
10. XML sitemap and robots.txt handling should be left to the SEO plugin (standard practice) — theme should not interfere with these.

## What the Client Will Handle Later (via WordPress only, no code needed)
- Installing a standard SEO plugin (Yoast SEO or RankMath).
- Filling in, per page: SEO title, meta description, focus keyword, Open Graph image.
- Reviewing/approving auto-generated sitemap and robots.txt.
- Setting up Google Search Console / Google Business Profile (outside WordPress).

## What Must Be Delivered Complete From the Start (via Cursor, code-level)
- All real, final page content (per docs/03) written with genuine keyword-relevant language naturally describing OGC NewFinity's actual services — not generic placeholder text — since good real content is itself the foundation of SEO.
- Correct heading hierarchy per page.
- Descriptive alt text on all images at time of building each page.
- Fast, clean, semantic markup across every template.

## Status
This is a cross-cutting requirement that applies to every template built (docs/06) and every page's content (docs/03) — not a separate feature to bolt on at the end. Each page template must be checked against this list as it is built.
