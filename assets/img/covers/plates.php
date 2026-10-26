<?php
/**
 * Bundled cover plates for strand (jalur) and form (bentuk) archives.
 * Keyed by taxonomy, then term slug. Each file has an 800px "-sm" companion;
 * sources and licences are in CREDITS.txt. Filter `book_wp_cover_plates`
 * to extend or replace entries; a term's own Cover image field overrides.
 */
$c = 'https://commons.wikimedia.org/wiki/File:';
return array(
    'literary_form' => array(
        'puisi'      => array( 'file' => 'form-puisi.jpg',      'credit' => 'Gurindam Dua Belas, Raja Ali Haji, cetakan litograf · Wikimedia Commons · domain awam', 'url' => $c . 'Gurindam_12.jpg' ),
        'syair'      => array( 'file' => 'form-syair.jpg',      'credit' => 'Syair Perahu, naskhah kulit kayu · Wikimedia Commons · domain awam', 'url' => $c . 'Syair_perahu.png' ),
        'karmina'    => array( 'file' => 'form-karmina.jpg',    'credit' => 'Surat pantun cara Lampung · Wikimedia Commons · domain awam', 'url' => $c . 'Surat_pantun_cara_Lampung.png' ),
        'hikayat'    => array( 'file' => 'form-hikayat.jpg',    'credit' => 'Hikayat Merong Mahawangsa, naskhah Wilkinson 1898 · Wikimedia Commons · domain awam', 'url' => $c . 'Hkyt_merong_mahawgsa.jpg' ),
        'nukilan'    => array( 'file' => 'form-nukilan.jpg',    'credit' => 'Taj al-Salatin, Mahkota Raja-raja, halaman akhir · Wikimedia Commons · CC0', 'url' => $c . 'Final_pages_of_the_Taj_al-Salatin,_The_Crown_of_Kings,_a_Malay_mirror_for_princes.jpg' ),
        'ratapan'    => array( 'file' => 'form-ratapan.jpg',    'credit' => 'Mawlid Sharaf al-Anam, abad ke-19, PNM MSS 819 · Wikimedia Commons · domain awam', 'url' => $c . 'Mawlid_sharaf_al-anam,_19th_century._National_Library_of_Malaysia,_MSS_819.jpg' ),
        'memoir'     => array( 'file' => 'form-memoir.jpg',     'credit' => 'Hikayat Abdullah, Abdullah Munsyi, 1843 · Wikimedia Commons · domain awam', 'url' => $c . 'AbdullahbinAbdulKadir-HikayatAbdullah-1843.jpg' ),
        'terjemahan' => array( 'file' => 'form-terjemahan.jpg', 'credit' => 'Edgar Allan Poe, daguerreotype c. 1849 · Wikimedia Commons · domain awam', 'url' => $c . 'Edgar_Allan_Poe,_circa_1849,_restored,_squared_off.jpg' ),
    ),
    'category' => array(
        'asrar'      => array( 'file' => 'jalur-asrar.jpg',      'credit' => 'Syair Perahu, Hamzah Fansuri, naskhah kulit kayu MSS Malay A2 · Wikimedia Commons · CC0', 'url' => $c . 'Syair_Perahu_MSS_Malay_A2_f1r.png' ),
        'ceritera'   => array( 'file' => 'jalur-ceritera.jpg',   'credit' => 'Naskhah Hikayat Hang Tuah · Wikimedia Commons · CC0', 'url' => $c . 'YosriHikayatHangTuah.jpg' ),
        'fiksi'      => array( 'file' => 'jalur-fiksi.jpg',      'credit' => 'Halaman depan Hikayat Sang Boma · Wikimedia Commons · CC0', 'url' => $c . 'Halaman_Depan_Hikayat_Sang_Boma.png' ),
        'iktibar'    => array( 'file' => 'jalur-iktibar.jpg',    'credit' => 'Hukum Kanun Melaka · Wikimedia Commons · CC0', 'url' => $c . 'Hukum_Kanun_Melaka.jpg' ),
        'kasmaran'   => array( 'file' => 'jalur-kasmaran.jpg',   'credit' => 'Panji Angreni, f. 10v · Wikimedia Commons · domain awam', 'url' => $c . 'Panji_Angreni_f.10v.png' ),
        'kenegaraan' => array( 'file' => 'jalur-kenegaraan.jpg', 'credit' => 'Taj al-Salatin, Mahkota Raja-raja, halaman awal · Wikimedia Commons · CC0', 'url' => $c . 'Initial_pages_of_the_Taj_al-Salatin,_‘The_Crown_of_Kings’,_a_Malay_‘mirror_for_princes’.jpg' ),
        'ketuhanan'  => array( 'file' => 'jalur-ketuhanan.jpg',  'credit' => 'Masjid Kampung Laut, Kelantan · Wikimedia Commons · CC0', 'url' => $c . 'Masjid_Kampung_Laut,_Nilampuri,_Kelantan,_Malaysia.jpg' ),
        'sanggahan'  => array( 'file' => 'jalur-sanggahan.jpg',  'credit' => 'Alamat Langkapuri, 8 Ogos 1869 · Wikimedia Commons · domain awam', 'url' => $c . 'Alamat_Langkapuri,_8_August_1869.jpg' ),
        'wangsa'     => array( 'file' => 'jalur-wangsa.jpg',     'credit' => 'Muka depan Sulalatus Salatin edisi Jawi · Wikimedia Commons · domain awam', 'url' => $c . 'Frontispiece_of_a_Jawi_edition_of_the_Malay_Annals.jpg' ),
    ),
);
