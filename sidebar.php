<?php defined( 'ABSPATH' ) || exit; ?>
<?php if ( is_active_sidebar( 'sidebar-widgets' ) ) : ?>
<aside class="bwp-sidebar" role="complementary">
    <?php dynamic_sidebar( 'sidebar-widgets' ); ?>
</aside>
<?php endif; ?>
