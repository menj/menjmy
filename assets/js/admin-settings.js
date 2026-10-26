/**
 * Book WP — Admin Settings JS
 *
 * Handles the tag multi-select dropdown widget on the Theme Settings page.
 * No external dependencies. Scoped entirely to .bwp-tag-select elements.
 *
 * Storage format: comma-separated term IDs (since v3.4.54).
 * Each option element carries data-id (term ID, stored) and data-slug
 * (used only for the aria-activedescendant DOM id reference).
 *
 * @package Book_WP
 * @version 3.4.54
 */

( function () {
    'use strict';

    /**
     * Initialise all .bwp-tag-select widgets on the page.
     */
    function initTagSelects() {
        document.querySelectorAll( '.bwp-tag-select' ).forEach( initOne );
    }

    /**
     * Initialise a single tag-select widget.
     *
     * @param {HTMLElement} widget
     */
    function initOne( widget ) {
        var box      = widget.querySelector( '.bwp-tag-select__box' );
        var input    = widget.querySelector( '.bwp-tag-select__input' );
        var dropdown = widget.querySelector( '.bwp-tag-select__dropdown' );
        var hidden   = widget.querySelector( '.bwp-tag-select__value' );
        var options  = Array.from( widget.querySelectorAll( '.bwp-tag-select__option' ) );

        // ── State ──────────────────────────────────────────────────────────
        var selected = [];         // array of term ID strings
        var focused  = -1;         // keyboard-focused option index

        // Seed from hidden input (page reload with saved value)
        if ( hidden.value ) {
            selected = hidden.value.split( ',' ).map( function ( s ) {
                return s.trim();
            } ).filter( Boolean );
        }

        renderPills();
        renderOptions( '' );

        // ── Open / close ───────────────────────────────────────────────────

        function open() {
            widget.classList.add( 'is-open' );
            input.setAttribute( 'aria-expanded', 'true' ); // W-07
            input.focus();
            renderOptions( input.value );
            focused = -1;
        }

        function close() {
            widget.classList.remove( 'is-open' );
            input.setAttribute( 'aria-expanded', 'false' ); // W-07
            input.setAttribute( 'aria-activedescendant', '' );  // W-07
            input.value = '';
            renderOptions( '' );
            focused = -1;
        }

        // Toggle on box click (but not on pill-remove buttons)
        box.addEventListener( 'mousedown', function ( e ) {
            if ( e.target.closest( '.bwp-tag-select__pill-remove' ) ) return;
            e.preventDefault();
            if ( widget.classList.contains( 'is-open' ) ) {
                close();
            } else {
                open();
            }
        } );

        // Close on outside click
        document.addEventListener( 'mousedown', function ( e ) {
            if ( ! widget.contains( e.target ) ) close();
        } );

        // ── Search filter ──────────────────────────────────────────────────

        input.addEventListener( 'input', function () {
            if ( ! widget.classList.contains( 'is-open' ) ) {
                widget.classList.add( 'is-open' );
            }
            renderOptions( input.value );
            focused = -1;
        } );

        // ── Keyboard navigation ────────────────────────────────────────────

        input.addEventListener( 'keydown', function ( e ) {
            var visible = getVisible();

            if ( e.key === 'ArrowDown' ) {
                e.preventDefault();
                if ( ! widget.classList.contains( 'is-open' ) ) { open(); return; }
                focused = Math.min( focused + 1, visible.length - 1 );
                highlightFocused( visible );

            } else if ( e.key === 'ArrowUp' ) {
                e.preventDefault();
                focused = Math.max( focused - 1, 0 );
                highlightFocused( visible );

            } else if ( e.key === 'Enter' ) {
                e.preventDefault();
                if ( focused >= 0 && visible[ focused ] ) {
                    toggleId( visible[ focused ].dataset.id );
                }

            } else if ( e.key === 'Escape' ) {
                close();

            } else if ( e.key === 'Backspace' && input.value === '' && selected.length ) {
                // Remove last pill on backspace with empty input
                selected.pop();
                syncHidden();
                renderPills();
                renderOptions( '' );
            }
        } );

        // ── Option click ───────────────────────────────────────────────────

        dropdown.addEventListener( 'mousedown', function ( e ) {
            e.preventDefault();
            var opt = e.target.closest( '.bwp-tag-select__option' );
            if ( opt ) toggleId( opt.dataset.id );
        } );

        // ── Pill remove ────────────────────────────────────────────────────

        box.addEventListener( 'click', function ( e ) {
            var btn = e.target.closest( '.bwp-tag-select__pill-remove' );
            if ( btn ) {
                e.stopPropagation();
                var id = btn.dataset.id;
                selected = selected.filter( function ( s ) { return s !== id; } );
                syncHidden();
                renderPills();
                renderOptions( input.value );
            }
        } );

        // ── Helpers ────────────────────────────────────────────────────────

        function toggleId( id ) {
            if ( selected.indexOf( id ) > -1 ) {
                selected = selected.filter( function ( s ) { return s !== id; } );
            } else {
                selected.push( id );
            }
            syncHidden();
            renderPills();
            renderOptions( input.value );
            input.focus();
        }

        function syncHidden() {
            hidden.value = selected.join( ',' );
        }

        function renderPills() {
            // Remove existing pills (not the input)
            box.querySelectorAll( '.bwp-tag-select__pill' ).forEach( function ( el ) {
                el.remove();
            } );

            selected.forEach( function ( id ) {
                var opt = options.find( function ( o ) { return o.dataset.id === id; } );
                if ( ! opt ) return;
                var label = opt.dataset.label;

                var pill = document.createElement( 'span' );
                pill.className = 'bwp-tag-select__pill';
                pill.title = label;

                var text = document.createElement( 'span' );
                text.textContent = label;

                var btn = document.createElement( 'button' );
                btn.type = 'button';
                btn.className = 'bwp-tag-select__pill-remove';
                btn.dataset.id = id;
                btn.setAttribute( 'aria-label', 'Remove ' + label );
                btn.textContent = '×';

                pill.appendChild( text );
                pill.appendChild( btn );
                box.insertBefore( pill, input );
            } );

            // Update placeholder visibility
            input.placeholder = selected.length ? '' : 'Select tags\u2026';
        }

        function renderOptions( query ) {
            var q = query.toLowerCase().trim();
            var hasVisible = false;

            options.forEach( function ( opt ) {
                var match = ! q || opt.dataset.label.toLowerCase().indexOf( q ) > -1;
                opt.style.display = match ? '' : 'none';
                if ( match ) hasVisible = true;

                // Mark selected state (keyed on term ID)
                if ( selected.indexOf( opt.dataset.id ) > -1 ) {
                    opt.classList.add( 'is-selected' );
                } else {
                    opt.classList.remove( 'is-selected' );
                }
                opt.classList.remove( 'is-focused' );
            } );

            // Empty state
            var empty = dropdown.querySelector( '.bwp-tag-select__empty' );
            if ( ! hasVisible ) {
                if ( ! empty ) {
                    empty = document.createElement( 'div' );
                    empty.className = 'bwp-tag-select__empty';
                    empty.textContent = 'No tags found.';
                    dropdown.appendChild( empty );
                }
                empty.style.display = '';
            } else if ( empty ) {
                empty.style.display = 'none';
            }

            focused = -1;
        }

        function getVisible() {
            return options.filter( function ( o ) { return o.style.display !== 'none'; } );
        }

        function highlightFocused( visible ) {
            options.forEach( function ( o ) { o.classList.remove( 'is-focused' ); } );
            if ( visible[ focused ] ) {
                visible[ focused ].classList.add( 'is-focused' );
                visible[ focused ].scrollIntoView( { block: 'nearest' } );
                // Expose focused option to AT via aria-activedescendant. (W-07 / SC 4.1.2)
                // The option DOM id uses the slug for readability; storage uses the term ID.
                input.setAttribute( 'aria-activedescendant', visible[ focused ].id || '' );
            } else {
                input.setAttribute( 'aria-activedescendant', '' );
            }
        }
    }

    // Boot when DOM is ready
    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initTagSelects );
    } else {
        initTagSelects();
    }

} )();
