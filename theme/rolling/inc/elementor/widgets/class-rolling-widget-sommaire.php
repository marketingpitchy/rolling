<?php
/**
 * Widget Rolling : Rolling_Widget_Sommaire.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Sommaire de l'article : un lien par intertitre ; le chapitre lu est mis en avant.
 */
class Rolling_Widget_Sommaire extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-sommaire';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Sommaire de l’article', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'sommaire', array( 'label' => __( 'Sommaire', 'rolling' ) ) );
		$this->texte( 'libelle', __( 'Nom pour lecteur d’écran', 'rolling' ), __( 'Sommaire de l’article', 'rolling' ) );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		if ( ! preg_match_all( '#<h2[^>]*\sid="([^"]+)"[^>]*>(.*?)</h2>#is', Rolling_Widget_Contenu::contenu_article(), $titres, PREG_SET_ORDER ) ) {
			return;
		}
		echo '<nav aria-label="' . esc_attr( $this->get_settings_for_display( 'libelle' ) ) . '"><ol class="sommaire" data-rl-sommaire>';
		foreach ( $titres as $titre ) {
			echo '<li><a href="#' . esc_attr( $titre[1] ) . '">' . esc_html( wp_strip_all_tags( $titre[2] ) ) . '</a></li>';
		}
		echo '</ol></nav>';
	}
}
