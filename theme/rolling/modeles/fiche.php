<?php
/**
 * Fiche (réalisation, témoignage, article, 404) : affiche le modèle Elementor choisi dans
 * Apparence > Personnaliser > Rolling : modèles des fiches.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="content" class="site-main rl-fiche">
	<?php
	if ( is_404() ) {
		Rolling_Dynamique::afficher_fiche();
	} else {
		while ( have_posts() ) :
			the_post();
			Rolling_Dynamique::afficher_fiche();
		endwhile;
	}
	?>
</main>
<?php
get_footer();
