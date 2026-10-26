/**
 * Book WP — Frontend JavaScript
 *
 * Literary reading enhancements:
 * - Reading progress bar (scroll-driven)
 * - Keyboard navigation between works (← →)
 * - Night mode toggle (persisted via localStorage, auto-expires next daytime)
 * - Scroll position memory (resume reading)
 *
 * All features gated by the bookWP settings object from wp_localize_script.
 *
 * @package Book_WP
 * @version 3.4.6
 */

( function () {
    'use strict';

    var cfg = window.bookWP || {};

    /* ── Reading Progress Bar ────────────────────────────────── */

    if ( '1' === cfg.progressBar ) {
        var bar = document.querySelector( '.bwp-progress__bar' );
        var progress = document.getElementById( 'bwpProgress' );

        if ( bar && progress ) {
            var updateProgress = function () {
                var scrollTop  = window.pageYOffset || document.documentElement.scrollTop;
                var docHeight  = document.documentElement.scrollHeight - window.innerHeight;
                var percentage = docHeight > 0 ? Math.min( ( scrollTop / docHeight ) * 100, 100 ) : 0;
                bar.style.width = percentage + '%';
                progress.setAttribute( 'aria-valuenow', Math.round( percentage ) );
            };

            window.addEventListener( 'scroll', updateProgress, { passive: true } );
            updateProgress();
        }
    }

    /* ── Keyboard Navigation (← → post nav, ↑ ↓ stanza nav) ── */

    if ( '1' === cfg.keyboardNav ) {

        // Stanza navigation state — tracks the currently focused stanza index.
        var stanzaIndex = -1;

        /**
         * Return all stanza paragraphs inside .bwp-work__content.
         * Only collects direct <p> children to avoid capturing nested markup
         * inside blockquotes or shortcode wrappers.
         */
        function getStanzas() {
            var content = document.querySelector( '.bwp-work__content' );
            if ( ! content ) return [];
            return Array.prototype.slice.call( content.querySelectorAll( 'p' ) );
        }

        /**
         * Move focus ring to a stanza by index.
         * Uses scrollIntoView({ behavior:'smooth' }) — no jQuery required.
         */
        function focusStanza( stanzas, idx ) {
            // Remove ring from previous stanza.
            stanzas.forEach( function ( p ) {
                p.classList.remove( 'bwp-stanza-focus' );
            } );

            if ( idx < 0 || idx >= stanzas.length ) return;

            stanzaIndex = idx;
            var target = stanzas[ idx ];
            target.classList.add( 'bwp-stanza-focus' );
            target.scrollIntoView( { behavior: 'smooth', block: 'center' } );
        }

        document.addEventListener( 'keydown', function ( e ) {
            // Skip if user is typing in a form field.
            var tag = ( e.target || e.srcElement ).tagName;
            if ( tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' ) return;

            // ← → — navigate between posts (prev / next work).
            var link = null;
            if ( e.key === 'ArrowLeft'  || e.keyCode === 37 ) {
                link = document.querySelector( '.bwp-work-nav__link--prev' );
            } else if ( e.key === 'ArrowRight' || e.keyCode === 39 ) {
                link = document.querySelector( '.bwp-work-nav__link--next' );
            }

            if ( link ) {
                e.preventDefault();
                window.location.href = link.href;
                return;
            }

            // ↑ ↓ — navigate between stanzas within the current work.
            var isDown = e.key === 'ArrowDown' || e.keyCode === 40;
            var isUp   = e.key === 'ArrowUp'   || e.keyCode === 38;

            if ( ! isDown && ! isUp ) return;

            var stanzas = getStanzas();
            if ( ! stanzas.length ) return;

            e.preventDefault();

            if ( isDown ) {
                // First press: jump to the first stanza from wherever the reader is.
                focusStanza( stanzas, stanzaIndex < stanzas.length - 1 ? stanzaIndex + 1 : stanzaIndex );
            } else {
                focusStanza( stanzas, stanzaIndex > 0 ? stanzaIndex - 1 : 0 );
            }
        } );
    }

    /* ── Night Mode Toggle ───────────────────────────────────── */

    if ( '1' === cfg.nightToggle ) {
        var NIGHT_KEY  = 'bwp_night_mode';
        var NIGHT_DATE = 'bwp_night_date';
        var btn  = document.getElementById( 'bwpNightToggle' );
        var html = document.documentElement;

        // time-theme.js (which runs earlier in <head>) may have already
        // expired a stale night toggle by removing .bwp-night from <html>.
        // Sync that state to <body> — if <html> still has the class the
        // toggle is active; if not it was either never set or just expired.
        var preloadNight = html.classList.contains( 'bwp-night' );
        if ( preloadNight ) {
            document.body.classList.add( 'bwp-night' );
        } else {
            // Ensure body is clean if time-theme.js expired the toggle
            document.body.classList.remove( 'bwp-night' );
        }

        // Mark explicit pref so the @media (prefers-color-scheme) CSS fallback
        // is disabled when the user has an active saved preference.
        try {
            var saved = localStorage.getItem( NIGHT_KEY );
            if ( saved !== null ) {
                html.setAttribute( 'data-bwp-night', saved === '1' ? '1' : '0' );
            }
        } catch ( e ) { /* silent */ }

        if ( btn ) {
            // Sync aria-pressed with current (possibly expired) state
            btn.setAttribute( 'aria-pressed', preloadNight ? 'true' : 'false' );

            btn.addEventListener( 'click', function () {
                var isNight = document.body.classList.toggle( 'bwp-night' );
                // Keep html class in sync for the dual-selector CSS to work
                // and for consistency if inspected via devtools.
                html.classList.toggle( 'bwp-night', isNight );
                html.setAttribute( 'data-bwp-night', isNight ? '1' : '0' );
                btn.setAttribute( 'aria-pressed', isNight ? 'true' : 'false' );
                try {
                    localStorage.setItem( NIGHT_KEY, isNight ? '1' : '0' );
                    if ( isNight ) {
                        // Store activation date so time-theme.js can expire
                        // the toggle when the next daytime period arrives.
                        var d = new Date();
                        localStorage.setItem( NIGHT_DATE,
                            d.getFullYear() + '-' + ( d.getMonth() + 1 ) + '-' + d.getDate() );
                    } else {
                        localStorage.removeItem( NIGHT_DATE );
                    }
                } catch ( e ) { /* silent */ }
            } );
        }
    }

    /* ── Reading Position Memory ─────────────────────────────── */

    if ( '1' === cfg.readingMemory ) {
        var SCROLL_PREFIX = 'bwp_scroll_';
        var path = window.location.pathname;

        // Tell the browser we are managing scroll restoration ourselves.
        // Without this, browsers restore the native scroll position on
        // back-navigation AFTER our code runs, overriding our saved pos.
        try { history.scrollRestoration = 'manual'; } catch ( e ) { /* silent */ }

        // Restore scroll position immediately — no setTimeout.
        // The previous implementation used setTimeout(..., 50) which caused
        // the page to render at scroll position 0, paint one frame, then
        // jump to the saved position 50ms later. Chrome records this as a
        // layout shift (CLS). Restoring synchronously before the first
        // meaningful paint eliminates the shift. (CWV-05)
        try {
            var saved = localStorage.getItem( SCROLL_PREFIX + path );
            if ( saved ) {
                var pos = parseInt( saved, 10 );
                if ( pos > 100 ) {
                    window.scrollTo( 0, pos );
                }
            }
        } catch ( e ) { /* silent */ }

        // Save scroll position periodically
        var saveTimer = null;
        window.addEventListener( 'scroll', function () {
            if ( saveTimer ) clearTimeout( saveTimer );
            saveTimer = setTimeout( function () {
                try {
                    localStorage.setItem(
                        SCROLL_PREFIX + path,
                        String( window.pageYOffset || document.documentElement.scrollTop )
                    );
                } catch ( e ) { /* silent */ }
            }, 300 );
        }, { passive: true } );
    }

} )();

/* ── Archive: Masonry + Infinite Scroll initialisation ──────────────────────
 *
 * Runs only on archive and tag pages (body.bwp-is-archive class added by PHP).
 * Options read from bookWP.archiveLayout and bookWP.archivePagination,
 * both set via wp_localize_script in enqueue.php.
 *
 * Masonry:        initialised after imagesLoaded confirms all card images are
 *                 ready — prevents height miscalculation on image-bearing cards.
 *                 Container: .bwp-archive-list  Items: .bwp-archive-list__item
 *
 * Infinite Scroll: intercepts the standard WordPress .page-numbers.next link
 *                 and appends the next page's .bwp-archive-list__item elements.
 *                 When Masonry is also active, each appended batch is handed to
 *                 Masonry for re-layout so gaps are filled correctly.
 *
 * @version 3.4.25
 * ─────────────────────────────────────────────────────────────────────────── */

( function () {
    'use strict';

    // BUG FIX 1: wrap in DOMContentLoaded — the script loads in footer but
    // executes before the parser has finished; querySelector returns null
    // on the archive list and everything exits silently.
    document.addEventListener( 'DOMContentLoaded', function () {

        var cfg    = window.bookWP || {};
        var layout = cfg.archiveLayout     || 'list';
        var paging = cfg.archivePagination || 'paged';

        // Only run on archive / tag pages.
        if ( ! document.body.classList.contains( 'bwp-is-archive' ) ) return;

        var container = document.querySelector( '.bwp-archive-list' );
        if ( ! container ) return;

        var msnry = null; // Masonry instance, shared between both blocks below.

        /* ── Masonry ── */
        if ( 'masonry' === layout && window.Masonry && window.imagesLoaded ) {

            // Gate on both font loading AND image loading before measuring item
            // heights. imagesLoaded alone fires immediately when there are no
            // images — but Cinzel (used for titles) loads asynchronously.
            // Without font-ready, Masonry calculates heights from the fallback
            // font; when Cinzel loads items grow taller and overlap.
            document.fonts.ready.then( function () {
                imagesLoaded( container, function () {
                    msnry = new Masonry( container, {
                        itemSelector    : '.bwp-archive-list__item',
                        columnWidth     : '.bwp-archive-list__item',
                        percentPosition : true,
                        gutter          : 0,
                    } );
                } );
            } );

        }
        // 'grid' layout is pure CSS — body.bwp-archive-grid class handles it.
        // No JS needed; Masonry instance stays null.

        /* ── Infinite Scroll ── */
        if ( 'infinite' === paging && window.InfiniteScroll ) {

            // BUG FIX 2: status element must exist in the DOM BEFORE the
            // InfiniteScroll constructor runs so the `status` option can
            // find it by selector. Inject it first.
            var statusEl = document.createElement( 'p' );
            statusEl.className = 'bwp-inf-status';
            container.parentNode.insertBefore( statusEl, container.nextSibling );

            var infScroll = new InfiniteScroll( container, {
                // BUG FIX 3: WordPress the_posts_pagination() outputs
                // <a class="next page-numbers"> — NOT .nav-links .next.
                // Correct compound selector: .page-numbers.next
                path    : '.page-numbers.next',
                append  : '.bwp-archive-list__item',
                history : 'replace',
                status  : '.bwp-inf-status',
                hideNav : '.navigation.pagination',
            } );

            infScroll.on( 'load', function ( response ) {
                // After each new page appends, re-layout Masonry if active.
                if ( msnry ) {
                    var newItems = response.querySelectorAll( '.bwp-archive-list__item' );
                    msnry.appended( newItems );
                }
            } );
        }

    } ); // end DOMContentLoaded

} )();

/* ── TOC flat-list: Masonry + Infinite Scroll initialisation ────────────────
 *
 * Runs only when body carries bwp-is-toc-flat — set by PHP body_class filter
 * when grouping option is None. Grid layout is pure CSS (body.bwp-toc-grid).
 *
 * Masonry: targets #bwp-toc-flat (.bwp-toc with id) and .bwp-toc__item.
 *          imagesLoaded gates init so item heights are accurate.
 *
 * Infinite Scroll: intercepts the paginate_links() output (.page-numbers.next)
 *                  produced by index.php when toc_pagination = infinite.
 *                  Each new page's .bwp-toc__item elements are appended;
 *                  if Masonry is also active they are re-laid-out immediately.
 *
 * @version 3.4.27
 * ─────────────────────────────────────────────────────────────────────────── */

( function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {

        var cfg    = window.bookWP || {};
        var layout = cfg.tocLayout     || 'list';
        var paging = cfg.tocPagination || 'paged';

        // Only run when grouping is None — body class set by PHP.
        if ( ! document.body.classList.contains( 'bwp-is-toc-flat' ) ) return;

        // Target the TOC list by its unique id to avoid matching grouped .bwp-toc lists.
        var container = document.getElementById( 'bwp-toc-flat' );
        if ( ! container ) return;

        var msnry = null;

        /* ── Masonry ── */
        if ( 'masonry' === layout && window.Masonry && window.imagesLoaded ) {

            // document.fonts.ready gates Masonry until Cinzel has loaded and
            // the browser has recalculated text metrics. Without this, Masonry
            // runs on fallback-font heights; wrapping titles (e.g. two-line
            // entries) grow taller after Cinzel loads and items overlap.
            document.fonts.ready.then( function () {
                imagesLoaded( container, function () {
                    msnry = new Masonry( container, {
                        itemSelector    : '.bwp-toc__item',
                        columnWidth     : '.bwp-toc__item',
                        percentPosition : true,
                        gutter          : 0,
                    } );
                } );
            } );

        }

        /* ── Infinite Scroll ── */
        if ( 'infinite' === paging && window.InfiniteScroll ) {

            // Status element must exist before InfiniteScroll constructor runs.
            var statusEl = document.createElement( 'p' );
            statusEl.className = 'bwp-inf-status';
            container.parentNode.insertBefore( statusEl, container.nextSibling );

            var infScroll = new InfiniteScroll( container, {
                path    : '.page-numbers.next',
                append  : '.bwp-toc__item',
                history : 'replace',
                status  : '.bwp-inf-status',
                hideNav : '.page-numbers',
            } );

            infScroll.on( 'load', function ( response ) {
                if ( msnry ) {
                    var newItems = response.querySelectorAll( '.bwp-toc__item' );
                    msnry.appended( newItems );
                }
            } );
        }

    } ); // end DOMContentLoaded

} )();

/* ── Social icons — collapse/expand toggle ──────────────────────────────
 *
 * If the .bwp-social list contains more than BWP_SOCIAL_LIMIT items,
 * this block:
 *   1. Adds .bwp-social--collapsible to the <ul> (CSS hides items 7+)
 *   2. Appends a <li> containing a <button class="bwp-social__toggle">
 *      showing "+N" where N = number of hidden items
 *   3. On click: toggles .bwp-social--expanded; button label becomes "×"
 *      when expanded and "+N" when collapsed
 *
 * Degrades gracefully — if JS is absent all icons are visible (no CSS
 * hiding is applied without the collapsible class).
 *
 * @version 3.4.46
 * ─────────────────────────────────────────────────────────────────────── */

( function () {
    'use strict';

    var BWP_SOCIAL_LIMIT = 6;

    document.addEventListener( 'DOMContentLoaded', function () {
        var list = document.querySelector( '.bwp-social' );
        if ( ! list ) return;

        var items = list.querySelectorAll( '.bwp-social__item' );
        var total = items.length;
        if ( total <= BWP_SOCIAL_LIMIT ) return; // nothing to collapse

        var hidden = total - BWP_SOCIAL_LIMIT;

        // Mark list as collapsible — CSS does the actual hiding
        list.classList.add( 'bwp-social--collapsible' );

        // Build toggle button wrapped in a list item
        var toggleLi  = document.createElement( 'li' );
        var toggleBtn = document.createElement( 'button' );
        toggleBtn.className  = 'bwp-social__toggle';
        toggleBtn.type       = 'button';
        toggleBtn.setAttribute( 'aria-expanded', 'false' );
        toggleBtn.setAttribute( 'aria-label', '+' + hidden + ' more social profiles' );
        toggleBtn.textContent = '+' + hidden;
        toggleLi.appendChild( toggleBtn );
        list.appendChild( toggleLi );

        toggleBtn.addEventListener( 'click', function () {
            var expanded = list.classList.toggle( 'bwp-social--expanded' );
            toggleBtn.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
            if ( expanded ) {
                toggleBtn.textContent = '×';
                toggleBtn.setAttribute( 'aria-label', 'Collapse social profiles' );
                // Move toggle to end after newly revealed items
                list.appendChild( toggleLi );
            } else {
                toggleBtn.textContent = '+' + hidden;
                toggleBtn.setAttribute( 'aria-label', '+' + hidden + ' more social profiles' );
                // Move toggle back to position 7 (after item 6)
                var seventh = items[ BWP_SOCIAL_LIMIT ]; // 0-indexed = item 7
                list.insertBefore( toggleLi, seventh );
            }
        } );

        // Initial placement — insert before item 7 (index 6)
        list.insertBefore( toggleLi, items[ BWP_SOCIAL_LIMIT ] );
    } );

} )();

// ── Share row: copy-link button ─────────────────────────────────────────
( function () {

    function markCopied( btn ) {
        // Icon-only button — no label element to update.
        // Feedback via:
        //   1. CSS class bwp-share__copy--copied (accent border + colour)
        //   2. aria-label swap for screen readers
        //   3. Small floating tooltip that appears above the button and fades out
        var origLabel = btn.getAttribute( 'aria-label' );
        var copiedMsg = ( window.bookWP && bookWP.copied ) ? bookWP.copied : 'Copied!';

        btn.classList.add( 'bwp-share__copy--copied' );
        btn.setAttribute( 'aria-label', copiedMsg );

        // Inject floating tooltip
        var tip = document.createElement( 'span' );
        tip.className   = 'bwp-copy-tip';
        tip.textContent = copiedMsg;
        tip.setAttribute( 'aria-hidden', 'true' );
        btn.appendChild( tip );

        // Force reflow so the CSS transition fires
        void tip.offsetWidth;
        tip.classList.add( 'bwp-copy-tip--visible' );

        setTimeout( function () {
            tip.classList.remove( 'bwp-copy-tip--visible' );
            // Remove after fade-out transition (200ms)
            setTimeout( function () {
                if ( tip.parentNode ) tip.parentNode.removeChild( tip );
            }, 200 );
            btn.classList.remove( 'bwp-share__copy--copied' );
            btn.setAttribute( 'aria-label', origLabel );
        }, 1800 );
    }

    document.addEventListener( 'click', function ( e ) {
        var btn = e.target.closest( '[data-bwp-copy]' );
        if ( ! btn ) return;
        var url = btn.getAttribute( 'data-bwp-copy' );
        if ( ! url ) return;

        if ( navigator.clipboard && navigator.clipboard.writeText ) {
            navigator.clipboard.writeText( url ).then( function () {
                markCopied( btn );
            } ).catch( function () {
                legacyCopy( url, btn );
            } );
        } else {
            legacyCopy( url, btn );
        }
    } );

    function legacyCopy( url, btn ) {
        var ta        = document.createElement( 'textarea' );
        ta.value      = url;
        ta.style.cssText = 'position:fixed;opacity:0;pointer-events:none;';
        document.body.appendChild( ta );
        ta.focus();
        ta.select();
        try { document.execCommand( 'copy' ); markCopied( btn ); } catch ( err ) { /* silent */ }
        document.body.removeChild( ta );
    }

} )();

/* ── Sub-menu touch support (M-12) ──────────────────────────────────────
 *
 * On touch-only devices, :hover does not fire. A tap on a parent nav item
 * that has a sub-menu would normally navigate to the parent page, making
 * the child links unreachable.
 *
 * Behaviour:
 *   - First tap on a parent with a sub-menu: reveal the sub-menu, suppress
 *     navigation.
 *   - Second tap on the same parent: navigate normally.
 *   - Tapping anywhere else: close all open sub-menus.
 *
 * Uses data-touch-open="1" attribute; the corresponding CSS rule
 * .bwp-nav__list li[data-touch-open="1"] > .sub-menu { display: block; }
 * does the actual reveal (no inline style manipulation).
 *
 * @since 3.4.61
 * ─────────────────────────────────────────────────────────────────────── */

( function () {
    'use strict';

    // Activate only on touch-capable devices.
    var isTouch = ( 'ontouchstart' in window ) || ( navigator.maxTouchPoints > 0 );
    if ( ! isTouch ) return;

    var navItems = document.querySelectorAll( '.bwp-nav__list > li' );
    if ( ! navItems.length ) return;

    navItems.forEach( function ( item ) {
        var subMenu = item.querySelector( '.sub-menu' );
        if ( ! subMenu ) return;

        var link = item.querySelector( ':scope > a' );
        if ( ! link ) return;

        link.addEventListener( 'touchend', function ( e ) {
            // If sub-menu is not yet visible, show it and suppress navigation.
            if ( item.getAttribute( 'data-touch-open' ) !== '1' ) {
                e.preventDefault();
                // Close any other open sub-menus first.
                navItems.forEach( function ( other ) {
                    if ( other !== item ) other.removeAttribute( 'data-touch-open' );
                } );
                item.setAttribute( 'data-touch-open', '1' );
            }
            // Second touchend: attribute already set — do nothing; let the
            // browser process the navigation click normally.
        } );
    } );

    // Close any open sub-menu when the user touches outside the nav.
    document.addEventListener( 'touchend', function ( e ) {
        if ( ! e.target.closest( '.bwp-nav' ) ) {
            navItems.forEach( function ( item ) {
                item.removeAttribute( 'data-touch-open' );
            } );
        }
    } );

} )();

/* ── Back to Top ─────────────────────────────────────────── */

( function () {
    'use strict';

    var cfg = window.bookWP || {};

    if ( '1' !== cfg.backToTop ) return;

    var bttBtn = document.getElementById( 'bwpBackToTop' );

    if ( ! bttBtn ) return;

    // Do not initialise on pages where Infinite Scroll is active.
    // Scrolling back to top on those pages re-triggers InfiniteScroll's
    // sentinel detection and causes duplicate page loads.
    //
    // Infinite scroll is active when:
    //   - Archive/tag page (body.bwp-is-archive) with archivePagination = 'infinite'
    //   - TOC flat list page (body.bwp-is-toc-flat) with tocPagination = 'infinite'
    var bodyClasses  = document.body.classList;
    var isInfArchive = bodyClasses.contains( 'bwp-is-archive' ) && cfg.archivePagination === 'infinite';
    var isInfToc     = bodyClasses.contains( 'bwp-is-toc-flat' ) && cfg.tocPagination === 'infinite';

    if ( isInfArchive || isInfToc ) return;

    // Remove the HTML `hidden` attribute; visibility is now CSS-driven.
    bttBtn.removeAttribute( 'hidden' );

    // Show button after the reader has scrolled 400 px down.
    var BTT_THRESHOLD = 400;

    window.addEventListener( 'scroll', function () {
        var scrolled = window.pageYOffset || document.documentElement.scrollTop;
        if ( scrolled > BTT_THRESHOLD ) {
            bttBtn.classList.add( 'is-visible' );
        } else {
            bttBtn.classList.remove( 'is-visible' );
        }
    }, { passive: true } );

    bttBtn.addEventListener( 'click', function () {
        window.scrollTo( { top: 0, behavior: 'smooth' } );
    } );

} )();
