# Sasterawan — Architecture, Upgrades & Roadmap

**Version:** 4.0.0  
**Type:** Classic PHP template theme (not FSE)  
**WordPress:** 6.0+ required, tested to 6.9.1  
**PHP:** 8.2+  
**Text domain:** `book-wp`  
**Theme slug:** `menjmy` (folder); upgrades in place from `book-wp`

---


## Upgrading from Book WP 3.5.x

Sasterawan 4.0 is a visual rebuild on the same data model. Nothing in the
database needs converting.

**Unchanged, so content and settings carry over as they are**

| Layer | Names kept |
|---|---|
| Options | every `book_wp_*` option, including scheme, font, drop caps, analytics, TOC layout and the social profile fields |
| Post meta | every `book_wp_*` meta key written by the form meta boxes (dateline, location, year, source, epigraph, dedication, notes, hikayat citation, translation fields) |
| Taxonomy | `literary_form` and its term slugs; 4.0 seeds one new term, `terjemahan`, and leaves the rest alone |
| Menus | `primary` and `footer` locations |
| Widget areas | `sidebar-widgets` and `footer-widgets` |
| Blocks and shortcodes | `book-wp/chapter`, `book-wp/divider` and the existing shortcodes render under the new styles |
| Text domain | `book-wp`, so existing `.po`/`.mo` files still load |

Two options are no longer read: `book_wp_front_blurb` (the front page no
longer prints an intro) and `book_wp_show_nav` (previous/next navigation was
removed). Their stored values are harmless.

**Two ways to install**

1. *Same folder.* Replace the contents of `wp-content/themes/book-wp/` with
   this release. WordPress sees the same theme, and the logo, Customizer CSS
   and menu assignments stay as they were.
2. *New folder.* Install as `wp-content/themes/menjmy/` and activate it.
   WordPress treats a new folder as a new theme and would normally start the
   per-theme mods from empty. On activation Sasterawan copies them across
   from the `book-wp` folder: custom logo, header and background settings,
   the Customizer's Additional CSS, and nav menu locations as a fallback to
   core's own mapping. It never overwrites a value the new install already
   has, and it records what it copied in `book_wp_migrated_from`. Widget
   placements are restored by WordPress itself because the area IDs match.
   The old folder can be deleted once the new one is active.

**What looks different by design**

- Defaults for the palette swatches changed (Kapas / Malam); a stored scheme
  value still selects the same scheme.
- The front page opens with a cover photograph; switch it off under
  Settings → General if the old plain title is preferred.
- Previous/next work navigation and the front-page blurb are gone.

## Overview

Sasterawan is a minimalist literary anthology theme. Every structural decision is oriented around a single-author, multi-work anthology where posts are independent literary works — not blog entries, not chapters of one book. Works are organised by literary form (a custom taxonomy), by WordPress category, or by tag. The theme enforces this model throughout: the front page is a Table of Contents, archives are form/category/tag indexes, and single pages are reading environments.

---

## Directory Structure

```
book-wp/
│
├── style.css                    ← Theme header (version, description, metadata)
├── functions.php                ← Bootstrap only: constants + require_once chain
├── theme.json                   ← Design tokens for Gutenberg editor bridge
│
├── index.php                    ← Front page / Table of Contents
├── single.php                   ← Single work reading page
├── archive.php                  ← Literary form / category archives
├── tag.php                      ← Tag archives
├── page.php                     ← Static pages
├── search.php                   ← Search results
├── 404.php                      ← Not found
├── header.php                   ← Site header partial
├── footer.php                   ← Site footer partial
├── sidebar.php                  ← Widget area partial
├── comments.php                 ← Comment list and form
├── searchform.php               ← Search input form
│
├── templates/                   ← Custom page templates
│   ├── single-puisi-generic.php ← Single template: generic puisi (no chapter)
│   ├── template-colophon.php    ← Page template: Colophon page
│   └── template-foreword.php   ← Page template: Foreword page
│
├── inc/
│   ├── setup/
│   │   ├── theme-support.php    ← add_theme_support(), image sizes, nav menus
│   │   ├── menus.php            ← register_nav_menus()
│   │   ├── sidebars.php         ← register_sidebar()
│   │   └── enqueue.php          ← All script/style registration and enqueue
│   │
│   ├── helpers/
│   │   ├── literary.php         ← Form slug/label helpers, ornament output
│   │   ├── analytics.php        ← Reading metrics: time, words, lines, stanzas
│   │   ├── colophon.php         ← Epigraph, dedication, colophon block output
│   │   └── navigation.php       ← Related works, prev/next nav, TOC entry, featured image, progress bar
│   │
│   ├── hooks/
│   │   ├── actions.php          ← Taxonomy, meta registration, structured data, blocks, tag cap
│   │   └── filters.php          ← body_class, page templates, Search block translation
│   │
│   ├── shortcodes/
│   │   └── shortcodes.php       ← Six shortcodes: bwp_quote, bwp_divider, bwp_term, bwp_verse, bwp_chapter, bwp_v
│   │
│   ├── blocks/
│   │   └── chapter/
│   │       └── block.json       ← book-wp/chapter Gutenberg block definition
│   │
│   └── admin/
│       ├── meta-boxes.php       ← Per-form post meta boxes (769 lines)
│       └── settings.php         ← Theme Options page: 6 tabs
│
├── assets/
│   ├── css/
│   │   ├── frontend.css         ← Primary stylesheet: all base styles (~2164 lines)
│   │   ├── literary.css         ← Literary block styles (blockquote, verse, etc.)
│   │   ├── admin.css            ← Admin UI styles (settings page, meta boxes)
│   │   ├── editor.css           ← Gutenberg editor canvas mirroring
│   │   ├── forms/               ← Per-form overrides (10 files, one per literary form)
│   │   │   ├── puisi.css
│   │   │   ├── syair.css
│   │   │   ├── karmina.css
│   │   │   ├── ratapan.css
│   │   │   ├── hikayat.css
│   │   │   ├── cerpen.css
│   │   │   ├── novel.css
│   │   │   ├── nukilan.css
│   │   │   ├── esei.css
│   │   │   └── memoir.css
│   │   ├── schemes/             ← Colour scheme overrides (3 files; default is in :root)
│   │   │   ├── cream.css
│   │   │   ├── dusk.css
│   │   │   └── night.css
│   │   ├── fonts/               ← Font-family overrides (6 non-default fonts)
│   │   │   ├── lora.css
│   │   │   ├── vollkorn.css
│   │   │   ├── arvo.css
│   │   │   ├── maven.css
│   │   │   ├── ubuntu.css
│   │   │   └── amiri.css
│   │   ├── tags/                ← Per-tag decorative CSS (whitelist-gated; loaded on tagged single posts only)
│   │   │   └── erotika.css      ← Batik background for posts tagged 'erotika'
│   │   └── times/
│   │       └── times.css        ← Time-of-day colour overrides (all 5 periods in one file)
│   │
│   ├── img/
│   │   └── batik-erotika.png    ← Batik motif for erotika tag background (622 KB)
│   │
│   └── js/
│       ├── frontend.js          ← Reading enhancements (progress bar, keyboard nav, night mode, scroll memory, archive/TOC layout init)
│       ├── time-theme.js        ← Time-of-day theme switching (runs in <head>)
│       ├── admin-settings.js    ← Settings page UI (tabs, live preview)
│       ├── blocks.js            ← Gutenberg block registration (editor-side)
│       └── vendor/
│           ├── imagesloaded.min.js   ← v5.0.0 — gates Masonry until images are ready
│           ├── masonry.min.js        ← v4.2.2 — cascading column layout
│           └── infinite-scroll.min.js ← v5.0.0 — auto-appends next paginated page
│
└── languages/
    ├── book-wp.pot              ← Translation template
    ├── ms_MY.po / .mo           ← Bahasa Melayu
    ├── es_ES.po / .mo           ← Spanish
    └── fr_FR.po / .mo           ← French
```

---

## Bootstrap Chain

`functions.php` defines three constants then performs a strict require_once chain. No feature code lives in `functions.php`.

```
BOOK_WP_VERSION  ← read dynamically from style.css via wp_get_theme()
BOOK_WP_DIR      ← get_template_directory()
BOOK_WP_URI      ← get_template_directory_uri()
BOOK_WP_MAX_TAGS ← 5 (thematic tag cap per post)

inc/setup/theme-support.php
inc/setup/menus.php
inc/setup/sidebars.php
inc/setup/enqueue.php

inc/helpers/literary.php
inc/helpers/analytics.php
inc/helpers/colophon.php
inc/helpers/navigation.php

inc/hooks/actions.php
inc/hooks/filters.php
inc/shortcodes/shortcodes.php

if ( is_admin() ):
    inc/admin/meta-boxes.php
    inc/admin/settings.php
```

---

## Custom Taxonomy: `literary_form`

Registered on `init` in `actions.php`. Applied to `post`. Hierarchical: false. Public: true.

**Default terms** (seeded on activation, version-gated under `book_wp_terms_version`):

| Slug | Label | Type |
|------|-------|------|
| `puisi` | Puisi | Poetry |
| `syair` | Syair | Poetry |
| `karmina` | Karmina | Poetry |
| `ratapan` | Ratapan | Poetry |
| `hikayat` | Hikayat | Prose (classical) |
| `cerpen` | Cerpen | Prose (short fiction) |
| `novel` | Novel | Prose (long fiction) |
| `nukilan` | Nukilan | Prose (fragment/excerpt) |
| `esei` | Esei | Essay |
| `memoir` | Memoir | Memoir |

Poetry forms (`puisi`, `syair`, `karmina`, `ratapan`) are detected by `book_wp_is_poetry()` and receive distinct typography: narrower column, configurable line height, stanza spacing, optional stanza numbers, and no drop cap.

---

## Post Meta (wp_postmeta)

All keys registered via `register_post_meta()` in `actions.php` with `show_in_rest: false` (except `book_wp_chapter_number` and `book_wp_chapter_part` which are REST-exposed for the block editor).

**Universal meta (all forms)**

| Key | Purpose |
|-----|---------|
| `book_wp_year` | Year of composition |
| `book_wp_date_composed` | Full composition date |
| `book_wp_duration_composed` | Duration of composition |
| `book_wp_first_published` | First publication venue/date |
| `book_wp_dedication` | Dedication text |
| `book_wp_epigraph` | Epigraph text |
| `book_wp_epigraph_source` | Epigraph attribution |
| `book_wp_inspiration` | Notes on inspiration |
| `book_wp_notes` | General notes |
| `book_wp_annotation` | Scholarly annotation |
| `book_wp_location` | Location of composition |
| `book_wp_language` | Language of the work |
| `book_wp_drop_cap_override` | Per-post drop cap on/off override |
| `book_wp_toc_excerpt` | Custom excerpt shown in TOC |

**Form-specific meta** (selected)

| Key | Forms | Purpose |
|-----|-------|---------|
| `book_wp_metre` | Puisi, Syair, Karmina, Ratapan | Metrical scheme |
| `book_wp_pov` | Cerpen, Novel | Point of view |
| `book_wp_chapter_number` | Hikayat, Cerpen, Novel | Chapter number (REST-exposed) |
| `book_wp_chapter_part` | Hikayat | Chapter part (REST-exposed) |
| `book_wp_sampiran` | Pantun (within Puisi) | Sampiran (first couplet) |
| `book_wp_isi` | Pantun (within Puisi) | Isi (second couplet) |
| `book_wp_hikayat_title` | Hikayat | Source work title |
| `book_wp_citation_toggle` | Hikayat | Show "Cited from" label |
| `book_wp_methodology` | Esei | Essay methodology |
| `book_wp_memoir_tone` | Memoir | Tone classification |
| `book_wp_life_stage` | Memoir | Life stage |

---

## Theme Options (wp_options)

Managed via `inc/admin/settings.php`. Settings page at Appearance → Sasterawan. Six tabs.

### Tab 1 — General

| Option | Default | Purpose |
|--------|---------|---------|
| `book_wp_author_name` | — | Author name in masthead |
| `book_wp_anthology_name` | — | Collection title on front page |
| `book_wp_front_blurb` | — | HTML blurb below anthology name |
| `book_wp_copyright` | — | Footer copyright (HTML, `wp_kses_post`) |
| `book_wp_show_copyright` | `1` | Show/hide copyright in footer |
| `book_wp_toc_grouping` | `none` | TOC grouping: none / form / category / tag |
| `book_wp_show_badges` | `1` | Literary form badges in TOC |
| `book_wp_show_nav` | `1` | Prev/next navigation on single pages |
| `book_wp_toc_layout` | `list` | TOC layout (none grouping only): list / grid / masonry |
| `book_wp_toc_pagination` | `paged` | TOC pagination (none grouping only): paged / infinite |
| `book_wp_archive_layout` | `list` | Archive layout: list / grid / masonry |
| `book_wp_archive_pagination` | `paged` | Archive pagination: paged / infinite |

### Tab 2 — Typography

| Option | Default | Purpose |
|--------|---------|---------|
| `book_wp_font` | `georgia` | Body font: georgia / lora / vollkorn / arvo / amiri / maven / ubuntu |
| `book_wp_remote_fonts` | `1` | Load Google Fonts for UI/display/body font requests; disable for no-remote-fonts mode |
| `book_wp_color_scheme` | `default` | Colour scheme: default / cream / dusk / night |
| `book_wp_time_theme` | `auto` | Time-of-day palette: auto / dawn / morning / afternoon / dusk / night |
| `book_wp_poetry_align` | `left` | Poetry text alignment |
| `book_wp_poetry_line_height` | `normal` | Poetry line height: normal / relaxed / loose |
| `book_wp_stanza_spacing` | `comfortable` | Stanza gap: compact / comfortable / generous |
| `book_wp_stanza_numbers` | `0` | Show stanza numbers |
| `book_wp_drop_cap_cerpen` | `1` | Drop cap on cerpen |
| `book_wp_drop_cap_novel` | `1` | Drop cap on novel |
| `book_wp_drop_cap_hikayat` | `1` | Drop cap on hikayat |
| `book_wp_drop_cap_memoir` | `1` | Drop cap on memoir |
| `book_wp_drop_cap_esei` | `0` | Drop cap on esei |
| `book_wp_drop_cap_nukilan` | `0` | Drop cap on nukilan |

### Tab 3 — Literary

| Option | Default | Purpose |
|--------|---------|---------|
| `book_wp_ornament_style` | `fleuron` | Section ornament glyph |

### Tab 4 — Reading

| Option | Default | Purpose |
|--------|---------|---------|
| `book_wp_show_featured` | `1` | Featured image on single pages |
| `book_wp_show_analytics` | `1` | Analytics bar (reading time, word count, etc.) |
| `book_wp_show_tags` | `1` | Tag display on single pages |
| `book_wp_progress_bar` | `1` | Reading progress bar |
| `book_wp_keyboard_nav` | `1` | Arrow-key navigation between works |
| `book_wp_reading_memory` | `1` | Scroll position memory per post |
| `book_wp_night_toggle` | `1` | Night mode toggle button in header |
| `book_wp_share_row` | `0` | Share row on single posts (Copy link, X, Facebook, WhatsApp) |
| `book_wp_comments` | `0` | Comments section on single pages |
| `book_wp_hidden_tags` | `[]` | Tag slugs that hide posts from TOC/archives |

### Tab 5 — Advanced

| Option | Default | Purpose |
|--------|---------|---------|
| `book_wp_structured_data` | `1` | JSON-LD Schema.org/CreativeWork output in `<head>` |
| `book_wp_social_meta_tags` | `1` | OpenGraph + Twitter Card meta in `<head>` (auto-disabled when SEO plugin detected) |
| `book_wp_json_feed` | `1` | JSON Feed v1.1 at /feed/json |
| `book_wp_form_permalinks` | `0` | Literary form URL prefix — /{form}/{post-slug}/ (requires permalink flush) |
| `book_wp_custom_css` | — | Custom CSS attached with `wp_add_inline_style()` after active theme styles |

### Tab 6 — Social

17 social profile URL fields, sanitised with `esc_url_raw`. All empty by default.

| Option | Platform |
|--------|----------|
| `book_wp_social_twitter` | Twitter / X |
| `book_wp_social_facebook` | Facebook |
| `book_wp_social_instagram` | Instagram |
| `book_wp_social_threads` | Threads |
| `book_wp_social_linkedin` | LinkedIn |
| `book_wp_social_youtube` | YouTube |
| `book_wp_social_medium` | Medium |
| `book_wp_social_wikipedia` | Wikipedia |
| `book_wp_social_github` | GitHub |
| `book_wp_social_goodreads` | Goodreads |
| `book_wp_social_worldcat` | WorldCat |
| `book_wp_social_loc` | Library of Congress |
| `book_wp_social_nlb` | NLB Singapore |
| `book_wp_social_pnm` | Perpustakaan Negara Malaysia |
| `book_wp_social_orcid` | ORCID |
| `book_wp_social_isni` | ISNI |
| `book_wp_social_rss` | RSS (falls back to `get_feed_link()` if empty) |

Populated URLs render as circular icon buttons in the site footer via `book_wp_social_icons()` in `inc/helpers/social.php`. All links carry `rel="noopener noreferrer me"`.

---

## Asset Pipeline

### CSS Load Order

All stylesheets registered and enqueued in `inc/setup/enqueue.php`. Load sequence per page:

```
1. style.css                  ← WordPress requires this; contains only the theme header
2. assets/css/frontend.css    ← Base styles, CSS custom properties, all components
3. assets/css/literary.css    ← Literary block styles (always; dep: frontend)
4. assets/css/times/times.css ← Time-of-day ink palette (dep: literary)
5. assets/css/forms/{s}.css   ← Per-form CSS (single pages only; dep: times)
6. assets/css/tags/{t}.css    ← Per-tag decorative CSS (single pages, whitelist-gated; dep: frontend)
7. assets/css/schemes/{n}.css ← Colour scheme surface override (if not default; dep: times —
                                 loads after times so compound selectors resolve correctly)
8. assets/css/fonts/{f}.css   ← Font-family override (if not Georgia; dep: frontend)
9. book-wp-custom             ← Inline custom CSS handle (dep: active theme styles)
10. assets/css/admin.css      ← Admin only
11. assets/css/editor.css     ← Block editor canvas only
```

### CSS Custom Properties — Paper & Ink

Defined on `:root` in `frontend.css`. The colour system follows a strict two-layer
contract: **Scheme owns surface (Paper). Time owns ink (Ink).**

Time sets ink tokens on `:root` (= `<html>`) via `bwp-time-*` classes applied by
`time-theme.js`. Scheme sets surface tokens on `body.bwp-scheme-*` via `body_class()`.
Body-level surface declarations beat inherited time-level surface values — no
specificity fight needed.

| Group | Owned by | Tokens |
|-------|----------|--------|
| Surface | Scheme | `--bwp-bg`, `--bwp-bg-subtle`, `--bwp-border`, `--bwp-border-faint`, `--bwp-stone` |
| Ink | Time | `--bwp-text`, `--bwp-text-mid`, `--bwp-muted`, `--bwp-link`, `--bwp-link-hover`, `--bwp-accent`, `--bwp-ornament`, `--bwp-progress`, `--bwp-border-ink`, `--bwp-teal`, `--bwp-teal-deep`, `--bwp-gold`, `--bwp-gold-dim`, `--bwp-gold-light` |
| Typography | — | `--bwp-font`, `--bwp-font-ui`, `--bwp-font-display`, `--bwp-font-size`, `--bwp-lh` |
| Layout | — | `--bwp-radius`, `--bwp-shadow`, `--bwp-shadow-lg` |

Night scheme is the exception: it sets both surface and ink because a dark
background requires light text. Five night activation paths (manual toggle,
body.bwp-night, scheme-night, time-night, prefers-color-scheme) all carry
identical token sets. Per-form day palettes (hikayat, ratapan, memoir) set
surface tokens only, with `:not(.bwp-scheme-cream):not(.bwp-scheme-dusk)` guards.

### CSS Class Prefix

All theme classes use the `bwp-` prefix with BEM-style naming: `.bwp-block__element--modifier`.

### JavaScript Load Order

```
1. time-theme.js      ← In <head>, synchronous. Applies bwp-time-{period} to <html>;
                         expires stale night toggle in localStorage. Re-evaluates on
                         visibilitychange and hourly interval (auto mode only).
2. (preload script)   ← Inline <script> after wp_head() in header.php. Reads localStorage
                         for night mode (after time-theme.js may have expired it); applies
                         html.bwp-night if still active.
3. frontend.js        ← Footer, after DOM. IIFEs for: progress bar, keyboard nav,
                         night toggle (stores bwp_night_date), scroll memory,
                         stanza navigation, archive/TOC layout init.
4. admin-settings.js  ← Admin only. Tab switching, live font/scheme preview.
5. blocks.js          ← Block editor only. Registers book-wp/term Format API,
                         book-wp/quote, book-wp/divider, book-wp/verse blocks.
5. vendor/imagesloaded.min.js  ← Registered; enqueued only when archive/TOC layout = masonry
6. vendor/masonry.min.js       ← Registered; enqueued only when archive/TOC layout = masonry
7. vendor/infinite-scroll.min.js ← Registered; enqueued only when pagination = infinite
```

The `bookWP` JS object (set via `wp_localize_script` on `book-wp-time-theme`) exposes all reading and layout settings to both `time-theme.js` and `frontend.js`.

### Image Sizes

| Handle | Dimensions | Crop | Use |
|--------|-----------|------|-----|
| `book-wp-cover` | 800 × 400 | Hard | Featured image on single post pages |
| `book-wp-thumb` | 80 × 80 | Hard | TOC flat-list row thumbnail |

---

## Body Class System

`book_wp_body_classes()` (filter: `body_class`) adds context classes consumed by both CSS and JS.

| Class | Condition |
|-------|-----------|
| `bwp-scheme-{n}` | Always — active colour scheme slug |
| `bwp-font-{n}` | Always — active font slug |
| `bwp-lang-{n}` | Always — first two chars of locale |
| `bwp-dropcap-{form}` | When drop cap enabled for that form |
| `bwp-dropcap-force-on` | Single page, per-post override on |
| `bwp-dropcap-force-off` | Single page, per-post override off |
| `bwp-poetry-lh-{n}` | Always — poetry line-height setting |
| `bwp-stanza-{n}` | Always — stanza spacing setting |
| `bwp-stanza-nums` | When stanza numbers enabled |
| `bwp-form-{slug}` | Single pages only — literary form of the post |
| `bwp-night` | JS-applied — when night mode is active |
| `bwp-time-{period}` | JS-applied — time-of-day period |
| `bwp-is-archive` | Archive and tag pages |
| `bwp-archive-grid` | Archive/tag pages, layout = grid |
| `bwp-archive-masonry` | Archive/tag pages, layout = masonry |
| `bwp-is-toc-flat` | Front page when TOC grouping = none |
| `bwp-toc-grid` | Front page, grouping = none, layout = grid |
| `bwp-toc-masonry` | Front page, grouping = none, layout = masonry |

Per-form CSS files are scoped to `.bwp-form-{slug}` so overrides apply only on the relevant single page and never bleed into archive cards.

---

## Template Architecture

### Front Page — `index.php`

Sole template for the anthology front page. Reads `book_wp_toc_grouping` and branches:

```
grouping = 'form'      → groups posts by literary_form taxonomy terms
grouping = 'category'  → groups posts by WordPress category
grouping = 'tag'       → groups posts by tag (post appears under every tag it carries)
grouping = 'none'      → flat list, supports grid/masonry layout and infinite scroll

  Pagination:
    toc_pagination = 'paged'    → transient-cached get_posts(-1), all works at once
    toc_pagination = 'infinite' → paginated WP_Query, posts_per_page from WP settings,
                                  post__not_in for hidden tags (DB-level exclusion)
```

All TOC entries output via `book_wp_toc_entry( $post, $show_badges, $show_thumb )`. Third arg `true` only in the none-grouping branch.

Hidden-tag exclusion runs as PHP `array_filter` on the cached `$all_posts` array in grouped/non-paginated paths. Moves to `post__not_in` WP_Query arg in the paginated IS path.

### Single Post — `single.php`

Standard WordPress single template. Calls helpers in sequence:

```
book_wp_analytics_bar()   ← reading time / word count / line count / stanza count
book_wp_night_toggle_markup()
book_wp_progress_bar_markup()
book_wp_dedication()
book_wp_epigraph()
book_wp_featured_image()  ← gated by book_wp_show_featured option
the_content()
book_wp_colophon()
book_wp_related_works()
book_wp_work_navigation()
```

Author's Note (`book_wp_author_note`) is injected as a full-width banner between the analytics bar and the content body.

### Archives — `archive.php` / `tag.php`

Paginated list views. Cards output title + form badge + reading time + year. No featured images in archive cards (by design — consistent card height).

Supports layout and pagination settings:
- `book_wp_archive_layout`: list / grid / masonry (CSS body class-driven)
- `book_wp_archive_pagination`: paged / infinite (vendor JS)

### Custom Page Templates

Registered via `theme_page_templates` filter in `filters.php`. Selectable in the WordPress page editor under Page Attributes.

| File | Template name | Purpose |
|------|---------------|---------|
| `templates/single-puisi-generic.php` | Single — Puisi (Generic) | Single template for puisi posts without chapter structure |
| `templates/template-colophon.php` | Colophon Page | Static colophon/imprint page |
| `templates/template-foreword.php` | Foreword | Static foreword/preface page |

---

## Hooks Summary

### `inc/hooks/actions.php`

| Hook | Function | Purpose |
|------|----------|---------|
| `init` | `book_wp_register_taxonomy` | Register `literary_form` taxonomy; seed/migrate default terms |
| `init` | `book_wp_register_meta` | Register all 50+ post meta keys via `register_post_meta()` |
| `init` | `book_wp_register_blocks` | Register `book-wp/chapter` block with PHP render callback |
| `wp_head` | `book_wp_structured_data` | Output JSON-LD schema on single posts |
| `wp_body_open` | `book_wp_progress_bar_markup` | Reading progress bar HTML |
| `wp_footer` | `book_wp_night_toggle_markup` | Night mode toggle button |
| `after_switch_theme` | `book_wp_on_activation` | Seed terms, flush rewrite rules |
| `save_post`, `trashed_post`, `untrashed_post` | `book_wp_clear_toc_cache` | Invalidate `book_wp_toc_posts` transient |
| `save_post` | `book_wp_enforce_tag_cap` | Enforce BOOK_WP_MAX_TAGS (5) on post save |
| `rest_after_insert_post` | `book_wp_enforce_tag_cap_rest` | Same enforcement via REST API |
| `pre_get_posts` | `book_wp_hide_from_public_queries` | Exclude hidden-tagged posts from home, category, date, author, search, feed, and non-hidden tag archives |
| `pre_get_posts` | `book_wp_hide_from_widget_queries` | Exclude hidden-tagged posts from sidebar widget secondary queries |
| `rest_post_query` | `book_wp_hide_from_rest_query` | Exclude hidden-tagged posts from REST API requests (unauthenticated / non-editor) |
| `wp_sitemaps_posts_query_args` | `book_wp_hide_from_sitemap` | Exclude hidden-tagged posts from core XML sitemap |
| `rank_math/sitemap/html_sitemap/post_args` | `book_wp_hide_from_rankmath_html_sitemap` | Exclude hidden-tagged posts from RankMath HTML sitemap |
| `widget_tag_cloud_args` | `book_wp_hide_tags_from_cloud` | Suppress hidden tags from Tag Cloud widget |

### `inc/hooks/filters.php`

| Hook | Function | Purpose |
|------|----------|---------|
| `body_class` | `book_wp_body_classes` | Add all `bwp-*` context classes |
| `theme_page_templates` | `book_wp_register_page_templates` | Register three custom PHP page templates |
| `render_block_core/search` | `book_wp_translate_search_block` | Translate Search block button text |
| `the_tags` | `book_wp_suppress_hidden_tags_from_output` | Remove hidden tag links from any output of `the_tags()` or `get_the_tag_list()` |

---

## Helper Functions

### `inc/helpers/literary.php`

| Function | Purpose |
|----------|---------|
| `book_wp_get_form_slug( $post_id )` | Returns literary_form term slug for a post |
| `book_wp_get_form_label( $post_id )` | Returns human-readable form label for a post |
| `book_wp_form_label( $slug )` | Returns label for a given slug |
| `book_wp_is_poetry( $post_id )` | True if form is puisi / syair / karmina / ratapan |
| `book_wp_form_description( $slug )` | Returns editorial description for archive header |
| `book_wp_ornament( $style )` | Returns SVG/HTML ornament markup |

### `inc/helpers/analytics.php`

| Function | Purpose |
|----------|---------|
| `book_wp_get_full_text( $post_id )` | Extracts plain text, stripping shortcodes and block markup |
| `book_wp_extract_chapter_text( $raw )` | Extracts chapter block text via `parse_blocks()` when available |
| `book_wp_reading_time_minutes( $post_id )` | Returns numeric reading-time estimate in minutes |
| `book_wp_reading_time( $post_id )` | Returns formatted reading time string |
| `book_wp_word_count( $post_id )` | Word count |
| `book_wp_line_count( $post_id )` | Line count (counts `<br>` and newlines) |
| `book_wp_stanza_count( $post_id )` | Stanza count (counts blank lines between verse blocks) |
| `book_wp_analytics_bar( $post_id )` | Renders the analytics bar HTML |

### `inc/helpers/colophon.php`

| Function | Purpose |
|----------|---------|
| `book_wp_epigraph( $post_id )` | Renders epigraph block |
| `book_wp_dedication( $post_id )` | Renders dedication block |
| `book_wp_colophon( $post_id )` | Renders full colophon block (form-specific fields) |

### `inc/helpers/navigation.php`

| Function | Purpose |
|----------|---------|
| `book_wp_related_works( $post_id, $max )` | Related works: same source first, then same form, skip hidden |
| `book_wp_work_navigation()` | Prev/next navigation between works in anthology order |
| `book_wp_toc_entry( $post, $show_badge, $show_thumb )` | Single TOC list item HTML; third arg enables 80×80 thumbnail |
| `book_wp_featured_image( $post_id )` | Featured image block (size: book-wp-cover 800×400) |
| `book_wp_progress_bar_markup()` | Reading progress bar HTML element |
| `book_wp_share_row( $post_id )` | Share row HTML (Copy link, X, Facebook, WhatsApp); empty string when option is off |
| `book_wp_get_form_slug( $post_id )` | First literary_form term slug for a post, or empty string (defined in `inc/helpers/literary.php`) |
| `book_wp_permalink_form_slugs()` | All registered literary_form term slugs as a flat array (added v3.4.60) |

---

## Shortcodes

Registered in `inc/shortcodes/shortcodes.php`. Each has both a `bwp_*` canonical name and a legacy alias.

| Canonical | Alias | Output |
|-----------|-------|--------|
| `[bwp_quote]` | `[quote]` | Pull-quote block |
| `[bwp_divider]` | `[divider]` | Section divider ornament |
| `[bwp_term]` | `[term]` | Inline term with tooltip definition |
| `[bwp_verse]` | `[verse]` | Verse block (line-break preserved) |
| `[bwp_chapter]` | `[chapter]` | Chapter heading with numeral |
| `[bwp_v]` | `[v]` | Inline verse number |

The `book-wp/chapter` Gutenberg block (`inc/blocks/chapter/`) is the block-editor equivalent of `[bwp_chapter]`. Both produce identical DOM output via `book_wp_render_chapter_block()`.

---

## Gutenberg Integration

The theme is a classic PHP template theme. Block editor support is a bridge layer, not a full FSE implementation.

**What works in the block editor:**
- `theme.json` feeds the `--bwp-*` colour palette, font families, and font sizes into the Gutenberg sidebar
- `editor.css` mirrors front-end typography on the editor canvas
- `book-wp/term` inline Format API (Format toolbar button)
- `book-wp/quote`, `book-wp/divider`, `book-wp/verse`, `book-wp/chapter` blocks
- `add_theme_support('editor-styles', 'align-wide', 'wp-block-styles', 'responsive-embeds')`

**What does not apply:**
- No `templates/index.html` — FSE is not active
- PHP template hierarchy is fully in control
- Classic meta boxes (`inc/admin/meta-boxes.php`) handle all per-post metadata

---

## Internationalisation

Text domain: `book-wp`. All user-facing strings wrapped in `__()`, `_e()`, or `esc_html_e()`. No hardcoded translated strings in source files (dual translation system removed in v3.3.51).

Translation files in `languages/`:

| Locale | Files |
|--------|-------|
| Bahasa Melayu | `ms_MY.po`, `ms_MY.mo`, `book-wp-ms_MY.json` |
| Spanish | `es_ES.po`, `es_ES.mo`, `book-wp-es_ES.json` |
| French | `fr_FR.po`, `fr_FR.mo`, `book-wp-fr_FR.json` |
| Template | `book-wp.pot` |

`.json` files serve the JS translation layer (`wp_set_script_translations`).

---

## Caching

| Transient | TTL | Invalidated by |
|-----------|-----|----------------|
| `book_wp_toc_posts` | 12 hours | `save_post`, `trashed_post`, `untrashed_post` via `book_wp_clear_toc_cache()` |

The transient stores the full `get_posts(-1)` result for the TOC query in grouped and non-paginated modes. It is not used in the paginated Infinite Scroll path (which uses a live `WP_Query` per request).

---

## CI and Tooling

| Tool | Config | Purpose |
|------|--------|---------|
| phpcs | `config/.phpcs.xml.dist` | PHP coding standards (WordPress-Core ruleset) |
| eslint | `config/.eslintrc.json` | JS linting |
| stylelint | `config/.stylelintrc.json` | CSS linting |
| GitHub Actions | `.github/workflows/ci.yml` | Lint on PR |
| Lighthouse | `config/lighthouserc.json` | Performance budget (requires deployed URL to activate) |

Config files live in `config/` to keep the theme root clean.

---

## Release Checklist

Run through every section before tagging a release. Items marked **CI** are
verified automatically by the GitHub Actions pipeline. Manual items require
a browser or local environment.

### Pre-release

#### Code quality (CI)

- [ ] `composer run lint:php` — zero phpcs errors (warnings acceptable)
- [ ] `npm run lint:js` — zero eslint errors
- [ ] `npm run lint:css` — zero stylelint errors
- [ ] PHP Compatibility check passes for PHP 8.0–8.3

#### Version consistency

- [ ] `style.css` `Version:` header updated
- [ ] `package.json` `version` field matches `style.css`
- [ ] All JS file `@version` headers match (`frontend.js`, `time-theme.js`, `blocks.js`)
- [ ] `changelog.md` entry written for this version
- [ ] `readme.txt` `= X.X.X =` entry written in both Upgrade Notice and Changelog sections

#### Translation

- [ ] New or changed strings added to `languages/book-wp.pot`
- [ ] `ms_MY.po` updated if any Malay strings changed
- [ ] `es_ES.po` and `fr_FR.po` updated if any front-end strings changed
- [ ] `msgfmt` run to regenerate `.mo` files from all updated `.po` files

### Browser testing (manual)

#### Functional smoke test

- [ ] Homepage — TOC renders, all grouping modes correct
- [ ] Single post — puisi: typography, colophon, ornament
- [ ] Single post — ratapan: chapter block, verse numbers
- [ ] Single post — esei: drop cap, abstract meta
- [ ] Search — results page and no-results state
- [ ] 404 page
- [ ] Foreword template (template-foreword.php)
- [ ] Colophon template (template-colophon.php)
- [ ] Comments enabled post — comment form renders

#### Time-of-day themes

- [ ] Dawn (04:00–06:59)
- [ ] Morning (07:00–11:59)
- [ ] Afternoon (12:00–16:59)
- [ ] Dusk (17:00–20:59)
- [ ] Night (21:00–03:59)

#### Colour schemes × Time of Day (Paper/Ink matrix)

- [ ] Default scheme × all 5 time periods — ink shifts, surface unchanged
- [ ] Cream scheme × all 5 time periods — surface tinted per compound selector
- [ ] Dusk scheme × all 5 time periods — surface tinted per compound selector
- [ ] Night scheme × all 5 time periods — accent/link colours shift per compound
- [ ] Hikayat on Default — warm parchment surface, time ink shifts
- [ ] Hikayat on Cream — Cream surface wins (form yields)
- [ ] Hikayat on Night — dark surface, light ink, gold-dim accent visible
- [ ] Ratapan on Default — muted grey surface, time ink shifts
- [ ] Ratapan on Dusk — Dusk surface wins (form yields)
- [ ] Memoir on Cream — Cream surface wins (form yields)
- [ ] Novel drop cap (`--bwp-teal`) visible in all night paths
- [ ] Esei h2 headings (`--bwp-teal`) visible in all night paths
- [ ] Gold ornaments (`--bwp-gold-dim`) visible in all night paths
- [ ] Night toggle at 10 PM → close → reopen at 2 PM → toggle expired
- [ ] Night toggle at 1 AM → stays active until daytime on next calendar date
- [ ] Tab backgrounded at 3 PM → foregrounded at 7 PM → ink shifts to dusk

#### Accessibility

- [ ] Skip link appears on focus and lands on `#bwp-main`
- [ ] Tab order logical through header → nav → content → footer
- [ ] Focus ring visible on all interactive elements in all schemes
- [ ] Keyboard navigation ← → between works
- [ ] Night mode toggle keyboard-accessible

#### Responsive

- [ ] 320px — nav wraps, no overflow
- [ ] 375px — poetry at full width, no horizontal scroll
- [ ] 768px — tablet breakpoint
- [ ] 1280px — desktop, max-width constraint active

#### Print

- [ ] Nav/UI chrome hidden, typography legible
- [ ] No page breaks inside stanzas or colophon

### Performance

- [ ] Lighthouse score ≥ 90 (Performance, Accessibility)
- [ ] LCP ≤ 2.5s on mobile throttled
- [ ] CLS ≤ 0.1
- [ ] Only `times.css` loaded (not individual period files)
- [ ] Form CSS only loads on single posts with a known form

### Package

- [ ] `zip -r book-wp-vX.X.X.zip . --exclude '*.git*' --exclude 'node_modules/*' --exclude 'vendor/*' --exclude '*.zip'`
- [ ] Test zip by installing fresh on a local WordPress instance
- [ ] Tag release in git: `git tag vX.X.X && git push origin vX.X.X`

### Post-release

- [ ] `upgrading.md` Version Milestones table updated
- [ ] Any newly resolved issues marked ✅ in Known Issues section

---

This document records planned upgrades, the Booklit fork roadmap, and
migration notes for maintainers.

---

## Version Milestones

| Version | Date | Summary |
|---------|------|---------|
| 3.3.50 | 2026-02 | Full security and design audit — S1–S4 security fixes, D1–D6 design fixes, REST meta exposure hardened |
| 3.3.51 | 2026-02 | Dual translation system resolved — `strings.php` deleted, all strings migrated to WordPress gettext |
| 3.3.52 | 2026-02 | CI infrastructure added — phpcs, eslint, stylelint, GitHub Actions, Lighthouse, release checklist |
| 3.3.56 | 2026-03 | Full i18n audit — ms-MY, es-ES, fr-FR `.po`/`.mo` files; `.pot` updated |
| 3.3.57–58 | 2026-03 | Three-mechanism dark-mode CSS guard system — `bwp-night`, `bwp-scheme-night`, `bwp-time-night` guards added to Ratapan, Hikayat, Memoir form CSS |
| 3.3.59 | 2026-03 | Ratapan chapter layout — CSS Grid replaced with float-based biblical layout; chapter numeral floats left, verse text wraps around it |
| 3.4 | 2026-03-04 | Critical bugfix — 9 orphaned PHP concatenation lines in `colophon.php` causing site-wide fatal errors; version series milestone |
| 3.4.3 | 2026-03-04 | Footer copyright deduplication; Search block button translated via `render_block_core/search` filter |
| 3.4.4 | 2026-03-04 | Copyright field changed to textarea with HTML support (`wp_kses_post`); Show Copyright toggle added; Front Page Blurb HTML hint added |
| 3.4.5 | **2026-03-04** | **Compliance audit — 10 fixes: license corrected to GPL-2.0-or-later, CRLF normalised, capability changed to `edit_theme_options`, body/comment link underlines, Privacy & FAQ sections in readme.txt, duplicate ABSPATH guard removed, archive.php guard moved to top, Settings section updated, `echo count()` given int cast** |
| 3.4.6 | 2026-03-04 | Tag support — tag display on single posts, TOC by-tag grouping, tag archive template, hidden-post feature, 5-tag cap, admin multi-select widget, schema keywords |
| 3.4.7 | 2026-03-04 | Tag cap raised 3→5; tag archive header redesign (pill title, "Tagged with" label, § glyph); maintenance fixes; translation sync (12 new strings) |
| 3.4.8 | 2026-03-04 | Search results page (`search.php`) redesigned to match tag archive — same `bwp-archive-header` structure, leaf ornament, query as pill, form badge and year in result list. Orphaned `.bwp-search-page` CSS removed. |
| 3.4.9 | 2026-03-04 | Hidden-tagged posts excluded from Related Works — `$is_hidden` closure added to both candidate loops in `book_wp_related_works()`. |
| **3.4.10** | **2026-03-04** | **`navigation.php` audit — 6 fixes: stale docblock, `$used[]` consistency, prev/next nav skips hidden posts, fetch buffer scaled to `max*2`, N+1 query eliminated via batch `wp_get_object_terms`, `$filled` counter replaces `count()` in loop.** |
| 3.4.10 | 2026-03-04 | `navigation.php` integrity audit — 6 fixes: docblock, hidden-post skip in prev/next, batch `wp_get_object_terms`, N+1 query eliminated |
| 3.4.11 | 2026-03-04 | Gutenberg-native shortcode support — `book-wp/term` inline Format API, `book-wp/quote`, `book-wp/divider`, `book-wp/verse` blocks; shortcode aliases; smart quote normalisation filter; span hydration filter |
| 3.4.12 | 2026-03-04 | Comments section redesigned — full custom markup for comment list and comment form (~230 lines CSS) |
| 3.4.13 | 2026-03-04 | All 10 literary form headers standardised — two-group alignment system, Cinzel italic abolished, per-form size scale, analytics bar alignment corrected per group |
| 3.4.15 | 2026-03-04 | Drop cap fonts — IM Fell English SC (hikayat), Libre Caslon Display (nukilan)|
| 3.4.18 | 2026-03-04 | Karmina and Nukilan layout fixes — page wrap widened, Nukilan title italic removed|
| 3.4.19 | 2026-03-05 | Hikayat chrome centred (title, analytics, ornament, colophon); "Cited from" citation label opt-in via meta box checkbox |
| 3.4.21 | 2026-03-05 | Author\'s Note — final position: full-width banner at page top between analytics bar and content|
| 3.4.22 | 2026-03-05 | Compact analytics mode for Puisi, Syair, Karmina — tighter gap and smaller font-size, all six items on one line |
| 3.4.23 | 2026-03-05 | en-US standardisation — template files renamed, CSS classes renamed, bilingual option strings replaced with en-US primaries, 61 ms-MY `.po` entries added |
| 3.4.27 | 2026-03-05 | Archive layout + TOC flat-list — Masonry, Infinite Scroll, featured thumbnails, four JS fixes|
| 3.4.28 | 2026-03-05 | Documentation audit — readme.txt, changelog.md and upgrading.md congruence fixes |
| 3.4.35 | 2026-03-05 | Erotika tag decorative background — per-tag CSS pipeline, two-layer batik frame, scheme-aware opacity|
| 3.4.36 | 2026-03-05 | Erotika column background full-bleed — meets side panels flush at viewport edges |
| 3.4.37 | 2026-03-05 | Erotika column background corrected — `left/right: -2em` instead of full-bleed calc |
| 3.4.38 | 2026-03-05 | Per-form erotika geometry overrides; readme.txt features/install/FAQ review |
| 3.4.39 | 2026-03-06 | Logo moved to footer; readme.txt features/install/FAQ review (3.4.39 sync) |
| 3.4.40 | 2026-03-06 | Root folder cleanup — config/, release-checklist merged, template-parts removed, package.json version fixed |
| 3.4.41 | 2026-03-06 | Bugfix: \\u2026 Unicode escape rendering as literal text in comment placeholder and admin tag selector |
| 3.4.42 | 2026-03-06 | Comment textarea placeholder replaced with poetic Malay phrase; ms_MY.po and .mo updated |
| 3.4.43 | 2026-03-07 | Post format support (aside, image) and customize-selective-refresh-widgets declared; KIV roadmap entries for RTL and block patterns added to upgrading.md |
| 3.4.44 | 2026-03-07 | Core Web Vitals — fetchpriority on featured image, preconnect hints for Google Fonts, translateY removed from bwp-in animation, aspect-ratio on .bwp-featured-image |
| 3.4.45 | 2026-03-07 | Border radius — --bwp-radius token set to 6px; featured image pinned at 6px; form badges changed to pill shape (2em) |
| 3.4.46 | 2026-03-07 | Social profile icons in footer — new inc/helpers/social.php, Social settings tab, 10 platforms, circular icon buttons |
| 3.4.47 | 2026-03-07 | Social icons collapse toggle — max 6 visible, +N button reveals overflow, degrades without JS |
| 3.4.48 | 2026-03-07 | Social platforms expanded to 17 (added Goodreads, WorldCat, LOC, NLB, PNM, ORCID, ISNI); rel=nofollow added to all social links |
| 3.4.49 | 2026-03-07 | nofollow removed from social links — owned identity endpoints pass authority correctly |
| 3.4.50 | 2026-03-07 | CWV batch 1 — @import Google Fonts removed from frontend.css (LCP); aspect-ratio corrected to 2/1 to match book-wp-cover crop (CLS) |
| 3.4.51 | 2026-03-07 | CWV batch 2 — body animation moved to .bwp-page-wrap (LCP); footer logo height fix (CLS); scroll restore no longer uses setTimeout (CLS); word count cached in post meta (TTFB); decoding=async on TOC thumbnails (INP) |
| 3.4.52 | 2026-03-08 | WCAG 2.2 batch 1 — nav sub-menu :focus-within (SC 2.1.1); 404 h2→h1 (SC 1.3.1); pagination prev/next accessible names (SC 2.4.4) |
| 3.4.53 | 2026-03-08 | WCAG 2.2 batch 2 — broken role=tab removed from admin (SC 4.1.2); social toggle 24px min target (SC 2.5.8); footer padding clears night toggle (SC 2.4.11); required note asterisk readable (SC 3.3.2); tag-select combobox ARIA (SC 4.1.2); prose-context link underlines (SC 1.4.1) |
| 3.4.54 | 2026-03-08 | Hidden tag hardening — ID-based storage migration; query coverage expanded (date, author, feed, non-hidden tag archive, widget, REST); sitemap exclusion (wp_sitemaps_posts_query_args); tag cloud suppression (widget_tag_cloud_args); the_tags output filter |
| 3.4.55 | 2026-03-08 | Bug fix — social profile fields not saving: social tab fields were registered under the wrong key in tab_fields ('advanced' instead of 'social'), causing the save handler to redirect without writing any options |
| 3.4.56 | 2026-03-08 | Social tab redesign — icon-prefixed URL inputs with per-platform brand tints; platforms grouped into sections (Social Media, Libraries, Developer, Identifiers, Syndication); markup converted to design system classes (.bwp-icon-input, .bwp-social-group-label) |
| 3.4.57 | 2026-03-08 | Social meta tags — OpenGraph (og:type/title/description/url/image/locale/article:*) and Twitter Card (summary_large_image) output on single posts; SEO plugin guard covers RankMath, Yoast, AIOSEO, SEOPress, The SEO Framework |
| 3.4.58 | 2026-03-08 | Share row — Copy link (Clipboard API), X, Facebook, WhatsApp; no third-party SDKs; gated toggle in Reading settings (default off) |
| 3.4.59 | 2026-03-08 | JSON Feed v1.1 — /feed/json endpoint; per-item: content_html, summary, image, tags (hidden excluded), _book_wp extension (literary_form, word_count, reading_time_min); autodiscovery <link> on front page; toggle in Advanced settings (default on) |
| 3.4.60 | 2026-03-08 | Literary Form Permalinks — opt-in /{form-slug}/{post-slug}/ URL structure; rewrite rules built from live term slugs; auto-flush on option toggle and theme switch; deferred flush on term create/edit/delete; toggle in Advanced settings (default off) |
| 3.4.61 | 2026-03-08 | Mobile-first audit — nav tap-target padding raised to meet WCAG 2.2 SC 2.5.8 24 px minimum |
| 3.4.64–3.4.67 | 2026-03-08 | Literary Form Permalink redirects — bare-slug, legacy post-slug, page/post disambiguation, and edge-case fixes |
| 3.4.68–3.4.69 | 2026-03-08 | Back-to-top button + keyboard stanza navigation (↑/↓); Infinite Scroll conflict guard |
| 3.4.70–3.4.73 | 2026-03-09 | Share row overhaul — icon-only layout; Threads, Pinterest, Mastodon, Email added; SVG icons traced via potrace; copy-button tooltip |
| 3.4.74 | 2026-03-09 | Fix: empty layout gap when Prev/Next navigation is disabled |
| 3.4.75 | 2026-03-09 | MUST FIX batch — TOC N+1 query eliminated (batched `wp_get_object_terms`); analytics dedup via static cache; 6-event cache invalidation matrix; dark-mode first-visit flash fixed (sync script in `<head>`); `wp_redirect` output escaping hardened |
| 3.4.76 | 2026-03-09 | Mastodon share row icon replaced with potrace-traced SVG |
| 3.4.77 | 2026-03-09 | Mastodon added to Social Profiles panel (social.php + settings.php) |
| 3.4.78 | 2026-03-09 | Hashtag support in share row (X, Threads, Mastodon) via `book_wp_share_hashtags()`; hidden tags excluded; capped at 3; poetry alignment option wired to body class |
| 3.4.79 | 2026-03-09 | ORCID icon replaced with potrace-traced four-path logo |
| 3.4.80–3.4.81 | 2026-03-09 | Rewrite rule inspector panel in Advanced tab; fixed wrong rules source (`$wp_rewrite->rules` → `get_option('rewrite_rules')`) and broken arrow entity |
| 3.4.82 | 2026-03-11 | Permalink contingencies — deleted-form-term 7-day 301 rescue; `preg_quote` delimiter fix; persistent object cache belt-and-suspenders; share hashtag static cache |
| 3.4.83 | 2026-03-11 | ORCID icon replaced with canonical official path data (viewBox 256×256, disc + white iD letterforms) |
| 3.4.84 | 2026-03-11 | WorldCat icon replaced with potrace-traced arc silhouette (5 clean paths, viewBox 747×724) |
| 3.4.90 | 2026-03-11 | Fix ORCID and WorldCat icons — both were rendering incorrectly due to extraction/inversion errors; rebuilt from official sources |
| 3.4.90 | 2026-03-11 | Fix double H1 on homepage — author name in site header now always renders as `<p>`, leaving each template's own title as the sole H1 |
| 3.4.90 | 2026-03-11 | Three bug fixes: hidden tag slugs excluded from body class; Schema.org inLanguage reads from `get_locale()`; TOC transient evicted from persistent object cache on invalidation |
| **3.4.91** | **2026-03-12** | **Paper/Ink audit — complete scheme/time conflict resolution across all 10 literary forms, 3 colour schemes, 5 time periods, and 5 night activation paths. Hikayat/Ratapan/Memoir refactored to surface-only day palettes. Missing night tokens added (--bwp-teal, --bwp-gold-dim, --bwp-gold-light) to all paths. Time-of-day live updates (visibilitychange + interval). Night toggle auto-expiry via bwp_night_date.** |
| 3.4.92 | 2026-03-12 | Hidden-tagged posts excluded from RankMath HTML sitemap via `rank_math/sitemap/html_sitemap/post_args` filter |
| 3.4.97–3.4.99 | 2026-05 | Reading Statistics dashboard widget; label-truncation fix; hidden-tagged posts excluded from dashboard stats and transient invalidation on `book_wp_hidden_tags` update |
| **3.5.0** | **2026-05-12** | **Documentation streamline + i18n cleanup — `readme.md`/`upgrading.md`/`changelog.md` moved into `docs/`; new `docs/ssot.md` (Single Source of Truth); `phpunit.xml` moved to `config/`; version unified at 3.5.0 (resolves drift between 3.4.42 / 3.4.92 / 3.4.99); `theme.json` FSE pointer updated. **Bug fix:** 14 Malay strings in `comments.php` used as English `msgid` (none were in `.pot`/`.po`) replaced with English msgids; Malay text rehomed as `ms_MY.po` translations. **Translation files regenerated from source** — `book-wp.pot` was at 3.3.56, now covers all 361 source msgids; 62 added, 5 stale removed; existing translations preserved. `.mo` files rebuilt from regenerated `.po`s (ms_MY: 315 strings, es_ES: 299, fr_FR: 299). **Mobile polish:** `.bwp-comment-submit` raised to 44 px tap target (WCAG 2.5.5 AAA); defensive 24 px floor on `.bwp-social__link/__toggle`; comment-form breakpoint unified at 36em; ratapan chapter numeral shrinks to 2.6em on phones. No deploy step required.** |
| **3.5.1** | **2026-05-13** | **Maintenance hardening — Back to Top JS scoped correctly; Custom CSS moved to `wp_add_inline_style()` after active theme styles; `book_wp_remote_fonts` toggle added for no-remote-fonts mode; analytics chapter extraction uses `parse_blocks()` with test fallback; JSON Feed reading_time_min uses numeric helper; ESLint/Stylelint/PHPCS/PHPUnit scripts and GitHub Actions CI added; translation files updated for new Typography strings.** |

### 3.4.37 — Upgrade Notes

Drop-in upgrade. CSS-only fix to `assets/css/tags/erotika.css`.

---

### 3.4.36 — Upgrade Notes

Drop-in upgrade. CSS-only change to `assets/css/tags/erotika.css`.

---

### 3.4.35 — Upgrade Notes

Drop-in upgrade. No database changes, no option key changes.

**New directories** — `assets/css/tags/` and `assets/img/` are created.

**`bwp-tag-{slug}` body classes** — `book_wp_body_classes()` now adds a
class for every tag on a single post. No existing class names are changed.

**Erotika background** — only appears on single posts with the exact slug
`erotika` assigned as a tag. No other posts are affected.

---


### 3.4.27 — Upgrade Notes

Drop-in upgrade. No option key changes, no breaking changes.

**New image size `book-wp-thumb` (80×80 hard crop)** — WordPress will generate
this crop automatically for images uploaded after upgrade. Images uploaded
before this version will not have the crop until thumbnails are regenerated.

Run via WP-CLI: `wp media regenerate --yes`
Or use the Regenerate Thumbnails plugin from the WordPress repository.

Without regeneration, WordPress falls back to the full-size image scaled in
the browser — images will display but may be oversized until regenerated.

### Suggested Fork Checklist

```
[ ] Create new GitHub repo: menj/booklit
[ ] Rename theme folder: book-wp/ → booklit/
[ ] Update style.css header (Theme Name, Theme URI, Text Domain)
[ ] Global rename: book_wp_ → booklit_ in all .php files
[ ] Global rename: BOOK_WP_ → BOOKLIT_ in all .php files
[ ] Global rename: book-wp- → booklit- in enqueue handles
[x] Fix stale handle: book-wp-sastera → book-wp-literary (done v3.3.52); → booklit-literary pending Booklit rename
[ ] Rename JS global: bookWP → booklitCfg in enqueue.php + .js files
[ ] Rename text domain: 'book-wp' → 'booklit' in all .php files
[ ] Rename .pot file and update its headers
[ ] Recompile ms_MY.mo
[ ] Update footer.php credit link
[ ] Write database migration routine (wp_options + wp_postmeta)
[ ] Decide on bwp- CSS prefix: keep for now or rename to bl-
[ ] Set version to 1.0.0 in style.css
[ ] Tag release on GitHub
```

---

## Future Upgrade Plans

Items are grouped by urgency. Each item has a number for cross-referencing in
commit messages and pull requests (e.g. `fix: settings map dedup (#6)`).

---

### Now — concrete, unblocked, address before next milestone

**#6 — Settings registration map duplicated**
`inc/admin/settings.php` defines the field-to-sanitiser mapping in two
separate arrays. They must be kept in sync manually; they have already drifted
once (causing the v3.4.55 social-tab save regression). Consolidate into a
single `$settings` map that is iterated by both the save handler and the
registration call. No user-visible change; maintenance-only.

**#7 — `archive.php` and `tag.php` are 96% identical**
Both files render the same card layout with the same pagination logic. The
only difference is the archive header copy. Extract the shared body into
`inc/helpers/archive.php` (a `book_wp_archive_loop()` function) and reduce
each template to a thin wrapper that sets the header and calls the helper.
Reduces maintenance surface: CSS changes, layout logic, and pagination fixes
need to be applied only once.

**#8 — `single-puisi-generic.php` is dead code**
`templates/single-puisi-generic.php` is registered as a page template via the
`theme_page_templates` filter but is never assigned to any post in normal use
and duplicates `single.php` almost completely. Audit whether it serves a
genuine use case. If not, remove the file and the filter registration. If it
does, document the specific scenario and add a comment to both templates
explaining the distinction.

**#9 — Sidebar widget area is registered but never rendered**
`inc/setup/sidebars.php` calls `register_sidebar()`, which creates the
Appearance → Widgets panel entry. `sidebar.php` and `get_sidebar()` are
never called from any template. Either render it (add `get_sidebar()` to
relevant templates behind an option toggle) or remove the registration to
avoid a misleading admin panel entry.

**#10 — Schema.org types too generic**
`book_wp_structured_data()` in `inc/hooks/actions.php` outputs
`@type: Article` for every literary form. The spec supports richer types:
`Poem` for poetry forms (puisi, syair, karmina, ratapan), `Book` or
`Chapter` for hikayat and novel, `ShortStory` for cerpen, `Essay` for esei.
Map each `literary_form` slug to its correct Schema.org type in a lookup
array and output accordingly. No template changes; structured data only.

**#11 — POT generation not automated**
The release checklist requires `languages/book-wp.pot` to be updated before
tagging, but there is no `make-pot` step in `package.json` or the CI
workflow. Add: `"i18n": "wp i18n make-pot . languages/book-wp.pot
--exclude=vendor,node_modules"` to `package.json` scripts, and add a
`make-pot` step to `.github/workflows/ci.yml` that fails the build if the
`.pot` file is stale (compare against a freshly generated one). Prevents
translatable strings going unnoticed between releases.

**#12 — JS `@version` comments are stale and outside the five-file rule**
`frontend.js` says `@version 3.0.0`. `time-theme.js` says `@version 3.3.16`.
The five-file version consistency rule (`style.css`, `readme.txt`,
`readme.md`, `upgrading.md`, `changelog.md`) does not cover JS file headers.
Two options: (a) extend the rule to include `frontend.js`, `time-theme.js`,
and `blocks.js`, or (b) remove the `@version` annotations from the JS files
entirely since `BOOK_WP_VERSION` is already appended as a cache-buster query
string at enqueue time. Option (b) is simpler and removes the maintenance
burden with no loss of functionality.

---

### Next — worth doing, no hard deadline

**#14 — Block editor support is incomplete**
The analytics bar, epigraph, dedication, colophon, and related-works helpers
are all server-rendered PHP and not insertable in the editor. Registering each
as a `register_block_type` with a `render_callback` (Phase H1 of the Hybrid
Block Theme path below) makes the literary apparatus visible and editable in
the block editor without changing any front-end output. This is the most
useful block editor improvement available without touching templates.

**#15 — Colophon helper early-exit is misleading**
`book_wp_colophon()` in `inc/helpers/colophon.php` returns early if no
colophon fields are populated, but the early-exit condition is not clearly
documented and the surrounding code in `single.php` does not signal to the
reader that the function may produce no output. Add a doc comment to the
helper explaining the early-exit logic, and consider returning an empty
string explicitly (rather than `return;`) so callers can test the return
value if needed.

**#16 — Self-hosted font binaries**
Resolved in 3.5.1 for the privacy-critical case: `book_wp_remote_fonts`
lets a site disable all Google Fonts requests from Typography settings. The
remaining optional enhancement is exact self-hosted typography: download the
required `.woff2` files, place them in `assets/fonts/`, and update the existing
`assets/css/fonts/{font}.css` files plus UI/display font declarations to point
at local assets.

---

### Deferred — needs a strategic decision before implementation

**#17 — FSE parallel branch**
Full Site Editing is a major-version breaking change (see the
[FSE Migration Plan](#fse-migration-plan-full-site-editing) section). It
requires rebuilding `inc/admin/meta-boxes.php` (~800 lines) as a Gutenberg
sidebar plugin, converting all PHP templates to HTML block templates, and
retiring the classic widget area. The groundwork (registering helpers as
`render_callback` blocks — item #14) can be done now, but the FSE switchover
itself must not be attempted as a point release. Decision: designate a
milestone (Booklit 2.0 or similar) and scope the work as a dedicated branch.

**#18 — Block pattern library**
Sasterawan has zero registered block patterns. A new post opens to a blank
editor; the user must know the correct block structure for literary apparatus
from memory. Candidate patterns: epigraph layout, dedication spread, chapter
opening (chapter block + drop cap paragraph), syair four-stanza structure,
karmina couplet pair, esei heading+body+divider scaffold. Implementation is
straightforward (`register_block_pattern()` in a new `inc/setup/patterns.php`)
but requires design decisions about which patterns are canonical and how they
interact with per-form meta boxes. Defer until block editor integration
(#14) is complete.

**#19 — Literary presentation enhancements**
Three pending presentation improvements, each requiring design work before
coding begins: (a) per-form archive template (`taxonomy-literary_form.php`)
with form-appropriate column data (line counts for puisi, source attribution
for hikayat); (b) RTL stylesheet (`rtl.css`) for Arabic-script Jawi content
alongside Amiri font — requires a full directional audit of all ten form
stylesheets; (c) child theme documentation covering the `bwp-` CSS prefix,
function prefix conventions, and recommended override patterns. None are
blocking; all depend on a clearer picture of the intended readership and
publishing scope.

## Hybrid Block Theme Path

### What This Is

A hybrid block theme keeps the PHP template hierarchy fully in control while
progressively converting PHP render helpers into **server-side registered
Gutenberg blocks** (`register_block_type` + `render_callback`). The outer
page structure — `single.php`, `index.php`, `archive.php`, `header.php`,
`footer.php` — remains PHP. `templates/index.html` is deliberately never
added, so FSE is never triggered and the WordPress PHP template resolver
continues to run.

This is the natural next step for Sasterawan. The literary apparatus (analytics
bar, colophon, epigraph, related works) is inherently server-side PHP — it
cannot be expressed as static saved block HTML. A hybrid approach preserves
that constraint while making each piece of apparatus insertable in the block
editor.

### Why Not Go Straight to FSE

Full Site Editing would require:

- Rebuilding `inc/admin/meta-boxes.php` (~800 lines) as a Gutenberg sidebar
  plugin using `@wordpress/plugins`, `@wordpress/edit-post`, and
  `@wordpress/data`. This is the largest single item in any migration.
- Replacing the classic widget areas (`sidebars.php`) with block patterns.
- Converting the `templates/` PHP page templates to HTML block templates.
- Giving up the PHP template hierarchy as the primary routing mechanism.

None of these are required for the hybrid path. FSE is a future major version
decision. The hybrid path is an incremental one.

### What Changes vs What Stays

| Component | Classic (now) | Hybrid target | FSE (future) |
|---|---|---|---|
| PHP templates | ✅ in control | ✅ in control | ❌ replaced by HTML |
| Template routing | PHP hierarchy | PHP hierarchy | Block resolver |
| Meta boxes | PHP classic | PHP classic | Gutenberg panels |
| Admin settings page | PHP tabs | PHP tabs | PHP tabs |
| `theme.json` | Editor tokens only | Editor tokens + block styles | Full styles |
| Helpers as blocks | ❌ | ✅ (render_callback) | ✅ (render_callback) |
| Block patterns | ❌ | ✅ | ✅ |
| Widget areas | Classic | Classic | Block-replaced |

### Migration Phases

#### Phase H1 — Register helpers as server-side blocks

Convert each PHP render helper into a `register_block_type` call with a
`render_callback`. The PHP logic is unchanged — only the registration changes.

Priority order (most useful in the editor first):

| Helper | Proposed block name | File |
|---|---|---|
| `book_wp_analytics_bar()` | `book-wp/analytics-bar` | `inc/helpers/analytics.php` |
| `book_wp_epigraph()` | `book-wp/epigraph` | `inc/helpers/colophon.php` |
| `book_wp_dedication()` | `book-wp/dedication` | `inc/helpers/colophon.php` |
| `book_wp_colophon()` | `book-wp/colophon` | `inc/helpers/colophon.php` |
| `book_wp_related_works()` | `book-wp/related-works` | `inc/helpers/navigation.php` |
| `book_wp_work_navigation()` | `book-wp/work-navigation` | `inc/helpers/navigation.php` |
| `book_wp_featured_image()` | *(use core/post-featured-image)* | — |

Each block is server-rendered — `save` returns `null` in the JS registration,
and `render_callback` returns the same HTML the PHP helper currently produces.
The editor shows a placeholder or a live preview; the front end is identical
to the current output.

```php
// Pattern for each helper conversion:
register_block_type( 'book-wp/analytics-bar', array(
    'render_callback' => 'book_wp_analytics_bar',
    'attributes'      => array(), // no attributes needed — reads from global $post
) );
```

No new `block.json` files are strictly required for the simplest render-only
blocks. A `block.json` can be added later to expose inspector controls.

#### Phase H2 — Block patterns for literary forms

Register starting-point block patterns for each literary form via
`register_block_pattern()` in a new `inc/setup/patterns.php`:

| Pattern name | Contents |
|---|---|
| `book-wp/syair-stanza` | Four `core/paragraph` blocks (one per bait) |
| `book-wp/karmina-pair` | Two `core/paragraph` blocks (sampiran + isi) |
| `book-wp/hikayat-opening` | `book-wp/epigraph` + `book-wp/chapter` block |
| `book-wp/ratapan-chapter` | `book-wp/chapter` block with verse_start attribute |
| `book-wp/esei-structure` | Heading + three paragraphs + `book-wp/divider` |
| `book-wp/nukilan-block` | `bwp_quote` block + source attribution |

Patterns are pure block HTML — no PHP logic required. They work in both the
classic and block editor and are independent of the template structure.

#### Phase H3 — Expand `theme.json` block styles

Add `styles.blocks` entries to `theme.json` for the custom block types
introduced in Phase H1. This binds the design tokens to block output directly
in the editor:

```json
"styles": {
    "blocks": {
        "book-wp/analytics-bar": {
            "spacing": { "margin": { "bottom": "1.5rem" } }
        },
        "book-wp/colophon": {
            "border": { "top": { "width": "1px", "style": "solid" } }
        }
    }
}
```

#### Phase H4 — Editor preview for `book-wp/divider`

The existing `book-wp/divider` block has no visual preview in the editor —
it renders only as a placeholder icon. Add an `edit` function in `blocks.js`
that renders the ornament SVG directly in the editor canvas using
`wp.element.createElement`. The `save` function remains `null` (server-side
render).

### What This Does Not Change

- `header.php`, `footer.php`, `single.php`, `index.php` — unchanged.
- `inc/admin/meta-boxes.php` — unchanged.
- `inc/admin/settings.php` — unchanged.
- `inc/setup/sidebars.php` — unchanged.
- The hidden tag system, taxonomy, structured data — unchanged.
- CSS load chain — unchanged.
- `templates/index.html` — never added in this path.

---

## FSE Migration Plan (Full Site Editing)

### Current State

Sasterawan is a **classic PHP template theme**. FSE is not active. The block
editor works but previously had no knowledge of the theme's design tokens.

**v3.3.20 partial bridge (already done):**
- `theme.json` added — feeds `--bwp-*` colour palette, font families, and
  font sizes into the Gutenberg block editor. This is valid in classic themes
  since WP 5.8 and does **not** enable FSE.
- `editor-styles`, `align-wide`, `wp-block-styles`, `responsive-embeds`
  support declarations added to `theme-support.php`.
- `assets/css/editor.css` added — mirrors front-end typography in the
  Gutenberg editor canvas.

### Why FSE Cannot Be Enabled Now

WordPress determines a theme is a block/FSE theme by the presence of
`templates/index.html` — an HTML block template file. The moment that file
exists, WordPress switches the entire template resolution pipeline from PHP
to HTML block templates and **abandons the PHP template hierarchy entirely**.

Sasterawan currently has PHP custom page templates in `templates/`:

```
templates/
  single-puisi-generic.php   ← custom single template for generic puisi
  template-kolofon.php       ← Kolofon page template
  template-mukadimah.php     ← Mukadimah (Foreword) page template
```

These are registered via the `theme_page_templates` filter in `filters.php`
and are selectable in the WordPress page editor. They cannot coexist with
FSE block templates in the same `templates/` directory.

Additionally, the per-post literary metadata system (epigraph, dedication,
colophon, form-specific fields) is implemented as classic meta boxes
(`inc/admin/meta-boxes.php`). In an FSE context this UI needs to be rebuilt
as either a block editor sidebar panel (using `@wordpress/plugins` and
`@wordpress/edit-post`) or via the Block Bindings API (WP 6.5+). This is
significant JavaScript work.

### FSE Migration Path

The migration is a **major version** (Booklit 2.0 or equivalent). It must
not be attempted as a point release — it is a breaking change.

#### Phase 1 — Already done (v3.3.20)

- [x] `theme.json` with colour palette, font families, font sizes, spacing
- [x] `editor-styles` support + `editor.css`
- [x] `align-wide`, `wp-block-styles`, `responsive-embeds`

#### Phase 2 — Pre-migration groundwork (can be done incrementally)

- [ ] Audit every PHP template for logic that cannot be expressed in blocks.
      `single.php` calls `book_wp_analytics_bar()`, `book_wp_epigraph()`,
      `book_wp_colophon()` etc. — these need to become custom blocks or
      block-based PHP render callbacks (`register_block_type` with
      `render_callback`).
- [ ] Register each helper function as a server-rendered block. This is the
      bridge: classic PHP logic, block editor insertable. Blocks can be
      inserted into block templates once the FSE migration happens.
- [ ] Expand `editor.css` to style block editor representations of the
      custom blocks so the editor canvas matches the front end.
- [ ] Move the three PHP custom templates out of `templates/` into a
      transitional location (e.g. `php-templates/`) and rebuild them as
      block templates when ready.

#### Phase 3 — FSE switchover (major version, breaking)

1.  **Add `templates/index.html`** — this is the trigger. Once this file
    exists, WordPress activates FSE mode. Do not add this file until all
    other steps are complete.

2.  **Convert PHP templates to HTML block templates:**

    | Current (PHP) | FSE equivalent (HTML) |
    |---|---|
    | `header.php` | `parts/header.html` |
    | `footer.php` | `parts/footer.html` |
    | `single.php` | `templates/single.html` |
    | `index.php` | `templates/index.html` |
    | `archive.php` | `templates/archive.html` |
    | `page.php` | `templates/page.html` |
    | `search.php` | `templates/search.html` |
    | `404.php` | `templates/404.html` |
    | `templates/single-puisi-generic.php` | `templates/single-post-puisi.html` |
    | `templates/template-kolofon.php` | `templates/page-kolofon.html` |
    | `templates/template-mukadimah.php` | `templates/page-mukadimah.html` |

    Each HTML template uses block markup and calls the custom blocks
    registered in Phase 2 via `<!-- wp:book-wp/analytics-bar /-->` etc.

3.  **Rebuild the settings panel.** The classic `inc/admin/settings.php`
    options page (6 tabs, colour scheme, font, ornament, etc.) may need
    a complementary Global Styles integration so the site editor can
    surface theme options. Alternatively keep the classic options page —
    it does not conflict with FSE.

4.  **Rebuild meta boxes as block editor sidebar panels.**
    `inc/admin/meta-boxes.php` (32K) needs to become a Gutenberg plugin
    using `@wordpress/plugins` + `@wordpress/edit-post` + `@wordpress/data`.
    This is the largest single item in the FSE migration.

5.  **Remove `inc/setup/sidebars.php`** — classic widget areas have no
    place in an FSE theme. Replace with block patterns.

6.  **Remove PHP template files** from root (`header.php`, `footer.php`,
    `single.php`, etc.) and from `templates/` (the PHP files). WordPress
    will ignore them once `templates/index.html` exists, but they should
    be removed to avoid confusion.

7.  **Retire `inc/hooks/filters.php` template registration** — the
    `theme_page_templates` filter that registers the three custom PHP
    templates is no longer needed.

### What theme.json Will Look Like at FSE Time

At the FSE switchover, `theme.json` gets extended with:

```json
{
    "settings": { ... },
    "styles": {
        "blocks": {
            "core/paragraph": {
                "typography": {
                    "fontFamily": "var(--wp--preset--font-family--bwp-body)",
                    "fontSize":   "var(--wp--preset--font-size--normal)",
                    "lineHeight": "1.9"
                }
            }
        }
    },
    "templateParts": [
        { "name": "header", "title": "Header", "area": "header" },
        { "name": "footer", "title": "Footer", "area": "footer" }
    ],
    "customTemplates": [
        { "name": "single-post-puisi",   "title": "Single — Puisi (Generic)",  "postTypes": ["post"] },
        { "name": "page-kolofon",        "title": "Kolofon (Colophon Page)",   "postTypes": ["page"] },
        { "name": "page-mukadimah",      "title": "Mukadimah (Foreword Page)", "postTypes": ["page"] }
    ]
}
```

### `--bwp-*` Custom Properties vs `theme.json` Presets

WordPress generates its own CSS variables from `theme.json` presets using
the `--wp--preset--*` namespace:

```
--wp--preset--color--bwp-accent    ← from settings.color.palette[slug=bwp-accent]
--wp--preset--font-family--bwp-ui  ← from settings.typography.fontFamilies[slug=bwp-ui]
```

The `--bwp-*` custom properties in `frontend.css` and the `--wp--preset--*`
variables from `theme.json` currently coexist independently. At FSE time,
the block styles in `theme.json` `styles.blocks` should reference the
`--wp--preset--*` names so the Site Editor can override them via Global
Styles. The `--bwp-*` tokens in `frontend.css` should be updated to alias
the preset variables:

```css
/* After FSE migration — frontend.css */
:root {
    --bwp-accent: var(--wp--preset--color--bwp-accent);
    --bwp-font:   var(--wp--preset--font-family--bwp-body);
    /* ... */
}
```

This keeps the existing `--bwp-*` API intact for any external CSS that
references it, while letting the Site Editor control the actual values.

---

## Known Issues — Pending Fixes

Issues identified in security and design audit (v3.3.48). None are blocking
but all should be addressed before the Booklit 1.0 release.

---

### 🔴 #7 — `archive.php` / `tag.php` code duplication (Pending)

**Files:** `archive.php`, `tag.php`
**Severity:** Medium — maintenance debt. Any layout or pagination fix must be applied to both files. They are already 96% identical and will continue to diverge.

Extract shared loop body into `inc/helpers/archive.php`. See item #7 in Future Upgrade Plans.

---

### 🟡 #8 — `single-puisi-generic.php` is dead code (Pending)

**File:** `templates/single-puisi-generic.php`
**Severity:** Low — no runtime impact, but the file appears in the Appearance → Page Attributes dropdown, which is confusing.

Audit use case. Remove or document. See item #8 in Future Upgrade Plans.

---

### 🟡 #9 — Sidebar widget area orphaned (Pending)

**Files:** `inc/setup/sidebars.php`, all templates
**Severity:** Low — admin UI confusion. The Appearance → Widgets panel shows a registered widget area that is never rendered.

Either render `get_sidebar()` behind an option toggle or remove the `register_sidebar()` call. See item #9 in Future Upgrade Plans.

---

### 🟡 #10 — Schema.org output too generic (Pending)

**File:** `inc/hooks/actions.php` — `book_wp_structured_data()`
**Severity:** Medium — SEO and structured data quality. All posts output `@type: Article` regardless of literary form.

Map each `literary_form` slug to the correct Schema.org type (`Poem`, `ShortStory`, `Essay`, `Book`, `Chapter`). See item #10 in Future Upgrade Plans.

---

### 🟡 #11 — POT generation not automated (Pending)

**Files:** `package.json`, `.github/workflows/ci.yml`
**Severity:** Low — translatable strings can go unnoticed between releases.

Add `wp i18n make-pot` to `package.json` scripts and to CI. See item #11 in Future Upgrade Plans.

---

### 🟡 #12 — JS `@version` comments stale (Pending)

**Files:** `assets/js/frontend.js`, `assets/js/time-theme.js`
**Severity:** Low — cosmetic, no runtime impact.

`frontend.js` says `@version 3.0.0`. `time-theme.js` says `@version 3.3.16`. Neither is in the five-file version consistency rule. Either extend the rule to cover these files or remove the annotations (preferred — version is already appended as a query string by `wp_enqueue_style`). See item #12 in Future Upgrade Plans.

---

### ✅ Night Scheme — Missing Tokens (Resolved in v3.3.49)

**Files:** `assets/css/schemes/night.css`
**Severity:** High — visually broken in night mode right now.

`night.css` does not define `--bwp-stone` or `--bwp-text-mid`. Both fall
back to the `:root` defaults (light grey and mid-grey), which are near-invisible
on the dark background.

- `--bwp-stone` — used for blockquote borders and `<hr>` horizontal rules
- `--bwp-text-mid` — used for body content text colour

Additionally, `--bwp-border-faint` is missing from `night.css` (it is defined
in `cream.css` and `dusk.css` but was never added to `night.css`).

**Fix:** Add dark-appropriate values to `night.css`:

```css
--bwp-stone:        #4a4a50;   /* was invisible light grey; needs dark-scheme value */
--bwp-text-mid:     #c0bdb5;   /* was #333333; needs light value on dark bg */
--bwp-border-faint: #2e2e35;   /* missing entirely */
```

Audit all three scheme files against `frontend.css` token usage after fixing
to ensure no further gaps. The non-colour tokens (`--bwp-shadow`, `--bwp-radius`,
`--bwp-font-*`, `--bwp-lh`, `--bwp-ui-border`) are intentionally not overridden
in scheme files — they cascade correctly from `:root` in `frontend.css`. Only
colour and opacity tokens need scheme-level overrides.

---

### ✅ `get_search_query()` Not Explicitly Escaped (Resolved in v3.3.49)

**File:** `searchform.php` line 3
**Severity:** Low (modern WordPress escapes internally, but explicit is correct).

```php
// Current — implicit escaping only:
value="<?php echo get_search_query(); ?>"

// Fix — explicit:
value="<?php echo esc_attr( get_search_query() ); ?>"
```

---

### ✅ Custom CSS Sanitised and Queued as Inline Style (Updated in v3.5.1)

**Files:** `inc/admin/settings.php`, `inc/setup/enqueue.php`
**Severity:** Low (admin-only field).

Custom CSS is saved with `wp_strip_all_tags()` so HTML cannot be stored in the
option. As of 3.5.1, front-end output is no longer echoed from an admin-only
settings include. `inc/setup/enqueue.php` registers a synthetic
`book-wp-custom` style handle and attaches the saved CSS with
`wp_add_inline_style()` after the active theme stylesheet dependencies.

Current registration:

```php
'book_wp_custom_css' => 'wp_strip_all_tags',
```

---

### ✅ ABSPATH Guards Missing from 14 Template Files (Resolved in v3.3.49)

**Severity:** Low (most hosts block direct PHP access; defence-in-depth best practice).

The following files have no `defined('ABSPATH') || exit;` guard at the top.
WordPress itself never loads these files directly, but the guard prevents
execution if a misconfigured server or directory traversal reaches them:

```
404.php
archive.php
comments.php
footer.php
header.php
index.php
page.php
search.php
searchform.php
sidebar.php
single.php
templates/single-puisi-generic.php
templates/template-kolofon.php
templates/template-mukadimah.php
```

**Fix:** Add as the first line after the opening `<?php` in each file:

```php
defined( 'ABSPATH' ) || exit;
```

---

### ✅ Google Fonts Privacy Toggle (Resolved in v3.5.1)

**File:** `inc/setup/enqueue.php`
**Severity:** Resolved for no-remote-fonts operation.

Sasterawan now includes `book_wp_remote_fonts` in Theme Options → Typography.
When disabled, the theme skips Google Fonts stylesheet requests, font
preconnect hints, and form-specific Google display fonts. The CSS keeps the
same font-family preferences and falls back to visitor-installed fonts or
browser/system stacks.

For exact typography without Google Fonts, self-host the relevant `.woff2`
files and update the existing local font CSS to reference those assets.

---

### ✅ Responsive — Single Breakpoint at 52em (Resolved in v3.3.49)

**File:** `assets/css/frontend.css`
**Severity:** Low — layout works but is not optimised for tablets and large phones.

The theme has one responsive breakpoint at `52em`. No intermediate treatment.
Two additions recommended:

1. **Second breakpoint at ~36em** — masthead and nav strip specifically.
   Nav items at `.72rem` uppercase may wrap badly on 320–375px viewports.

2. **Poetry column on mobile** — `max-width: 32rem` for poetry content is
   correct at desktop. At mobile, `32rem` at a 16px base = 512px, wider than
   most phones. The column will be full-width anyway but the centering logic
   should explicitly reset:

   ```css
   @media screen and (max-width: 36em) {
       .bwp-form-puisi .bwp-work__content,
       .bwp-form-syair .bwp-work__content,
       .bwp-form-karmina .bwp-work__content,
       .bwp-form-ratapan .bwp-work__content {
           max-width: 100%;
       }
   }
   ```

---

### ✅ Print Stylesheet — Minimal Typography (Resolved in v3.3.49)

**File:** `assets/css/frontend.css` — `@media print` block
**Severity:** Low — print works but is unpolished.

Current print CSS hides UI chrome but does no typography work.
Recommended additions:

- Heading size reduction for print (`h1`, `h2`, `h3` — `font-size` in `pt`)
- `page-break-inside: avoid` on `.bwp-work`, `.bwp-colophon`, `.bwp-stanza`
- `orphans: 3; widows: 3` on `.bwp-work__content p`
- Running header (author + title) via `@page` — requires CSS Paged Media
  (supported in print-specific browsers; degrades gracefully in others)

---

### ✅ JS File Header Version Strings Stale (Resolved in v3.3.49)
---

### ✅ S1 — REST API Meta Exposure (Resolved in v3.3.50)

All 50+ scholarly and personal meta fields set to `show_in_rest: false`.
Only `book_wp_chapter_number` and `book_wp_chapter_part` retain REST exposure
(required by the block editor). See `inc/hooks/actions.php` — `book_wp_register_meta()`.

---

### ✅ S2 — Incorrect CSS Output Sanitisation (Resolved in v3.3.50)

`sanitize_textarea_field` replaced with `wp_strip_all_tags` on save.
Output is now raw `$css` (pre-sanitised on save). Angle brackets in CSS
child combinators, `calc()`, and `content:` values are preserved correctly.

---

### ✅ S3 — Scheme/Font Path Without Whitelist (Resolved in v3.3.50)

Explicit whitelist arrays replace `sanitize_file_name()` for both scheme
slugs and font slugs. Unknown values bail early — no file path is constructed.

---

### ✅ S4 — Shortcode Numeric Attributes Not Cast (Resolved in v3.3.50)

`intval()` applied to all numeric shortcode attributes (`[chapter num]`,
`[chapter verse_start]`, `[verse num]`, `[v num]`) immediately after
`shortcode_atts()`. `[chapter]` attributes use `max(1, intval(...))`.

---

### ✅ D2 — All Time CSS Loaded Always (Resolved in v3.3.50)

Five individual time CSS files merged into `assets/css/times/times.css`.
Single `wp_enqueue_style` call replaces the previous `foreach` loop.
Old files (`dawn.css`, `morning.css`, `afternoon.css`, `dusk.css`, `night.css`) removed.

---

### ✅ D3 — Uncached TOC Query (Resolved in v3.3.50)

`get_posts(numberposts: -1)` wrapped in `get_transient('book_wp_toc_posts')`.
Cache TTL: 12 hours. Invalidated on `save_post`, `trashed_post`, `untrashed_post`
via `book_wp_clear_toc_cache()` in `inc/hooks/actions.php`.

---

### ✅ D4 — Shortcode/Block HTML Divergence (Resolved in v3.3.50)

Chapter shortcode `$num_html` moved outside `.bwp-chapter__content` to match
block render callback. Both paths now produce identical DOM structure.

---

### ✅ D6 — Editor Inline Style Duplication (Resolved in v3.3.50)

Inline style objects removed from `blocks.js` canvas render. Layout and
typography now handled by `editor.css` via `.bwp-chapter*` class selectors.

---

### ✅ D1 — Dual Translation System (Resolved in v3.3.51)

**Files:** `inc/helpers/strings.php` + all template files using `book_wp_str()`
**Severity:** High — two parallel translation systems cannot be unified by translators.

`strings.php` contains 40+ front-end strings in a hardcoded PHP array switching
on `get_locale()`. The rest of the theme uses WordPress gettext (`__()`, `_e()`).
Translators using Poedit or Loco Translate see neither system completely.

**Fix:** Replace every `book_wp_str('key')` call with `__('English string', 'book-wp')`.
Add the Malay translations to `languages/ms_MY.po`. Delete `strings.php`.
This touches every template file and both helper files — do as a dedicated release.

**Resolution:** Done in v3.3.51. `strings.php` deleted. 40 call sites across 16 files
migrated to `__()`. `book_wp_form_label()` added for dynamic slug lookups.
`ms_MY.po`, `es_ES.po`, `fr_FR.po`, and `book-wp.pot` all updated.

---

### 🟡 R1 — CSS Cascade Conflicts (Partially Resolved)

**Files:** `assets/css/frontend.css`, `assets/css/literary.css`, `assets/css/forms/*.css`
**Severity:** Reduced from High to Medium (v3.4.91).

The scheme/time token conflict — where hikayat, ratapan, and memoir day palettes
overwrote both scheme surface and time ink — was resolved in v3.4.91. All three
forms now set surface-only day palettes with `:not(.bwp-scheme-*)` guards, and
ink tokens inherit from the time system. Missing night tokens (`--bwp-teal`,
`--bwp-gold-dim`, `--bwp-gold-light`) were added to all five night activation
paths. The broader architectural concern remains: `.bwp-colophon` and
`.bwp-work__content` each appear in 10+ CSS files, and the cascade relies on
load-order via `wp_enqueue_style` dependency chains. A reordering of dependencies
could still silently break form-specific overrides. Consider CSS `@layer` as a
future hardening measure.

---

### 🟡 R5 — WCAG Failures in Time/Scheme Modes (Partially Addressed)

**Severity:** Medium — critical failures (invisible `--bwp-teal` text on dark
backgrounds) resolved in v3.4.91. Full contrast validation across all dawn,
morning, and dusk palettes is still pending. `--bwp-link` used as focus ring
colour may not meet 3:1 contrast against warm light backgrounds (cream + morning
in particular). Requires a full audit per mode with automated axe scan or manual
contrast-ratio checks.

---

### ✅ R15 — Absence of Regression CI (Resolved in v3.3.52)

**Severity:** High — no phpcs, no eslint, no Lighthouse budget, no GitHub Actions.

Recommended stack:
- phpcs with `WordPress-Core` ruleset (not full `WordPress` — avoids noisy doc block warnings)
- eslint for `blocks.js` and `frontend.js`
- GitHub Actions workflow: lint on PR, Lighthouse on merge to main
- Visual regression: Percy or Playwright screenshots of index, single-puisi, single-ratapan

**Resolution:** Done in v3.3.52. `composer.json`, `.phpcs.xml.dist`, `package.json`,
`.eslintrc.json`, `.stylelintrc.json`, `.github/workflows/ci.yml`, `lighthouserc.json`,
`release-checklist.md`, `.gitignore` all added. Lighthouse job is present but commented
out — requires a deployed preview URL and `LHCI_GITHUB_APP_TOKEN` secret to activate.


**Files:** `assets/js/frontend.js`, `assets/js/time-theme.js`

`frontend.js` says `@version 3.4.6`. `time-theme.js` says `@version 3.4.91`.
Theme is at `3.4.91`. Update `frontend.js` header as part of the next release.

---
