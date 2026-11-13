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
		$this->choix(
			'source',
			__( 'Vidéo de', 'rolling' ),
			array(
				'courant'     => __( 'Contenu affiché', 'rolling' ),
				'realisation' => __( 'Réalisation liée (sur un témoignage)', 'rolling' ),
			)
		);
		$this->choix(
			'apercu',
			__( 'Avant la lecture (fichier vidéo)', 'rolling' ),
			array(
				'anime' => __( 'Aperçu muet en boucle', 'rolling' ),
				'image' => __( 'Image fixe', 'rolling' ),
			)
		);
		$this->choix(
			'priorite',
			__( 'Chargement de l’image', 'rolling' ),
			array(
				'haut' => __( 'Tout de suite (haut de page)', 'rolling' ),
				'bas'  => __( 'Différé (plus bas, listes)', 'rolling' ),
			)
		);
		$this->end_controls_section();
	}

	/** Affichage. */
	protected function render() {
		$r  = $this->get_settings_for_display();
		$id = $this->contenu();
		if ( 'realisation' === $r['source'] ) {
			// Sur un témoignage : la première réalisation liée.
			$liees = 'rolling_temoignage' === get_post_type( $id ) ? Rolling_Contenus::realisations_liees( $id ) : array();
			$id    = $liees ? $liees[0] : 0;
			if ( ! $id ) {
				return;
			}
		}
		$haut  = 'bas' !== $r['priorite'];
		$fixe  = 'image' === $r['apercu'];
		$video = (string) Rolling_Contenus::valeur( 'video', $id );
		if ( ! $video && ! has_post_thumbnail( $id ) ) {
			return;
		}
		$formats = get_the_terms( $id, 'rolling_format' );
		$format  = $formats && ! is_wp_error( $formats ) ? $formats[0]->name : '';
		$duree   = (string) Rolling_Contenus::valeur( 'duree', $id );
		$titre   = get_the_title( $id );

		echo '<div class="' . esc_attr( $r['classe'] ) . '"' . ( $video ? ' data-rl-video="' . esc_url( $video ) . '"' : '' ) . ( $fixe ? ' data-rl-apercu="fixe"' : '' ) . '>';
		if ( has_post_thumbnail( $id ) ) {
			echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de WordPress.
				$id,
				$haut ? 'large' : 'medium_large',
				$haut ? array(
					'class'         => 'image-couvrante',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'alt'           => '',
				) : array(
					'class'   => 'image-couvrante',
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
		}
		$youtube = self::id_youtube( $video );
		if ( $youtube && ! has_post_thumbnail( $id ) ) {
			// Sans image à la une : la miniature de la vidéo YouTube.
			echo '<img class="image-couvrante" src="' . esc_url( 'https://i.ytimg.com/vi/' . $youtube . '/hqdefault.jpg' ) . '" width="480" height="360" alt=""' . ( $haut ? '' : ' loading="lazy"' ) . '>';
		} elseif ( $video && ! $youtube ) {
			// Fichier vidéo : aperçu muet en boucle (lancé par rolling.js quand il est à l'écran, jamais avec « réduire les
			// animations », ni avec « Image fixe ») ; sans image à la une, sa première image sert d'affiche.
			echo '<video class="image-couvrante apercu-video" src="' . esc_url( $video ) . '#t=0.1" muted loop playsinline preload="metadata" aria-hidden="true" tabindex="-1"></video>';
		}
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

	/**
	 * Identifiant d'une vidéo YouTube.
	 *
	 * @param string $url Lien.
	 * @return string
	 */
	public static function id_youtube( $url ) {
		return preg_match( '~(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})~', (string) $url, $m ) ? $m[1] : '';
	}
}
