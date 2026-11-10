<?php
/**
 * Widget Rolling : Rolling_Widget_Logos.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Logos clients (liste « Logos clients ») : une modification de la liste met à jour toutes les bandes.
 */
class Rolling_Widget_Logos extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-logos';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Logos clients', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-logo';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'logos', array( 'label' => __( 'Logos', 'rolling' ) ) );
		$this->texte( 'classe', __( 'Classes de la liste', 'rolling' ), 'logos-defilants grille-6', Controls_Manager::TEXT );
		$this->add_control(
			'aide',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Retirez « logos-defilants » pour une grille fixe. Les logos se gèrent dans Logos clients (ordre : attribut « Ordre »).', 'rolling' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$logos = get_posts(
			array(
				'post_type'      => 'rolling_logo',
				'posts_per_page' => 40,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		);
		if ( ! $logos ) {
			return;
		}
		echo '<div class="masque-bords"><div class="rl-logos ' . esc_attr( $this->get_settings_for_display( 'classe' ) ) . '">';
		foreach ( $logos as $logo ) {
			$hauteur = (int) Rolling_Contenus::valeur( 'hauteur', $logo->ID );
			echo '<div class="carte-verre tuile-logo">' . get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de WordPress.
				$logo->ID,
				'medium',
				array(
					'alt'     => get_the_title( $logo ),
					'loading' => 'lazy',
					'style'   => 'height:' . ( $hauteur ? $hauteur : 32 ) . 'px',
				)
			) . '</div>';
		}
		echo '</div></div>';
	}
}
