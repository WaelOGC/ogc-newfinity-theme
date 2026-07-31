# 04 — Admin Theme Options (Dashboard Control Panel)

## Why This Document Exists
The admin must change brand, layout chrome, content strings, ads, and tracking from wp-admin **without code changes**, and every saved setting must visibly apply on the front end. This document describes the **shipped** Theme Options system (custom branded admin page), not the old native Customizer panel.

## Where To Find It
**wp-admin → OGC NewFinity** (top-level menu). Capability: `edit_theme_options`.

Not under Settings → General. Native Customizer **Site Identity** (logo + Site Icon/favicon) remains available alongside Theme Options header/footer logo overrides.

## Architecture
| Layer | Implementation |
|---|---|
| UI | Custom `add_menu_page` screen — dark/orange branded, left sidebar tabs, one form, sticky Save |
| Storage | `get_theme_mod()` / `set_theme_mod()` for every field |
| Schema | `ogc_theme_options_schema()` — single source for render + sanitize + save + import/export/reset |
| Front CSS | `ogc_theme_options_css()` prints `:root` custom properties in `wp_head` |
| Assets | `assets/css/admin-theme-options.css`, `assets/js/admin-theme-options.js` (admin screen only) |
| Code | `inc/theme-options.php` |

Save posts to `admin-post.php?action=ogc_save_theme_options` (nonce-protected). Import / Export / Reset This Tab / Reset All are separate `admin-post` actions reusing the same schema sanitizers.

## Tab Map (11 sections)

1. **General** — site-wide contact/location/response copy, analytics, maintenance  
2. **Header** — logo, sizes, bg, sticky, nav order/visibility, nav text color  
3. **Footer** — logo, sizes, bg, tagline, columns JSON, copyright, social URLs  
4. **Colors** — base + component tokens + presets (JS fill, save to persist)  
5. **Fonts** — allow-listed Google Fonts, sizes, weights, line-height, letter-spacing  
6. **Buttons & Effects** — radius, shadow, per-variant hover (primary vs outline), a11y states, orbit + H1 underline  
7. **Images** — light logo + optional hero backgrounds (Home/About/Services/Mission)  
8. **Pages** — hero background opacity per key page  
9. **Posts** — blog card image/date/category toggles + excerpt length  
10. **Ads Manager** — master enable + per-slot config JSON (`sidebar`, `inline`)  
11. **Advanced** — custom CSS/JS, import/export, reset  

Social links live under **Footer** (not a separate tab).

---

## Field Reference

Types: `color` | `text` | `email` | `url` | `textarea` | `monospace` | `number` | `range` | `checkbox` | `select` | `image` | `nav_order` | `footer_columns` | `ads_config`

Sanitize: every field has a callback (hex, email, `esc_url_raw`, text/textarea, whitelist select, image URL, JSON structure validators).

### General
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_contact_email` | email | `contact@ogcnewfinity.com` | `ogc_get_contact_email()` → footer, support, contact forms `wp_mail`, brand-cube copy |
| `ogc_location_text` | text | `The Hague, Netherlands` | `ogc_get_location_text()` → footer, contact, about, submit-idea, etc. |
| `ogc_response_time_text` | text | `48h Response` | `ogc_get_response_time_text()` → home pillars, support hero |
| `ogc_google_analytics_id` | text | `''` | `wp_head` GA4 snippet only if non-empty |
| `ogc_meta_pixel_id` | text | `''` | `wp_head` Meta Pixel only if non-empty |
| `ogc_maintenance_mode_enabled` | checkbox | off | `template_redirect` — logged-out visitors see maintenance page; admins bypass |
| `ogc_maintenance_mode_message` | textarea | `We'll be back shortly.` | Maintenance page body |

### Header
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_header_logo` | image | `''` (fallback: custom logo → bundled mark) | `header.php` via `ogc_get_header_logo_url()` |
| `ogc_header_logo_width_desktop` | number (px) | `40` | CSS `--ogc-header-logo-width-desktop` |
| `ogc_header_logo_width_mobile` | number (px) | `40` | CSS `--ogc-header-logo-width-mobile` |
| `ogc_header_bg_color` | color | `#0A0A0A` | CSS header background token |
| `ogc_header_bg_opacity` | range 0–100 | `0` (transparent at top) | CSS opacity for header bg |
| `ogc_header_sticky` | checkbox | on | Body class `ogc-header-not-sticky` when off |
| `ogc_header_nav_order` | nav_order JSON | all 7 items visible in default order | `ogc_render_primary_nav()` / `header.php` |
| `ogc_header_text_color` | color | `#FFD27A` | Nav link color CSS var |

Nav slugs (fixed set — reorder/show/hide only): `home`, `about`, `services`, `mission`, `blog`, `support`, `contact`.

### Footer
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_footer_logo` | image | `''` | `footer.php` via `ogc_get_footer_logo_url()` |
| `ogc_footer_logo_width` | number (px) | `54` | CSS / img width |
| `ogc_footer_bg_color` | color | `#0A0A0A` | Footer bg token |
| `ogc_footer_tagline` | textarea | agency paragraph (see code constant) | `ogc_get_footer_tagline()` |
| `ogc_footer_columns_json` | footer_columns | Quick Links + Legal (current links) | `ogc_get_footer_columns()` → `footer.php` |
| `ogc_footer_copyright_text` | text | `© {start_year}–{year} OGC NewFinity. All rights reserved.` | `ogc_get_footer_copyright()` (`{year}` → current year, `{start_year}` → 2022) |
| `ogc_social_x` | url | `https://x.com/OGCNewfinity` | `ogc_get_social_url( 'x' )` — empty hides icon |
| `ogc_social_linkedin` | url | company LinkedIn URL | ditto |
| `ogc_social_github` | url | org GitHub URL | ditto |
| `ogc_social_discord` | url | invite URL | ditto |

Footer layout: **Brand** (fixed) → JSON columns (default Quick Links, then after Contact insert pattern) → **Contact** (location + email getters) → remaining JSON columns (Legal). Brand + Contact columns are not fully free-form JSON so location/email stay tied to General fields.

### Colors
| Key | Type | Default | CSS / usage |
|---|---|---|---|
| `ogc_color_accent_start` | color | `#FFD27A` | `--accent-gradient-start` |
| `ogc_color_accent_end` | color | `#FFEAC2` | `--accent-gradient-end` |
| `ogc_color_bg` | color | `#0A0A0A` | `--bg-primary` |
| `ogc_color_text_primary` | color | `#F5F5F5` | `--text-primary` / `--ogc-text-primary` |
| `ogc_color_text_secondary` | color | `#C9C9C9` | `--text-secondary` |
| `ogc_color_button_bg_start` | color | `#FFD27A` | Button gradient token |
| `ogc_color_button_bg_end` | color | `#FFEAC2` | Button gradient token |
| `ogc_color_button_text` | color | `#FFFFFF` | Button text token |
| `ogc_color_border` | color | `#FFD27A` | Card/input border token |
| `ogc_gradient_angle` | number (°) | `145` | `--ogc-gradient-angle` |
| `ogc_color_shadow` | color | `#000000` | Shadow color token |
| `ogc_shadow_opacity` | range | `40` | Shadow opacity |

**Presets** (admin JS only until Save): OGC Default, Slate Amber, Deep Ember.

### Fonts
Allow-listed Google Fonts only (expanded curated lists in `ogc_heading_font_choices()` / `ogc_body_font_choices()`). URL builder requests the weights selected below.

| Key | Type | Default | CSS |
|---|---|---|---|
| `ogc_font_heading` | select | `Space Grotesk` | `--font-heading` |
| `ogc_font_body` | select | `Inter` | `--font-body` |
| `ogc_font_base_size` | number (px) | `16` | `--ogc-font-size-base` on `html` |
| `ogc_heading_weight` | select | `700` | `--ogc-heading-weight` |
| `ogc_body_weight` | select | `400` | `--ogc-body-weight` |
| `ogc_line_height_body` | number | `1.6` | `--ogc-line-height-body` |
| `ogc_letter_spacing_heading` | number (em) | `-0.02` | `--ogc-letter-spacing-heading` |

### Buttons & Effects
| Key | Type | Default | Notes |
|---|---|---|---|
| `ogc_button_radius` | number (px) | `12` | `--ogc-button-radius` |
| `ogc_button_shadow_style` | select | `soft` | none / soft / glow / hard |
| `ogc_button_hover_effect_primary` | select | `lift` | lift, glow, scale, fill-sweep, border-pulse, gradient-shift, icon-slide, ripple, none |
| `ogc_button_hover_duration_primary` | select | `normal` | fast 120ms / normal 220ms / slow 350ms |
| `ogc_button_hover_intensity_primary` | select | `medium` | subtle / medium / strong |
| `ogc_button_hover_effect_outline` | select | `border-pulse` | same options, independent |
| `ogc_button_hover_duration_outline` | select | `normal` | |
| `ogc_button_hover_intensity_outline` | select | `medium` | |
| `ogc_button_active_state` | select | `scale-down` | scale-down / darken / none |
| `ogc_button_disabled_style` | select | `dimmed` | dimmed / grayscale |
| `ogc_button_focus_style` | select | `outline` | outline / glow-ring — focus never fully disabled |
| `ogc_transition_speed` | select | `normal` | fast 150 / normal 250 / slow 400 → `--ogc-transition-speed` |
| `ogc_toggle_orbit_animation` | checkbox | on | Off → body `ogc-motion-reduced-by-admin` (same path as reduced-motion in hero JS) |
| `ogc_underline_swoosh_style` | select | `gradient` | Admin label: **H1 Underline Style** — gradient / solid / none → `--ogc-swoosh-mode` |

Primary buttons: `.ogc-btn`. Outline: `.ogc-btn-outline`. Effects use body classes / data selectors in `assets/css/button-effects.css`; Ripple uses `assets/js/button-ripple.js`.

### Images
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_logo_light` | image | `''` | `ogc_get_logo_light_url()` |
| `ogc_hero_bg_home` | image | `''` | `ogc_render_hero_bg( 'home' )` |
| `ogc_hero_bg_about` | image | `''` | `about` |
| `ogc_hero_bg_services` | image | `''` | `services` |
| `ogc_hero_bg_mission` | image | `''` | `mission` |

Empty hero image = no extra layer (WebGL/canvas heroes unchanged). Favicon = WP Site Icon; theme bundled files are fallback only.

### Pages
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_hero_bg_opacity_home` | range | `35` | Inline opacity on home hero bg layer |
| `ogc_hero_bg_opacity_about` | range | `35` | about |
| `ogc_hero_bg_opacity_services` | range | `35` | services |
| `ogc_hero_bg_opacity_mission` | range | `35` | mission |

### Posts
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_blog_card_show_image` | checkbox | on | `template-parts/blog-post-card.php` |
| `ogc_blog_card_show_date` | checkbox | on | blog card |
| `ogc_blog_card_show_category` | checkbox | on | blog card |
| `ogc_blog_excerpt_length` | number (words) | `22` | `ogc_get_blog_card_excerpt()` in `inc/blog.php` |

### Ads Manager
| Key | Type | Default | Read by |
|---|---|---|---|
| `ogc_ads_enabled` | checkbox | **off** | `template-parts/ad-slot.php` — master gate |
| `ogc_ads_config_json` | ads_config | sidebar + inline slots, disabled, empty | ad-slot by `variant` key |

Slot keys in use (do not invent new ones without updating call sites):
- `sidebar` — `template-parts/blog-sidebar.php`
- `inline` — `ogc_insert_mobile_ad_slots()` in `inc/blog.php`

Per slot: `enabled`, `type` (`image`|`code`), `image`, `url`, `code`. Code is trusted-admin input, sanitized with a scoped `wp_kses` allow-list (`script`/`iframe`/`ins`, no inline event handlers). If master off, slot disabled, or no content → **render nothing** (no empty `.ogc-ad-slot`).

### Advanced
| Key | Type | Default | Output |
|---|---|---|---|
| `ogc_custom_css` | monospace | `''` | `<style id="ogc-custom-css">` late `wp_head` (`wp_strip_all_tags`) |
| `ogc_custom_js` | monospace | `''` | `<script>` late `wp_footer` (trusted admin) |

Also on this tab: **Export** (JSON download of schema keys), **Import** (schema keys only; unknown keys ignored; same sanitizers), **Reset This Tab**, **Reset All** (confirm).

---

## Front-End Wiring Summary
- Colors/fonts/button/header/footer tokens → CSS variables in `wp_head`
- Contact email → display + `inc/contact-form.php` / `inc/submit-idea-form.php` `wp_mail()`
- Motion toggle → existing hero JS reduced-motion path
- Buttons → `assets/css/button-effects.css` + optional ripple JS
- Ads → `template-parts/ad-slot.php` only

## Explicitly Out Of Scope (still requires code)
- Adding/removing entire page sections or templates
- Changing core layout grids / component order beyond documented options
- New page types or features not listed above

## QA Checklist
1. Existing getters (email→mail, social hide, hero bg, motion freeze) still work  
2. Header logo widths desktop vs mobile  
3. Footer column link edits reflect live  
4. Ads off = zero `.ogc-ad-slot` in source; on + image slot renders  
5. Primary vs outline hover effects independent; Tab focus always visible  
6. Color preset fills fields but does not save until Save all  
7. Empty GA/Pixel IDs = no tracking tags  
8. Maintenance mode: logged-out sees maintenance; admin sees site  
9. Export/import round-trip; bogus keys ignored  
10. Reset This Tab vs Reset All (with confirm)  
11. Fresh install / zero theme_mods matches previous hardcoded look  
