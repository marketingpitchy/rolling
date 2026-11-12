<?php
/**
 * Éditeur des articles : compositions Rolling (bouton « + » > Compositions > Rolling), styles de blocs
 * (Liste à coches, Étapes numérotées, Tableau comparatif) et aperçu au style du site.
 * Le rendu sur le site vient de la classe Elementor « article-texte » (modèle d'article).
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compositions des articles.
 */
class Rolling_Compositions {

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'enregistrer' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'apercu' ) );
	}

	/**
	 * Styles de blocs et compositions.
	 */
	public static function enregistrer() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		register_block_style(
			'core/list',
			array(
				'name'  => 'coches',
				'label' => __( 'Liste à coches', 'rolling' ),
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'  => 'etapes',
				'label' => __( 'Étapes numérotées', 'rolling' ),
			)
		);
		register_block_style(
			'core/table',
			array(
				'name'  => 'comparatif',
				'label' => __( 'Comparatif', 'rolling' ),
			)
		);

		register_block_pattern_category( 'rolling', array( 'label' => 'Rolling' ) );

		foreach ( Rolling_Donnees::lire( 'compositions' ) as $composition ) {
			$fichier = ROLLING_DIR . '/data/compositions/' . sanitize_file_name( $composition['cle'] ) . '.html';
			if ( ! is_readable( $fichier ) ) {
				continue;
			}
			// Lecture d'un fichier local du thème.
			$contenu = (string) file_get_contents( $fichier ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			register_block_pattern(
				'rolling/' . $composition['cle'],
				array(
					'title'       => 'Rolling : ' . $composition['titre'],
					'description' => $composition['description'],
					'categories'  => array( 'rolling' ),
					'postTypes'   => array( 'post' ),
					'content'     => self::remplir( $contenu ),
				)
			);
		}
	}

	/**
	 * Remplace {{media:clé|taille}} (adresse de l'image installée par le thème), {{media-id:clé}} (son id : WordPress
	 * ajoute alors dimensions et srcset) et {{page:chemin}} (page du site).
	 *
	 * @param string $contenu Composition.
	 * @return string
	 */
	private static function remplir( $contenu ) {
		$medias = (array) get_option( Rolling_Installateur::OPTION_MEDIAS, array() );

		return (string) preg_replace_callback(
			'/\{\{(media-id|media|page):([\w-]+)(?:\|([\w-]+))?\}\}/',
			function ( $m ) use ( $medias ) {
				$id = empty( $medias[ $m[2] ] ) ? 0 : (int) $medias[ $m[2] ];
				if ( 'media-id' === $m[1] ) {
					return (string) $id;
				}
				if ( 'media' === $m[1] ) {
					$url = $id ? wp_get_attachment_image_url( $id, empty( $m[3] ) ? 'medium' : $m[3] ) : '';
					return esc_url( $url ? $url : ROLLING_URL . '/assets/medias/' . $m[2] . '.jpg' );
				}
				$page = get_page_by_path( $m[2] );
				return esc_url( $page ? get_permalink( $page ) : home_url( '/' . $m[2] . '/' ) );
			},
			$contenu
		);
	}

	/**
	 * Aperçu dans l'éditeur WordPress : polices et styles du texte d'article (feuille générée depuis la classe).
	 */
	public static function apercu() {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/rolling-article-editeur.css' );
	}
}
