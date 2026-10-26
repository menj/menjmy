## [4.0.0] — Sasterawan: Malaysiana Redesign

### Compatibility
- Data model unchanged from Book WP 3.5.x: options, post meta keys, taxonomy,
  menu locations, widget areas and text domain all keep their names.
- New `inc/helpers/migrate.php` copies theme mods (logo, header/background,
  Customizer CSS, menu locations) from a `book-wp` folder on activation when
  the theme is installed under a new folder. Covered by `tests/MigrationTest.php`.

### Changed
- Theme renamed **Sasterawan**, tailored for https://menj.my (Di Bawah Permukaan).
- Front end rebuilt from scratch (`assets/css/frontend.css`): editorial,
  asymmetric openings with oversized Fraunces display type set off-axis, a
  nila/kunyit/kapas palette, 999px pills for navigation, badges, chips and
  buttons, 20–28px card radii, and a self-hosted Jawi face (Dubidam Arabic)
  for the anthology watermark, per-form watermarks and the footer signature.
- New site header: brand mark + author, centred context label
  ("Puisi · Sanggahan"), pill navigation with a phone disclosure button.
- Front page opens like an anthology jacket: a toned underwater photograph
  (David Boca, Pexels) under the Kagnue title, with an imprint line and Jawi
  seal at the head and the author's name and edition line at the foot.
  Switchable and replaceable under Settings → General. **Jalur tema** pills
  generated from categories, table of contents as two-column cards.
- One title-page panel opens every work, page, archive, tag, search and the
  404: kicker and Jawi seal at the head, Kagnue title, chips or description
  beneath a hairline. Kagnue carries all h1/h2; Fraunces keeps italics,
  datelines and list titles. The floating strand blurb under the header is
  retired. Rounded ink-tile drop cap and colophon card; the previous/next work
  navigation and its setting are removed.
- Night mode and the Night scheme now share one Malam palette; Cream and Dusk
  re-ink correctly at clock-night and under the reader toggle.
- Per-form stylesheets no longer set the title treatment; they keep their
  form's typography, reading measure and ornament styling.
- Display font Cinzel replaced by Fraunces; Noto Naskh Arabic loaded for
  Jawi fallback.

### Added
- **Terjemahan** literary form (translations into Malay, e.g. Poe) with
  source title, author, year, language and translator's note fields and an
  apparatus block above the work.
- `book_wp_jawi_name` option (General tab): anthology name in Jawi.
- Brand assets under `assets/img/brand/` (mark, inverse, mono, favicon,
  wordmark, lockup) and a favicon fallback when no Site Icon is set.
- `book_wp_form_jawi()`, `book_wp_header_section_label()`,
  `book_wp_terjemahan_block()` helpers.

### Fixed
- Meta box no longer loses edits when a field name appears in several form
  groups; stray `</div>` removed.
- Legacy-slug redirect leaves paged posts, comment pages, feeds and previews
  alone; permalink toggle defers its rewrite flush.
- Stanza counter no longer merges bare paragraphs.
- Test bootstrap loads without WordPress (plugin API stubs); 120 tests pass.
- Back-to-top and copy tooltip use defined colour tokens.

## [3.5.1] — Maintenance Hardening, Tooling & Font Privacy

### Fixed
- `assets/js/frontend.js`: Back to Top now runs inside its own IIFE and reads
  `window.bookWP` locally, fixing the out-of-scope `cfg` reference.
- `inc/admin/settings.php` / `inc/setup/enqueue.php`: Custom CSS no longer
  depends on an admin-only include or raw `wp_head` output. It is attached via
  `wp_add_inline_style()` to a synthetic `book-wp-custom` style handle that
  depends on the active front-end style stack.
- `inc/helpers/json-feed.php`: `reading_time_min` now uses
  `book_wp_reading_time_minutes()` instead of casting a translated display
  string.

### Added
- Typography setting `book_wp_remote_fonts` / "Load Google Fonts". Disable it
  to prevent Google Fonts stylesheet requests, preconnect hints, and
  form-specific remote display font requests.
- `book_wp_reading_time_minutes()` numeric helper.
- Tooling config: `config/.eslintrc.json`, `config/.stylelintrc.json`,
  `config/.phpcs.xml.dist`, Composer `test`, and `.github/workflows/ci.yml`.

### Changed
- `inc/helpers/analytics.php`: Chapter block extraction uses WordPress
  `parse_blocks()` when available, keeping the regex parser only as a
  standalone PHPUnit fallback.
- `composer.json` now includes PHPUnit and the PHPCS installer plugin in
  `require-dev`; lint scripts point to the config files under `config/`.
- Translation template, locale `.po`, and compiled `.mo` files refreshed for
  the new Typography setting strings.
- Documentation updated across `readme.txt`, `docs/readme.md`,
  `docs/upgrading.md`, and `docs/ssot.md`.

## [3.5.0] — Documentation Reorganisation, Version Streamline & i18n Cleanup

### Fixed
- `comments.php`: **14 strings were Malay text used as English `msgid` arguments**
  to `__()`/`_e()`/`_n()` — none of them existed in `book-wp.pot` or any locale
  `.po` file, so WordPress was falling through to the Malay msgid for English
  visitors and "translating" Malay msgids to themselves for Malay visitors only
  by coincidence.  All replaced with canonical English msgids:

  | Line(s) | Before (msgid in source)                                   | After                                                                |
  |---------|------------------------------------------------------------|----------------------------------------------------------------------|
  | 27      | `'%s Ulasan'` / `'%s Ulasan'`                              | `'%s Comment'` / `'%s Comments'` (proper singular/plural)            |
  | 60      | `'Tinggalkan Ulasan'`                                      | `'Leave a Comment'`                                                  |
  | 61      | `'Balas kepada %s'`                                        | `'Reply to %s'`                                                      |
  | 62      | `'Batal'`                                                  | `'Cancel'`                                                           |
  | 63      | `'Hantar Ulasan'`                                          | `'Post Comment'`                                                     |
  | 68      | `'Ulasan'`                                                 | `'Comment'`                                                          |
  | 76      | `'Nama'`                                                   | `'Name'`                                                             |
  | 81      | `'E-mel'`                                                  | `'Email'`                                                            |
  | 90      | `'Simpan nama dan e-mel saya dalam pelayar ini untuk ulasan seterusnya.'` | `'Save my name and email in this browser for the next time I comment.'` |
  | 99      | `'Medan diperlukan ditanda dengan'`                        | `'Required fields are marked with'`                                  |
  | 134     | `'%1$s pada %2$s'`                                         | `'%1$s at %2$s'`                                                     |
  | 142     | `'Ulasan anda sedang menunggu tapisan.'`                   | `'Your comment is awaiting moderation.'`                             |
  | 153     | `'Balas'`                                                  | `'Reply'`                                                            |
  | 155     | `'Sunting'`                                                | `'Edit'`                                                             |

  The Malay strings have been re-homed as `msgstr` translations in `languages/ms_MY.po`,
  where they belong.

### Changed (translation files)
- `languages/book-wp.pot` **regenerated from source** at 3.5.0.  Old `.pot`
  header read `Project-Id-Version: Sasterawan 3.3.56` — it had been stale for
  five minor versions.  New `.pot` covers all 361 unique `msgid` calls in
  PHP + JS source (`__()`, `_e()`, `esc_html__()`/`_e()`, `esc_attr__()`/`_e()`,
  `_x()`, `_ex()`, `esc_html_x()`, `esc_attr_x()`, `_n()`, `_nx()` and their
  JS equivalents).  Entries are sorted alphabetically by `msgctxt` + `msgid`
  and carry `#:` source reference comments.
- 62 new `msgid` entries added (strings landed in 3.4.x that were never
  templated).
- 5 stale entries removed from the old `.pot` (msgids no longer in source).
- Plural forms are now correctly declared with `Plural-Forms:
  nplurals=2; plural=(n != 1);` in the `.pot` and per-locale plural rules
  in each `.po`.
- `languages/ms_MY.po`, `languages/es_ES.po`, `languages/fr_FR.po`
  resynchronised against the new `.pot`.  Existing translations preserved
  on a key-by-key basis (msgid + msgctxt match).
- `languages/ms_MY.po`: the 14 ex-Malay-msgid strings from `comments.php`
  are now populated as proper `msgid → msgstr` pairs (English → Malay).
  301 existing `ms_MY` translations preserved; 46 new strings are
  untranslated (empty `msgstr`).

### Changed
- Developer documentation moved into `docs/`: `readme.md`, `upgrading.md`,
  `changelog.md` (this file). `readme.txt` remains at the theme root per
  WordPress.org convention.
- Version unified at **3.5.0** across every source of truth:
  `style.css` (theme header), `package.json`, `readme.txt`, `docs/readme.md`,
  `docs/upgrading.md`, and this changelog. Resolves prior drift —
  `package.json` was at 3.4.42, `readme.txt`/`readme.md`/`upgrading.md` at
  3.4.92, while `style.css` had advanced to 3.4.99.
- `theme.json` FSE-migration pointer updated from `upgrading.md` to
  `docs/upgrading.md`.
- `phpunit.xml` moved from theme root to `config/` (alongside
  `lighthouserc.json`). Internal paths rewritten to be relative to the new
  location: `bootstrap="../tests/bootstrap.php"`, `<directory>../tests</directory>`.
  Run tests with `phpunit -c config/phpunit.xml`. The `tests/` directory is
  unchanged.

### Added
- Developer documentation moved into `docs/`: `readme.md`, `upgrading.md`,
  `changelog.md` (this file). `readme.txt` remains at the theme root per
  WordPress.org convention.
- Version unified at **3.5.0** across every source of truth:
  `style.css` (theme header), `package.json`, `readme.txt`, `docs/readme.md`,
  `docs/upgrading.md`, and this changelog. Resolves prior drift —
  `package.json` was at 3.4.42, `readme.txt`/`readme.md`/`upgrading.md` at
  3.4.92, while `style.css` had advanced to 3.4.99.
- `theme.json` FSE-migration pointer updated from `upgrading.md` to
  `docs/upgrading.md`.
- `phpunit.xml` moved from theme root to `config/` (alongside
  `lighthouserc.json`). Internal paths rewritten to be relative to the new
  location: `bootstrap="../tests/bootstrap.php"`, `<directory>../tests</directory>`.
  Run tests with `phpunit -c config/phpunit.xml`. The `tests/` directory is
  unchanged.

### Added
- `docs/ssot.md` — Single Source of Truth. Names the file-of-record for every
  versioned or canonical fact in the theme (version, taxonomy slug, text
  domain, requirements, doc inventory) and lists hard invariants that future
  edits must preserve.

### Accessibility & mobile polish
- `.bwp-comment-submit` ([assets/css/frontend.css:1908](assets/css/frontend.css))
  now declares `min-height: 44px; min-width: 44px;` and `justify-content:
  center;`, raising its computed tap target from ~26 px to the WCAG 2.5.5
  AAA target (44 px).  The 3.4.61 mobile audit fixed primary nav but missed
  this button.
- `.bwp-social__link, .bwp-social__toggle` ([assets/css/frontend.css:2040](assets/css/frontend.css))
  gain `min-width: 24px; min-height: 24px;` as a defensive floor.  At
  default footer font the 2em sizing already computes to ~32 px; the
  explicit minimum prevents inherited-font-size regressions from dropping
  below WCAG 2.5.8 (24 px).
- `.bwp-cf-row` and the comment-reply indentation block both switched their
  mobile breakpoint from `max-width: 540px` to `max-width: 36em` (~576 px),
  matching the rest of `frontend.css`.  The 540–575 px gap where the
  comment form stayed two-column but the nav had already wrapped is gone.
- `assets/css/forms/ratapan.css` gains a `@media (max-width: 36em)` block
  that shrinks the Bible-style chapter numeral from 4.2em (~67 px) to 2.6em
  on phones, restoring legible verse-text wrap on 360 px viewports.

### Notes
- Two code fixes this release: `comments.php` msgid bug and the mobile
  polish above.  No other PHP, JS, CSS, asset, option, schema, or
  runtime-behavioural changes.
- `BOOK_WP_VERSION` continues to read dynamically from `style.css` via
  `wp_get_theme()`, so the constant follows automatically.
- In-source `@since 3.4.x` markers (in `inc/`, `assets/js/`) are historical
  records of when features landed and are intentionally left untouched.

### Binaries rebuilt

- `languages/ms_MY.mo` — 315 strings (301 carried over + 14 fixed
  `comments.php` strings now resolving through the new English `msgid`s).
- `languages/es_ES.mo` — 299 strings.
- `languages/fr_FR.mo` — 299 strings.

All `.mo` files written with the GNU MO format (magic `0x950412de`, no hash
table, originals sorted ascending for binary search) and round-trip-verified
against the `.po` source.

If you regenerate from a real toolchain later (`wp i18n make-mo languages/`
or Poedit save), the output should be byte-equivalent in content, though
the table ordering may differ — either is valid.

---

## [3.4.99] — Exclude Hidden-Tagged Posts from Dashboard Stats

### Fixed
- `inc/admin/dashboard-widget.php`: the `get_posts()` query now passes
  `tag__not_in` with the IDs from `book_wp_hidden_tag_ids()`, so posts
  carrying any hidden tag are excluded from all dashboard statistics —
  total works, total word count, averages, works-by-form breakdown,
  and the longest/shortest extremes.  This is consistent with how hidden
  posts are already excluded from the TOC, archives, search results, and
  related works on the frontend.
- The transient cache is now also busted when the `book_wp_hidden_tags`
  option is updated (via `update_option_book_wp_hidden_tags`), so changing
  which tags are hidden immediately refreshes the dashboard numbers.

## [3.4.98] — Dashboard Widget Label Truncation Fix

### Fixed
- `assets/css/dashboard-widget.css`: headline metric labels (Works, Total
  Words, Avg. Words, Avg. Read Time) were truncated with `text-overflow:
  ellipsis` when translations exceeded the column width — Malay labels
  like "JUMLAH PERKATAAN" and "PURATA MASA BACA" were clipped.  Replaced
  `white-space: nowrap; overflow: hidden; text-overflow: ellipsis` with
  `line-height: 1.35; text-wrap: balance` so labels wrap gracefully.
  Form badge column changed from a fixed `5.5rem` to `auto` so translated
  badge labels are never clipped either.

## [3.4.97] — Reading Statistics Dashboard Widget

### Added
- `inc/admin/dashboard-widget.php`: new WordPress admin dashboard widget
  displaying aggregate reading statistics — total works, total word count,
  average words per work, and average reading time.  Below the headline
  metrics, a "Works by Form" section shows each literary form with a colour-
  coded badge, a proportional bar, work count, and word total.  A "Longest /
  Shortest" section links directly to the extreme works.  Data is computed
  from all published posts and cached in a transient for 12 hours;  the
  transient is automatically busted on post save, trash, and untrash.
- `assets/css/dashboard-widget.css`: dedicated stylesheet for the widget,
  scoped to `.bwp-dash`, inheriting WP admin theme colour tokens.  Includes
  per-form badge colours matching the frontend palette, responsive 2×2
  fallback for narrow dashboard columns, and tabular-nums for alignment.
- `assets/js/dashboard-widget.js`: lightweight script that animates the
  form-distribution bars from 0 to their target width on first viewport
  intersection (IntersectionObserver with graceful fallback).
- `functions.php`: includes the new dashboard widget file in the admin-only
  block.
- `languages/book-wp.pot`, `ms_MY.po`, `fr_FR.po`, `es_ES.po`: 13 new
  translatable strings added and translated; `.mo` files recompiled.

## [3.4.96] — Full i18n Sweep of Theme Settings

### Changed
- `inc/admin/settings.php`: every bare English string across all six settings
  tabs (General, Typography, Literary, Reading, Advanced, Social) is now
  wrapped in `esc_html_e()`, `__()`, or `wp_kses( __(), … )` with the
  `book-wp` text domain. This covers 142 translatable strings including
  section labels, field labels, hints, descriptions, `<option>` text,
  sublabels, info box paragraphs, shortcode documentation, and social
  group headings.
- `languages/book-wp.pot`: 90 new translatable strings appended.
- `languages/ms_MY.po` (354 entries), `fr_FR.po` (292 entries),
  `es_ES.po` (292 entries): all new strings translated; `.mo` files
  recompiled.

## [3.4.95] — Poetry Options CSS Fix + i18n

### Fixed
- `assets/css/frontend.css`: Poetry line-height, stanza-spacing, alignment,
  and stanza-numbering selectors used **descendant** combinators (space) instead
  of **compound** selectors (no space). Because both `bwp-stanza-*` /
  `bwp-poetry-lh-*` and `bwp-form-*` live on the same `<body>` element,
  `.bwp-stanza-tight .bwp-form-puisi` never matched — the browser looked for
  `.bwp-form-puisi` *inside* `.bwp-stanza-tight` instead of on the same node.
  All affected selectors are now compound (e.g. `.bwp-stanza-tight.bwp-form-puisi`).
  This restores Stanza Spacing, Verse Line Height, Poetry Alignment, and
  Stanza Numbering functionality on the frontend.

### Changed
- `inc/admin/settings.php`: all bare strings in the Typography tab's
  Text Options and Poetry Options sections are now wrapped in
  `esc_html_e()` / `esc_html__()` with the `book-wp` text domain — labels,
  descriptions, hints, and `<option>` text for Stanza Spacing, Verse Line
  Height, Poetry Alignment, Drop Cap Per Form, and Drop Cap Font.
- `languages/book-wp.pot`: new translatable strings appended.
- `languages/ms_MY.po`, `fr_FR.po`, `es_ES.po`: Malay, French, and Spanish
  translations added for all new strings; `.mo` files recompiled.

## [3.4.94] — Additional Drop Cap Fonts

### Added
- `assets/fonts/dropcap/liturgisch.woff2` — ecclesiastical drop cap font.
- `assets/fonts/dropcap/rediviva.woff2` — ornamental drop cap font.
- `assets/fonts/dropcap/gotische.woff2` — gothic drop cap font.
- `assets/css/dropcap/liturgisch.css` — `@font-face` + `--bwp-dropcap-font`
  override for Liturgisch.
- `assets/css/dropcap/rediviva.css` — `@font-face` + `--bwp-dropcap-font`
  override for Rediviva.
- `assets/css/dropcap/gotische.css` — `@font-face` + `--bwp-dropcap-font`
  override for Gotische.

### Changed
- `inc/admin/settings.php`: Typography tab visual picker now offers six drop
  cap fonts (Default, Camelot, Romantik, Liturgisch, Rediviva, Gotische);
  admin enqueue loads all five custom font stylesheets for live previews.
- `inc/setup/enqueue.php`: frontend whitelist extended with the three new slugs.

## [3.4.93] — Drop Cap Font Selection

### Added
- **Typography → Drop Cap Font**: new visual picker in the Typography tab lets
  authors choose a decorative typeface for the `::first-letter` drop cap across
  all prose forms.  Ships with two bundled fonts — **Camelot** (decorative)
  and **Romantik** (calligraphic) — alongside the "Default" option that
  preserves each form's built-in font (Cinzel, IM Fell English SC, etc.).
- `assets/fonts/dropcap/camelot.woff2` — bundled decorative drop cap font.
- `assets/fonts/dropcap/romantik.woff2` — bundled calligraphic drop cap font.
- `assets/css/dropcap/camelot.css` — `@font-face` + `--bwp-dropcap-font`
  override for Camelot.
- `assets/css/dropcap/romantik.css` — `@font-face` + `--bwp-dropcap-font`
  override for Romantik.

### Changed
- `assets/css/frontend.css`: base `::first-letter` rule now includes
  `font-family: var(--bwp-dropcap-font, inherit)` so the variable propagates
  to the force-on override path.
- `assets/css/forms/cerpen.css`, `novel.css`, `hikayat.css`, `memoir.css`,
  `nukilan.css`: each form's `::first-letter` `font-family` now reads
  `var(--bwp-dropcap-font, <original>)` — when the variable is unset
  (i.e. "Default"), the form's built-in font applies as before.
- `inc/setup/enqueue.php`: conditionally loads the selected dropcap font CSS
  on the frontend; skips Google Fonts requests for IM Fell English SC / Libre
  Caslon Display when a custom drop cap font is active (saves an HTTP request).
- `inc/admin/settings.php`: registers `book_wp_dropcap_font` setting, adds it
  to the Typography tab save handler, renders the visual picker UI, and loads
  both dropcap font stylesheets on the admin settings page for live previews.

## [3.4.92] — Exclude hidden-tagged posts from RankMath HTML Sitemap

### Fixed
- `inc/hooks/actions.php`: Hidden-tagged posts appeared in the RankMath HTML
  Sitemap because RankMath's HTML sitemap module runs its own `WP_Query`,
  bypassing both `pre_get_posts` (used for main queries) and the core XML
  sitemap filter (`wp_sitemaps_posts_query_args`). Added
  `book_wp_hide_from_rankmath_html_sitemap()` on the
  `rank_math/sitemap/html_sitemap/post_args` filter, applying the same
  `tax_query NOT IN` pattern used by the existing exclusion functions.

## [3.4.91] — Paper/Ink audit: fix scheme/time conflicts across all literary forms

### Fixed
- `assets/css/frontend.css`: Manual night toggle (`html.bwp-night body`,
  `body.bwp-night`) and `@media (prefers-color-scheme: dark)` fallback were
  missing `--bwp-teal`, `--bwp-teal-deep`, `--bwp-gold`, `--bwp-gold-dim`,
  and `--bwp-gold-light`. Novel drop caps and esei headings (both using
  `--bwp-teal`) were invisible on dark backgrounds. All gold ornamental
  elements rendered at insufficient contrast. Added all five tokens to both
  blocks.

- `assets/css/times/times.css`: `--bwp-teal` in the `bwp-time-night` ink
  block was #0c1e32 (near-black on dark background — invisible). Changed
  to #4a8ab0. Added missing `--bwp-stone`, `--bwp-gold-dim`, `--bwp-gold-light`
  to the same block for complete night token coverage.

- `assets/css/schemes/night.css`: Night scheme base was missing `--bwp-gold-dim`
  and `--bwp-gold-light`. Added to the base block and all four compound
  selectors (dawn, morning, dusk, night) with mood-appropriate values.

- `assets/css/forms/hikayat.css`: Day palette set both surface AND ink tokens
  at specificity 0,6,1, which overwhelmed Cream/Dusk scheme selectors (max
  0,3,1) and flattened time-of-day ink mood. Refactored to surface-only
  (`--bwp-bg`, `--bwp-bg-subtle`, `--bwp-border`, `--bwp-border-faint`,
  `--bwp-gold-dim`); ink now inherits from the time system. Added
  `:not(.bwp-scheme-cream):not(.bwp-scheme-dusk)` guards. Night block reduced
  to form-specific accent (`--bwp-gold-dim`) only.

- `assets/css/forms/ratapan.css`: Same refactor as hikayat — removed ink
  tokens (`--bwp-text`, `--bwp-text-mid`, `--bwp-muted`, `--bwp-link`,
  `--bwp-link-hover`) from day palette, added scheme guards. Night block
  reduced to form-specific accents only. Hardcoded progress bar colour
  (`background: #6a6870`) replaced with `var(--bwp-progress)`.

- `assets/css/forms/memoir.css`: Same refactor — removed `--bwp-text` and
  `--bwp-text-mid` from day palette, added scheme guards. Night override
  block removed entirely (no form-specific accents needed).

- `assets/css/literary.css`: Thesis (`.bwp-colophon__thesis`) night override
  only matched `body.bwp-night`; extended to `html.bwp-night`,
  `:root.bwp-time-night`, and `body.bwp-scheme-night`. Added night override
  for `.bwp-colophon__karmina` which had no dark-mode coverage.

- `assets/js/time-theme.js`: Time-of-day period was frozen at page load.
  Returning to a long-lived tab showed the wrong ink (e.g. afternoon palette
  at 9 PM). Added `visibilitychange` listener that re-evaluates the period
  when the tab regains focus, plus an hourly `setInterval` fallback for
  foreground tabs that cross a period boundary. Both are skipped when the
  admin has pinned a specific period (non-auto mode).

- `assets/js/time-theme.js`, `assets/js/frontend.js`, `header.php`:
  Night toggle (floating moon button) persisted indefinitely in localStorage.
  Toggling dark mode at 10 PM kept it active at 2 PM the next day because
  no expiry logic existed. The time engine now auto-expires the manual toggle
  when: (a) the current clock period is daytime (dawn, morning, afternoon, or
  dusk), AND (b) the toggle was activated on a previous calendar date or has
  no date stamp (legacy entries from before v3.4.91). Same-day toggles are
  honoured — toggling at 1 AM stays active until the next daytime visit on a
  new date. `frontend.js` now stores `bwp_night_date` alongside
  `bwp_night_mode` in localStorage. The preload script in `header.php` runs
  AFTER `time-theme.js` (which is enqueued in `wp_head()`), so expired
  toggles are cleared before the preload reads localStorage — no flash of
  dark mode on stale preferences. Header comment updated to document this
  execution order dependency.

## [3.4.90] — Fix Ratapan/Hikayat/Memoir invisible text in night mode (correct fix)

### Fixed
- `assets/css/forms/ratapan.css`, `hikayat.css`, `memoir.css`: v3.4.89 added
  `html.bwp-night` to the night override block but the day palette block still
  fired and won due to higher CSS specificity — (0,5,1) vs (0,2,2). The day
  palette selector used `:root:not(.bwp-time-night)` with `:not(.bwp-night)`
  only on `body`; since the JS preload toggle writes `bwp-night` to `html`
  (= `:root`), not `body`, the guard on `body` passed and the high-specificity
  day rule stamped `--bwp-text: #1a1a1a` (near-black) over the dark background.
  Correct fix: add `:not(.bwp-night)` to the `:root` portion of the day palette
  selector — `:root:not(.bwp-time-night):not(.bwp-night)` — so the rule does
  not fire at all when `html.bwp-night` is active.

## [3.4.90] — Fix invisible text on Ratapan (and Hikayat, Memoir) in night mode

### Fixed
- `assets/css/forms/ratapan.css`, `hikayat.css`, `memoir.css`: All three forms
  define a day palette that explicitly sets `--bwp-text` to a near-black value.
  The palette selector used `:not(.bwp-night)` on `body` to exclude night mode —
  but the JS preload path writes the `bwp-night` class to `<html>`, not `body`.
  The guard therefore passed, the day palette fired, and dark ink was stamped over
  the dark night background, making all content invisible.
  Fix: added `html.bwp-night body.bwp-form-{slug}` to each night override block;
  added `--bwp-text` and `--bwp-text-mid` (light values) to each night block;
  added `@media (prefers-color-scheme: dark)` blocks for OS-level dark mode.
  Ratapan was the reported case; Hikayat and Memoir had the identical structural gap.

## [3.4.90] — Correct heading hierarchy (H1→H2→H3→H4)

### Changed
- `header.php`: Author name promoted from `<p>` to `<h2 class="bwp-site-header__title">`.
  It is the consistent H2 landmark across every page type — every view now has
  H1 (content title) → H2 (author name) as its top two outline levels.
- `index.php`: "Table of Contents" demoted from `<h2>` to `<h3 class="bwp-toc-header">`,
  yielding the correct homepage outline:
  H1 (anthology title) → H2 (author name) → H3 (TOC) → H4 (TOC group headings).
- `index.php`: All six TOC group headings (`<h3 class="bwp-toc-group__heading">`)
  demoted to `<h4>`. CSS is unaffected — all rules target class names, not element tags.

## [3.4.90] — Fix double H1 on homepage

### Fixed
- `header.php`: The author name in the site header was conditionally rendered
  as `<h1>` when `is_front_page() && is_home()` was true (blog-posts front page),
  and as `<p>` on all other pages. On the homepage this produced two H1 tags:
  the author name from the header and the anthology title from `index.php`.
  The author name is a site-identity and navigation element, not a content
  heading. It is now unconditionally rendered as `<p class="bwp-site-header__title">`
  on every page view. Each page template retains its own single content-level
  `<h1>` (anthology title, post title, archive title, etc.). Every frontend
  page type now has exactly one H1.

## [3.4.90] — Fix hidden-tag body class leak, stale inLanguage, TOC cache eviction

### Fixed
- `inc/hooks/filters.php`: `book_wp_body_classes()` was adding `bwp-tag-{slug}` body
  classes for all tags on a post, including hidden ones. Hidden tag slugs were therefore
  visible in the page HTML source even though the tags are suppressed everywhere in the UI.
  The loop now skips any tag whose slug is in `book_wp_hidden_tag_slugs()`.
- `inc/hooks/actions.php`: `book_wp_structured_data()` was reading the removed
  `book_wp_language` option (deleted in v3.2.3) to determine the `inLanguage` field in
  the JSON-LD schema block. The option is absent from all current installs so
  `get_option()` always returned the fallback `'en'`, causing every post to output
  `"inLanguage": "en"` regardless of the WordPress site locale. Replaced with
  `substr( get_locale(), 0, 2 )`, consistent with how the language body class is derived
  in `book_wp_body_classes()`.
- `inc/hooks/actions.php`: `book_wp_clear_toc_cache()` called `delete_transient()` but
  not `wp_cache_delete()`. On sites with a persistent object cache (Redis, Memcached),
  the in-memory copy of `book_wp_toc_posts` could survive for up to 12 hours after
  invalidation, causing the TOC to show stale content after a post was published,
  trashed, or had its tags changed. Added `wp_cache_delete( 'book_wp_toc_posts',
  'transient' )` — the same belt-and-suspenders pattern applied to
  `book_wp_form_slugs` in v3.4.82.

## [3.4.90] — Fix ORCID and WorldCat icons

### Fixed
- `inc/helpers/social.php`: Replaced the broken ORCID icon (traced JPEG which
  incorrectly painted white over the entire disc) with the canonical official
  ORCID SVG path data (viewBox="0 0 256 256"). Disc fills via currentColor;
  i-stem, D-shape, and i-dot are white `<path>` elements inside a `<g
  fill="white">`.
- `inc/helpers/social.php`: Replaced the broken WorldCat icon (traced the
  rounded-rect background instead of the arcs, producing an inverted shape)
  with a corrected trace. Pipeline: remove black app-icon corners → Gaussian
  blur (0x2) → fuzz-transparent white background → alpha-extract → threshold
  → morphological close/open → Bilevel BMP → potrace. Result: 5 clean paths
  describing the four-arc pinwheel silhouette. viewBox="0 0 747.77 724.67".

## [3.4.90] — Fix ORCID and WorldCat icon rendering

### Fixed
- `inc/helpers/social.php`: Both the ORCID and WorldCat SVG icons were
  invisible. The potrace SVG format wraps all path data in a
  `<g transform="translate(tx,ty) scale(0.1,-0.1)">` group that scales
  coordinates from potrace's internal 1/10 pt units and flips the Y axis.
  The previous builds extracted only the raw `d` attributes and discarded
  this transform, leaving paths in the wrong coordinate space (10× too large
  and upside down). Both icons are now rebuilt with the group transform
  preserved, so paths render correctly within their declared viewBox.

## [3.4.84] — Replace WorldCat icon with traced logo

### Changed
- `inc/helpers/social.php`: Replaced the placeholder WorldCat SVG (24×24
  stroke outline) with the official WorldCat logo traced via potrace from the
  uploaded PNG. The four coloured arcs are unified into a single monochrome
  silhouette path (fill="currentColor"). viewBox="0 0 1023.73 1023.33".

## [3.4.83] — Replace ORCID icon with official filled logo

### Changed
- `inc/helpers/social.php`: Replaced the monochrome outline ORCID SVG with the
  official filled iD logo. The new icon uses a two-layer composite: a
  `<circle>` filled with `currentColor` (the green disc) and two `<path>`
  elements filled with `white` (the iD letterforms traced via potrace from the
  official ORCID PNG). This renders at any size and responds to colour-scheme
  changes via `currentColor` while preserving the recognisable green-disc
  appearance. viewBox="0 0 537.10 536.93" from a 543×543 centre-crop of the
  official logo (no distortion).

## [3.4.82] — Permalink contingencies and share hashtag caching

### Added
- `inc/helpers/permalink.php`: `pre_delete_term` hook snapshots all published
  post IDs assigned to a literary_form term before it is deleted, storing a
  `slug → post_name → post_id` map in a 7-day transient
  (`book_wp_deleted_form_slugs`). A new `template_redirect` handler reads this
  snapshot on 404 responses and sends a 301 to the post's current canonical
  permalink, rescuing any bookmarked or indexed `/{deleted-form}/{post-slug}/`
  URLs for up to 7 days after the term is removed.

### Fixed
- `inc/helpers/permalink.php`: `preg_quote()` calls in the rewrite rule
  alternation now pass `'|'` as the delimiter argument. WordPress wraps rewrite
  rule patterns with `|` as the `preg_match` delimiter; without the delimiter
  argument, a literal `|` in a form slug would not be escaped and would corrupt
  the alternation. No current slugs contain `|` but the fix closes the latent
  risk.
- `inc/helpers/permalink.php`: `book_wp_invalidate_form_slugs_transient()` now
  also calls `wp_cache_delete( 'book_wp_form_slugs', 'transient' )` after
  `delete_transient()`. On sites with a persistent object cache (Redis,
  Memcached) that does not immediately honour `delete_transient()`, this
  ensures the stale slug list is evicted from the in-memory cache before the
  next rewrite rule registration pass.
- `inc/helpers/navigation.php`: `book_wp_share_hashtags()` now uses a static
  `$cache` array keyed by post ID. All three return paths (no tags, all hidden,
  success) populate the cache, so repeated calls within the same request return
  immediately without a second `get_the_tags()` database query.

## [3.4.81] — Fix rewrite rule inspector (v3.4.80 regression)

### Fixed
- `inc/admin/settings.php`: Rewrite rule inspector was always reporting
  "0 rules found" because it read from `$wp_rewrite->rules`, which is not
  populated in the WordPress admin context. Changed to read from
  `get_option('rewrite_rules')` — the database option that stores the
  flushed rules and is available everywhere.
- `inc/admin/settings.php`: "Flush now" link was rendering as `â†'` due
  to PHP octal escape bytes that were not interpolated. Replaced with the
  HTML entity `&rarr;`.

## [3.4.80] — Rewrite rule inspector in Advanced tab

### Added
- `inc/admin/settings.php`: Rewrite rule inspector panel in the Permalinks
  section of the Advanced tab. Only visible when Literary Form Permalinks is
  enabled. Reads live from `$wp_rewrite->rules`, filters for entries matching
  any registered form slug, and shows:
  - A colour-coded status badge: green (≥3 rules registered, routing active)
    or amber (0 rules found or fewer than expected, flush required).
  - A direct "Flush now →" link to Settings › Permalinks when the status is
    amber.
  - A collapsible table listing each matched regex pattern and its
    `index.php?…` resolution target.

## [3.4.79] — Replace ORCID icon with traced logo

### Changed
- `inc/helpers/social.php`: Replaced the old geometric ORCID SVG (viewBox 24×24,
  single-path approximation) with a new icon traced from the uploaded ORCID logo
  PNG via potrace. Uses viewBox="0 0 9736 9735" with four paths: outer ring,
  i-dot, i-stem, and D letterform — matching the official ORCID iD logotype.

## [3.4.78] — Hashtag support in share row

### Added
- `inc/helpers/navigation.php`: New `book_wp_share_hashtags()` helper. Reads
  the current post's public tags, excludes any whose slugs are in
  `book_wp_hidden_tag_slugs()`, and caps the result at 3. Returns three keys:
  `x` (comma-joined slugs, hyphens stripped for X's tag-terminator rule),
  `inline` (rawurlencoded `#slug` token string for text-field platforms), and
  `empty` (bool).
- X: `&hashtags=` parameter appended to the intent URL when public tags exist.
- Threads: `$share_text_tagged` (base share text + inline tags) used in place
  of `$share_text`.
- Mastodon: same `$share_text_tagged` used in the toot.kytta.dev text param.

### Unchanged
- Facebook (`sharer.php` accepts URL only; no hashtag mechanism).
- WhatsApp (`wa.me` plain-text messaging; `#` is not a hyperlink).
- Email (`mailto:` has no hashtag concept).
- Copy link button (copies permalink only, unaffected).
- Pinterest (excluded: visual-search-first platform, description field not
  the primary discovery path for literary content).

## [3.4.78] — Wire up poetry typography dropdowns

### Fixed
- `inc/hooks/filters.php`: `book_wp_poetry_align` option was saved but never
  consumed. Added body-class injection: `bwp-poetry-centred` is added when the
  option value is `center`. Stanza spacing and verse line height were already
  wired; comment updated to reflect all three.
- `assets/css/frontend.css`: Added `/* Poetry alignment */` block — four
  poetry-form selectors targeting `.bwp-poetry-centred` set `text-align:center`
  on `.bwp-work__content`. Left-aligned is the default (no class needed).

## [3.4.77] — Add Mastodon to Social Profiles panel

### Added
- `inc/helpers/social.php`: Added `mastodon` to `book_wp_social_platforms()`
  (after Threads) and added the corresponding SVG icon to `book_wp_social_icon()`
  using the same traced path already in use for the share row.
- `inc/admin/settings.php`: Registered `book_wp_social_mastodon` with
  `esc_url_raw` in both the global sanitisation map and the per-tab social map.
  The Social tab UI renders dynamically from `book_wp_social_platforms()` and
  required no further changes.

## [3.4.76] — Replace Mastodon share icon

### Changed
- `inc/helpers/navigation.php`: Replaced the old Mastodon SVG (74×79 viewBox,
  two-path geometric glyph) with a new icon traced from the uploaded PNG via
  potrace. Uses `viewBox="0 0 11000 12000"` with a single monochrome path
  matching the official Mastodon logotype mark.

## [3.4.75] — MUST FIX: Performance, cache, analytics, dark-mode, escaping

### Fixed (MUST-1) TOC hidden-tag N+1 query — `index.php`
- Replaced per-post `wp_get_post_tags()` calls inside `array_filter()` with a
  single batched `wp_get_object_terms()` retrieval for all TOC post IDs at once.
  Builds an `object_id => slug[]` map, then filters the post list against that
  map in PHP. Zero per-post term queries on any TOC page load regardless of
  post count. Follows the same pattern already used in `navigation.php`.
- Also fixed the secondary N+1 in the "Group by Tag" path: that loop now uses
  its own `wp_get_object_terms()` batch (`$grouping_terms_map`) instead of a
  per-post `wp_get_post_tags()` call for tag label resolution.

### Fixed (MUST-2) Cache invalidation incomplete — `inc/hooks/actions.php`
- Replaced the minimal `save_post` + `trashed_post` + `untrashed_post` hooks
  with a full six-event invalidation matrix:
  1. `save_post` — now guarded against autosave and revisions
  2. `transition_post_status` — fires on publish/unpublish transitions
  3. `trashed_post` / `untrashed_post` — explicit UI trash actions
  4. `set_object_terms` — tag assignment changes (hidden-tag visibility)
  5. `edited_term` — tag slug edits (hidden-tag slug lookup must rebuild)
  6. `updated_option` — TOC-relevant settings: hidden_tags, toc_grouping,
     toc_pagination, toc_layout, archive_pagination, show_badges
- Removed the now-superseded duplicate `updated_option` handler for
  `book_wp_hidden_tags` that previously existed separately in the file.

### Fixed (MUST-3) Analytics text extracted twice — `inc/helpers/analytics.php`
- Added a static per-request cache to `book_wp_get_full_text()`. When
  `book_wp_analytics_bar()` calls both `book_wp_reading_time()` and
  `book_wp_word_count()` in sequence, the second call returns the cached
  result immediately instead of repeating the regex + `wp_strip_all_tags()`
  pass over the full post content.

### Fixed (MUST-4) Dark-mode first-visit flash — `header.php`, `frontend.css`, `frontend.js`
- Added a synchronous inline `<script>` in `<head>` (before first paint) that
  reads `localStorage` and the `prefers-color-scheme` media query, then adds
  `.bwp-night` to `<html>` immediately — eliminating the flash-of-light-theme
  that occurred when the class was applied only on `DOMContentLoaded`.
- Added `html.bwp-night body` CSS selector alongside `body.bwp-night` so the
  night variables apply from the `<html>` class set by the preload script.
- Added `@media (prefers-color-scheme: dark)` CSS fallback block for no-JS
  environments, guarded by `html:not([data-bwp-night="0"])` to defer to an
  explicit user preference when one exists.
- Updated `frontend.js` night-toggle block to: sync body class from html class
  on init; write `data-bwp-night` attribute for the CSS fallback guard; keep
  both `html` and `body` classes in sync on each toggle click.
- Preload script is gated by `book_wp_night_toggle` option — not injected when
  the night toggle is disabled in Theme Options.

### Fixed (MUST-5) Output escaping audit — `inc/helpers/permalink.php`
- Applied `esc_url_raw()` to both `wp_redirect()` call targets (bare form-slug
  redirect to `home_url('/')` and legacy post-slug redirect to `$canonical`).
  `wp_redirect()` docs recommend `esc_url_raw()` rather than `esc_url()` for
  redirect targets since `esc_url()` strips characters valid in URLs.
- All other render paths audited: templates, helpers, meta-boxes, share row,
  JSON feed, and shortcodes confirmed to use correct escaping at output.

## [3.4.74] — Fix empty gap when Prev/Next navigation is disabled

### Fixed
- `single.php`: When Previous/Next Navigation is disabled in Theme Options (or
  when there are no adjacent posts), a `bwp-no-work-nav` body class is now
  added via an inline `add_filter( 'body_class' )`. This allows CSS to reduce
  the top spacing on the comments section, eliminating the large empty gap that
  previously appeared between the last article element and "TINGGALKAN ULASAN".
- `assets/css/frontend.css`: Reduced `.bwp-comments` base `margin-top` from
  `5em` to `3em` (the nav itself contributes visual separation when present).
  Added `body.bwp-no-work-nav .bwp-comments { margin-top: 2em }` to further
  tighten the gap when nav is absent.

## [3.4.73] — Share row: copy button visible feedback restored

### Fixed
- `assets/js/frontend.js`: `markCopied()` now injects a floating
  `<span class="bwp-copy-tip">` tooltip above the copy button containing the
  "Copied!" confirmation text. The tooltip fades in and is removed from the DOM
  after 2 seconds. Previously the only feedback was a border/colour class change
  which was invisible after label spans were removed in v3.4.70.
- `assets/css/frontend.css`: Added `.bwp-copy-tip` and `.bwp-copy-tip--visible`
  styles — absolute positioned above the button, `background: var(--bwp-ink)`,
  `color: var(--bwp-paper)`, fade-in via opacity + translateY transition.
  Added `position: relative` to `.bwp-share__btn` so the tooltip anchors
  correctly.

## [3.4.72] — Share row: Pinterest icon replaced with traced logo

### Changed
- `inc/helpers/navigation.php`: Pinterest share button now uses an SVG path
  traced from the official Pinterest logo (JPG provided by author). The red
  circle background was isolated via green-channel separation so only the
  white P letterform is traced, rendered with `fill="currentColor"` — the icon
  inherits the button colour and responds to hover and night mode correctly.

## [3.4.71] — Share row: Threads icon replaced with traced logo

### Changed
- `inc/helpers/navigation.php`: Threads share button now uses an SVG path
  traced from the official Threads logo (PNG provided by author) via potrace.
  Replaces the approximate hand-coded path used in v3.4.70. Rendered inline
  at 16×16 px with `fill="currentColor"` for full theme colour compatibility.

## [3.4.71] — Share row: copy fix, Telegram + LinkedIn, remove Mastodon

### Fixed
- `assets/js/frontend.js`: `markCopied()` was querying `.bwp-share__btn-label`
  which no longer exists after the icon-only change in v3.4.70. Rewritten to
  use `aria-label` swap for screen-reader feedback and the existing
  `bwp-share__copy--copied` CSS class for visual feedback. Copy button now
  works correctly.

### Added
- `inc/helpers/navigation.php`: Telegram share button
  (`t.me/share/url?url=…&text=…`).
- `inc/helpers/navigation.php`: LinkedIn share button
  (`linkedin.com/sharing/share-offsite/?url=…`).

### Removed
- `inc/helpers/navigation.php`: Mastodon share button removed.

## [3.4.70] — Share row: icon-only + Threads, Pinterest, Mastodon, Email

### Added
- `inc/helpers/navigation.php`: Four new share targets added to
  `book_wp_share_row()`: Threads (`threads.net/intent/post`), Pinterest
  (`pinterest.com/pin/create/button`), Mastodon (via `toot.kytta.dev` universal
  redirector — prompts user for their instance, no API key required), and Email
  (`mailto:` link with subject + body). All use no third-party SDKs.

### Changed
- `inc/helpers/navigation.php`: All `bwp-share__btn-label` spans removed from
  every share button. Buttons are now icon-only; accessibility preserved via
  `aria-label` on each element. SVG size increased from 13 px to 16 px for
  better legibility at icon-only size.
- `inc/helpers/navigation.php`: WhatsApp URL simplified to single
  `$share_text` variable (title + space + URL) shared across Threads and
  Mastodon targets.
- `assets/css/frontend.css`: `.bwp-share__btn` changed to fixed 44×44 px
  square with `justify-content: center` and `padding: 0`. Removes `gap` and
  font properties no longer needed for icon-only layout.

## [3.4.69] — Back to top: Infinite Scroll conflict guard

### Fixed
- `assets/js/frontend.js`: Back to top button no longer initialises on pages
  where Infinite Scroll is active. Scrolling to the top on an infinite-scroll
  page re-triggers InfiniteScroll's sentinel detection and causes duplicate page
  loads. Guard checks two conditions: archive/tag page (`body.bwp-is-archive`)
  with `archivePagination = 'infinite'`, and TOC flat-list page
  (`body.bwp-is-toc-flat`) with `tocPagination = 'infinite'`. When either is
  true the button remains hidden and no scroll listener is attached.

## [3.4.68] — Back to top button + keyboard stanza navigation

### Added
- `footer.php`: Back to top `<button id="bwpBackToTop">` injected before
  `.bwp-page-wrap` closes. Gated by `book_wp_back_to_top` option. Carries
  `hidden` attribute as a no-JS fallback; JS removes it at runtime.
- `assets/css/frontend.css`: `.bwp-back-to-top` — fixed bottom-right, 44×44 px
  (SC 2.5.8 compliant), opacity/translateY transition, `.is-visible` state.
  `.bwp-stanza-focus` — accent-colour outline ring for keyboard stanza focus.
- `assets/js/frontend.js`: Back to top — shows button after 400 px scroll
  (passive scroll listener, CWV H8 compliant); click uses
  `window.scrollTo({ top:0, behavior:'smooth' })`. Stanza navigation —
  `ArrowUp`/`ArrowDown` within the existing `keyboardNav` block; moves a
  `.bwp-stanza-focus` ring between `<p>` elements inside `.bwp-work__content`
  using `scrollIntoView({ behavior:'smooth', block:'center' })`. No jQuery,
  no external dependency.
- `inc/admin/settings.php`: Back to Top Button toggle in Reading tab
  (default on). Registered in sanitize whitelist.
- `inc/setup/enqueue.php`: `backToTop` key added to `bookWP` localize object.

## [3.4.67] — Literary Form Permalink legacy redirect fix

### Fixed
- `inc/helpers/permalink.php`: Rewrote `book_wp_redirect_legacy_post_slug()`.
  The previous implementation (v3.4.65–66) checked `is_404()` — but
  `/{post-slug}/` is never a 404: WordPress's own `%postname%` rewrite rule
  still resolves it to the post. The redirect therefore never fired.
  New approach: hook fires on `is_single()`, compares the current request URL
  against `get_permalink()` (which applies the `post_link` filter and returns
  the canonical `/{form-slug}/{post-slug}/` URL), and redirects 301 if they
  differ. Posts with no literary form assigned produce an identical canonical
  URL and are never redirected. Pages are excluded by `is_single()`. Query
  strings are preserved across the redirect.

## [3.4.66] — Literary Form Permalink: page/post slug differentiation

### Changed
- `inc/helpers/permalink.php`: `book_wp_redirect_legacy_post_slug()` now
  explicitly checks for a published **page** with the same slug before
  attempting a post redirect. If a page is found, the function returns without
  redirecting — pages do not participate in Literary Form Permalinks and
  WordPress resolves them independently. This guards the edge case where a page
  slug and a post slug share the same name; the page always takes precedence.

## [3.4.65] — Literary Form Permalink legacy post-slug redirect

### Added
- `inc/helpers/permalink.php`: Added `book_wp_redirect_legacy_post_slug()` on
  `template_redirect`. When Literary Form Permalinks is enabled, a visit to the
  old `/{post-slug}/` URL (standard WordPress permalink) now redirects 301 to
  the new `/{form-slug}/{post-slug}/` URL. Handles pre-existing bookmarks,
  external links, and search engine indexes pointing to old URLs.
  - Destination URL resolved via `get_permalink()`, which already applies the
    `post_link` filter — no duplicate logic.
  - Posts with no literary form assigned redirect to their standard permalink
    (no change in behaviour for unclassified posts).
  - Skips segments already matched as form slugs (handled by the bare-slug
    redirect added in v3.4.64).
  - Only fires on 404 responses with the feature enabled — zero overhead
    on all other requests.

## [3.4.64] — Literary Form Permalink bare-URL redirect

### Added
- `inc/helpers/permalink.php`: Added `book_wp_redirect_bare_form_slug()` on
  `template_redirect`. When Literary Form Permalinks is enabled, a direct visit
  to `/{form-slug}/` or `/{form-slug}` (no post slug) now redirects 301 to the
  homepage instead of returning a 404.
  - Only fires on 404 responses — no impact on any other request.
  - Handles WordPress sub-directory installs by stripping the home path prefix.
  - Guards against partial matches: only an exact single-segment match against
    a registered `literary_form` slug triggers the redirect.
  - Resolves the destination URL via `get_term_link()` so custom taxonomy
    rewrite bases are respected.

## [3.4.61] — Mobile-First Audit Patch

### Fixed
- **M-01** Nav tap targets: increased `.bwp-nav__list a` padding from `.15em 0` to `.4em 0` — meets WCAG 2.2 SC 2.5.8 24 px minimum on all screen sizes.
- **M-02** Post content overflow: added `overflow-wrap: break-word` to `.bwp-work__content` — prevents long URLs and unbroken strings causing horizontal scroll.
- **M-03** Work nav title overflow: added `overflow-wrap: break-word` to `.bwp-work-nav__title`.
- **M-04** Share row button height: `min-height` increased from `32px` to `44px` — meets WCAG SC 2.5.5 recommended 44 px touch target.
- **M-05** TOC title overflow: added `overflow-wrap: break-word` to `.bwp-toc__title`.
- **M-06** Page wrap top padding on mobile: reduced `3em` to `1.75em` at max-width 36em.
- **M-07** Book title hero padding on mobile: top `4em` to `2.5em`, bottom `3em` to `2em` at max-width 36em.
- **M-08** Site header bottom margin on mobile: `3em` to `2em` at max-width 36em.
- **M-09** Verse number (`[verse]` shortcode) horizontal overflow: reset `margin-left: -2.4em` to `0` at max-width 36em — outdent exceeded 2em page-wrap padding on 320 px viewports.
- **M-10** Istilah tooltip viewport clipping: `white-space: nowrap; left: 50%; transform: translateX(-50%)` overflows viewport at right margin on mobile — replaced with `white-space: normal; max-width: 14em; left: 0; transform: none` at max-width 36em.
- **M-11** TOC thumbnail excerpt indent: removed thumbnail-width left indent at max-width 36em.
- **M-12** Sub-menu touch accessibility: CSS `li[data-touch-open="1"] > .sub-menu` rule + touchend JS handler. First tap reveals sub-menu; second navigates. Touch-device only.

## 3.4.60 — 2026-03-08

### Literary Form Permalinks

**`inc/helpers/permalink.php` (new), `functions.php`, `inc/admin/settings.php`**

Post URLs can now follow the structure `/{literary-form-slug}/{post-slug}/` — for example `https://example.com/puisi/waktu-melata/`. The feature is opt-in: `book_wp_form_permalinks` defaults to `0`. Posts with no literary form assigned retain their existing permalink. Taxonomy archives are unaffected — they remain at `/form/{slug}/`.

**New file: `inc/helpers/permalink.php`**

`book_wp_permalink_form_slugs()` — reads all `literary_form` term slugs from a day-long transient (`book_wp_form_slugs`). The transient is invalidated on `created_term`, `edited_term`, and `delete_term` for the `literary_form` taxonomy.

`book_wp_get_form_slug( $post_id )` — returns the first `literary_form` term slug assigned to a post, or empty string. Used by the `post_link` filter and available to templates.

`book_wp_register_form_permalink_rules()` on `init` priority 20 (after taxonomy registration at priority 10). Builds a regex alternation from the live term slug list — never a wildcard — and registers three rules at `'top'` priority:
- `^({slugs})/([^/]+)/?$` → `index.php?name=$matches[2]`
- `^({slugs})/([^/]+)/comment-page-([0-9]{1,4})/?$` → adds `&cpage=`
- `^({slugs})/([^/]+)/([0-9]+)/?$` → adds `&page=` (for `<!--nextpage-->` splits)

Using a precise alternation rather than a wildcard means only known form slugs trigger these rules. WordPress pages, categories, tags, and other rewrites are not caught.

`book_wp_form_permalink()` on `post_link` filter. Replaces the generated URL with `/{form-slug}/{post-slug}/` when the option is on and the post has a form assigned. Respects `$leavename` by outputting `%postname%` when true (used by the editor and nav menu builders).

`book_wp_flush_on_permalink_toggle()` on `updated_option` — calls `flush_rewrite_rules( false )` (soft flush) immediately when `book_wp_form_permalinks` is saved.

`book_wp_flush_on_theme_switch()` on `after_switch_theme` — flushes if the option is already on, covering theme reactivation.

`book_wp_maybe_flush_rewrite_rules()` on `admin_init` — checks a `book_wp_flush_rewrite_rules` flag set by term create/edit/delete hooks and flushes on the next admin page load. Avoids calling `flush_rewrite_rules()` directly inside taxonomy hooks (which fire on `init`).

**Settings:** `book_wp_form_permalinks` toggle added to a new Permalinks section in the Advanced tab. When on, the UI shows the active form slugs and a direct link to Settings > Permalinks for the required flush.

**Upgrading note:** after enabling, visit Settings > Permalinks and click Save Changes. The `post_link` filter alone is not enough — the rewrite rules must be written to the database before WordPress can resolve the new URL structure.

---

## 3.4.59 — 2026-03-08

### JSON Feed v1.1

**`inc/helpers/json-feed.php` (new), `functions.php`, `inc/admin/settings.php`**

Publishes a JSON Feed v1.1 endpoint at `/feed/json`. The spec is [jsonfeed.org/version/1.1](https://www.jsonfeed.org/version/1.1/).

`book_wp_register_json_feed()` calls `add_feed( 'json', ... )` on `init`, gated by the `book_wp_json_feed` option (default: `1` — on). `add_feed` hooks into WP's rewrite layer, producing the clean URL `/feed/json` via the query var `?feed=json`. No template file is needed; the callback handles the full response.

`book_wp_json_feed_render()` sets `Content-Type: application/feed+json`, runs a `WP_Query` with `no_found_rows: true` (no `SQL_CALC_FOUND_ROWS`), and encodes the result with `JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT`. The flag combination keeps Malay and Arabic text readable in the raw feed output rather than escaped to `\uXXXX` sequences.

**Feed-level fields:** `version`, `title` (anthology name), `home_page_url`, `feed_url`, `description`, `language`, `authors` (when author name is set).

**Per-item fields:**
- `id`, `url` — canonical permalink
- `title` — post title
- `content_html` — full post content via `apply_filters('the_content')` — shortcodes and blocks expand
- `summary` — `book_wp_toc_excerpt` first; falls back to `wp_trim_words( content, 30 )`
- `date_published`, `date_modified` — ISO 8601, UTC
- `authors` — post author display name
- `image` — featured image at `book-wp-cover` size (800×400); omitted when not set
- `tags` — thematic tags only; hidden tags excluded via `book_wp_hidden_tag_ids()`
- `_book_wp` (extension object) — `literary_form`, `word_count`, `reading_time_min`; omitted when all values are empty

**Autodiscovery:** `book_wp_json_feed_link_tag()` emits `<link rel="alternate" type="application/feed+json">` on `wp_head` priority 2, front page and blog index only. Priority 2 places it before other theme head output.

**Settings:** `book_wp_json_feed` toggle added to the Advanced tab, SEO section, after the Social Meta Tags toggle. When the feed is on, the live feed URL is shown as a clickable hint below the toggle.

**New file:** `inc/helpers/json-feed.php` — loaded in `functions.php` after `social.php`.

---

## 3.4.58 — 2026-03-08

### Share row — Copy link, X, Facebook, WhatsApp

**`inc/helpers/navigation.php`, `single.php`, `assets/css/frontend.css`, `assets/js/frontend.js`, `inc/admin/settings.php`, `inc/setup/enqueue.php`**

A compact share strip added to single post pages, positioned between the tags and the colophon. Contains four targets: Copy link, X (Twitter), Facebook, WhatsApp. No third-party SDKs or external scripts loaded — X and Facebook use standard intent/sharer URL patterns; WhatsApp uses the `wa.me` deep-link. All links open in a new tab with `rel="noopener noreferrer"`.

`book_wp_share_row( $post_id )` added to `navigation.php`. Gated by `book_wp_share_row` option (default: `0` — off). Returns an empty string when the option is off so no markup is emitted. Called unconditionally in `single.php` after the tags block — the function itself handles the gate so it fires whether or not tags are shown.

CSS: `.bwp-share` flex row with `.bwp-share__label` and `.bwp-share__btn` components. Full night mode coverage (`bwp-night`, `bwp-scheme-night`, `bwp-time-night`). Copied confirmation state via `.bwp-share__copy--copied` (accent colour, 2s timeout).

JS: clipboard IIFE appended to `frontend.js`. Uses `navigator.clipboard.writeText()` with a `document.execCommand('copy')` fallback for older browsers. Label text swaps to `bookWP.copied` string on success; reverts after 2s. `bookWP.copied` and `bookWP.shareRow` added to `wp_localize_script` object in `enqueue.php`.

Settings: `book_wp_share_row` toggle added to Reading tab, Appearance > Sasterawan > Reading, under Reading Enhancements. Default off.

---

## 3.4.57 — 2026-03-08

### Social meta tags — OpenGraph and Twitter Card

**`inc/hooks/actions.php`, `inc/admin/settings.php`**

Single posts had no `og:*` or `twitter:*` meta tags. Share previews on all platforms (X/Twitter, Facebook, LinkedIn, WhatsApp, Telegram, iMessage) fell back to platform guessing — wrong images, missing descriptions, bare URLs. The existing JSON-LD structured data is invisible to share-card scrapers; it is read only by search engines.

`book_wp_social_meta()` added to `actions.php`, hooked on `wp_head` at priority 5 (before most other head output). Fires on `is_single()` only.

**Fields emitted:**
- OpenGraph base: `og:type` (article), `og:title`, `og:description`, `og:url`, `og:site_name`, `og:locale`
- OpenGraph image (when featured image exists): `og:image`, `og:image:width`, `og:image:height`, `og:image:alt` — uses `book-wp-cover` size (800×400, 2:1 ratio)
- Article namespace: `article:published_time`, `article:modified_time`, `article:author`, `article:section` (literary form label), `article:tag` (one per thematic tag, hidden tags excluded)
- Twitter Card: `twitter:card` (`summary_large_image` when image present, else `summary`), `twitter:title`, `twitter:description`, `twitter:image`, `twitter:image:alt`, `twitter:site` (resolved from `book_wp_social_twitter` URL already stored in Social tab settings)

**Description source priority:** `book_wp_toc_excerpt` meta field → `wp_trim_words( content, 30 )`.

**SEO plugin guard:** `book_wp_seo_plugin_active()` added as a standalone utility. Checks `RANK_MATH_VERSION`, `WPSEO_VERSION`, `AIOSEO_VERSION`, `SEOPRESS_VERSION`, and `class_exists('The_SEO_Framework\Load')`. If any match, `book_wp_social_meta()` returns early and emits nothing — the active SEO plugin owns all meta output.

Settings: `book_wp_social_meta_tags` toggle added to Advanced tab, SEO section. When an SEO plugin is detected at page load, the toggle is replaced with a read-only notice naming the detected plugin; a hidden input holds `0` so the option is not accidentally re-enabled. When no SEO plugin is present, the toggle defaults to `1` (on).

---



### Social tab — icon-prefixed inputs and grouped sections

**`inc/admin/settings.php`, `assets/css/admin.css`**

The social profiles tab used `.bwp-row`, `.bwp-label`, and `.bwp-input` classes
that had no CSS definitions, causing the fields to render with raw browser
default styling — unstyled input boxes with no visual hierarchy or platform
identity.

Markup: `bwp_tab_social()` fully rewritten. Each platform field now uses the
`.bwp-field` / `.bwp-field__label` / `.bwp-field__control` grid system consistent
with all other settings tabs. A new `.bwp-icon-input` wrapper wraps each `<input>`
with an icon badge showing the platform SVG. The helper function `bwp_social_field()`
encapsulates the per-platform markup. Platforms are grouped into five logical
sections via a `$groups` array: Social Media, Libraries & Catalogues, Developer &
Repository, Author Identifiers, and Syndication. Section sub-headings use the new
`.bwp-social-group-label` class.

CSS: new `.bwp-icon-input` block added to `assets/css/admin.css`. The wrapper
handles the border, focus ring, and shadow so the inner `<input>` is borderless.
The icon badge activates brand-tinted colour on focus-within. Per-platform tints
defined via `.bwp-icon-input__badge--{slug}` modifier classes (17 platforms).
Group label styled as a dashed-border sub-heading at 0.656rem uppercase.

---

## 3.4.55 — 2026-03-08


### Bug fix — Social profile fields not saving

**`inc/admin/settings.php`**

The Social tab form submits `bwp_tab=social`. The `$tab_fields` array in
`book_wp_handle_tab_save()` had no `'social'` key — all 17 social platform
options were nested inside `'advanced'` instead. When the save handler received
`$tab = 'social'` it fell through to the `! isset( $tab_fields[ $tab ] )` guard
and redirected without writing any options.

Fix: the 17 `book_wp_social_*` fields are moved from the `'advanced'` array into
a new `'social'` array that matches the tab slug the form actually posts. The
`'advanced'` array retains only its own two fields (`book_wp_structured_data` and
`book_wp_custom_css`).

---

## 3.4.54 — 2026-03-08


### Hidden tag system — ID-based storage, full query coverage, sitemap, tag cloud, and output suppression

**HT-01 — Storage migrated from slugs to term IDs**
`inc/hooks/actions.php`, `inc/admin/settings.php`, `assets/js/admin-settings.js`

The `book_wp_hidden_tags` option previously stored a comma-separated list of tag
slugs. Slug storage breaks silently when a tag is renamed in WP admin — the stored
slug no longer matches the term and hidden posts reappear on all public surfaces.

Fix: option now stores comma-separated term IDs. `book_wp_hidden_tag_ids()` is the
new canonical reader; `book_wp_hidden_tag_slugs()` is retained for all existing call
sites and resolves IDs to slugs via a single `get_terms()` call (static-cached).
`book_wp_sanitize_tag_ids()` replaces `book_wp_sanitize_tag_slugs()` for this
option — each submitted value is cast to `absint` and verified against `post_tag`
via `term_exists()` before storage.

A one-time migration function (`book_wp_migrate_hidden_tags_to_ids()`) runs on
`admin_init`. It detects slug-format values, resolves each via `get_term_by()`,
and rewrites the option as IDs. It no-ops immediately if the value is already
numeric or empty.

Settings UI: option element `data-id` attribute added alongside the existing
`data-slug`. The PHP UI now compares by `term_id` instead of `slug` to determine
pre-selected state.

`admin-settings.js`: `selected[]` now stores ID strings (from `data-id`);
`toggleSlug()` replaced by `toggleId()`; pill remove button carries `data-id`
instead of `data-slug`; `syncHidden()` and `renderPills()` updated accordingly.
`@version` bumped to 3.4.54.

---

**HT-02 — Query filter coverage expanded to all required public contexts**
`inc/hooks/actions.php`

`book_wp_hide_from_category_archive()` was scoped to `is_category()` and
`is_search()` only. Missing contexts: home/blog index, date archives, author
archives, feeds, and non-hidden tag archives.

Fix: function renamed to `book_wp_hide_from_public_queries()`. The condition now
covers `is_home()`, `is_category()`, `is_date()`, `is_author()`, `is_search()`,
and `is_feed()`. For `is_tag()`, the queried term slug is checked against the
hidden list: matching hidden-tag archives are allowed through (per spec); all other
tag archives are filtered.

---

**HT-03 — Widget secondary query exclusion**
`inc/hooks/actions.php`

Secondary `WP_Query` instances created inside sidebar widgets are not the main
query and were not filtered by the existing hook. A `book_wp_hide_from_widget_queries()`
function is added, scoped to `pre_get_posts` on non-main queries only while
`doing_action( 'dynamic_sidebar' )` is true. This prevents hidden posts from
appearing in Recent Posts and similar widgets without affecting the theme's own
navigation and TOC helper queries.

---

**HT-04 — REST API post query exclusion**
`inc/hooks/actions.php`

Public REST API requests to `/wp/v2/posts` returned hidden-tagged posts because
`pre_get_posts` conditional checks do not apply in REST context. A
`book_wp_hide_from_rest_query()` function is added via `rest_post_query`, applying
a `NOT IN` tax_query for unauthenticated requests and users without `edit_posts`.
Editors and administrators retain full REST access.

---

**HT-05 — WordPress core XML sitemap exclusion**
`inc/hooks/actions.php`

WordPress core sitemaps (since 5.5) enumerate posts via a direct `WP_Query` that
bypasses the conditional checks used in `pre_get_posts`. A
`book_wp_hide_from_sitemap()` function is added via `wp_sitemaps_posts_query_args`,
scoped to `post_type === 'post'`. Hidden-tagged posts are omitted from the XML
sitemap while remaining crawlable and indexable through their direct single-post
URLs (the sitemap filter does not affect the posts themselves).

---

**HT-06 — Tag Cloud widget suppression**
`inc/hooks/actions.php`

The Tag Cloud widget calls `get_terms()` directly and is not affected by
`pre_get_posts`. A `book_wp_hide_tags_from_cloud()` function is added via
`widget_tag_cloud_args`, appending hidden tag IDs to the `exclude` list so
hidden tags never appear in rendered tag clouds.

---

**HT-07 — the_tags output filter**
`inc/hooks/filters.php`

The rendered HTML string from `the_tags()` / `get_the_tag_list()` was not filtered
at the output layer. Any template or widget calling these functions would display
hidden tag links. A `book_wp_suppress_hidden_tags_from_output()` function is added
via the `the_tags` filter (priority 10, 4 args). It rebuilds the link list from
the current post's tags, excludes any with IDs in the hidden list, and returns
the filtered string. Returns `false` (no tags) when all tags are hidden. Returns
the original string unchanged when no hidden tags are present (zero overhead).

---

## 3.4.53 — 2026-03-08


### WCAG 2.2 — ARIA, target size, focus, and link fixes (batch 2 of 2)

**W-02 — Broken ARIA tab pattern removed (SC 4.1.2, Level A — HIGH)**
`inc/admin/settings.php`

Admin settings tabs used `role="tab"` on `<a>` elements and `role="tablist"` on the
`<nav>`, but the pattern was incomplete: no `aria-controls`, no `role="tabpanel"`,
no `aria-labelledby` on panels, and no arrow-key JS. An incomplete ARIA tab widget is
worse than no ARIA — AT announces `role="tab"` but cannot build the required
tab/panel relationship, leaving assistive technology in an undefined state.

Fix: `role="tab"` and `aria-selected` removed from all tab `<a>` elements. `role="tablist"`
removed from `<nav>`. Replaced with `aria-current="page"` on the active link — the
standard, unambiguous signal for the current page in a server-rendered navigation. The
`aria-label` on each link and the `aria-label` on the `<nav>` are retained.

---

**W-03 — Social collapse toggle target size (SC 2.5.8, Level AA — HIGH)**
`assets/css/frontend.css`

`.bwp-social__toggle` uses `font-size: .65em` and inherits its size from the shared
`width: 2em; height: 2em` rule on `.bwp-social__link`. At the default 16px base, the
em resolves at 0.65 × 16 = 10.4px, making 2em ≈ 20.8px — below the WCAG 2.2 SC 2.5.8
minimum of 24×24 CSS px.

Fix: `min-width: 24px; min-height: 24px` added to `.bwp-social__toggle`. The minimum
is expressed in absolute px so it is immune to inherited font-size variations.

---

**W-04 — Night toggle no longer obscures footer focus ring (SC 2.4.11, Level AA — HIGH)**
`assets/css/frontend.css`

The night toggle is `position: fixed; bottom: 1.5em; right: 1.5em`. Footer credit links
and footer nav items near the bottom-right corner of the viewport could receive keyboard
focus while the toggle overlapped their outline, partially obscuring the focus indicator.

Fix: `padding-bottom: 4em` added to `.bwp-site-footer__inner`. This pushes all
keyboard-reachable footer content upward, out of the toggle's footprint, so focus rings
on the last tabbable elements remain fully visible.

---

**W-05 — Required-field note readable in full by AT (SC 3.3.2, Level A — MEDIUM)**
`comments.php`

The note "Medan diperlukan ditanda dengan *" had `aria-hidden="true"` on the asterisk.
Screen readers read the sentence and stopped before the concluding symbol, producing the
syntactically incomplete phrase "required fields are marked with".

Fix: `aria-hidden="true"` removed from the note's asterisk only. The asterisks on
individual field labels (Ulasan, Nama, E-mel) remain `aria-hidden` — they are redundant
to `aria-required="true"`, which is the authoritative programmatic signal for AT.

---

**W-07 — Tag-select combobox ARIA completed (SC 4.1.2, Level A — MEDIUM)**
`inc/admin/settings.php`, `assets/js/admin-settings.js`

The admin tag-select widget had `role="listbox"` on the dropdown but the `<input>` had
no combobox role, no open/closed state, no association to the dropdown, and no
indication of the keyboard-focused option. AT could not determine the widget state or
navigate the option list.

Fix in `settings.php`: `role="combobox"`, `aria-expanded="false"`, `aria-haspopup="listbox"`,
`aria-controls="bwp-hidden-tags-dropdown"`, `aria-activedescendant=""` added to the
`<input>`. `id="bwp-hidden-tags-dropdown"` added to the dropdown container. Each option
`<div>` gains `id="bwp-tag-opt-{slug}"` so `aria-activedescendant` can reference it.

Fix in `admin-settings.js`: `open()` sets `aria-expanded="true"`; `close()` sets
`aria-expanded="false"` and clears `aria-activedescendant`; `highlightFocused()` sets
`aria-activedescendant` to the focused option's id on each arrow-key move.

---

**W-09 — Inline links underlined in all text-bearing contexts (SC 1.4.1, Level A — LOW)**
`assets/css/frontend.css`

Global rule `a { text-decoration: none }` meant that inline links in contexts outside
`.bwp-work__content` and `.bwp-comment__content` were distinguished from surrounding
text by colour alone. Link colour #1B3C53 vs body text #111111 produces ~1.6:1 contrast —
well below the 3:1 WCAG minimum required when colour is the sole differentiator.

Fix: `text-decoration: underline` extended to `.bwp-chapter-blurb a`, `.bwp-book-blurb a`,
and `.bwp-footer-widget a`. Navigation links and isolated list-format links (TOC entries,
related works, work navigation) are exempt — their interactive context makes their role
unambiguous without underlines.

---

## 3.4.52 — 2026-03-08

### WCAG 2.2 — Keyboard, structural, and link text fixes (batch 1 of 2)

**W-01 — Sub-menu keyboard access added (SC 2.1.1, Level A — CRITICAL)**
`assets/css/frontend.css`

Sub-menus were disclosed only via `.bwp-nav__list li:hover > .sub-menu { display: block }`.
A keyboard user tabbing through the primary navigation would focus each top-level
link but never trigger sub-menu disclosure — all child items were completely
unreachable without a pointer device.

Fix: `.bwp-nav__list li:focus-within > .sub-menu { display: block }` added alongside
the existing `:hover` rule. When keyboard focus lands anywhere inside a nav list item
(including on a child link), the sub-menu opens. No JS required; browser support for
`:focus-within` covers all supported WordPress browser targets.

---

**W-06 — 404 page heading changed from h2 to h1 (SC 1.3.1 + 2.4.2, Level A — MEDIUM)**
`404.php`

The 404 template had `<h2 class="bwp-404__title">Page Not Found</h2>` as its sole
heading. Heading hierarchy began at level 2, skipping level 1 entirely. This misrepresents
the document structure to AT — screen readers present a heading outline with a level-2
entry and no level-1.

Fix: `<h2>` changed to `<h1>`. The site title in header.php renders as `<h1>` only on
the front page and as `<p>` on all interior pages, so there is no heading conflict.

---

**W-08 — Pagination prev/next links given accessible names (SC 2.4.4, Level A — LOW)**
`index.php`, `archive.php`, `tag.php`, `search.php`

`prev_text` and `next_text` were passed as bare `&larr;` / `&rarr;` entities to
`the_posts_pagination()`. WordPress renders these directly as the link text — a single
Unicode arrow character. Screen readers announce it as "left-pointing arrow" rather than
anything meaningful.

Fix: each arrow is now wrapped in `<span aria-hidden="true">` so AT ignores it, with
a sibling `<span class="screen-reader-text">` providing "Previous page" / "Next page"
via `esc_html__()`. Applied to all four templates that call `the_posts_pagination()`.

---

## 3.4.51 — 2026-03-07

### CWV — LCP, CLS & INP fixes (batch 2 of 2)

**CWV-03 — Body fade-in animation moved to .bwp-page-wrap (LCP — MEDIUM)**
`assets/css/frontend.css`

`body { animation: bwp-in .35s ease both }` started the page at `opacity: 0`.
Chrome's LCP algorithm does not record an element as the LCP candidate while
it is invisible — the score was not reported until the opacity animation fired,
artificially delaying the measured LCP by up to 350ms on every page view.

Fix: `animation` removed from `body`. It now lives on `.bwp-page-wrap` instead.
The visual fade effect is preserved; the LCP element is no longer hidden from
Chrome's perspective at T=0.

---

**CWV-04 — Footer logo from max-height to height (CLS — MEDIUM)**
`assets/css/frontend.css`

`.bwp-site-footer__logo img { max-height: 60px; width: auto }` — `max-height`
alone does not override WordPress's explicit `width` attribute injected from
attachment metadata. On first load (logo not cached), the browser reserved no
space, then resized the element when the image arrived, producing a layout
shift in the footer.

Fix: `max-height: 60px` → `height: 60px`. `width: auto` is retained to
preserve the intrinsic aspect ratio.

---

**CWV-05 — Reading memory scroll restore: setTimeout removed (CLS — MEDIUM)**
`assets/js/frontend.js`

The reading memory feature restored scroll position via
`window.setTimeout(() => window.scrollTo(0, pos), 50)`. The page rendered at
scroll 0, painted a first frame, then jumped 50ms later — recorded by Chrome
as a layout shift on every repeat visit to any post with memory active.

Fix: `history.scrollRestoration = 'manual'` set immediately at feature init
so the browser does not compete with the restore. `window.scrollTo()` called
synchronously (no `setTimeout`) before the first meaningful paint.

---

**CWV-06 — Word count cached in post meta on save (TTFB — LOW)**
`inc/hooks/actions.php`

`str_word_count( wp_strip_all_tags( $post_content ) )` was running live inside
`book_wp_structured_data()` on every `wp_head` action for every single post
page load. For long-form works this is a non-trivial string operation executed
per request, at zero benefit — the content does not change between saves.

Fix: new `book_wp_cache_word_count()` hooked to `save_post`. Computes and
stores the count as `book_wp_word_count` post meta on each save, skipping
autosaves and revisions. `book_wp_structured_data()` now reads from meta;
falls back to live computation for posts not yet re-saved (self-heals on next
save).

---

**CWV-07 — decoding="async" added to TOC thumbnails (INP — LOW)**
`inc/helpers/navigation.php`

TOC thumbnails (`book-wp-thumb`, 80×80) carried `loading="lazy"` but not
`decoding="async"`. Without async decoding the browser blocks the main thread
while decoding each image, even lazily-loaded ones. On large TOC pages with
many thumbnails this produced repeated short main-thread stalls during
scroll-triggered loads.

Fix: `'decoding' => 'async'` added to the `get_the_post_thumbnail()` attributes
array in `book_wp_toc_entry()`.

---

## 3.4.50 — 2026-03-07

### CWV — LCP & CLS fixes (batch 1 of 2)

**CWV-01 — @import Google Fonts removed (LCP — CRITICAL)**
`assets/css/frontend.css`, `inc/setup/enqueue.php`

The `@import url(Google Fonts)` at line 1 of `frontend.css` was creating a
serialised fetch chain: the browser had to download and parse `frontend.css`
before it could discover the Fonts URL, then fetch the Fonts CSS, parse it
for font binary URLs, and only then begin loading the fonts. This blocked
rendering on every page load.

Fix: `@import` removed from `frontend.css` (replaced with an explanatory
comment). Cinzel, EB Garamond, and Jost are now registered via
`wp_enqueue_style( 'book-wp-ui-fonts', ... )` in `enqueue.php` before
`frontend.css` — the browser initiates both requests in parallel.
Preconnect hints for `fonts.googleapis.com` and `fonts.gstatic.com` now
fire unconditionally at priority 9 (previously only when a per-user font
was selected). The old conditional per-user-font preconnect filter removed
as redundant.

Expected LCP improvement: 300–800ms on a median mobile connection.

---

**CWV-02 — aspect-ratio mismatch corrected (CLS — HIGH)**
`assets/css/frontend.css`

`.bwp-featured-image` carried `aspect-ratio: 16/9` (≈1.78:1) but the
registered image size `book-wp-cover` is 800×400 (exactly 2:1). The
browser reserved a 16:9 box then re-laid out when the 2:1 image loaded,
producing a measurable layout shift.

Fix: `aspect-ratio: 16/9` → `aspect-ratio: 2/1`. Comment updated to note
the coupling with `add_image_size()` — if crop dimensions ever change, the
`aspect-ratio` value must be updated in the same commit.

Eliminates the featured image layout shift on all single post templates.

---

## 3.4.49 — 2026-03-07

### Social links — nofollow removed

`nofollow` removed from the `rel` attribute on all social profile links.
`rel` reverts to `"noopener noreferrer me"`. Social profile links are
owned identity endpoints — passing PageRank authority to them is correct
and intended. `nofollow` is appropriate for links to unrelated external
sites, not to the author's own profiles. `me` is retained as the IndieWeb
ownership signal.

---

## 3.4.48 — 2026-03-07

### Social icons — seven new book/library platforms + nofollow fix

**`rel` attribute corrected** (`inc/helpers/social.php`)
All social links now carry `rel="noopener noreferrer nofollow me"`.
Previously `noreferrer` implied nofollow but did not state it explicitly.
`nofollow` added for correctness and forward-compatibility.

**Seven new platforms added** (`inc/helpers/social.php`, `inc/admin/settings.php`)
Registry expanded from 10 to 17 platforms. New entries:

| Slug | Label | Icon style |
|---|---|---|
| `goodreads` | Goodreads | Branded G lettermark (filled) |
| `worldcat` | WorldCat | Globe with latitude lines (stroke) |
| `loc` | Library of Congress | Columned building / pediment (stroke) |
| `nlb` | NLB Singapore | Open book (stroke) |
| `pnm` | Perpustakaan Negara Malaysia | Book with bookmark (stroke) |
| `orcid` | ORCID | iD badge lettermark (filled) |
| `isni` | ISNI | Identifier card with grid lines (stroke) |

LOC, NLB, and PNM share a library/archive glyph register (stroke, no brand mark).
ORCID uses its published iD path. ISNI uses an identifier card metaphor.

All seven options registered in `book_wp_register_settings()` and
`$tab_fields['social']` with `esc_url_raw` sanitisation. The Social settings
tab renders fields for all 17 platforms automatically via the registry loop.

---

## 3.4.47 — 2026-03-07

### Social icons — collapse/expand toggle (max 6 visible)

**CSS** (`assets/css/frontend.css`)
`.bwp-social--collapsible` class (added by JS) activates `nth-child(n+7)`
hiding. `.bwp-social--expanded` overrides it to show all items.
`.bwp-social__toggle` shares all dimensional and colour rules with
`.bwp-social__link` — same 2em circular button, same hover/focus states,
same night-mode border overrides. No new layout rules needed.

**JS** (`assets/js/frontend.js`)
`BWP_SOCIAL_LIMIT = 6` constant. On `DOMContentLoaded`, counts
`.bwp-social__item` elements. If `total <= 6` exits immediately — no
classes added, all icons visible with no toggle. If `total > 6`:
adds `.bwp-social--collapsible` to `<ul>`; injects `<button
class="bwp-social__toggle">` as a `<li>` child at position 7 showing
`+N` (N = hidden count); on click toggles `.bwp-social--expanded`, flips
label to `×` and moves the toggle to list end; on collapse restores `+N`
and repositions toggle at slot 7. `aria-expanded` and `aria-label` updated
on every toggle. Degrades gracefully without JS — no collapsible class is
set so all icons remain visible.

---

## 3.4.46 — 2026-03-07

### Social profile icons — footer row

**New file: `inc/helpers/social.php`**
Contains the platform registry (`book_wp_social_platforms()`), inline SVG
icon function (`book_wp_social_icon( $slug )`), and output function
(`book_wp_social_icons()`). Ten platforms supported: Twitter/X, Facebook,
Instagram, Threads, LinkedIn, YouTube, Medium, Wikipedia, GitHub, RSS.

**New settings tab: Social** (`inc/admin/settings.php`)
Sixth tab added to Appearance > Sasterawan. Each platform has its own URL
field (sanitised with `esc_url_raw`). RSS falls back to `get_feed_link()`
automatically when the field is left blank. The `share` icon added to
`bwp_icon()`. Options registered in `book_wp_register_settings()` and
`$tab_fields` save handler.

**Footer integration** (`footer.php`)
`book_wp_social_icons()` called between the logo block and the colophon.
Output is conditional — nothing renders if no platforms are configured.

**CSS** (`assets/css/frontend.css`)
`.bwp-social` — flex row, `justify-content: center`, `gap: .5em`.
`.bwp-social__link` — 2em circular icon button, border `1px solid
--bwp-border-faint`, hover transitions colour and background.
Night/dark scheme overrides included for border and hover background.

**`functions.php`**
`require_once` for `inc/helpers/social.php` added to core includes chain.

---

## 3.4.45 — 2026-03-07

### Border radius — global token and per-element overrides

**`--bwp-radius` token set to `6px`** (`assets/css/frontend.css`)
Previously `0`. The token propagates to all elements that reference it:
TOC thumbnails, blockquote/epigraph containers, input fields, submit buttons,
search form input/button pairs, and the glossary tooltip. Consistent softened
corners across all UI elements without individual overrides.

**Featured image — explicit `6px`** (`.bwp-featured-image__img`)
Value pinned explicitly and independent of the token so future token changes
do not inadvertently affect the hero image. Visually identical to token value
at 6px.

**Form badges — pill shape** (`.bwp-badge`)
`border-radius` changed from `0` to `2em`. Literary form badges (PUISI,
CERPEN, HIKAYAT, etc.) now render as pill labels, consistent with the
existing `.bwp-tag` pill register (`border-radius: 20px`, pre-existing).
`.bwp-toc__cat` category labels were already pill-shaped and are unchanged.

---

## 3.4.44 — 2026-03-07

### Core Web Vitals — LCP and CLS improvements

Four targeted fixes applied across `inc/helpers/navigation.php`,
`inc/setup/enqueue.php`, and `assets/css/frontend.css`.

**Fix 1 — `fetchpriority` and `loading` on featured image (`navigation.php`)**
`book_wp_featured_image()` now passes `'fetchpriority' => 'high'` and
`'loading' => 'eager'` to `get_the_post_thumbnail()`. The featured image is
the LCP candidate on single posts; without these hints the browser queues it
at normal priority, delaying the LCP measurement. No behaviour change when no
featured image is present.

**Fix 2 — `preconnect` hints for Google Fonts (`enqueue.php`)**
A `wp_resource_hints` filter is registered conditionally when a non-Georgia
font is selected. It emits `<link rel="preconnect">` for both
`fonts.googleapis.com` (stylesheet origin) and `fonts.gstatic.com`
(font binary CDN, with `crossorigin`). This eliminates the DNS + TCP + TLS
cold-connection overhead from the critical path for font delivery. Not
registered when the default Georgia stack is in use.

**Fix 3 — `translateY` removed from `bwp-in` entrance animation (`frontend.css`)**
The `@keyframes bwp-in` rule previously shifted `<body>` 5px on entry
(`from { opacity: 0; transform: translateY(5px); }`). Chrome's CLS
measurement counts this shift. The `translateY` is removed; the opacity
fade is retained. `prefers-reduced-motion` handling is unchanged.

**Fix 4 — `aspect-ratio` on `.bwp-featured-image` figure (`frontend.css`)**
`.bwp-featured-image` now declares `aspect-ratio: 16 / 9` and
`overflow: hidden`. WordPress 5.5+ injects `width` and `height` on the
`<img>` automatically from attachment metadata; the `aspect-ratio` on the
`<figure>` is a belt-and-braces fallback for posts where attachment
metadata is absent, preventing the container from collapsing and reflowing
when the image loads. `height: auto` added to `.bwp-featured-image__img`
for explicit intrinsic sizing.

---

## 3.4.43 — 2026-03-07

### Theme support — post formats and Customizer selective refresh

Two standard WordPress theme features added to `inc/setup/theme-support.php`:

**Post format support**
`add_theme_support( 'post-formats', [...] )` declared for two formats only:
`aside` (annotations and short prose notes) and `image` (illustrated works,
cover reveals). The full nine-format set is intentionally not used — only
formats with a genuine literary use case are declared. The `literary_form`
taxonomy remains the primary classification mechanism for all full-form works.

**Customizer selective refresh**
`add_theme_support( 'customize-selective-refresh-widgets' )` declared.
Refreshes only the affected widget area in the Customizer preview instead
of reloading the full iframe on every widget change. Standard for any
classic theme with a registered sidebar.

**KIV roadmap entries added to `upgrading.md`**
RTL stylesheet and Block patterns documented as scoped future releases,
replacing the brief prior RTL note. Both entries include implementation
scope and candidate work items.

---

## 3.4.42 — 2026-03-06

### Comment textarea — poetic placeholder

The generic comment placeholder ("Tulis ulasan anda di sini…", previously
hardcoded in Malay) replaced with a proper i18n implementation:

- **English source string** (default): *"Leave your words here…"*
- **Malay translation** (`ms_MY`): *"Keratlah kata dari kalbumu, curahkanlah di sini…"*

The Malay phrase uses *k/k/k* alliteration, *a/a/a* assonance, and the
classical Arabic-root word *kalbu* (heart/soul). The *-lah* particles
shift the register to a gentle imperative — an invitation rather than
an instruction.

**Files changed:** `comments.php`, `languages/ms_MY.po`, `languages/ms_MY.mo`,
`languages/book-wp.pot`

---

## 3.4.41 — 2026-03-06

### Bugfix — \u2026 Unicode escape rendering as literal text

`comments.php` and `inc/admin/settings.php` both contained `\u2026` (the
Unicode ellipsis character) as a PHP single-quoted string escape. PHP
single-quoted strings do not process `\u` sequences — the literal characters
`\u2026` were output to the browser instead of `…`.

Both occurrences replaced with the UTF-8 ellipsis character directly:

- `comments.php` line 70 — comment textarea placeholder ("Tulis ulasan anda di sini…")
- `inc/admin/settings.php` line 691 — hidden-tag multi-select placeholder ("Select tags…")

---

## 3.4.40 — 2026-03-06

### Root folder cleanup

Four housekeeping changes to reduce noise in the theme root:

1. **`config/` directory created** — `lighthouserc.json` moved here. CI config
   files (`.phpcs.xml.dist`, `.eslintrc.json`, `.stylelintrc.json`) should also
   live here when created. `upgrading.md` CI table and `readme.md` updated to
   reflect new paths.

2. **`release-checklist.md` removed** — content merged into `upgrading.md` as a
   new `## Release Checklist` section, immediately after CI and Tooling. The
   standalone file was redundant given upgrading.md is the developer reference.

3. **`template-parts/` removed** — empty directory with no files. If partial
   templates are introduced in future they should go here, but the empty
   directory added noise.

4. **`package.json` version corrected** — was stuck at `3.4.6` (the version at
   which the npm config was first added). Updated to match `style.css`.

---

## 3.4.39 — 2026-03-06

### readme.txt — technical sections moved to upgrading.md

`== Directory Architecture ==` and `== CSS Custom Properties ==` removed from
`readme.txt`. Both sections are already documented (in greater detail) in
`upgrading.md`, which is the designated developer reference. `readme.txt` is
now scoped to general readers: features, settings, installation, FAQs, and
privacy. A `== Developer Reference ==` pointer section replaces the two
removed sections, directing developers to `upgrading.md`.

`upgrading.md` directory structure updated: `assets/css/tags/` (per-tag CSS
pipeline) and `assets/img/` (static assets) added. Per-tag stylesheet added
to CSS load order sequence as step 8.

---

### readme.md created; readme.txt simplified for general readers

**`readme.md`** *(new file)*

Developer-facing technical reference covering: requirements, installation,
directory structure overview, constants, custom taxonomy, CSS architecture
and load order, per-tag CSS pipeline, action and filter hooks, helper
function signatures, shortcodes, caching, internationalisation, child theme
extension points, and CI/linting tools. Bridges the gap between the
general-reader `readme.txt` and the exhaustive `upgrading.md`.

**`readme.txt`** — technical content removed or simplified

- Settings section rewritten in plain English (removed "SVG preview selector",
  "CSS custom properties", "Schema.org", etc.)
- Per-tag CSS section simplified — file paths and code references removed
- FAQ: "How do I add a decorative background for a tag?" removed (dev question
  → `readme.md`). Replaced with plain "How do I enable structured data?"
- FAQ: "How many tags can I add?" replaces "How do I raise the tag cap?"
  (keeps the answer, removes `BOOK_WP_MAX_TAGS` detail; pointer to `readme.md` added)
- FAQ: "Can I use a child theme?" simplified — removed class prefix details
- Privacy section: Google Fonts self-hosting instructions simplified;
  `@font-face`, `wp-config.php`, `BOOK_WP_LOCAL_FONTS` references removed
- Developer Reference pointer updated to mention `readme.md`
- `localStorage`, `inline SVG`, `CSS counters` references removed from Features

**`upgrading.md`** — directory structure updated

- `assets/css/tags/` and `assets/img/` added to directory listing
- Per-tag CSS added as step 8 in CSS load order sequence


### readme.txt — seven issues resolved

Cross-checked against readme.md and upgrading.md. All corrections align
readme.txt with the authoritative information in the .md files.

1. **Font list corrected** — readme.txt listed `Amiri` as one of the seven font
   selector choices; the correct list (per upgrading.md) is Georgia, EB Garamond,
   Lora, Vollkorn, Arvo, Maven Pro, Ubuntu. Amiri is now described separately as
   a bundled Arabic/Jawi font.
2. **Duplicate `= Per-tag Decorative CSS =` section removed** — two near-identical
   blocks present; merged and simplified into a single `= Per-tag Decorative Backgrounds =`
   section without file paths or class names (those belong in readme.md).
3. **File paths removed from Features** — `assets/css/tags/`, `inc/setup/enqueue.php`,
   `bwp-tag-{slug}` class names removed; pointer to readme.md added.
4. **WP-CLI command removed from Installation** (step 14) — replaced with plain-English
   instruction to use the Regenerate Thumbnails plugin.
5. **WP-CLI command removed from FAQ** — Masonry/Infinite Scroll answer simplified.
6. **Upgrade Notice entries simplified** — 3.4.7, 3.4.10, 3.4.11, 3.4.12, 3.4.13
   had PHP internals, function signatures, and CSS token names; rewritten in plain English.


## 3.4.38 — 2026-03-05

### Erotika — column background and side panels extended to page-wrap borders on all forms

The column background (`::before`) was offset by only `−2em` on each side —
equal to the page-wrap padding — which fills the gap for the default form but
leaves visible gaps on forms that set a narrower `bwp-work__content` inside a
wider `bwp-page-wrap`. The side panels were also calculated against the default
`40em` page-wrap, so forms with a wider page-wrap left an unlit strip between
the column background and the side panel inner edge.

Per-form overrides added to `assets/css/tags/erotika.css` with two-class
specificity (`bwp-form-X` + `bwp-tag-erotika`), which outranks all generic
single-class responsive rules at every breakpoint:

- Column background `left`/`right` corrected per form using the exact offset
  `−(centering-gap + 2em padding)` so the pseudo fills flush to the page-wrap
  border lines.
- Side panel `width` corrected per form to `calc(50vw − [page-wrap-half])` so
  panels meet the page-wrap edge without gap or overlap.
- Memoir pinned at `calc(50vw − 20em)` to prevent the wide-viewport generic
  rule (`−24em`) from pulling panels inside the `40em` memoir page-wrap.

Forms affected: cerpen, esei, hikayat, karmina, memoir, novel, nukilan, puisi,
ratapan, syair. Default (no form class) unchanged at `−2em` / `20em`.

---

## 3.4.37 — 2026-03-05

### Erotika — column background corrected to column borders only

Reverts the full-bleed `calc(-50vw + 50%)` approach from 3.4.36, which caused
the column background to extend past the column border lines and overlap the
side panels. Replaced with `left: -2em; right: -2em` — the page-wrap padding
value — so the pseudo-element fills flush to the column borders and stops there.
Side panels meet it exactly at the border edge with no gap and no overlap.

---

## 3.4.36 — 2026-03-05

### Erotika — column background extended full-bleed to viewport edges

The column background (`bwp-work__content::before`) previously covered only
the prose div width, leaving a visible gap at the page-wrap border between
the column background and the side panels.

`inset: 0` replaced with the full-bleed technique:

```css
left:  calc( -50vw + 50% );
right: calc( -50vw + 50% );
```

The pseudo-element now spans from the viewport left edge to the viewport
right edge, meeting the fixed side panels flush. The column background,
side panels, and the gap between them are now one continuous surface.

---

# Sasterawan — Changelog

## Editorial note — intentional consolidations

Several version ranges have been deliberately merged into a single entry
at the highest version in the range. This is intentional editorial
consolidation for brevity, not an error or omission. Do not re-expand
these into individual entries.

| Consolidated entry | Absorbed versions | Reason |
|--------------------|-------------------|--------|
| `3.4.35` | 3.4.29–3.4.34 | Erotika background iterated across 7 releases before reaching final form |
| `3.4.27` | 3.4.24–3.4.26 | Archive layout and TOC flat-list developed across 4 consecutive releases |
| `3.4.21` | 3.4.20 | Author's Note positioning corrected in the following release |
| `3.4.18` | 3.4.16–3.4.17 | Karmina and Nukilan layout fixes made in immediate succession |
| `3.4.15` | 3.4.14 | Drop cap font choice refined in the following release |

When adding new entries, continue from the current highest version as normal.
Only merge future entries if they form a coherent, completed feature arc
where the intermediate states are no longer relevant to upgraders.

---

## 3.4.35 — 2026-03-05

### Erotika tag — decorative background (feature + iterations)

Adds a per-tag CSS pipeline (`assets/css/tags/`) and the first consumer:
single posts tagged `erotika` receive a batik background from
`assets/img/batik-erotika.png`. Iterated over seven releases to reach
the final form.

**Infrastructure (`inc/hooks/filters.php`, `inc/setup/enqueue.php`)**

`book_wp_body_classes()` now adds `bwp-tag-{slug}` for every tag on a
single post. A whitelist-gated per-tag CSS enqueue block (parallel to
the per-form block) loads `assets/css/tags/{slug}.css` when the post
carries a matching tag. `erotika` is the first entry.

**`assets/img/batik-erotika.png`** *(new)*

Dark-ground batik with bilateral symmetry, lotus motif, and ripple base.
622 KB. Referenced only from `erotika.css`.

**`assets/css/tags/erotika.css`** *(new)*

Two layers:

| Layer | Element | Crop |
|-------|---------|------|
| Column background | `.bwp-work__content::before` | Full image, centre |
| Left panel | `body::before` | Left wing zone, fixed |
| Right panel | `body::after` | Right wing zone, fixed |

Column background is scoped to `.bwp-work__content` — the prose div ends
structurally before tags, colophon, and all meta fields; the image cannot
overflow into those areas. Side panels fill the viewport margin on each
side and collapse on mobile.

Scheme-aware opacity (7–10%) and `mix-blend-mode: multiply` on light
schemes (Cream, Dusk):

| Scheme  | Opacity | Blend |
|---------|---------|-------|
| Default | 8%      | —     |
| Night   | 7%      | —     |
| Cream   | 10%     | multiply |
| Dusk    | 9%      | multiply |

## 3.4.28 — 2026-03-05

### Documentation audit — cross-file congruence fixes

**`readme.txt`**
- Version bump in header and Stable tag
- `PHP 7.4 or higher` in Requirements corrected to `PHP 8.2 or higher`
- Directory Architecture section rewritten: removed deleted `strings.php`; replaced `template-kolofon.php` / `template-mukadimah.php` with `template-colophon.php` / `template-foreword.php`; added `editor.css`, `admin-settings.js`, `blocks.js`, `vendor/` scripts, `blocks/chapter/`, `tag.php`, `upgrading.md`
- `3.4.27` entry added to Changelog section

**`changelog.md`**
- Duplicate `3.4.27` entries consolidated (Masonry/Grid fixes + TOC thumbnails)
- Duplicate `3.4.19` entries consolidated (citation toggle + centred alignment)

**`upgrading.md`**
- `3.4.27` / `3.4.26` milestone rows restored to correct order
- Stale `3.4.1` and `3.4.2` milestone rows removed (consolidated into `3.4.3`)
- Missing `3.4.10` milestone row added

---

# Changelog

All notable changes to Sasterawan are documented in this file.

---

## 3.4.27 — 2026-03-05

### Archive layout and TOC flat-list — Masonry, Infinite Scroll, featured thumbnails

Adds configurable layout and pagination to both archives and the TOC
flat-list (grouping = None). Vendor scripts bundled. Several bug fixes
and the `book-wp-thumb` image size added.

**Vendor scripts (`assets/js/vendor/`)**

- `masonry.min.js` v4.2.2 — cascading column layout
- `imagesloaded.min.js` v5.0.0 — gates Masonry until images settle
- `infinite-scroll.min.js` v5.0.0 — auto-appends next paginated page

Registered but enqueued only when the relevant settings are active.

**New option keys** (registered with defaults — no action required on upgrade):

- `book_wp_archive_layout` — `list` / `grid` / `masonry` (default: `list`)
- `book_wp_archive_pagination` — `paged` / `infinite` (default: `paged`)
- `book_wp_toc_layout` — `list` / `grid` / `masonry` (default: `list`)
- `book_wp_toc_pagination` — `paged` / `infinite` (default: `paged`)

Applies to literary form, category, and tag archives and (for toc_* keys)
to the TOC when grouping = None. Grouped TOC modes are unaffected.

**TOC flat-list (`index.php`)**

Grid, Masonry, and Infinite Scroll support when grouping = None.
Paginated `WP_Query` used in the IS path (transient not used).
Featured image thumbnails (80×80 `book-wp-thumb` hard crop) displayed
when grouping = None. `$show_thumb` parameter added to `book_wp_toc_entry()`.

**`assets/js/frontend.js` — four bug fixes**

1. `DOMContentLoaded` guard missing from layout-init IIFEs
2. Next-page selector was `.page-numbers.next` (wrong); corrected to `.page-numbers.next a`
3. Masonry initialisation ran before fonts loaded — now gated on `document.fonts.ready`
4. Grid/Masonry hover `padding-left` shift suppressed

**Image size `book-wp-thumb` (80×80 hard crop)**

New size registered in `theme-support.php`. Existing images will not have
the crop until regenerated. Run `wp media regenerate --yes` or use the
Regenerate Thumbnails plugin. Without regeneration WordPress falls back to
the full-size image scaled in the browser.

## 3.4.23 — 2026-03-05

### en-US standardisation — file names, CSS classes, UI strings

**Principle**

All identifiers, file names, class names, and UI-facing strings must use
en-US as the primary language. Translations into ms-MY belong exclusively in
`languages/ms_MY.po`. Malay *literary terms* used as form slugs and domain
identifiers (puisi, cerpen, hikayat, syair, karmina, ratapan, nukilan, bait,
vnum, petikan, istilah) are kept — they are domain vocabulary, not UI strings.

**Template files renamed**

`templates/template-kolofon.php`   → `templates/template-colophon.php`
`templates/template-mukadimah.php` → `templates/template-foreword.php`

Internal `Template Name` headers updated to match.

**CSS classes renamed** (global find-replace across all PHP, CSS, JS)

`bwp-kolofon`              → `bwp-colophon-page`
`bwp-kolofon__content`     → `bwp-colophon-page__content`
`bwp-mukadimah`            → `bwp-foreword`
`bwp-mukadimah__content`   → `bwp-foreword__content`
`bwp-nota-penulis`         → `bwp-author-note`
`bwp-nota-penulis__heading` → `bwp-author-note__heading`
`bwp-nota-penulis__text`   → `bwp-author-note__text`
`bwp-petikan-daripada`     → `bwp-citation-label`

**Template registry** (`inc/hooks/filters.php`)

Paths updated to new filenames.
Labels: `'Mukadimah (Foreword)'` → `__( 'Foreword', 'book-wp' )`
        `'Kolofon (Colophon Page)'` → `__( 'Colophon Page', 'book-wp' )`

**UI strings** (`single.php`, `inc/admin/meta-boxes.php`)

`'PETIKAN DARIPADA'`  → `__( 'Cited from', 'book-wp' )`
`'Nota Penulis'`      → `__( "Author's Note", 'book-wp' )`
Checkbox label updated to reference "Cited from" instead of "PETIKAN DARIPADA".

**Option array strings** (`inc/admin/meta-boxes.php`)

All bilingual `'English / Malay'` option labels stripped to English only.
61 entries added to `languages/ms_MY.po` covering all affected strings.
Hint/placeholder text: Malay example phrases replaced with English equivalents.
`"Untuk ibuku yang tercinta"` → `"For my beloved mother"`
`'e.g. 4 baris, 8–12 suku kata'` → `'e.g. 4 lines, 8–12 syllables'`
`'e.g. Bab 3'` → `'e.g. Chapter 3'`

---

## 3.4.22 — 2026-03-05

### Puisi, Syair, Karmina — analytics bar compact mode

**Problem**

Six analytics items (badge, category, read time, words, lines, stanzas) in a
centred 34em column wrap to a second line. Stanza count orphans alone.

**Fix — `assets/css/forms/puisi.css`, `syair.css`, `karmina.css`**

Per-form compact analytics override appended to each file:
`gap: .2em .55em` (was `.3em .9em`); `font-size: .68em` on
`.bwp-analytics__item` and `.bwp-analytics__cat` (was `.75em`);
`letter-spacing: .07em` (was `.09em`). Applied only to those three forms —
wider prose forms retain the standard spacing.

---

## 3.4.21 — 2026-03-04

### Author's Note — repositioned to full-width banner at page top

Author's Note (Nota Penulis) was moved twice to reach its final position.
Final state: full-width banner between the analytics bar and body content,
rendered in `single.php` for all forms when the `book_wp_notes` field is set.

**`single.php`**

`.bwp-author-note` banner added between `book_wp_analytics_bar()` and
`book_wp_dedication()`. Reads `book_wp_notes` directly. Present for all
forms when the field is filled.

**`inc/helpers/colophon.php`**

Author's Note block removed from the colophon entirely — it no longer
renders there. If you have a child theme override of `colophon.php` that
references the notes block, remove that section to avoid double rendering.

**Label** changed from "Author's Note" to "Nota Penulis" then back to
"Author's Note" with the en-US standardisation in 3.4.23.

## 3.4.19 — 2026-03-04

### Hikayat — citation toggle and centred alignment (consolidated)

### Hikayat — "PETIKAN DARIPADA" citation toggle

**Problem**

Hikayat posts can be either original prose written in the hikayat form or
cited passages from other works. Previously there was no way to distinguish
them. The old CSS `::before { content: "Nukilan daripada" }` was hardcoded
and removed in v3.4.13 as incorrect for original works. This version
reintroduces the label correctly, as a conditional toggle.

**`inc/admin/meta-boxes.php`**

Checkbox field `book_wp_hikayat_is_citation` added at the top of the
hikayat meta box section — above the bibliographic fields.
Label: "This is a citation — show 'PETIKAN DARIPADA' above the title".
Description explains: unchecked = own writing; checked = cited passage.

Save handler extended with a `$checkboxes` loop: saves `'1'` when checked,
calls `delete_post_meta()` when unchecked (no stale value left in the DB).

**`single.php`**

Conditional block inserted above `<h1 class="bwp-work__title">`:
renders `<p class="bwp-petikan-daripada">PETIKAN DARIPADA</p>` only when
`book_wp_get_form_slug() === 'hikayat'` AND
`get_post_meta( ..., 'book_wp_hikayat_is_citation', true ) === '1'`.

**`assets/css/forms/hikayat.css`**

`.bwp-petikan-daripada` rule added: UI font, 0.68rem, letter-spacing .16em,
uppercase, muted colour, zero padding, small bottom margin — sits flush
above the title line.

---

---

### Hikayat — centred alignment; PETIKAN DARIPADA label centred

**`assets/css/forms/hikayat.css`**

Title, analytics bar, ornament, and colophon changed from left to centred
alignment to be consistent with the other literary forms. Specifically:
`text-align: center` on `.bwp-work__title` and `.bwp-petikan-daripada`;
`margin: .6em auto 0` on `.bwp-work__title::after` (centred gold rule);
`justify-content: center` on `.bwp-analytics`; `text-align: center` on
`.bwp-ornament` and `.bwp-colophon`. Border-bottom removed from title —
the `::after` gold rule is sufficient. Prose body text remains left-aligned.

**PETIKAN DARIPADA note**

This label appears above the title only when the checkbox
"This is a citation — show PETIKAN DARIPADA above the title" is ticked
in the Literary Work Details meta box for that specific post. It is off
by default. Tick it on posts that cite a passage from another work.

---

---

## 3.4.18 — 2026-03-04

### Karmina and Nukilan — page wrap and column width fixes

**Karmina (`assets/css/forms/karmina.css`)**

`bwp-page-wrap` widened from 26em to 38em. The 26em value matched the verse
content width but crushed the title (two-line wrap), analytics bar (orphaned
items), and tags area. `bwp-work__content` remains 20em centred within the
wider wrapper — the two verse lines still float in whitespace as intended.

**Nukilan (`assets/css/forms/nukilan.css`)**

`bwp-page-wrap` widened from 32em to 46em; `bwp-work__content` from 28em
to 40em — identical to ratapan. Responsive padding at ≥52em adjusted from
2.2em to 2.6em. Title `font-style` changed from `italic` to `normal` —
the body text retains its italic (quotation signal); the heading does not.

## 3.4.15 — 2026-03-04

### Drop cap fonts — IM Fell English SC (hikayat), Libre Caslon Display (nukilan)

**Font rationale**

IM Fell English is a digital revival of typefaces cut by Peter de Walpergen
in the 1670s. The irregular, hand-cut character suits hikayat — a classical
prose form. The SC (Small Caps) variant gives the drop cap initial genuine
inscriptional capitals rather than scaled regular letters.

Libre Caslon Display is a high-contrast display cut at optical sizes above
36pt. It suits nukilan (fragment/excerpt) — a form that foregrounds the
typography of the excerpt itself.

**`inc/setup/enqueue.php`**

Two separate conditional enqueues inside the per-form CSS block:
- `hikayat` → `IM+Fell+English+SC` via Google Fonts
- `nukilan` → `Libre+Caslon+Display` via Google Fonts

Each page fetches exactly one font file — no shared request.

**`assets/css/forms/hikayat.css`**

Drop cap `font-family` set to `'IM Fell English SC'`.

**`assets/css/forms/nukilan.css`**

Drop cap `font-family` set to `'Libre Caslon Display'`.

## 3.4.13 — 2026-03-04

### Literary form headers and analytics bar — standardisation audit

Complete rewrite of all 10 form CSS files following a brainstormed evaluation
of the distinctive character and visual requirements of each form.

**Alignment — two defined groups**

Centred (lyric/classical verse, curated excerpt): syair, karmina, ratapan,
nukilan, puisi. Left (prose and free verse): novel, hikayat, cerpen, esei,
memoir.

Puisi moved from left to centred — sajak is lyric verse, not prose. Previous
left-alignment was incorrect.

**Italic on Cinzel — abolished**

Cinzel has no genuine italic face. Browser-synthesised oblique rendered poorly
on ratapan, syair, hikayat. All three changed to `font-style: normal`. Forms
that genuinely need italic (nukilan, memoir) use Georgia which has a real
italic.

**Per-form changes**

- `hikayat.css` — recharacterised as original compositions in hikayat prose
  form (not exclusively archival citation). Removed: `::before { content:
  "Nukilan daripada" }` (wrong for original works), boxed content area (gold
  borders + background — made own writing look like someone else's excerpt),
  `bwp-epigraph { display: none }`. Column widened to 44em. Title: Cinzel
  normal, left, 2.0em→2.6em.

- `ratapan.css` — column widened from 34em to 46em (chapter numeral float
  needs room). Title: Cinzel normal (was italic), centred, 1.7em→2.0em/2.6em.

- `memoir.css` — autobiographical essay subtype. Title: Georgia italic
  (personal register), left-aligned, 1.85em→1.8em/2.2em. `::before {
  content: attr(data-date-place) }` removed (dead code — attribute never
  set). Warm palette retained.

- `puisi.css` — centred alignment. Title: Cinzel normal, centred, 1.9em→1.8em/2.2em.
  Analytics centred.

- `syair.css` — title: Cinzel normal (was italic — Cinzel has no italic face),
  1.8em→2.2em. Column 34em→32em. Analytics centred.

- `nukilan.css` — column narrowed from 38em to 32em (narrowness is
  intentional for curated fragment). Title: Georgia italic (was Cinzel
  italic — switched to genuine italic face), centred, 1.85em→1.8em/2.2em.

- `karmina.css` — title size raised from 1.3em to 1.5em/1.8em. Was
  undersized to the point of looking accidental; muted colour and uppercase
  do the differentiation work. Analytics centred.

- `cerpen.css`, `novel.css`, `esei.css` — analytics `justify-content:
  flex-start` added. Cerpen title size aligned to large tier (2.0em→2.6em).

**`assets/css/frontend.css` — analytics bar**

`gap: .5em 1em` replaced with `gap: .3em .9em` (row-gap .3em prevents
orphaned single-item second lines on narrow columns). `justify-content:
flex-start` set as default; centred forms override via per-form rule.

---

## 3.4.12 — 2026-03-04

### Comments section redesigned to match theme aesthetic

**`comments.php`** — full rewrite. Previous file used bare `wp_list_comments()`
and `comment_form()` with near-zero customisation, producing default WordPress
browser-stylesheet output. Replaced with:

- Custom `book_wp_comment_callback()` / `book_wp_comment_end_callback()` — full
  BEM markup: `.bwp-comment`, `.bwp-comment__header`, `.bwp-comment__avatar`,
  `.bwp-comment__meta`, `.bwp-comment__actions`, `.bwp-comment__content`
- Section heading uses `.bwp-comments__heading` with Cinzel display font,
  spaced caps, muted colour — matches tag archive heading style
- Ornament dash between heading and list (`.bwp-ornament`)
- Comment date as `<time datetime="c">` with ISO timestamp for semantics
- Reply and edit links styled as pill-shaped outline buttons
- Comment list heading copy in Bahasa Melayu (_n singular/plural)
- Pending comment notice in Malay

`comment_form()` customised via arguments:
- URL field removed (not relevant for a literary anthology)
- Name/email fields in two-column grid row (`.bwp-cf-row`)
- All labels uppercase spaced-caps in UI font
- Textarea replaces WordPress default; placeholder in Malay
- Required field asterisk separated from label via `<span>`
- Cookies consent checkbox using `accent-color: var(--bwp-accent)`
- Submit button as `.bwp-comment-submit` — accent fill, uppercase, full
  focus-visible ring

**`assets/css/frontend.css`**

Old 5-line comment block replaced with ~230 lines covering:
section wrapper, heading, ornament spacing, comment list, nested children
indent with left border, avatar (48px circle), header flex row, author/date
meta, reply/edit pill buttons, comment text column-aligned with avatar,
comment navigation, full form layout (two-column row, labels, inputs,
textarea, checkbox row, submit button), responsive collapse at 540px.

WCAG 1.4.1 underline rule updated from `.bwp-comments .comment-content a`
to `.bwp-comment__content a` to match new class.

---

## 3.4.11 — 2026-03-04

### Gutenberg-native shortcode support — inline format and block registrations

**Root problem**
Three separate issues caused `[term]`, `[quote]`, `[verse]`, and `[divider]` to
fail or break verse structure in Gutenberg:
1. Registered shortcode tags (`bwp_term`, `bwp_quote`, etc.) did not match the
   documented tags (`[term]`, `[quote]`, etc.) — the aliases were never registered.
2. Gutenberg's visual editor converts straight ASCII quotes in attribute values to
   smart/curly Unicode quotes, breaking the shortcode regex parser.
3. `[term]` placed in a Shortcode block became a block-level element, severing the
   inline verse line it belonged to.

**`inc/shortcodes/shortcodes.php` — 3 fixes**

`book_wp_normalize_shortcode_quotes()` — new `the_content` filter (priority 10,
before `do_shortcode` at 11). Uses `preg_replace_callback` on shortcode opening
tags only; converts U+201C/201D/2018/2019 to straight ASCII quotes in attribute
strings. Body copy is untouched.

`book_wp_hydrate_term_spans()` — new `the_content` filter (priority 12). Transforms
`<span class="bwp-istilah" data-definition="...">word</span>` produced by the
Gutenberg Format API into the full tooltip structure (aria-label + nested
`.bwp-istilah__tip` span) that the PHP `[term]` shortcode also outputs. The two
paths produce identical front-end HTML.

`book_wp_kses_allow_term_attrs()` — `wp_kses_allowed_html` filter. Adds
`data-definition`, `tabindex`, `aria-label`, `aria-hidden`, and `role` to the
kses allowlist for `<span>` in post context, so the Format API's serialized
attributes survive WordPress's content sanitiser for non-admin users.

Friendly shortcode aliases registered: `[term]`, `[quote]`, `[divider]`,
`[verse]`, `[chapter]`, `[v]` — point to the same callbacks as their `bwp_`
prefixed canonical counterparts.

**`assets/js/blocks.js` — Format API + 3 new blocks**

`book-wp/term` inline format type (`registerFormatType`). Select any word in
any RichText block → click the book-alt toolbar button → enter definition in
popover → Apply (or press Enter). Format serialises as
`<span class="bwp-istilah" data-definition="...">word</span>`. Hydrated to
full tooltip on the front end by `book_wp_hydrate_term_spans()`. Replaces the
Shortcode block workaround that was breaking verse structure.

`book-wp/quote` block — RichText for content, TextControl for source/attribution
in InspectorControls. Static save: `<blockquote class="bwp-petikan">...</blockquote>`.
Matches PHP `[quote]` shortcode output exactly.

`book-wp/divider` block — SelectControl for ornament style (or global setting).
Server-side rendered (`save: null`) so the SVG always reflects the current
Theme Options ornament. PHP render callback: `book_wp_render_divider_block()`.

`book-wp/verse` block — RichText for content, optional verse number in
InspectorControls (displayed in left margin). Static save:
`<div class="bwp-bait">...</div>`. Matches PHP `[verse]` shortcode output exactly.

Function wrapper signature updated: `window.wp.richText` added as third argument.

**`inc/setup/enqueue.php`**
`wp-rich-text` added to `book-wp-blocks` script dependencies (required for
`registerFormatType` and the `applyFormat`/`removeFormat` utilities).

**`inc/hooks/actions.php` — `book_wp_register_blocks()`**
`book-wp/divider` block type registered with `book_wp_render_divider_block()`
as the PHP render callback. Delegates to `book_wp_ornament()` — same helper
used by the `[divider]` shortcode.

**`assets/css/editor.css`**
Editor canvas styles added for:
- `.bwp-istilah` — dotted underline; tooltip hidden in editor
- `.bwp-term-popover` — definition input popover layout
- `.bwp-petikan` — quote block preview
- `.bwp-ornament-preview` — divider block placeholder label
- `.bwp-bait` / `.bwp-bait__num` — verse block with outdented number

**`inc/admin/settings.php`**
Shortcode help block updated to document correct tag names and note the
block editor toolbar button for term definitions.

---

## 3.4.10 — 2026-03-04

### `inc/helpers/navigation.php` — integrity and efficiency audit (6 fixes)

**I1 — Stale docblock corrected**
`Depends on` comment removed `strings.php` (deleted v3.3.51) and added
`actions.php` (source of `book_wp_hidden_tag_slugs()`).

**I2 — `$used[]` now updated in both candidate loops**
Second loop (same literary form) now appends accepted post IDs to `$used`,
matching the first loop. Prevents theoretical duplicate entries if a third
pass is ever added.

**I3 — Previous/Next navigation now skips hidden-tagged posts**
`book_wp_work_navigation()` replaced single `get_previous_post()` /
`get_next_post()` calls with a `$find_adjacent` closure that walks adjacent
posts in a loop, skipping any that carry a hidden-trigger tag. A `$limit`
guard (10 iterations) prevents runaway loops against pathological archives.

**I4 — Over-fetch buffer scaled to `max( $max * 2, $max + 4 )`**
Previous `+1` buffer was insufficient when multiple hidden posts occupy a
batch. New formula ensures enough candidates survive hidden-post skips to
fill all slots without a second query.

**E1 — N+1 query pattern eliminated in `$is_hidden` check**
Replaced per-post `wp_get_post_tags()` inside the loop with a
`$make_hidden_check` factory closure. The factory calls
`wp_get_object_terms( $ids, 'post_tag', ['fields' => 'all_with_object_id'] )`
once per batch, builds a `post_id → [slugs]` map, and returns a lightweight
closure that checks against the map. Maximum of 2 term queries per page load
(one per pass) regardless of batch size.

**E2 — `count()` loop condition replaced with `$filled` counter**
`count( $related )` on every iteration replaced with a `$filled` integer
incremented on each accepted candidate. Semantically clearer; avoids repeated
function calls even though PHP `count()` is O(1).

---

## 3.4.9 — 2026-03-04

### Hidden-tagged posts excluded from Related Works

**`inc/helpers/navigation.php` — `book_wp_related_works()`**

Added `$is_hidden` closure using `book_wp_hidden_tag_slugs()`. Applied as a
`continue` guard in both candidate-collection loops:

- Same source/collection loop — hidden-tagged posts skipped before being
  pushed to `$related`
- Same literary form fill loop — same guard applied

No change to the query arguments or slot count logic. If all candidates in a
batch are hidden, the function simply returns fewer (or zero) related works
rather than exposing hidden posts.

---

## 3.4.8 — 2026-03-04

### Search results page redesigned to match tag archive

**`search.php`** — complete rewrite. Old structure (`bwp-search-page` wrapper,
plain h1, bare quoted paragraph) replaced with the same `bwp-archive-header`
pattern used by `tag.php`:
- Leaf ornament at top
- `bwp-archive-header__taxlabel` — "Search Results" / "Hasil Carian"
- Search query displayed as `bwp-tag bwp-tag--lg` pill (same visual weight
  as the tag name on the tag archive page)
- Dash ornament below
- Result list items now include literary form badge and year, matching `tag.php`

**`assets/css/frontend.css`**
- Added `bwp-archive-header--search` modifier alongside `--tag` to neutralise
  the h1 so the pill carries the visual weight
- Removed orphaned `.bwp-search-page` and `.bwp-search-page__title` rules
  (no longer referenced anywhere). `.bwp-404` rules kept intact.

---

## 3.4.7 — 2026-03-04

### Maintenance + tag cap raised to 5

**footer.php** — ABSPATH guard moved to line 1 (was line 14, after docblock).

**colophon.php** — Karmina `Sampiran` and `Isi` labels wrapped in `esc_html__()`. Were bare hardcoded PHP string literals, invisible to gettext.

**settings.php** — `echo $active ? ... : ...` replaced with `echo esc_attr( $active ? ... : ... )` in tab renderer. Previous phpcs:ignore comments removed.

**style.css** — `License URI: https://www.gnu.org/licenses/gpl-2.0.html` added. Was missing despite licence being declared GPL-2.0-or-later since v3.4.5.

**single.php** — `wp_link_pages()` added after `the_content()`. Required classic-theme function was absent.

**shortcodes.php** — All six shortcodes renamed to `bwp_` prefix: `[bwp_quote]`, `[bwp_divider]`, `[bwp_term]`, `[bwp_verse]`, `[bwp_chapter]`, `[bwp_v]`.

**readme.txt** — `== Upgrade Notice ==` section added.

**Tag archive page redesign (`tag.php`, `frontend.css`)**
The tag archive header now uses a dedicated layout:
- `bwp-archive-header--tag` modifier class on the wrapper
- `<p class="bwp-archive-header__taxlabel">Tagged with</p>` in small-caps above the title — translatable string, styled to match `.bwp-colophon__karmina-label` (`.68em`, uppercase, `--bwp-muted`, `.12em` tracking)
- `<h1>` wraps a `<span class="bwp-tag bwp-tag--lg">` — tag name rendered as a large pill rather than bare text; `pointer-events: none` since it is not a link
- `.bwp-tag--lg` modifier added to `frontend.css`: `.85rem`, `30px` radius, larger padding
- `.bwp-archive-header--tag .bwp-archive-header__title` rule neutralises the h1's `1.9em` to `1rem` so the pill carries the visual weight

**`bwp-tags` block — centred and § glyph added**
- `.bwp-tags` gains `justify-content: center` — pills are now centred beneath the content rule
- `.bwp-tags::before { content: '§' }` — full-width flex row above the pills; same display font, `.72em`, `.12em` tracking, `--bwp-muted`, `opacity: .7`. Matches the hairline label style of the colophon

**Search exclusion for hidden-tagged posts**
`book_wp_hide_from_category_archive()` extended to also apply to `is_search()` main queries. Posts tagged with any hidden-trigger slug now disappear from WordPress search results even when the query matches their content or title.

**`BOOK_WP_MAX_TAGS`** — raised from 3 to 5. Existing posts with 4–5 thematic tags will no longer be trimmed on next save.

**Languages** — Eight tag-feature strings added in v3.4.6 were absent from all translation files. Added to ms_MY, fr_FR, es_ES PO/MO and the POT: `Tags`, `Untagged`, `Hidden Post Tags`, `Show post tags…`, `Search and select hidden tags`, `Select tags…`, `No tags found…`, hidden-tags field description. `Tagged with` (tag archive label) added for this release. Also synced: `Sampiran`, `Isi`, `Page navigation`. All MO files recompiled (182 entries each). POT updated to 181 msgids.

---

## 3.4.6 — 2026-03-04

### Tag support and hidden-post feature

**New constant — `BOOK_WP_MAX_TAGS`**
Defined in `functions.php`. Hard-coded to `3`. Controls the maximum number of
thematic tags per post. Hiding-trigger tags are excluded from this count.

**Tag cap enforcement**
Two hooks in `inc/hooks/actions.php`:
- `save_post` → `book_wp_enforce_tag_cap()` (classic editor)
- `rest_after_insert_post` → `book_wp_enforce_tag_cap_rest()` (block editor)

Trigger tags are always preserved. Thematic tags trimmed to first 5 if over cap.
Recursion guard (`remove_action` / `add_action`) prevents infinite loop.

**`book_wp_hidden_tag_slugs()` helper**
Returns the saved hidden-trigger slug list as an array. Statically cached per
request. Used by schema output, index.php, single.php, and the category filter.

**Hidden-post feature**
Posts tagged with any slug listed in `book_wp_hidden_tags` are:
- Removed from the TOC (all four grouping modes) via post-loop filter in `index.php`
- Excluded from category archives via `pre_get_posts` filter (main query only)
- Still accessible at their permalink and via their tag archive page

Trigger tags are suppressed from the `bwp-tags` display block and from
schema `keywords` output on the single post.

**TOC grouping — By Tag (fourth mode)**
`index.php` by-tag block: posts appear under every thematic tag they carry.
Groups sorted alphabetically. Posts with no thematic tags fall into "Untagged"
at the bottom. Hidden-trigger tags never appear as group headings.

**`bwp-tags` block on single posts**
Rendered between work content and colophon. Gated on `book_wp_show_tags` toggle
(default on) and at least one thematic tag being present. Each tag links to its
archive. Trigger tags suppressed from display.

**`tag.php` — dedicated tag archive template**
New file. Uses `single_tag_title()` for heading. Displays tag description if
set. All posts shown (no hiding filter on tag archives). Reuses `.bwp-archive-*`
classes. No new CSS required for the template.

**Frontend CSS — `.bwp-tag` styles**
Pill-style tags: uppercase spaced-caps, thin border, muted colour, rounded.
Hover state reveals text colour and background tint. Full night mode guard
for `bwp-night`, `bwp-scheme-night`, `bwp-time-night`.

**Admin — `book_wp_admin_assets()`**
`admin-settings.js` now enqueued on the theme settings page only.

**Admin — new options registered**
- `book_wp_show_tags` (checkbox, default `1`) — Literary tab, Content Display section
- `book_wp_hidden_tags` (comma-separated slugs) — Literary tab, Tag Settings section

**Admin — custom tag multi-select widget**
`assets/js/admin-settings.js` (252 lines, vanilla JS, no external dependencies).
Pill box with per-tag remove buttons, live search filter, keyboard navigation
(↑↓ arrows, Enter to select, Escape to close, Backspace to remove last pill).
Styled entirely via `admin.css` tokens. Loaded only on the theme settings page.

**`book_wp_sanitize_tag_slugs()`**
New sanitizer in `inc/admin/settings.php`. Explodes on comma, runs each value
through `sanitize_key()`, implodes back. Rejects anything that is not a valid
slug.

**Transient invalidation**
`updated_option` hook clears TOC transient cache when `book_wp_hidden_tags` is
updated, so changes take effect immediately without waiting for TTL expiry.

**Schema `keywords`**
`book_wp_schema_output()` now adds `keywords` property from thematic tags
(trigger tags excluded). Only emitted when at least one thematic tag is present.

---

## 3.4.5 — 2026-03-04

### Compliance audit fixes (10 issues)

**F1 — License declaration corrected**
`style.css` and `readme.txt` declared `BSD` but the `LICENSE` file contained
GPL v3 text. All three now consistently declare `GPL-2.0-or-later`. The `LICENSE`
file has been replaced with a proper GPL-2.0-or-later notice.

**F2 — CRLF line endings normalised to LF**
Five files contained Windows-style CRLF line endings:
`inc/helpers/navigation.php`, `inc/helpers/analytics.php`,
`inc/admin/settings.php`, `inc/admin/meta-boxes.php`, `inc/hooks/actions.php`.
All converted to LF.

**F3 — Settings capability changed from `manage_options` to `edit_theme_options`**
Three occurrences in `inc/admin/settings.php`: `add_theme_page()` capability
argument and both `current_user_can()` checks. `edit_theme_options` is the
correct scope for theme configuration — does not reduce admin access.

**F4 — Links in body and comment content now underlined (WCAG 1.4.1)**
Added to `assets/css/frontend.css`:
```css
.bwp-work__content a,
.bwp-comments .comment-content a { text-decoration: underline; }
```
Global `a { text-decoration: none }` remains; underline is scoped to content
areas only per the accessibility rule.

**F5 — Google Fonts remote call documented in `readme.txt`**
New `== Privacy & Data Collection ==` section added. Documents that no data is
collected by default, explains the opt-in Google Fonts call (only fires when a
non-default font is selected), and describes the self-hosting path.

**F6 — FAQ section added to `readme.txt`**
New `== Frequently Asked Questions ==` section with 8 entries covering anthology
setup, literary forms, colophon display, progress bar, colour schemes, RTL
support, and child theme usage.

**F7 — Duplicate `ABSPATH` guard removed from `index.php`**
`index.php` had two `defined( 'ABSPATH' ) || exit;` calls — one at line 1 and
one inside the template body at line 15. The second (orphaned) guard removed.

**F8 — `ABSPATH` guard moved to top of `archive.php`**
`archive.php` had the guard buried inside the template body after `get_header()`
had already executed. Moved to line 1 before `get_header()`.

**F9 — Settings section in `readme.txt` updated**
Previously described only five generic tab summaries with incomplete field lists.
Now reflects current v3.4.4 feature set: copyright textarea with HTML support,
show/hide copyright toggle, all seven font options, all four colour schemes,
all ornament styles, all literary forms count corrected (10, not 3).

**F10 — `echo count()` given explicit `(int)` cast in `index.php`**
Four occurrences of `echo count( $array )` now use `echo (int) count( $array )`
for explicit type safety. No functional change.

---

## 3.4.4 — 2026-03-04

### Copyright toggle + HTML support in settings textareas

**inc/admin/settings.php:**

- `book_wp_copyright` — field type changed from `<input type="text">` to
  `<textarea rows="2">`. Sanitizer changed from `sanitize_text_field` to
  `wp_kses_post` in both registration maps. Field hint added noting HTML support.

- `book_wp_show_copyright` — new toggle field (checkbox, default on). Controls
  whether the copyright line appears in the site footer. Registered with
  `book_wp_cb` sanitizer in both registration maps.

- `book_wp_front_blurb` — no functional change. Hint updated to note that HTML
  is supported (`wp_kses_post` sanitizer was already in place from v3.3.23).

**footer.php:**

- Anthology name and author name always display.
- Copyright line gated independently:
  `! empty( $bwp_copyright ) && '1' === get_option( 'book_wp_show_copyright', '1' )`
- Toggle off hides the copyright line only — anthology name and author remain.

---

## 3.4.3 — 2026-03-04

### Footer redesign + search form translations (consolidated from 3.4.1, 3.4.2, 3.4.3)

**footer.php — colophon layout (3.4.1):**
- Ornament removed from footer (content templates own their closing ornaments)
- New `.bwp-site-footer__inner` flex wrapper
- `.bwp-site-footer__colophon`: anthology name (display font, spaced caps), author name, copyright — all drawn from existing theme options
- `.bwp-site-footer__credit`: Sasterawan by MENJ separated by thin rule, opacity .55
- `::before` pseudo-element: centred ✦ on the border-top rule replaces duplicate SVG ornament

**footer-widget search form (3.4.1 — was completely unstyled):**
- `.bwp-footer-widget .search-form`: flex layout, max-width 22em, centred
- `.bwp-footer-widget .search-field` / `.search-submit`: match sidebar styling
- Night mode guard added for footer search submit button
- `.bwp-footer-widgets` gains `border-top` + `padding-top` separator

**searchform.php — search button translatable (3.4.2):**
The button previously rendered `&rarr;` (→) as its label — a glyph, not a string. Changed to `esc_html_e( 'Search', 'book-wp' )`. The `aria-label` attribute removed — redundant when visible text is present. No `.po`/`.mo` changes needed; `"Search"` was already compiled into all three `.mo` files.

**footer.php — copyright/colophon deduplication (3.4.3):**
The footer previously showed anthology name, author name, and copyright string as three separate lines — causing duplication when the copyright field contains the full attribution. Logic now:
- If `book_wp_copyright` is set: show it alone as the single attribution line.
- If not set: show `book_wp_anthology_name` and `book_wp_author_name` separately. Never both.

**inc/hooks/filters.php — Search block button translation (3.4.3):**
The Gutenberg `core/search` block renders its own HTML and does not call `searchform.php`. Added `render_block_core/search` filter (`book_wp_translate_search_block`) that rewrites the rendered HTML, replacing the button text with `__( 'Search', 'book-wp' )`. Existing `ms_MY` (Cari), `es_ES` (Buscar), `fr_FR` (Rechercher) translations apply automatically.
## 3.4 — 2026-03-04

### Critical bugfix — colophon.php parse errors (site-wide breakage)

**Root cause:** Nine orphaned PHP concatenation lines in `inc/helpers/colophon.php`.
Each was a duplicate of the line above it — a leftover from an editing artifact where
a multi-line `$html .=` statement was accidentally split, leaving the tail fragment
as a bare statement beginning with `.` (the string concatenation operator).

In PHP, a statement beginning with `.\ at the top level of a function body is a
**parse error** (`T_DOT` is not a valid statement opener). Since `colophon.php` is
loaded unconditionally via `functions.php → require_once`, every page request
triggered a fatal error, breaking the entire site.

**`php -l` did not catch this** because the erroneous lines do not cause a parse
error in all PHP lint contexts — behaviour depends on PHP version and error reporting
level. The breakage was confirmed by static analysis of statement-opener tokens.

**Lines removed (all in `inc/helpers/colophon.php`):**

| Line | Form | Duplicate of |
|------|------|--------------|
| 121 | Syair | `book_wp_melody` |
| 138 | Karmina | `book_wp_rhyme_scheme` |
| 153 | Hikayat | `book_wp_protagonist` |
| 156 | Hikayat | `book_wp_manuscript` |
| 159 | Hikayat | `book_wp_editor_transcriber` |
| 162 | Hikayat | `book_wp_folio_page` |
| 174 | Puisi | `book_wp_sequence` |
| 177 | Puisi | `book_wp_performance_history` |
| 228 | Ratapan | `book_wp_addressee` |

No logic changes. No functional output changes. Only the fatal duplicate lines removed.

---

## 3.3.59 — 2026-03-04

### Ratapan — chapter numeral layout reverted to biblical float model

**Change:** The chapter/verse apparatus in `ratapan.css` previously used CSS Grid
to place the chapter numeral in a narrow left column with verse content in a right
column. This produced a rigid two-column look that did not resemble printed Bible
typography — the numeral sat isolated, text did not wrap around it.

**New layout:** Float-based, matching ESV/NIV chapter-opening convention.
The chapter numeral (`.bwp-chapter__num`) floats left at `4.2em`, verse text
wraps naturally around it across however many lines are needed. The chapter title
(`.bwp-chapter__title`) sits above as a full-width block before the float begins.
A `::after` clearfix on `.bwp-chapter` ensures each chapter opens cleanly below
the previous one.

**Unchanged:** Verse number styling (`.bwp-vnum`), verse 1 number suppression,
verse paragraph tight-packing, chapter title small-caps treatment — all preserved.

---

## 3.3.58 — 2026-03-04

### Bug fix — Night mode: unguarded palette overrides + missing third dark-mode class (consolidated from 3.3.57, 3.3.58)

Sasterawan has three independent dark-mode mechanisms:
- `bwp-night` — floating toggle button (set on `body` by JS)
- `bwp-scheme-night` — persistent Night scheme (set on `body` by PHP)
- `bwp-time-night` — clock-driven Night period (set on `:root` by JS)

**Root cause (3.3.57):** `ratapan.css`, `hikayat.css`, and `memoir.css` set dark ink tokens directly on `body.bwp-form-*` with no Night-mode guard. Because form CSS loads after scheme CSS, these light-mode values (e.g. `--bwp-text: #1a1a1a`) overwrote the Night scheme's light ink tokens, producing dark text on a dark background.

**Additional gap (3.3.58):** The 3.3.57 fix guarded against `bwp-scheme-night` and `bwp-time-night` but omitted `bwp-night`. A visitor using the floating toggle on a Ratapan, Hikayat, or Memoir page still got dark ink on a dark background.

**Files changed:**
- `assets/css/forms/ratapan.css` — light guard: `:root:not(.bwp-time-night) body.bwp-form-ratapan:not(.bwp-scheme-night):not(.bwp-night)`; Night blocks added with desaturated silver-blue links and ash gold accents
- `assets/css/forms/hikayat.css` — same guard applied; Night block preserves manuscript amber with warm links on dark
- `assets/css/forms/memoir.css` — same guard applied; Night block adds warm amber links

Automated audit across all 10 form CSS files confirmed all palette-overriding selectors carry all three `:not()` guards. All other forms carry no ink or surface overrides and are unaffected.

---

## 3.3.56 — 2026-03-03

### i18n — Complete translation audit: all three locales fully covered

**PHP extraction:** 152 plain strings + 2 contextual (_x) extracted. All compared
against ms_MY, es_ES, fr_FR.

**Fixed:**
- 74 meta-box field labels missing from all three locales — added with translations
- 22 bwp_toggle() labels/descriptions missing from all three locales — added
- 7 front-end structural strings missing (Footer, Primary Menu, Search, Skip to content, etc.)
- 4 taxonomy/hook strings missing (Literary Forms, Toggle dark mode, etc.)
- 7 form short labels missing from es_ES and fr_FR only
- 7 orphaned entries removed from all three files (Back to Contents, Continue reading,
  From the same collection, In, More in this form, Works in this form, posted)

**Result:** Each .po file: 152 entries, 0 empty msgstr. .mo files recompiled.

---

## 3.3.55 — 2026-03-03

### UX — Colour Scheme and Time-of-Day merged into single Reading Palette section

The two palette controls appeared as separate sections, implying competing
alternatives. Merged into a single **"Reading Palette"** section with
explanatory copy: *"Colour Scheme sets the paper. Time of Day sets the ink.
Your chosen paper holds across all time periods; the ink shifts with the light."*

Two sublabels within the section — **"Paper — Colour Scheme"** and
**"Ink — Time of Day"** — make the relationship explicit. Hint text updated
to reinforce the paper/ink model. No logic, CSS, or JS changed.

---

## 3.3.54 — 2026-03-03

### Fix — R1 (partial): Scheme and time-of-day now complement rather than collide

**Problem:**
Both systems defined all CSS custom property tokens on `:root`. Specificity was
identical (0,1,0). Whichever stylesheet loaded last won everything — scheme
selection had no effect during dawn, morning, dusk, or night because
`book-wp-times` was loaded first and its higher-specificity `:root.bwp-time-*`
selectors overrode the flat `:root` scheme selectors.

**Architecture (token ownership split):**

| System | Element | Owns |
|--------|---------|------|
| Time of day | `<html>` (via `time-theme.js`) | Ink tokens — text, links, accents, ornaments |
| Scheme | `<body>` (via PHP `body_class()`) | Surface tokens — backgrounds, borders, stone |

CSS custom properties set directly on `<body>` always beat values inherited
from `<html>`. No specificity rules needed — the DOM cascade handles it.

**`assets/css/times/times.css`** — surface tokens removed from all periods.
Each period now sets ink only: `--bwp-text`, `--bwp-text-mid`, `--bwp-muted`,
`--bwp-link`, `--bwp-link-hover`, `--bwp-accent`, `--bwp-ornament`,
`--bwp-progress`, `--bwp-border-ink`, `--bwp-teal`, `--bwp-gold`.
Night period retains a surface fallback block (for the default scheme where
no scheme file is loaded and dark surface must still appear).

**`assets/css/schemes/cream.css`** — rewritten. Selector changed from `:root`
to `body.bwp-scheme-cream`. Defines surface tokens only. Four compound
selectors (`:root.bwp-time-{period} body.bwp-scheme-cream`) tune the paper
tint to complement each ink mood: cooler/misty at dawn, warmest amber at
morning, ochre parchment at dusk, dimmed lamp-lit at night.

**`assets/css/schemes/dusk.css`** — rewritten. Same pattern: `body.bwp-scheme-dusk`
surface base + four time-tuned compound selectors. Warm grey cools at dawn,
warms through morning, deepens at dusk, dims to graphite at night.

**`assets/css/schemes/night.css`** — rewritten. Night scheme is the exception:
a dark background requires light ink, so `body.bwp-scheme-night` sets both
surface and ink tokens (cannot rely on time-inherited ink — dark bg + dark ink
is unreadable during afternoon). Four compound selectors then re-introduce
time-of-day character by tinting link, accent, ornament, and gold tokens:
violet-silver at dawn, amber warmth at morning, ochre ember at dusk, deepest
navy at night.

**`inc/setup/enqueue.php`** — scheme dependency updated from `book-wp-frontend`
to `book-wp-times`. Load order is now: `frontend → literary → times → scheme`.
Scheme compound selectors require times to already be in the cascade.

**Result:**
- Selecting "Cream" now always shows cream paper, with ink mood shifting
  through lavender at dawn, amber at morning, ochre at dusk, cooled at night.
- Selecting "Dusk" shows warm grey paper with the same ink progression.
- Selecting "Night" always shows dark mode, with accent/link colours shifting
  subtly through the time periods.
- Default scheme (no file) retains full time palette control as before.

---

## 3.3.53 — 2026-03-03

### Fix — Body class sanitisation, phpcs suppression for boolean echo

**Fix 1 — `sanitize_key` for options concatenated into body class strings
(`inc/admin/settings.php`, `inc/hooks/filters.php`):**

Five options were saved with `sanitize_text_field` but used directly in HTML
`class` attribute strings via `body_class`. `sanitize_text_field` does not strip
spaces, which could allow a crafted DB value to inject additional class tokens.
`sanitize_key` (restricts output to `a-z 0-9 _ -`) is the correct sanitiser for
values used as CSS class name suffixes.

Fields changed from `sanitize_text_field` to `sanitize_key` in the settings save
map (both registration and save handler):
- `book_wp_color_scheme` → `bwp-scheme-{value}`
- `book_wp_font` → `bwp-font-{value}`
- `book_wp_stanza_spacing` → `bwp-stanza-{value}`
- `book_wp_poetry_line_height` → `bwp-poetry-lh-{value}`

Additionally, all five class concatenations in `filters.php` `book_wp_body_classes()`
now wrap the option value in `sanitize_key()` on read — defence in depth covering
`book_wp_language` (which has no settings UI and can only be set directly in DB):
- `'bwp-scheme-' . sanitize_key( get_option( ... ) )`
- `'bwp-font-'   . sanitize_key( get_option( ... ) )`
- `'bwp-lang-'   . sanitize_key( get_option( ... ) )`
- `'bwp-poetry-lh-' . sanitize_key( $lh )`
- `'bwp-stanza-' . sanitize_key( $spacing )`

Text and textarea meta fields, front-blurb, author name, and copyright retain
`sanitize_text_field` / `sanitize_textarea_field` — correct for free-form text.

**Fix 2 — `phpcs:ignore` on boolean echo in admin tab nav
(`inc/admin/settings.php` lines 209, 211):**

`$active` is a PHP boolean derived from comparing two admin-controlled slugs —
it can only ever output `''`, `' bwp-tab--active'`, `'true'`, or `'false'`.
Never user input. Added targeted suppression:
`// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- boolean, not user input`

---

## 3.3.52 — 2026-03-03

### Fix — R15: CI pipeline, release checklist, stale enqueue handle

**R15 — Regression and CI infrastructure:**

`composer.json` added. Requires `squizlabs/php_codesniffer ^3.9`,
`wp-coding-standards/wpcs ^3.1`, and `phpcompatibility/phpcompatibility-wp ^2.1`.
Scripts: `composer run lint:php` (phpcs), `composer run lint:php:fix` (phpcbf).

`.phpcs.xml.dist` added. Ruleset: `WordPress-Core` + `WordPress-Extra` with
`Squiz.Commenting.FunctionComment`, `Generic.Commenting.DocComment`, and
`Squiz.Commenting.FileComment` excluded (docblock requirement too noisy for
a theme with template partials). PHP compatibility target: 8.0+, WP minimum: 6.3.
Text domain enforced as `book-wp`. Global prefix set to `book_wp`/`bwp`/`BOOK_WP`.

`package.json` added. Dev dependencies: `eslint ^8.57`, `stylelint ^16.3`,
`stylelint-config-standard ^36`. Scripts: `lint:js`, `lint:js:fix`, `lint:css`,
`lint:css:fix`, `lint` (runs both).

`.eslintrc.json` added. ES5 environment (existing JS uses `var` and IIFEs — no
build step). Globals: `wp` (readonly), `bookWP` (readonly). Key rules: `no-undef`,
`no-unused-vars`, `no-eval`, `no-new-func`, `no-restricted-syntax` blocking
`innerHTML` assignments. `no-var` off (ES5 codebase).

`.stylelintrc.json` added. Extends `stylelint-config-standard`. BEM class pattern
enforced for `bwp-` prefix. Custom property pattern enforced: `--bwp-*`. 
`no-descending-specificity` disabled (intentional cascade overrides).

`.github/workflows/ci.yml` added. Four jobs:
- `phpcs` — runs on push to `main`/`develop` and all PRs to `main`. PHP 8.2.
  Outputs as checkstyle annotations via `cs2pr`.
- `eslint` — runs on same triggers. Node 20.
- `stylelint` — runs on same triggers. Node 20.
- `php-compatibility` — separate job, PHPCompatibilityWP for PHP 8.0+.
- `lighthouse` — commented out. Requires a deployed preview URL and
  `LHCI_GITHUB_APP_TOKEN` secret. Uncomment when hosting is configured.
Concurrency: cancel in-progress runs on the same branch.

`lighthouserc.json` added. Performance budget:
- Performance score ≥ 90 (error)
- Accessibility score ≥ 90 (error)
- LCP ≤ 2500ms (error), CLS ≤ 0.1 (error)
- FCP ≤ 1800ms (warn), TBI ≤ 200ms (warn)
- `color-contrast`, `html-has-lang`, `link-name` (error)
- `render-blocking-resources`, `unused-css-rules` (warn)

`release-checklist.md` added. Covers: CI checks, version consistency, translation
(including `msgfmt` commands for `.mo` regeneration), browser smoke test across
all forms and time-of-day palettes, responsive breakpoints, print, performance,
packaging, and post-release.

`.gitignore` added. Excludes: `vendor/`, `node_modules/`, `*.zip`, `*.tar.gz`,
`composer.lock`, `package-lock.json`, `.DS_Store`, `.vscode/`, `.lighthouseci/`.

**Stale enqueue handle renamed:**
`book-wp-sastera` → `book-wp-literary` in `enqueue.php`. All three occurrences
updated (the `wp_enqueue_style` call for `literary.css` and both dependency
arrays that referenced it). The `sastera` name was a holdover from an earlier
theme identity; `literary` correctly describes the file's purpose.

**JS version headers updated:**
All three JS files now carry version `3.3.52`:
`frontend.js`, `time-theme.js` (were on 3.3.48), `blocks.js` (`@version` header
added — was missing entirely).

---

## 3.3.51 — 2026-03-03

### Fix — D1: Dual translation system eliminated

**Summary:** `inc/helpers/strings.php` deleted. All 40+ front-end strings
migrated to WordPress gettext (`__()`). The theme now has a single translation
system — translators using Poedit, Loco Translate, or GlotPress see every string.

**Changes:**

`inc/helpers/strings.php` — **deleted.**
The custom bilingual string function (`book_wp_strings()` + `book_wp_str()`)
and its hardcoded PHP locale-switching array are gone. 40 `book_wp_str()`
call sites across 16 files replaced.

`functions.php` — `require_once strings.php` removed. Comment added noting
the removal and migration version.

`inc/helpers/literary.php` — three changes:
- `book_wp_form_label( $slug )` added — new function mapping literary form
  taxonomy slugs to translated display labels via `__()`. Used wherever a
  form name needs to be shown to the reader (badges, TOC groups, related works).
  Replaces the dynamic `book_wp_str( $slug )` pattern.
- `book_wp_get_form_label()` updated to call `book_wp_form_label()`.
- `book_wp_form_description()` migrated — custom `$is_ms` locale detection
  replaced with 10 `__()` calls. All 10 form description strings now in the
  gettext pipeline.
- `'Depends on: strings.php'` removed from docblock.

**16 call-site files updated** (40 replacements total):
`404.php`, `archive.php`, `comments.php`, `footer.php`, `index.php`,
`page.php`, `search.php`, `searchform.php`,
`templates/template-kolofon.php`, `templates/template-mukadimah.php`,
`templates/single-puisi-generic.php`,
`inc/helpers/analytics.php`, `inc/helpers/colophon.php`,
`inc/helpers/navigation.php`, `inc/shortcodes/shortcodes.php`,
`inc/hooks/actions.php`

**`languages/ms_MY.po`** — created (new). 206 lines. Full Malay translations
for all front-end strings, form labels, and form descriptions. Translated from
the old strings.php Malay column. Plural-Forms set to `nplurals=1; plural=0`
(Malay has no grammatical plural).

**`languages/book-wp.pot`** — created (new). Extraction template for future
translators. All msgstr values empty.

**`languages/es_ES.po`** and **`languages/fr_FR.po`** — front-end strings
appended. Previously these files only contained admin/UI strings (menus,
sidebar, meta-box labels). All front-end strings now present, translated from
the old strings.php `$es`/`$fr` columns. Version header updated to 3.3.51.

**What changed for visitors:**
- None. All translations carry over exactly from the old strings.php values.
  The Malay, Spanish, and French text visible on screen is identical.

**What changed for developers / translators:**
- One system instead of two. Poedit/Loco Translate now sees all strings.
- Adding a new language requires only a `.po` file — no PHP edits.
- Dead code eliminated (~80 lines of PHP).

---

## 3.3.50 — 2026-03-03

### Fix — Security hardening, performance (time CSS), transient cache, block alignment

**S1 — REST API meta exposure (`inc/hooks/actions.php`):**
`book_wp_register_meta()` split into two groups. All 50+ scholarly and
personal meta fields (`book_wp_annotation`, `book_wp_verification_note`,
`book_wp_thesis`, `book_wp_abstract`, etc.) now registered with
`show_in_rest: false` — no longer readable via `/wp-json/wp/v2/posts/{id}/meta`
without authentication. Only the two fields required by the block editor
(`book_wp_chapter_number`, `book_wp_chapter_part`) retain `show_in_rest: true`.
Literary form taxonomy `show_in_rest: true` unchanged (required for block editor
term selector). Detailed comment block added explaining the policy.

**S2 — CSS output sanitiser corrected (`inc/admin/settings.php`):**
Custom CSS was saved with `sanitize_textarea_field` (introduced in v3.3.49),
which strips angle brackets — breaking `>` child combinator, `calc()`, and
`content:` values. Reverted to `wp_strip_all_tags` on save (correct: strips
HTML tags, preserves CSS syntax). Output changed from `sanitize_textarea_field($css)`
to raw `$css` — safe because the value was already sanitised on save.

**S3 — Scheme and font path whitelist (`inc/setup/enqueue.php`):**
`sanitize_file_name()` replaced with explicit whitelist validation.
- Scheme: `in_array($scheme, ['cream','dusk','night'], true)` — unknown
  slugs bail early, no file path constructed.
- Font: `isset($gfonts[$font])` now gates both the Google Fonts URL and the
  local font CSS path. `$gfonts` array is the whitelist. Unknown font slugs
  return early from `book_wp_enqueue_frontend()`.

**S4 — Shortcode numeric attributes cast (`inc/shortcodes/shortcodes.php`):**
All numeric shortcode attributes now cast with `intval()` immediately after
`shortcode_atts()`:
- `[chapter]`: `$atts['num']` and `$atts['verse_start']` cast with
  `max(1, intval(...))` — minimum value 1, rejects non-numeric input.
- `[verse]`: `$atts['num']` cast with `intval()`.
- `[v]`: `$atts['num']` cast with `intval()`; empty/zero returns early.

**D2 — Five time CSS files merged into one (`assets/css/times/`):**
`dawn.css`, `morning.css`, `afternoon.css`, `dusk.css`, `night.css` merged
into `times.css`. All five palettes coexist as `:root.bwp-time-{period}`
selectors. `enqueue.php` updated from `foreach` loop of 5 enqueues to a
single `wp_enqueue_style('book-wp-times', .../times/times.css)`. Old files
removed. Same payload, 4 fewer HTTP requests per page load.

**D3 — TOC query transient cache (`index.php` + `inc/hooks/actions.php`):**
`get_posts(numberposts: -1)` on every index page load replaced with a
12-hour transient (`book_wp_toc_posts`). Cache populated on miss, stored
with `set_transient()`. Invalidated automatically via three new hooks:
`save_post`, `trashed_post`, `untrashed_post` — all call
`book_wp_clear_toc_cache()` which deletes the transient. Post status
`publish` now explicitly set in the query (defence in depth — drafts
were never shown but were previously fetched).

**D4 — Chapter shortcode HTML aligned with block render (`inc/shortcodes/shortcodes.php`):**
`$num_html` (chapter numeral span) moved outside `.bwp-chapter__content` div
to match the block render callback output. Both paths now produce identical
structure: `.bwp-chapter > title_html + num_html + .bwp-chapter__content > verses`.

**D6 — Inline styles removed from blocks.js (`assets/js/blocks.js`, `assets/css/editor.css`):**
All hardcoded `fontFamily`, `fontSize`, `gridColumn`, `gridTemplateColumns`
and related inline style objects removed from the block editor canvas render.
Replaced with BEM class names (`.bwp-chapter`, `.bwp-chapter__num`,
`.bwp-chapter__title`, `.bwp-chapter__content`) already used on the front end.
Corresponding CSS rules added to `editor.css` — block editor canvas now
matches front end via stylesheet, and responds correctly to font overrides
set in Theme Options → Typography.

---

## 3.3.49 — 2026-03-02

### Fix — Security hardening, responsive overhaul, print improvements

**Security:**
- `searchform.php` — `get_search_query()` now wrapped in `esc_attr()` (explicit escaping)
- `inc/admin/settings.php` — custom CSS option sanitiser changed from
  `wp_strip_all_tags` to `sanitize_textarea_field` in both the settings
  registration map and the `book_wp_custom_css()` output function
- 14 template files — `defined('ABSPATH') || exit;` guard added:
  `404.php`, `archive.php`, `comments.php`, `footer.php`, `header.php`,
  `index.php`, `page.php`, `search.php`, `searchform.php`, `sidebar.php`,
  `single.php`, `templates/single-puisi-generic.php`,
  `templates/template-kolofon.php`, `templates/template-mukadimah.php`

**Bug fix — Night scheme broken tokens (`assets/css/schemes/night.css`):**
- `--bwp-text-mid` added: `#b8b5ae` — body content text was falling back to
  light-scheme `#333333`, near-invisible on dark background
- `--bwp-stone` added: `#4a4a52` — blockquote borders and `<hr>` were
  falling back to light grey, invisible on dark background
- `--bwp-border-faint` added: `#2a2a30` — missing entirely from night scheme
- Redundant empty `:root.bwp-scheme-night` ruleset removed

**Responsive — two new breakpoints (`assets/css/frontend.css`):**
- `max-width: 36em` (≤575px) — narrow/mobile:
  - Masthead letter-spacing reduced to `.12em`
  - Nav items wrap with `flex-wrap: wrap` to prevent overflow
  - Poetry `max-width` reset to `100%` (32rem is wider than most phones)
  - Work title reduced to `1.6em`
  - TOC header reduced to `1.2rem`
  - Drop cap reduced to `2.8em`
- `36em–52em` (576px–831px) — tablet/mid:
  - Page wrap constrained to `38em`
  - Poetry column constrained to `28rem`
- `min-width: 52em` — unchanged from before

**Print stylesheet improvements (`assets/css/frontend.css`):**
- Heading sizes in `pt`: work title `18pt`, TOC header `14pt`, group heading `10pt`
- `page-break-inside: avoid` on `.bwp-work`, `.bwp-colophon`, `.bwp-epigraph`
- `orphans: 3; widows: 3` on `.bwp-work__content p`
- `page-break-inside: avoid` on individual stanza paragraphs
- `.bwp-colophon__line` set to `color: #333` for print legibility
- Base `font-size` corrected from `11.5pt` to `11pt`; `line-height` from `1.65` to `1.6`

**JS file headers:**
- `frontend.js` `@version` updated: `3.0.0` → `3.3.49`
- `time-theme.js` `@version` updated: `3.3.16` → `3.3.49`

**`upgrading.md`:**
- All 7 resolved issues marked ✅ with version reference

---

## 3.3.43 — 2026-03-02

### Fix — Ratapan analytics + Isaiah-style verse layout (consolidated from multiple 3.3.43 patches)

**Analytics bar showing 0 words / 0 lines on Ratapan chapter posts:**
Gutenberg stores chapter verse content inside block comment JSON attributes in `post_content`, not as visible body text: `<!-- wp:book-wp/chapter {"content":"<p>verse</p>"} /-->`. All analytics functions were reading only raw `post_content` via `wp_strip_all_tags`, which strips the block comment entirely.

Two helpers added to `inc/helpers/analytics.php`:
- `book_wp_extract_chapter_text( $raw_content )` — finds all `wp:book-wp/chapter` block comments via `preg_match_all`, decodes the JSON attribute, returns verse text as plain string. Handles both the `content` HTML string format and the legacy `verses` array format from v3.3.40.
- `book_wp_get_full_text( $post_id )` — strips chapter block comments from raw body, combines with extracted chapter text. Used by `book_wp_reading_time` and `book_wp_word_count`.
- `book_wp_line_count` updated to extract chapter verse `<p>` separately (each `<p>` = one line).
- `book_wp_stanza_count` updated to count verse `<p>` tags inside chapter block JSON.

**Isaiah-style verse layout:**
Verse display changed from continuous justified prose with superscript numbers to Isaiah/NWT style — each verse on its own line, verse numbers as bold body-size inline prefixes.

`assets/css/forms/ratapan.css`:
- `.bwp-chapter__content p` — `display: inline` → `display: block`; `margin: 0 0 .15em`
- `p::after` space connector removed
- `.bwp-vnum` — `font-size: .68em` → `.78em`; `vertical-align: super/line-height:0` removed; now baseline bold inline (`font-weight: 700`)
- `.bwp-chapter__content` — `text-align: justify; hyphens: auto` → `text-align: left; hyphens: none`

---

## 3.3.42 — 2026-03-02

### Feature — Bible-style chapter/verse apparatus for Ratapan (consolidated from v3.3.32–3.3.42)

**Background:** The Ratapan (Lamentations) form mirrors the Biblical lament tradition. This block/shortcode apparatus renders chapters with a bold Cinzel numeral floating left, first verse text wrapping around it, and subsequent verses flowing as continuous justified prose with small superscript verse numbers — matching the typography of a printed scripture page.

**Shortcodes (`inc/shortcodes/shortcodes.php`):**

`[chapter num="1" title="optional"]...[/chapter]` — wraps a chapter. Every paragraph is auto-numbered as a verse. Accepts `verse_start` for chapters continuing a previous count. `[v num="3"]` — inline verse number marker for manual placement.

**Gutenberg block (`inc/blocks/chapter/block.json`, `assets/js/blocks.js`, `inc/hooks/actions.php`):**
`book-wp/chapter` registered via `register_block_type()` with a PHP render callback. Written in ES5 using `window.wp.*` globals — no build step. Direct canvas editing via `RichText` with `multiline: 'p'`: one paragraph per verse, Enter adds a verse. Sidebar contains Chapter Number, Chapter Title (optional), First Verse Number. Block is server-side rendered — shortcode and block produce identical HTML.

Block editor assets enqueued on `enqueue_block_editor_assets` in `inc/setup/enqueue.php`.

**PHP render callback (`inc/hooks/actions.php` — `book_wp_render_chapter_block()`):**
Reads `content` HTML via `wpautop` + `wp_kses_post`. Uses `preg_match_all` to extract each `<p>` block. Skips paragraphs with no real text content (phantom Gutenberg nodes — never rendered on front end). Strips `<br>` tags (verses are continuous prose). Outputs verse number spans and wraps in chapter container.

**CSS (`assets/css/frontend.css`):**
Base rules for `.bwp-chapter`, `.bwp-chapter__num` (floats left), `.bwp-chapter__title`, `.bwp-chapter__content p`, `.bwp-vnum` (superscript inline verse numbers). `.bwp-chapter .bwp-vnum[data-v="1"]` hidden — chapter numeral is its anchor.

**CSS (`assets/css/forms/ratapan.css`):**
- Chapter number: `5em`, `font-weight: 700`, `opacity: .85`, floats left — bold and authoritative
- Chapter separator: `border-top: 1px solid var(--bwp-border-faint)`, first chapter has no border
- Inter-chapter spacing: `margin: 1.6em 0 0`, `padding-top: 1.4em` — close-set like printed scripture
- Verse paragraphs: `display: inline` — all `<p>` collapse into continuous flowing prose
- `text-align: justify; hyphens: auto` on `.bwp-chapter__content`
- Verse numbers: `vertical-align: super; line-height: 0; font-size: .6em; font-weight: 400; opacity: .65` — superscript style; `line-height: 0` prevents superscript from affecting line spacing
- `::after: ' '` on verse `<p>` to prevent adjacent word collisions in continuous flow
- Stanza counter suppressed inside `.bwp-chapter` to avoid double-numbering

**Bilingual strings (`inc/helpers/strings.php`):**
Added `chapter` (Fasal / Capítulo / Chapitre / Chapter) and `verse` (Ayat / Versículo / Verset / Verse) to all four locales — used as `aria-label` text.

**Note on editor phantom lines:** Gutenberg's `RichText multiline:'p'` may show empty paragraph lines at the bottom of the block in the editor. These are editor-only artefacts — the PHP render skips them on output. Workaround: do not press Enter after the last verse; use arrow keys to navigate away.

---

## 3.3.31 — 2026-03-02

### Fix + Enhancement — Colophon ornament and redundant divider

**Problem 1 — Ornament left-aligned in colophon**
Form-level ornament overrides (`text-align: left` on puisi, memoir, esei, hikayat,
ratapan) cascaded into the colophon because `book_wp_ornament()` outputs the same
`.bwp-ornament` element whether it's a mid-content scene break or the colophon
terminus. Added `.bwp-colophon .bwp-ornament { text-align: center !important }`
to `frontend.css` — colophon ornament is now always centred regardless of form.

**Problem 2 — Redundant border-top + ornament double-dividing**
The form CSS files added `border-top` to `.bwp-colophon` on top of the ornament,
creating a line → gap → ornament sequence. The ornament is the divider; the border
was redundant and competed with it. Removed `border-top` and `padding-top` from
`.bwp-colophon` overrides in all 10 form files: esei, hikayat, karmina, memoir,
novel, nukilan, puisi, ratapan, syair. Base `padding-top` also zeroed in
`frontend.css` — spacing is now handled entirely by the ornament's own margin.

**Enhancement — Colophon ornament as terminus**
Colophon ornament enlarged: `font-size: 1.3em`, `letter-spacing: .35em`,
`opacity: .5`, `margin: 0 0 1.8em`. It reads as a deliberate closing mark rather
than a same-weight mid-content divider.

---

## 3.3.30 — 2026-03-02

### Feature — Poetry typography system + settings tab rewrites

**Poetry enhancements (`frontend.css`):**

1. Ragged-right enforcement — all 4 poetry forms (puisi, syair, karmina,
   ratapan) hardcoded `text-align: left; hyphens: none`. Prose justification
   setting (future) will never affect verse.

2. Narrower reading column — poetry content max-width: 32rem (vs prose 40–48em).
   Verse needs a tighter column to read as a unit, not a wide block of text.

3. Per-form indent conventions:
   - Karmina: `text-align: center` — 2-line couplet, centred is the
     classical Malay print convention
   - Syair: `padding-left: 2em` — 4-line stanza, indented column
     distinguishes it from the prose margin

4. Poetry line height — independent from prose `--bwp-lh`. Three levels:
   tight (1.55) / normal (1.65) / loose (1.80). Controlled by
   `bwp-poetry-lh-{level}` body class. Default: normal.

5. Stanza spacing — the breath between stanzas. Three levels:
   tight (0.7em) / comfortable (1.6em) / spacious (2.6em). Controlled by
   `bwp-stanza-{level}` body class. Default: comfortable.

6. Stanza numbering extended to all 4 poetry forms (was puisi + ratapan only).
   counter-reset now added to cover syair and karmina.

7. [verse] shortcode number outdented into left margin — `margin-left: -2.4em`
   pulls the number clear of the text column. Italic removed (small, muted,
   upright — less distracting). Gap between number and text via padding-right
   on the number element rather than gap on the flex container.

**New settings (`settings.php` + `filters.php`):**
- `book_wp_stanza_spacing` — tight / comfortable (default) / spacious
- `book_wp_poetry_line_height` — tight / normal (default) / loose
- Both registered, wired into the typography tab save handler, and emit
  body classes via `book_wp_body_classes()`

**Settings tab descriptions rewritten:**
- General: expanded Site Title vs Author Name note; TOC grouping explained;
  language note updated (all 4 locales listed)
- Typography: time-of-day note px → rem; drop cap section description clarified
- Literary: shortcode list updated to English names (petikan→quote, pemisah→divider,
  bait→verse, istilah→term); ornament description expanded; per-work override noted;
  ornament hint px → rem
- Reading: all 4 toggle descriptions expanded with mechanical detail
- Advanced: SEO toggle note expanded; Custom CSS hint updated; CSS vars list
  expanded (added --bwp-font, --bwp-font-ui, --bwp-font-display, --bwp-lh);
  inline px → rem

**Other fixes:**
- `.bwp-istilah__tip` `margin-bottom: 5px` → `0.3rem`

---

## 3.3.29 — 2026-03-02

### Fix — TOC font hierarchy (consolidated from v3.3.24, 3.3.28, 3.3.29)

Three iterative size corrections to establish a clear visual hierarchy in the Table of Contents:

- `.bwp-toc-header` — `font-size: .72rem` → `1rem` → `1.5rem` (final). Letter-spacing tightened from `.22em` to `.18em` at 1rem (optical compensation); retained at final 1.5rem size
- `.bwp-toc-group__heading` — `font-size: .65rem` → `1rem` (matches original toc-header before it was further increased)

Final state: TOC header at 1.5rem, group/category headings at 1rem — a clear two-level hierarchy.

---

## 3.3.27 — 2026-03-02

### Fix + Feature — Drop cap: per-form settings and per-post override

**Bug fixed:** CSS selector `.bwp-dropcap:not(.bwp-form-puisi):not(.bwp-form-ratapan)`
only excluded two of the four poetry forms. Syair and karmina were incorrectly
receiving a drop cap. Now no poetry form ever gets a drop cap — handled via
explicit per-form body classes, not exclusions.

**`inc/hooks/filters.php`:**
- Removed: single `bwp-dropcap` body class
- Added: `bwp-dropcap-{form}` body class for each prose form whose setting is on
- Added: `bwp-dropcap-force-on` / `bwp-dropcap-force-off` per-post override classes
- Defaults: cerpen ✓ novel ✓ hikayat ✓ memoir ✓ esei ✗ nukilan ✗

**`assets/css/frontend.css`:**
- Old selector replaced with 6 per-form selectors:
  `.bwp-dropcap-{form}.bwp-form-{form} .bwp-work__content > p:first-of-type::first-letter`
- `.bwp-dropcap-force-on` activates drop cap regardless of form setting
- `.bwp-dropcap-force-off` suppresses drop cap regardless of form setting

**`inc/admin/settings.php` — Typography tab:**
- Single "Drop Cap on Prose" toggle removed
- Replaced with 2-column checkbox grid — one checkbox per prose form
- Option keys: `book_wp_drop_cap_{form}` for each of the 6 prose forms
- Registered in global settings map and per-tab save handler

**`inc/admin/meta-boxes.php` — Display section:**
- "Drop Cap Override" select added: Follow form setting / Force on / Force off
- Saved as `book_wp_drop_cap_override` post meta

---

## 3.3.26 — 2026-03-02

### Breaking — Shortcodes renamed to English

All four shortcodes renamed from Malay to English. Malay attribute aliases
removed. Existing posts using the old Malay shortcode names will need a
database find-and-replace.

| Old | New | Change |
|---|---|---|
| `[petikan]` | `[quote]` | attribute `source` unchanged |
| `[pemisah]` | `[divider]` | `gaya` alias removed; `style` only |
| `[istilah]` | `[term]` | `takrifan` alias removed; `definition` only |
| `[bait]` | `[verse]` | attribute `num` unchanged |

**Database migration** (run once via WP-CLI or a plugin like Better Search Replace):
```
[petikan  →  [quote
[/petikan →  [/quote
[pemisah  →  [divider
[istilah  →  [term
[/istilah →  [/term
[bait     →  [verse
[/bait    →  [/verse
gaya=     →  style=
takrifan= →  definition=
```

CSS classes on the output HTML are unchanged (`bwp-petikan`, `bwp-bait`,
`bwp-istilah`) — no stylesheet changes required.

---

## 3.3.25 — 2026-03-02

### Housekeeping — px/rem convention enforced across all CSS

Convention: typography and spacing in `rem`, borders and decoration in `px`.

All 27 CSS files audited. Violations were exclusively in `admin.css` (written
before the convention was formalised). frontend.css, literary.css, all 10 form
files, 3 scheme files, and 5 time files were already fully conformant.

**`admin.css` — 38 fixes:**
All `font-size`, `padding`, `margin`, and `gap` values converted from px to rem.
Conversion base: 16px = 1rem.

**Left as px (correct):**
- All borders, box-shadow, outline, outline-offset
- ≤4px positional nudges (top/bottom/left/right)  
- ≤2px hairlines used as decorative dividers
- border-radius, transform (animation), visually-hidden pattern (1px)
- Fixed UI control dimensions: toggle widgets (38×20px, 48×30px, 40×22px),
  icon sizes (13×13px, 14×14px), progress bar height
- Layout max-widths: admin.css 740px, editor.css 680px (these mirror the
  PHP `$content_width` and WP admin container — intentionally fixed)

---

## 3.3.23 — 2026-03-02

### Feature + Fix — Masthead navigation (consolidated from v3.3.21, 3.3.22, 3.3.23)

**Design intent:** The site header is a masthead — author name at display
scale (Cinzel, uppercase) with the primary nav strip locked below it by a
3px rule above and a 4px rule below. The two elements read as a single
composed block. Reference: literary journal mastheads and poetry collection
title pages.

**`header.php`:**
- `wp_nav_menu()` added for `primary` location, inside `.bwp-nav`, conditional
  on `has_nav_menu('primary')` — no layout breakage on installs with no menu
- Back link (`← Kembali ke Isi Kandungan`) removed — home link in nav serves
  this purpose
- Chapter blurb (category description) retained below the nav strip

**`footer.php`:** `wp_nav_menu()` for new `footer` location, conditional on `has_nav_menu('footer')`.

**`inc/setup/menus.php`:** `footer` → `Footer Menu` location added.

**`assets/css/frontend.css`:**
- `.bwp-site-header__title` — Cinzel, `1.1rem` mobile / `1.25rem` desktop (reduced from 2rem/2.75rem — signature weight, not hero), `letter-spacing: .22em`, `weight: 400`, `margin-bottom: 1.1em` for breathing room above nav
- `.bwp-nav` — `border-top: 3px`, `border-bottom: 4px`, both `var(--bwp-border-text)`; `padding: .45em 0`
- `.bwp-nav__list` — flex, centred, `gap: 1.8em`; Jost `.72rem` uppercase `.1em` tracking; current-menu-item muted
- `.bwp-nav__list .sub-menu` — depth-2 dropdown, absolute, centred, hidden until hover
- `.bwp-back-link` — `display: none` (retired; selector kept for child themes)
- `.bwp-footer-nav` + `.bwp-footer-nav__list` — Jost `.68rem` uppercase, muted, centred flex
- Responsive: title scales to `1.25rem` at ≥52em
- Print: `.bwp-nav` and `.bwp-footer-nav` added to hidden elements

**`assets/css/editor.css`:** `.bwp-site-header__title` preview styles added.

---

## 3.3.20 — 2026-03-02

### Feature — Block editor bridge (classic theme FSE groundwork)

Sasterawan is and remains a **classic PHP template theme**. This release adds the
maximum block editor integration possible without triggering FSE mode, and
documents the full migration path for when FSE is undertaken.

**`theme.json` added:**
- Colour palette: 14 named swatches matching `--bwp-*` token values
- Font families: Body (EB Garamond), UI (Jost), Display (Cinzel)
- Font sizes: small / normal / medium / large / x-large
- Content width: 680px content, 900px wide
- Disabled: custom colours, custom gradients, default WP palettes, drop cap,
  arbitrary font sizes — keeps the block editor within the literary design system
- `styles.typography` seeds the editor body with the correct font/size/line-height

**`assets/css/editor.css` added:**
- Mirrors front-end typography in the Gutenberg editor canvas
- Styles headings, links, blockquotes, code, verse blocks, pullquotes, separators
- Loaded via `add_editor_style()` — no impact on front end

**`inc/setup/theme-support.php` additions:**
- `editor-styles` + `add_editor_style( 'assets/css/editor.css' )`
- `align-wide`
- `wp-block-styles`
- `responsive-embeds`

**`upgrading.md` — new FSE Migration Plan section:**
- Explains exactly why FSE cannot be enabled now (`templates/index.html`
  trigger, PHP templates conflict, meta boxes)
- Three-phase migration plan with checklist
- `templates/` directory mapping (PHP → HTML block equivalents)
- `--bwp-*` vs `--wp--preset--*` aliasing strategy for post-FSE
- `theme.json` extensions needed at FSE switchover

---

## 3.3.19 — 2026-03-02

### Feature — Spanish and French translation support

**Two new locale files added:**

| File | Language | Strings |
|---|---|---|
| `languages/es_ES.po` + `es_ES.mo` | Spanish (Español) | 17/17 translated |
| `languages/fr_FR.po` + `fr_FR.mo` | French (Français) | 17/17 translated |

Covers all 17 WP gettext strings (menus, sidebars, admin UI, taxonomy labels,
accessibility strings). Both locales activate automatically via
Settings → General → Site Language — no code changes needed.

**`inc/helpers/strings.php` extended:**
- `book_wp_strings()` now resolves to Spanish (`es`, `es_*`) and French
  (`fr`, `fr_*`) in addition to Malay and English
- 37 runtime UI strings translated for each locale (analytics bar, colophon
  labels, navigation, badges, search, error messages)
- Locale resolution order: Malay → Spanish → French → English (fallback)
- Closure `$t()` replaces the previous binary `$is_ms ? ... : ...` pattern —
  adding a fifth language now requires one column per string, not a new conditional

**`languages/book-wp.pot` fixed:**
- Source reference corrected from `template-tags.php:557` to `navigation.php`
  (stale since the v3.3.18 helpers split)

---

## 3.3.18 — 2026-03-01

### Refactor — helpers/ split from monolith into five focused files

`inc/helpers/template-tags.php` (43K, 20 functions) removed and replaced
with five single-responsibility files loaded in dependency order:

| File | Responsibility | Functions |
|---|---|---|
| `strings.php` | Bilingual BM/EN string pairs | `book_wp_strings`, `book_wp_str` |
| `literary.php` | Form slug/label, poetry detection, ornament renderer | `book_wp_get_form_slug`, `book_wp_get_form_label`, `book_wp_is_poetry`, `book_wp_form_description`, `book_wp_ornament` |
| `analytics.php` | Reading metrics and analytics bar | `book_wp_reading_time`, `book_wp_word_count`, `book_wp_line_count`, `book_wp_stanza_count`, `book_wp_analytics_bar` |
| `colophon.php` | Epigraph, dedication, full colophon | `book_wp_epigraph`, `book_wp_dedication`, `book_wp_colophon` |
| `navigation.php` | Related works, prev/next, TOC entry, featured image, progress bar | `book_wp_related_works`, `book_wp_work_navigation`, `book_wp_toc_entry`, `book_wp_featured_image`, `book_wp_progress_bar_markup` |

Bonus: `book_wp_colophon()` now fetches all post meta in a single `foreach`
loop over an array of keys rather than 50+ individual `get_post_meta()` calls.

`meta-boxes.php` and `settings.php` left as-is — each is one atomic admin
concern and splitting them would add indirection without clarity.

---

## 3.3.17 — 2026-03-01

### Fix — colophon dateline aligned with analytics bar

The dateline (place, date) at the bottom of each work was rendering inconsistently across forms — some italic serif, some italic muted, some with different sizes — because `.bwp-colophon__line--dateline` had only `font-style: italic` and `opacity: .9`, inheriting font-family inconsistently from form-level overrides.

Dateline now matches the analytics bar exactly: Jost (`--bwp-font-ui`), uppercase, `.09em` letter-spacing, `.75em` size, `--bwp-muted` colour. `font-style: italic` removed from the dateline modifier. `font-style: italic` also removed from `.bwp-colophon` in `hikayat.css` and `memoir.css` where it was cascading into all child lines and fighting the new explicit dateline rules.

---

## 3.3.16 — 2026-03-01

### Fix — time-of-day auto/visitor clock not applying

The time theme class was being added to `<html>` immediately but then moved to `<body>` via a `DOMContentLoaded` listener. WordPress's own inline admin bar script manipulates `body.className` at `DOMContentLoaded` as well, and its timing meant the `bwp-time-*` class was never reliably present on `<body>` when CSS was evaluated.

Fix: the class now stays on `<html>` permanently. All time-of-day CSS selectors updated from `body.bwp-time-*` to `:root.bwp-time-*` across `times/dawn.css`, `times/dusk.css`, `times/morning.css`, and `times/night.css` (4 selectors total). The `DOMContentLoaded` body-move logic removed from `time-theme.js`. The class is now applied in `<head>` before any paint, with no timing dependency.

---

## 3.3.15 — 2026-03-01

### Fix — visual selector grids no longer leave orphan gaps

Font Family (7 items), Colour Scheme (4 items), and Ornament (8 items) grids were using `repeat(auto-fill, minmax(105px, 1fr))` producing a 3-column layout with trailing empty cells. Changed to `repeat(4, 1fr)` — all three grids now tile into complete rows. Responsive breakpoint at ≤782px drops to 2 columns. Time-of-Day grid (`--wide`) unchanged.

---

## 3.3.14 — 2026-03-01

### Revert — analytics bar restored to original design, font sizes increased

Reverted the 3.3.12 analytics bar redesign. Restored the original outlined badge box, Jost uppercase stats, and hairline top/bottom borders. Font sizes increased from the original: badge `.58em` → `.65em`, stats/category `.67em` → `.75em`.

---

## 3.3.13 — 2026-03-01

### Chapter Blurb — replaced theme option with native category description

The Chapter Blurb setting has been removed from Theme Options. The header now automatically uses the native WordPress Category Description of the first assigned category that has one.

**Why:** A single sitewide blurb repeated on every work page created duplicate content and offered no sectional variation. Category descriptions are already the correct WordPress-native place to describe a section of content, and they vary per collection.

**Behaviour:** On single post pages, the header iterates the post's assigned categories in order and uses the Description of the first one that is non-empty. If no assigned category has a description, no blurb is shown. No slug matching, no parent detection, no hardcoded values.

**Editorial workflow:** Set the description on any category via Posts → Categories → Edit. Only one category per post needs a description for precise control; if multiple have descriptions, the first wins.

**`header.php`** — `get_option('book_wp_chapter_blurb')` replaced with a `foreach` over `get_the_category()` calling `term_description()`.

**`inc/admin/settings.php`** — `book_wp_chapter_blurb` removed from sanitize callback array, settings registration array, and the UI field block (label, textarea, hint).

---

## 3.3.12 — 2026-03-01

### Redesign — analytics bar and literary form badge

The analytics bar and badge have been redesigned for literary feel. The previous design used uppercase Jost text, a hairline-bordered box badge, and top/bottom rules enclosing the bar — giving it the character of a file manager rather than a literary publication.

**Badge**
- Outlined box removed entirely; form name now displays as unboxed Cinzel small caps, tracked, muted — a quiet classifier rather than a stamped label
- Font size: `.72em` (up from `.6em`) — legible without imposing
- Colour variant rules simplified: puisi uses `--bwp-accent`, all others use `--bwp-muted`
- Stale `--bwp-badge-*` tokens removed from `frontend.css` and `times/night.css` — no longer referenced by any CSS rule

**Analytics items**
- Font changed from Jost (UI sans-serif) to EB Garamond italic — matches the body voice
- Font size: `.92em` (up from `.82em`) — italic reads cleanly at this size; `.82em` was too small for the weight to carry
- Uppercase and wide letter-spacing removed; items read as a flowing annotation, not a data row
- Items separated by mid-dots (·) via CSS `::before` rather than spaced gaps
- Category name (`bwp-analytics__cat`) styled identically to items — same weight, same italic, no longer bolded or accent-coloured

**Bar**
- Top and bottom hairline borders removed; metadata sits in open space beneath the title rule
- Stale per-form overrides (`font-size`, `opacity`, `border-color`, `justify-content`) removed from all ten form CSS files

**`--bwp-border-ink` — full contrast audit across all schemes and time themes**

The mid-dot separator now uses `--bwp-border-ink`, which as a UI element requires ≥3:1 contrast (WCAG 1.4.11). The existing values failed in six of nine contexts:

- `frontend.css` base: `#aaaaaa` → `#767676` (2.32:1 → 4.54:1 on white)
- `schemes/cream.css`: `#b8aa98` → `#8a7e72` (2.07:1 → 3.61:1)
- `schemes/dusk.css`: `#a8a298` → `#807870` (2.00:1 → 3.43:1)
- `schemes/night.css`: `#555558` → `#707078` (2.33:1 → 3.53:1 on dark bg)
- `times/dawn.css`: added `--bwp-border-ink: #7878a0` (4.03:1, cool-violet tint)
- `times/dusk.css`: added `--bwp-border-ink: #907050` (4.30:1, warm amber-grey)
- `times/morning.css`: added `--bwp-border-ink: #887858` (4.27:1, warm golden-grey)

`times/afternoon.css` inherits the corrected base value. `times/night.css` was already correct — base `#767676` gives 4.06:1 on the dark background.

---

## 3.3.11 — 2026-03-01

### Fix — stale pre-3.2.0 header gradient backgrounds in form CSS files

Three literary form stylesheets retained dark background gradient rules on `.bwp-site-header`, `.bwp-book-title`, and `.bwp-site-footer` from the pre-3.2.0 decorative design system. These were supposed to be removed during the 3.2.0 minimalist overhaul but were missed in the per-form files. The `@media print` blocks in the same files correctly neutralised them for print, but screen rendering was broken.

- **ratapan.css** — dark desaturated gradient (`#1a1a1e → #3a3438`) on site header and book title; dark footer (`#1a1a1e`); arabesque `::before` remnant with `opacity: .15; filter: grayscale(1)` — all removed
- **hikayat.css** — dark parchment gradient (`#2e2208 → #7a5c28`) on site header and book title; dark footer (`#2e2208`) — removed
- **memoir.css** — dark teal gradient (`#1a2e28 → #3a5e50`) on site header and book title; dark footer (`#1a2e28`) — removed

The now-redundant `background: none` overrides in each file's `@media print` block were also removed. All other form CSS (puisi, nukilan, syair, karmina, cerpen, esei, novel) audited and confirmed clean.

---

## 3.3.10 — 2026-03-01

### Colour scheme and time-of-day CSS consistency pass

**Badge colours on dark backgrounds (schemes/night.css, times/night.css)**
`--bwp-badge-puisi` (#1B3C53) and `--bwp-badge-nukilan` (#444444) were never
overridden for dark themes — both nearly invisible at 1.50:1 and 1.78:1
respectively on dark backgrounds. Added light overrides to both `schemes/night.css`
and `times/night.css`: badge-puisi inherits `--bwp-link` colour, badge-nukilan
uses the muted token.

**`--bwp-teal` on night colour scheme (schemes/night.css)**
Used by esei h2 headings and the thesis colophon block border in `literary.css`.
Base value #1B3C53 achieves only 1.50:1 on the dark scheme background. Added
`--bwp-teal: #7eb5d3` (matches `--bwp-link` in this scheme).

**`--bwp-accent` not synced to `--bwp-link` in time themes**
`--bwp-accent` was not overridden in any time-of-day theme despite `--bwp-link`
being different in each. Time-night was the critical failure (base #1B3C53 at
1.60:1 on the dark background). Added `--bwp-accent` to all four time themes
synced to their respective link colour: dawn (#5a4a8a), morning (#8a5210),
dusk (#a03a10), night (#6aaad4).

**`--bwp-stone` on cream and dusk schemes**
Used for epigraph and petikan left borders. Base #cccccc achieved only 1.47:1
on cream and 1.27:1 on dusk — both essentially invisible. Fixed:
cream → #9a8c70 (3.01:1), dusk → #8a7c60 (3.23:1).

**`--bwp-border-faint` on cream and dusk schemes**
Base #e2e2e2 achieved 1.18:1 on cream and 1.02:1 on dusk (literally invisible).
Used for TOC separators, archive list dividers, and colophon section borders.
Fixed: cream → #b8b0a0 (1.96:1), dusk → #b8b0a0 (1.70:1).

**`--bwp-text-mid` added to cream and dusk schemes**
Was not overridden, defaulting to #333333 (fine contrast but wrong warmth on
tinted backgrounds). Added warm-shifted values: cream #4a3e30, dusk #3e3830.

**`--bwp-teal` added to cream and dusk schemes**
Was not overridden, defaulting to #1B3C53. Passes contrast on both but shifts
warm: cream #5a3e20, dusk #3a5a6e.

---

## 3.3.9 — 2026-03-01

### Added upgrading.md

Added `upgrading.md` documenting the Booklit fork plan (10-point rename
checklist: folder slug, style.css header, 138 PHP function/option prefixes,
3 constants, 12 enqueue handles, JS global object, text domain, taxonomy,
translation files, footer credit) plus a database migration strategy for
`wp_options` and `wp_postmeta` keys. Also documents near-term and longer-term
future upgrade plans: stale enqueue handle, term migration maintenance,
`book_wp_strings()` → `.po` files, `taxonomy-literary_form.php` archive
template, child theme support, block theme / FSE roadmap, and RTL/Jawi support.

---

## 3.3.8 — 2026-03-01

### Rename sastera-theme.css → literary.css

`sastera-theme.css` was renamed to `literary.css`. The old name was opaque to non-Malay readers and did not describe the file's actual scope. `literary.css` is language-neutral, self-explanatory, and consistent with the rest of the asset naming convention. All references updated: `enqueue.php`, `frontend.css` comment, `readme.txt`, `changelog.md`, and the file's own header comment.

---

## 3.3.7 — 2026-03-01

### CSS consistency pass — analytics visibility, ornaments, colophon, duplicate definitions

**Analytics visibility (7 form files)**
All poetry/verse forms were reducing analytics opacity too aggressively; `ratapan` had no override at all (full opacity) while `puisi` was at `.65` and `karmina` at `.45` — the user could barely read word counts on puisi while ratapan showed them clearly. Standardised to a consistent 3-tier system:
- Prose forms (`cerpen`, `esei`, `novel`): no change — full opacity, left-aligned, base size
- Verse/lyric forms (`puisi`, `syair`, `ratapan`): `font-size: .65em; opacity: .85; border-color: transparent`
- Compressed/quoted forms (`karmina`, `nukilan`, `hikayat`, `memoir`): `font-size: .63em; opacity: .75–.80; border-color: transparent`

Also added `border-color: transparent` to `puisi` and `ratapan` for consistency with the other verse forms.

**Ornament inconsistency (cerpen, novel, ratapan, nukilan, syair)**
- `cerpen`: no opacity set — ornament rendered at full `--bwp-ornament` color while every other form reduced it. Added `opacity: .35; font-size: 1.0em`
- `novel`: no `font-size` set — was inheriting the large base `1.35em`. Added `font-size: 1.0em; opacity: .35`
- `ratapan`, `nukilan`: no `text-align` set. Added `text-align: center`
- `syair`: ornament oversized at `1.3em / letter-spacing: .3em`. Reduced to `1.1em / .2em` to match the other verse forms

**Title rule — puisi**
Title `::after` rule used `--bwp-border-ink (#aaaaaa)` — a faint grey that nearly disappeared. Changed to `--bwp-accent (#1B3C53)` to give the rule visual presence consistent with the form's contemporary verse identity.

**Missing colophon separators (puisi, syair, karmina)**
These three forms had no colophon override at all — content ran straight into metadata with no visual break. Added `border-top: 1px solid var(--bwp-border-faint); padding-top: 1em; margin-top: 2em` to all three (plus `text-align: center` for syair and karmina).

**Critical: duplicate colophon definitions — frontend.css vs literary.css**
All 7 extended colophon classes (`bwp-colophon__line--dateline/subject/meta`, `bwp-colophon__karmina*`, `bwp-colophon__thesis`) were defined in both files with conflicting values. Since `literary.css` loads after `frontend.css`, it silently overrode the carefully designed values, causing:
- Karmina colophon block: lost gold border (`--bwp-gold-dim`) → plain `--bwp-border` grey
- Esei thesis block: lost teal `3px` border → plain `--bwp-text` black, `2px`
- Meta line: different font-size and opacity

Fix: removed the stale duplicate block from `frontend.css` entirely (replaced with a comment directing to `literary.css`). Fixed the values in `literary.css` to restore gold on karmina, `3px var(--bwp-teal)` on thesis, and bumped meta opacity from `.6` to `.7`.

---

## 3.3.6 — 2026-03-01

### Translation files + version constant fix

**Translation files**
- Added `languages/book-wp.pot` — master translation template covering all 17 WP i18n calls (`__()`, `_e()`, `_x()`, `esc_html_e()`, `esc_attr_e()`) across the theme. Front-end strings (analytics bar, navigation, colophon labels, etc.) are handled separately by `book_wp_strings()` in `template-tags.php` which already returns BM or EN based on `get_locale()`.
- Added `languages/ms_MY.po` — full Malay (Malaysia) translation for all 17 admin/template strings: taxonomy labels, meta box title, settings page UI, night toggle, skip link, search, comments, page templates, sidebar/menu names.
- Added `languages/ms_MY.mo` — compiled binary loaded by WordPress at runtime.

**Version constant**
- `BOOK_WP_VERSION` in `functions.php` was hardcoded to `3.2.8` and never updated across subsequent releases, causing the settings page version badge to show a stale number. Replaced with `wp_get_theme()->get( 'Version' )` so the constant always reads directly from `style.css` and can never drift out of sync again.

---

## 3.3.5 — 2026-03-01

### WCAG 2.1 AA compliance fixes — non-text contrast, search button contrast, ARIA; housekeeping

**1.4.11 Non-text Contrast**
- Night toggle button and search form/input borders were using `--bwp-border-ink` (#aaaaaa) which only achieves 2.32:1 against the default white background (needs 3:1). Introduced `--bwp-ui-border: #767676` — a dedicated token specifically for interactive UI component boundaries that meets ≥3:1 against all scheme and time-of-day theme backgrounds. Applied to: night toggle border, search form outer border, search input/button divider, sidebar search field border.

**1.4.3 Text Contrast — search button in dark themes**
- Search button uses `color: #fff` hardcoded with `background: var(--bwp-link)`. In the night colour scheme (`--bwp-link: #7eb5d3`) this yields 2.23:1; in the time-night theme (`--bwp-link: #6aaad4`) it yields 2.53:1 — both well below the 4.5:1 AA minimum. Fixed by adding dark text overrides (`color: #1a1a1f` / `#0e141e`) for the search button in `schemes/night.css`, `times/night.css`, and the `body.bwp-night` block in `frontend.css`.

**ARIA fixes**
- Work navigation arrows (`←`/`→`) lacked `aria-hidden="true"`. They are purely decorative — the accessible name of each link comes from the "Previous work" / "Next work" label and title text. Screen readers were announcing the arrow characters as text.
- `[istilah]` shortcode used `role="term"` which is not a valid ARIA role. Replaced with `aria-label="{term}: {definition}"` on the trigger span so screen readers announce both the term and its definition on focus. The visual tooltip retains `role="tooltip"` and gains `aria-hidden="true"` to prevent double-reading.

**Housekeeping**
- Removed leftover `.bak` files (`admin.css.bak`, `settings.php.bak`) and `languages/.gitkeep` from the distributable zip

---

## 3.3.4 — 2026-03-01

### Bug fix — autobiografi slug migration did not fire

The slug migration from `autobiografi` to `memoir` introduced in 3.3.3 never ran on existing installs because `book_wp_terms_version` was already set to `3.3.3` in the database from the name-change migration earlier in the same release. Bumped `$terms_version` to `3.3.4` to force the slug update to run.

---

## 3.3.3 — 2026-03-01

### Rename default term — Autobiografi / Memoir → Memoir (slug: autobiografi → memoir)

The default term label has been simplified from "Autobiografi / Memoir" to "Memoir" and its slug updated from `autobiografi` to `memoir`. All post associations are migrated automatically — no posts lose their term assignment.

A `$term_migrations` array has been added alongside the existing `$default_terms` seeder. On update, `wp_update_term()` is called for any term whose slug or name differs from the target — fully automatic on next page load. The form CSS file has been renamed from `autobiografi.css` to `memoir.css`, the body class from `.bwp-form-autobiografi` to `.bwp-form-memoir`, and all internal references updated accordingly. Future slug or label changes only require adding an entry to `$term_migrations` and bumping `$terms_version`.

---

## 3.3.2 — 2026-03-01

### Bug fix — missing default literary form terms after update

The default term seeder was guarded by a one-time boolean flag (`book_wp_terms_installed`). The original 3.0.0 release only seeded three terms (puisi, nukilan, ratapan); the remaining seven (syair, karmina, hikayat, cerpen, novel, esei, autobiografi/memoir) were added to the code later but never inserted on existing installs because the flag was already `true` in the database.

Fix: replaced the boolean flag with a version string (`book_wp_terms_version`). On each theme load, the stored version is compared against the current value. If they differ, the seeder runs and inserts any missing terms — `term_exists()` ensures existing terms are never duplicated or overwritten. When the default term list is expanded in future releases, bumping `$terms_version` is sufficient to trigger seeding on all existing installs.

---

## 3.3.1 — 2026-03-01

### Update author URL to GitHub

Replaced `https://menj.net` with `https://github.com/menj` across `style.css` (Author URI), `footer.php` (MENJ credit link), and `readme.txt` (Credits). `settings.php` was already using the correct GitHub URL.

---

## 3.3.0 — 2026-03-01

### Bug fixes — reading analytics (line count and stanza count)

`book_wp_line_count()` was calling `wp_strip_all_tags()` before converting `<br>` tags to newlines. Because `strip_all_tags` silently removes `<br>` without inserting a newline, all lines within a stanza collapsed into a single string and the function was effectively returning the stanza count instead of the line count. Fix: `<br>` and `</p>` are now replaced with `
` before tags are stripped, so each poem line is preserved as a distinct entry.

`book_wp_stanza_count()` had two successive issues. First, it was splitting on the alternation `(?:
\s*
|<\/p>\s*<p>)` which misfired on the single newline the block editor places between `</p>` and `<p>`, producing a count one short. Second, the `<p>` element counting replacement missed stanzas stored as `<!-- wp:verse -->` blocks in Gutenberg, since those blocks have a different raw structure despite rendering a `<p>` in the browser. Final fix: when Gutenberg block markers are detected in the raw content, the function counts `<!-- wp:paragraph -->` and `<!-- wp:verse -->` openers directly — unambiguous and unaffected by block attributes. Classic editor content falls back to `<p>` element counting; plain text falls back to blank-line splitting.

---

## 3.2.9 — 2026-03-01

### Admin UI — modern_settings_ui v1.0.0 compliance + theme CSS scoping

Rebuilt the settings page and admin CSS to conform to the `modern_settings_ui v1.0.0` design system specification. No front-end behaviour changes.

**Wrapper and scoping**
- Wrapper class `.theme-settings-root` scopes all settings page styles; no `:root` overrides, no global host selectors
- All CSS rules scoped exclusively to `.theme-settings-root` or `.bwp-meta-root`
- All CSS custom properties prefixed `--theme-*` (e.g. `--theme-accent`, `--theme-border`, `--theme-text-muted`)

**Tokens**
- All colours defined as CSS variables inheriting from `var(--wp-admin-theme-color)` — settings page automatically follows the active WP admin colour scheme
- No hardcoded palette hex in style rules; notice colours extracted into `--theme-notice-bg/border/text` tokens

**Layout**
- Card-based layout with `bwp-section` groups; no table-based rows
- Field rows use CSS grid: 190px label column / 1fr control column on desktop; stacked on ≤782px
- All controls wrapped in `.bwp-field__control` for correct grid positioning; `.bwp-field--full` variant for full-width controls

**Controls**
- 13 boolean settings now use toggle switches (`bwp_toggle()` helper)
- Visual selector grid (`bwp-vs-grid`) for font family, colour scheme, time-of-day theme, and ornament style
- Ornament choices display inline SVG previews of the actual glyphs

**Tabs**
- Inline SVG icons per tab; active tab shows 2px bottom border in accent colour
- `role="tab"` and `aria-selected` on all tab links; tabs horizontally scrollable on narrow screens

**Accessibility**
- Focus-visible on tabs, toggles, selects, inputs, save button
- Toggle: `<input>` precedes `<label class="bwp-toggle-track">` in DOM; CSS uses `input:checked + .bwp-toggle-track` sibling selector
- `prefers-reduced-motion` block disables all transitions

**Footer**
- Author credit (MENJ) and GitHub link in every tab's form footer

**Meta boxes**
- `.bwp-meta-root` added to the literary work meta box wrapper div, enabling scoped admin CSS in the post editor

**Dead code removed**
- Unused `bwp_field_open()` / `bwp_field_close()` helper functions removed

---

## 3.2.8 — 2026-03-01

### Author Name setting — decouple site header from WordPress Site Title

The theme was using `bloginfo('name')` (WordPress Site Title) as the author name in the site header, forcing users to set the Site Title to their name and breaking browser tab titles, RSS feeds, and SEO — where the Site Title should be the anthology name.

- New setting: **Author Name** (General tab, first field) — displayed in the site header on every page
- `header.php` now outputs `book_wp_author_name` with a fallback to `bloginfo('name')` for backwards compatibility
- Info note added to the General tab: Site Title = anthology name (browser tab / SEO); Author Name = header display
- Correct workflow: set WordPress Site Title to the anthology name; set Author Name to the author's name

---

## 3.2.6 — 2026-03-01

### WCAG 2.1 AA compliance — full audit and remediation

All 24 WCAG 2.1 Level AA success criteria now met.

**1.4.3 Contrast (Minimum) — text/background pairs**
- `--bwp-muted` darkened: #777777 → #666666 (5.74:1 on white; was 4.48:1)
- Night mode muted lightened: #7a756e → #9a9590 (6.17:1 on dark bg; was 4.01:1); 5.63:1 on bg-subtle (was 3.66:1)
- Cream scheme muted: #8a7e72 → #6e6257 (5.40:1; was 3.61:1)
- Dusk scheme muted: #7a756e → #5e5953 (5.47:1; was 3.61:1); link: #4a6e82 → #3a5a6e (5.78:1; was 4.31:1)
- Night colour scheme muted: #888580 → #a09d98 (6.41:1; was 3.61:1)
- Dawn time-theme muted: #7a7488 → #6a6478 (5.43:1; was 4.30:1)
- Night time-theme muted: #5a6878 → #8a9aaa (6.40:1; was 3.29:1)
- `--bwp-bg-subtle` adjusted: #f7f7f7 → #f5f5f5

**1.4.11 Non-text Contrast — UI component boundaries**
- Night toggle button, search form container, and search input divider: `--bwp-border` → `--bwp-border-ink` (≥3:1; was 1.61:1)

**2.4.1 Bypass Blocks**
- Skip link added as first body element: "Skip to content" → `#bwp-main`; visually hidden until focused

**1.3.1 Info & Relationships / Landmark regions**
- `<section class="bwp-page-wrap">` → `<div>` (section without accessible name)
- `<main id="bwp-main">` added to all page templates (index, single, page, 404, search, archive)
- `<section class="bwp-book-title">` and `.bwp-front-blurb` → `<div>` (structural containers)
- `<section id="comments">` given `aria-label="Comments"`
- `<header>` given `role="banner"`

**2.4.6 Headings — corrected hierarchy**
- Site title `<h1>` conditional: `<h1>` on front page only, `<p>` elsewhere — eliminates duplicate `<h1>` on single post pages

**3.3.2 Labels / 4.1.2 Name, Role, Value**
- Search input: visually-hidden `<label>` added; submit button given `aria-label="Search"`
- Night toggle: `aria-pressed` added to HTML; JS updates it on click

**2.4.7 Focus Visible**
- `outline: none` removed from search inputs
- Global `:focus-visible` rule added (3px solid `--bwp-link`, 2px offset)

**2.3.3 Animation from Interactions**
- `@media (prefers-reduced-motion: reduce)` block added — disables all animations and transitions

---

## 3.2.5 — 2026-02-28

### Design refinements — structure and layout corrections post-redesign

Addressed visual sparseness and layout bugs introduced by the 3.2.0 minimalist overhaul. All changes are to `frontend.css` and colour scheme files; no template or PHP logic changes.

**Layout fixes**
- Front page — `.bwp-book-title`, `.bwp-site-header`, `.bwp-toc-header`, and `.bwp-front-blurb` were left-aligned during the redesign; centred layout restored throughout
- Front-page hero — orphan ornament rendering above the anthology title removed; title and subtitle now open the section directly

**Visual refinements**
- Hero — `--bwp-bg-subtle` tint applied to the hero block; `2px --bwp-border-ink` rule added below anthology title; padding increased
- TOC rows — separators upgraded from `--bwp-border-faint` (#eeeeee) to `--bwp-border` (#cccccc); link padding and hover indent increased
- TOC group count — changed from filled black square to outlined box matching badge language
- Badges — puisi badge uses `--bwp-accent`; other forms use `--bwp-border-ink`; all remain outline-only
- Epigraph — left border increased to 3px, uses `--bwp-border-ink`
- Puisi title rule — uses `--bwp-border-ink` instead of `--bwp-text` (consistent with badge language)
- Page wrap — widened to 40em; side padding increased to 2em

**Token added**
- `--bwp-border-ink` (#aaaaaa) — mid-tone structural colour between `--bwp-border` and `--bwp-text`; defined in all colour schemes and night mode

---

## 3.2.3 — 2026-02-28

### Language handling — admin English-only + delegate front-end to WordPress core locale

**Admin interface**
- All admin interface text is now English only; WordPress admin language is controlled by WordPress Settings → General → Site Language
- "Sastera" settings tab renamed to "Literary"
- Bilingual admin field labels (e.g. `Anthology Name / Nama Antologi`) replaced with plain English
- Settings page subtitle simplified to "Literary publishing theme for WordPress"
- Shortcode attribute names anglicised: `[pemisah gaya=""]` → `[pemisah style=""]`; `[istilah takrifan=""]` → `[istilah definition=""]`; old Malay names kept as silent aliases

**Front-end language**
- Removed the custom Interface Language setting entirely; front-end labels now follow WordPress's own `get_locale()`, set via Settings → General → Site Language
- General tab: language dropdown replaced with a one-line info notice directing users to the WordPress core setting
- Fixed: front-end was showing Malay despite the WordPress site language being set to English — the custom `book_wp_language` option had `ms` stored in the database from an earlier session, overriding the PHP default. Delegating to `get_locale()` means front-end language is always consistent with WordPress, with no stale database value able to override it

**Verse spacing fixes**
- Stanza spacing across all verse forms — `white-space: pre-line` was set on both the content container and `<p>` elements in Puisi, the generic verse template, and the base `frontend.css` verse rules. Blank lines typed in the editor were rendering as whitespace on top of the CSS inter-stanza margin, producing excessive gaps. `pre-line` removed from all verse containers; stanza breaks use `<p>`, line breaks within stanzas use `<br>` (Shift+Enter)
- Conflicting `p + p` margin-top — `frontend.css` was setting `margin-top: 1.6em` on `.bwp-form-puisi p + p`, competing with the `1.4em` override in `puisi.css`; base rule now only resets `text-indent`; margin owned by each form's CSS
- Puisi title rule colour — decorative rule under the work title was using `--bwp-link` (blue); changed to `--bwp-border-ink`
- Puisi epigraph border — was using `--bwp-link`; changed to `--bwp-border`

---

## 3.2.0 — 2026-02-28

### Minimalist-modern visual overhaul

**Changed**
- Removed all decorative apparatus — gradient hero backgrounds, arabesque SVG corner ornaments, gold inner frame borders, gold gradient rules, large pseudo-element quote marks on epigraphs and petikan blocks
- Palette — pure white (`#ffffff`) replaces warm sepia (`#faf8f4`); body text `#111111`; single structural accent `#1B3C53` for interactive elements only
- Body font — replaced Lora with EB Garamond at 18px / line-height 1.9
- Display font — Cinzel applied to site name, book title, work titles, TOC headers, group headings
- Site header — dark teal background removed; white with single `1px` bottom border; title in Cinzel small caps at `0.85rem`
- Book title hero — background removed; large left-aligned Cinzel heading on white
- Badges — filled coloured pills → outline rectangles in Cinzel; colours unified to border/muted ink
- Drop cap colour — `--bwp-link` → `--bwp-text`
- Border radius — `3px` → `0` throughout; square corners
- `literary.css` — reduced from 461 to 83 lines; now handles colophon form-specific elements and night mode overrides only
- Time-of-day theme files — rewritten as palette-only token overrides; removed background gradients on `.bwp-site-header` and `.bwp-book-title` that were silently overriding all CSS edits

**Removed**
- Sajak taxonomy term — dissolved into Puisi; `sajak.css` deleted; styles merged into `puisi.css`
- Archivist-only metadata fields — removed fields for cataloguing others' work: `narrative_moment`, `movement`, `poetic_subform`, `narrator`, `life_stage`, `source_work_*`

**Added**
- Generic verse page template (`templates/single-puisi-generic.php`) — for ghazal, elegi, terzarima, ode, balada; select via Page Attributes → Template
- `book_wp_inspiration` (Ilham) added to all verse forms; `book_wp_duration_composed` to Ratapan; `book_wp_verification_note` to Autobiografi

---

## 3.1.0 — 2026-02-28

### Architecture overhaul + settings data-loss fix

**Fixed**
- Settings data-loss bug — saving any tab no longer wipes other tabs' data. Each tab now has its own independent `<form>` posting to `admin-post.php` via a `book_wp_save_tab` handler. Only the active tab's options are ever written

**Changed**
- Typography — replaced Georgia with Lora (body) + Jost (UI); recalibrated sizes, line-heights, letter-spacing
- Ornament system — replaced Unicode entities with inline SVG paths; ornaments render correctly with any body font
- Admin settings — tab switching is full page navigation (anchor links) instead of JS show/hide; each tab is a self-contained form
- Save button label updates per tab (e.g. "Save Typography Settings")

**Architecture**
- `inc/` restructured to standalone theme best-practice spec: `inc/setup/`, `inc/helpers/`, `inc/hooks/`, `inc/shortcodes/`, `inc/admin/`
- `functions.php` reduced to 39-line bootstrap loader
- Page templates moved to `templates/` with `template-` prefix
- Admin-only files loaded exclusively inside `is_admin()` guard

---

## 3.0.0 — 2026-02-28

Complete sastera (literary) rebuild. Every file rewritten.

**Added**
- Anthology name setting, category support, TOC grouping (flat / by form / by category)
- Literary metadata: epigraph, dedication, colophon, per-work ornament override, TOC excerpt
- Seven typographic ornaments as inline SVG
- Reading analytics bar: form badge, category, reading time, word count, line count, stanza count
- Reading experience: progress bar, keyboard navigation, night mode, scroll position memory
- Related works, previous/next navigation
- Bilingual interface — 40+ BM/EN string pairs
- Four colour schemes, seven font families
- Shortcodes: `[petikan]`, `[pemisah]`, `[bait]`, `[istilah]`
- Page templates: Mukadimah, Kolofon
- JSON-LD Schema.org/CreativeWork structured data
- Custom taxonomy `literary_form` with default terms: puisi, nukilan, ratapan

---

## 2.1.0 — 2026-02-27

- Modernised admin settings with wpcp- design conventions
- Added ornament system (3 styles)
- Bilingual string framework
- Basic TOC with badges

---

## 2.0.0 — 2026-02-27

- Rewrote theme as Sasterawan for literary publishing
- Custom taxonomy: literary_form (puisi, nukilan, ratapan)
- Post meta fields for year, source, dedication
- Basic front-page Table of Contents
- Colour scheme support (cream, dusk, night)

---

## 1.0.0 — 2026-02-26

- Initial release as a minimal literary theme
