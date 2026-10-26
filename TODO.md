# Sasterawan — Review TODO

Prioritised from the codebase review (v3.5.1). Each item names the file and line to start from.

## P0 — Breaks the site or loses data

- [x] **Night-time ink on light schemes is unreadable.** `assets/css/times/times.css:87` sets light `--bwp-text` on `:root.bwp-time-night`, but `assets/css/schemes/cream.css:58` and `dusk.css:116` only override surfaces. Cream/Dusk sites show ~1.1:1 contrast every night 21:00–03:59. Resolve ink and surface together per scheme × mode.
- [x] **Night toggle broken on Cream/Dusk.** `assets/css/frontend.css:1330` (`html.bwp-night body`) loses to the scheme's `:root.bwp-time-X body.bwp-scheme-*` rules; ink goes dark while paper stays light. Give the toggle one higher-specificity rule.
- [x] **Meta box silently discards edits.** `inc/admin/meta-boxes.php:224/269, 398/458` etc. render `book_wp_occasion`, `book_wp_setting`, `book_wp_theme`, `book_wp_inspiration`, `book_wp_rhyme_scheme`, `book_wp_first_published`, `book_wp_point_of_view`, `book_wp_annotation` once per form group; hidden groups still submit and the last duplicate wins. Render shared fields once, or namespace per form.
- [x] **Legacy-slug redirect kills pagination/feeds.** `inc/helpers/permalink.php:277-314` 301s any single URL ≠ `get_permalink()`, so `/form/slug/2/`, `comment-page-2/`, `feed/` redirect to page 1. Remove it (core `redirect_canonical` already covers the legacy case) or compare against `$wp->request` with pagination stripped.
- [x] **Permalink toggle flushes stale rules.** `inc/helpers/permalink.php:176-181` flushes before the new rules are registered; links 404 until Permalinks are re-saved. Set the deferred flush flag instead.
- [x] **Test suite cannot boot.** `tests/bootstrap.php` lacks `add_action`/`add_filter`/`apply_filters` stubs; `inc/helpers/navigation.php:263` fatals on load. Add stubs, then fix the 2 real failures below.

## P1 — Features quietly fail

- [ ] **Prev/Next navigation misuses `get_adjacent_post`.** `inc/helpers/navigation.php:153` passes post IDs as excluded *term* IDs; hidden-tag neighbours cause 10 wasted queries then no nav.
- [ ] **Hidden posts still appear in widgets.** `inc/hooks/actions.php:635` `doing_action('dynamic_sidebar')` is never true during widget render.
- [x] **Stanza count regex merges paragraphs.** `inc/helpers/analytics.php:304` → use `/<p(?:\s[^>]*)?>(.*?)<\/p>/si`. Fixes two failing tests in `tests/AnalyticsTest.php:165,231`.
- [ ] **Masonry + infinite scroll overlap.** `assets/js/frontend.js:298-304, 381-386` lays out items on the `load` event before they are appended; use `append`. Also guard the null `msnry` race with `document.fonts.ready`.
- [x] **Undefined tokens `--bwp-ink` / `--bwp-paper`.** `assets/css/frontend.css:1042, 2542` — back-to-top button and "Copied!" tooltip render transparent. Use `--bwp-text` / `--bwp-bg`.
- [x] **Stray `</div>` in meta box.** `inc/admin/meta-boxes.php:344, 601` closes the postbox early and pushes later groups outside `.bwp-metabox`.
- [ ] **Front-end CSS leaks into wp-admin.** `inc/setup/enqueue.php:367-372` enqueues unscoped `frontend.css` via `enqueue_block_editor_assets`; use the existing `add_editor_style()` (`theme-support.php:49`) and set `apiVersion: 3` in `assets/js/blocks.js:157,230,293`.
- [x] **Dead `body.bwp-time-night` rules.** `assets/css/frontend.css:945,952,1064,1073,2109,2118` — the class is only ever set on `<html>`.
- [ ] **Colophon double-escapes separators.** `inc/helpers/colophon.php:113,165,180,193,206,236,249` — `esc_html(implode(' &middot; ', …))` prints literal `&middot;`.
- [ ] **Word counts are ASCII-only.** `inc/helpers/analytics.php:240,251`, `inc/hooks/actions.php:277,304` — `str_word_count` reports 0 words for Jawi/Arabic; use `preg_match_all('/\p{L}[\p{L}\p{Mn}\'’-]*/u')`.
- [ ] **Global arrow-key handler hijacks browser shortcuts.** `assets/js/frontend.js:77-113` — ignore events with modifiers, contenteditable and media targets; stop `preventDefault` on ↑/↓.
- [ ] **TOC transient defeats cache priming (N+1).** `index.php:30-39`, `inc/helpers/navigation.php:193-241` — call `update_meta_cache()` / `update_object_term_cache()` after restoring from transient.
- [ ] **Deprecated `trim(null)`.** `inc/helpers/permalink.php:229,429` — guard `parse_url()` result with `(string)`.

## P2 — Admin UI, assets and conventions

- [ ] **Move inline CSS/JS out of PHP.** `inc/admin/meta-boxes.php:604-669` ships a `<style>` and `<script>` block → `assets/css/admin.css` and new `assets/js/admin-meta-boxes.js`. ~45 inline `style=""` attributes in `inc/admin/settings.php` (e.g. `:597,615,1025-1054`) → classes in `admin.css`; keep only data-driven swatch styles as custom properties.
- [ ] **Register all options.** `book_wp_share_row`, `book_wp_back_to_top` are saved (`settings.php:179-180`) but missing from `register_setting` (`:20-82`). Whitelist select/radio values at save time.
- [ ] **Remember active settings tab** (user meta) and warn on unsaved changes when switching tabs. Add an error state to the save notice (`settings.php:305-310`).
- [ ] **Promote `.bwp-section__label` from `<p>` to headings** (`settings.php:403,579`); drop redundant `aria-label` on tab links (`:323`); update `aria-selected` in the tag combobox (`assets/js/admin-settings.js:204-237`).
- [x] **Local fonts by default.** `inc/setup/enqueue.php:25-54,106-124` loads Google Fonts with `book_wp_remote_fonts` on; self-host the woff2 files under `assets/fonts/` for privacy and design stability.
- [ ] **Night-mode localStorage expiry.** `assets/js/time-theme.js:95` writes `'0'` instead of `removeItem`, permanently disabling the OS dark-mode fallback.
- [ ] **Smooth-scroll restore jank.** `assets/js/frontend.js:189-196` — pass `behavior: 'auto'`.
- [ ] **i18n sweep.** Untranslated strings in `meta-boxes.php:36-150,169,177,242-246,448`, `settings.php:254,280-285,299-300,332,352`, `admin-settings.js:192,201,228`, `frontend.js:433,443,448`. Add `wp_set_script_translations` for admin JS; generate the `-{md5}.json` files WordPress expects for `blocks.js`.
- [ ] **Templates missing `<main id="bwp-main">`.** `templates/*.php` — skip link and layout CSS target nothing.
- [ ] **`body_class` filter added too late.** `single.php:88-91` runs after `header.php:32` printed the class; `bwp-no-work-nav` never appears.
- [ ] **Guard comment callbacks** with `function_exists()` (`comments.php:116-179`).
- [ ] **Term descriptions** through `wp_kses_post`, not `esc_html` (`archive.php:23`, `tag.php:19`, `index.php:144`).
- [ ] **Shortcode collisions.** `inc/shortcodes/shortcodes.php:45-68,285-290` — prefix `[v]`, `[term]`, `[quote]` aliases or make them opt-in.
- [ ] **theme.json palette is static** (`theme.json:493-564`); `editor.css:15-19` hard-codes EB Garamond. Follow the chosen scheme/font in the editor.

## P3 — Tooling, docs, lint

- [ ] **PHPCS scope.** `config/.phpcs.xml.dist:6` `<file>.</file>` resolves to `config/`; point it at the theme root.
- [ ] **ESLint errors** (`frontend.js:53,63,139,190`) and 185 Stylelint violations under the repo's own config; run `npm run lint:*:fix` then fix the remainder (18 duplicate selectors, hex lengths, media range notation).
- [ ] **Changelog versions.** `docs/changelog.md:381-471` has seven `[3.4.90]` entries; `readme.txt:363-375,537-557` repeats 3.4.90 five times and skips 3.4.61–3.4.89. Renumber.
- [ ] **False doc claims.** `docs/readme.md:327` (admin JS tab switching/live preview does not exist), `:550` (no `.github/workflows/ci.yml`). Add a CI workflow or remove the claim. Stale 5-tab comment at `settings.php:8`.
- [ ] **Dead code.** `book_wp_sanitize_tag_slugs` (`settings.php:98-102`); duplicate `wp_get_object_terms` call (`navigation.php:43-59`); redundant `is_archive() || is_tag()` (`enqueue.php:292`, `filters.php:71`).
- [ ] **Dashboard widget "shortest" logic** never updates (`inc/admin/dashboard-widget.php:124`).
- [ ] Add `X-Domain: book-wp` to `languages/book-wp.pot`.

## Design evaluation

Static renders of the real CSS (28 screenshots in `docs/design-review/`, 1440px and 390px, all four schemes). Scores out of 10: aesthetic 8, scheme consistency 8, contrast 7, hierarchy 7, admin UI 7, typographic rhythm 6, responsive 6, token system 6, reading affordances 5.

- [x] **Back-to-top and copy tooltip are invisible on every scheme.** Alias `--bwp-ink`/`--bwp-paper` to `--bwp-text`/`--bwp-bg` in `:root` (`assets/css/frontend.css:25`); `--bwp-heading` is also undefined in `templates/single-puisi-generic.php:99`.
- [x] **Prose measure too wide.** Cerpen ≈84ch and esei 40em exceed the 75ch ceiling; default page wrap 48em ≈90ch. Cap at 34em (`assets/css/forms/cerpen.css:19`), 36em (`forms/esei.css:19`) and add a `.bwp-work__content{max-width:36em}` fallback at `frontend.css:584`. Poem column (≈58ch) is right.
- [x] **Cerpen title stacks four rules within 80px** (title border, `::after` accent, analytics top and bottom borders). Drop `forms/cerpen.css:29` or `:37-43`, and the `.bwp-analytics` border-top at `frontend.css:472`.
- [x] **Fixed controls overlap the text column at 390px.** Shrink and re-corner the night toggle and back-to-top in `@media (max-width:36em)` (`frontend.css:2383, 2528`); the two buttons also share the same corner.
- [ ] **Contrast floors.** Dusk muted ink is 4.53:1 at 13px uppercase; raise to ≈`#5a544c` (`schemes/dusk.css:19`). Footer credit at opacity .55 (2.1–2.9:1) and `.bwp-colophon__line--meta` opacity .7 (`literary.css:41`) fail AA; replace opacity dimming with a `--bwp-muted-faint` token. Night-scheme link vs text is 1.47:1, so keep underlines.
- [x] **Single-source the night palette.** It is duplicated at `frontend.css:1115`, `:1140` and `schemes/night.css:19`; make toggle, scheme and OS media query consume one block.
- [x] **Mobile nav wraps to two rows with a 1em gap** and the 2.8em hero title breaks to three lines at 390px; tighten the row gap and use `clamp()` for the title.
- [ ] **Admin polish.** General-tab hints render under the label because `.bwp-field__hint` sits outside `.bwp-field__control` (`settings.php:449-476`); "Show Copyright" is a bare checkbox next to styled toggles (`:478-487`); drop-cap font cards all show the same glyph because `assets/css/dropcap/*.css` is not enqueued in admin; the "Georgia / Default" card is mislabelled, the front end defaults to EB Garamond (`frontend.css:46`).
- [ ] **Token architecture.** Add a semantic layer (`--bwp-surface-raised`, `--bwp-on-accent`), retire legacy aliases leaking into form CSS (`esei.css:33`, `cerpen.css:76`), and make `theme.json` read the active scheme.
- [ ] **User-configurable colours.** Add paper / ink / accent colour pickers on the Typography tab that emit `body.bwp-scheme-custom{…}` with WCAG validation at save time, so the colour scheme is genuinely variable rather than a fixed set of presets.
