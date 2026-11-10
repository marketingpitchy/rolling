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
	if ( empty( $suivi[ $cle ]['post_id'] ) || 0 === strpos( $cle, '99-' ) ) {
		continue;
	}
	$doc     = \Elementor\Plugin::$instance->documents->get( $suivi[ $cle ]['post_id'], false );
	$contenu = array_merge( $contenu, $doc->get_elements_data() );
}

$slug = isset( $args[1] ) && $args[1] ? sanitize_title( $args[1] ) : 'essai-blocs';
$page = get_page_by_path( $slug );
$id   = $page ? $page->ID : wp_insert_post(
	array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_title'  => 'Essai ' . $slug,
		'post_name'   => $slug,
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
