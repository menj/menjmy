/**
 * Sasterawan — Block Editor Scripts
 *
 * Registers all custom blocks and the book-wp/term inline format type.
 *
 * Inline Format:
 *   book-wp/term — Glossary tooltip. Select any word, click the "Term
 *   Definition" button in the toolbar, enter the definition. Works inside
 *   any RichText block (Paragraph, Verse, etc.) without breaking block
 *   structure. Replaces the shortcode block workaround.
 *
 * Blocks:
 *   book-wp/chapter — Bible-style lament chapter with auto-numbered verses.
 *                     Server-side rendered (save: null).
 *   book-wp/quote   — Styled literary pull-quote with optional attribution.
 *                     Static save.
 *   book-wp/divider — Decorative ornament divider. Server-side rendered so
 *                     the SVG always reflects the current Theme Options setting.
 *   book-wp/verse   — Numbered verse block with outdented verse number.
 *                     Static save.
 *
 * Layout and typography are handled by editor.css — not inline styles.
 * This keeps blocks.js free of hardcoded font families that would not respond
 * to Theme Options → Typography font overrides.
 *
 * @package Book_WP
 * @version 3.4.26
 */

( function( blocks, blockEditor, richText, components, element, i18n ) {
    'use strict';

    var el                    = element.createElement;
    var Fragment              = element.Fragment;
    var useState              = element.useState;
    var RichText              = blockEditor.RichText;
    var InspectorControls     = blockEditor.InspectorControls;
    var RichTextToolbarButton = blockEditor.RichTextToolbarButton;
    var registerFormatType    = richText.registerFormatType;
    var applyFormat           = richText.applyFormat;
    var removeFormat          = richText.removeFormat;
    var PanelBody             = components.PanelBody;
    var TextControl           = components.TextControl;
    var SelectControl         = components.SelectControl;
    var Button                = components.Button;
    var Popover               = components.Popover;
    var __                    = i18n.__;

    /* ════════════════════════════════════════════════════════════════════════
       INLINE FORMAT — book-wp/term
       Applies .bwp-istilah to selected text with a data-definition attribute.
       A PHP filter (the_content, priority 12) hydrates this to the full
       tooltip structure on the front end.
       ════════════════════════════════════════════════════════════════════════ */

    registerFormatType( 'book-wp/term', {
        title:     __( 'Term Definition', 'book-wp' ),
        tagName:   'span',
        className: 'bwp-istilah',
        attributes: {
            // data-definition stores the definition; hydrated to aria-label +
            // tooltip span on the front end by book_wp_hydrate_term_spans().
            definition: 'data-definition',
        },

        edit: function( props ) {
            var isActive    = props.isActive;
            var value       = props.value;
            var onChange    = props.onChange;
            var activeAttrs = props.activeAttributes;

            var openState   = useState( false );
            var isOpen      = openState[0];
            var setIsOpen   = openState[1];

            var defState    = useState( '' );
            var definition  = defState[0];
            var setDef      = defState[1];

            function handleButtonClick() {
                if ( isActive ) {
                    // Remove existing format when button clicked while active.
                    onChange( removeFormat( value, 'book-wp/term' ) );
                    setIsOpen( false );
                } else {
                    // Pre-populate field if re-editing an existing term.
                    setDef( activeAttrs.definition || '' );
                    setIsOpen( ! isOpen );
                }
            }

            function handleApply() {
                if ( ! definition.trim() ) return;
                onChange( applyFormat( value, {
                    type: 'book-wp/term',
                    attributes: { definition: definition },
                } ) );
                setIsOpen( false );
                setDef( '' );
            }

            function handleKeyDown( e ) {
                if ( e.key === 'Enter' ) {
                    e.preventDefault();
                    handleApply();
                }
            }

            return el( Fragment, {},

                el( RichTextToolbarButton, {
                    icon:     'book-alt',
                    title:    isActive
                                  ? __( 'Remove Definition', 'book-wp' )
                                  : __( 'Term Definition', 'book-wp' ),
                    onClick:  handleButtonClick,
                    isActive: isActive,
                } ),

                isOpen && el( Popover, {
                    position:     'bottom center',
                    onClose:      function() { setIsOpen( false ); },
                    focusOnMount: 'firstElement',
                    className:    'bwp-term-popover',
                },
                    el( 'div', { className: 'bwp-term-popover__body' },
                        el( TextControl, {
                            label:    __( 'Definition', 'book-wp' ),
                            help:     __( 'Appears as a tooltip on hover/focus. Press Enter to apply.', 'book-wp' ),
                            value:    definition,
                            onChange: setDef,
                            onKeyDown: handleKeyDown,
                        } ),
                        el( 'div', { className: 'bwp-term-popover__actions' },
                            el( Button, {
                                variant:  'primary',
                                onClick:  handleApply,
                                disabled: ! definition.trim(),
                            }, __( 'Apply', 'book-wp' ) ),
                            el( Button, {
                                variant: 'secondary',
                                onClick: function() { setIsOpen( false ); },
                            }, __( 'Cancel', 'book-wp' ) )
                        )
                    )
                )
            );
        },
    } );

    /* ════════════════════════════════════════════════════════════════════════
       BLOCK — book-wp/quote
       Styled literary pull-quote with optional attribution (cite).
       Static save — no PHP render callback needed.
       ════════════════════════════════════════════════════════════════════════ */

    blocks.registerBlockType( 'book-wp/quote', {
        title:    __( 'Literary Quote', 'book-wp' ),
        icon:     'format-quote',
        category: 'text',
        attributes: {
            content: {
                type:     'string',
                source:   'html',
                selector: '.bwp-petikan__text',
                default:  '',
            },
            source: {
                type:    'string',
                default: '',
            },
        },

        edit: function( props ) {
            var attrs         = props.attributes;
            var setAttributes = props.setAttributes;

            return el( Fragment, {},
                el( InspectorControls, {},
                    el( PanelBody, {
                        title:       __( 'Quote Settings', 'book-wp' ),
                        initialOpen: true,
                    },
                        el( TextControl, {
                            label:    __( 'Source / Attribution', 'book-wp' ),
                            help:     __( 'Name of the author or work being quoted.', 'book-wp' ),
                            value:    attrs.source,
                            onChange: function( v ) { setAttributes( { source: v } ); },
                        } )
                    )
                ),
                el( 'blockquote', { className: 'bwp-petikan' },
                    el( RichText, {
                        tagName:     'div',
                        className:   'bwp-petikan__text',
                        value:       attrs.content,
                        placeholder: __( 'Quotation\u2026', 'book-wp' ),
                        onChange:    function( v ) { setAttributes( { content: v } ); },
                    } ),
                    attrs.source && el( 'cite', { className: 'bwp-petikan__source' },
                        '\u2014 ' + attrs.source
                    )
                )
            );
        },

        save: function( props ) {
            var attrs = props.attributes;
            return el( 'blockquote', { className: 'bwp-petikan' },
                el( RichText.Content, {
                    tagName:   'div',
                    className: 'bwp-petikan__text',
                    value:     attrs.content,
                } ),
                attrs.source
                    ? el( 'cite', { className: 'bwp-petikan__source' },
                        '\u2014 ' + attrs.source
                    )
                    : null
            );
        },
    } );

    /* ════════════════════════════════════════════════════════════════════════
       BLOCK — book-wp/divider
       Decorative ornament divider. Server-side rendered so the SVG always
       reflects the current Theme Options ornament setting.
       ════════════════════════════════════════════════════════════════════════ */

    blocks.registerBlockType( 'book-wp/divider', {
        title:    __( 'Literary Divider', 'book-wp' ),
        icon:     'minus',
        category: 'design',
        attributes: {
            style: {
                type:    'string',
                default: '',
            },
        },

        edit: function( props ) {
            var attrs         = props.attributes;
            var setAttributes = props.setAttributes;

            var ornamentOptions = [
                { label: __( 'Use global setting (Theme Options)', 'book-wp' ), value: '' },
                { label: 'Fleuron',  value: 'fleuron'  },
                { label: 'Asterism', value: 'asterism' },
                { label: 'Dinkus',   value: 'dinkus'   },
                { label: 'Dash',     value: 'dash'     },
                { label: 'Leaf',     value: 'leaf'     },
                { label: 'Flower',   value: 'flower'   },
                { label: 'Diamond',  value: 'diamond'  },
                { label: __( 'None', 'book-wp' ), value: 'none' },
            ];

            return el( Fragment, {},
                el( InspectorControls, {},
                    el( PanelBody, {
                        title:       __( 'Divider Settings', 'book-wp' ),
                        initialOpen: true,
                    },
                        el( SelectControl, {
                            label:    __( 'Ornament Style', 'book-wp' ),
                            help:     __( 'Leave blank to follow the global ornament set in Theme Options \u2192 Literary.', 'book-wp' ),
                            value:    attrs.style,
                            options:  ornamentOptions,
                            onChange: function( v ) { setAttributes( { style: v } ); },
                        } )
                    )
                ),
                // Editor canvas preview — text label since PHP SVG is not available in JS.
                el( 'div', { className: 'bwp-ornament-preview' },
                    el( 'span', { className: 'bwp-ornament-preview__label' },
                        attrs.style
                            ? attrs.style.charAt(0).toUpperCase() + attrs.style.slice(1)
                            : __( 'Global ornament', 'book-wp' )
                    )
                )
            );
        },

        // Server-side rendered — PHP book_wp_render_divider_block() handles output.
        save: function() { return null; },
    } );

    /* ════════════════════════════════════════════════════════════════════════
       BLOCK — book-wp/verse
       Numbered verse block. Verse number outdents into the left margin.
       Static save — matches PHP [verse] shortcode output exactly.
       ════════════════════════════════════════════════════════════════════════ */

    blocks.registerBlockType( 'book-wp/verse', {
        title:    __( 'Verse Block', 'book-wp' ),
        icon:     'editor-paragraph',
        category: 'text',
        attributes: {
            num: {
                type:    'string',
                default: '',
            },
            content: {
                type:     'string',
                source:   'html',
                selector: '.bwp-bait__text',
                default:  '',
            },
        },

        edit: function( props ) {
            var attrs         = props.attributes;
            var setAttributes = props.setAttributes;

            return el( Fragment, {},
                el( InspectorControls, {},
                    el( PanelBody, {
                        title:       __( 'Verse Settings', 'book-wp' ),
                        initialOpen: true,
                    },
                        el( TextControl, {
                            label:    __( 'Verse Number', 'book-wp' ),
                            help:     __( 'Optional \u2014 displayed in the left margin.', 'book-wp' ),
                            type:     'number',
                            min:      '1',
                            value:    attrs.num,
                            onChange: function( v ) { setAttributes( { num: v } ); },
                        } )
                    )
                ),
                el( 'div', { className: 'bwp-bait' },
                    attrs.num
                        ? el( 'span', {
                            className:       'bwp-bait__num',
                            contentEditable: false,
                          }, attrs.num )
                        : null,
                    el( RichText, {
                        tagName:     'div',
                        className:   'bwp-bait__text',
                        value:       attrs.content,
                        placeholder: __( 'Verse text\u2026', 'book-wp' ),
                        onChange:    function( v ) { setAttributes( { content: v } ); },
                    } )
                )
            );
        },

        save: function( props ) {
            var attrs = props.attributes;
            return el( 'div', { className: 'bwp-bait' },
                attrs.num
                    ? el( 'span', { className: 'bwp-bait__num' }, attrs.num )
                    : null,
                el( RichText.Content, {
                    tagName:   'div',
                    className: 'bwp-bait__text',
                    value:     attrs.content,
                } )
            );
        },
    } );

    /* ════════════════════════════════════════════════════════════════════════
       BLOCK — book-wp/chapter
       Bible-style lament chapter with auto-numbered verses.
       Server-side rendered — PHP book_wp_render_chapter_block() handles output.
       Input model: single RichText with multiline:'p'.
       One paragraph = one verse. Enter adds a new verse.
       ════════════════════════════════════════════════════════════════════════ */

    blocks.registerBlockType( 'book-wp/chapter', {

        edit: function ( props ) {
            var attrs         = props.attributes;
            var setAttributes = props.setAttributes;

            return el( Fragment, {},

                // Sidebar — chapter meta only
                el( InspectorControls, {},
                    el( PanelBody, {
                        title:       __( 'Chapter Settings', 'book-wp' ),
                        initialOpen: true,
                    },
                        el( TextControl, {
                            label:    __( 'Chapter Number', 'book-wp' ),
                            help:     __( 'Large numeral floated left beside verse 1.', 'book-wp' ),
                            value:    attrs.chapterNum,
                            onChange: function(v){ setAttributes({ chapterNum: v }); },
                        } ),
                        el( TextControl, {
                            label:    __( 'Chapter Title (optional)', 'book-wp' ),
                            help:     __( 'Small-caps label above the chapter.', 'book-wp' ),
                            value:    attrs.chapterTitle,
                            onChange: function(v){ setAttributes({ chapterTitle: v }); },
                        } ),
                        el( TextControl, {
                            label:    __( 'First verse number', 'book-wp' ),
                            help:     __( 'Start numbering from this value (default: 1).', 'book-wp' ),
                            value:    attrs.verseStart,
                            type:     'number',
                            min:      '1',
                            onChange: function(v){ setAttributes({ verseStart: v }); },
                        } )
                    )
                ),

                // Canvas — direct editing.
                el( 'div', { className: 'bwp-chapter' },

                    attrs.chapterTitle
                        ? el( 'span', { className: 'bwp-chapter__title' }, attrs.chapterTitle )
                        : null,

                    el( 'span', {
                        className:       'bwp-chapter__num',
                        contentEditable: false,
                    }, attrs.chapterNum || '1' ),

                    el( 'div', { className: 'bwp-chapter__content' },
                        el( RichText, {
                            tagName:     'div',
                            multiline:   'p',
                            value:       attrs.content,
                            placeholder: __( 'Type verse 1 here. Press Enter for next verse.', 'book-wp' ),
                            onChange:    function( val ) {
                                setAttributes({ content: val });
                            },
                        } )
                    )
                )
            );
        },

        // Server-side rendered.
        save: function () { return null; },

    } );

} (
    window.wp.blocks,
    window.wp.blockEditor,
    window.wp.richText,
    window.wp.components,
    window.wp.element,
    window.wp.i18n
) );
