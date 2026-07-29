# 07 — Elementor Compatibility & Custom Widgets

## Why This Document Exists
The client's previous purchased theme ("Bili"/WGL-based) shipped its own library of custom Elementor widgets (visible in the Elementor editor sidebar as "WGL Header Modules," etc.) — ready-made, brand-styled building blocks (logo, menu, sliders, buttons, image-with-effects, etc.) that could be dragged into any page and controlled entirely from the Elementor left panel (per-word color control, animations, etc.), with zero code editing.

The OGC NewFinity theme must offer the same capability: a small library of **custom Elementor widgets**, styled to match the OGC brand identity (docs/02), registered so they appear in the Elementor editor under an "OGC NewFinity" widget category.

## Elementor Base Compatibility (foundation, required first)
1. Theme must declare Elementor support and full-width/canvas-friendly content areas.
2. Provide (or use Elementor's native) a full-width/canvas page template so the theme header/footer wrap around Elementor-built content cleanly, without double-padding or style conflicts.
3. Ensure the_content(), wp_head(), wp_footer(), and body classes are implemented correctly (already done in the skeleton) so Elementor (and other plugins like Contact Form 7, WPForms, Yoast, etc.) function without conflicts.
4. Enqueue theme's main.css variables so Elementor-built sections can inherit the same CSS custom properties (colors/fonts) — keeps everything visually consistent even inside Elementor-built sections.

## Custom Widget Library — Phase 1 (build these first, about 8 to 12 widgets)
Each widget must expose editable controls in the Elementor left panel (text, per-element color, typography, spacing, and where relevant, simple hover/entrance animation toggles) — matching the flexibility shown in the client's reference screenshot.

1. OGC Badge/Pill — small eyebrow label with gradient background, editable text + color.
2. OGC Split Hero — two-column hero: badge + large italic serif headline (word-by-word color control) + underline swoosh + supporting text, paired with a right-side slot (image, form, or repeater cards).
3. OGC Feature Card — icon/image + title + description, border/background/hover-effect controls.
4. OGC Service Card — icon + title + short text, grid-friendly, used on Services page.
5. OGC Gradient Button — call-to-action button with the brand gradient fill, editable text/link/size, hover animation toggle.
6. OGC Outline Button — secondary button variant (orange border, transparent fill).
7. OGC Image With Effects — single image widget with built-in hover/reveal effect options and optional caption, matching the "single image ready with effects" feature the client referenced.
8. OGC Slider — brand-styled content/image slider (autoplay, speed, transition style controls).
9. OGC CTA Band — full-width call-to-action section (headline + button), used before footer on multiple pages.
10. OGC Orbit Graphic — decorative animated orbit/atom ring graphic, position and opacity controls.
11. OGC Footer Meta Block — contact/location meta block matching footer styling, for use in custom footer layouts if needed.
12. OGC Logo/Menu Widget — wraps the theme's custom-logo + primary nav for use in fully Elementor-built headers, if the client ever wants an Elementor-based header instead of the PHP one.

## Technical Approach (for Cursor implementation)
- Register a new Elementor widget category: ogc-newfinity (label: "OGC NewFinity").
- Each widget is a PHP class extending Elementor Widget_Base, placed in inc/elementor/widgets/, with a loader inc/elementor/widgets-loader.php that registers all widgets via the elementor/widgets/register hook.
- Use Elementor's native Controls (add_control, add_group_control for typography/background/border) so the client gets the same left-panel editing experience as the reference screenshot (per-element color, typography, spacing, simple animations via Elementor's built-in entrance animations where possible, rather than reinventing animation systems).
- Widgets must read their default colors/fonts from the theme's CSS custom properties (docs/04 Theme Options) so brand consistency is automatic, while still allowing per-instance overrides in the Elementor panel.
- Graceful degradation: if Elementor is not installed/active, the theme itself must still work fine (widgets simply won't be registered; no fatal errors — always check for Elementor being loaded before registering).

## Other Plugin Compatibility (general, not just Elementor)
- Contact Form 7 / WPForms — no special theme code required beyond correct the_content() and enqueue behavior (already in place); style form output generically via CSS so forms visually match the brand automatically (dark inputs, orange focus states) without per-plugin custom code.
- Yoast SEO / RankMath — ensure wp_head() placement is correct (already in place) so SEO meta tags render properly. Full SEO requirements now live in 08-seo-requirements.md.
- Any future plugin — theme must never hardcode assumptions that break standard WP hooks; always use standard action/filter hooks.

## Build Order Note
Phase 1 widget list above should be built after the core page templates exist (docs/06 build order steps 1-4), so widgets can be tested directly on real pages. Confirmed final content pages must NOT rely solely on Elementor for their primary content — see docs/03 update: pages should ship complete and final via Cursor-built PHP templates or Elementor-built content saved directly into the page, not left empty for the client to fill in later. Elementor/custom widgets exist so the client has future editing flexibility, not as the initial delivery method.
