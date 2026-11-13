# Changelog

All notable changes to the OGC NewFinity theme are documented here.

## [1.27.0] — 2026-11-13

### Added

- **Theme Options Overview** — dashboard landing view with tab stats, card grid, quick on/off toggles for the first switch in each section, and deep links into each tab.
- **Per-tab access control** — administrators can grant non-admin roles and specific user IDs access to individual Theme Options tabs; enforced server-side on direct URLs and on save/import/reset.
- **`inc/theme-options-access.php`** — overview helpers, access map storage (`ogc_theme_options_tab_access`), and capability checks.

### Changed

- Field help text is shown only through the unified `(?)` tooltip bubble (including section headings); tab-level intro lines under each `<h2>` are unchanged.
- Theme Options menu uses `edit_posts` with per-user visibility; delegated users no longer need full `edit_theme_options` for granted tabs only.

## [1.26.14] — 2026-11-13

### Added

- Legacy URL redirects (`/mission`, `/help`) on 404 only.
- Theme Options company details fields (KVK / VAT) and optional footer line.

### Changed

- Pricing catalogue adjustments (AI Starter, software discovery/MVP cards, text-price styling).
