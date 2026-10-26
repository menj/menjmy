/**
 * Book WP — Dashboard Widget: Reading Statistics
 *
 * Animates the form-distribution bar widths from 0 to their target
 * value when the widget first enters the viewport, using
 * IntersectionObserver for efficiency.
 *
 * @package Book_WP
 */

( function () {
    'use strict';

    function initBars() {
        var bars = document.querySelectorAll( '.bwp-dash__form-bar' );
        if ( ! bars.length ) return;

        // Stash target widths, then zero them out so we can animate in.
        bars.forEach( function ( bar ) {
            bar.dataset.target = bar.style.width;
            bar.style.width = '0%';
        } );

        if ( 'IntersectionObserver' in window ) {
            var widget = document.querySelector( '.bwp-dash' );
            if ( ! widget ) return;

            var observer = new IntersectionObserver( function ( entries ) {
                entries.forEach( function ( entry ) {
                    if ( entry.isIntersecting ) {
                        revealBars( bars );
                        observer.disconnect();
                    }
                } );
            }, { threshold: 0.2 } );

            observer.observe( widget );
        } else {
            // Fallback: animate immediately.
            revealBars( bars );
        }
    }

    function revealBars( bars ) {
        bars.forEach( function ( bar, i ) {
            setTimeout( function () {
                bar.style.width = bar.dataset.target;
            }, i * 60 );
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', initBars );
    } else {
        initBars();
    }
} )();
