<?php
/**
 * Template Name: Landing : logo seul, sans menu
 *
 * Page de campagne (Google Ads) : en-tête réduit au logo, pied de page simple.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="content" <?php post_class( 'site-main' ); ?>>
		<?php the_content(); ?>
	</main>
	<?php
endwhile;

get_footer();
