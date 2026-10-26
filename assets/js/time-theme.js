/**
 * Sasterawan — Time Theme Engine
 *
 * Detects the visitor's local hour and applies a time-of-day
 * class to <html> before first paint. Admin can override via WP settings.
 *
 * Classes applied to <html>:
 *   bwp-time-dawn       04:00 – 06:59
 *   bwp-time-morning    07:00 – 11:59
 *   bwp-time-afternoon  12:00 – 16:59
 *   bwp-time-dusk       17:00 – 20:59
 *   bwp-time-night      21:00 – 03:59
 *
 * Applied to <html> (not <body>) so the class is present immediately
 * when the script runs in <head>, before <body> exists. CSS selectors
 * use :root.bwp-time-* to match.
 *
 * Live updates:
 *   - visibilitychange: re-evaluates when the user returns to the tab
 *   - Hourly interval: catches transitions on long-lived pages
 *   Both are skipped when the admin has pinned a specific period.
 *
 * Night toggle expiry:
 *   When the time engine detects a daytime period (dawn–dusk) and
 *   the manual night toggle was activated on a previous calendar date,
 *   the stale preference is cleared so the visitor is not stuck in
 *   dark mode indefinitely.
 *
 * @package Book_WP
 * @version 3.4.91
 */

( function () {
    'use strict';

    var cfg   = window.bookWP || {};
    var html  = document.documentElement;
    var NAMES = [ 'dawn', 'morning', 'afternoon', 'dusk', 'night' ];

    /* ── Period mapping ─────────────────────────────────── */

    var PERIODS = [
        { name: 'night',     start:  0, end:  3 },   /* 00:00 – 03:59 */
        { name: 'dawn',      start:  4, end:  6 },   /* 04:00 – 06:59 */
        { name: 'morning',   start:  7, end: 11 },   /* 07:00 – 11:59 */
        { name: 'afternoon', start: 12, end: 16 },   /* 12:00 – 16:59 */
        { name: 'dusk',      start: 17, end: 20 },   /* 17:00 – 20:59 */
        { name: 'night',     start: 21, end: 23 },   /* 21:00 – 23:59 */
    ];

    function getPeriodForHour( h ) {
        for ( var i = 0; i < PERIODS.length; i++ ) {
            if ( h >= PERIODS[i].start && h <= PERIODS[i].end ) {
                return PERIODS[i].name;
            }
        }
        return 'afternoon'; /* fallback */
    }

    /* ── Apply period class to <html> ──────────────────── */

    function applyPeriod( newPeriod ) {
        if ( newPeriod === window.bwpTimePeriod ) return; /* no change */
        for ( var i = 0; i < NAMES.length; i++ ) {
            html.classList.remove( 'bwp-time-' + NAMES[i] );
        }
        html.classList.add( 'bwp-time-' + newPeriod );
        window.bwpTimePeriod = newPeriod;
    }

    /* ── Night toggle expiry ───────────────────────────── */
    /* If the manual night toggle was activated on a previous
       calendar date and the current clock period is daytime
       (dawn–dusk), clear the stale preference so the visitor
       is not stuck in dark mode.                             */

    function expireStaleNightToggle( currentPeriod ) {
        if ( currentPeriod === 'night' ) return; /* still night — don't touch */
        try {
            var saved = localStorage.getItem( 'bwp_night_mode' );
            if ( saved !== '1' ) return; /* not on — nothing to expire */
            var stamp = localStorage.getItem( 'bwp_night_date' );

            /* If a date stamp exists and it matches today, the user toggled
               during the current session — honour it even in daytime.        */
            if ( stamp ) {
                var today    = new Date();
                var todayStr = today.getFullYear() + '-' + ( today.getMonth() + 1 ) + '-' + today.getDate();
                if ( stamp === todayStr ) return; /* toggled today — honour it */
            }

            /* Either no stamp (legacy toggle from before v3.4.91) or stamp
               is from a previous calendar date. Current period is daytime.
               Clear the stale preference so the visitor is not stuck.        */
            localStorage.setItem( 'bwp_night_mode', '0' );
            localStorage.removeItem( 'bwp_night_date' );
            html.classList.remove( 'bwp-night' );
            html.setAttribute( 'data-bwp-night', '0' );

            /* body.bwp-night is cleaned up later by frontend.js on
               DOMContentLoaded — at this point <body> may not exist yet. */
        } catch ( e ) { /* localStorage unavailable — silent */ }
    }

    /* ── Resolve period: admin override > auto ──────────── */

    var override = cfg.timeTheme || 'auto';
    var isAuto   = ( ! override || override === 'auto' );
    var period;

    if ( ! isAuto ) {
        /* Admin has pinned a specific period */
        period = override;
    } else {
        /* Detect from visitor's local clock */
        period = getPeriodForHour( new Date().getHours() );
    }

    /* ── Apply to <html> immediately ───────────────────── */

    for ( var i = 0; i < NAMES.length; i++ ) {
        html.classList.remove( 'bwp-time-' + NAMES[i] );
    }
    html.classList.add( 'bwp-time-' + period );
    window.bwpTimePeriod = period;

    /* Expire stale night toggle on first paint */
    expireStaleNightToggle( period );

    /* ── Live updates (auto mode only) ─────────────────── */
    /* When the admin pins a period there is nothing to update. */

    if ( isAuto ) {

        /* Re-evaluate when the visitor returns to the tab.
           Covers the common case: page loaded at 3 PM, tab
           revisited at 6 PM — period shifts from afternoon to dusk. */
        if ( typeof document.addEventListener === 'function' ) {
            document.addEventListener( 'visibilitychange', function () {
                if ( document.visibilityState === 'visible' ) {
                    var fresh = getPeriodForHour( new Date().getHours() );
                    applyPeriod( fresh );
                    expireStaleNightToggle( fresh );
                }
            } );
        }

        /* Hourly fallback — catches transitions on long-lived
           foreground tabs where visibilitychange never fires. */
        setInterval( function () {
            var fresh = getPeriodForHour( new Date().getHours() );
            applyPeriod( fresh );
            expireStaleNightToggle( fresh );
        }, 60 * 60 * 1000 );
    }

} )();
