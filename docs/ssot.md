# Book WP — Single Source of Truth

**Version:** 3.5.1
**License:** GPL-2.0-or-later
**Text domain:** `book-wp`
**Theme slug:** `book-wp`

This document names the **file of record** for every versioned or canonical
fact in Book WP. When two sources disagree, the file named here wins; the
other is stale and should be aligned.

---

## File-of-record map

| Fact | File of record | Notes |
|---|---|---|
| Theme version | `style.css` (header) | Bump here first. `BOOK_WP_VERSION` reads it dynamically via `wp_get_theme()`. Mirror to `package.json`, `readme.txt`, `docs/readme.md`, `docs/upgrading.md`, `docs/changelog.md`. |
| Node/build version | `package.json` `"version"` | Mirror of theme version. |
| WordPress.org stable tag | `readme.txt` `Stable tag:` | Must match `style.css` Version at release. |
| Minimum WordPress | `style.css` `Requires at least:` | Currently 6.0. Mirror to `readme.txt`, `composer.json` (if relevant), `docs/upgrading.md`. |
| Minimum PHP | `style.css` `Requires PHP:` | Currently 8.2. |
| Tested up to (WP) | `style.css` `Tested up to:` | Currently 6.9.1. |
| Custom taxonomy slug | `inc/hooks/actions.php` → `book_wp_register_taxonomy()` | `literary_form`. Do not rename without a migration. |
| Default literary form terms | `inc/hooks/actions.php` (seeder) | `puisi`, `syair`, `karmina`, `ratapan`, `nukilan`, `hikayat`, `cerpen`, `novel`, `esei`, `memoir`. |
| Max thematic tags per post | `functions.php` constant `BOOK_WP_MAX_TAGS` | Default 5. Child themes may redefine before parent loads. Hidden-trigger tags are excluded from the cap. |
| Hidden tag storage format | `inc/admin/settings.php` option `book_wp_hidden_tags` | Comma-separated term IDs (since v3.4.54). Auto-migrated from slug-list on first admin load. |
| Translation template | `languages/book-wp.pot` | Locale `.po`/`.mo`/`.json` files derive from this. |
| Body class contract | `inc/helpers/literary.php` → `book_wp_body_classes()` | See `docs/readme.md` § Body Class System for the full table. |
| Paper/Ink token ownership | `assets/css/frontend.css` (`:root`) | Scheme owns surface; time owns ink. Both layers documented in `docs/readme.md` § Paper & Ink. |
| Theme options registry | `inc/admin/settings.php` `tab_fields` array | Each option key must appear under its correct tab key (`general`, `typography`, `literary`, `reading`, `advanced`, `social`). |
| Post meta keys | `inc/hooks/actions.php` → `book_wp_register_meta()` | 50+ keys. Inventory in `docs/upgrading.md` § Post Meta Registry. |
| Shortcode canonical names | `inc/shortcodes/shortcodes.php` | All canonical names use the `bwp_` prefix; legacy aliases preserved for backward compatibility. |
| Gutenberg block namespace | `inc/blocks/chapter/block.json` and `assets/js/blocks.js` | `book-wp/*` (e.g. `book-wp/chapter`, `book-wp/quote`). |

---

## Documentation inventory

| File | Audience | Lives at | Purpose |
|---|---|---|---|
| `readme.txt` | WordPress.org / end users | **root** (per WP convention) | Theme description, features, settings overview, FAQ, Upgrade Notice, Changelog summary, Privacy. |
| `docs/readme.md` | Developers | `docs/` | Architecture quick reference: directory structure, constants, hooks, helpers, shortcodes, body classes, Paper/Ink colour system, CSS/JS load order. |
| `docs/upgrading.md` | Developers / release engineers | `docs/` | Full architecture spec, post meta registry, theme options registry, template architecture, hooks in full, version milestones table, FSE migration plan, release checklist, roadmap. |
| `docs/changelog.md` | Developers / release engineers | `docs/` | Full version history, newest first. |
| `docs/ssot.md` | Maintainers (this file) | `docs/` | File-of-record map; hard invariants; documentation inventory. |

Only `readme.txt` may live at the theme root.  WordPress.org reads it from
that location; all other documentation lives in `docs/`.

---

## Hard invariants

These constraints have failure modes severe enough to break sites or violate
release policy. Do not relax them without a deliberate version bump and an
entry in `docs/upgrading.md`.

1. **`BOOK_WP_VERSION` must read from `style.css`.** Never hardcode a version
   string in PHP outside that one file. The constant is the single runtime
   accessor; `@since` markers in source files are historical and untouched
   on version bumps.
2. **License is GPL-2.0-or-later.** Reflected in `style.css`, `readme.txt`,
   `composer.json`, and `LICENSE`. Any bundled third-party asset must be
   GPL-compatible.
3. **Scheme owns surface; time owns ink.** No CSS file may set ink tokens
   inside a scheme rule, or surface tokens inside a time rule, with the
   single documented exception of the Night scheme (dark surface requires
   light ink). Per-form CSS sets surface only.
4. **Hidden-tagged posts must be excluded from every public listing.**
   Frontend (TOC, archives, search, related works, prev/next), feeds (RSS,
   JSON Feed), sitemaps (core XML, RankMath HTML), REST API (unauth),
   widget queries, dashboard statistics, and Tag Cloud widget. New listing
   surfaces added in future must also filter hidden tags.
5. **Text domain is `book-wp`.** Every user-facing string passes through a
   gettext function with this domain. JS strings register via
   `wp_set_script_translations()`.
6. **Custom taxonomy slug `literary_form` is permanent.** A rename requires
   a migration and a major version bump.
7. **Drop-in upgrades within a minor series.** Within a `3.x` series, no
   user action should be required to upgrade. Settings, options, and meta
   keys preserve compatibility or auto-migrate.
8. **`readme.txt` lives at the root.** WordPress.org infrastructure reads
   from this path; do not move it.

---

## Release checklist (condensed)

For the full checklist see `docs/upgrading.md` § Release Checklist.

1. Bump `style.css` `Version:` and `Stable tag` line in `readme.txt`.
2. Mirror to `package.json` `"version"`.
3. Update `docs/readme.md` version line and `docs/upgrading.md` version
   header.
4. Add an entry to `docs/changelog.md` (newest first).
5. Add a row to the Version Milestones table in `docs/upgrading.md`.
6. Add an Upgrade Notice and Changelog block to `readme.txt`.
7. If a fact named in this document changed, update its row here.

---

*Last reviewed: 3.5.1 (2026-05-13).*
