# 01 — Vision, Identity & Services

## About OGC NewFinity
OGC NewFinity is a digital agency based in The Hague, Netherlands. The agency positions itself around "Ethical Innovation" and delivers custom-priced project work (no fixed packages) with a 48-hour response guarantee.

## Core Tagline / Positioning
- "Build Smarter. Scale Faster."
- Mission pillars: Intelligence With Purpose · Ethical by Design · Future Innovation

## Services (Final List — 6 Services)
No blockchain. Confirmed final scope:

1. **Software & App Development** — Web apps, mobile apps, full-stack custom software.
2. **Custom WordPress Development** — Bespoke WordPress themes/builds (same approach used for this project).
3. **UI/UX Design** — Interface design, user experience, prototyping.
4. **Marketing & Strategy Consulting** — Marketing advisory, growth strategy, positioning.
5. **Cybersecurity** — Security consulting/services for client projects and infrastructure.
6. **AI Solutions** — AI development/integration work for clients.

## Brand Identity
- **Background:** Black
- **Accent:** Orange gradient (light orange → deep orange)
- **Headings:** Serif, italic, large display type (e.g., "Submit Your Idea", "Get in Touch", "About OGC NewFinity")
- **Body/UI text:** Clean sans-serif
- **Motif:** Orbiting rings / atom-like line-art graphic (used as decorative element across pages), OGC atom logo mark
- **Location badge:** Pill-shaped badge, orange gradient background, dark text — e.g. "OGC NEWFINITY" / "THE HAGUE, NETHERLANDS"
- **Cards/panels:** Dark charcoal background, thin orange border, rounded corners
- **Buttons (primary):** Orange gradient fill, dark text
- **Underline accent:** Curved orange gradient underline beneath big headings

## Company Facts (for footer/about copy)
- Est. 2022
- Location: The Hague, Netherlands
- Contact email: contact@ogcnewfinity.com
- Response time: 48h guaranteed
- Confidential + Free initial review (idea submission)

## New Feature — AI Assistant
Site will include a customer-facing AI chat assistant powered by the Gemini API. Full spec lives in `05-ai-assistant-gemini.md`.

## Admin Control Requirement (applies across whole project)
The client (site admin) must be able to change, without touching code or Cursor:
- Colors
- Fonts
- Images
- Visual effects/animations (on/off or intensity where feasible)

All via WordPress Dashboard → **OGC NewFinity** (top-level Theme Options admin page). Full field map lives in `04-admin-theme-options.md`. Settings store as theme_mods and output CSS custom properties in `wp_head`. Native Customizer is only used for core Site Identity (logo / Site Icon), not for brand options. This was a known weak point in the previous OGC NewFinity build and must work reliably this time.
