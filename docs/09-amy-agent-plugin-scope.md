# 09 — Amy Agent Plugin Scope

## Status

**Scope / reference only.** Amy Agent is **not** part of this theme and must not be implemented here. Build it as a **separate WordPress plugin** only after the theme is finished and stable. This document exists so theme templates can reserve the right integration points today without shipping plugin code.

## What Amy Agent Is

Amy Agent is the product name for OGC NewFinity’s future customer-facing AI layer. It is the core of that future plugin — not a theme feature, not a partial embed that the theme “almost” builds.

## Future Plugin Responsibilities (reference)

When the plugin is built later, Amy Agent is expected to cover:

1. **Site-wide AI chat widget** — floating launcher icon, bottom-right, on all pages (theme may ship a non-functional placeholder until then; the plugin powers the real assistant).
2. **Submit Your Idea — full replacement** — not an add-on beside the manual form. A guided multi-step conversational intake (chat bubbles + inline fields / choices per step) that produces a complete structured brief of the client’s request.
3. **Email marketing / newsletter** — sending to subscribers.
4. **Presence-aware handoff** — when Wael is offline / not logged into wp-admin, Amy notifies him via WhatsApp or Telegram instead of email-only alerts.
5. **Roadmap** — online meeting scheduling via the plugin (later).

### Forward-looking business note (no action now)

Long-term, the theme + Amy Agent plugin may be sold together as a product with a license-key system. Treat that as a future commercial option only — do not build licensing, DRM, or productization into the theme or this scope pass.

---

## AI Provider Configuration (Admin-Managed, Not Hardcoded)

The Amy Agent plugin must include a **settings page in the WordPress admin dashboard** where the site owner configures which AI provider powers Amy. API keys must **never** live in the plugin’s source code, in the theme, or in Cursor / developer tooling.

### Requirements

1. **Admin settings UI** — The site owner can:
   - Select an AI provider from a list (for example: Google Gemini, OpenAI, Anthropic Claude, DeepSeek).
   - Enter the corresponding API key for that provider.
2. **Secure storage** — The selected provider and its API key must be stored in the WordPress database via the WordPress Options API (or equivalent WP-native storage). Nothing is hardcoded in plugin source.
3. **Python middleware reads WP config** — The Python service (the middleware layer that calls the AI provider) must read the currently selected provider and API key from this WordPress-stored configuration, then call the correct provider’s API.
4. **Switch without code changes** — Changing providers later is an admin settings action only. No plugin or middleware code changes should be required to switch between supported providers.

This is a **future plugin obligation**. Do not implement provider settings, API key storage, or provider-switching logic in the theme.

---

## Integration Contract — Submit Your Idea (`/submit-idea/`)

The theme and the future plugin must cooperate so visitors never see both UIs at once.

### Theme obligations (default, zero plugin dependency)

1. Ship a **fully working manual form** as the default experience:
   - Project Title
   - Category
   - Description
   - Submit
2. The form must work with **no plugin installed**.
3. Gate the manual form behind a theme helper:

```php
ogc_amy_agent_is_active()
```

- Defined in the **theme**.
- Returns `false` by default.
- The future plugin may define/override this so it returns `true` when Amy Agent is active and ready to own the page.

4. Always fire a dedicated hook where the plugin injects its UI:

```php
do_action( 'ogc_submit_idea_render' );
```

### Replacement rule (mandatory)

| State | What renders |
| --- | --- |
| Plugin inactive / `ogc_amy_agent_is_active()` → `false` | Manual form only |
| Plugin active / `ogc_amy_agent_is_active()` → `true` | Amy Agent conversational UI only (via `ogc_submit_idea_render`) |

**Never both at once.** Amy Agent is a **total replacement** of the manual form, not a supplement beside it.

### Suggested template shape (for when Submit Your Idea is built)

```php
do_action( 'ogc_submit_idea_render' );

if ( ! ogc_amy_agent_is_active() ) {
	// Render the manual form (theme default).
}
```

(Exact markup lives in the Submit Your Idea page template when that page is implemented — this doc only locks the contract.)

---

## Integration Contract — Help & Support (`/support/`)

### Floating AI Agent icon

- The theme may show a placeholder floating agent icon.
- Once the plugin exists, Amy Agent powers that widget.

### Extra context on `/support/` (plugin system-prompt rules — not theme work)

When the visitor is on Help & Support, Amy should:

1. **Prioritize answering from the on-page FAQ content first** (General / Technical Support / Collaboration).
2. **Offer the Submit Your Idea flow only** if the visitor’s question is actually a new project / request — not a support question.

This is a **scope note for the future plugin’s system prompt / routing rules**. Do not implement FAQ-aware agent logic in the theme now.

---

## Relationship to `docs/05-ai-assistant-gemini.md`

Doc 05 described an early Gemini chat idea inside the theme. **Amy Agent supersedes that direction for product architecture:** the assistant belongs in a separate plugin. Doc 05 may still inform API/UX details later; this doc owns the theme ↔ plugin boundary and the Submit Your Idea / Support contracts.

## Status Summary

| Item | Now | Later (plugin) |
| --- | --- | --- |
| Manual Submit Your Idea form | Theme ships it | Suppressed when Amy active |
| `ogc_amy_agent_is_active()` | Theme stub → `false` | Plugin overrides → `true` |
| `ogc_submit_idea_render` | Theme fires hook | Plugin renders Amy UI |
| Chat widget | Theme placeholder ok | Plugin powers Amy |
| Support FAQ agent context | Documented only | Plugin system prompt |
| AI provider + API key | Out of scope | Admin settings → WP Options; Python middleware reads config |
| License-key productization | Out of scope | Possible commercial path |
