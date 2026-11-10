<?php
/**
 * Widget Rolling : Rolling_Widget_Coches.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Liste à coches (ce que nous avons produit, notre accompagnement).
 */
class Rolling_Widget_Coches extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-coches';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Liste à coches', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'coches', array( 'label' => __( 'Liste à coches', 'rolling' ) ) );
		$this->choix(
			'champ',
			__( 'Champ', 'rolling' ),
			array(
				'accompagnement' => __( 'Notre accompagnement (témoignage)', 'rolling' ),
				'produit'        => __( 'Ce que nous avons produit (réalisation)', 'rolling' ),
			)
		);
		$this->texte( 'classe_texte', __( 'Classe du texte', 'rolling' ), 'texte' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r      = $this->get_settings_for_display();
		$lignes = Rolling_Contenus::valeur( $r['champ'], $this->contenu() );
		if ( ! $lignes ) {
			return;
		}
		$coche = Rolling_Dynamique::image_du_theme( 'coche-verre', 'thumbnail', array( 'class' => 'coche' ) );
		echo '<ul class="liste-coches">';
		foreach ( $lignes as $ligne ) {
			echo '<li class="ligne-coche">' . $coche . '<span class="' . esc_attr( $r['classe_texte'] ) . '">' . esc_html( $ligne['texte'] ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image de wp_get_attachment_image().
		}
		echo '</ul>';
	}
}
