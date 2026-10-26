<?php
/**
 * Sasterawan — Literary Work Meta Boxes
 *
 * Each literary form has its own distinctive custom fields that
 * characterise it to readers — not just bibliographic data but
 * structural, compositional, and interpretive information specific
 * to each form's conventions and scholarly apparatus.
 *
 * Sections visible to all: Composition, Literary Apparatus, Display, Author's Note.
 * Form-specific sections appear only when that taxonomy term is assigned.
 *
 * @package Book_WP
 */

defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'book_wp_add_meta_boxes' );

function book_wp_add_meta_boxes() {
    add_meta_box(
        'book_wp_literary_details',
        __( 'Literary Work Details', 'book-wp' ),
        'book_wp_render_meta_box',
        'post', 'normal', 'high'
    );
}

function book_wp_render_meta_box( $post ) {
    wp_nonce_field( 'book_wp_save_meta', 'book_wp_meta_nonce' );

    // Every assigned form, not only the first: a translation is filed under
    // terjemahan *and* its native form, and get_the_terms() sorts by name, so
    // "terjemahan" is never first. Each matching group is shown and saved.
    $terms      = get_the_terms( $post->ID, 'literary_form' );
    $form_slugs = ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'slug' ) : array();
    $form_slug  = implode( ' ', $form_slugs );

    $orn_opts = array(
        ''         => '— Global default —',
        'fleuron'  => 'Fleuron',
        'asterism' => 'Asterism',
        'dinkus'   => 'Dinkus',
        'leaf'     => 'Leaf',
        'flower'   => 'Flower',
        'diamond'  => 'Diamond',
        'none'     => 'None',
    );

    $pov_opts = array(
        ''                  => '— Select —',
        'first'             => 'First person',
        'third-omniscient'  => 'Third person omniscient',
        'third-limited'     => 'Third person limited',
        'second'            => 'Second person',
        'multiple'          => 'Multiple / shifting narrators',
    );

    $completeness_opts = array(
        ''         => '— Select —',
        'complete' => 'Complete',
        'extract'  => 'Extract',
        'fragment' => 'Fragment',
    );

    $mode_opts = array(
        ''             => '— Select —',
        'lyrical'      => 'Lyrical',
        'political'    => 'Political',
        'confessional' => 'Confessional',
        'imagistic'    => 'Imagistic',
        'ekphrastic'   => 'Ekphrastic',
        'elegiac'      => 'Elegiac',
        'satirical'    => 'Satirical',
        'narrative'    => 'Narrative',
    );

    $lament_reg_opts = array(
        ''           => '— Select —',
        'classical'  => 'Classical elegy',
        'folk'       => 'Folk lament',
        'personal'   => 'Personal',
        'liturgical' => 'Liturgical',
        'political'  => 'Political',
    );

    $memoir_tone_opts = array(
        ''            => '— Select —',
        'reflective'  => 'Reflective',
        'elegiac'     => 'Elegiac',
        'celebratory' => 'Celebratory',
        'painful'     => 'Painful',
        'humorous'    => 'Humorous',
        'matter-of-fact' => 'Matter-of-fact',
    );

    $memory_type_opts = array(
        ''              => '— Select —',
        'direct'        => 'Direct recollection',
        'reconstructed' => 'Reconstructed',
        'composite'     => 'Composite',
        'imagined'      => 'Imaginatively filled',
    );

    $life_stage_opts = array(
        ''           => '— Select —',
        'childhood'  => 'Childhood',
        'youth'      => 'Youth',
        'young-adult'=> 'Young adult',
        'adulthood'  => 'Adulthood',
        'middle-age' => 'Middle age',
        'old-age'    => 'Old age',
    );

    $methodology_opts = array(
        ''              => '— Select —',
        'textual'       => 'Textual',
        'historical'    => 'Historical',
        'comparative'   => 'Comparative',
        'philosophical' => 'Philosophical',
        'theological'   => 'Theological',
        'sociological'  => 'Sociological',
        'biographical'  => 'Biographical',
    );

    $tale_cycle_opts = array(
        ''          => '— Select —',
        'parso-arabic'  => 'Parso-Arabic',
        'indic'         => 'Indic',
        'malay-indigenous' => 'Malay indigenous',
        'islamic'       => 'Islamic',
        'chinese'       => 'Chinese',
        'hybrid'        => 'Hybrid',
    );

    $register_opts = array(
        ''          => '— Select —',
        'courtly'   => 'Courtly',
        'formal'    => 'Formal',
        'colloquial' => 'Colloquial',
        'folk'      => 'Folk',
        'religious' => 'Religious',
    );

    $timeline_opts = array(
        ''            => '— Select —',
        'opening'     => 'Opening',
        'rising'      => 'Rising action',
        'complication'=> 'Complication',
        'climax'      => 'Climax',
        'falling'     => 'Falling action',
        'denouement'  => 'Denouement',
        'coda'        => 'Coda',
    );

    ?>
    <?php book_wp_mf_set_group( '' ); ?>
    <div class="bwp-metabox bwp-meta-root" data-form="<?php echo esc_attr( $form_slug ); ?>">

    <?php /* ════════════════════════════════════════════════════
       CORE — all forms
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section">
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Composition', 'book-wp' ); ?></h4>
        <?php book_wp_mf_text( $post, 'book_wp_date_composed', __( 'Date Composed', 'book-wp' ), 'e.g. 3 Ramadan 1446 &middot; Mac 2025 &middot; Circa 1880' ); ?>
        <?php book_wp_mf_text( $post, 'book_wp_location', __( 'Location', 'book-wp' ), 'e.g. Cyberjaya &middot; Masjid Negara, KL &middot; Makkah al-Mukarramah' ); ?>
        <?php book_wp_mf_text( $post, 'book_wp_year', __( 'Year (for sorting)', 'book-wp' ), 'Numeric year &mdash; used for TOC sorting.' ); ?>
    </div>

    <div class="bwp-mf-section">
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Literary Apparatus', 'book-wp' ); ?></h4>
        <?php book_wp_mf_textarea( $post, 'book_wp_epigraph', __( 'Epigraph', 'book-wp' ), 'Opening verse or quotation before the work.' ); ?>
        <?php book_wp_mf_text( $post, 'book_wp_epigraph_source', 'Epigraph Source', '&mdash; al-Mutanabbi &middot; Surah Al-Baqarah 2:183' ); ?>
        <?php book_wp_mf_text( $post, 'book_wp_dedication', __( 'Dedication', 'book-wp' ), '"For my beloved mother"' ); ?>
        <?php book_wp_mf_text( $post, 'book_wp_source', __( 'Source', 'book-wp' ), 'Collection, journal, or publisher.' ); ?>
        <?php book_wp_mf_text( $post, 'book_wp_original_lang', __( 'Original Language', 'book-wp' ), 'Fill only if this is a translation.' ); ?>
    </div>

    <div class="bwp-mf-section">
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Display', 'book-wp' ); ?></h4>
        <?php book_wp_mf_text( $post, 'book_wp_toc_excerpt', 'TOC Description', 'Brief subtitle shown in Table of Contents.' ); ?>
        <?php book_wp_mf_select( $post, 'book_wp_ornament', __( 'Ornament Override', 'book-wp' ), 'Override the global ornament for this work.', $orn_opts ); ?>
        <?php book_wp_mf_select( $post, 'book_wp_drop_cap_override', __( 'Drop Cap Override', 'book-wp' ), 'Override the per-form drop cap setting for this work only.', array(
            ''  => '— Follow form setting —',
            '1' => 'Force on',
            '0' => 'Force off',
        ) ); ?>
    </div>

    <div class="bwp-mf-section">
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Author\'s Note', 'book-wp' ); ?></h4>
        <?php book_wp_mf_textarea( $post, 'book_wp_notes', __( "Author's Note", 'book-wp' ), 'Commentary, context, or background for this work.' ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       SYAIR — composed by you; metre, irama, occasion matter
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="syair"><?php book_wp_mf_set_group( 'syair' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Syair — Composition Details', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_narrative_subject',
            __( 'Narrative Subject', 'book-wp' ),
            'What story or teaching does this syair carry? One or two sentences in your own words.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_metre',
            __( 'Metre', 'book-wp' ),
            'e.g. 4 lines, 8&ndash;12 syllables, rhyme aaaa &middot; or describe your own variation.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_melody',
            __( 'Melody (if set to music)', 'book-wp' ),
            'Lagu or irama tradition this syair is composed for or set to, if any.'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_completeness',
            __( 'Completeness', 'book-wp' ),
            'Is this the full syair or an extract posted here?',
            $completeness_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_canto_count',
            __( 'Number of Cantos', 'book-wp' ),
            'How many cantos or sections this syair contains.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_occasion',
            __( 'Occasion', 'book-wp' ),
            'Was this composed for a specific event, person, or occasion?'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_inspiration',
            __( 'Inspiration', 'book-wp' ),
            'What sparked this syair? A memory, a text, a moment — in your own words.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       KARMINA — structural; sampiran/isi labelling teaches readers the form
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="karmina"><?php book_wp_mf_set_group( 'karmina' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Karmina — Pantun Structure', 'book-wp' ); ?></h4>

        <p class="bwp-mf-intro">
            Isi medan ini akan dipaparkan berlabel kepada pembaca &mdash;
            mendidik mereka tentang struktur <em>sampiran</em> (pembayang) dan <em>isi</em> (makna)
            yang menjadi teras karmina.
        </p>

        <?php book_wp_mf_text( $post, 'book_wp_sampiran',
            __( 'Sampiran (Line 1 — Image)', 'book-wp' ),
            'The first line: the image or hook.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_isi',
            __( 'Isi (Line 2 — Meaning)', 'book-wp' ),
            'The second line: the meaning or lesson.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_rhyme_scheme',
            __( 'Rhyme Scheme', 'book-wp' ),
            'e.g. aa &middot; ab'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_register',
            __( 'Register', 'book-wp' ),
            'The social register of the language.',
            $register_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_occasion',
            __( 'Occasion', 'book-wp' ),
            'Situation or event this karmina was composed for.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_inspiration',
            __( 'Inspiration', 'book-wp' ),
            'What prompted this particular karmina?'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       HIKAYAT — you are presenting a passage from a classical text;
                 source apparatus remains fully bibliographic
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="hikayat"><?php book_wp_mf_set_group( 'hikayat' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Hikayat — Source Text & Manuscript', 'book-wp' ); ?></h4>

        <?php
        // Citation toggle — determines whether "Cited from" label
        // appears above the title on the front end.
        $is_citation = get_post_meta( $post->ID, 'book_wp_hikayat_is_citation', true );
        ?>
        <div class="bwp-mf">
            <label style="display:flex;align-items:center;gap:.5em;cursor:pointer;font-weight:600;color:#1a1814;font-size:12px;">
                <?php $cite = book_wp_mf_field_attrs( 'book_wp_hikayat_is_citation' ); ?>
                <input type="checkbox"
                       name="<?php echo esc_attr( $cite['name'] ); ?>"
                       id="<?php echo esc_attr( $cite['id'] ); ?>"
                       value="1"
                       <?php checked( $is_citation, '1' ); ?>>
                <?php esc_html_e( 'This is a citation — show "Cited from" above the title', 'book-wp' ); ?>
            </label>
            <span class="description">
                <?php esc_html_e( 'Check this if you are presenting a passage cited from another work. Leave unchecked if this is your own writing in the hikayat form.', 'book-wp' ); ?>
            </span>
        </div>

        <?php book_wp_mf_text( $post, 'book_wp_hikayat_title',
            __( 'Hikayat Title', 'book-wp' ),
            'Full classical title: e.g. Hikayat Hang Tuah &middot; Hikayat Amir Hamzah'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_tale_cycle',
            __( 'Tale Cycle', 'book-wp' ),
            'Which storytelling tradition does this hikayat belong to?',
            $tale_cycle_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_protagonist',
            __( 'Protagonist', 'book-wp' ),
            'Main figure present or discussed in this passage.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_manuscript',
            __( 'Manuscript or Edition', 'book-wp' ),
            'e.g. Shellabear 1896 &middot; MS Raffles 18 &middot; DBP 1988 ed.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_editor_transcriber',
            __( 'Editor or Transcriber', 'book-wp' ),
            'Scholar responsible for this edition or transcription.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_folio_page',
            __( 'Folio or Page Reference', 'book-wp' ),
            'e.g. f. 12r &middot; pp. 45&ndash;47'
        ); ?>

        <?php book_wp_mf_textarea( $post, 'book_wp_annotation',
            __( 'Commentary', 'book-wp' ),
            'Your own commentary on why you selected this passage and what it illuminates.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       PUISI — general poetry fallback for your own compositions
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="puisi"><?php book_wp_mf_set_group( 'puisi' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Puisi — Free Verse & Modern Forms', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_rhyme_scheme',
            __( 'Rhyme Scheme', 'book-wp' ),
            'e.g. ABAB &middot; AABB &middot; bebas'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_poetic_mode',
            __( 'Poetic Mode', 'book-wp' ),
            'What is the dominant mode of this poem?',
            $mode_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_inspiration',
            __( 'Inspiration', 'book-wp' ),
            'What prompted this poem?'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_sequence',
            __( 'Sequence', 'book-wp' ),
            'If part of a larger cycle or sequence of poems, name it here.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_performance_history',
            __( 'Performance History', 'book-wp' ),
            'If performed: venue, event, year.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_first_published',
            __( 'First Published In', 'book-wp' ),
            'Journal, anthology, newspaper, or platform.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       CERPEN — your own short fiction
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="cerpen"><?php book_wp_mf_set_group( 'cerpen' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Cerpen — Short Fiction Details', 'book-wp' ); ?></h4>

        <?php book_wp_mf_select( $post, 'book_wp_point_of_view',
            __( 'Point of View', 'book-wp' ),
            'From whose perspective is the story told?',
            $pov_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_setting',
            __( 'Setting', 'book-wp' ),
            'Time and place of the story world. e.g. "Kampung Baru, KL, 1969" &middot; "Near-future Malaysia"'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_theme',
            __( 'Intention', 'book-wp' ),
            'What were you trying to do or say with this story? In your own words, for readers.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_inspiration',
            __( 'Inspiration', 'book-wp' ),
            'What sparked this cerpen? An event, a person, a question you needed to explore.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_first_published',
            __( 'First Published In', 'book-wp' ),
            'Journal, anthology, newspaper, or platform.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       NOVEL — chapter postings from your long work
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="novel"><?php book_wp_mf_set_group( 'novel' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Novel — Chapter Details', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_novel_title',
            __( 'Novel Title', 'book-wp' ),
            'Title of the parent novel this chapter belongs to.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_chapter_part',
            __( 'Part', 'book-wp' ),
            'e.g. Bahagian I &middot; Part Two &middot; Volume 3'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_chapter_number',
            __( 'Chapter', 'book-wp' ),
            'e.g. Chapter 3 &middot; Chapter III'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_timeline_position',
            __( 'Narrative Position', 'book-wp' ),
            'Where does this chapter fall in the overall story arc?',
            $timeline_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_genre',
            'Genre',
            'e.g. Literary fiction &middot; Historical &middot; Magical realism &middot; Thriller'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_point_of_view',
            __( 'Point of View', 'book-wp' ),
            'Narrative perspective in this chapter.',
            $pov_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_setting',
            __( 'Setting', 'book-wp' ),
            'Time and place specific to this chapter.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_theme',
            __( 'Chapter Intention', 'book-wp' ),
            'What role does this chapter play in the larger work? What does it need to accomplish?'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       NUKILAN — you selected and are presenting this passage;
                 source fields remain; annotation becomes your voice
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="nukilan"><?php book_wp_mf_set_group( 'nukilan' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Nukilan — Source Details', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_source_work_title',
            __( 'Source Work Title', 'book-wp' ),
            'Full title of the work this passage is taken from.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_source_author',
            __( 'Author of Source', 'book-wp' ),
            'Author of the source work.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_source_genre',
            __( 'Genre of Source', 'book-wp' ),
            'e.g. Novel &middot; Ceramah &middot; Surat &middot; Ucapan &middot; Risalah'
        ); ?>

        <?php book_wp_mf_textarea( $post, 'book_wp_annotation',
            __( 'My Commentary', 'book-wp' ),
            'Why you chose this passage, what it means to you, and what you want readers to notice in it.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       RATAPAN — your own lament; grief context matters for readers
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="ratapan"><?php book_wp_mf_set_group( 'ratapan' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Ratapan — Grief Context', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_occasion_of_grief',
            __( 'Occasion of Grief', 'book-wp' ),
            'What loss, sorrow, or occasion prompted this lament?'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_addressee',
            __( 'Addressee', 'book-wp' ),
            'Who or what is being mourned, addressed, or spoken to.'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_lament_register',
            __( 'Lament Register', 'book-wp' ),
            'What tradition or mode does this lament draw from?',
            $lament_reg_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_duration_composed',
            __( 'Duration of Composition', 'book-wp' ),
            'e.g. "Written in one night" &middot; "One month after the passing"'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       ESEI — scholarly/literary essays; some may cite others
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="esei"><?php book_wp_mf_set_group( 'esei' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Esei — Argument & Scope', 'book-wp' ); ?></h4>

        <?php book_wp_mf_textarea( $post, 'book_wp_thesis',
            __( 'Central Argument', 'book-wp' ),
            'Your central argument in one or two sentences. Shown to readers as a summary before the essay.'
        ); ?>

        <?php book_wp_mf_textarea( $post, 'book_wp_abstract',
            __( 'Abstract', 'book-wp' ),
            '3&ndash;5 sentences for readers deciding whether to read the full essay.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_field',
            __( 'Field', 'book-wp' ),
            'e.g. Literary criticism &middot; Islamic studies &middot; History &middot; Comparative religion'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_methodology',
            __( 'Methodology', 'book-wp' ),
            'Primary analytical approach.',
            $methodology_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_subject',
            __( 'Subject', 'book-wp' ),
            'Main subject, text, or figure under examination.'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_first_published',
            __( 'First Published In', 'book-wp' ),
            'Journal, edited volume, or conference proceedings, if applicable.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       AUTOBIOGRAFI — your own remembered life
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="memoir"><?php book_wp_mf_set_group( 'memoir' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Memoir — Memory & Context', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_period_covered',
            __( 'Period Covered', 'book-wp' ),
            'e.g. 1985&ndash;1990 &middot; School years &middot; Years in Cairo'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_key_figures',
            __( 'Key Figures', 'book-wp' ),
            'People who appear or are central to this passage: family, teachers, mentors, friends.'
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_memoir_tone',
            __( 'Tone', 'book-wp' ),
            'What is the emotional register of this passage?',
            $memoir_tone_opts
        ); ?>

        <?php book_wp_mf_select( $post, 'book_wp_memory_type',
            __( 'Nature of This Memory', 'book-wp' ),
            'How was this memory recovered? You know best.',
            $memory_type_opts
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_verification_note',
            __( 'Verification Note', 'book-wp' ),
            'Have you cross-checked this against letters, photographs, or other records? Brief note.'
        ); ?>
    </div>

    <?php /* ════════════════════════════════════════════════════
       TERJEMAHAN — works translated into Malay (Poe, etc.)
    ════════════════════════════════════════════════════ */ ?>

    <div class="bwp-mf-section bwp-mf-group" data-show-for="terjemahan"><?php book_wp_mf_set_group( 'terjemahan' ); ?>
        <h4 class="bwp-mf-heading"><?php esc_html_e( 'Terjemahan — Source Work', 'book-wp' ); ?></h4>

        <?php book_wp_mf_text( $post, 'book_wp_source_title',
            __( 'Original Title', 'book-wp' ),
            'e.g. The Raven &middot; The Tell-Tale Heart'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_source_author',
            __( 'Original Author', 'book-wp' ),
            'e.g. Edgar Allan Poe'
        ); ?>

        <?php book_wp_mf_text( $post, 'book_wp_source_year',
            __( 'Year of Original', 'book-wp' ),
            'e.g. 1845'
        ); ?>

        <?php /* Source language is the core "Original Language" field above. */ ?>
        <?php book_wp_mf_textarea( $post, 'book_wp_translator_note',
            __( "Translator's Note", 'book-wp' ),
            'Choices of rhyme, register, or what could not be carried across.'
        ); ?>
    </div>

    </div><!-- .bwp-metabox -->


    <style>
    .bwp-metabox { font-size:13px; }
    .bwp-mf-section { margin-bottom:1.4em; padding-bottom:1.2em; border-bottom:1px solid #e8e4de; }
    .bwp-mf-section:last-child { border-bottom:none; margin-bottom:0; }
    .bwp-mf-heading { margin:0 0 .7em; font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#8a7a68; font-weight:600; }
    .bwp-mf-intro { font-size:11.5px; color:#5a5048; background:#f8f4ee; border-left:3px solid #c9a84c; padding:.5em .7em; margin:0 0 .9em; line-height:1.55; }
    .bwp-mf { margin-bottom:.85em; }
    .bwp-mf label { display:block; font-weight:600; margin-bottom:.25em; color:#1a1814; font-size:12px; }
    .bwp-mf input[type=text], .bwp-mf textarea, .bwp-mf select { width:100%; box-sizing:border-box; }
    .bwp-mf textarea { resize:vertical; min-height:60px; }
    .bwp-mf .description { display:block; color:#8a7a68; font-size:11px; margin-top:.2em; font-style:italic; }
    .bwp-mf-group { display:none; }
    .bwp-mf-group.bwp-mf-visible { display:block; }
    </style>

    <script>
    ( function () {
        var box    = document.querySelector( '.bwp-metabox' );
        var groups = document.querySelectorAll( '.bwp-mf-group' );

        // `slugs` is a space-separated list of every assigned form; a group is
        // shown when any of its data-show-for slugs is among them.
        function applyForm( slugs ) {
            var assigned = ( slugs || '' ).split( /\s+/ ).filter( Boolean );
            groups.forEach( function ( g ) {
                var forForms = ( g.dataset.showFor || '' ).split( ' ' );
                var show     = forForms.some( function ( f ) { return assigned.indexOf( f ) !== -1; } );
                g.classList.toggle( 'bwp-mf-visible', show );
            } );
            if ( box ) box.dataset.form = assigned.join( ' ' );
        }

        applyForm( box ? box.dataset.form : '' );

        if ( window.wp && wp.domReady && wp.data ) {
            wp.domReady( function () {
                setTimeout( function () {
                    if ( ! wp.data.select( 'core/editor' ) ) return;
                    var prevSlug = '';
                    wp.data.subscribe( function () {
                        var editorStore = wp.data.select( 'core/editor' );
                        if ( ! editorStore ) return;
                        var termIds = editorStore.getEditedPostAttribute( 'literary_form' );
                        if ( ! termIds || ! termIds.length ) {
                            if ( prevSlug ) { prevSlug = ''; applyForm( '' ); }
                            return;
                        }
                        var allTerms = wp.data.select( 'core' ).getEntityRecords(
                            'taxonomy', 'literary_form', { include: termIds, per_page: 100 }
                        );
                        if ( ! allTerms ) return;
                        var slug = allTerms.map( function ( t ) { return t.slug; } ).sort().join( ' ' );
                        if ( slug !== prevSlug ) { prevSlug = slug; applyForm( slug ); }
                    } );
                }, 0 );
            } );
        }

        var taxDiv = document.getElementById( 'literary_formdiv' );
        if ( taxDiv ) {
            taxDiv.addEventListener( 'change', function () {
                var checked = taxDiv.querySelectorAll( 'input[type=checkbox]:checked' );
                var slugs   = [];
                checked.forEach( function ( cb ) {
                    var label = document.querySelector( 'label[for="' + cb.id + '"]' );
                    if ( label ) slugs.push( label.textContent.trim().toLowerCase().replace( /\s+/g, '-' ) );
                } );
                applyForm( slugs.join( ' ' ) );
            } );
        }
    } )();
    </script>
    <?php
}

/* ── Field helpers ─────────────────────────────────────────────────────── */

/**
 * Set the per-form group that subsequent field helpers render into.
 *
 * Several meta keys (occasion, setting, theme, …) appear in more than one
 * form group. If every group posted the same bare `name`, the browser would
 * submit all of them — hidden groups included — and PHP would keep the last
 * one, silently discarding the author's edit. Grouped fields are therefore
 * namespaced as book_wp_mf[{group}][{key}] and the save handler reads only
 * the groups that match one of the post's assigned literary forms.
 *
 * @param string $group Space-separated form slugs from data-show-for, or '' for core fields.
 */
function book_wp_mf_set_group( $group = '' ) {
    $GLOBALS['book_wp_mf_group'] = (string) $group;
}

/**
 * Resolve the submitted name and unique DOM id for a meta key in the current group.
 *
 * @param string $key Meta key.
 * @return array{name:string,id:string}
 */
function book_wp_mf_field_attrs( $key ) {
    $group = isset( $GLOBALS['book_wp_mf_group'] ) ? $GLOBALS['book_wp_mf_group'] : '';
    if ( '' === $group ) {
        return array( 'name' => $key, 'id' => $key );
    }
    $slug = sanitize_key( str_replace( ' ', '-', $group ) );
    return array(
        'name' => 'book_wp_mf[' . $group . '][' . $key . ']',
        'id'   => 'book_wp_mf_' . $slug . '_' . $key,
    );
}

function book_wp_mf_text( $post, $key, $label, $desc = '' ) {
    $val  = get_post_meta( $post->ID, $key, true );
    $attr = book_wp_mf_field_attrs( $key );
    echo '<div class="bwp-mf">';
    printf( '<label for="%s">%s</label>', esc_attr( $attr['id'] ), esc_html( $label ) );
    printf( '<input type="text" name="%s" id="%s" value="%s">', esc_attr( $attr['name'] ), esc_attr( $attr['id'] ), esc_attr( $val ) );
    if ( $desc ) printf( '<span class="description">%s</span>', wp_kses_post( $desc ) );
    echo '</div>';
}

function book_wp_mf_textarea( $post, $key, $label, $desc = '' ) {
    $val  = get_post_meta( $post->ID, $key, true );
    $attr = book_wp_mf_field_attrs( $key );
    echo '<div class="bwp-mf">';
    printf( '<label for="%s">%s</label>', esc_attr( $attr['id'] ), esc_html( $label ) );
    printf( '<textarea name="%s" id="%s" rows="3">%s</textarea>', esc_attr( $attr['name'] ), esc_attr( $attr['id'] ), esc_textarea( $val ) );
    if ( $desc ) printf( '<span class="description">%s</span>', wp_kses_post( $desc ) );
    echo '</div>';
}

function book_wp_mf_select( $post, $key, $label, $desc, $options ) {
    $val  = get_post_meta( $post->ID, $key, true );
    $attr = book_wp_mf_field_attrs( $key );
    echo '<div class="bwp-mf">';
    printf( '<label for="%s">%s</label>', esc_attr( $attr['id'] ), esc_html( $label ) );
    printf( '<select name="%s" id="%s">', esc_attr( $attr['name'] ), esc_attr( $attr['id'] ) );
    foreach ( $options as $ov => $ol ) {
        printf( '<option value="%s"%s>%s</option>', esc_attr( $ov ), selected( $val, $ov, false ), esc_html( $ol ) );
    }
    echo '</select>';
    if ( $desc ) printf( '<span class="description">%s</span>', wp_kses_post( $desc ) );
    echo '</div>';
}

/* ── Save ──────────────────────────────────────────────────────────────── */

add_action( 'save_post', 'book_wp_save_meta' );

function book_wp_save_meta( $post_id ) {
    if ( ! isset( $_POST['book_wp_meta_nonce'] )
        || ! wp_verify_nonce( $_POST['book_wp_meta_nonce'], 'book_wp_save_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $input = book_wp_mf_collect_input( $post_id );

    // Checkboxes — saved as '1' or deleted when unchecked.
    $checkboxes = array( 'book_wp_hikayat_is_citation' );
    foreach ( $checkboxes as $k ) {
        if ( ! empty( $input[ $k ] ) ) {
            update_post_meta( $post_id, $k, '1' );
        } else {
            delete_post_meta( $post_id, $k );
        }
    }

    $text_fields = array(
        // Core
        'book_wp_date_composed', 'book_wp_location', 'book_wp_year',
        'book_wp_dedication', 'book_wp_source', 'book_wp_original_lang',
        'book_wp_ornament', 'book_wp_toc_excerpt', 'book_wp_epigraph_source',
        'book_wp_drop_cap_override',
        // Verse forms — sole author
        'book_wp_occasion', 'book_wp_inspiration',
        'book_wp_narrative_subject', 'book_wp_metre', 'book_wp_melody',
        'book_wp_completeness', 'book_wp_canto_count',
        // Karmina structural labels
        'book_wp_sampiran', 'book_wp_isi', 'book_wp_rhyme_scheme', 'book_wp_register',
        // Hikayat — bibliographic source apparatus
        'book_wp_hikayat_title', 'book_wp_tale_cycle', 'book_wp_protagonist',
        'book_wp_manuscript', 'book_wp_editor_transcriber', 'book_wp_folio_page',
        // Sajak / Puisi — sole author
        'book_wp_poetic_mode', 'book_wp_sequence', 'book_wp_performance_history',
        // Cerpen / Novel — sole author
        'book_wp_point_of_view', 'book_wp_setting', 'book_wp_theme',
        'book_wp_first_published', 'book_wp_novel_title', 'book_wp_genre',
        'book_wp_timeline_position', 'book_wp_chapter_number', 'book_wp_chapter_part',
        // Nukilan — you as curator
        'book_wp_source_work_title', 'book_wp_source_author', 'book_wp_source_genre',
        // Ratapan — sole author
        'book_wp_occasion_of_grief', 'book_wp_addressee',
        'book_wp_lament_register', 'book_wp_duration_composed',
        // Esei
        'book_wp_field', 'book_wp_methodology', 'book_wp_subject',
        // Autobiografi — sole author
        'book_wp_key_figures', 'book_wp_memoir_tone',
        'book_wp_memory_type', 'book_wp_period_covered', 'book_wp_verification_note',
        // Terjemahan — source work
        'book_wp_source_title', 'book_wp_source_author', 'book_wp_source_year',
    );

    foreach ( $text_fields as $k ) {
        if ( isset( $input[ $k ] ) ) {
            update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $input[ $k ] ) ) );
        }
    }

    $textarea_fields = array(
        'book_wp_epigraph', 'book_wp_notes',
        'book_wp_thesis', 'book_wp_abstract', 'book_wp_annotation',
        'book_wp_translator_note',
    );
    foreach ( $textarea_fields as $k ) {
        if ( isset( $input[ $k ] ) ) {
            update_post_meta( $post_id, $k, sanitize_textarea_field( wp_unslash( $input[ $k ] ) ) );
        }
    }
}

/**
 * Flatten the submitted meta box fields for one post.
 *
 * Core fields post under their bare key. Per-form groups post under
 * book_wp_mf[{group}][{key}]; only the groups whose slugs include one of the
 * post's assigned literary forms are merged in, so values from hidden groups
 * can never overwrite the visible edit. Values are returned raw (still
 * slashed) and are sanitised by the caller.
 *
 * @param int $post_id Post being saved.
 * @return array<string,mixed>
 */
function book_wp_mf_collect_input( $post_id ) {
    $input = array();
    foreach ( $_POST as $k => $v ) {
        if ( is_string( $k ) && 0 === strpos( $k, 'book_wp_' ) && 'book_wp_mf' !== $k && is_scalar( $v ) ) {
            $input[ $k ] = $v;
        }
    }

    if ( empty( $_POST['book_wp_mf'] ) || ! is_array( $_POST['book_wp_mf'] ) ) {
        return $input;
    }

    $form_slugs = function_exists( 'book_wp_get_form_slugs' ) ? book_wp_get_form_slugs( $post_id ) : array();
    if ( ! $form_slugs ) {
        return $input;
    }

    foreach ( $_POST['book_wp_mf'] as $group => $fields ) {
        if ( ! is_array( $fields ) ) continue;
        $slugs = preg_split( '/\s+/', (string) $group, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! array_intersect( $form_slugs, $slugs ) ) continue;
        foreach ( $fields as $k => $v ) {
            if ( is_string( $k ) && 0 === strpos( $k, 'book_wp_' ) && is_scalar( $v ) ) {
                $input[ $k ] = $v;
            }
        }
    }
    return $input;
}
