<?php
/**
 * Page de texte (mentions légales, CGV, confidentialité). Le thème l'affiche avec le modèle Elementor
 * « Rolling · Page de texte » (Rolling_Dynamique::gabarit_fiche) ; ce fichier ne sert que si Elementor est absent.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="content" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		the_title( '<h1>', '</h1>' );
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();
