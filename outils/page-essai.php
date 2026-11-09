<?php
/**
 * Copie de test : crée ou remplace la page « essai-blocs » avec le contenu des blocs installés.
 *   wp eval-file page-essai.php [clé1,clé2…]
 *
 * @package Rolling
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.GlobalVariablesOverride.Prohibited -- Script de construction lancé en ligne de commande (wp eval-file) sur la copie de test, jamais sur le site.

wp_set_current_user( 1 );
$cles    = isset( $args[0] ) && $args[0] ? explode( ',', $args[0] ) : array_keys( get_option( 'rolling_modeles', array() ) );
$suivi   = get_option( 'rolling_modeles', array() );
$contenu = array();

foreach ( $cles as $cle ) {
	if ( empty( $suivi[ $cle ]['post_id'] ) ) {
		continue;
	}
	$doc     = \Elementor\Plugin::$instance->documents->get( $suivi[ $cle ]['post_id'], false );
	$contenu = array_merge( $contenu, $doc->get_elements_data() );
}

$page = get_page_by_path( 'essai-blocs' );
$id   = $page ? $page->ID : wp_insert_post(
	array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => 'Essai des blocs',
		'post_name'   => 'essai-blocs',
	)
);
update_post_meta( $id, '_elementor_edit_mode', 'builder' );
update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
$doc = \Elementor\Plugin::$instance->documents->get( $id, false );
$doc->save(
	array(
		'elements' => \Elementor\Plugin::$instance->db->iterate_data(
			$contenu,
			function ( $e ) {
				$e['id'] = \Elementor\Utils::generate_random_string();
				return $e;
			}
		),
	)
);
\Elementor\Plugin::$instance->files_manager->clear_cache();
echo get_permalink( $id ) . "\n";
