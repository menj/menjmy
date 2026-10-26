=== Sasterawan ===
Contributors: menj
Requires at least: 6.0
Tested up to: 6.9.1
Requires PHP: 8.2
Version: 3.5.1
Stable tag: 3.5.1
License: GPL-2.0-or-later
Tags: literary, poetry, book, bilingual, minimal, typography

Sasterawan is a literary anthology theme tailored for https://menj.my (Di Bawah Permukaan): per-form typography for Malay literary forms (puisi, syair, karmina, hikayat, cerpen, certot, esei, memoir), reading analytics, thematic tagging, night mode, bilingual support, and a custom logo in the footer.


== Description ==

Sasterawan is a minimalist-modern WordPress theme for publishing literary works.
It treats your site as a literary anthology — white ground, high-contrast ink,
EB Garamond at 18px, Cinzel display headings. No decorative apparatus competes
with the writing.

Works are organised by literary form (puisi, nukilan, ratapan, syair, karmina,
hikayat, cerpen, novel, esei, memoir) using the built-in custom taxonomy,
or by category for thematic grouping. Metadata is designed for the sole author:
fields capture your creative process, not an archivist's cataloguing notes.


== Features ==

= Post Formats =
Sasterawan declares support for two WordPress post formats:
* aside — short annotations and prose notes outside the main literary taxonomy
* image — illustrated works and cover reveals
The literary_form taxonomy remains the primary classification for all full-form
works. Post formats are a complementary standard-WordPress layer enabling
format-based queries and feed filtering.

= Anthology & Organisation =
* Author name — set in Sasterawan General settings; displayed in the theme header
  on every page. Use the WordPress Site Title for the anthology or collection
  name — it controls the browser tab, RSS feed, and SEO title
* Anthology name — dedicated setting for the collection title, shown prominently
  on the front page; falls back to the WordPress site title if left blank
* Custom logo — displayed in the site footer above the anthology name and
  copyright block. Upload via Appearance > Customize > Site Identity.
* Category support — WordPress categories serve as collections or thematic groups
  (e.g. "Koleksi Pertama", "Cinta", "Alam")
* TOC grouping — four modes: flat list, group by literary form, group by category, group by tag
* Archive and TOC layout — list, grid, or Masonry; pagination as paged or Infinite Scroll
* TOC flat-list thumbnails — 80×80 featured image crops (book-wp-thumb) when grouping is None
* Literary form taxonomy — custom taxonomy with ten default forms: puisi, syair,
  karmina, ratapan, nukilan, hikayat, cerpen, novel, esei, memoir

= Literary Metadata =
* Epigraph — opening quotation with source attribution
* Dedication — "Untuk ibuku yang tercinta"
* Colophon — year composed, source/collection, original language, author's note
* Per-work ornament override — each work can use a different decorative symbol
* TOC excerpt — custom subtitle shown in the Table of Contents

= Typographic Ornaments =
Seven decorative styles used throughout the theme: Fleuron, Asterism, Dinkus,
Dash, Leaf, Flower, Diamond. Configurable globally and per-work. All rendered as crisp vector graphics compatible with any font.

= Reading Analytics =
* Reading time estimate (120 wpm for poetry, 200 wpm for prose)
* Word count, line count (poetry), stanza count (multi-stanza poetry)
* Literary form badge and category label in analytics bar

= Reading Experience =
* Scroll-driven reading progress bar
* Keyboard navigation — left/right arrow keys between works
* Night mode — floating toggle with same-day persistence (auto-expires at next
  daytime visit; use Night scheme for permanent dark mode)
* Reading memory — remembers and restores scroll position per work
* Time-of-day live updates — ink palette re-evaluates on tab return and hourly

= Poetry & Prose Typography =
* Drop caps — decorative initial letter on prose works (configurable)
* Paragraph indentation for prose; stanza spacing for poetry
* Optional stanza numbering
* Configurable poetry alignment (left or centred)

= Shortcodes =
* [bwp_quote source="..."]...[/bwp_quote] — styled literary quotation block
* [bwp_divider style="fleuron"] — decorative section divider
* [bwp_verse num="1"]...[/bwp_verse] — numbered verse block
* [bwp_term definition="..."]term[/bwp_term] — glossary tooltip
* [bwp_chapter num="1" title="optional"]...[/bwp_chapter] — Ratapan chapter with biblical verse layout
* [bwp_v num="3"] — inline verse number marker

= Page Templates =
* Mukadimah (Foreword) — decorative foreword page with special typography
* Kolofon (Colophon Page) — bibliographic and publishing information page

= Colour Schemes (Paper & Ink) =
Two independent controls work together. Paper (Colour Scheme) sets the background:
Default (warm white), Cream (aged paper), Dusk (warm grey), Night (dark mode).
Ink (Time of Day) sets the text colour and accent mood: auto-detects from the
visitor's clock, or can be pinned to Dawn, Morning, Afternoon, Dusk, or Night.
Any Paper × Ink combination is valid — cream parchment at dusk, warm grey at
dawn, dark mode at morning.

= Font Choices =
Seven options: Georgia (default), EB Garamond, Lora, Vollkorn, Arvo, Maven Pro,
Ubuntu. Selectable from Appearance > Sasterawan > Typography. The Amiri font
(Arabic script / Jawi) is also bundled for right-to-left text support. The
Typography tab includes a Load Google Fonts toggle; disable it for a
no-remote-fonts mode that uses browser/system fallbacks.

= Bilingual Interface =
Full Bahasa Melayu and English support with 40+ translated strings. Language
follows the WordPress site language (Settings > General > Site Language).

= Structured Data =
Structured data output for search engines — includes author, publication date, genre, word count, and series information.

= Thematic Tags =
* Up to five thematic tags per post (trigger/hidden tags excluded from cap)
* Tags displayed as centred pill links between content and colophon, with § glyph header
* Dedicated tag archive template with "Tagged with" label and pill-style heading
* TOC "By Tag" grouping — posts listed under every tag they carry
* Hidden-post feature — posts tagged with designated slugs are removed from TOC,
  category archives, and search results (still accessible at permalink and tag archive)

= Per-tag Decorative Backgrounds =
* Certain tags can apply an optional decorative visual treatment to single posts
* Included: posts tagged "erotika" display a batik motif background, subtly visible
  behind the text, with automatic adjustments for all colour schemes
* For details on adding your own per-tag backgrounds, see docs/readme.md

= Related Works =
Automatic discovery of related works by collection (category) and literary form,
displayed in a grid with badges and year metadata.

= Previous/Next Navigation =
Links between adjacent works with directional arrows and titles.

= Accessibility =
WCAG 2.2 Level AA compliant. Skip link, landmark regions, sufficient colour
contrast across all schemes and time-of-day themes, visible focus states,
aria-pressed on night mode toggle, prefers-reduced-motion support.


== Settings ==

Six-tab settings panel under Appearance > Sasterawan. Each tab saves independently.

General — Author name, anthology name, front page description, copyright text,
TOC grouping (flat / by literary form / by category / by tag), and display toggles.

Typography — Font family (seven choices), colour scheme (Default, Cream, Dusk,
Night), time-of-day theme, drop caps, and poetry alignment.

Literary — Decorative ornament style (seven choices), featured image display,
related works, stanza line numbers, analytics bar, tags display, and hidden-post
tag configuration.

Reading — Reading progress bar, keyboard navigation between works, scroll
position memory, Night Mode toggle, and comments.

Advanced — Structured data output and custom CSS field.

Social — Seventeen social and bibliographic profile URL fields: Twitter/X, Facebook,
Instagram, Threads, LinkedIn, YouTube, Medium, Wikipedia, GitHub, Goodreads, WorldCat,
Library of Congress, NLB Singapore, Perpustakaan Negara Malaysia, ORCID, ISNI, and RSS.
Populated URLs render as circular icon buttons in the site footer.


== Developer Reference ==

For technical documentation — directory structure, hooks, PHP functions, CSS
architecture, and upgrade notes — see docs/readme.md, docs/upgrading.md, and
docs/ssot.md included in the theme folder, or visit the project repository.


== Installation ==

1. Upload the book-wp folder to /wp-content/themes/
2. Activate the theme in Appearance > Themes
3. Go to Appearance > Sasterawan to configure settings
4. Set your WordPress Site Title (Settings > General) to the anthology or collection name
5. Set your Author Name in the Sasterawan General tab
6. Optionally upload a logo via Appearance > Customize > Site Identity — it will
   appear in the site footer above the anthology name and copyright block
7. Choose a colour scheme and font in the Typography tab
8. Optionally set a time-of-day theme (auto-switches palette by hour) in Typography
9. Create categories for your collections (e.g. "Koleksi Pertama", "Cinta")
10. Go to Posts > Literary Forms to confirm or add literary form terms
11. Assign a literary form to each post using the Literary Form panel in the post editor
12. Add literary metadata (epigraph, dedication, year composed, author's note) via
    the Literary Metadata panel in the post editor
13. If using Masonry, Grid, or Infinite Scroll layouts, set your preferred layout
    options in Appearance > Sasterawan > Literary
14. If using grid or Masonry layouts with featured images, regenerate your image
    thumbnails using the Regenerate Thumbnails plugin (available from the WordPress
    plugin directory)


== Requirements ==

* WordPress 6.0 or higher
* PHP 8.2 or higher


== Frequently Asked Questions ==

= Can I use this theme for prose works, not just poetry? =

Yes. Sasterawan supports cerpen (short story), novel (long-form prose), hikayat
(classical narrative), esei (essay), and memoir in addition to verse forms
(puisi, syair, karmina, ratapan, nukilan). Each form has its own typography,
column width, drop cap settings, and analytics display. Assign the appropriate
literary form to each post via the Literary Form panel in the post editor.

= How do I set the anthology title shown on the front page? =

Go to Appearance > Sasterawan > General and fill in the Anthology Name field. This
is separate from the WordPress Site Title — the Site Title controls the browser
tab, RSS feed, and SEO title. The anthology name is displayed prominently on the
front page above the Table of Contents.

= How do I add a logo? =

Upload your logo via Appearance > Customize > Site Identity. The logo is
displayed in the site footer, above the anthology name and copyright line.
Maximum display height is 60px; width scales automatically.

= How do I add a new literary form? =

Go to Posts > Literary Forms and add a new term. The theme seeds ten default
forms on activation (puisi, nukilan, ratapan, syair, karmina, hikayat, cerpen,
novel, esei, memoir) but any additional terms you create will appear in the TOC
grouping and archives.

= The colophon is not showing on my posts. =

The colophon displays only when at least one colophon field is filled in (year
composed, source, dedication, epigraph, author's note, etc.). Edit the post and
check the Literary Metadata panel in the right-hand sidebar or below the editor.

= How do I add an author's note to a post? =

Open the post editor and find the Literary Metadata panel. Fill in the Author's
Note field. It renders as a full-width banner between the analytics bar and the
body content, visible before the reader begins the work.

= Can I disable the reading progress bar? =

Yes. Go to Appearance > Sasterawan > Reading and turn off the Progress Bar toggle.

= How do I change the colour scheme? =

Go to Appearance > Sasterawan > Typography and select from the Colour Scheme
selector: Default (high-contrast ink on warm white), Cream (aged paper), Dusk
(warm grey), or Night (dark mode). The Night scheme can also be toggled by the
reader using the moon icon in the navigation bar if the Night Mode toggle is
enabled under Reading settings.

= What is the time-of-day theme? =

The time-of-day theme automatically adjusts the ink palette (text colour, link
colour, accent mood) based on the visitor's local clock — lavender at dawn, amber
in the morning, neutral in the afternoon, warm ochre at dusk, silver on dark at
night. Configure it in Appearance > Sasterawan > Typography. Set it to Auto to
follow the clock, or pin it to a specific period.

In Auto mode, the ink updates live: when you return to a backgrounded tab the
theme re-evaluates the period, and a background check runs every hour for
foreground tabs. The chosen colour scheme (Paper) is independent — cream
parchment at dusk, warm grey at dawn, any combination works.

= Does the night toggle persist forever? =

No. The floating moon toggle saves the preference in the browser, but the
theme auto-expires it: if you toggled dark mode last night and return during
the day, the preference clears automatically on first visit. If you want
permanent dark mode, use the Night colour scheme in Appearance > Sasterawan >
Typography instead of the toggle.

= How does the hidden-post feature work? =

Designate one or more tag slugs as hidden triggers in Appearance > Sasterawan >
Literary (the Hidden Tags multi-select widget). Any post carrying one of those
tags is removed from the TOC, category archives, and search results, but remains
accessible at its permalink and on its tag archive page. Useful for drafts,
restricted content, or administrative posts.

= How many tags can I add to a post? =

Up to five thematic tags per post. Tags used as hidden-post triggers are not
counted toward this limit. For developers: the cap is configurable via the
BOOK_WP_MAX_TAGS constant — see docs/readme.md for details.

= How do I use Masonry or Infinite Scroll on archives? =

Go to Appearance > Sasterawan > Literary and set Archive Layout to Masonry (or Grid)
and Archive Pagination to Infinite Scroll. The same settings are available
separately for the TOC flat-list when TOC Grouping is set to None. If you use
featured images with a grid or Masonry layout, regenerate your thumbnails once
after enabling the layout — use the Regenerate Thumbnails plugin from the
WordPress plugin directory.

= Can I disable drop caps on specific posts? =

Yes. Open the post editor and find the Literary Metadata panel. The Drop Cap
Override field lets you force drop caps on or off for that post regardless of
the global per-form setting in Typography.

= Does the theme support right-to-left languages? =

Partial support is in place — the Amiri font (Arabic script / Jawi) is bundled
and selectable from the Typography font list. Full RTL stylesheet support is
planned for a future release.

= Can I use a child theme? =

Yes. Create a child theme in the standard WordPress way. The theme's consistent
naming conventions make it straightforward to override specific styles or
templates without touching the parent theme files.

= How do I enable structured data for my posts? =

Go to Appearance > Sasterawan > Advanced and enable the Structured Data toggle.
The theme will automatically generate search-engine-readable information for
each work, including title, author, publication date, genre, and word count.

== Privacy & Data Collection ==

Sasterawan does not collect, transmit, or store analytics or reader profile data.
No tracking scripts or analytics calls are loaded on the front end.

= Google Fonts =

When Load Google Fonts is enabled in Appearance > Sasterawan > Typography, the
theme loads Cinzel, EB Garamond, Jost, form-specific display fonts, and any
selected optional body font from Google Fonts (fonts.googleapis.com). This
causes the visitor's IP address to be transmitted to Google's servers when the
font stylesheet and font files are requested.

Disable Load Google Fonts to prevent remote font requests. In that mode the
theme keeps the same font-family preferences but relies on visitor-installed
fonts and browser/system fallbacks.

If you need exact typography without Google Fonts, self-host the required font
files and point the existing font CSS at those local assets.

No other remote connections are made by this theme.


== Upgrade Notice ==
= 3.5.1 =
Drop-in upgrade. Fixes Back to Top JavaScript scope, moves custom CSS to the stylesheet queue, adds no-remote-fonts mode, hardens chapter-block analytics, and adds documented lint/test/CI config.

= 3.5.0 =
Documentation streamlined (developer docs moved to docs/), version unified at 3.5.0, and translation files (.pot, .po, .mo) regenerated from source. Bug fix: 14 Malay strings in comments.php that were incorrectly used as English msgids are now proper English msgids with Malay translations. Drop-in upgrade.

= 3.4.92 =
Drop-in upgrade. Hidden-tagged posts now excluded from RankMath HTML Sitemap.

= 3.4.91 =
Drop-in upgrade. Comprehensive Paper/Ink audit — fixes invisible text (novel drop caps, esei headings) in night mode, restores Cream/Dusk scheme support for Hikayat/Ratapan/Memoir, restores time-of-day ink mood across all forms. Time-of-day now updates live when returning to stale tabs. Night toggle auto-expires at next daytime visit (no more stuck dark mode at 2 PM).

= 3.4.90 =
Drop-in upgrade. Correctly fixes invisible text on Ratapan, Hikayat, Memoir in night mode. The v3.4.89 attempt failed due to CSS specificity — the day palette selector now excludes html.bwp-night at the :root level.

= 3.4.90 =
Drop-in upgrade. Fixes invisible text on Ratapan (and Hikayat, Memoir) in all night modes: toggle, persistent Night scheme, time-of-day, and OS dark preference.

= 3.4.90 =
Drop-in upgrade. Corrects heading hierarchy: author name is now H2 on all pages; TOC heading is H3; TOC group headings are H4.

= 3.4.90 =
Drop-in upgrade. Fixes a double-H1 issue on the homepage: the author name in the site header is now always a <p> element, leaving each page template's own title as the sole H1.

= 3.4.90 =
Drop-in upgrade. Three bug fixes: hidden tag slugs no longer leak into page HTML source; Schema.org inLanguage now reflects the WordPress site locale; TOC cache eviction is now reliable on persistent object cache setups.


= 3.4.60 =
Literary Form Permalinks — opt-in /{form-slug}/{post-slug}/ URL structure (e.g. /puisi/waktu-melata/). Off by default. After enabling, visit Settings > Permalinks and click Save Changes to flush rewrite rules. Posts with no literary form assigned are unaffected. Taxonomy archives remain at /form/{slug}/.

= 3.4.59 =
JSON Feed v1.1 published at /feed/json. Includes full content, summary, featured image, thematic tags, and a _book_wp extension object with literary form, word count, and reading time. Autodiscovery link added to front-page head. Toggle in Appearance > Sasterawan > Advanced.

= 3.4.58 =
Share row added to single posts — Copy link, X, Facebook, WhatsApp. Off by default; enable under Appearance > Sasterawan > Reading. No third-party SDKs or remote scripts loaded.

= 3.4.57 =
OpenGraph and Twitter Card meta tags now output on single posts. Controls share card appearance on X (Twitter), Facebook, LinkedIn, WhatsApp, and Telegram. Automatically disabled when RankMath, Yoast, or another SEO plugin is detected. Toggle in Appearance > Sasterawan > Advanced.

= 3.4.55 =
Bug fix: social profile fields were not saving. The social tab fields were registered under the wrong key in the save handler ('advanced' instead of 'social'), causing all 17 social platform options to be silently discarded on every save.

= 3.4.54 =
Hidden tag system hardened: storage migrated from slugs to term IDs (automatic migration on first admin load). Query coverage expanded to date archives, author archives, feeds, non-hidden tag archives, sidebar widget queries, and REST API. Core XML sitemap exclusion added. Tag Cloud widget suppression added. the_tags output filter added as a safety net for all output paths.

= 3.4.53 =
WCAG 2.2 batch 2: broken role=tab removed from admin settings (SC 4.1.2). Social toggle 24px minimum target (SC 2.5.8). Footer padding prevents night toggle obscuring focus (SC 2.4.11). Required-field note readable in full (SC 3.3.2). Tag-select combobox ARIA completed (SC 4.1.2). Inline links underlined in text-bearing contexts (SC 1.4.1).

= 3.4.52 =
WCAG 2.2 batch 1: sub-menu keyboard access via :focus-within (SC 2.1.1). 404 heading corrected to h1 (SC 1.3.1). Pagination prev/next links given accessible names (SC 2.4.4).

= 3.4.51 =
CWV batch 2: body fade-in moved to .bwp-page-wrap (LCP fix). Footer logo dimension fix (CLS). Reading memory scroll restore no longer causes post-paint page jump (CLS). Word count cached in post meta (TTFB). TOC thumbnails gain decoding="async" (INP).

= 3.4.50 =
CWV batch 1: Google Fonts @import removed from frontend.css (LCP fix — parallel loading). Featured image aspect-ratio corrected from 16/9 to 2/1 (CLS fix).

= 3.4.49 =
nofollow removed from social profile links — these are owned identity endpoints; PageRank authority is correctly passed to them.

= 3.4.48 =
Seven new book/library platforms added (Goodreads, WorldCat, LOC, NLB Singapore, PNM, ORCID, ISNI). Social links now carry rel="nofollow" explicitly.

= 3.4.47 =
Social icons collapse/expand toggle — max 6 icons visible by default; +N button reveals remaining icons. Degrades gracefully without JS.

= 3.4.46 =
Social profile icons added to site footer. New Social tab in Appearance > Sasterawan. Platforms: Twitter/X, Facebook, Instagram, Threads, LinkedIn, YouTube, Medium, Wikipedia, GitHub, RSS.

= 3.4.45 =
Border radius applied globally via --bwp-radius: 6px token. Featured image pinned at 6px. Form badges (literary form labels) changed to pill shape (border-radius: 2em).

= 3.4.44 =
Post format support (aside, image) and Customizer selective-refresh-widgets declared. KIV roadmap entries for RTL stylesheet and block patterns added to upgrading.md.

= 3.4.42 =
Comment textarea placeholder updated to a poetic Malay phrase. Translation files updated.

= 3.4.41 =
Bugfix: ellipsis placeholder in comment form and tag selector was displaying as literal text \u2026.

= 3.4.40 =
Root folder cleanup — config/ directory added, release checklist merged into upgrading.md, empty template-parts removed, package.json version corrected.
= 3.4.39 =
Logo moved to site footer — no longer displayed in the header. No settings change required.

= 3.4.38 =
Per-form erotika geometry overrides + readme.txt features, installation, and FAQ review.


= 3.4.37 =
Erotika column background corrected — extends to column borders only, no viewport overflow.

= 3.4.36 =
Erotika column background extended full-bleed to meet side panels flush.

= 3.4.35 =
Erotika tag decorative background — per-tag CSS pipeline, column background, side panels, scheme-aware opacity.

= 3.4.28 =
Documentation audit — readme.txt, changelog.md and upgrading.md congruence fixes; version fields, PHP requirement, directory listing, and duplicate entries corrected.

= 3.4.27 =
Archive layout and TOC flat-list — Masonry, Infinite Scroll, featured thumbnails; four JS bug fixes.

= 3.4.23 =
en-US standardisation: template files renamed, CSS classes renamed, all bilingual option strings stripped to English with ms-MY translations added to .po file.

= 3.4.22 =
Puisi, Syair, Karmina analytics bar compact mode — tighter gap and smaller text so all six items fit on one line.

= 3.4.21 =
Author's Note repositioned — full-width banner at page top between analytics bar and content.

= 3.4.19 =
Hikayat chrome centred (title, analytics, ornament, colophon). Citation label ("Cited from") opt-in via meta box checkbox. Prose body stays left.

= 3.4.18 =
Karmina page wrap widened; Nukilan column widened and title italic removed.

= 3.4.15 =
Drop cap fonts — IM Fell English SC (hikayat), Libre Caslon Display (nukilan).

= 3.4.13 =
All 10 literary form headers standardised — consistent typography and alignment across all forms.

= 3.4.12 =
Comments section redesigned to match the theme aesthetic — custom comment list, form, and avatar layout.

= 3.4.11 =
Gutenberg-native shortcode support — shortcodes now work as native blocks in the block editor.

= 3.4.10 =
Navigation reliability fixes — prev/next now correctly skips hidden posts; related works query optimised.

= 3.4.9 =
Hidden-tagged posts excluded from the Related Works (Karya Berkaitan) section — both same-source and same-form candidate loops now skip posts carrying a hidden-trigger tag.

= 3.4.8 =
Search results page redesigned to match tag archive: same bwp-archive-header structure with leaf ornament, query displayed as pill, result list items include form badge and year. Orphaned bwp-search-page CSS removed.

= 3.4.7 =
Thematic tag cap raised from 3 to 5. Shortcodes renamed with bwp_ prefix. Translation files updated.

= 3.4.6 =
Adds tag support: thematic tag display on single posts, TOC by-tag grouping, tag archive template, hidden-post feature, and a 5-tag cap with admin multi-select widget.

= 3.4.5 =
Compliance fixes: licence corrected to GPL-2.0-or-later, CRLF normalised, capability scoped to edit_theme_options, content links underlined (WCAG 1.4.1), readme expanded with FAQ and Privacy sections.

== Changelog ==
= 3.5.1 =
* Fixed: Back to Top frontend script now has its own `bookWP` config scope, preventing `cfg is not defined` errors.
* Changed: Custom CSS is now attached with `wp_add_inline_style()` after the active theme stylesheet stack instead of being echoed from an admin-only include.
* Added: Typography setting `book_wp_remote_fonts` / Load Google Fonts. Disable it to prevent Google Fonts requests and use browser/system fallbacks.
* Improved: Chapter block analytics use WordPress `parse_blocks()` when available, with a standalone-test fallback.
* Improved: JSON Feed `reading_time_min` now uses a numeric helper instead of casting a translated display string.
* Added: ESLint, Stylelint, PHPCS, PHPUnit Composer script, and GitHub Actions CI workflow.
* Updated: Translation template, locale PO files, and compiled MO files for the new Typography setting strings.

= 3.5.0 =
* Documentation reorganisation. `readme.md`, `upgrading.md`, `changelog.md`, and the new `ssot.md` moved to `docs/`. `readme.txt` stays at root (WordPress.org convention).
* Version unified across all sources: `style.css`, `package.json`, `readme.txt`, `docs/readme.md`, `docs/upgrading.md`, `docs/changelog.md` all read 3.5.0. Resolves prior drift (package.json was at 3.4.42; readmes lagged at 3.4.92 while style.css advanced to 3.4.99).
* New file `docs/ssot.md` — Single Source of Truth: file-of-record map for version, taxonomy slug, text domain, requirements; doc inventory; hard invariants.
* `theme.json` FSE-migration pointer updated to `docs/upgrading.md`.
* `phpunit.xml` moved to `config/` (run tests via `phpunit -c config/phpunit.xml`).
* Bug fix: `comments.php` had 14 Malay strings used as English `msgid` arguments (e.g. `'Tinggalkan Ulasan'`, `'Hantar Ulasan'`, `'%1$s pada %2$s'`) — none of which existed in any `.po` file. All replaced with canonical English msgids; Malay text rehomed as `ms_MY.po` translations.
* Translation files regenerated from source. `book-wp.pot` was at v3.3.56; now covers all 361 source msgids (62 added, 5 dead pruned). Existing `ms_MY` / `es_ES` / `fr_FR` translations preserved on msgid match. Compiled `.mo` binaries also rebuilt — ms_MY (315 strings), es_ES (299), fr_FR (299).
* Mobile / accessibility polish: comment submit button raised to 44 px (WCAG 2.5.5 AAA); defensive 24 px floor on footer social icons; comment-form responsive breakpoint unified at 36em; Ratapan Bible-style chapter numeral shrinks to 2.6em on phones so verse text wraps cleanly at 360 px.
* No other code, option, or asset changes. Drop-in upgrade.

= 3.4.92 =
* Fixed: Hidden-tagged posts appeared in RankMath HTML Sitemap. RankMath's HTML sitemap module runs its own WP_Query, bypassing both pre_get_posts and the core XML sitemap filter. Added book_wp_hide_from_rankmath_html_sitemap() on rank_math/sitemap/html_sitemap/post_args.

= 3.4.91 =
* Fixed: --bwp-teal invisible on dark backgrounds — novel drop caps and esei section headings/title borders were unreadable in all night-mode paths except the Night scheme. times.css value corrected from #0c1e32 to #4a8ab0.
* Fixed: Hikayat, Ratapan, Memoir day palettes overrode both Cream/Dusk scheme surface and time-of-day ink (dawn/morning/dusk mood was flattened). Refactored all three to surface-only + form accents; ink now comes from the time system. Added :not(.bwp-scheme-cream):not(.bwp-scheme-dusk) guards so forms yield when an explicit scheme is active.
* Fixed: --bwp-gold-dim and --bwp-gold-light had no night-safe values in any activation path. Added to manual toggle, prefers-color-scheme fallback, time-night, and Night scheme (base + all four compound selectors).
* Fixed: --bwp-teal, --bwp-teal-deep, --bwp-gold missing from manual night toggle and prefers-color-scheme fallback in frontend.css.
* Fixed: --bwp-stone missing from time-night ink block in times.css.
* Fixed: Ratapan progress bar used hardcoded #6a6870 instead of var(--bwp-progress).
* Fixed: literary.css thesis night override only matched body.bwp-night; extended to html.bwp-night, :root.bwp-time-night, and body.bwp-scheme-night. Added night override for .bwp-colophon__karmina (had none).
* Fixed: Time-of-day period was frozen at page load — returning to a stale tab showed the wrong ink. time-theme.js now re-evaluates on visibilitychange and via an hourly interval. Only active when Time of Day is set to Auto.
* Fixed: Night toggle (localStorage) persisted indefinitely. Toggling at 10 PM kept dark mode active at 2 PM the next day. The time engine now auto-expires the toggle when: (a) the current period is daytime (dawn–dusk), AND (b) the toggle was activated on a previous calendar date or has no date stamp (legacy). Same-day toggles are honoured. frontend.js now stores bwp_night_date alongside bwp_night_mode.

= 3.4.90 =
* Fixed: Ratapan, Hikayat, Memoir — invisible text in night mode. CSS specificity caused day palette (0,5,1) to beat night override (0,2,2). Day selector now uses :root:not(.bwp-time-night):not(.bwp-night) to exclude the html.bwp-night preload path entirely.

= 3.4.90 =
* Fixed: Ratapan, Hikayat, Memoir forms — text invisible in night mode when JS preload toggle (html.bwp-night) was active. Day palette was overwriting --bwp-text with dark ink on dark background.

= 3.4.90 =
* Changed: author name in site header promoted to H2 (was <p> after v3.4.87 fix).
* Changed: "Table of Contents" demoted to H3 (was H2).
* Changed: TOC group headings demoted to H4 (were H3).

= 3.4.90 =
* Fixed: author name in site header rendered as <h1> on blog-front page, producing two H1 tags alongside the anthology title. Now always <p>.

= 3.4.90 =
* Fixed: hidden tag slugs were exposed as bwp-tag-{slug} body classes in HTML source even when suppressed from all UI.
* Fixed: Schema.org inLanguage always output "en" due to a stale removed option; now reads from get_locale().
* Fixed: TOC transient was not evicted from persistent object caches (Redis/Memcached) on invalidation.


= 3.4.60 =
inc/helpers/permalink.php added. book_wp_register_form_permalink_rules() registers rewrite rules at 'top' priority using a precise alternation of live literary_form term slugs — never a wildcard. book_wp_form_permalink() filters post_link to return /{form-slug}/{post-slug}/. Auto-flush on option toggle (updated_option) and theme switch (after_switch_theme). Deferred flush via admin_init flag for term create/edit/delete. book_wp_get_form_slug() and book_wp_permalink_form_slugs() added as public helpers. Toggle in Advanced tab, Permalinks section (default off).

= 3.4.59 =
book_wp_register_json_feed() and book_wp_json_feed_render() added to new file inc/helpers/json-feed.php, loaded in functions.php. Feed registered via add_feed('json'). Full JSON Feed v1.1 spec output: feed-level fields (version, title, home_page_url, feed_url, description, language, authors) and per-item fields (id, url, title, content_html via apply_filters, summary, date_published/modified in ISO 8601 UTC, authors, image, tags, _book_wp extension object). Hidden tags excluded. book_wp_json_feed_link_tag() emits autodiscovery <link> on wp_head priority 2 on front page and blog index. book_wp_json_feed toggle added to Advanced tab SEO section with live feed URL hint. book_wp_json_feed registered in settings and tab_fields['advanced'].

= 3.4.58 =
book_wp_share_row() added to navigation.php — share strip (Copy link, X, Facebook, WhatsApp) rendered on single posts below the tags block. No third-party SDKs. Clipboard API with execCommand fallback. book_wp_share_row option added to Reading tab (default 0). bookWP.copied and bookWP.shareRow added to wp_localize_script. CSS .bwp-share block added to frontend.css with full night mode coverage.

= 3.4.57 =
book_wp_social_meta() added to actions.php on wp_head priority 5. Outputs og:type, og:title, og:description, og:url, og:site_name, og:locale, og:image (+width/height/alt when featured image exists), article:published_time, article:modified_time, article:author, article:section (literary form), article:tag (thematic tags only), twitter:card (summary_large_image or summary), twitter:title, twitter:description, twitter:image, twitter:site (resolved from book_wp_social_twitter). book_wp_seo_plugin_active() utility added — guards against output when RankMath, Yoast, AIOSEO, SEOPress, or The SEO Framework is active. book_wp_social_meta_tags toggle added to Advanced tab with live detection notice.

= 3.4.55 =
Bug fix: book_wp_social_* fields moved from 'advanced' to 'social' key in tab_fields array inside book_wp_handle_tab_save(). The save handler matches bwp_tab POST value to tab_fields key; mismatch caused redirect without saving.

= 3.4.54 =
HT-01: book_wp_hidden_tags option migrated from slug-list to term ID list. One-time migration runs on admin_init. book_wp_hidden_tag_ids() added; book_wp_hidden_tag_slugs() now resolves IDs via get_terms(). book_wp_sanitize_tag_ids() replaces book_wp_sanitize_tag_slugs() for this option. Settings UI updated to store and compare by term_id (data-id attribute added to option elements). admin-settings.js updated: selected[] stores IDs; toggleId() replaces toggleSlug(); pill remove button uses data-id.
HT-02: book_wp_hide_from_category_archive() renamed to book_wp_hide_from_public_queries(). Coverage expanded to is_home(), is_date(), is_author(), is_feed(), and is_tag() (non-hidden tag archives only; matching hidden-tag archive passes through).
HT-03: book_wp_hide_from_widget_queries() added — filters secondary WP_Query instances during dynamic_sidebar() action.
HT-04: book_wp_hide_from_rest_query() added — filters REST API post queries for unauthenticated/non-editor requests via rest_post_query.
HT-05: book_wp_hide_from_sitemap() added — filters wp_sitemaps_posts_query_args to exclude hidden-tagged posts from WP core XML sitemap.
HT-06: book_wp_hide_tags_from_cloud() added — filters widget_tag_cloud_args to suppress hidden tags from the Tag Cloud widget.
HT-07: book_wp_suppress_hidden_tags_from_output() added in filters.php — filters the_tags output to remove hidden tag links from any template or widget that calls the_tags() or get_the_tag_list().

= 3.4.53 =
W-02: role=tab/aria-selected removed from admin settings tabs; replaced with aria-current=page. W-03: min-width/height 24px on .bwp-social__toggle. W-04: padding-bottom: 4em on .bwp-site-footer__inner. W-05: aria-hidden removed from required-field note asterisk. W-07: role=combobox + aria-expanded + aria-controls + aria-activedescendant added to tag-select. W-09: underline added to .bwp-chapter-blurb a, .bwp-book-blurb a, .bwp-footer-widget a.

= 3.4.52 =
W-01: nav sub-menu :focus-within added alongside :hover so keyboard users can access child items. W-06: 404 page heading changed from h2 to h1. W-08: pagination prev_text/next_text wrapped in aria-hidden arrow + screen-reader-text span in index.php, archive.php, tag.php, search.php.

= 3.4.51 =
CWV-03: animation moved from body to .bwp-page-wrap — body opacity:0 was delaying LCP reporting. CWV-04: footer logo max-height replaced with height so intrinsic dimensions are preserved before image loads. CWV-05: reading memory restore changed from setTimeout to synchronous call with history.scrollRestoration='manual'. CWV-06: str_word_count() cached to post meta book_wp_word_count on save_post; structured data reads from cache. CWV-07: decoding=async added to TOC thumbnails.

= 3.4.50 =
CWV-01: @import Google Fonts removed from frontend.css; fonts registered via wp_enqueue_style() for parallel loading; preconnect hints now unconditional. CWV-02: aspect-ratio on .bwp-featured-image corrected to 2/1 to match book-wp-cover 800x400 crop.

= 3.4.49 =
nofollow removed from social link rel attribute. rel reverts to "noopener noreferrer me".

= 3.4.48 =
Social platforms expanded to 17 — added Goodreads, WorldCat, LOC, NLB Singapore, PNM, ORCID, ISNI. rel="nofollow" added explicitly to all social links.

= 3.4.47 =
Social icons collapse toggle — BWP_SOCIAL_LIMIT=6; JS injects +N button when overflow exists; CSS .bwp-social--collapsible/.bwp-social--expanded pair handles show/hide; aria-expanded updated on toggle; graceful no-JS degradation.

= 3.4.46 =
Social profile icons in footer. New inc/helpers/social.php with 10 platforms (Twitter/X, Facebook, Instagram, Threads, LinkedIn, YouTube, Medium, Wikipedia, GitHub, RSS). New Social tab in settings. URL fields sanitised with esc_url_raw. RSS falls back to get_feed_link(). Circular icon buttons with night-mode overrides.

= 3.4.45 =
--bwp-radius token set to 6px; propagates to thumbnails, inputs, buttons, tooltips. Featured image border-radius pinned explicitly at 6px. Form badges changed from border-radius: 0 to pill shape (2em).

= 3.4.44 =
Post format support declared (aside, image). Customizer selective-refresh-widgets support added. RTL stylesheet and block patterns documented as KIV roadmap entries in upgrading.md.

= 3.4.42 =
Comment textarea placeholder updated to a poetic Malay phrase. Translation files updated.

= 3.4.41 =
Bugfix: \\u2026 escape in comment textarea and admin tag selector placeholder rendering as literal text instead of ellipsis character.

= 3.4.40 =
Root folder cleanup — config/ directory added, release checklist merged into upgrading.md, empty template-parts removed, package.json version corrected.

= 3.4.39 =
Logo moved to site footer — removed from header, added above colophon block in footer.

= 3.4.38 =
Per-form erotika geometry overrides; readme.txt features, installation, and FAQ review.

= 3.4.28 =
Documentation audit — readme.txt, changelog.md and upgrading.md congruence fixes; version fields, PHP requirement, directory listing, and duplicate entries corrected.

= 3.4.27 =
Archive layout and TOC flat-list — Masonry, Infinite Scroll, featured thumbnails; four JS bug fixes.

= 3.4.23 =
en-US standardisation: template-kolofon.php → template-colophon.php, template-mukadimah.php → template-foreword.php. CSS classes bwp-kolofon, bwp-mukadimah, bwp-nota-penulis, bwp-petikan-daripada renamed. All bilingual option strings stripped to English; 61 ms-MY .po entries added.

= 3.4.22 =
Compact analytics mode for Puisi, Syair, Karmina — tighter gap and smaller font-size so all six analytics items fit on one line at narrow column widths.

= 3.4.21 =
Author's Note repositioned — full-width banner at page top between analytics bar and content.

= 3.4.19 =
Hikayat chrome centred (title, analytics, ornament, colophon). Citation label ("Cited from") opt-in via meta box checkbox. Prose body stays left.

= 3.4.18 =
Karmina page wrap widened; Nukilan column widened and title italic removed.

= 3.4.15 =
Drop cap fonts — IM Fell English SC (hikayat), Libre Caslon Display (nukilan).

= 3.4.13 =
All 10 literary form headers standardised — two-group alignment system, Cinzel italic abolished, per-form size scale, analytics bar alignment corrected.

= 3.4.12 =
Comments section redesigned: full custom markup for comment list and comment form.

= 3.4.11 =
Gutenberg-native shortcode support: book-wp/term Format API, book-wp/quote, book-wp/divider, book-wp/verse blocks registered.

= 3.4.10 =
navigation.php audit — 6 fixes: stale docblock, $used[] consistency, prev/next skips hidden posts, fetch buffer scaled, N+1 query eliminated, $filled counter.

= 3.4.9 =
Hidden-tagged posts excluded from Related Works section.

= 3.4.8 =
Search results page redesigned to match tag archive.

= 3.4.7 =
Tag cap raised 3→5; tag archive redesign; maintenance fixes; translation sync — 12 new strings.

= 3.4.6 =
Tag support: display on single posts, TOC by-tag grouping, tag archive, hidden-post feature, 5-tag cap, admin widget.

= 3.4.5 =
Compliance audit: licence corrected to GPL-2.0-or-later; CRLF normalised; capability scoped to edit_theme_options; WCAG link underlines; readme expanded.

= 3.4.4 =
Copyright field changed to textarea with HTML support. Show Copyright toggle added.

= 3.4.3 =
Footer redesign: colophon layout, widget search styled, search button translatable.

= 3.4 =
Critical bugfix: 9 orphaned PHP concatenation lines in colophon.php causing fatal errors.

= 3.3.59 =
Ratapan chapter layout: CSS Grid replaced with float-based biblical layout matching ESV/NIV style.

= 3.3.58 =
Bug fix: unguarded light palette overrides in Ratapan, Hikayat, Memoir form CSS causing dark text on dark background in all three Night mechanisms (bwp-night, bwp-scheme-night, bwp-time-night). Consolidated from 3.3.57, 3.3.58.

= 3.3.56 =
i18n: Complete translation audit. 152 entries per locale, 0 empty msgstr. 74 meta-box labels, 22 toggle labels/descs, 7 structural strings, 4 taxonomy strings added. 7 orphaned entries removed.

= 3.3.55 =
UX: Colour Scheme and Time-of-Day merged into single Reading Palette section with paper/ink sublabels.

= 3.3.54 =
Fix: scheme and time-of-day palette systems now complement via token ownership split (time owns ink on html, scheme owns surface on body). Scheme enqueue dependency updated.

= 3.3.53 =
Security: sanitize_key() applied to five options used as CSS class suffixes. Applied on read in body_class filter as well.

= 3.3.52 =
CI pipeline added (phpcs/WordPress-Core, eslint, stylelint, php-compat). Stale book-wp-sastera enqueue handle renamed to book-wp-literary. JS version headers updated.

= 3.3.51 =
Dual translation system eliminated. strings.php deleted. All 40+ front-end strings migrated to WordPress gettext. book_wp_form_label() added. ms_MY.po and book-wp.pot created.

= 3.3.50 =
Security: REST meta exposure closed, CSS sanitiser corrected, path whitelists enforced, shortcode numerics cast. Performance: 5 time-of-day CSS files merged into one. TOC transient cache added. Chapter block/shortcode HTML unified. Inline styles removed from blocks.js.

= 3.3.49 =
Security hardening (ABSPATH guards, search escaping, CSS sanitiser), night scheme token fixes, three responsive breakpoints, print stylesheet improvements.

= 3.3.43 =
Fix: analytics bar 0 words/lines on Ratapan posts — chapter verse content extracted from Gutenberg block JSON. Isaiah-style verse layout: each verse on own line, bold inline verse number prefix. Consolidated from multiple 3.3.43 patches.

= 3.3.42 =
Feature: Bible-style chapter/verse apparatus for Ratapan. book-wp/chapter Gutenberg block (RichText, one paragraph per verse). [bwp_chapter] and [bwp_v] shortcodes. Chapter numeral floats left, verses flow as continuous prose with superscript numbers.

= 3.3.31 =
Fix: colophon ornament always centred (was inheriting form left-align). Redundant border-top removed from colophon in all 10 form files. Colophon ornament enlarged as deliberate terminus mark.

= 3.3.30 =
Poetry typography system: ragged-right, 32rem column, karmina centred, syair indented, independent line height and stanza spacing settings, stanza numbering extended to all 4 forms.

= 3.3.29 =
TOC font hierarchy consolidated from 3.3.24, 3.3.28, 3.3.29: .bwp-toc-header 1.5rem, .bwp-toc-group__heading 1rem.

= 3.3.27 =
Drop cap overhauled: per-form settings replace global toggle. Per-post force on/off override added. Bug fixed: syair and karmina incorrectly receiving drop caps.

= 3.3.26 =
BREAKING: Shortcodes renamed to English with bwp_ prefix. Old names (petikan, pemisah, istilah, bait) no longer registered. Database find-and-replace required for existing content.

= 3.3.25 =
px/rem convention enforced. 38 fixes in admin.css. All other CSS files were already conformant.

= 3.3.23 =
Masthead navigation consolidated from 3.3.21, 3.3.22, 3.3.23: author name at Cinzel display scale, primary nav strip with 3px/4px rules, footer nav location added.

= 3.3.20 =
Block editor bridge: theme.json (14 colour tokens, 3 font families, content width). editor.css for Gutenberg canvas. FSE migration plan documented in upgrading.md.

= 3.3.19 =
Added Spanish (es_ES) and French (fr_FR) translation support. 17 gettext strings + 37 runtime UI strings per locale.

= 3.3.18 =
Refactored inc/helpers/template-tags.php (43K) into five focused files: strings.php, literary.php, analytics.php, colophon.php, navigation.php.

= 3.3.17 =
Colophon dateline aligned with analytics bar: Jost uppercase, .09em tracking, .75em, --bwp-muted.

= 3.3.16 =
Fixed time-of-day visitor clock not applying. Time class stays on html via :root.bwp-time-* selectors — eliminated conflict with WP admin bar script timing.

= 3.3.15 =
Visual selector grids (Font Family, Colour Scheme, Ornament) changed from auto-fill 3-column to fixed 4-column.

= 3.3.14 =
Reverted 3.3.12 analytics bar redesign. Original outlined badge, Jost uppercase stats, and hairline borders restored.

= 3.3.13 =
Chapter Blurb theme option removed. Header uses native WordPress Category Description of first assigned category.

= 3.3.12 =
Analytics bar and badge redesigned. --bwp-border-ink contrast audit — all nine scheme/time combinations now pass WCAG 1.4.11.

= 3.3.11 =
Fixed stale pre-3.2.0 header gradient backgrounds in ratapan.css, hikayat.css, memoir.css.

= 3.3.10 =
Colour scheme and time-of-day CSS consistency pass. Badge visibility on dark themes, --bwp-stone, --bwp-border-faint, --bwp-teal, --bwp-accent all corrected.

= 3.3.9 =
Added upgrading.md — Booklit fork checklist and future upgrade plans.

= 3.3.8 =
Renamed sastera-theme.css to literary.css.

= 3.3.7 =
CSS consistency pass across all 10 form stylesheets. Analytics visibility standardised. Duplicate colophon definitions removed from frontend.css.

= 3.3.6 =
Added translation files (book-wp.pot, ms_MY.po/mo). BOOK_WP_VERSION now reads dynamically from style.css.

= 3.3.5 =
WCAG 2.1 AA fixes: --bwp-ui-border token, search button contrast on dark themes, ARIA fixes.

= 3.3.4 =
Fixed autobiografi→memoir slug migration not firing on existing installs.

= 3.3.3 =
Renamed literary form "Autobiografi / Memoir" to "Memoir". Post associations migrated automatically.

= 3.3.2 =
Fixed missing default literary form terms on existing installs. Replaced one-time boolean seeder with version-string flag.

= 3.3.1 =
Updated author URL from menj.net to github.com/menj.

= 3.3.0 =
Fixed reading analytics: line count collapsing stanza lines, stanza count regex bugs.

= 3.2.9 =
Admin UI rebuilt per modern_settings_ui v1.0.0: scoped CSS, toggle switches, visual selector cards, tab icons.

= 3.2.8 =
Added Author Name setting to decouple site header from WordPress Site Title.

= 3.2.6 =
WCAG 2.1 Level AA full audit and remediation.

= 3.2.5 =
Design refinements post-overhaul: centred layout restored, TOC and badge language unified, --bwp-border-ink token added.

= 3.2.3 =
Admin made English-only. Custom language setting removed — front-end follows WordPress site locale. Verse spacing fixed.

= 3.2.0 =
Minimalist-modern visual overhaul. Decorative apparatus removed. EB Garamond + Cinzel typography. Sajak dissolved into Puisi.

= 3.1.0 =
Settings data-loss bug fixed. inc/ restructured. functions.php reduced to 39-line bootstrap loader.

= 3.0.0 =
Complete sastera rebuild. Anthology organisation, literary metadata, SVG ornaments, reading analytics, night mode, bilingual interface, JSON-LD structured data.

= 2.1.0 =
Modernised admin, ornament system, bilingual framework.

= 2.0.0 =
Rewrite as literary theme with custom taxonomy and colour schemes.

= 1.0.0 =
Initial release.

== Credits ==

Author: MENJ — https://github.com/menj
Licence: GPL-2.0-or-later
