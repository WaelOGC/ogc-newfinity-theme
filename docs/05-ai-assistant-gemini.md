> **⚠️ SUPERSEDED — DO NOT IMPLEMENT FROM THIS DOCUMENT.**
> The site-wide AI chat widget described below is now owned by the future **Amy Agent plugin** (see `09-amy-agent-plugin-scope.md`, section "Future Plugin Responsibilities" item 1). It will NOT be built as a theme feature, and will not necessarily use the Gemini API as described below — that decision is deferred to when the plugin is actually built. This file is kept only as historical context for the original idea. Read `09-amy-agent-plugin-scope.md` for the current plan.

# 05 — AI Assistant (Gemini API Integration)

## Purpose
A customer-facing chat assistant embedded on the site (widget, likely bottom-right corner, all pages) that can interact with visitors — answer questions about OGC NewFinity's services, help guide idea submissions, general assistance.

## Requirements
- Powered by **Google Gemini API** (Wael will supply his own Gemini API key).
- API key must be stored securely — **never hardcoded in theme files**. Store via:
  - A field in the custom Theme Options admin page (see `04-admin-theme-options.md`), saved to the WordPress options table, OR
  - A `wp-config.php` constant (more secure, but requires manual server-side edit — decide based on Wael's comfort level; default to admin-page storage for simplicity unless told otherwise).
- Chat widget UI must match brand identity: dark panel, orange gradient accents, serif italic header text for the widget title, sans-serif for chat bubbles.
- Widget behavior:
  - Floating launcher button (bottom-right, all pages)
  - Expands into chat panel on click
  - Sends visitor messages to Gemini API via a WordPress REST API endpoint or `admin-ajax.php` handler (server-side proxy — the API key must never be exposed in front-end JS).
  - Displays streamed or full response back to visitor.
- Assistant should be given a system prompt/context describing OGC NewFinity, the 6 services, location, and response-time guarantee, so it answers accurately about the company rather than generically.

## Technical Notes for Cursor
- Server-side: PHP function that calls the Gemini API endpoint using the stored API key, triggered via a REST route (e.g. `/wp-json/ogc/v1/chat`) or AJAX action.
- Front-end: lightweight vanilla JS (no heavy framework needed) for the widget UI and fetch calls to the REST route.
- Rate-limiting/basic abuse protection should be considered (simple nonce + maybe a basic request cap) since this uses a paid API key.
- This feature can be built after the core theme/pages are complete and stable — it is an enhancement layer, not part of the initial skeleton.

## Status
Superseded by `09-amy-agent-plugin-scope.md`. Not part of the theme.
