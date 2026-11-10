<?php
/**
 * Widget Rolling : Rolling_Widget_Video.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Vidéo d'une réalisation : affiche + bouton lecture ; la vidéo ne se charge qu'au clic.
 */
class Rolling_Widget_Video extends Rolling_Widget {

	/** Nom. */
	public function get_name() {
		return 'rolling-video';
	}

	/** Titre. */
	public function get_title() {
		return __( 'Vidéo de la réalisation', 'rolling' );
	}

	/** Icône. */
	public function get_icon() {
		return 'eicon-youtube';
	}

	/** Réglages. */
	protected function register_controls() {
		$this->start_controls_section( 'video', array( 'label' => __( 'Vidéo', 'rolling' ) ) );
		/* translators: %s : titre de la vidéo. */
		$this->texte( 'libelle', __( 'Bouton lecture, pour lecteur d’écran (%s = titre)', 'rolling' ), __( 'Lire la vidéo : %s', 'rolling' ) );
		$this->texte( 'classe', __( 'Classes du cadre', 'rolling' ), 'cadre-photo video-fiche' );
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r     = $this->get_settings_for_display();
		$id    = $this->contenu();
		$video = (string) Rolling_Contenus::valeur( 'video', $id );
		if ( ! $video && ! has_post_thumbnail( $id ) ) {
			return;
		}
		$formats = get_the_terms( $id, 'rolling_format' );
		$format  = $formats && ! is_wp_error( $formats ) ? $formats[0]->name : '';
		$duree   = (string) Rolling_Contenus::valeur( 'duree', $id );
		$titre   = get_the_title( $id );

		echo '<div class="' . esc_attr( $r['classe'] ) . '"' . ( $video ? ' data-rl-video="' . esc_url( $video ) . '"' : '' ) . '>';
		echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de WordPress.
			$id,
			'large',
			array(
				'class'         => 'image-couvrante',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'alt'           => '',
			)
		);
		if ( $format ) {
			echo '<span class="bandeau-video">' . esc_html( $format ) . '</span>';
		}
		if ( $duree ) {
			echo '<span class="duree">' . esc_html( $duree ) . '</span>';
		}
		if ( $video ) {
			echo '<button type="button" class="lecture-centre" aria-label="' . esc_attr( sprintf( $r['libelle'], $titre ) ) . '">' . Rolling_Dynamique::image_du_theme( 'bouton-lecture', 'thumbnail' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image de wp_get_attachment_image().
		}
		echo '</div>';
	}
}
