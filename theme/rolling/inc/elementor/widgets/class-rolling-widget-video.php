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
				'lien'        => __( 'Lien ci-dessous (Vimeo, YouTube ou fichier)', 'rolling' ),
			)
		);
		$this->texte( 'lien', __( 'Lien de la vidéo (si « Lien ci-dessous »)', 'rolling' ), '' );
		$this->texte( 'titre', __( 'Titre de la vidéo (si « Lien ci-dessous »)', 'rolling' ), __( 'Showreel', 'rolling' ) );
		$this->choix(
			'mode',
			__( 'Lien : avant le clic', 'rolling' ),
			array(
				'boucle' => __( 'Aperçu muet en boucle, puis le son au clic', 'rolling' ),
				'clic'   => __( 'Image seulement, lecture au clic', 'rolling' ),
				'muet'   => __( 'Vidéo muette en boucle, sans son ni bouton (à la place d’une image)', 'rolling' ),
			)
		);
		$this->texte( 'son', __( 'Lien : texte du bouton son', 'rolling' ), __( 'Activer le son', 'rolling' ) );
		$this->texte( 'son_off', __( 'Lien : texte du bouton pour couper le son', 'rolling' ), __( 'Couper le son', 'rolling' ) );
		$this->choix(
			'apercu',
			__( 'Avant la lecture (fichier vidéo)', 'rolling' ),
			array(
				'anime' => __( 'Aperçu muet en boucle', 'rolling' ),
				'image' => __( 'Image fixe', 'rolling' ),
			)
		);
		$this->choix(
			'adapter',
			__( 'Format du cadre', 'rolling' ),
			array(
				'non' => __( '16:9 (cartes, listes)', 'rolling' ),
				'oui' => __( 'Celui de la vidéo (fiche réalisation)', 'rolling' ),
			)
		);
		$this->choix(
			'partie',
			__( 'Afficher', 'rolling' ),
			array(
				'lecteur'  => __( 'Le lecteur', 'rolling' ),
				'versions' => __( 'Les boutons de versions (s’il y a plusieurs vidéos)', 'rolling' ),
			)
		);
		$this->texte( 'versions_nom', __( 'Boutons de versions : nom du groupe (lecteur d’écran)', 'rolling' ), __( 'Versions de la vidéo', 'rolling' ) );
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
		if ( 'lien' === $r['source'] ) {
			$this->rendre_lien( $r );
			return;
		}
		if ( 'realisation' === $r['source'] ) {
			// Sur un témoignage : la première réalisation liée.
			$liees = 'rolling_temoignage' === get_post_type( $id ) ? Rolling_Contenus::realisations_liees( $id ) : array();
			$id    = $liees ? $liees[0] : 0;
			if ( ! $id ) {
				return;
			}
		}
		$haut     = 'bas' !== $r['priorite'];
		$fixe     = 'image' === $r['apercu'];
		$adapter  = 'oui' === ( $r['adapter'] ?? 'non' );
		$versions = Rolling_Contenus::videos( $id );
		if ( ! $adapter ) {
			$versions = array_slice( $versions, 0, 1 );
		}
		if ( 'versions' === ( $r['partie'] ?? 'lecteur' ) ) {
			$this->rendre_versions( $versions, (string) ( $r['versions_nom'] ?? '' ) );
			return;
		}
		if ( ! $versions && ! has_post_thumbnail( $id ) ) {
			return;
		}
		if ( ! $versions ) {
			$versions = array(
				array(
					'lien'   => '',
					'format' => '16-9',
					'nom'    => '',
				),
			);
		}
		foreach ( $versions as $rang => $version ) {
			$this->rendre_cadre( $r, $id, $version, $rang, $adapter, $haut, $fixe, count( $versions ) > 1 );
		}
	}

	/**
	 * Boutons de versions (au-dessus du lecteur) : un par vidéo, avec la forme de son format.
	 *
	 * @param array  $versions Vidéos.
	 * @param string $nom      Nom du groupe.
	 */
	private function rendre_versions( $versions, $nom ) {
		if ( count( $versions ) < 2 ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p class="texte-petit">' . esc_html__( 'Boutons de versions : ils apparaissent quand la réalisation a plusieurs vidéos.', 'rolling' ) . '</p>';
			}
			return;
		}
		echo '<div class="versions-video" role="group" aria-label="' . esc_attr( $nom ) . '">';
		foreach ( $versions as $rang => $version ) {
			$texte = self::texte_version( $version );
			echo '<button type="button" data-rl-version="' . esc_attr( $rang ) . '" data-rl-forme="' . esc_attr( $version['format'] ) . '" aria-pressed="' . ( 0 === $rang ? 'true' : 'false' ) . '">' . esc_html( $texte ) . '</button>';
		}
		echo '</div>';
	}

	/**
	 * Cadre d'une vidéo : affiche, étiquette, durée, bouton lecture ; la vidéo se charge au clic.
	 *
	 * @param array $r       Réglages.
	 * @param int   $id      Réalisation.
	 * @param array $version Vidéo (lien, format, nom).
	 * @param int   $rang    Rang (0 = principale ; les autres sont cachées jusqu'au choix d'une version).
	 * @param bool  $adapter Cadre au format de la vidéo.
	 * @param bool  $haut    Image chargée tout de suite.
	 * @param bool  $fixe      Image fixe plutôt qu'aperçu animé.
	 * @param bool  $plusieurs Plusieurs versions (l'étiquette est alors portée par les boutons de versions).
	 */
	private function rendre_cadre( $r, $id, $version, $rang, $adapter, $haut, $fixe, $plusieurs = false ) {
		$video   = $version['lien'];
		$titre   = get_the_title( $id );
		$duree   = 0 === $rang ? (string) Rolling_Contenus::valeur( 'duree', $id ) : '';
		$affiche = 0 === $rang && has_post_thumbnail( $id );
		if ( $adapter ) {
			// Fiche : l'étiquette dit le vrai format de la vidéo ; avec plusieurs versions, les boutons au-dessus le disent déjà.
			$etiquette = $plusieurs ? '' : self::texte_version( $version );
		} else {
			$etiquette = self::format_carte( $id, $version['format'] );
		}

		// Fichier au format automatique : rolling.js vérifie le format sur le fichier lui-même.
		$fichier = $video && ! self::id_youtube( $video ) && ! Rolling_Contenus::est_vimeo( $video );
		echo '<div class="' . esc_attr( $r['classe'] ) . '"' . ( $video ? ' data-rl-video="' . esc_url( $video ) . '"' : '' ) . ( $fixe ? ' data-rl-apercu="fixe"' : '' )
			. ( $adapter ? ' data-rl-format="' . esc_attr( $version['format'] ) . '" data-rl-version="' . esc_attr( $rang ) . '"' : '' )
			. ( $adapter && $fichier && ! empty( $version['auto'] ) ? ' data-rl-auto="1"' : '' ) . ( $rang > 0 ? ' hidden' : '' ) . '>';
		if ( $affiche ) {
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
		$vimeo   = $video && Rolling_Contenus::est_vimeo( $video ) && class_exists( 'Rolling_Widget_Contenu' ) ? Rolling_Widget_Contenu::image_vimeo( $video ) : '';
		if ( $affiche ) {
			// Avec une image à la une : pas de miniature en plus (un fichier vidéo garde son aperçu muet par-dessus).
			$youtube = '';
			$vimeo   = '';
		}
		$video_a = $video;
		if ( $youtube ) {
			// Sans image à la une : la miniature de la vidéo YouTube.
			echo '<img class="image-couvrante" src="' . esc_url( 'https://i.ytimg.com/vi/' . $youtube . ( '9-16' === $version['format'] ? '/hqdefault.jpg' : '/maxresdefault.jpg' ) ) . '" width="1280" height="720" alt=""' . ( $haut && 0 === $rang ? '' : ' loading="lazy"' ) . '>';
		} elseif ( $vimeo ) {
			echo '<img class="image-couvrante" src="' . esc_url( $vimeo ) . '" width="1280" height="720" alt=""' . ( $haut && 0 === $rang ? '' : ' loading="lazy"' ) . '>';
		} elseif ( $video_a && ! Rolling_Contenus::est_vimeo( $video_a ) ) {
			// Fichier vidéo : aperçu muet en boucle (lancé par rolling.js quand il est à l'écran, jamais avec « réduire les
			// animations », ni avec « Image fixe ») ; sans image à la une, sa première image sert d'affiche.
			echo '<video class="image-couvrante apercu-video" src="' . esc_url( $video ) . ( $fixe ? '#t=2' : '#t=0.1' ) . '" muted loop playsinline preload="metadata" aria-hidden="true" tabindex="-1"></video>';
		}
		if ( $etiquette ) {
			echo '<span class="bandeau-video">' . esc_html( $etiquette ) . '</span>';
		}
		if ( $duree ) {
			echo '<span class="duree">' . esc_html( $duree ) . '</span>';
		}
		if ( $video ) {
			$nom_lecture = sprintf( $r['libelle'], $titre . ( '' !== $version['nom'] ? ' (' . $version['nom'] . ')' : '' ) );
			echo '<button type="button" class="lecture-centre" aria-label="' . esc_attr( $nom_lecture ) . '">' . Rolling_Dynamique::image_du_theme( 'bouton-lecture', 'thumbnail' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image de wp_get_attachment_image().
		}
		echo '</div>';
	}

	/**
	 * Texte d'une version : « 9:16 · Stories » ; le nom seul s'il donne déjà le format (« Format 9:16 ») ; le format seul sans nom.
	 *
	 * @param array $version Vidéo (lien, format, nom).
	 * @return string
	 */
	public static function texte_version( $version ) {
		$format = Rolling_Contenus::libelle_format( $version['format'] );
		$nom    = trim( (string) $version['nom'] );
		if ( '' === $nom ) {
			return $format;
		}
		if ( preg_match( '/(?:^|\D)(16[-:]9|9[-:]16|1[-:]1|4[-:]5)(?:\D|$)/', $nom ) ) {
			return $nom;
		}
		return $format . ' · ' . $nom;
	}

	/**
	 * Étiquette d'une carte : le 1er format vidéo coché qui n'est pas un format d'image (ex. « Motion design ») ;
	 * sinon le vrai format de la vidéo.
	 *
	 * @param int    $id     Réalisation.
	 * @param string $format Format de la vidéo principale.
	 * @return string
	 */
	public static function format_carte( $id, $format ) {
		$termes = get_the_terms( $id, 'rolling_format' );
		foreach ( is_array( $termes ) ? $termes : array() as $terme ) {
			if ( ! self::est_rapport( $terme ) ) {
				return $terme->name;
			}
		}
		return is_array( $termes ) && $termes ? Rolling_Contenus::libelle_format( $format ) : '';
	}

	/**
	 * Le terme désigne-t-il un format d'image (« 9:16 », « Format 1:1 »…) ?
	 *
	 * @param WP_Term $terme Terme.
	 * @return string Format (16-9, 9-16, 1-1, 4-5) ou chaîne vide.
	 */
	public static function est_rapport( $terme ) {
		return preg_match( '/(?:^|\D)(16[-:]9|9[-:]16|1[-:]1|4[-:]5)(?:\D|$)/', $terme->slug . ' ' . $terme->name, $m ) ? str_replace( ':', '-', $m[1] ) : '';
	}

	/**
	 * Vidéo d'un lien (ex. le showreel de la landing) : un calque posé sur l'image d'affiche du cadre (élément Image
	 * d'Elementor, modifiable). rolling.js y charge l'aperçu muet quand il est à l'écran, puis le lecteur avec le son au
	 * clic. Vimeo : titre, nom et photo du compte masqués.
	 *
	 * @param array $r Réglages.
	 */
	private function rendre_lien( $r ) {
		$url = trim( (string) $r['lien'] );
		if ( '' === $url ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				if ( 'muet' === $r['mode'] ) {
					echo '<p class="bandeau-video" style="top:12px;right:12px;left:auto;bottom:auto">' . esc_html__( 'Vidéo (facultatif) : lien dans le widget', 'rolling' ) . '</p>';
				} else {
					echo '<p class="bandeau-video" style="top:auto;bottom:14px">' . esc_html__( 'Vidéo : collez le lien dans les réglages du widget', 'rolling' ) . '</p>';
				}
			}
			return;
		}
		if ( 'muet' === $r['mode'] ) {
			// Décor : la vidéo remplace l'image une fois prête (l'image reste avec « réduire les animations »). Ni son, ni bouton.
			echo '<div class="video-calque" data-rl-video="' . esc_url( $url ) . '" data-rl-mode="muet" aria-hidden="true"></div>';
			return;
		}
		$titre  = '' !== trim( (string) $r['titre'] ) ? $r['titre'] : __( 'Vidéo', 'rolling' );
		$boucle = 'clic' !== $r['mode'];
		echo '<div class="video-calque" data-rl-video="' . esc_url( $url ) . '" data-rl-mode="' . ( $boucle ? 'boucle' : 'clic' ) . '">';
		echo '<button type="button" class="video-calque-bouton" aria-label="' . esc_attr( sprintf( $r['libelle'], $titre ) ) . '" data-rl-titre="' . esc_attr( $titre ) . '">';
		if ( $boucle ) {
			// Pendant l'aperçu, un clic active le son sans changer d'affichage (la vidéo reste en plein cadre) ; un 2e clic le coupe.
			echo '<span class="pastille-son" data-rl-son-on="' . esc_attr( $r['son'] ) . '" data-rl-son-off="' . esc_attr( '' !== trim( (string) ( $r['son_off'] ?? '' ) ) ? $r['son_off'] : __( 'Couper le son', 'rolling' ) ) . '">' . esc_html( $r['son'] ) . '</span>';
		}
		echo '</button></div>';
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
