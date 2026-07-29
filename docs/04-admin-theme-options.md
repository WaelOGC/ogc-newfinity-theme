# 04 — Admin Theme Options (Dashboard Control Panel)

## Why This Document Exists
On the previous OGC NewFinity build, the theme options panel was unreliable — several settings did not actually apply to the live site. This document defines exactly what must be controllable from wp-admin, without needing Cursor/code changes, and it must actually work end-to-end (setting saved → setting visibly changes the front-end).

## Requirement Summary
The site admin (Wael) must be able to log into WordPress wp-admin and, via **Appearance → Theme Options** (custom admin page) and/or the native **Customizer**, change:

1. **Colors**
   - Primary accent gradient start color
   - Primary accent gradient end color
   - Background color
   - Text color (primary/secondary)
   - Changes must apply site-wide by updating CSS custom properties (CSS variables), not per-page hardcoded values.

2. **Fonts**
   - Heading font (dropdown of pre-approved serif options, or Google Fonts picker)
   - Body font (dropdown of pre-approved sans-serif options, or Google Fonts picker)

3. **Images**
   - Logo (light/dark variants)
   - Hero background images per key page (Home, About, Services, Mission, etc.)
   - Favicon

4. **Effects**
   - Toggle decorative orbit-ring animation on/off
   - Toggle gradient underline swoosh style (or simple on/off if full customization is too complex)

5. **Content-editable fields exposed to admin (no code needed)**
   - Contact email
   - Location text
   - Footer tagline text
   - Social links (if/when added)
   - Response-time guarantee text (currently "48h Response")

## Technical Approach (for Cursor implementation)
- Build a dedicated **Theme Options** admin page using the WordPress Settings API (`add_theme_page`, `register_setting`, `settings_fields`) OR the Customizer API (`customize_register`) — Customizer is preferred where live-preview is useful (colors, fonts), a custom admin page is fine for content fields/images.
- All saved values must be output as **CSS custom properties** in a `<style>` block injected in `wp_head`, so front-end templates reference `var(--accent-start)` etc. rather than hardcoded hex values. This is the key fix versus the old broken implementation.
- Use the WordPress Customizer's built-in image control (`WP_Customize_Image_Control`) for logo/hero image uploads — this guarantees standard, reliable media-library integration.
- Every setting must have a sane default matching the brand identity in `02-design-ui-guidelines.md`, so the site looks correct even before the admin changes anything.
- Test checklist before considering this feature "done": change each setting one at a time, save, hard-refresh front end, confirm the visual change actually appears. No setting should be present in the admin UI if it doesn't actually affect the front end.

## Explicitly Out of Scope for Dashboard (requires Cursor/code)
- Adding/removing entire page sections or page templates
- Changing layout structure (grid arrangement, component order)
- New page types or new theme features
