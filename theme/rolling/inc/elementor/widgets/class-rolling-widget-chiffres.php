<?php
/**
 * Widget Rolling : Rolling_Widget_Chiffres.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Plaque des chiffres clés d'un témoignage (chiffres en verre).
 */
class Rolling_Widget_Chiffres extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-chiffres';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Chiffres clés', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-counter';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'chiffres', array( 'label' => __( 'Chiffres clés', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classes de la plaque', 'rolling' ), 'plaque-libre' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$chiffres = Rolling_Contenus::valeur( 'chiffres', $this->contenu() );
		if ( ! $chiffres ) {
			return;
		}
		echo '<div class="' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '">';
		foreach ( $chiffres as $chiffre ) {
			echo '<div>';
			echo '<div class="chiffre-verre">' . Rolling_Dynamique::chiffres_en_verre( $chiffre['valeur'] ) . '<span class="texte-masque">' . esc_html( $chiffre['valeur'] ) . '</span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- images de wp_get_attachment_image().
			echo '<span class="libelle-chiffre">' . esc_html( $chiffre['libelle'] ) . '</span>';
			echo '</div>';
		}
		echo '</div>';
	}
}
