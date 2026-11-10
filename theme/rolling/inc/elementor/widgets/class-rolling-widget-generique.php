<?php
/**
 * Widget Rolling : Rolling_Widget_Generique.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Générique de fin d'une réalisation.
 */
class Rolling_Widget_Generique extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-generique';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Générique', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-text-align-center';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'generique', array( 'label' => __( 'Générique', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classe de la liste', 'rolling' ), 'generique-liste' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$lignes = Rolling_Contenus::valeur( 'generique', $this->contenu() );
		if ( ! $lignes ) {
			return;
		}
		echo '<dl class="' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '">';
		foreach ( $lignes as $rang => $ligne ) {
			echo '<div style="--i:' . (int) $rang . '"><dt>' . esc_html( $ligne['role'] ) . '</dt><dd>' . esc_html( $ligne['valeur'] ) . '</dd></div>';
		}
		echo '</dl>';
	}
}
