# Sasterawan

A minimalist literary anthology theme for WordPress. Posts are independent
literary works organised by custom taxonomy (`literary_form`), category, and
tag. The front page is a Table of Contents; single pages are reading
environments. Not a blog. Not FSE.

**Version:** 3.5.1 | **License:** GPL-2.0-or-later | **Text domain:** `book-wp`

---

## Requirements

| Requirement | Minimum |
|---|---|
| WordPress | 6.0 |
| PHP | 8.2 |
| Tested to | WordPress 6.9.1 |

---

## Installation

```bash
# Via WP-CLI
wp theme install book-wp --activate

# Or manually: upload the book-wp/ folder to /wp-content/themes/
# then activate via Appearance > Themes
```

After activation:

1. Set `Settings > General > Site Title` to the anthology or collection name
2. Go to `Appearance > Sasterawan > General` — set Author Name and Anthology Name
3. Go to `Appearance > Sasterawan > Typography` — choose font, remote-font loading,
   and colour scheme
4. Create WordPress categories for your collections
5. Go to `Posts > Literary Forms` to review or add literary form terms
6. If using Masonry or grid layouts, regenerate thumbnails after enabling:
   ```bash
   wp media regenerate --yes
   ```

---

## Directory Structure

```
book-wp/
├── style.css               ← Theme header only (version, metadata)
├── functions.php           ← Constants + require_once bootstrap; no feature code
├── theme.json              ← Gutenberg editor design tokens
│
├── inc/
│   ├── setup/              ← theme-support, menus, sidebars, enqueue
│   ├── helpers/            ← literary, analytics, colophon, navigation, social,
│   │                         permalink, json-feed
│   ├── hooks/              ← actions.php, filters.php
│   ├── shortcodes/         ← shortcodes.php (6 shortcodes)
│   ├── blocks/chapter/     ← book-wp/chapter Gutenberg block
│   └── admin/              ← meta-boxes.php, settings.php
│
├── assets/
│   ├── css/
│   │   ├── frontend.css    ← All base styles and CSS custom properties
│   │   ├── literary.css    ← Literary block styles (colophon, work-nav)
│   │   ├── forms/          ← Per-literary-form overrides (10 files)
│   │   ├── tags/           ← Per-tag decorative CSS (whitelist-gated)
│   │   ├── schemes/        ← Colour scheme overrides (cream, dusk, night)
│   │   ├── fonts/          ← Font-family overrides (6 non-default fonts)
│   │   └── times/          ← Time-of-day palette (all 5 periods)
│   ├── img/                ← Static image assets
│   └── js/
│       ├── frontend.js     ← Progress bar, keyboard nav, night mode, layouts
│       ├── time-theme.js   ← Time-of-day class + night toggle expiry
│       ├── admin-settings.js
│       ├── blocks.js       ← Gutenberg block registration
│       └── vendor/         ← masonry, imagesloaded, infinite-scroll
│
├── templates/              ← custom page templates (colophon, foreword, puisi)
├── languages/              ← .pot, ms_MY, es_ES, fr_FR (.po/.mo/.json)
├── docs/
│   ├── ssot.md             ← Single Source of Truth — canonical facts, file-of-record map
│   ├── readme.md           ← Developer reference (this file)
│   ├── upgrading.md        ← Architecture, post meta registry, roadmap, version milestones
│   └── changelog.md        ← Full version history
└── readme.txt              ← WordPress.org / general user documentation (root, per WP convention)
```

For the complete annotated directory listing, see `upgrading.md`.

---

## Constants

Defined in `functions.php` before the require chain:

| Constant | Value |
|---|---|
| `BOOK_WP_VERSION` | Read dynamically from `style.css` via `wp_get_theme()` |
| `BOOK_WP_DIR` | `get_template_directory()` |
| `BOOK_WP_URI` | `get_template_directory_uri()` |
| `BOOK_WP_MAX_TAGS` | `5` — thematic tag cap per post |

To override `BOOK_WP_MAX_TAGS` in a child theme, define the constant before
the parent loads:

```php
// child theme functions.php
define( 'BOOK_WP_MAX_TAGS', 8 );
```

---

## Custom Taxonomy

**Slug:** `literary_form` | **Object type:** `post` | **Hierarchical:** no

Seeded on `after_switch_theme` with ten default terms:

`puisi` `syair` `karmina` `ratapan` `nukilan` `hikayat` `cerpen` `novel` `esei` `memoir`

The taxonomy drives per-form CSS loading, analytics display, colophon field
sets, drop cap settings, and body class assignment. Additional terms can be
added freely — they will appear in archives and TOC grouping but will not
receive per-form CSS or analytics specialisation unless explicitly added.

---

## Paper & Ink — Colour Architecture

The theme's colour system is built on a strict two-layer contract:

> **Scheme owns surface (Paper). Time owns ink (Ink).**

"Surface" means background colour, border tones, and subtle backgrounds.
"Ink" means text colour, link colour, accent, ornament, and progress bar.
The two layers are kept independent so every combination of scheme × time
period produces a valid, readable result.

### Why it works at the CSS level

The split relies on a CSS cascade property: custom properties set directly
on `<body>` always beat values inherited from `<html>`, regardless of
selector specificity or stylesheet load order.

- `bwp-time-*` classes are applied to `<html>` by `time-theme.js`, which
  runs synchronously in `<head>` before first paint.
- `bwp-scheme-*` classes are applied to `<body>` by `body_class()` in PHP.

Time sets ink tokens on `:root` (= `<html>`). Scheme sets surface tokens on
`body.bwp-scheme-*`. The body-level surface declarations beat the inherited
time-level surface declarations — no specificity fight needed, no load-order
dependency.

### CSS token groups

All tokens defined on `:root` in `frontend.css`:

**Surface tokens** (owned by scheme):
`--bwp-bg`, `--bwp-bg-subtle`, `--bwp-border`, `--bwp-border-faint`, `--bwp-stone`

**Ink tokens** (owned by time):
`--bwp-text`, `--bwp-text-mid`, `--bwp-muted`, `--bwp-link`, `--bwp-link-hover`,
`--bwp-accent`, `--bwp-ornament`, `--bwp-progress`, `--bwp-border-ink`,
`--bwp-teal`, `--bwp-teal-deep`, `--bwp-gold`, `--bwp-gold-dim`, `--bwp-gold-light`

### Schemes (Paper)

Four schemes, set via `Appearance > Sasterawan > Typography > Paper`:

| Scheme | Class on `<body>` | CSS file | Character |
|--------|------------------|----------|-----------|
| Default | `bwp-scheme-default` | (none — `:root` values) | Warm white |
| Cream | `bwp-scheme-cream` | `schemes/cream.css` | Aged parchment |
| Dusk | `bwp-scheme-dusk` | `schemes/dusk.css` | Warm grey |
| Night | `bwp-scheme-night` | `schemes/night.css` | Persistent dark mode |

Cream and Dusk set only surface tokens. Night is the exception: because a
dark background requires light ink for legibility, the Night scheme sets both
surface and ink tokens on `<body>`.

Each non-default scheme file includes compound selectors (e.g.
`:root.bwp-time-dawn body.bwp-scheme-cream`) that tune the surface to
complement the active ink mood — cooler at dawn, warmest at morning, deep
at dusk, dimmed at night.

### Time of Day (Ink)

Five periods, set automatically from the visitor's local clock or pinned by
the admin via `Appearance > Sasterawan > Typography > Ink`:

| Period | Hours | Class on `<html>` | Character |
|--------|-------|-------------------|-----------|
| Dawn | 04:00–06:59 | `bwp-time-dawn` | Lavender-violet |
| Morning | 07:00–11:59 | `bwp-time-morning` | Amber-gold |
| Afternoon | 12:00–16:59 | `bwp-time-afternoon` | Neutral (no overrides) |
| Dusk | 17:00–20:59 | `bwp-time-dusk` | Warm ochre |
| Night | 21:00–03:59 | `bwp-time-night` | Silver on dark |

Afternoon is the default. `times.css` contains no rules for the afternoon
period — the `:root` fallback values in `frontend.css` serve as the neutral
baseline.

Night (time) includes a surface block in `times.css` for the case where no
explicit scheme is active. When a scheme is active, its body-level surface
tokens override the inherited time-night surface.

### Night mode — five activation paths

Dark mode can be triggered five ways. All five must produce the same
visual result for a given scheme:

| # | Path | Selector | Set by |
|---|------|----------|--------|
| 1 | Manual toggle (preload) | `html.bwp-night body` | Inline `<script>` in `<head>`, reads `localStorage` |
| 2 | Manual toggle (runtime) | `body.bwp-night` | `frontend.js` click handler |
| 3 | Night scheme | `body.bwp-scheme-night` | PHP `body_class()` |
| 4 | Time-of-day night | `:root.bwp-time-night` | `time-theme.js` |
| 5 | OS dark mode | `@media (prefers-color-scheme: dark)` | CSS media query |

All five paths carry identical token sets (surface + ink + accent tokens).

### Per-form palettes

Three literary forms define form-specific surface tints for daytime reading:
hikayat (warm parchment), ratapan (muted grey), and memoir (warm white).
These are scoped to surface tokens only — ink tokens are always inherited
from the time system.

The day palette selectors include `:not(.bwp-scheme-cream):not(.bwp-scheme-dusk)`
guards so the form's surface yields when an explicit Cream or Dusk scheme
is active. Night mode guards (`:not(.bwp-time-night):not(.bwp-night)`) prevent
the day palette from overwriting dark-mode ink.

Form-specific night blocks set only accent tokens (e.g. `--bwp-gold-dim`)
that are unique to that form's decorative elements. Surface and ink come
from the night system.

### Night toggle auto-expiry

The manual night toggle (floating moon button) stores `bwp_night_mode` and
`bwp_night_date` in `localStorage`. The time engine (`time-theme.js`)
auto-expires the toggle when:

1. The current clock period is daytime (dawn through dusk), AND
2. The toggle was activated on a previous calendar date (or has no date
   stamp — legacy entries from before v3.4.91).

Same-day toggles are always honoured. The expiry runs before the preload
script reads `localStorage`, so there is no flash of dark mode on stale
preferences.

Users who want persistent dark mode should use the Night scheme (Paper
setting) instead of the toggle.

### Time-of-day live updates

`time-theme.js` re-evaluates the period on two triggers:

- `visibilitychange` — when the visitor returns to a backgrounded tab
- Hourly `setInterval` — catches transitions on foreground tabs

Both are skipped when the admin has pinned a specific period (non-auto mode).

---

## CSS Architecture

### Load order (per page)

```
1.  style.css                  ← theme header only
2.  frontend.css               ← base styles + CSS custom properties
3.  literary.css               ← literary block styles (always; dep: frontend)
4.  times/times.css            ← time-of-day palette (dep: literary)
5.  forms/{slug}.css           ← per-form CSS (single posts only; dep: times)
6.  tags/{slug}.css            ← per-tag decorative CSS (whitelist-gated; dep: frontend)
7.  schemes/{name}.css         ← colour scheme override (if not default; dep: times)
8.  fonts/{name}.css           ← font-family override (if not Georgia; dep: frontend)
9.  book-wp-custom             ← inline custom CSS handle (dep: active theme styles)
10. admin.css                  ← admin only
11. editor.css                 ← block editor only
```

Load order matters: scheme files load after times so compound selectors
(`:root.bwp-time-* body.bwp-scheme-*`) resolve correctly. Form files load
after times so inherited ink tokens are available.

Custom CSS is attached with `wp_add_inline_style()` to a synthetic
`book-wp-custom` handle that depends on the active style stack, so user CSS
prints after scheme, form, tag, and font layers.

Remote Google Fonts are controlled by `book_wp_remote_fonts` (default `1`).
When disabled, Sasterawan skips Google Fonts and preconnect hints. The CSS still
declares the preferred font-family stack, so visitor-installed fonts can be
used before browser/system fallbacks.

### CSS class prefix

All theme classes use `bwp-` with BEM-style naming: `.bwp-block__element--modifier`.

### Per-tag CSS pipeline

Drop a file at `assets/css/tags/{slug}.css`, scope styles to
`body.bwp-tag-{slug}`, and add the slug to `$tag_css_slugs` in
`inc/setup/enqueue.php`. The sheet loads only on single posts carrying
that tag. `bwp-tag-{slug}` body classes are added for every tag on
`is_single()` pages.

---

## JavaScript Architecture

### Load order

```
1. time-theme.js      ← <head>, synchronous. Applies bwp-time-{period} to <html>;
                         expires stale night toggle. Runs BEFORE the night preload
                         inline script in header.php.
2. (preload script)   ← Inline <script> after wp_head(). Reads localStorage for
                         night mode; applies html.bwp-night if active.
3. frontend.js        ← Footer, after DOM. IIFEs for: progress bar, keyboard nav,
                         night toggle (stores bwp_night_date), scroll memory,
                         stanza navigation, archive/TOC layout init.
4. admin-settings.js  ← Admin only. Tab switching, live font/scheme preview.
5. blocks.js          ← Block editor only. Registers book-wp/term Format API,
                         book-wp/quote, book-wp/divider, book-wp/verse blocks.
6. vendor/*.min.js    ← Registered; enqueued only when layout/pagination requires.
```

### The `bookWP` object

Set via `wp_localize_script` on the `book-wp-time-theme` handle. Available to
both `time-theme.js` (which runs in `<head>`) and `frontend.js` (which runs
in the footer). Exposes all reading and layout settings:

| Key | Type | Source |
|-----|------|--------|
| `timeTheme` | `string` | `book_wp_time_theme` option |
| `progressBar` | `string` | `book_wp_progress_bar` |
| `keyboardNav` | `string` | `book_wp_keyboard_nav` |
| `readingMemory` | `string` | `book_wp_reading_memory` |
| `nightToggle` | `string` | `book_wp_night_toggle` |
| `stanzaNumbers` | `string` | `book_wp_stanza_numbers` |
| `archiveLayout` | `string` | `book_wp_archive_layout` |
| `archivePagination` | `string` | `book_wp_archive_pagination` |
| `tocLayout` | `string` | `book_wp_toc_layout` |
| `tocPagination` | `string` | `book_wp_toc_pagination` |
| `shareRow` | `string` | `book_wp_share_row` |
| `backToTop` | `string` | `book_wp_back_to_top` |
| `copied` | `string` | Translated "Copied!" label |

All values are strings (`'0'` or `'1'`) because `wp_localize_script`
serialises everything as strings.

### Execution order dependency

The night preload inline script in `header.php` runs AFTER `time-theme.js`
(which is enqueued in `wp_head()`). This ordering is critical: if the night
toggle has expired, `time-theme.js` clears `localStorage` before the
preload reads it. No flash of dark mode on stale preferences.

---

## Hooks

### Actions (`inc/hooks/actions.php`)

| Hook | Function | Purpose |
|---|---|---|
| `init` | `book_wp_register_taxonomy` | Register `literary_form`; seed/migrate terms |
| `init` | `book_wp_register_meta` | Register 50+ post meta keys |
| `init` | `book_wp_register_blocks` | Register `book-wp/chapter` block |
| `wp_head` | `book_wp_structured_data` | JSON-LD Schema.org/CreativeWork output |
| `wp_head` | `book_wp_social_meta` | OpenGraph + Twitter Card meta (SEO plugin guard) |
| `wp_body_open` | `book_wp_progress_bar_markup` | Progress bar HTML |
| `wp_footer` | `book_wp_night_toggle_markup` | Night mode toggle button |
| `after_switch_theme` | `book_wp_on_activation` | Seed terms, flush rewrites |
| `save_post` / `trashed_post` / `untrashed_post` | `book_wp_clear_toc_cache` | Invalidate TOC transient |
| `save_post` | `book_wp_enforce_tag_cap` | Enforce `BOOK_WP_MAX_TAGS` |
| `rest_after_insert_post` | `book_wp_enforce_tag_cap_rest` | Same via REST API |
| `pre_get_posts` | `book_wp_hide_from_public_queries` | Exclude hidden-tagged posts from home, category, date, author, search, feed, and non-hidden tag archives |
| `pre_get_posts` | `book_wp_hide_from_widget_queries` | Exclude hidden-tagged posts from sidebar widget secondary queries |
| `rest_post_query` | `book_wp_hide_from_rest_query` | Exclude hidden-tagged posts from REST API (unauthenticated / non-editor) |
| `wp_sitemaps_posts_query_args` | `book_wp_hide_from_sitemap` | Exclude hidden-tagged posts from core XML sitemap |
| `rank_math/sitemap/html_sitemap/post_args` | `book_wp_hide_from_rankmath_html_sitemap` | Exclude hidden-tagged posts from RankMath HTML sitemap |
| `widget_tag_cloud_args` | `book_wp_hide_tags_from_cloud` | Suppress hidden tags from Tag Cloud widget |

### Filters (`inc/hooks/filters.php`)

| Hook | Function | Purpose |
|---|---|---|
| `body_class` | `book_wp_body_classes` | Add all `bwp-*` context classes |
| `theme_page_templates` | `book_wp_register_page_templates` | Register three custom PHP page templates |
| `render_block_core/search` | `book_wp_translate_search_block` | Translate Search block button text |
| `the_tags` | `book_wp_suppress_hidden_tags_from_output` | Remove hidden tag links from `the_tags()` / `get_the_tag_list()` output |

---

## Helper Functions

### `inc/helpers/literary.php`

| Function | Returns |
|---|---|
| `book_wp_get_form_slug( $post_id )` | `literary_form` slug for a post |
| `book_wp_get_form_label( $post_id )` | Human-readable form label |
| `book_wp_form_label( $slug )` | Label for a given slug |
| `book_wp_is_poetry( $post_id )` | `true` if form is puisi / syair / karmina / ratapan |
| `book_wp_form_description( $slug )` | Editorial description for archive header |
| `book_wp_ornament( $style )` | SVG/HTML ornament markup |

### `inc/helpers/analytics.php`

| Function | Returns |
|---|---|
| `book_wp_reading_time_minutes( $post_id )` | Numeric reading-time estimate in minutes |
| `book_wp_reading_time( $post_id )` | Formatted reading time string |
| `book_wp_word_count( $post_id )` | Word count |
| `book_wp_line_count( $post_id )` | Line count |
| `book_wp_stanza_count( $post_id )` | Stanza count |
| `book_wp_analytics_bar( $post_id )` | Analytics bar HTML |

Chapter block text extraction uses WordPress `parse_blocks()` when available
and falls back to a small regex parser only in standalone test environments.

### `inc/helpers/colophon.php`

| Function | Returns |
|---|---|
| `book_wp_epigraph( $post_id )` | Epigraph block HTML |
| `book_wp_dedication( $post_id )` | Dedication block HTML |
| `book_wp_colophon( $post_id )` | Full colophon block HTML (form-specific) |

### `inc/helpers/navigation.php`

| Function | Returns |
|---|---|
| `book_wp_related_works( $post_id, $max )` | Related works HTML (same source → same form, skips hidden) |
| `book_wp_work_navigation()` | Prev/next navigation HTML |
| `book_wp_toc_entry( $post, $show_badge, $show_thumb )` | Single TOC list item HTML |
| `book_wp_featured_image( $post_id )` | Featured image block HTML |
| `book_wp_share_row( $post_id )` | Share row HTML (Copy link, X, Facebook, WhatsApp, etc.) |
| `book_wp_permalink_form_slugs()` | All registered literary_form slugs as a flat array |

---

## Shortcodes

Each shortcode has a `bwp_*` canonical name and a legacy alias:

| Canonical | Alias | Output |
|---|---|---|
| `[bwp_quote source="..."]` | `[quote]` | Pull-quote block |
| `[bwp_divider style="fleuron"]` | `[divider]` | Section divider ornament |
| `[bwp_term definition="..."]` | `[term]` | Inline term with tooltip |
| `[bwp_verse num="1"]` | `[verse]` | Verse block |
| `[bwp_chapter num="1" title="..."]` | `[chapter]` | Chapter heading |
| `[bwp_v num="3"]` | `[v]` | Inline verse number |

The `book-wp/chapter` Gutenberg block is the block-editor equivalent of
`[bwp_chapter]`. Both produce identical DOM output.

---

## Body Class System

`book_wp_body_classes()` (filter: `body_class`) adds context classes consumed
by both CSS and JS:

| Class | Condition |
|---|---|
| `bwp-scheme-{name}` | Always — active colour scheme |
| `bwp-font-{name}` | Always — active font |
| `bwp-lang-{code}` | Always — first two chars of locale |
| `bwp-dropcap-{form}` | When drop cap enabled for that form |
| `bwp-dropcap-force-on` / `force-off` | Single page, per-post override |
| `bwp-poetry-lh-{value}` | Always — poetry line-height setting |
| `bwp-stanza-{value}` | Always — stanza spacing setting |
| `bwp-poetry-centred` | When poetry alignment is centre |
| `bwp-stanza-nums` | When stanza numbers enabled |
| `bwp-form-{slug}` | Single pages only — literary form of the post |
| `bwp-tag-{slug}` | Single pages only — per-tag decorative class |
| `bwp-is-archive` | Archive and tag pages |
| `bwp-archive-grid` / `masonry` | When archive layout is grid or masonry |
| `bwp-is-toc-flat` | Front page when TOC grouping = none |
| `bwp-toc-grid` / `masonry` | When TOC flat-list layout is grid or masonry |

JS-applied (not in `body_class` filter):

| Class | Set by |
|---|---|
| `bwp-night` | `frontend.js` night toggle (synced to `html` and `body`) |
| `bwp-time-{period}` | `time-theme.js` on `<html>` only |

---

## Caching

| Transient | TTL | Invalidated by |
|---|---|---|
| `book_wp_toc_posts` | 12 hours | `save_post`, `trashed_post`, `untrashed_post` |

The transient caches the full `get_posts(-1)` result for grouped and
non-paginated TOC modes. Not used in the Infinite Scroll path.

---

## Internationalisation

Text domain: `book-wp`. All user-facing strings wrapped in `__()`, `_e()`,
or `esc_html_e()`. JS strings served via `wp_set_script_translations()`.

Translation files in `languages/`:

| Locale | Files |
|---|---|
| Bahasa Melayu | `ms_MY.po`, `.mo`, `.json` |
| Spanish | `es_ES.po`, `.mo`, `.json` |
| French | `fr_FR.po`, `.mo`, `.json` |
| Template | `book-wp.pot` |

---

## Child Themes

Standard WordPress child theme structure applies. The `bwp-` prefix and
`book_wp_` function prefix isolate theme code. Useful extension points:

- Override any template file in the child theme directory
- Use `body.bwp-form-{slug}` and `body.bwp-tag-{slug}` body classes to
  target specific forms or tags in child theme CSS
- Add per-tag decorative CSS by creating `assets/css/tags/{slug}.css` and
  adding the slug to `$tag_css_slugs` in `inc/setup/enqueue.php`
- Use `book_wp_get_form_slug()`, `book_wp_is_poetry()`, and helper functions
  directly in child theme templates

---

## CI & Linting

| Tool | Config |
|---|---|
| phpcs | `config/.phpcs.xml.dist` (WordPress + PHPCompatibility rulesets) |
| eslint | `config/.eslintrc.json` |
| stylelint | `config/.stylelintrc.json` |
| PHPUnit | `config/phpunit.xml` |
| GitHub Actions | `.github/workflows/ci.yml` — lint and test on PR/push |
| Lighthouse | `config/lighthouserc.json` — performance budget |

Local setup:

```bash
composer install
npm install
composer test
composer lint:php
npm run lint
```

---

## Further Reading

- `ssot.md` — Single Source of Truth: which file owns which fact (version,
  text domain, taxonomy slug, etc.) and the doc inventory
- `upgrading.md` — complete architecture reference, post meta registry, theme
  options, body class system, template architecture, hooks in full, roadmap,
  FSE migration plan, and version milestones
- `changelog.md` — full version history
